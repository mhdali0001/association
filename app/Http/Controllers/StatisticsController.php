<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\FinalStatus;
use App\Models\Member;
use App\Models\VerificationStatus;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $totalMembers         = Member::count();
        $totalEstimatedAmount = Member::sum('estimated_amount');

        // حالة التحقق
        $verificationDist = VerificationStatus::withCount('members')->orderBy('name')->get()
            ->map(fn($v) => ['name' => $v->name, 'count' => $v->members_count, 'color' => $v->color]);
        $noVerificationCount = Member::whereNull('verification_status_id')->count();

        // الحالة النهائية
        $finalDist = FinalStatus::active()->withCount('members')->orderBy('name')->get()
            ->map(fn($f) => ['name' => $f->name, 'count' => $f->members_count, 'color' => $f->color]);
        $noFinalStatusCount = Member::whereNull('final_status_id')->count();

        // توزيع الجنس
        $genderDist = Member::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')->get()
            ->map(fn($r) => ['name' => $r->gender ?: 'غير محدد', 'count' => $r->total]);

        // الحالة الاجتماعية
        $maritalDist = Member::select('marital_status', DB::raw('count(*) as total'))
            ->groupBy('marital_status')->get()
            ->map(fn($r) => ['name' => $r->marital_status ?: 'غير محدد', 'count' => $r->total]);

        // نوع الشبكة
        $networkDist = Member::select('network', DB::raw('count(*) as total'))
            ->groupBy('network')->get()
            ->map(fn($r) => ['name' => $r->network ?: 'غير محدد', 'count' => $r->total]);

        // شام كاش
        $shamCashDist = Member::select('sham_cash_account', DB::raw('count(*) as total'))
            ->groupBy('sham_cash_account')->get()
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
            ->limit(10)->get()
            ->map(fn($a) => ['name' => $a->name, 'count' => $a->members_count]);

        // الحالة النهائية (للبطاقات)
        $finalStatuses = FinalStatus::active()->withCount('members')->orderBy('name')->get();

        return view('statistics', compact(
            'totalMembers', 'totalEstimatedAmount',
            'verificationDist', 'noVerificationCount',
            'finalDist', 'noFinalStatusCount',
            'genderDist', 'maritalDist', 'networkDist', 'shamCashDist',
            'specialCasesCount', 'noSpecialCasesCount',
            'associationDist', 'finalStatuses',
        ));
    }
}
