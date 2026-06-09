<?php

namespace App\Http\Controllers;

use App\Exports\MatchedAndReviewedExport;
use App\Exports\MatchedPaymentExport;
use App\Imports\PaymentInfoImport;
use App\Models\Member;
use App\Models\PaymentInfo;
use App\Models\PaymentReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PaymentReviewController extends Controller
{
    public function index(Request $request)
    {
        $filter   = $request->get('filter', 'all');
        $search   = trim($request->get('search', ''));
        $shamCash = $request->get('sham_cash', '');
        $dateFrom = trim($request->get('date_from', ''));
        $dateTo   = trim($request->get('date_to', ''));

        // "done_no_iban": members with sham_cash=done but NO IBAN — bypass the IBAN requirement
        if ($shamCash === 'done_no_iban') {
            $query = Member::query()
                ->with(['paymentInfo', 'paymentInfoAI'])
                ->where('sham_cash_account', 'done')
                ->where(function ($q) {
                    $q->whereDoesntHave('paymentInfo')
                      ->orWhereHas('paymentInfo', fn($s) => $s->where(fn($x) =>
                          $x->whereNull('iban')->orWhere('iban', '')
                      ));
                })
                ->where(function ($q) {
                    $q->whereDoesntHave('paymentInfoAI')
                      ->orWhereHas('paymentInfoAI', fn($s) => $s->where(fn($x) =>
                          $x->whereNull('iban')->orWhere('iban', '')
                      ));
                })
                ->orderBy('full_name');
        } else {
            // All other filters: only show members who have an IBAN
            $query = Member::query()
                ->with(['paymentInfo', 'paymentInfoAI'])
                ->where(function ($q) {
                    $q->whereHas('paymentInfo', fn($s) => $s->whereNotNull('iban')->where('iban', '!=', ''))
                      ->orWhereHas('paymentInfoAI', fn($s) => $s->whereNotNull('iban')->where('iban', '!=', ''));
                })
                ->orderBy('full_name');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('dossier_number', 'like', "%{$search}%");
            });
        }

        if ($dateFrom !== '') $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo   !== '') $query->whereDate('created_at', '<=', $dateTo);

        if ($shamCash === 'done') {
            $query->where('sham_cash_account', 'done');
        } elseif ($shamCash === 'manual') {
            $query->where('sham_cash_account', 'manual');
        } elseif ($shamCash === 'none') {
            $query->where(function ($q) {
                $q->whereNull('sham_cash_account')->orWhere('sham_cash_account', '');
            });
        } elseif ($shamCash === 'iban_no_done') {
            $query->where(function ($q) {
                $q->whereNull('sham_cash_account')
                  ->orWhere('sham_cash_account', '')
                  ->orWhere('sham_cash_account', 'manual');
            })->whereHas('paymentInfo', fn($s) => $s->whereNotNull('iban')->where('iban', '!=', ''));
        }

        // Annotate all members with auto-match result and mismatch type
        $allAnnotated = $query->get()->map(function ($member) {
            $pi     = $member->paymentInfo;
            $ai     = $member->paymentInfoAI;
            $piIban = str_replace(' ', '', $pi->iban ?? '');
            $aiIban = str_replace(' ', '', $ai->iban ?? '');
            $member->auto_match = $pi && $ai
                && $piIban !== ''
                && $aiIban !== ''
                && $piIban === $aiIban;
            // classify mismatch type
            if ($member->auto_match) {
                $member->mismatch_type = null;
            } elseif ($piIban !== '' && $aiIban !== '') {
                $member->mismatch_type = levenshtein($piIban, $aiIban) === 1 ? 'one_digit' : 'partial';
            } else {
                $member->mismatch_type = 'full';
            }
            return $member;
        });

        // Stats (before filter)
        $totalCount           = $allAnnotated->count();
        $autoMatchCount       = $allAnnotated->filter(fn($m) => $m->auto_match)->count();
        $autoMismatchCount    = $allAnnotated->filter(fn($m) => !$m->auto_match)->count();
        $mismatchOneCount     = $allAnnotated->filter(fn($m) => $m->mismatch_type === 'one_digit')->count();
        $mismatchPartialCount = $allAnnotated->filter(fn($m) => $m->mismatch_type === 'partial')->count();
        $mismatchFullCount    = $allAnnotated->filter(fn($m) => $m->mismatch_type === 'full')->count();

        // Apply filter
        $members = match ($filter) {
            'auto_match'       => $allAnnotated->filter(fn($m) => $m->auto_match),
            'auto_mismatch'    => $allAnnotated->filter(fn($m) => !$m->auto_match),
            'mismatch_one'     => $allAnnotated->filter(fn($m) => $m->mismatch_type === 'one_digit'),
            'mismatch_partial' => $allAnnotated->filter(fn($m) => $m->mismatch_type === 'partial'),
            'mismatch_full'    => $allAnnotated->filter(fn($m) => $m->mismatch_type === 'full'),
            default            => $allAnnotated,
        };

        return view('payment-review.index', compact(
            'members', 'filter', 'search', 'shamCash', 'dateFrom', 'dateTo',
            'totalCount', 'autoMatchCount', 'autoMismatchCount',
            'mismatchOneCount', 'mismatchPartialCount', 'mismatchFullCount'
        ));
    }

    public function exportMatched()
    {
        $filename = 'المتطابقون-' . now()->format('Y-m-d') . '.xlsx';
        \App\Services\ActivityLogger::log('exported', 'تصدير الآيبانات المتطابقة');
        return Excel::download(new MatchedPaymentExport(), $filename);
    }

    public function exportMatchedReviewed()
    {
        $filename = 'متطابقون-ومراجَعون-' . now()->format('Y-m-d') . '.xlsx';
        \App\Services\ActivityLogger::log('exported', 'تصدير المتطابقين تلقائياً والمراجَعين بحالة تم');
        return Excel::download(new MatchedAndReviewedExport(), $filename);
    }

    public function importShow()
    {
        return view('payment-review.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file'   => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'target' => 'required|in:payment_info,payment_info_ai',
        ]);

        $importer = new PaymentInfoImport($request->input('target'));
        Excel::import($importer, $request->file('file'));

        \App\Services\ActivityLogger::log('imported', 'استيراد بيانات آيبان — الجدول: ' . $request->input('target'));

        return redirect()->route('payment-review.import.show')
            ->with('import_updated', $importer->updated)
            ->with('import_skipped', $importer->skipped)
            ->with('import_errors',  $importer->errors);
    }

    public function duplicateIbans(Request $request)
    {
        $search         = trim($request->get('search', ''));
        $finalStatusIds = array_filter((array) $request->get('final_status_id', []));
        $dateFrom       = $request->get('date_from', '');
        $dateTo         = $request->get('date_to', '');

        $includeNone = in_array('none', $finalStatusIds);
        $numericIds  = array_map('intval', array_filter($finalStatusIds, fn($v) => $v !== 'none'));

        // Step 1: find all duplicate IBANs
        $allDuplicateIbans = DB::table('payment_info')
            ->select('iban', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('iban')
            ->where('iban', '!=', '')
            ->groupBy('iban')
            ->having('cnt', '>', 1)
            ->pluck('cnt', 'iban');

        if ($allDuplicateIbans->isEmpty()) {
            return view('payment-review.duplicate-ibans', [
                'membersByIban'          => collect(),
                'search'                 => $search,
                'finalStatusIds'         => $finalStatusIds,
                'finalStatusList'        => \App\Models\FinalStatus::active()->orderBy('name')->get(),
                'totalDuplicateIbans'    => 0,
                'totalAffectedMembers'   => 0,
                'rawTotalDuplicateIbans' => 0,
                'rawTotalAffectedMembers'=> 0,
                'dateFrom'               => $dateFrom,
                'dateTo'                 => $dateTo,
            ]);
        }

        // Step 2: if a status filter is active, narrow the IBAN list via SQL
        $ibanPool = $allDuplicateIbans->keys();

        if (!empty($finalStatusIds)) {
            $statusIbansQuery = DB::table('payment_info')
                ->join('members', 'members.id', '=', 'payment_info.member_id')
                ->whereIn('payment_info.iban', $ibanPool)
                ->where(function ($q) use ($includeNone, $numericIds) {
                    if ($includeNone)        $q->orWhereNull('members.final_status_id');
                    if (!empty($numericIds)) $q->orWhereIn('members.final_status_id', $numericIds);
                });

            $ibanPool = $statusIbansQuery->pluck('payment_info.iban')->unique()->values();

            if ($ibanPool->isEmpty()) {
                return view('payment-review.duplicate-ibans', [
                    'membersByIban'           => collect(),
                    'search'                  => $search,
                    'finalStatusIds'          => $finalStatusIds,
                    'finalStatusList'         => \App\Models\FinalStatus::active()->orderBy('name')->get(),
                    'totalDuplicateIbans'     => 0,
                    'totalAffectedMembers'    => 0,
                    'rawTotalDuplicateIbans'  => $allDuplicateIbans->count(),
                    'rawTotalAffectedMembers' => 0,
                    'dateFrom'                => $dateFrom,
                    'dateTo'                  => $dateTo,
                ]);
            }
        }

        // Step 3: load ALL members for the remaining IBans in one query
        $memberQuery = Member::whereHas('paymentInfo', fn($q) => $q->whereIn('iban', $ibanPool))
            ->with(['paymentInfo', 'verificationStatus', 'finalStatus', 'association', 'latestFieldVisit'])
            ->orderBy('full_name');

        // Date filter applied in SQL
        if ($dateFrom || $dateTo) {
            $memberQuery->whereHas('paymentInfo', function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom) $q->whereDate('created_at', '>=', $dateFrom);
                if ($dateTo)   $q->whereDate('created_at', '<=', $dateTo);
            });
        }

        $allMembers = $memberQuery->get();

        // Apply name/IBAN search filter
        if ($search) {
            $allMembers = $allMembers->filter(fn($m) =>
                stripos($m->paymentInfo?->iban ?? '', $search) !== false ||
                stripos($m->full_name, $search)                !== false
            );
        }

        // Group by IBAN — keep only groups with 2+ members
        $membersByIban = $allMembers
            ->groupBy(fn($m) => $m->paymentInfo?->iban ?? '')
            ->filter(fn($members, $iban) => $iban !== '' && $members->count() >= 2)
            ->sortByDesc(fn($members) => $members->count());

        // Stats
        $rawTotalDuplicateIbans  = $allDuplicateIbans->count();
        $rawTotalAffectedMembers = $allDuplicateIbans->sum();   // total entries across all dup IBans
        $totalDuplicateIbans     = $membersByIban->count();
        $totalAffectedMembers    = $membersByIban->flatten(1)->count();
        $finalStatusList         = \App\Models\FinalStatus::active()->orderBy('name')->get();

        return view('payment-review.duplicate-ibans', compact(
            'membersByIban', 'search', 'finalStatusIds', 'finalStatusList',
            'totalDuplicateIbans', 'totalAffectedMembers',
            'rawTotalDuplicateIbans', 'rawTotalAffectedMembers',
            'dateFrom', 'dateTo'
        ));
    }

    public function recentIbans(Request $request)
    {
        $tab = $request->get('tab', 'week');

        $weekStart  = now()->subDays(7)->startOfDay();
        $monthStart = now()->subDays(30)->startOfDay();

        $buildQuery = fn($from) => Member::whereHas('paymentInfo', fn($q) =>
                $q->whereNotNull('iban')->where('iban', '!=', '')->where('created_at', '>=', $from)
            )
            ->with(['paymentInfo' => fn($q) => $q->where('created_at', '>=', $from), 'verificationStatus', 'finalStatus', 'association'])
            ->orderBy('full_name')
            ->get();

        $weekMembers  = $buildQuery($weekStart);
        $monthMembers = $buildQuery($monthStart);

        // Duplicate IBANs added recently: IBANs that appear >1 time overall AND were added in the period
        $buildDuplicateQuery = fn($from) => DB::table('payment_info')
            ->select('iban', DB::raw('COUNT(*) as count'))
            ->whereNotNull('iban')->where('iban', '!=', '')
            ->where('created_at', '>=', $from)
            ->groupBy('iban')
            ->having('count', '>', 1)
            ->pluck('count', 'iban');

        $weekDuplicateIbans  = $buildDuplicateQuery($weekStart);
        $monthDuplicateIbans = $buildDuplicateQuery($monthStart);

        $buildDuplicateMembers = fn($ibans, $from) => $ibans->mapWithKeys(fn($count, $iban) => [
            $iban => Member::whereHas('paymentInfo', fn($q) => $q->where('iban', $iban))
                ->with(['paymentInfo' => fn($q) => $q->where('iban', $iban)->where('created_at', '>=', $from), 'verificationStatus', 'finalStatus', 'association'])
                ->orderBy('full_name')
                ->get()
        ]);

        $weekDuplicateMembers  = $buildDuplicateMembers($weekDuplicateIbans,  $weekStart);
        $monthDuplicateMembers = $buildDuplicateMembers($monthDuplicateIbans, $monthStart);

        return view('payment-review.recent-ibans', compact(
            'tab', 'weekMembers', 'monthMembers', 'weekStart', 'monthStart',
            'weekDuplicateMembers', 'monthDuplicateMembers'
        ));
    }

    public function bulkDelete(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));

        if (empty($ids)) {
            return back()->with('error', 'لم يتم تحديد أي سجل.');
        }

        PaymentInfo::whereIn('member_id', $ids)->delete();
        \App\Models\PaymentInfoAI::whereIn('member_id', $ids)->delete();
        PaymentReview::whereIn('member_id', $ids)->delete();

        \App\Services\ActivityLogger::log('deleted', 'حذف جماعي لبيانات الدفع — ' . count($ids) . ' عضو');

        return back()->with('success', 'تم حذف بيانات الدفع لـ ' . count($ids) . ' عضو بنجاح.');
    }

    public function store(Request $request, Member $member)
    {
        $data = $request->validate([
            'status' => 'required|in:match,mismatch,pending',
            'notes'  => 'nullable|string|max:500',
        ]);

        PaymentReview::updateOrCreate(
            ['member_id' => $member->id],
            [
                'status'      => $data['status'],
                'notes'       => $data['notes'] ?? null,
                'reviewed_by' => $data['status'] === 'pending' ? null : Auth::id(),
                'reviewed_at' => $data['status'] === 'pending' ? null : now(),
            ]
        );

        return back()->with('success', "تم حفظ نتيجة المراجعة لـ {$member->full_name}.");
    }

    public function updateIban(Request $request, Member $member)
    {
        $data = $request->validate([
            'iban'       => 'nullable|string|max:50',
            'barcode'    => 'nullable|string|max:100',
            'iban_ai'    => 'nullable|string|max:50',
            'barcode_ai' => 'nullable|string|max:100',
        ]);

        $iban      = str_replace(' ', '', $data['iban']       ?? '');
        $barcode   = str_replace(' ', '', $data['barcode']    ?? '');
        $ibanAi    = str_replace(' ', '', $data['iban_ai']    ?? '');
        $barcodeAi = str_replace(' ', '', $data['barcode_ai'] ?? '');

        PaymentInfo::updateOrCreate(
            ['member_id' => $member->id],
            ['iban' => $iban ?: null, 'barcode' => $barcode ?: null]
        );

        \App\Models\PaymentInfoAI::updateOrCreate(
            ['member_id' => $member->id],
            ['iban' => $ibanAi ?: null, 'barcode' => $barcodeAi ?: null]
        );

        \App\Services\ActivityLogger::log('updated', "تعديل الآيبان من صفحة المراجعة: {$member->full_name}", $member);

        return back()->with('success', "تم تحديث بيانات الدفع للعضو {$member->full_name}.");
    }
}
