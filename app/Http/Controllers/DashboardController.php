<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FinalStatus;
use App\Models\Member;
use App\Models\MemberScore;
use App\Models\PaymentInfo;
use App\Models\PendingChange;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $myPendingCount  = PendingChange::where('requested_by', Auth::id())->where('status', 'pending')->count();
        $myRejectedCount = PendingChange::where('requested_by', Auth::id())->where('status', 'rejected')->count();
        $myRecentChanges = PendingChange::with('reviewer')
                            ->where('requested_by', Auth::id())
                            ->latest()->limit(5)->get();

        $allPendingCount = Auth::user()?->role === 'admin'
            ? PendingChange::where('status', 'pending')->count()
            : 0;

        $finalStatuses      = FinalStatus::active()->withCount('members')->orderBy('name')->get();
        $noFinalStatusCount = Member::whereNull('final_status_id')->count();

        $totalMembers         = Member::count();
        $totalEstimatedAmount = Member::sum('estimated_amount');

        // ── User monitoring ──────────────────────────────────────────
        $todayStart = now()->startOfDay();
        $weekStart  = now()->startOfWeek();

        $lastActivitySub = ActivityLog::select('user_id', DB::raw('MAX(id) as last_id'))
            ->whereNotNull('user_id')->groupBy('user_id');

        $usersActivity = User::leftJoinSub($lastActivitySub, 'la', 'users.id', '=', 'la.user_id')
            ->leftJoin('activity_logs as al', 'al.id', '=', 'la.last_id')
            ->select(
                'users.id', 'users.name', 'users.role',
                'al.action as last_action',
                'al.subject_label as last_subject',
                'al.description as last_description',
                'al.created_at as last_at',
            )
            ->orderByDesc('al.created_at')
            ->get();

        // Today breakdown per user: created / updated / deleted counts
        $todayBreakdown = ActivityLog::select('user_id', 'action', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('user_id')
            ->whereIn('action', ['created', 'updated', 'deleted'])
            ->where('created_at', '>=', $todayStart)
            ->groupBy('user_id', 'action')
            ->get()
            ->groupBy('user_id')
            ->map(fn($rows) => $rows->pluck('cnt', 'action'));

        // This-week edits per user
        $weekEdits = ActivityLog::select('user_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('user_id')
            ->whereIn('action', ['created', 'updated', 'deleted'])
            ->where('created_at', '>=', $weekStart)
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        // Last login per user
        $lastLoginPerUser = ActivityLog::select('user_id', DB::raw('MAX(created_at) as last_login'))
            ->whereNotNull('user_id')
            ->where('action', 'login')
            ->groupBy('user_id')
            ->pluck('last_login', 'user_id');

        // Mini feed: last 3 meaningful actions per user
        $recentActionsPerUser = ActivityLog::whereNotNull('user_id')
            ->whereIn('action', ['created', 'updated', 'deleted'])
            ->latest()->limit(300)->get()
            ->groupBy('user_id')
            ->map(fn($logs) => $logs->take(3));

        return view('dashboard', compact(
            'myPendingCount', 'myRejectedCount', 'myRecentChanges',
            'allPendingCount', 'finalStatuses', 'noFinalStatusCount',
            'totalMembers', 'totalEstimatedAmount',
            'usersActivity', 'todayBreakdown', 'weekEdits',
            'lastLoginPerUser', 'recentActionsPerUser',
        ));
    }

    public function userWeekActivity(User $user)
    {
        $logs = ActivityLog::where('user_id', $user->id)
            ->whereIn('action', ['created', 'updated', 'deleted'])
            ->where('created_at', '>=', now()->startOfWeek())
            ->latest()
            ->get(['id', 'action', 'subject_type', 'subject_id', 'subject_label', 'description', 'created_at', 'properties']);

        $actionLabels = ['created' => 'إضافة', 'updated' => 'تعديل', 'deleted' => 'حذف'];

        $isAdmin = Auth::user()?->role === 'admin';

        return response()->json([
            'user' => ['name' => $user->name, 'role' => $user->role],
            'logs' => $logs->map(fn($l) => [
                'id'          => $l->id,
                'action'      => $l->action,
                'actionLabel' => $actionLabels[$l->action] ?? $l->action,
                'label'       => $l->subject_label ?: $l->description ?: '—',
                'time'        => $l->created_at->format('Y/m/d — H:i'),
                'diff'        => $l->created_at->diffForHumans(),
                'can_revert'  => $isAdmin
                                 && $l->action === 'updated'
                                 && $l->subject_type === 'Member'
                                 && !empty($l->properties['old']),
                'revert_url'  => $isAdmin ? route('dashboard.revert-activity', $l->id) : null,
            ]),
        ]);
    }

    public function revertActivity(Request $request, ActivityLog $log)
    {
        if (Auth::user()?->role !== 'admin') abort(403);

        $jsonError = fn(string $msg) => $request->ajax() || $request->wantsJson()
            ? response()->json(['success' => false, 'message' => $msg], 422)
            : back()->with('error', $msg);

        if ($log->action !== 'updated' || $log->subject_type !== 'Member') {
            return $jsonError('لا يمكن التراجع عن هذا الإجراء.');
        }

        $old = $log->properties['old'] ?? null;
        if (!$old) {
            return $jsonError('لا تتوفر بيانات كافية للتراجع.');
        }

        $member = Member::find($log->subject_id);
        if (!$member) {
            return $jsonError('المستفيد غير موجود.');
        }

        DB::transaction(function () use ($member, $old, $log) {
            if (!empty($old['member'])) {
                $member->fill(array_intersect_key($old['member'], array_flip($member->getFillable())))->save();
            }

            if (!empty($old['scores'])) {
                $scores = $member->scores ?? new MemberScore(['member_id' => $member->id]);
                $scores->fill(array_intersect_key($old['scores'], array_flip($scores->getFillable())))->save();
            }

            if (!empty($old['payment'])) {
                $payment = $member->paymentInfo ?? new PaymentInfo(['member_id' => $member->id]);
                $payment->fill(array_merge(
                    ['member_id' => $member->id],
                    array_intersect_key($old['payment'], ['iban'=>1,'barcode'=>1,'recipient_name'=>1,'data_entry_name'=>1])
                ))->save();
            }

            ActivityLogger::log('updated', "تراجع عن تعديل بيانات المستفيد: {$member->full_name}", $member);
        });

        $message = "تم التراجع عن التعديل بنجاح — {$member->full_name}.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
