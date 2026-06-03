<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
use App\Models\Delegate;
use App\Models\FieldVisit;
use App\Models\FieldVisitStatus;
use App\Models\FinalStatus;
use App\Models\Member;
use App\Models\MemberScore;
use App\Models\PaymentInfo;
use App\Models\PaymentInfoAI;
use App\Models\PendingChange;
use App\Models\VerificationStatus;
use App\Models\MaritalStatus;
use App\Models\Association;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    use \App\Http\Controllers\Concerns\FiltersMembersQuery;

    private function formData(): array
    {
        return [
            'verificationStatuses' => VerificationStatus::active()->orderBy('name')->get(),
            'finalStatuses'        => FinalStatus::active()->orderBy('name')->get(),
            'maritalStatuses'      => MaritalStatus::active()->orderBy('id')->get(),
            'associations'         => Association::active()->orderBy('id')->get(),
            'representatives'      => User::orderBy('name')->get(),
            'regionsList'          => \App\Models\Region::active()->with('sector')->orderBy('name')->get(),
            'housingStatuses'      => \App\Models\HousingStatus::active()->orderBy('name')->get(),
            'sectorsList'          => \App\Models\Sector::active()->orderBy('name')->get(),
            'delegateList'         => Delegate::orderBy('name')->pluck('name'),
        ];
    }


    public function index(Request $request)
    {
        $search              = $request->get('search');
        $dossierSearch       = trim($request->get('dossier_search', ''));
        $dossierFrom         = trim($request->get('dossier_from', ''));
        $dossierTo           = trim($request->get('dossier_to', ''));
        $verificationIds     = array_filter((array) $request->get('verification_status_id', []));
        $finalStatusIds      = array_filter((array) $request->get('final_status_id', []));
        $maritalStatuses     = array_filter((array) $request->get('marital_status', []));
        $genders             = array_filter((array) $request->get('gender', []));
        $delegates           = array_filter((array) $request->get('delegate', []));
        $specialCases        = $request->get('special_cases', '');
        $specialDescriptions = array_filter((array) $request->get('special_cases_description', []));
        $addresses           = array_filter((array) $request->get('current_address', []));
        $associationIds      = array_filter((array) $request->get('association_id', []));
        $networks            = array_filter((array) $request->get('network', []));

        $filteredQuery = $this->buildFilteredQuery($request);

        $totalAmount = (clone $filteredQuery)->sum('estimated_amount') ?? 0;

        $totalFinalAmount = (clone $filteredQuery)
            ->select(DB::raw('SUM(COALESCE(estimated_amount, 0) + COALESCE((SELECT estimated_amount FROM field_visits fv WHERE fv.member_id = members.id ORDER BY fv.created_at DESC LIMIT 1), 0)) as total'))
            ->value('total') ?? 0;

        $query   = $filteredQuery->with(['verificationStatus', 'representative', 'paymentInfo', 'fieldVisits.status', 'region', 'sector', 'housingStatus', 'latestFieldVisit']);
        $members = $query->orderByRaw('CAST(dossier_number AS UNSIGNED) ASC')->paginate(20)->withQueryString();

        // Collect duplicate IBANs for warning indicator
        $duplicateIbans = DB::table('payment_info')
            ->select('iban')
            ->whereNotNull('iban')
            ->where('iban', '!=', '')
            ->groupBy('iban')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('iban')
            ->flip()
            ->toArray();
        $regionIds            = array_filter((array) $request->get('region_id', []));
        $sectorIds            = array_filter((array) $request->get('sector_id', []));
        $housingStatusIds     = array_filter((array) $request->get('housing_status_id', []));
        $fieldVisitStatusIds  = array_filter((array) $request->get('field_visit_status_id', []));
        $fvHouseTypeIds       = array_filter((array) $request->get('fv_house_type_id', []));
        $fvVisitors           = array_filter((array) $request->get('fv_visitors', []));
        $fvCreatedByIds       = array_filter((array) $request->get('fv_created_by', []));
        $fvDateFrom           = trim($request->get('fv_date_from', ''));
        $fvDateTo             = trim($request->get('fv_date_to', ''));
        $fvAmountFrom         = trim($request->get('fv_amount_from', ''));
        $fvAmountTo           = trim($request->get('fv_amount_to', ''));
        $fvHouseConditionIds  = array_filter((array) $request->get('fv_house_condition_id', []));
        $fvNotes              = trim($request->get('fv_notes', ''));
        $fvHasVideo           = $request->get('fv_has_video', '');
        $fvHasSpecialCase     = $request->get('fv_has_special_case', '');
        $fvCount              = trim($request->get('fv_count', ''));
        $estimatedFrom        = trim($request->get('estimated_from', ''));
        $estimatedTo          = trim($request->get('estimated_to', ''));
        $paymentsCountFrom    = trim($request->get('payments_count_from', ''));
        $paymentsCountTo      = trim($request->get('payments_count_to', ''));
        $regionList           = \App\Models\Region::active()->orderBy('name')->get();
        $sectorList           = \App\Models\Sector::active()->orderBy('name')->get();
        $verificationStatuses = VerificationStatus::active()->orderBy('name')->get();
        $finalStatusList      = FinalStatus::active()->orderBy('name')->get();
        $maritalStatusList    = MaritalStatus::active()->orderBy('id')->get();
        $houseTypes           = \App\Models\HouseType::active()->orderBy('id')->get();
        $houseConditions      = \App\Models\HouseCondition::active()->orderBy('name')->get();
        $housingStatusList    = \App\Models\HousingStatus::active()->orderBy('name')->get();
        $delegateList            = Delegate::orderBy('name')->pluck('name');

        $secondPersons           = array_filter((array) $request->get('second_person', []));
        $secondPersonList        = Member::whereNotNull('second_person')
                                         ->where('second_person', '!=', '')
                                         ->distinct()
                                         ->orderBy('second_person')
                                         ->pluck('second_person');

        $specialDescriptionList  = Member::whereNotNull('special_cases_description')
                                         ->where('special_cases_description', '!=', '')
                                         ->distinct()
                                         ->orderBy('special_cases_description')
                                         ->pluck('special_cases_description');

        $addressList             = Member::whereNotNull('current_address')
                                         ->where('current_address', '!=', '')
                                         ->distinct()
                                         ->orderBy('current_address')
                                         ->pluck('current_address');

        $associationList      = Association::active()->orderBy('name')->get();
        $fieldVisitStatuses   = FieldVisitStatus::active()->orderBy('id')->get();
        $fvVisitorList        = \App\Models\FieldVisit::whereNotNull('visitor')
                                    ->where('visitor', '!=', '')
                                    ->distinct()
                                    ->orderBy('visitor')
                                    ->pluck('visitor');

        $fvCreatedByList      = \App\Models\User::whereIn('id',
                                    \App\Models\FieldVisit::whereNotNull('created_by')->distinct()->pluck('created_by')
                                )->orderBy('name')->get(['id', 'name']);

        $paymentDataEntries     = array_filter((array) $request->get('payment_data_entry', []));
        $paymentDataEntryList   = \App\Models\PaymentInfo::whereNotNull('data_entry_name')
                                    ->where('data_entry_name', '!=', '')
                                    ->distinct()
                                    ->orderBy('data_entry_name')
                                    ->pluck('data_entry_name');

        return view('members.index', compact(
            'members', 'search', 'dossierSearch', 'dossierFrom', 'dossierTo', 'totalAmount', 'totalFinalAmount',
            'verificationIds', 'finalStatusIds', 'maritalStatuses', 'genders', 'delegates', 'secondPersons', 'specialCases', 'specialDescriptions', 'addresses', 'associationIds', 'networks', 'fieldVisitStatusIds', 'regionIds', 'sectorIds', 'housingStatusIds',
            'estimatedFrom', 'estimatedTo', 'paymentsCountFrom', 'paymentsCountTo',
            'fvHouseTypeIds', 'fvHouseConditionIds', 'fvVisitors', 'fvVisitorList', 'fvCreatedByIds', 'fvCreatedByList', 'fvDateFrom', 'fvDateTo', 'fvAmountFrom', 'fvAmountTo', 'fvNotes', 'fvHasVideo', 'fvHasSpecialCase', 'fvCount',
            'verificationStatuses', 'finalStatusList', 'maritalStatusList', 'delegateList', 'secondPersonList', 'specialDescriptionList', 'addressList', 'associationList',
            'duplicateIbans', 'fieldVisitStatuses', 'regionList', 'sectorList', 'houseTypes', 'houseConditions', 'housingStatusList',
            'paymentDataEntries', 'paymentDataEntryList'
        ));
    }

    public function mapIndex(Request $request)
    {
        $members = Member::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['verificationStatus', 'finalStatus'])
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'dossier_number', 'gender', 'latitude', 'longitude',
                   'verification_status_id', 'final_status_id', 'sham_cash_account']);

        $totalCount = $members->count();

        return view('members.map', compact('members', 'totalCount'));
    }

    public function export(Request $request)
    {
        $query = $this->buildFilteredQuery($request)
            ->orderByRaw('CAST(dossier_number AS UNSIGNED) ASC');

        $filename = 'أعضاء-مسالك-النور-' . now()->format('Y-m-d') . '.xlsx';

        ActivityLogger::log('exported', 'تصدير قائمة الأعضاء إلى Excel');

        return Excel::download(new MembersExport($query), $filename);
    }

    public function create()
    {
        ActivityLogger::log('viewed', 'فتح نموذج إضافة مستفيد جديد');
        return view('members.create', array_merge($this->formData(), ['visitAmount' => 0]));
    }

    public function show(Member $member)
    {
        $member->load(['scores', 'paymentInfo', 'paymentInfoAI', 'association', 'associations', 'verificationStatus', 'representative', 'images.uploader', 'fieldVisits.status', 'fieldVisits.houseType', 'fieldVisits.houseCondition']);
        $fieldVisitStatuses = \App\Models\FieldVisitStatus::active()->orderBy('id')->get();
        $houseTypes         = \App\Models\HouseType::active()->orderBy('id')->get();
        $houseConditions    = \App\Models\HouseCondition::active()->orderBy('name')->get();
        ActivityLogger::log('viewed', "عرض بيانات المستفيد: {$member->full_name}", $member);
        return view('members.show', compact('member', 'fieldVisitStatuses', 'houseTypes', 'houseConditions'));
    }

    // ── Bulk Amount Editor ─────────────────────────────────────────────

    private function buildBulkAmountQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query     = $this->buildFilteredQuery($request);
        $hasAmount = $request->get('has_amount', '');

        if ($hasAmount === '1') $query->whereNotNull('estimated_amount')->where('estimated_amount', '>', 0);
        if ($hasAmount === '0') $query->where(fn($q) => $q->whereNull('estimated_amount')->orWhere('estimated_amount', 0));

        return $query;
    }

    public function bulkAmountShow(Request $request)
    {
        $base    = $this->buildBulkAmountQuery($request);
        $members = (clone $base)->with(['verificationStatus', 'latestFieldVisit'])
                                ->orderByRaw('CAST(dossier_number AS UNSIGNED)')
                                ->paginate(60)->withQueryString();

        $totalCount       = (clone $base)->count();
        $withAmount       = (clone $base)->whereNotNull('estimated_amount')->where('estimated_amount', '>', 0)->count();
        $totalAmount      = (clone $base)->sum('estimated_amount');
        $withFinalAmount  = (clone $base)->whereHas('fieldVisits', fn($q) => $q->whereNotNull('estimated_amount'))->count();
        $totalFinalAmount = (clone $base)
            ->select(DB::raw('SUM(COALESCE(estimated_amount, 0) + COALESCE((SELECT estimated_amount FROM field_visits fv WHERE fv.member_id = members.id ORDER BY fv.created_at DESC LIMIT 1), 0)) as total'))
            ->value('total') ?? 0;

        $search              = trim($request->get('search', ''));
        $dossierFrom         = trim($request->get('dossier_from', ''));
        $dossierTo           = trim($request->get('dossier_to', ''));
        $verificationIds     = array_filter((array) $request->get('verification_status_id', []));
        $finalStatusIds      = array_filter((array) $request->get('final_status_id', []));
        $maritalStatuses     = array_filter((array) $request->get('marital_status', []));
        $genders             = array_filter((array) $request->get('gender', []));
        $delegates           = array_filter((array) $request->get('delegate', []));
        $secondPersons       = array_filter((array) $request->get('second_person', []));
        $specialCases        = $request->get('special_cases', '');
        $specialDescriptions = array_filter((array) $request->get('special_cases_description', []));
        $addresses           = array_filter((array) $request->get('current_address', []));
        $associationIds      = array_filter((array) $request->get('association_id', []));
        $networks            = array_filter((array) $request->get('network', []));
        $housingStatusIds    = array_filter((array) $request->get('housing_status_id', []));
        $regionIds           = array_filter((array) $request->get('region_id', []));
        $estimatedFrom       = trim($request->get('estimated_from', ''));
        $estimatedTo         = trim($request->get('estimated_to', ''));
        $shamCash            = array_filter((array) $request->get('sham_cash', []));

        $verificationStatuses   = VerificationStatus::active()->orderBy('name')->get();
        $finalStatusList        = FinalStatus::active()->orderBy('name')->get();
        $maritalStatusList      = \App\Models\MaritalStatus::active()->orderBy('name')->get();
        $housingStatusList      = \App\Models\HousingStatus::active()->orderBy('name')->get();
        $regionList             = \App\Models\Region::orderBy('name')->get();
        $associationList        = Association::active()->orderBy('name')->get();
        $fieldVisitStatuses     = \App\Models\FieldVisitStatus::active()->orderBy('id')->get();
        $houseTypes             = \App\Models\HouseType::active()->orderBy('id')->get();
        $houseConditions        = \App\Models\HouseCondition::active()->orderBy('name')->get();

        $addressList            = Member::whereNotNull('current_address')->where('current_address', '!=', '')->distinct()->orderBy('current_address')->pluck('current_address');
        $delegateList           = Delegate::orderBy('name')->pluck('name');
        $secondPersonList       = Member::whereNotNull('second_person')->where('second_person', '!=', '')->distinct()->orderBy('second_person')->pluck('second_person');
        $specialDescriptionList = Member::whereNotNull('special_cases_description')->where('special_cases_description', '!=', '')->distinct()->orderBy('special_cases_description')->pluck('special_cases_description');

        $hasAmount          = $request->get('has_amount', '');
        $fvHouseTypeIds     = array_filter((array) $request->get('fv_house_type_id', []));
        $fvHouseConditionIds= array_filter((array) $request->get('fv_house_condition_id', []));
        $fvVisitors         = array_filter((array) $request->get('fv_visitors', []));
        $fvCreatedByIds     = array_filter((array) $request->get('fv_created_by', []));
        $fvDateFrom         = trim($request->get('fv_date_from', ''));
        $fvDateTo           = trim($request->get('fv_date_to', ''));
        $fvAmountFrom       = trim($request->get('fv_amount_from', ''));
        $fvAmountTo         = trim($request->get('fv_amount_to', ''));
        $fvNotes            = trim($request->get('fv_notes', ''));
        $fvHasVideo         = $request->get('fv_has_video', '');
        $fvHasSpecialCase   = $request->get('fv_has_special_case', '');
        $fvCount            = $request->get('fv_count', '');
        $fieldVisitStatusIds= array_filter((array) $request->get('field_visit_status_id', []));
        $fvVisitorList      = \App\Models\FieldVisit::whereNotNull('visitor')->where('visitor', '!=', '')->distinct()->orderBy('visitor')->pluck('visitor');
        $fvCreatedByList    = \App\Models\User::whereIn('id',
                                \App\Models\FieldVisit::whereNotNull('created_by')->distinct()->pluck('created_by')
                             )->orderBy('name')->get(['id', 'name']);

        $hasFvFilters = !empty($fieldVisitStatusIds) || !empty($fvHouseTypeIds) || !empty($fvHouseConditionIds) || !empty($fvVisitors)
            || !empty($fvCreatedByIds)
            || $fvDateFrom !== '' || $fvDateTo !== '' || $fvAmountFrom !== '' || $fvAmountTo !== ''
            || $fvNotes !== '' || $fvHasVideo !== '' || $fvHasSpecialCase !== '';

        return view('members.bulk-amount', compact(
            'members', 'totalCount', 'withAmount', 'totalAmount', 'withFinalAmount', 'totalFinalAmount',
            'search', 'dossierFrom', 'dossierTo', 'hasAmount',
            'verificationIds', 'finalStatusIds', 'maritalStatuses', 'genders', 'delegates', 'secondPersons',
            'specialCases', 'specialDescriptions', 'addresses', 'associationIds', 'networks',
            'housingStatusIds', 'regionIds', 'estimatedFrom', 'estimatedTo', 'shamCash',
            'verificationStatuses', 'finalStatusList', 'maritalStatusList', 'housingStatusList', 'regionList',
            'associationList', 'delegateList', 'secondPersonList', 'specialDescriptionList', 'addressList',
            'fieldVisitStatuses', 'houseTypes', 'houseConditions',
            'fieldVisitStatusIds', 'fvHouseTypeIds', 'fvHouseConditionIds', 'fvVisitors', 'fvVisitorList',
            'fvCreatedByIds', 'fvCreatedByList',
            'fvDateFrom', 'fvDateTo', 'fvAmountFrom', 'fvAmountTo', 'fvNotes',
            'fvHasVideo', 'fvHasSpecialCase', 'fvCount', 'hasFvFilters'
        ));
    }

    public function bulkAmountApply(Request $request)
    {
        $request->validate([
            'score_deduction'        => 'nullable|integer|min:0',
            'score_deduction_reason' => 'nullable|string|max:1000',
            'score_addition'         => 'nullable|integer|min:0',
            'score_addition_reason'  => 'nullable|string|max:1000',
            'apply_to'               => 'required|in:selected,filtered',
            'member_ids'             => 'array',
        ]);

        $mode      = $request->get('ba_mode', 'deduct');
        $isAdd     = $mode === 'add';
        $applyTo   = $request->apply_to;

        $query = $applyTo === 'selected'
            ? Member::whereIn('id', $request->input('member_ids', []))
            : $this->buildBulkAmountQuery($request);

        $count = $query->count();
        if ($count === 0) {
            return back()->with('error', 'لم يتم تحديد أي أعضاء.');
        }

        if ($isAdd) {
            $addition = (int) $request->score_addition;
            $reason   = $request->score_addition_reason ?: null;
            $label    = "إضافة {$addition} نقطة لـ {$count} عضو";
            if ($reason) $label .= " — السبب: {$reason}";

            if (!$this->isAdmin()) {
                $memberIds = $query->pluck('id')->all();
                PendingChange::createWithSnapshots([
                    'model_type'   => 'member',
                    'model_id'     => null,
                    'action'       => 'bulk_score_addition',
                    'payload'      => [
                        'score_addition'        => $addition,
                        'score_addition_reason' => $reason,
                        'member_ids'            => $memberIds,
                        'count'                 => $count,
                        'label'                 => $label,
                    ],
                    'original'     => null,
                    'requested_by' => Auth::id(),
                    'status'       => 'pending',
                ], $memberIds);
                return back()->with('success', "تم إرسال طلب إضافة النقاط ({$label}) وهو بانتظار موافقة المسؤول.");
            }

            $this->applyBulkScoreAdditionToIds($query->pluck('id')->all(), $addition, $reason);
            ActivityLogger::log('updated', "إضافة جماعية للنقاط: {$label}");
            return back()->with('success', "تم تطبيق إضافة النقاط بنجاح: {$label}.");
        }

        $deduction = (int) $request->score_deduction;
        $reason    = $request->score_deduction_reason ?: null;
        $label     = "انقاص {$deduction} نقطة من {$count} عضو";
        if ($reason) $label .= " — السبب: {$reason}";

        if (!$this->isAdmin()) {
            $memberIds = $query->pluck('id')->all();
            PendingChange::createWithSnapshots([
                'model_type'   => 'member',
                'model_id'     => null,
                'action'       => 'bulk_score_deduction',
                'payload'      => [
                    'score_deduction'        => $deduction,
                    'score_deduction_reason' => $reason,
                    'member_ids'             => $memberIds,
                    'count'                  => $count,
                    'label'                  => $label,
                ],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ], $memberIds);
            return back()->with('success', "تم إرسال طلب انقاص النقاط ({$label}) وهو بانتظار موافقة المسؤول.");
        }

        $this->applyBulkScoreDeductionToIds($query->pluck('id')->all(), $deduction, $reason);
        ActivityLogger::log('updated', "انقاص جماعي للنقاط: {$label}");
        return back()->with('success', "تم تطبيق انقاص النقاط بنجاح: {$label}.");
    }

    private function applyBulkScoreAdditionToIds(array $ids, int $addition, ?string $reason): void
    {
        foreach ($ids as $memberId) {
            $member = Member::find($memberId);
            if (!$member) continue;

            $scores = $member->scores ?? new MemberScore(['member_id' => $memberId]);

            $rawScore = ($scores->work_score            ?? 0)
                      + ($scores->housing_score          ?? 0)
                      + ($scores->dependents_score       ?? 0)
                      + ($scores->dependent_status_score ?? 0)
                      + ($scores->illness_score          ?? 0)
                      + ($scores->special_cases_score    ?? 0);

            $deduction    = (int)($scores->score_deduction ?? 0);
            $newAddition  = (int)($scores->score_addition ?? 0) + $addition;
            $totalScore   = max(0, $rawScore + $newAddition - $deduction);

            $scores->fill([
                'member_id'             => $memberId,
                'score_addition'        => $newAddition,
                'score_addition_reason' => $reason,
                'total_score'           => $totalScore,
            ])->save();

            $member->update([
                'score'            => $totalScore,
                'estimated_amount' => $totalScore * 500,
            ]);
        }
    }

    private function applyBulkScoreDeductionToIds(array $ids, int $deduction, ?string $reason): void
    {
        foreach ($ids as $memberId) {
            $member = Member::find($memberId);
            if (!$member) continue;

            $scores = $member->scores ?? new MemberScore(['member_id' => $memberId]);

            $rawScore = ($scores->work_score            ?? 0)
                      + ($scores->housing_score          ?? 0)
                      + ($scores->dependents_score       ?? 0)
                      + ($scores->dependent_status_score ?? 0)
                      + ($scores->illness_score          ?? 0)
                      + ($scores->special_cases_score    ?? 0);

            $addition     = (int)($scores->score_addition ?? 0);
            $newDeduction = (int)($scores->score_deduction ?? 0) + $deduction;
            $totalScore   = max(0, $rawScore + $addition - $newDeduction);

            $scores->fill([
                'member_id'              => $memberId,
                'score_deduction'        => $newDeduction,
                'score_deduction_reason' => $reason,
                'total_score'            => $totalScore,
            ])->save();

            $member->update([
                'score'            => $totalScore,
                'estimated_amount' => $totalScore * 500,
            ]);
        }
    }

    // ── Field-Visit Amount Reduction ───────────────────────────────────

    public function fvReductionShow(Request $request)
    {
        $base = $this->buildFilteredQuery($request);

        $fieldVisitStatusIds = array_filter((array) $request->get('field_visit_status_id', []));
        $fvHouseTypeIds      = array_filter((array) $request->get('fv_house_type_id', []));
        $fvHouseConditionIds = array_filter((array) $request->get('fv_house_condition_id', []));
        $fvVisitors          = array_filter((array) $request->get('fv_visitors', []));
        $fvCreatedByIds      = array_filter((array) $request->get('fv_created_by', []));
        $fvDateFrom          = trim($request->get('fv_date_from', ''));
        $fvDateTo            = trim($request->get('fv_date_to', ''));
        $fvAmountFrom        = trim($request->get('fv_amount_from', ''));
        $fvAmountTo          = trim($request->get('fv_amount_to', ''));
        $fvNotes             = trim($request->get('fv_notes', ''));
        $fvHasVideo          = $request->get('fv_has_video', '');
        $fvHasSpecialCase    = $request->get('fv_has_special_case', '');
        $fvCount             = $request->get('fv_count', '');
        $hasFvFilters        = !empty($fieldVisitStatusIds) || !empty($fvHouseTypeIds) || !empty($fvHouseConditionIds)
            || !empty($fvVisitors) || !empty($fvCreatedByIds)
            || $fvDateFrom !== '' || $fvDateTo !== '' || $fvAmountFrom !== '' || $fvAmountTo !== ''
            || $fvNotes !== '' || $fvHasVideo !== '' || $fvHasSpecialCase !== '';

        $search          = trim($request->get('search', ''));
        $dossierFrom     = trim($request->get('dossier_from', ''));
        $dossierTo       = trim($request->get('dossier_to', ''));
        $verificationIds = array_filter((array) $request->get('verification_status_id', []));
        $finalStatusIds  = array_filter((array) $request->get('final_status_id', []));
        $associationIds  = array_filter((array) $request->get('association_id', []));
        $regionIds       = array_filter((array) $request->get('region_id', []));
        $sectorIds       = array_filter((array) $request->get('sector_id', []));
        $delegates       = array_filter((array) $request->get('delegate', []));
        $estimatedFrom      = trim($request->get('estimated_from', ''));
        $estimatedTo        = trim($request->get('estimated_to', ''));
        $shamCash           = array_filter((array) $request->get('sham_cash', []));
        $fvReductionApplied = $request->get('fv_reduction_applied', '');

        if ($fvReductionApplied === 'yes') {
            $base->whereHas('scores', fn($q) => $q->where('score_deduction', '>', 0));
        } elseif ($fvReductionApplied === 'no') {
            $base->where(fn($q) => $q
                ->whereDoesntHave('scores')
                ->orWhereHas('scores', fn($q2) => $q2->where(fn($q3) => $q3->where('score_deduction', 0)->orWhereNull('score_deduction')))
            );
        }

        $totalCount  = (clone $base)->count();
        $withAmount  = (clone $base)->whereNotNull('estimated_amount')->where('estimated_amount', '>', 0)->count();
        $totalAmount = (clone $base)->whereNotNull('estimated_amount')->sum('estimated_amount');

        $members = (clone $base)
            ->with(['verificationStatus', 'finalStatus', 'region', 'association', 'latestFieldVisit.status'])
            ->orderByRaw('CAST(dossier_number AS UNSIGNED)')
            ->paginate(60)->withQueryString();

        $verificationStatuses = VerificationStatus::active()->orderBy('name')->get();
        $finalStatusList      = FinalStatus::active()->orderBy('name')->get();
        $associationList      = Association::active()->orderBy('name')->get();
        $regionList           = \App\Models\Region::active()->orderBy('name')->get();
        $sectorList           = \App\Models\Sector::active()->orderBy('name')->get();
        $delegateList         = Delegate::orderBy('name')->pluck('name');
        $fieldVisitStatuses   = \App\Models\FieldVisitStatus::active()->orderBy('id')->get();
        $houseTypes           = \App\Models\HouseType::active()->orderBy('id')->get();
        $houseConditions      = \App\Models\HouseCondition::active()->orderBy('name')->get();
        $fvVisitorList        = \App\Models\FieldVisit::whereNotNull('visitor')->where('visitor','!=','')->distinct()->orderBy('visitor')->pluck('visitor');
        $fvCreatedByList      = \App\Models\User::whereIn('id', \App\Models\FieldVisit::whereNotNull('created_by')->distinct()->pluck('created_by'))->orderBy('name')->get(['id','name']);

        return view('members.fv-reduction', compact(
            'members', 'totalCount', 'withAmount', 'totalAmount',
            'search', 'dossierFrom', 'dossierTo', 'estimatedFrom', 'estimatedTo', 'shamCash', 'fvReductionApplied',
            'verificationIds', 'finalStatusIds', 'associationIds', 'regionIds', 'sectorIds', 'delegates',
            'fieldVisitStatusIds', 'fvHouseTypeIds', 'fvHouseConditionIds', 'fvVisitors', 'fvCreatedByIds',
            'fvDateFrom', 'fvDateTo', 'fvAmountFrom', 'fvAmountTo', 'fvNotes',
            'fvHasVideo', 'fvHasSpecialCase', 'fvCount', 'hasFvFilters',
            'verificationStatuses', 'finalStatusList', 'associationList', 'regionList', 'sectorList',
            'delegateList', 'fieldVisitStatuses', 'houseTypes', 'houseConditions',
            'fvVisitorList', 'fvCreatedByList'
        ));
    }

    public function fvReductionApply(Request $request)
    {
        $request->validate([
            'percentage' => 'required|numeric|min:1|max:100',
            'reason'     => 'nullable|string|max:500',
            'apply_to'   => 'required|in:selected,filtered',
            'member_ids' => 'array',
            'mode'       => 'nullable|in:reduce,raise',
        ]);

        $percentage = (float) $request->percentage;
        $reason     = $request->reason ?: null;
        $applyTo    = $request->apply_to;
        $mode       = $request->input('mode', 'reduce');

        $query = $applyTo === 'selected'
            ? Member::whereIn('id', $request->input('member_ids', []))
            : $this->buildFilteredQuery($request);

        $query->whereNotNull('estimated_amount')->where('estimated_amount', '>', 0);

        $count = $query->count();
        if ($count === 0) {
            return back()->with('error', 'لا يوجد أعضاء لديهم مبلغ مقدّر في الاختيار الحالي.');
        }

        $verb  = $mode === 'raise' ? 'رفع' : 'تخفيض';
        $label = "{$verb} {$percentage}% من المبلغ المقدّر لـ {$count} عضو";
        if ($reason) $label .= " — السبب: {$reason}";

        $action = $mode === 'raise' ? 'bulk_fv_raise' : 'bulk_fv_reduction';

        if (!$this->isAdmin()) {
            $memberIds = $query->pluck('id')->all();
            PendingChange::createWithSnapshots([
                'model_type'   => 'member',
                'model_id'     => null,
                'action'       => $action,
                'payload'      => [
                    'percentage' => $percentage,
                    'reason'     => $reason,
                    'member_ids' => $memberIds,
                    'count'      => $count,
                    'label'      => $label,
                ],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ], $memberIds);
            return back()->with('success', "تم إرسال طلب ({$label}) وهو بانتظار موافقة المسؤول.");
        }

        $ids = $query->pluck('id')->all();
        if ($mode === 'raise') {
            $this->applyFvRaiseToIds($ids, $percentage, $reason);
        } else {
            $this->applyFvReductionToIds($ids, $percentage, $reason);
        }

        $pastVerb = $mode === 'raise' ? 'الرفع' : 'التخفيض';
        ActivityLogger::log('updated', "{$verb} جماعي للمبلغ: {$label}");
        return back()->with('success', "تم تطبيق {$pastVerb} بنجاح: {$label}.");
    }

    private function applyFvRaiseToIds(array $ids, float $percentage, ?string $reason): void
    {
        foreach ($ids as $memberId) {
            $member = Member::with('scores')->find($memberId);
            if (!$member || !$member->estimated_amount) continue;

            $currentAmount = (float) $member->estimated_amount;
            $raise         = (int) floor($currentAmount * $percentage / 100);
            $pointsAdded   = (int) floor($raise / 500);

            if ($pointsAdded <= 0) continue;

            $scores = $member->scores ?? new MemberScore(['member_id' => $memberId]);

            $rawScore = ($scores->work_score            ?? 0)
                      + ($scores->housing_score          ?? 0)
                      + ($scores->dependents_score       ?? 0)
                      + ($scores->dependent_status_score ?? 0)
                      + ($scores->illness_score          ?? 0)
                      + ($scores->special_cases_score    ?? 0);

            $deduction   = (int)($scores->score_deduction ?? 0);
            $newAddition = (int)($scores->score_addition  ?? 0) + $pointsAdded;
            $totalScore  = max(0, $rawScore + $newAddition - $deduction);
            $newAmount   = $totalScore * 500;

            $scores->fill([
                'member_id'             => $memberId,
                'score_addition'        => $newAddition,
                'score_addition_reason' => $reason ?? 'رفع الجولة الميدانية',
                'total_score'           => $totalScore,
            ])->save();

            DB::table('members')->where('id', $memberId)->update([
                'score'            => $totalScore,
                'estimated_amount' => $newAmount,
            ]);
        }
    }

    private function applyFvReductionToIds(array $ids, float $percentage, ?string $reason): void
    {
        foreach ($ids as $memberId) {
            $member = Member::with('scores')->find($memberId);
            if (!$member || !$member->estimated_amount) continue;

            $currentAmount  = (float) $member->estimated_amount;
            $reduction      = (int) floor($currentAmount * $percentage / 100);
            $pointsDeducted = (int) floor($reduction / 500);

            if ($pointsDeducted <= 0) continue;

            $scores = $member->scores ?? new MemberScore(['member_id' => $memberId]);

            $rawScore = ($scores->work_score            ?? 0)
                      + ($scores->housing_score          ?? 0)
                      + ($scores->dependents_score       ?? 0)
                      + ($scores->dependent_status_score ?? 0)
                      + ($scores->illness_score          ?? 0)
                      + ($scores->special_cases_score    ?? 0);

            $addition     = (int)($scores->score_addition  ?? 0);
            $newDeduction = (int)($scores->score_deduction ?? 0) + $pointsDeducted;
            $totalScore   = max(0, $rawScore + $addition - $newDeduction);
            $newAmount    = $totalScore * 500;

            $scores->fill([
                'member_id'              => $memberId,
                'score_deduction'        => $newDeduction,
                'score_deduction_reason' => $reason ?? 'تخفيض الجولة الميدانية',
                'total_score'            => $totalScore,
            ])->save();

            DB::table('members')->where('id', $memberId)->update([
                'score'            => $totalScore,
                'estimated_amount' => $newAmount,
            ]);
        }
    }

    // ── Bulk Payments ──────────────────────────────────────────────────

    public function bulkPaymentsShow(Request $request)
    {
        $search              = $request->get('search', '');
        $dossierSearch       = trim($request->get('dossier_search', ''));
        $dossierFrom         = trim($request->get('dossier_from', ''));
        $dossierTo           = trim($request->get('dossier_to', ''));
        $verificationIds     = array_filter((array) $request->get('verification_status_id', []));
        $finalStatusIds      = array_filter((array) $request->get('final_status_id', []));
        $maritalStatuses     = array_filter((array) $request->get('marital_status', []));
        $genders             = array_filter((array) $request->get('gender', []));
        $delegates           = array_filter((array) $request->get('delegate', []));
        $secondPersons       = array_filter((array) $request->get('second_person', []));
        $specialCases        = $request->get('special_cases', '');
        $specialDescriptions = array_filter((array) $request->get('special_cases_description', []));
        $addresses           = array_filter((array) $request->get('current_address', []));
        $associationIds      = array_filter((array) $request->get('association_id', []));
        $networks            = array_filter((array) $request->get('network', []));
        $housingStatusIds    = array_filter((array) $request->get('housing_status_id', []));
        $regionIds           = array_filter((array) $request->get('region_id', []));
        $sectorIds           = array_filter((array) $request->get('sector_id', []));
        $estimatedFrom       = trim($request->get('estimated_from', ''));
        $estimatedTo         = trim($request->get('estimated_to', ''));
        $paymentsCountFrom   = trim($request->get('payments_count_from', ''));
        $paymentsCountTo     = trim($request->get('payments_count_to', ''));
        $shamCash            = array_filter((array) $request->get('sham_cash', []));
        $hasPayments         = $request->get('has_payments', '');
        $paymentDataEntries  = array_filter((array) $request->get('payment_data_entry', []));
        // Field visit filters
        $fieldVisitStatusIds = array_filter((array) $request->get('field_visit_status_id', []));
        $fvHouseTypeIds      = array_filter((array) $request->get('fv_house_type_id', []));
        $fvHouseConditionIds = array_filter((array) $request->get('fv_house_condition_id', []));
        $fvVisitors          = array_filter((array) $request->get('fv_visitors', []));
        $fvCreatedByIds      = array_filter((array) $request->get('fv_created_by', []));
        $fvDateFrom          = trim($request->get('fv_date_from', ''));
        $fvDateTo            = trim($request->get('fv_date_to', ''));
        $fvAmountFrom        = trim($request->get('fv_amount_from', ''));
        $fvAmountTo          = trim($request->get('fv_amount_to', ''));
        $fvNotes             = trim($request->get('fv_notes', ''));
        $fvHasVideo          = $request->get('fv_has_video', '');
        $fvHasSpecialCase    = $request->get('fv_has_special_case', '');
        $fvCount             = $request->get('fv_count', '');

        $base = $this->buildFilteredQuery($request);
        if ($hasPayments === '1') $base->whereNotNull('payments_count');
        if ($hasPayments === '0') $base->whereNull('payments_count');

        $totalCount    = (clone $base)->count();
        $withPayments  = (clone $base)->whereNotNull('payments_count')->count();
        $totalPayments = (clone $base)->sum('payments_count') ?? 0;

        $members = (clone $base)->with(['verificationStatus', 'finalStatus', 'region', 'association'])
                                ->orderByRaw('CAST(dossier_number AS UNSIGNED)')
                                ->paginate(60)->withQueryString();

        $verificationStatuses   = VerificationStatus::active()->orderBy('name')->get();
        $finalStatusList        = FinalStatus::active()->orderBy('name')->get();
        $maritalStatusList      = \App\Models\MaritalStatus::active()->orderBy('id')->get();
        $housingStatusList      = \App\Models\HousingStatus::active()->orderBy('name')->get();
        $regionList             = \App\Models\Region::active()->orderBy('name')->get();
        $sectorList             = \App\Models\Sector::active()->orderBy('name')->get();
        $associationList        = Association::active()->orderBy('name')->get();
        $fieldVisitStatuses     = FieldVisitStatus::active()->orderBy('id')->get();
        $houseTypes             = \App\Models\HouseType::active()->orderBy('id')->get();
        $houseConditions        = \App\Models\HouseCondition::active()->orderBy('name')->get();
        $delegateList           = Delegate::orderBy('name')->pluck('name');
        $secondPersonList       = Member::whereNotNull('second_person')->where('second_person','!=','')->distinct()->orderBy('second_person')->pluck('second_person');
        $specialDescriptionList = Member::whereNotNull('special_cases_description')->where('special_cases_description','!=','')->distinct()->orderBy('special_cases_description')->pluck('special_cases_description');
        $addressList            = Member::whereNotNull('current_address')->where('current_address','!=','')->distinct()->orderBy('current_address')->pluck('current_address');
        $paymentDataEntryList   = \App\Models\PaymentInfo::whereNotNull('data_entry_name')->where('data_entry_name','!=','')->distinct()->orderBy('data_entry_name')->pluck('data_entry_name');
        $fvVisitorList          = \App\Models\FieldVisit::whereNotNull('visitor')->where('visitor','!=','')->distinct()->orderBy('visitor')->pluck('visitor');
        $fvCreatedByList        = \App\Models\User::whereIn('id', \App\Models\FieldVisit::whereNotNull('created_by')->distinct()->pluck('created_by'))->orderBy('name')->get(['id', 'name']);

        $hasFvFilters = !empty($fieldVisitStatusIds) || !empty($fvHouseTypeIds) || !empty($fvHouseConditionIds)
            || !empty($fvVisitors) || !empty($fvCreatedByIds)
            || $fvDateFrom !== '' || $fvDateTo !== '' || $fvAmountFrom !== '' || $fvAmountTo !== ''
            || $fvNotes !== '' || $fvHasVideo !== '' || $fvHasSpecialCase !== '';

        return view('members.bulk-payments', compact(
            'members', 'totalCount', 'withPayments', 'totalPayments',
            'search', 'dossierSearch', 'dossierFrom', 'dossierTo', 'hasPayments',
            'verificationIds', 'finalStatusIds', 'maritalStatuses', 'genders', 'delegates', 'secondPersons',
            'specialCases', 'specialDescriptions', 'addresses', 'associationIds', 'networks',
            'housingStatusIds', 'regionIds', 'sectorIds', 'estimatedFrom', 'estimatedTo',
            'paymentsCountFrom', 'paymentsCountTo', 'shamCash', 'paymentDataEntries',
            'fieldVisitStatusIds', 'fvHouseTypeIds', 'fvHouseConditionIds', 'fvVisitors', 'fvCreatedByIds',
            'fvDateFrom', 'fvDateTo', 'fvAmountFrom', 'fvAmountTo', 'fvNotes', 'fvHasVideo', 'fvHasSpecialCase', 'fvCount',
            'hasFvFilters',
            'verificationStatuses', 'finalStatusList', 'maritalStatusList', 'housingStatusList',
            'regionList', 'sectorList', 'associationList', 'fieldVisitStatuses', 'houseTypes', 'houseConditions',
            'delegateList', 'secondPersonList', 'specialDescriptionList', 'addressList',
            'paymentDataEntryList', 'fvVisitorList', 'fvCreatedByList'
        ));
    }

    public function bulkPaymentsApply(Request $request)
    {
        $request->validate([
            'payments_count' => 'required|integer|min:0',
            'operation'      => 'required|in:add,subtract,set',
            'apply_to'       => 'required|in:selected,filtered',
            'member_ids'     => 'array',
        ]);

        $amount    = (int) $request->payments_count;
        $operation = $request->operation;
        $applyTo   = $request->apply_to;

        $hasPayments = $request->get('has_payments', '');
        $base        = $this->buildFilteredQuery($request);
        if ($hasPayments === '1') $base->whereNotNull('payments_count');
        if ($hasPayments === '0') $base->whereNull('payments_count');

        $query = $applyTo === 'selected'
            ? Member::whereIn('id', $request->input('member_ids', []))
            : $base;

        $count = $query->count();
        if ($count === 0) {
            return back()->with('error', 'لم يتم تحديد أي أعضاء.');
        }

        $opLabel = match($operation) {
            'add'      => "إضافة {$amount} دفعة",
            'subtract' => "طرح {$amount} دفعة",
            default    => "تعيين {$amount} دفعة",
        };
        $label   = "{$opLabel} لـ {$count} عضو";

        if (!$this->isAdmin()) {
            $memberIds = $query->pluck('id')->all();
            PendingChange::createWithSnapshots([
                'model_type'   => 'member',
                'model_id'     => null,
                'action'       => 'bulk_update',
                'payload'      => [
                    'member_ids'    => $memberIds,
                    'count'         => $count,
                    'fields'        => ['payments_count' => $amount],
                    'label'         => $label,
                ],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ], $memberIds);
            return back()->with('success', "تم إرسال طلب ({$label}) وهو بانتظار موافقة المسؤول.");
        }

        $ids = $query->pluck('id')->all();

        $memberData = Member::whereIn('id', $ids)
            ->with('latestFieldVisit')
            ->get(['id', 'payments_count', 'estimated_amount']);

        $totalEstimated = $memberData->sum(fn($m) => $m->final_amount);

        $batch = \App\Models\PaymentBatch::create([
            'label'                  => $request->input('batch_label') ?: $label,
            'payment_date'           => $request->input('payment_date') ?: now()->toDateString(),
            'operation'              => $operation,
            'amount'                 => $amount,
            'members_count'          => $count,
            'total_estimated_amount' => $totalEstimated,
            'notes'                  => $request->input('batch_notes') ?: null,
            'applied_by'             => Auth::id(),
        ]);

        $batchMembers = $memberData->map(function ($member) use ($batch, $operation, $amount) {
            $prev = (int)($member->payments_count ?? 0);
            $new  = match($operation) {
                'add'      => $prev + $amount,
                'subtract' => max(0, $prev - $amount),
                default    => $amount,
            };
            return [
                'batch_id'         => $batch->id,
                'member_id'        => $member->id,
                'previous_count'   => $prev,
                'new_count'        => $new,
                'estimated_amount' => $member->final_amount,
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        })->toArray();

        \App\Models\PaymentBatchMember::insert($batchMembers);

        if ($operation === 'add') {
            DB::table('members')->whereIn('id', $ids)->update([
                'payments_count' => DB::raw('COALESCE(payments_count, 0) + ' . $amount),
            ]);
        } elseif ($operation === 'subtract') {
            DB::table('members')->whereIn('id', $ids)->update([
                'payments_count' => DB::raw('GREATEST(COALESCE(payments_count, 0) - ' . $amount . ', 0)'),
            ]);
        } else {
            DB::table('members')->whereIn('id', $ids)->update(['payments_count' => $amount]);
        }

        ActivityLogger::log('updated', "دفعات جماعية: {$label}");
        return back()->with('success', "تم تطبيق العملية بنجاح: {$label}.");
    }

    public function paymentBatchesIndex(Request $request)
    {
        $search    = trim($request->get('search', ''));
        $operation = $request->get('operation', '');
        $dateFrom  = $request->get('date_from', '');
        $dateTo    = $request->get('date_to', '');

        $query = \App\Models\PaymentBatch::with('appliedBy')
            ->withCount('members');

        if ($search) {
            $query->where(fn($q) => $q->where('label', 'like', "%{$search}%")
                                      ->orWhere('notes', 'like', "%{$search}%"));
        }
        if ($operation) $query->where('operation', $operation);
        if ($dateFrom)  $query->whereDate('payment_date', '>=', $dateFrom);
        if ($dateTo)    $query->whereDate('payment_date', '<=', $dateTo);

        $batches    = $query->orderByDesc('payment_date')->orderByDesc('id')->paginate(30)->withQueryString();
        $totalBatches = \App\Models\PaymentBatch::count();
        $totalMembers = \App\Models\PaymentBatchMember::distinct('member_id')->count();
        $totalAmount  = \App\Models\PaymentBatch::sum('total_estimated_amount');

        return view('members.payment-batches', compact(
            'batches', 'totalBatches', 'totalMembers', 'totalAmount',
            'search', 'operation', 'dateFrom', 'dateTo'
        ));
    }

    public function paymentBatchShow(Request $request, \App\Models\PaymentBatch $batch)
    {
        $batch->load('appliedBy');

        $search      = trim($request->get('search', ''));
        $amountFrom  = trim($request->get('amount_from', ''));
        $amountTo    = trim($request->get('amount_to', ''));
        $diffFilter  = $request->get('diff', '');

        $query = \App\Models\PaymentBatchMember::with('member')
            ->where('payment_batch_members.batch_id', $batch->id)
            ->join('members', 'members.id', '=', 'payment_batch_members.member_id')
            ->select('payment_batch_members.*');

        if ($search) {
            $query->where(fn($q) => $q
                ->where('members.full_name', 'like', "%{$search}%")
                ->orWhere('members.dossier_number', 'like', "%{$search}%")
            );
        }

        if ($amountFrom !== '') {
            $query->where('payment_batch_members.estimated_amount', '>=', (float)$amountFrom);
        }
        if ($amountTo !== '') {
            $query->where('payment_batch_members.estimated_amount', '<=', (float)$amountTo);
        }

        if ($diffFilter === 'added') {
            $query->whereRaw('payment_batch_members.new_count > payment_batch_members.previous_count');
        } elseif ($diffFilter === 'subtracted') {
            $query->whereRaw('payment_batch_members.new_count < payment_batch_members.previous_count');
        } elseif ($diffFilter === 'same') {
            $query->whereRaw('payment_batch_members.new_count = payment_batch_members.previous_count');
        }

        $members = $query
            ->orderByRaw('CAST(members.dossier_number AS UNSIGNED)')
            ->paginate(60)
            ->withQueryString();

        return view('members.payment-batch-show', compact(
            'batch', 'members', 'search', 'amountFrom', 'amountTo', 'diffFilter'
        ));
    }

    // ───────────────────────────────────────────────────────────────────

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function updateRegion(Request $request, Member $member)
    {
        $request->validate(['region_id' => 'nullable|exists:regions,id']);

        $newRegionId = $request->input('region_id') ?: null;

        if (!$this->isAdmin()) {
            $member->load('scores');
            $scores = $member->scores;
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => $member->id,
                'action'       => 'update',
                'payload'      => [
                    'full_name'                 => $member->full_name,
                    'age'                       => $member->age,
                    'gender'                    => $member->gender,
                    'mother_name'               => $member->mother_name,
                    'national_id'               => $member->national_id,
                    'verification_status_id'    => $member->verification_status_id,
                    'dossier_number'            => $member->dossier_number,
                    'current_address'           => $member->current_address,
                    'region_id'                 => $newRegionId,
                    'marital_status'            => $member->marital_status,
                    'disease_type'              => $member->disease_type,
                    'other_association'         => $member->other_association,
                    'phone'                     => $member->phone,
                    'phone2'                    => $member->phone2,
                    'representative_id'         => $member->representative_id,
                    'delegate'                  => $member->delegate,
                    'network'                   => $member->network,
                    'provider_status'           => $member->provider_status,
                    'job'                       => $member->job,
                    'housing_status_id'         => $member->housing_status_id,
                    'dependents_count'          => $member->dependents_count,
                    'illness_details'           => $member->illness_details,
                    'special_cases'             => $member->special_cases,
                    'special_cases_description' => $member->special_cases_description,
                    'sham_cash_account'         => $member->sham_cash_account,
                    'scores' => [
                        'work_score'             => $scores?->work_score             ?? 0,
                        'housing_score'          => $scores?->housing_score          ?? 0,
                        'dependents_score'       => $scores?->dependents_score       ?? 0,
                        'dependent_status_score' => $scores?->dependent_status_score ?? 0,
                        'illness_score'          => $scores?->illness_score          ?? 0,
                        'special_cases_score'    => $scores?->special_cases_score    ?? 0,
                    ],
                ],
                'original'     => ['full_name' => $member->full_name, 'region_id' => $member->region_id],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('members.show', $member)
                             ->with('pending', 'تم إرسال طلب تعديل المنطقة — بانتظار موافقة المسؤول.');
        }

        $member->update(['region_id' => $newRegionId]);
        ActivityLogger::log('updated', "تعديل منطقة المستفيد: {$member->full_name}", $member);

        return redirect()->route('members.show', $member)->with('success', 'تم تحديث المنطقة بنجاح.');
    }

    public function updateSector(Request $request, Member $member)
    {
        $request->validate(['sector_id' => 'nullable|exists:sectors,id']);
        $newSectorId = $request->input('sector_id') ?: null;
        $member->update(['sector_id' => $newSectorId]);
        ActivityLogger::log('updated', "تعديل قطاع المستفيد: {$member->full_name}", $member);
        return redirect()->route('members.show', $member)->with('success', 'تم تحديث القطاع بنجاح.');
    }

    public function updateLocation(Request $request, Member $member)
    {
        $data = $request->validate([
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $newLat = $data['latitude']  ?: null;
        $newLng = $data['longitude'] ?: null;

        if (!$this->isAdmin()) {
            $member->load('scores');
            $scores = $member->scores;
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => $member->id,
                'action'       => 'update',
                'payload'      => [
                    'full_name'                 => $member->full_name,
                    'age'                       => $member->age,
                    'gender'                    => $member->gender,
                    'mother_name'               => $member->mother_name,
                    'national_id'               => $member->national_id,
                    'verification_status_id'    => $member->verification_status_id,
                    'dossier_number'            => $member->dossier_number,
                    'current_address'           => $member->current_address,
                    'region_id'                 => $member->region_id,
                    'marital_status'            => $member->marital_status,
                    'disease_type'              => $member->disease_type,
                    'other_association'         => $member->other_association,
                    'phone'                     => $member->phone,
                    'phone2'                    => $member->phone2,
                    'representative_id'         => $member->representative_id,
                    'delegate'                  => $member->delegate,
                    'network'                   => $member->network,
                    'provider_status'           => $member->provider_status,
                    'job'                       => $member->job,
                    'housing_status_id'         => $member->housing_status_id,
                    'dependents_count'          => $member->dependents_count,
                    'illness_details'           => $member->illness_details,
                    'special_cases'             => $member->special_cases,
                    'special_cases_description' => $member->special_cases_description,
                    'sham_cash_account'         => $member->sham_cash_account,
                    'latitude'                  => $newLat,
                    'longitude'                 => $newLng,
                    'scores' => [
                        'work_score'             => $scores?->work_score             ?? 0,
                        'housing_score'          => $scores?->housing_score          ?? 0,
                        'dependents_score'       => $scores?->dependents_score       ?? 0,
                        'dependent_status_score' => $scores?->dependent_status_score ?? 0,
                        'illness_score'          => $scores?->illness_score          ?? 0,
                        'special_cases_score'    => $scores?->special_cases_score    ?? 0,
                    ],
                ],
                'original'     => [
                    'full_name' => $member->full_name,
                    'latitude'  => $member->latitude,
                    'longitude' => $member->longitude,
                ],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return back()->with('pending', 'تم إرسال طلب تعديل الموقع — بانتظار موافقة المسؤول.');
        }

        $member->update(['latitude' => $newLat, 'longitude' => $newLng]);

        ActivityLogger::log('updated', "تعديل موقع المستفيد على الخريطة: {$member->full_name}", $member);

        return back()->with('success', 'تم تحديث الموقع الجغرافي بنجاح.');
    }

    public function updateAddress(Request $request, Member $member)
    {
        $request->validate(['current_address' => 'nullable|string|max:255']);

        $newAddress = $request->input('current_address') ?: null;

        if (!$this->isAdmin()) {
            $member->load('scores');
            $scores = $member->scores;
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => $member->id,
                'action'       => 'update',
                'payload'      => [
                    'full_name'                 => $member->full_name,
                    'age'                       => $member->age,
                    'gender'                    => $member->gender,
                    'mother_name'               => $member->mother_name,
                    'national_id'               => $member->national_id,
                    'verification_status_id'    => $member->verification_status_id,
                    'dossier_number'            => $member->dossier_number,
                    'current_address'           => $newAddress,
                    'marital_status'            => $member->marital_status,
                    'disease_type'              => $member->disease_type,
                    'other_association'         => $member->other_association,
                    'phone'                     => $member->phone,
                    'phone2'                    => $member->phone2,
                    'representative_id'         => $member->representative_id,
                    'delegate'                  => $member->delegate,
                    'network'                   => $member->network,
                    'provider_status'           => $member->provider_status,
                    'job'                       => $member->job,
                    'housing_status_id'         => $member->housing_status_id,
                    'dependents_count'          => $member->dependents_count,
                    'illness_details'           => $member->illness_details,
                    'special_cases'             => $member->special_cases,
                    'special_cases_description' => $member->special_cases_description,
                    'sham_cash_account'         => $member->sham_cash_account,
                    'scores' => [
                        'work_score'             => $scores?->work_score             ?? 0,
                        'housing_score'          => $scores?->housing_score          ?? 0,
                        'dependents_score'       => $scores?->dependents_score       ?? 0,
                        'dependent_status_score' => $scores?->dependent_status_score ?? 0,
                        'illness_score'          => $scores?->illness_score          ?? 0,
                        'special_cases_score'    => $scores?->special_cases_score    ?? 0,
                    ],
                ],
                'original'     => ['full_name' => $member->full_name, 'current_address' => $member->current_address],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('members.show', $member)
                             ->with('pending', 'تم إرسال طلب تعديل العنوان — بانتظار موافقة المسؤول.');
        }

        $member->update(['current_address' => $newAddress]);
        ActivityLogger::log('updated', "تعديل عنوان المستفيد: {$member->full_name}", $member);

        return redirect()->route('members.show', $member)->with('success', 'تم تحديث العنوان بنجاح.');
    }

    private function buildMemberPayload(Request $request, ?Member $member = null): array
    {
        $rawScorePayload = min(2,  (int)($request->work_score ?? 0))
                         + min(4,  (int)($request->housing_score ?? 0))
                         + min(20, (int)($request->dependents_score ?? 0))
                         + min(2,  (int)($request->dependent_status_score ?? 0))
                         + min(5,  (int)($request->illness_score ?? 0))
                         + min(10, (int)($request->special_cases_score ?? 0));
        $deductionPayload = max(0, (int)($request->score_deduction ?? 0));
        $additionPayload  = max(0, (int)($request->score_addition ?? 0));
        $scores = [
            'work_score'             => min(2,  (int)($request->work_score ?? 0)),
            'housing_score'          => min(4,  (int)($request->housing_score ?? 0)),
            'dependents_score'       => min(20, (int)($request->dependents_score ?? 0)),
            'dependent_status_score' => min(2,  (int)($request->dependent_status_score ?? 0)),
            'illness_score'          => min(5,  (int)($request->illness_score ?? 0)),
            'special_cases_score'    => min(10, (int)($request->special_cases_score ?? 0)),
            'score_deduction'        => $deductionPayload,
            'score_deduction_reason' => $request->score_deduction_reason ?? null,
            'score_addition'         => $additionPayload,
            'score_addition_reason'  => $request->score_addition_reason ?? null,
            'total_score'            => max(0, $rawScorePayload + $additionPayload - $deductionPayload),
        ];

        $payment = [
            'iban'            => str_replace(' ', '', $request->input('iban')),
            'barcode'         => str_replace(' ', '', $request->input('barcode')),
            'recipient_name'  => $request->input('recipient_name'),
            'data_entry_name' => $request->input('payment_data_entry_name') ?: null,
        ];

        $paymentAI = [
            'iban'           => str_replace(' ', '', $request->input('iban_ai')),
            'barcode'        => str_replace(' ', '', $request->input('barcode_ai')),
            'recipient_name' => $request->input('recipient_name_ai'),
        ];

        if ($request->hasFile('iban_image')) {
            $id = $member?->id ?? 'pending';
            $payment['iban_image'] = $request->file('iban_image')->store("payment/iban_{$id}", 'public');
        }
        if ($request->hasFile('barcode_image')) {
            $id = $member?->id ?? 'pending';
            $payment['barcode_image'] = $request->file('barcode_image')->store("payment/barcode_{$id}", 'public');
        }

        return [
            'full_name'                 => $request->input('full_name'),
            'age'                       => $request->input('age'),
            'gender'                    => $request->input('gender'),
            'mother_name'               => $request->input('mother_name'),
            'national_id'               => $request->input('national_id'),
            'verification_status_id'    => $request->input('verification_status_id'),
            'final_status_id'           => $this->isAdmin() ? ($request->input('final_status_id') ?: null) : ($member?->final_status_id ?? null),
            'dossier_number'            => $request->input('dossier_number'),
            'current_address'           => $request->input('current_address'),
            'region_id'                 => $request->input('region_id') ?: null,
            'sector_id'                 => $request->input('sector_id') ?: null,
            'marital_status'            => $request->input('marital_status'),
            'disease_type'              => $request->input('disease_type'),
            'phone'                     => $request->input('phone'),
            'phone2'                    => $request->input('phone2'),
            'network'                   => $request->input('network'),
            'provider_status'           => $request->input('provider_status'),
            'job'                       => $request->input('job'),
            'housing_status_id'         => $request->input('housing_status_id') ?: null,
            'dependents_count'          => $request->input('dependents_count'),
            'payments_count'            => $request->input('payments_count') !== '' ? $request->input('payments_count') : null,
            'notes'                     => $request->input('notes') ?: null,
            'illness_details'           => $request->input('illness_details'),
            'special_cases'             => $request->boolean('special_cases'),
            'special_cases_description' => $request->input('special_cases_description'),
            'sham_cash_account'         => $this->isAdmin()
                                              ? (in_array($request->input('sham_cash_account'), ['done','manual']) ? $request->input('sham_cash_account') : null)
                                              : ($member?->sham_cash_account ?? null),
            'other_association'         => !empty($request->association_ids),
            'representative_id'         => $request->input('representative_id'),
            'data_entry_name'           => $request->input('data_entry_name'),
            'delegate'                  => $request->input('delegate'),
            'second_person'             => $request->input('second_person'),
            'association_id'            => $request->input('association_id'),
            'association_ids'           => $request->input('association_ids', []),
            'latitude'                  => $request->input('latitude') ?: null,
            'longitude'                 => $request->input('longitude') ?: null,
            'scores'                    => $scores,
            'payment'                   => $payment,
            'payment_ai'                => $paymentAI,
        ];
    }

    private function getMemberOriginal(Member $member): array
    {
        return array_merge(
            $member->only([
                'full_name', 'age', 'gender', 'mother_name', 'national_id',
                'verification_status_id', 'final_status_id', 'dossier_number', 'current_address', 'region_id', 'sector_id',
                'marital_status', 'disease_type', 'phone', 'phone2', 'network', 'provider_status',
                'job', 'second_person', 'housing_status_id', 'dependents_count', 'payments_count', 'notes', 'illness_details',
                'special_cases', 'special_cases_description', 'sham_cash_account',
                'other_association', 'representative_id', 'data_entry_name', 'delegate', 'association_id',
                'score', 'estimated_amount', 'latitude', 'longitude',
            ]),
            [
                'scores' => $member->scores?->only([
                    'work_score', 'housing_score', 'dependents_score',
                    'dependent_status_score', 'illness_score', 'special_cases_score', 'total_score',
                    'score_deduction', 'score_deduction_reason',
                    'score_addition', 'score_addition_reason',
                ]) ?? [],
                'payment' => $member->paymentInfo?->only([
                    'iban', 'barcode', 'iban_image', 'barcode_image', 'recipient_name', 'data_entry_name',
                ]) ?? [],
                'payment_ai' => $member->paymentInfoAI?->only(['iban', 'barcode', 'recipient_name']) ?? [],
            ]
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'                  => 'required|string|max:255',
            'age'                        => 'nullable|integer|min:0|max:150',
            'gender'                     => 'nullable|string|max:10',
            'mother_name'                => 'nullable|string|max:255',
            'national_id'                => 'required|string|unique:members,national_id',
            'verification_status_id'     => 'required|exists:verification_statuses,id',
            'final_status_id'            => 'nullable|exists:final_statuses,id',
            'dossier_number'             => 'nullable|string|max:50|unique:members,dossier_number',
            'current_address'            => 'nullable|string',
            'marital_status'             => 'nullable|string|max:100',
            'disease_type'               => 'nullable|string|max:255',
            'phone'                      => 'nullable|string|max:50',
            'phone2'                     => 'nullable|string|max:50',
            'network'                    => 'nullable|in:MTN,SYRIATEL',
            'provider_status'            => 'nullable|string|max:100',
            'job'                        => 'nullable|string|max:150',
            'housing_status_id'          => 'nullable|exists:housing_statuses,id',
            'dependents_count'           => 'nullable|integer|min:0',
            'payments_count'             => 'nullable|integer|min:0',
            'notes'                      => 'nullable|string',
            'illness_details'            => 'nullable|string',
            'special_cases_description'  => 'nullable|string',
            'representative_id'          => 'nullable|exists:users,id',
            'data_entry_name'            => 'nullable|string|max:255',
            'delegate'                   => 'nullable|string|max:255',
            'second_person'              => 'nullable|string|max:255',
            'association_id'             => 'nullable|exists:associations,id',
            // scores
            'work_score'                 => 'nullable|integer|min:0|max:2',
            'housing_score'              => 'nullable|integer|min:0|max:4',
            'dependents_score'           => 'nullable|integer|min:0|max:20',
            'dependent_status_score'     => 'nullable|integer|min:0|max:2',
            'illness_score'              => 'nullable|integer|min:0|max:5',
            'special_cases_score'        => 'nullable|integer|min:0|max:10',
            'score_deduction'            => 'nullable|integer|min:0',
            'score_deduction_reason'     => 'nullable|string|max:500',
            'score_addition'             => 'nullable|integer|min:0',
            'score_addition_reason'      => 'nullable|string|max:500',
            // payment
            'iban'                       => 'nullable|string|size:16',
            'barcode'                    => 'nullable|string|max:100',
            'iban_image'                 => 'nullable|image|max:2048',
            'barcode_image'              => 'nullable|image|max:2048',
            'recipient_name'             => 'nullable|string|max:150',
            // payment AI
            'iban_ai'                    => 'nullable|string|size:16',
            'barcode_ai'                 => 'nullable|string|max:100',
            'recipient_name_ai'          => 'nullable|string|max:150',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => $this->buildMemberPayload($request),
                'original'     => null,
                'requested_by' => Auth::id(),
            ]);
            ActivityLogger::log('created', 'طلب إضافة مستفيد جديد بانتظار موافقة المسؤول: ' . $request->input('full_name'));
            return redirect()->route('members.index')
                             ->with('pending', 'تم إرسال طلب إضافة المستفيد — بانتظار موافقة المسؤول.');
        }

        $workScore              = min(2,  (int)($request->work_score ?? 0));
        $housingScore           = min(4,  (int)($request->housing_score ?? 0));
        $dependentsScore        = min(20, (int)($request->dependents_score ?? 0));
        $dependentStatusScore   = min(2,  (int)($request->dependent_status_score ?? 0));
        $illnessScore           = min(5,  (int)($request->illness_score ?? 0));
        $specialScore           = min(10, (int)($request->special_cases_score ?? 0));
        $scoreDeduction         = max(0,  (int)($request->score_deduction ?? 0));
        $scoreDeductionReason   = $request->score_deduction_reason ?? null;
        $scoreAddition          = max(0,  (int)($request->score_addition ?? 0));
        $scoreAdditionReason    = $request->score_addition_reason ?? null;
        // store region_id / sector_id in data array for use below
        $data['region_id']  = $request->input('region_id') ?: null;
        $data['sector_id']  = $request->input('sector_id') ?: null;
        $rawScore               = $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore;
        $totalScore             = max(0, $rawScore + $scoreAddition - $scoreDeduction);

        $member = Member::create([
            'full_name'                  => $data['full_name'],
            'age'                        => $data['age'] ?? null,
            'gender'                     => $data['gender'] ?? null,
            'mother_name'                => $data['mother_name'] ?? null,
            'national_id'                => $data['national_id'],
            'verification_status_id'     => $data['verification_status_id'],
            'final_status_id'            => $data['final_status_id'] ?? null,
            'dossier_number'             => $data['dossier_number'] ?? null,
            'current_address'            => $data['current_address'] ?? null,
            'region_id'                  => $data['region_id'] ?? null,
            'sector_id'                  => $data['sector_id'] ?? null,
            'marital_status'             => $data['marital_status'] ?? null,
            'disease_type'               => $data['disease_type'] ?? null,
            'other_association'          => !empty($request->association_ids),
            'phone'                      => $data['phone'] ?? null,
            'phone2'                     => $data['phone2'] ?? null,
            'representative_id'          => $data['representative_id'] ?? Auth::id(),
            'data_entry_name'            => $data['data_entry_name'] ?? null,
            'delegate'                   => $data['delegate'] ?? null,
            'second_person'              => $data['second_person'] ?? null,
            'association_id'             => $data['association_id'] ?? null,
            'network'                    => $data['network'] ?? null,
            'provider_status'            => $data['provider_status'] ?? null,
            'job'                        => $data['job'] ?? null,
            'housing_status_id'          => $data['housing_status_id'] ?? null,
            'dependents_count'           => $data['dependents_count'] ?? null,
            'payments_count'             => $data['payments_count'] ?? null,
            'notes'                      => $data['notes'] ?? null,
            'illness_details'            => $data['illness_details'] ?? null,
            'special_cases'              => $request->boolean('special_cases'),
            'special_cases_description'  => $data['special_cases_description'] ?? null,
            'sham_cash_account'          => $this->isAdmin() && in_array($request->input('sham_cash_account'), ['done','manual']) ? $request->input('sham_cash_account') : null,
            'score'                      => $totalScore,
            'estimated_amount'           => $totalScore * 500,
        ]);

        MemberScore::create([
            'member_id'              => $member->id,
            'work_score'             => $workScore,
            'housing_score'          => $housingScore,
            'dependents_score'       => $dependentsScore,
            'dependent_status_score' => $dependentStatusScore,
            'illness_score'          => $illnessScore,
            'special_cases_score'    => $specialScore,
            'total_score'            => $totalScore,
            'score_deduction'        => $scoreDeduction,
            'score_deduction_reason' => $scoreDeductionReason,
            'score_addition'         => $scoreAddition,
            'score_addition_reason'  => $scoreAdditionReason,
        ]);

        $ibanImagePath    = null;
        $barcodeImagePath = null;

        if ($request->hasFile('iban_image')) {
            $ibanImagePath = $request->file('iban_image')->store("payment/iban_{$member->id}", 'public');
        }
        if ($request->hasFile('barcode_image')) {
            $barcodeImagePath = $request->file('barcode_image')->store("payment/barcode_{$member->id}", 'public');
        }

        PaymentInfo::create([
            'member_id'       => $member->id,
            'iban'            => str_replace(' ', '', $request->input('iban')),
            'barcode'         => str_replace(' ', '', $request->input('barcode')),
            'iban_image'      => $ibanImagePath,
            'barcode_image'   => $barcodeImagePath,
            'recipient_name'  => $request->input('recipient_name'),
            'data_entry_name' => $request->input('payment_data_entry_name') ?: null,
        ]);

        PaymentInfoAI::create([
            'member_id'      => $member->id,
            'iban'           => str_replace(' ', '', $request->input('iban_ai')),
            'barcode'        => str_replace(' ', '', $request->input('barcode_ai')),
            'recipient_name' => $request->input('recipient_name_ai'),
        ]);

        $member->associations()->sync($request->input('association_ids', []));

        ActivityLogger::log('created', "إضافة مستفيد جديد: {$member->full_name}", $member);

        return redirect()->route('members.index')->with('success', 'تم إضافة المستفيد بنجاح.');
    }

    public function edit(Member $member)
    {
        $member->load(['scores', 'paymentInfo', 'paymentInfoAI', 'association', 'associations', 'fieldVisits.status', 'fieldVisits.houseType', 'fieldVisits.houseCondition']);
        $visitAmount        = $member->fieldVisits->first()?->estimated_amount ?? 0;
        $fieldVisitStatuses = \App\Models\FieldVisitStatus::active()->orderBy('id')->get();
        $houseTypes         = \App\Models\HouseType::active()->orderBy('id')->get();
        $houseConditions    = \App\Models\HouseCondition::active()->orderBy('name')->get();
        ActivityLogger::log('viewed', "فتح نموذج تعديل المستفيد: {$member->full_name}", $member);
        return view('members.edit', array_merge($this->formData(), compact('member', 'visitAmount', 'fieldVisitStatuses', 'houseTypes', 'houseConditions')));
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'full_name'                  => 'required|string|max:255',
            'age'                        => 'nullable|integer|min:0|max:150',
            'gender'                     => 'nullable|string|max:10',
            'mother_name'                => 'nullable|string|max:255',
            'national_id'                => 'required|string|unique:members,national_id,' . $member->id,
            'verification_status_id'     => 'required|exists:verification_statuses,id',
            'final_status_id'            => 'nullable|exists:final_statuses,id',
            'dossier_number'             => 'nullable|string|max:50|unique:members,dossier_number,' . $member->id,
            'current_address'            => 'nullable|string',
            'region_id'                  => 'nullable|exists:regions,id',
            'sector_id'                  => 'nullable|exists:sectors,id',
            'marital_status'             => 'nullable|string|max:100',
            'disease_type'               => 'nullable|string|max:255',
            'phone'                      => 'nullable|string|max:50',
            'phone2'                     => 'nullable|string|max:50',
            'network'                    => 'nullable|in:MTN,SYRIATEL',
            'provider_status'            => 'nullable|string|max:100',
            'job'                        => 'nullable|string|max:150',
            'housing_status_id'          => 'nullable|exists:housing_statuses,id',
            'dependents_count'           => 'nullable|integer|min:0',
            'payments_count'             => 'nullable|integer|min:0',
            'notes'                      => 'nullable|string',
            'illness_details'            => 'nullable|string',
            'special_cases_description'  => 'nullable|string',
            'representative_id'          => 'nullable|exists:users,id',
            'data_entry_name'            => 'nullable|string|max:255',
            'delegate'                   => 'nullable|string|max:255',
            'second_person'              => 'nullable|string|max:255',
            'association_id'             => 'nullable|exists:associations,id',
            'work_score'                 => 'nullable|integer|min:0|max:2',
            'housing_score'              => 'nullable|integer|min:0|max:4',
            'dependents_score'           => 'nullable|integer|min:0|max:20',
            'dependent_status_score'     => 'nullable|integer|min:0|max:2',
            'illness_score'              => 'nullable|integer|min:0|max:5',
            'special_cases_score'        => 'nullable|integer|min:0|max:10',
            'score_deduction'            => 'nullable|integer|min:0',
            'score_deduction_reason'     => 'nullable|string|max:500',
            'score_addition'             => 'nullable|integer|min:0',
            'score_addition_reason'      => 'nullable|string|max:500',
            'iban'                       => 'nullable|string|size:16',
            'barcode'                    => 'nullable|string|max:100',
            'iban_image'                 => 'nullable|image|max:2048',
            'barcode_image'              => 'nullable|image|max:2048',
            'recipient_name'             => 'nullable|string|max:150',
            // payment AI
            'iban_ai'                    => 'nullable|string|size:16',
            'barcode_ai'                 => 'nullable|string|max:100',
            'recipient_name_ai'          => 'nullable|string|max:150',
            'latitude'                   => 'nullable|numeric|between:-90,90',
            'longitude'                  => 'nullable|numeric|between:-180,180',
        ]);

        if (!$this->isAdmin()) {
            $member->load(['scores', 'paymentInfo', 'paymentInfoAI']);
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => $member->id,
                'action'       => 'update',
                'payload'      => $this->buildMemberPayload($request, $member),
                'original'     => $this->getMemberOriginal($member),
                'requested_by' => Auth::id(),
            ]);
            ActivityLogger::log('updated', "طلب تعديل بيانات المستفيد بانتظار موافقة المسؤول: {$member->full_name}", $member);
            return redirect()->route('members.show', $member)
                             ->with('pending', 'تم إرسال طلب التعديل — بانتظار موافقة المسؤول.');
        }

        $workScore            = min(2,  (int)($request->work_score ?? 0));
        $housingScore         = min(4,  (int)($request->housing_score ?? 0));
        $dependentsScore      = min(20, (int)($request->dependents_score ?? 0));
        $dependentStatusScore = min(2,  (int)($request->dependent_status_score ?? 0));
        $illnessScore         = min(5,  (int)($request->illness_score ?? 0));
        $specialScore         = min(10, (int)($request->special_cases_score ?? 0));
        $scoreDeduction       = max(0,  (int)($request->score_deduction ?? 0));
        $scoreDeductionReason = $request->score_deduction_reason ?? null;
        $scoreAddition        = max(0,  (int)($request->score_addition ?? 0));
        $scoreAdditionReason  = $request->score_addition_reason ?? null;
        $rawScore             = $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore;
        $totalScore           = max(0, $rawScore + $scoreAddition - $scoreDeduction);

        $member->update([
            'full_name'                  => $data['full_name'],
            'age'                        => $data['age'] ?? null,
            'gender'                     => $data['gender'] ?? null,
            'mother_name'                => $data['mother_name'] ?? null,
            'national_id'                => $data['national_id'],
            'verification_status_id'     => $data['verification_status_id'],
            'final_status_id'            => $data['final_status_id'] ?? null,
            'dossier_number'             => $data['dossier_number'] ?? null,
            'current_address'            => $data['current_address'] ?? null,
            'region_id'                  => $data['region_id'] ?? null,
            'sector_id'                  => $data['sector_id'] ?? null,
            'marital_status'             => $data['marital_status'] ?? null,
            'disease_type'               => $data['disease_type'] ?? null,
            'other_association'          => !empty($request->association_ids),
            'phone'                      => $data['phone'] ?? null,
            'phone2'                     => $data['phone2'] ?? null,
            'representative_id'          => $data['representative_id'] ?? $member->representative_id,
            'data_entry_name'            => $data['data_entry_name'] ?? null,
            'delegate'                   => $data['delegate'] ?? null,
            'second_person'              => $data['second_person'] ?? null,
            'association_id'             => $data['association_id'] ?? null,
            'network'                    => $data['network'] ?? null,
            'provider_status'            => $data['provider_status'] ?? null,
            'job'                        => $data['job'] ?? null,
            'housing_status_id'          => $data['housing_status_id'] ?? null,
            'dependents_count'           => $data['dependents_count'] ?? null,
            'payments_count'             => $data['payments_count'] ?? null,
            'notes'                      => $data['notes'] ?? null,
            'illness_details'            => $data['illness_details'] ?? null,
            'special_cases'              => $request->boolean('special_cases'),
            'special_cases_description'  => $data['special_cases_description'] ?? null,
            'sham_cash_account'          => in_array($request->input('sham_cash_account'), ['done','manual']) ? $request->input('sham_cash_account') : null, // admin-only path
            'score'                      => $totalScore,
            'estimated_amount'           => $totalScore * 500,
            'latitude'                   => ($data['latitude']  ?? null) ?: null,
            'longitude'                  => ($data['longitude'] ?? null) ?: null,
        ]);

        $scores = $member->scores ?? new MemberScore(['member_id' => $member->id]);
        $scores->fill([
            'member_id'              => $member->id,
            'work_score'             => $workScore,
            'housing_score'          => $housingScore,
            'dependents_score'       => $dependentsScore,
            'dependent_status_score' => $dependentStatusScore,
            'illness_score'          => $illnessScore,
            'special_cases_score'    => $specialScore,
            'total_score'            => $totalScore,
            'score_deduction'        => $scoreDeduction,
            'score_deduction_reason' => $scoreDeductionReason,
            'score_addition'         => $scoreAddition,
            'score_addition_reason'  => $scoreAdditionReason,
        ])->save();

        $payment = $member->paymentInfo ?? new PaymentInfo(['member_id' => $member->id]);

        if ($request->hasFile('iban_image')) {
            $payment->iban_image = $request->file('iban_image')->store("payment/iban_{$member->id}", 'public');
        }
        if ($request->hasFile('barcode_image')) {
            $payment->barcode_image = $request->file('barcode_image')->store("payment/barcode_{$member->id}", 'public');
        }

        $payment->fill([
            'member_id'       => $member->id,
            'iban'            => str_replace(' ', '', $request->input('iban')),
            'barcode'         => str_replace(' ', '', $request->input('barcode')),
            'recipient_name'  => $request->input('recipient_name'),
            'data_entry_name' => $request->input('payment_data_entry_name') ?: null,
        ])->save();

        $paymentAI = $member->paymentInfoAI ?? new PaymentInfoAI(['member_id' => $member->id]);
        $paymentAI->fill([
            'member_id'      => $member->id,
            'iban'           => str_replace(' ', '', $request->input('iban_ai')),
            'barcode'        => str_replace(' ', '', $request->input('barcode_ai')),
            'recipient_name' => $request->input('recipient_name_ai'),
        ])->save();

        $member->associations()->sync($request->input('association_ids', []));

        ActivityLogger::log('updated', "تعديل بيانات المستفيد: {$member->full_name}", $member);

        return redirect()->route('members.edit', $member)->with('success', 'تم تحديث بيانات المستفيد بنجاح.');
    }

    public function destroy(Member $member)
    {
        if (!$this->isAdmin()) {
            $member->load(['scores', 'paymentInfo', 'paymentInfoAI']);
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => $member->id,
                'action'       => 'delete',
                'payload'      => null,
                'original'     => $this->getMemberOriginal($member),
                'requested_by' => Auth::id(),
            ]);
            ActivityLogger::log('deleted', "طلب حذف المستفيد بانتظار موافقة المسؤول: {$member->full_name}", $member);
            return redirect()->route('members.show', $member)
                             ->with('pending', 'تم إرسال طلب الحذف — بانتظار موافقة المسؤول.');
        }

        $name = $member->full_name;
        \App\Models\DeletedMember::archive($member, Auth::id());
        $member->delete();
        ActivityLogger::log('deleted', "حذف المستفيد: {$name}");
        return redirect()->route('members.index')->with('success', 'تم حذف المستفيد بنجاح.');
    }

    public function bulkDestroy(Request $request)
    {
        if ($request->boolean('select_all')) {
            $ids = $this->buildFilteredQuery($request)->pluck('id')->toArray();
        } else {
            $ids = array_filter((array) $request->input('ids', []));
        }

        if (empty($ids)) {
            return redirect()->route('members.index')->with('success', 'لم يتم تحديد أي عضو.');
        }

        if ($this->isAdmin()) {
            $members = Member::whereIn('id', $ids)->get();
            foreach ($members as $member) {
                ActivityLogger::log('deleted', "حذف المستفيد: {$member->full_name}");
                \App\Models\DeletedMember::archive($member, Auth::id());
                $member->delete();
            }
            return redirect()->route('members.index')->with('success', "تم حذف {$members->count()} عضو بنجاح.");
        }

        PendingChange::createWithSnapshots([
            'model_type'   => 'member',
            'model_id'     => null,
            'action'       => 'bulk_delete',
            'payload'      => ['member_ids' => $ids, 'count' => count($ids)],
            'original'     => null,
            'requested_by' => Auth::id(),
            'status'       => 'pending',
        ], $ids);

        ActivityLogger::log('requested', 'طلب حذف جماعي لـ ' . count($ids) . ' مستفيد بانتظار موافقة المسؤول');
        return redirect()->route('members.index')->with('pending', 'تم إرسال طلب الحذف الجماعي — بانتظار موافقة المسؤول.');
    }

    public function bulkUpdate(Request $request)
    {
        $fields = array_filter((array) $request->input('apply_fields', []));

        if ($request->boolean('select_all')) {
            $ids = $this->buildFilteredQuery($request)->pluck('id')->toArray();
        } else {
            $ids = array_filter((array) $request->input('ids', []));
        }

        if (empty($ids)) {
            return redirect()->route('members.index')->with('success', 'لم يتم تحديد أي عضو.');
        }

        if (empty($fields)) {
            return redirect()->route('members.index')->with('success', 'لم يتم تحديد أي حقل للتعديل.');
        }

        $allowed = ['network', 'marital_status', 'current_address', 'region_id', 'sector_id', 'housing_status_id', 'verification_status_id', 'estimated_amount', 'payments_count', 'field_visit_status_id', 'fv_visitor', 'payment_data_entry_name', 'delegate'];
        if ($this->isAdmin()) { $allowed[] = 'final_status_id'; $allowed[] = 'sham_cash_account'; }
        $data    = [];

        foreach ($fields as $field) {
            if (!in_array($field, $allowed)) continue;
            $value = $request->input("fields.{$field}");
            if ($field === 'sham_cash_account') {
                $data[$field] = in_array($value, ['done', 'manual']) ? $value : null;
            } elseif (in_array($field, ['verification_status_id', 'final_status_id', 'field_visit_status_id', 'housing_status_id'])) {
                $data[$field] = $value ?: null;
            } elseif ($field === 'payments_count') {
                $data[$field] = $value !== '' && $value !== null ? (int) $value : null;
            } elseif ($field === 'fv_visitor') {
                $data[$field] = $value ?: null;
            } elseif ($field === 'payment_data_entry_name') {
                $data[$field] = $value ?: null;
            } elseif ($field === 'estimated_amount') {
                $data[$field] = $value !== '' && $value !== null ? (float) $value : null;
            } else {
                $data[$field] = $value ?: null;
            }
        }

        if (empty($data)) {
            return redirect()->route('members.index')->with('success', 'لم يتم تحديد أي حقل للتعديل.');
        }

        if ($this->isAdmin()) {
            // Handle field_visit_status_id separately (stored in field_visits table)
            if (array_key_exists('field_visit_status_id', $data)) {
                $fvsId = $data['field_visit_status_id'];
                unset($data['field_visit_status_id']);
                foreach ($ids as $memberId) {
                    $visit = FieldVisit::where('member_id', $memberId)->latest()->first();
                    if ($visit) {
                        $visit->update(['field_visit_status_id' => $fvsId]);
                    } else {
                        FieldVisit::create(['member_id' => $memberId, 'field_visit_status_id' => $fvsId]);
                    }
                }
            }
            if (array_key_exists('fv_visitor', $data)) {
                $visitor = $data['fv_visitor'];
                unset($data['fv_visitor']);
                foreach ($ids as $memberId) {
                    $visit = FieldVisit::where('member_id', $memberId)->latest()->first();
                    if ($visit) {
                        $visit->update(['visitor' => $visitor]);
                    }
                }
            }
            if (array_key_exists('payment_data_entry_name', $data)) {
                $payDeName = $data['payment_data_entry_name'];
                unset($data['payment_data_entry_name']);
                foreach ($ids as $memberId) {
                    \App\Models\PaymentInfo::where('member_id', $memberId)
                        ->update(['data_entry_name' => $payDeName]);
                }
            }
            if (!empty($data)) {
                Member::whereIn('id', $ids)->update($data);
            }
            ActivityLogger::log('updated', "تعديل جماعي على " . count($ids) . " مستفيد: " . implode(', ', array_keys(array_merge($data, array_key_exists('field_visit_status_id', $data ?? []) ? ['field_visit_status_id' => ''] : []))));
            return redirect()->route('members.index')->with('success', "تم تعديل " . count($ids) . " عضو بنجاح.");
        }

        PendingChange::createWithSnapshots([
            'model_type'   => 'member',
            'model_id'     => null,
            'action'       => 'bulk_update',
            'payload'      => ['member_ids' => $ids, 'count' => count($ids), 'fields' => $data],
            'original'     => null,
            'requested_by' => Auth::id(),
            'status'       => 'pending',
        ], $ids);

        ActivityLogger::log('requested', 'طلب تعديل جماعي لـ ' . count($ids) . ' مستفيد بانتظار موافقة المسؤول');
        return redirect()->route('members.index')->with('pending', 'تم إرسال طلب التعديل الجماعي — بانتظار موافقة المسؤول.');
    }

    public function updateFinalStatus(Request $request, Member $member)
    {
        $request->validate([
            'final_status_id' => 'nullable|exists:final_statuses,id',
        ]);

        if (!$this->isAdmin()) {
            abort(403, 'تعديل الحالة النهائية مخصص للمسؤولين فقط.');
        }

        $newStatusId = $request->input('final_status_id') ?: null;

        $oldStatus = $member->finalStatus?->name ?? 'بدون';
        $member->update(['final_status_id' => $newStatusId]);
        $newStatus = $member->fresh()->finalStatus?->name ?? 'بدون';
        ActivityLogger::log('updated', "تغيير الحالة النهائية لـ {$member->full_name}: {$oldStatus} → {$newStatus}", $member);

        return back()->with('success', "تم تحديث الحالة النهائية لـ {$member->full_name}.");
    }

    public function scoreEqualizerIndex(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        $allIds = (clone $query)->pluck('members.id')->toArray();

        $members = $query
            ->with(['verificationStatus', 'scores'])
            ->orderByRaw('CAST(dossier_number AS UNSIGNED)')
            ->paginate(50)
            ->withQueryString();

        $verificationStatuses = VerificationStatus::active()->orderBy('name')->get();
        $finalStatusList      = FinalStatus::active()->orderBy('name')->get();
        $maritalStatusList    = MaritalStatus::active()->orderBy('id')->get();
        $associationList      = Association::active()->orderBy('name')->get();
        $delegateList         = Delegate::orderBy('name')->pluck('name');
        $addressList          = Member::whereNotNull('current_address')->where('current_address', '!=', '')->distinct()->orderBy('current_address')->pluck('current_address');
        $regionList           = \App\Models\Region::active()->orderBy('name')->get();
        $housingStatusList    = \App\Models\HousingStatus::active()->orderBy('name')->get();

        return view('members.score-equalizer', compact(
            'members', 'allIds',
            'verificationStatuses', 'finalStatusList', 'maritalStatusList',
            'associationList', 'delegateList', 'addressList',
            'regionList', 'housingStatusList'
        ));
    }

    public function scoreEqualizerApply(Request $request)
    {
        $request->validate([
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'integer|exists:members,id',
            'target_score' => 'required|integer|min:0',
            'reason'       => 'nullable|string|max:1000',
        ]);

        $ids    = $request->input('member_ids');
        $target = (int) $request->input('target_score');
        $reason = $request->input('reason') ?: null;
        $count  = count($ids);
        $label  = "تسوية نقاط {$count} عضو إلى {$target} نقطة";
        if ($reason) $label .= " — السبب: {$reason}";

        if (!$this->isAdmin()) {
            PendingChange::createWithSnapshots([
                'model_type'   => 'member',
                'model_id'     => null,
                'action'       => 'bulk_score_equalize',
                'payload'      => ['member_ids' => $ids, 'target_score' => $target, 'reason' => $reason, 'count' => $count, 'label' => $label],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ], $ids);
            ActivityLogger::log('requested', "طلب {$label} — بانتظار موافقة المسؤول");
            return back()->with('pending', "تم إرسال طلب {$label} — بانتظار موافقة المسؤول.");
        }

        $this->applyScoreEqualizationToIds($ids, $target, $reason);
        ActivityLogger::log('updated', "تسوية نقاط: {$label}");
        return back()->with('success', "تم تطبيق {$label} بنجاح.");
    }

    private function applyScoreEqualizationToIds(array $ids, int $target, ?string $reason): void
    {
        $clamped = max(0, $target);
        foreach ($ids as $memberId) {
            $member = Member::find($memberId);
            if (!$member) continue;

            $scores = $member->scores ?? new MemberScore(['member_id' => $memberId]);

            $rawScore = ($scores->work_score            ?? 0)
                      + ($scores->housing_score          ?? 0)
                      + ($scores->dependents_score       ?? 0)
                      + ($scores->dependent_status_score ?? 0)
                      + ($scores->illness_score          ?? 0)
                      + ($scores->special_cases_score    ?? 0);

            if ($clamped >= $rawScore) {
                $addition  = $clamped - $rawScore;
                $deduction = 0;
            } else {
                $addition  = 0;
                $deduction = $rawScore - $clamped;
            }

            $scores->fill([
                'member_id'              => $memberId,
                'score_addition'         => $addition,
                'score_addition_reason'  => $addition  > 0 ? $reason : null,
                'score_deduction'        => $deduction,
                'score_deduction_reason' => $deduction > 0 ? $reason : null,
                'total_score'            => $clamped,
            ])->save();

            $member->update([
                'score'            => $clamped,
                'estimated_amount' => $clamped * 500,
            ]);
        }
    }

    public function bulkSetScoreAdjustment(Request $request)
    {
        $request->validate([
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'integer|exists:members,id',
            'mode'         => 'required|in:deduction,addition',
            'amount'       => 'required|integer|min:0',
            'reason'       => 'nullable|string|max:1000',
        ]);

        $ids    = $request->input('member_ids');
        $mode   = $request->input('mode');
        $amount = (int) $request->input('amount');
        $reason = $request->input('reason') ?: null;
        $count  = count($ids);

        $modeLabel = $mode === 'addition' ? 'إضافة' : 'انقاص';
        $label     = "تعيين {$modeLabel} {$amount} نقطة لـ {$count} عضو";
        if ($reason) $label .= " — السبب: {$reason}";

        if (!$this->isAdmin()) {
            $action  = $mode === 'addition' ? 'bulk_score_addition' : 'bulk_score_deduction';
            $payload = $mode === 'addition'
                ? ['score_addition'   => $amount, 'score_addition_reason'  => $reason, 'member_ids' => $ids, 'count' => $count, 'label' => $label]
                : ['score_deduction'  => $amount, 'score_deduction_reason' => $reason, 'member_ids' => $ids, 'count' => $count, 'label' => $label];

            PendingChange::createWithSnapshots([
                'model_type'   => 'member',
                'model_id'     => null,
                'action'       => $action,
                'payload'      => $payload,
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ], $ids);

            ActivityLogger::log('requested', "طلب {$label} — بانتظار موافقة المسؤول");
            return back()->with('pending', "تم إرسال طلب {$label} — بانتظار موافقة المسؤول.");
        }

        if ($mode === 'addition') {
            $this->applyBulkScoreAdditionToIds($ids, $amount, $reason);
        } else {
            $this->applyBulkScoreDeductionToIds($ids, $amount, $reason);
        }

        ActivityLogger::log('updated', "تعيين جماعي للنقاط: {$label}");
        $tab = $mode === 'addition' ? 'additions' : 'deductions';
        return redirect()->route('members.score-adjustments', ['tab' => $tab])
                         ->with('success', "تم تعيين {$label} بنجاح.");
    }

    public function scoreDeductionsIndex(Request $request)
    {
        return redirect()->route('members.score-adjustments', array_merge($request->query(), ['tab' => 'deductions']));
    }

    public function scoreAdditionsIndex(Request $request)
    {
        return redirect()->route('members.score-adjustments', array_merge($request->query(), ['tab' => 'additions']));
    }

    public function scoreAdjustmentsIndex(Request $request)
    {
        $tab          = $request->get('tab', 'deductions');
        $search       = trim($request->get('search', ''));
        $reasonFilter = trim($request->get('reason', ''));
        $dateFrom     = $request->get('date_from', '');
        $dateTo       = $request->get('date_to', '');
        $sortBy       = $request->get('sort', 'dossier');

        // Deductions query
        $deductQuery = Member::query()
            ->join('member_scores', 'member_scores.member_id', '=', 'members.id')
            ->where('member_scores.score_deduction', '>', 0)
            ->with(['verificationStatus'])
            ->select('members.*', 'member_scores.score_deduction', 'member_scores.score_deduction_reason',
                     'member_scores.total_score', 'member_scores.updated_at as score_updated_at');

        // Additions query
        $addQuery = Member::query()
            ->join('member_scores', 'member_scores.member_id', '=', 'members.id')
            ->where('member_scores.score_addition', '>', 0)
            ->with(['verificationStatus'])
            ->select('members.*', 'member_scores.score_addition', 'member_scores.score_addition_reason', 'member_scores.total_score');

        // Recalculate query — members with selected component > 0, shows current vs correct total
        $validComponents = ['housing_score', 'work_score', 'dependents_score', 'dependent_status_score', 'illness_score', 'special_cases_score'];
        $recalcComponent = $request->get('component', 'housing_score');
        if (!in_array($recalcComponent, $validComponents)) {
            $recalcComponent = 'housing_score';
        }

        $correctTotalExpr = 'GREATEST(0, COALESCE(member_scores.work_score,0) + COALESCE(member_scores.housing_score,0) + COALESCE(member_scores.dependents_score,0) + COALESCE(member_scores.dependent_status_score,0) + COALESCE(member_scores.illness_score,0) + COALESCE(member_scores.special_cases_score,0) + COALESCE(member_scores.score_addition,0) - COALESCE(member_scores.score_deduction,0))';

        $recalcQuery = Member::query()
            ->join('member_scores', 'member_scores.member_id', '=', 'members.id')
            ->where("member_scores.{$recalcComponent}", '>', 0)
            ->whereRaw("COALESCE(member_scores.total_score,0) != {$correctTotalExpr}")
            ->with(['verificationStatus'])
            ->selectRaw("members.*, member_scores.housing_score, member_scores.work_score, member_scores.dependents_score, member_scores.dependent_status_score, member_scores.illness_score, member_scores.special_cases_score, member_scores.score_addition, member_scores.score_deduction, member_scores.total_score, {$correctTotalExpr} as correct_total");

        if ($search !== '') {
            $searchFn = function ($q) use ($search) {
                $q->where('members.full_name', 'like', "%{$search}%")
                  ->orWhere('members.dossier_number', 'like', "%{$search}%")
                  ->orWhere('members.national_id', 'like', "%{$search}%");
            };
            $deductQuery->where($searchFn);
            $addQuery->where($searchFn);
            $recalcQuery->where($searchFn);
        }

        if ($reasonFilter !== '') {
            if ($tab === 'additions') {
                $addQuery->where('member_scores.score_addition_reason', 'like', "%{$reasonFilter}%");
            } else {
                $deductQuery->where('member_scores.score_deduction_reason', 'like', "%{$reasonFilter}%");
            }
        }

        if ($dateFrom !== '') {
            $deductQuery->where('member_scores.updated_at', '>=', $dateFrom . ' 00:00:00');
        }
        if ($dateTo !== '') {
            $deductQuery->where('member_scores.updated_at', '<=', $dateTo . ' 23:59:59');
        }

        $deductQuery->when($sortBy === 'deduction_desc', fn($q) => $q->orderByDesc('member_scores.score_deduction'))
                    ->when($sortBy === 'deduction_asc',  fn($q) => $q->orderBy('member_scores.score_deduction'))
                    ->when($sortBy === 'date_desc',       fn($q) => $q->orderByDesc('member_scores.updated_at'))
                    ->when($sortBy === 'date_asc',        fn($q) => $q->orderBy('member_scores.updated_at'))
                    ->when($sortBy === 'name',            fn($q) => $q->orderBy('members.full_name'))
                    ->when($sortBy === 'dossier',         fn($q) => $q->orderByRaw('CAST(members.dossier_number AS UNSIGNED)'));

        $addQuery->when($sortBy === 'addition_desc', fn($q) => $q->orderByDesc('member_scores.score_addition'))
                 ->when($sortBy === 'addition_asc',  fn($q) => $q->orderBy('member_scores.score_addition'))
                 ->when($sortBy === 'name',           fn($q) => $q->orderBy('members.full_name'))
                 ->when($sortBy === 'dossier',        fn($q) => $q->orderByRaw('CAST(members.dossier_number AS UNSIGNED)'));

        $recalcQuery->when($sortBy === 'housing_desc', fn($q) => $q->orderByDesc('member_scores.housing_score'))
                    ->when($sortBy === 'housing_asc',  fn($q) => $q->orderBy('member_scores.housing_score'))
                    ->when($sortBy === 'diff_desc',    fn($q) => $q->orderByRaw("(COALESCE(member_scores.total_score,0) - ({$correctTotalExpr})) DESC"))
                    ->when($sortBy === 'name',         fn($q) => $q->orderBy('members.full_name'))
                    ->when(!in_array($sortBy, ['housing_desc', 'housing_asc', 'diff_desc', 'name']),
                                                       fn($q) => $q->orderByRaw('CAST(members.dossier_number AS UNSIGNED)'));

        $members = match ($tab) {
            'additions'   => $addQuery->paginate(50)->withQueryString(),
            'recalculate' => $recalcQuery->paginate(50)->withQueryString(),
            default       => $deductQuery->paginate(50)->withQueryString(),
        };

        $totalDeductCount = Member::join('member_scores', 'member_scores.member_id', '=', 'members.id')->where('member_scores.score_deduction', '>', 0)->count();
        $totalAddCount    = Member::join('member_scores', 'member_scores.member_id', '=', 'members.id')->where('member_scores.score_addition',  '>', 0)->count();
        $recalcCount      = Member::join('member_scores', 'member_scores.member_id', '=', 'members.id')
            ->where('member_scores.housing_score', '>', 0)
            ->whereRaw("COALESCE(member_scores.total_score,0) != {$correctTotalExpr}")
            ->count();
        $totalDeduction   = \App\Models\MemberScore::where('score_deduction', '>', 0)->sum('score_deduction');
        $totalAddition    = \App\Models\MemberScore::where('score_addition',  '>', 0)->sum('score_addition');

        $reasonList = match ($tab) {
            'additions'   => \App\Models\MemberScore::where('score_addition', '>', 0)->whereNotNull('score_addition_reason')->where('score_addition_reason', '!=', '')->distinct()->orderBy('score_addition_reason')->pluck('score_addition_reason'),
            'recalculate' => collect(),
            default       => \App\Models\MemberScore::where('score_deduction', '>', 0)->whereNotNull('score_deduction_reason')->where('score_deduction_reason', '!=', '')->distinct()->orderBy('score_deduction_reason')->pluck('score_deduction_reason'),
        };

        return view('members.score-adjustments', compact(
            'members', 'tab', 'search', 'reasonFilter', 'sortBy', 'reasonList',
            'dateFrom', 'dateTo',
            'totalDeductCount', 'totalAddCount', 'totalDeduction', 'totalAddition',
            'recalcCount', 'recalcComponent'
        ));
    }

    public function bulkRecalculateScore(Request $request)
    {
        $validComponents = ['housing_score', 'work_score', 'dependents_score', 'dependent_status_score', 'illness_score', 'special_cases_score'];
        $component       = $request->get('component', 'housing_score');
        if (!in_array($component, $validComponents)) $component = 'housing_score';

        if ($request->boolean('recalc_all')) {
            $search = trim($request->get('search', ''));
            $query  = Member::join('member_scores', 'member_scores.member_id', '=', 'members.id')
                ->where("member_scores.{$component}", '>', 0)
                ->select('members.id');
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('members.full_name', 'like', "%{$search}%")
                      ->orWhere('members.dossier_number', 'like', "%{$search}%")
                      ->orWhere('members.national_id', 'like', "%{$search}%");
                });
            }
            $ids = $query->pluck('members.id')->toArray();
        } else {
            $ids = $request->input('member_ids', []);
        }

        if (empty($ids)) {
            return back()->with('error', 'لم يتم تحديد أي عضو.');
        }

        $correctExpr = 'GREATEST(0, COALESCE(work_score,0) + COALESCE(housing_score,0) + COALESCE(dependents_score,0) + COALESCE(dependent_status_score,0) + COALESCE(illness_score,0) + COALESCE(special_cases_score,0) + COALESCE(score_addition,0) - COALESCE(score_deduction,0))';

        \DB::table('member_scores')
            ->whereIn('member_id', $ids)
            ->update(['total_score' => \DB::raw($correctExpr)]);

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        \DB::statement("
            UPDATE members
            JOIN member_scores ON member_scores.member_id = members.id
            SET members.score = member_scores.total_score,
                members.estimated_amount = member_scores.total_score * 500
            WHERE members.id IN ({$placeholders})
        ", array_values($ids));

        $count = count($ids);

        return redirect()
            ->route('members.score-adjustments', ['tab' => 'recalculate', 'component' => $component])
            ->with('success', "تمت إعادة حساب نقاط {$count} عضو بنجاح.");
    }

    private function buildScoreManagerQuery(Request $request): array
    {
        $search    = trim($request->get('search', ''));
        $sortBy    = $request->get('sort', 'dossier');
        $hasScores = $request->get('has_scores', '');

        $scoreComponents = [
            'work_score'             => 2,
            'housing_score'          => 4,
            'dependents_score'       => 20,
            'dependent_status_score' => 2,
            'illness_score'          => 5,
            'special_cases_score'    => 10,
        ];

        $scoreFilters = [];
        foreach (array_keys($scoreComponents) as $col) {
            $scoreFilters[$col] = array_values(
                array_filter((array) $request->get("sf_{$col}", []), fn($v) => $v !== '')
            );
        }

        $fvAmountSub = \App\Models\FieldVisit::selectRaw('COALESCE(SUM(estimated_amount), 0)')
            ->whereColumn('member_id', 'members.id');

        $query = Member::query()
            ->leftJoin('member_scores', 'member_scores.member_id', '=', 'members.id')
            ->select('members.*',
                'member_scores.id as score_id',
                'member_scores.work_score',
                'member_scores.housing_score',
                'member_scores.dependents_score',
                'member_scores.dependent_status_score',
                'member_scores.illness_score',
                'member_scores.special_cases_score',
                'member_scores.score_addition',
                'member_scores.score_addition_reason',
                'member_scores.score_deduction',
                'member_scores.score_deduction_reason',
                'member_scores.total_score',
                'member_scores.updated_at as score_updated_at'
            )
            ->selectSub($fvAmountSub, 'field_visit_amount');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('members.full_name', 'like', "%{$search}%")
                  ->orWhere('members.dossier_number', 'like', "%{$search}%")
                  ->orWhere('members.national_id', 'like', "%{$search}%");
            });
        }

        if ($hasScores === '1') {
            $query->whereNotNull('member_scores.id')->where('member_scores.total_score', '>', 0);
        } elseif ($hasScores === '0') {
            $query->where(fn($q) => $q->whereNull('member_scores.id')->orWhere('member_scores.total_score', 0));
        }

        foreach ($scoreFilters as $col => $vals) {
            if (empty($vals)) continue;
            $hasZero = in_array('0', $vals);
            $nonZero = array_values(array_map('intval', array_filter($vals, fn($v) => (int) $v > 0)));

            if ($hasZero && !empty($nonZero)) {
                $query->where(function ($q) use ($col, $nonZero) {
                    $q->whereIn("member_scores.{$col}", $nonZero)
                      ->orWhereNull("member_scores.{$col}")
                      ->orWhere("member_scores.{$col}", 0);
                });
            } elseif ($hasZero) {
                $query->where(function ($q) use ($col) {
                    $q->whereNull("member_scores.{$col}")
                      ->orWhere("member_scores.{$col}", 0);
                });
            } else {
                $query->whereIn("member_scores.{$col}", $nonZero);
            }
        }

        $query->when($sortBy === 'score_desc', fn($q) => $q->orderByDesc('member_scores.total_score'))
              ->when($sortBy === 'score_asc',  fn($q) => $q->orderBy('member_scores.total_score'))
              ->when($sortBy === 'fv_desc',    fn($q) => $q->orderByDesc('field_visit_amount'))
              ->when($sortBy === 'fv_asc',     fn($q) => $q->orderBy('field_visit_amount'))
              ->when($sortBy === 'name',       fn($q) => $q->orderBy('members.full_name'))
              ->when($sortBy === 'dossier',    fn($q) => $q->orderByRaw('CAST(members.dossier_number AS UNSIGNED)'));

        return compact('query', 'search', 'sortBy', 'hasScores', 'scoreFilters', 'scoreComponents');
    }

    public function scoreManagerExport(Request $request)
    {
        ['query' => $query] = $this->buildScoreManagerQuery($request);

        $validKeys = ['ws', 'hs', 'ds', 'dss', 'is', 'ss', 'add', 'fv'];
        $excluded  = [];
        foreach ($request->input('excl', []) as $key) {
            if (in_array($key, $validKeys)) {
                $excluded[$key] = true;
            }
        }

        $filename = 'نقاط-المستفيدين-' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new \App\Exports\ScoreManagerExport($query, $excluded), $filename);
    }

    public function bulkScoreUpdate(Request $request)
    {
        $request->validate([
            'ids'                    => 'required|array|min:1',
            'ids.*'                  => 'integer|exists:members,id',
            'work_score'             => 'nullable|integer|min:0|max:2',
            'housing_score'          => 'nullable|integer|min:0|max:4',
            'dependents_score'       => 'nullable|integer|min:0|max:20',
            'dependent_status_score' => 'nullable|integer|min:0|max:2',
            'illness_score'          => 'nullable|integer|min:0|max:5',
            'special_cases_score'    => 'nullable|integer|min:0|max:10',
            'score_addition'         => 'nullable|integer|min:0',
            'score_deduction'        => 'nullable|integer|min:0',
        ]);

        $scoreFields = ['work_score','housing_score','dependents_score','dependent_status_score','illness_score','special_cases_score','score_addition','score_deduction'];

        $fields = [];
        foreach ($scoreFields as $field) {
            if ($request->input($field) !== null && $request->input($field) !== '') {
                $fields[$field] = (int) $request->input($field);
            }
        }

        if (empty($fields)) {
            return back()->with('error', 'لم يتم تحديد أي حقل للتعديل.');
        }

        $members = Member::whereIn('id', $request->ids)->get();

        foreach ($members as $member) {
            $score = \App\Models\MemberScore::firstOrNew(['member_id' => $member->id]);
            foreach ($fields as $key => $value) {
                $score->$key = $value;
            }
            $total = max(0,
                ($score->work_score             ?? 0) +
                ($score->housing_score          ?? 0) +
                ($score->dependents_score       ?? 0) +
                ($score->dependent_status_score ?? 0) +
                ($score->illness_score          ?? 0) +
                ($score->special_cases_score    ?? 0) +
                ($score->score_addition         ?? 0) -
                ($score->score_deduction        ?? 0)
            );
            $score->total_score = $total;
            $score->member_id   = $member->id;
            $score->save();
            $member->update(['score' => $total, 'estimated_amount' => $total * 500]);
        }

        $count = count($members);
        ActivityLogger::log('updated', "تعديل جماعي لنقاط {$count} مستفيد", null);

        return back()->with('success', "تم تعديل نقاط {$count} مستفيد بنجاح.");
    }

    public function scoreManagerIndex(Request $request)
    {
        [
            'query'           => $query,
            'search'          => $search,
            'sortBy'          => $sortBy,
            'hasScores'       => $hasScores,
            'scoreFilters'    => $scoreFilters,
            'scoreComponents' => $scoreComponents,
        ] = $this->buildScoreManagerQuery($request);

        $allIds          = (clone $query)->pluck('members.id')->toArray();
        $members         = $query->paginate(50)->withQueryString();
        $totalScore      = \App\Models\MemberScore::sum('total_score');
        $totalWithScores = \App\Models\MemberScore::where('total_score', '>', 0)->count();

        return view('members.score-manager', compact(
            'members', 'search', 'sortBy', 'hasScores',
            'totalScore', 'totalWithScores', 'scoreFilters', 'scoreComponents', 'allIds'
        ));
    }

    public function updateMemberScore(Request $request, Member $member)
    {
        $data = $request->validate([
            'work_score'             => 'required|integer|min:0|max:2',
            'housing_score'          => 'required|integer|min:0|max:4',
            'dependents_score'       => 'required|integer|min:0|max:20',
            'dependent_status_score' => 'required|integer|min:0|max:2',
            'illness_score'          => 'required|integer|min:0|max:5',
            'special_cases_score'    => 'required|integer|min:0|max:10',
            'score_addition'         => 'required|integer|min:0',
            'score_addition_reason'  => 'nullable|string|max:1000',
            'score_deduction'        => 'required|integer|min:0',
            'score_deduction_reason' => 'nullable|string|max:1000',
        ]);

        $raw   = $data['work_score'] + $data['housing_score'] + $data['dependents_score']
               + $data['dependent_status_score'] + $data['illness_score'] + $data['special_cases_score'];
        $total = max(0, $raw + $data['score_addition'] - $data['score_deduction']);
        $data['total_score'] = $total;

        $member->scores()->updateOrCreate(
            ['member_id' => $member->id],
            $data
        );

        $member->update(['score' => $total, 'estimated_amount' => $total * 500]);

        ActivityLogger::log('updated', "تعديل نقاط المستفيد: {$member->full_name} → {$total} نقطة", $member);

        return back()->with('success', "تم تحديث نقاط {$member->full_name} بنجاح — المجموع: {$total}");
    }

    public function resetMemberScore(Member $member)
    {
        $member->scores()->updateOrCreate(
            ['member_id' => $member->id],
            [
                'work_score'             => 0,
                'housing_score'          => 0,
                'dependents_score'       => 0,
                'dependent_status_score' => 0,
                'illness_score'          => 0,
                'special_cases_score'    => 0,
                'score_addition'         => 0,
                'score_addition_reason'  => null,
                'score_deduction'        => 0,
                'score_deduction_reason' => null,
                'total_score'            => 0,
            ]
        );

        $member->update(['score' => 0, 'estimated_amount' => 0]);

        ActivityLogger::log('updated', "تصفير نقاط المستفيد: {$member->full_name}", $member);

        return back()->with('success', "تم تصفير نقاط {$member->full_name} بنجاح.");
    }
}
