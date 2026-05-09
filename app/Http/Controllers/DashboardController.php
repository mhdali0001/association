<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FinalStatus;
use App\Models\Member;
use App\Models\PendingChange;
use App\Models\User;
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
            ->get(['action', 'subject_type', 'subject_label', 'description', 'created_at']);

        $actionLabels = ['created' => 'إضافة', 'updated' => 'تعديل', 'deleted' => 'حذف'];

        return response()->json([
            'user' => ['name' => $user->name, 'role' => $user->role],
            'logs' => $logs->map(fn($l) => [
                'action'      => $l->action,
                'actionLabel' => $actionLabels[$l->action] ?? $l->action,
                'label'       => $l->subject_label ?: $l->description ?: '—',
                'time'        => $l->created_at->format('Y/m/d — H:i'),
                'diff'        => $l->created_at->diffForHumans(),
            ]),
        ]);
    }
}
