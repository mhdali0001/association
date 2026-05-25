<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $date          = $request->get('date', now()->toDateString());
        $userFilter    = $request->get('user_id', '');
        $actionFilter  = $request->get('action', '');
        $subjectFilter = $request->get('subject_type', '');

        $carbonDate = Carbon::parse($date);

        // ── Primary timeline from activity_logs ──────────────────────
        $logsQuery = ActivityLog::with('user')
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc');

        if ($userFilter)    $logsQuery->where('user_id', $userFilter);
        if ($actionFilter)  $logsQuery->where('action', $actionFilter);
        if ($subjectFilter) $logsQuery->where('subject_type', $subjectFilter);

        $logs = $logsQuery->paginate(60)->withQueryString();

        // ── Day-level stats (no user/action/subject filters) ─────────
        $dayBase        = ActivityLog::whereDate('created_at', $date);
        $totalLogs      = (clone $dayBase)->count();
        $statsByAction  = (clone $dayBase)->select('action', DB::raw('COUNT(*) as cnt'))
                            ->groupBy('action')->pluck('cnt', 'action');
        $statsBySubject = (clone $dayBase)->select('subject_type', DB::raw('COUNT(*) as cnt'))
                            ->whereNotNull('subject_type')
                            ->groupBy('subject_type')
                            ->orderByDesc('cnt')
                            ->pluck('cnt', 'subject_type');

        // ── Supplementary: direct table record counts ─────────────────
        $tableSummary = [
            ['label' => 'الأعضاء',              'icon' => 'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z',
              'created' => Member::whereDate('created_at', $date)->count(),
              'updated' => Member::whereDate('updated_at', $date)->whereDate('created_at', '!=', $date)->count()],

            ['label' => 'الجولات الميدانية',    'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z',
              'created' => DB::table('field_visits')->whereDate('created_at', $date)->count(),
              'updated' => DB::table('field_visits')->whereDate('updated_at', $date)->whereDate('created_at', '!=', $date)->count()],

            ['label' => 'التبرعات',              'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
              'created' => DB::table('donations')->whereDate('created_at', $date)->count(),
              'updated' => DB::table('donations')->whereDate('updated_at', $date)->whereDate('created_at', '!=', $date)->count()],

            ['label' => 'بيانات الدفع (IBAN)',   'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
              'created' => DB::table('payment_info')->whereDate('created_at', $date)->count(),
              'updated' => DB::table('payment_info')->whereDate('updated_at', $date)->whereDate('created_at', '!=', $date)->count()],

            ['label' => 'المصروفات',             'icon' => 'M9 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z',
              'created' => DB::table('expenses')->whereDate('created_at', $date)->count(),
              'updated' => DB::table('expenses')->whereDate('updated_at', $date)->whereDate('created_at', '!=', $date)->count()],

            ['label' => 'معاملات الموظفين',     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
              'created' => DB::table('employee_transactions')->whereDate('created_at', $date)->count(),
              'updated' => 0],

            ['label' => 'طلبات التعديل (جديدة)','icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
              'created' => DB::table('pending_changes')->whereDate('created_at', $date)->count(),
              'updated' => DB::table('pending_changes')->whereNotNull('reviewed_at')->whereDate('reviewed_at', $date)->count()],

            ['label' => 'صور الأعضاء',          'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
              'created' => DB::table('member_images')->whereDate('created_at', $date)->count(),
              'updated' => 0],
        ];

        // ── Dossier map for Member logs ───────────────────────────────
        $memberIds = $logs->getCollection()
            ->where('subject_type', 'Member')
            ->pluck('subject_id')->filter()->unique()->values();
        $dossierMap = Member::whereIn('id', $memberIds)->pluck('dossier_number', 'id');

        // ── Filter dropdowns ──────────────────────────────────────────
        $usersList    = User::orderBy('name')->get(['id', 'name']);
        $subjectTypes = ActivityLog::whereDate('created_at', $date)
            ->whereNotNull('subject_type')->distinct()->pluck('subject_type')->sort()->values();

        // ── Supplementary: field_visits on this date ─────────────────
        $fieldVisits = DB::table('field_visits as fv')
            ->join('members as m', 'm.id', '=', 'fv.member_id')
            ->leftJoin('users as u', 'u.id', '=', 'fv.created_by')
            ->leftJoin('field_visit_statuses as fvs', 'fvs.id', '=', 'fv.field_visit_status_id')
            ->whereDate('fv.created_at', $date)
            ->orderBy('fv.created_at', 'desc')
            ->select(
                'fv.id', 'fv.created_at', 'fv.visit_date', 'fv.visitor',
                'fv.estimated_amount', 'fv.notes', 'fv.has_video', 'fv.has_special_case',
                'm.id as member_id', 'm.full_name', 'm.dossier_number',
                'u.name as creator_name',
                'fvs.name as status_name', 'fvs.color as status_color'
            )
            ->get();

        // ── Prev / Next active dates ──────────────────────────────────
        $prevDate = ActivityLog::whereDate('created_at', '<', $date)
            ->orderBy('created_at', 'desc')
            ->value(DB::raw('DATE(created_at)'));
        $nextDate = ActivityLog::whereDate('created_at', '>', $date)
            ->orderBy('created_at')
            ->value(DB::raw('DATE(created_at)'));

        return view('archive.index', compact(
            'date', 'logs', 'totalLogs', 'statsByAction', 'statsBySubject',
            'tableSummary', 'usersList', 'subjectTypes', 'dossierMap',
            'userFilter', 'actionFilter', 'subjectFilter',
            'prevDate', 'nextDate', 'fieldVisits'
        ));
    }
}
