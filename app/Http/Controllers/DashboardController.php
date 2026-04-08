<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\FinalStatus;
use App\Models\Member;
use App\Models\PendingChange;
use App\Models\VerificationStatus;
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

        $totalMembers        = Member::count();
        $totalEstimatedAmount = Member::sum('estimated_amount');

        // حالة التحقق
        $verificationDist = VerificationStatus::withCount('members')->orderBy('name')->get()
            ->map(fn($v) => ['name' => $v->name, 'count' => $v->members_count, 'color' => $v->color]);

        $noVerificationCount = Member::whereNull('verification_status_id')->count();

        // الحالة النهائية
        $finalDist = FinalStatus::active()->withCount('members')->orderBy('name')->get()
            ->map(fn($f) => ['name' => $f->name, 'count' => $f->members_count, 'color' => $f->color]);

        // توزيع الجنس
        $genderDist = Member::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get()
            ->map(fn($r) => ['name' => $r->gender ?: 'غير محدد', 'count' => $r->total]);

        // الحالة الاجتماعية
        $maritalDist = Member::select('marital_status', DB::raw('count(*) as total'))
            ->groupBy('marital_status')
            ->get()
            ->map(fn($r) => ['name' => $r->marital_status ?: 'غير محدد', 'count' => $r->total]);

        // نوع الشبكة
        $networkDist = Member::select('network', DB::raw('count(*) as total'))
            ->groupBy('network')
            ->get()
            ->map(fn($r) => ['name' => $r->network ?: 'غير محدد', 'count' => $r->total]);

        // شام كاش
        $shamCashDist = Member::select('sham_cash_account', DB::raw('count(*) as total'))
            ->groupBy('sham_cash_account')
            ->get()
            ->map(fn($r) => [
                'name'  => match($r->sham_cash_account) { 'done' => 'تم', 'manual' => 'يدوي', default => 'لا' },
                'count' => $r->total,
            ]);

        // الحالات الخاصة
        $specialCasesCount   = Member::where('special_cases', true)->count();
        $noSpecialCasesCount = $totalMembers - $specialCasesCount;

        // أكثر الجمعيات أعضاءً
        $associationDist = Association::withCount('members')
            ->having('members_count', '>', 0)
            ->orderByDesc('members_count')
            ->limit(10)
            ->get()
            ->map(fn($a) => ['name' => $a->name, 'count' => $a->members_count]);

        return view('dashboard', compact(
            'myPendingCount', 'myRejectedCount', 'myRecentChanges',
            'allPendingCount', 'finalStatuses', 'noFinalStatusCount',
            'totalMembers', 'totalEstimatedAmount',
            'verificationDist', 'noVerificationCount',
            'finalDist',
            'genderDist', 'maritalDist', 'networkDist', 'shamCashDist',
            'specialCasesCount', 'noSpecialCasesCount',
            'associationDist',
        ));
    }
}
