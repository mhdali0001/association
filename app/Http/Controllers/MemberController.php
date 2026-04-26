<?php

namespace App\Http\Controllers;

use App\Exports\MembersExport;
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
    private function formData(): array
    {
        return [
            'verificationStatuses' => VerificationStatus::active()->orderBy('name')->get(),
            'finalStatuses'        => FinalStatus::active()->orderBy('name')->get(),
            'maritalStatuses'      => MaritalStatus::active()->orderBy('id')->get(),
            'associations'         => Association::active()->orderBy('id')->get(),
            'representatives'      => User::orderBy('name')->get(),
            'regionsList'          => \App\Models\Region::active()->orderBy('name')->get(),
            'housingStatuses'      => \App\Models\HousingStatus::active()->orderBy('name')->get(),
        ];
    }

    private function buildFilteredQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $search              = $request->get('search');
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
        $shamCash            = array_filter((array) $request->get('sham_cash', []));
        $regionIds           = array_filter((array) $request->get('region_id', []));
        $housingStatusIds    = array_filter((array) $request->get('housing_status_id', []));
        $estimatedFrom       = trim($request->get('estimated_from', ''));
        $estimatedTo         = trim($request->get('estimated_to', ''));
        $finalFrom           = trim($request->get('final_from', ''));
        $finalTo             = trim($request->get('final_to', ''));
        // Field visit filters
        $fieldVisitStatusIds = array_filter((array) $request->get('field_visit_status_id', []));
        $fvHouseTypeIds      = array_filter((array) $request->get('fv_house_type_id', []));
        $fvVisitors          = array_filter((array) $request->get('fv_visitors', []));
        $fvCreatedByIds      = array_filter((array) $request->get('fv_created_by', []));
        $fvDateFrom          = trim($request->get('fv_date_from', ''));
        $fvDateTo            = trim($request->get('fv_date_to', ''));
        $fvAmountFrom        = trim($request->get('fv_amount_from', ''));
        $fvAmountTo          = trim($request->get('fv_amount_to', ''));
        $fvHouseConditionIds = array_filter((array) $request->get('fv_house_condition_id', []));
        $fvNotes             = trim($request->get('fv_notes', ''));
        $fvHasVideo         = $request->get('fv_has_video', '');
        $fvHasSpecialCase   = $request->get('fv_has_special_case', '');
        $fvCount            = trim($request->get('fv_count', ''));

        $query = Member::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('dossier_number', 'like', "%{$search}%")
                  ->orWhere('second_person', 'like', "%{$search}%");
            });
        }
        if ($dossierFrom !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) >= ?', [(int) $dossierFrom]);
        if ($dossierTo   !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) <= ?', [(int) $dossierTo]);
        if (!empty($verificationIds))     $query->whereIn('verification_status_id', $verificationIds);
        if (!empty($finalStatusIds))      $query->whereIn('final_status_id', $finalStatusIds);
        if (!empty($maritalStatuses))     $query->whereIn('marital_status', $maritalStatuses);
        if (!empty($genders))             $query->whereIn('gender', $genders);
        if (!empty($delegates))       $query->whereIn('delegate', $delegates);
        if (!empty($secondPersons))   $query->whereIn('second_person', $secondPersons);
        if ($specialCases === '1') {
            $query->where('special_cases', true);
        } elseif ($specialCases === '0') {
            $query->where(function ($q) {
                $q->where('special_cases', false)->orWhereNull('special_cases');
            });
        }
        if (!empty($specialDescriptions)) $query->whereIn('special_cases_description', $specialDescriptions);
        if (!empty($addresses))           $query->whereIn('current_address', $addresses);
        if (!empty($associationIds))      $query->whereIn('association_id', $associationIds);
        if (!empty($networks))            $query->whereIn('network', $networks);
        if (!empty($shamCash)) {
            $query->where(function ($q) use ($shamCash) {
                if (in_array('done',   $shamCash)) $q->orWhere('sham_cash_account', 'done');
                if (in_array('manual', $shamCash)) $q->orWhere('sham_cash_account', 'manual');
                if (in_array('none',   $shamCash)) $q->orWhereNull('sham_cash_account');
            });
        }
        if (!empty($fieldVisitStatusIds) || !empty($fvHouseTypeIds) || !empty($fvHouseConditionIds) || !empty($fvVisitors)
            || !empty($fvCreatedByIds)
            || $fvDateFrom !== '' || $fvDateTo !== ''
            || $fvAmountFrom !== '' || $fvAmountTo !== ''
            || $fvNotes !== '' || $fvHasVideo !== '' || $fvHasSpecialCase !== '') {
            $query->whereHas('fieldVisits', function ($q) use (
                $fieldVisitStatusIds, $fvHouseTypeIds, $fvHouseConditionIds, $fvVisitors, $fvCreatedByIds,
                $fvDateFrom, $fvDateTo, $fvAmountFrom, $fvAmountTo, $fvNotes,
                $fvHasVideo, $fvHasSpecialCase
            ) {
                if (!empty($fieldVisitStatusIds))  $q->whereIn('field_visit_status_id', $fieldVisitStatusIds);
                if (!empty($fvHouseTypeIds))        $q->whereIn('house_type_id', $fvHouseTypeIds);
                if (!empty($fvHouseConditionIds))   $q->whereIn('house_condition_id', $fvHouseConditionIds);
                if (!empty($fvVisitors))            $q->whereIn('visitor', $fvVisitors);
                if (!empty($fvCreatedByIds))        $q->whereIn('created_by', $fvCreatedByIds);
                if ($fvDateFrom !== '')             $q->where('visit_date', '>=', $fvDateFrom);
                if ($fvDateTo !== '')               $q->where('visit_date', '<=', $fvDateTo);
                if ($fvAmountFrom !== '')           $q->where('estimated_amount', '>=', (float) $fvAmountFrom);
                if ($fvAmountTo !== '')             $q->where('estimated_amount', '<=', (float) $fvAmountTo);
                if ($fvNotes !== '')               $q->where('notes', 'like', "%{$fvNotes}%");
                if ($fvHasVideo === '1')           $q->where('has_video', true);
                elseif ($fvHasVideo === '0')       $q->where(fn($s) => $s->where('has_video', false)->orWhereNull('has_video'));
                if ($fvHasSpecialCase === '1')     $q->where('has_special_case', true);
                elseif ($fvHasSpecialCase === '0') $q->where(fn($s) => $s->where('has_special_case', false)->orWhereNull('has_special_case'));
            });
        }
        if ($fvCount !== '') {
            if ($fvCount === '0') {
                $query->doesntHave('fieldVisits');
            } else {
                $query->has('fieldVisits', '>=', (int) $fvCount);
            }
        }
        if (!empty($regionIds))        $query->whereIn('region_id', $regionIds);
        if (!empty($housingStatusIds)) $query->whereIn('housing_status_id', $housingStatusIds);
        if ($estimatedFrom !== '') $query->where('estimated_amount', '>=', (float) str_replace(',', '', $estimatedFrom));
        if ($estimatedTo   !== '') $query->where('estimated_amount', '<=', (float) str_replace(',', '', $estimatedTo));
        if ($finalFrom     !== '') $query->where('final_amount', '>=', (float) str_replace(',', '', $finalFrom));
        if ($finalTo       !== '') $query->where('final_amount', '<=', (float) str_replace(',', '', $finalTo));

        return $query;
    }

    public function index(Request $request)
    {
        $search              = $request->get('search');
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

        $totals = (clone $filteredQuery)
            ->selectRaw('SUM(COALESCE(estimated_amount, 0)) as total_estimated, SUM(COALESCE(final_amount, estimated_amount, 0)) as total_final')
            ->first();

        $totalAmount      = $totals->total_estimated ?? 0;
        $totalFinalAmount = $totals->total_final      ?? 0;

        $query   = $filteredQuery->with(['verificationStatus', 'representative', 'paymentInfo', 'fieldVisits.status', 'region', 'housingStatus']);
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
        $finalFrom            = trim($request->get('final_from', ''));
        $finalTo              = trim($request->get('final_to', ''));
        $regionList           = \App\Models\Region::active()->orderBy('name')->get();
        $verificationStatuses = VerificationStatus::active()->orderBy('name')->get();
        $finalStatusList      = FinalStatus::active()->orderBy('name')->get();
        $maritalStatusList    = MaritalStatus::active()->orderBy('id')->get();
        $houseTypes           = \App\Models\HouseType::active()->orderBy('id')->get();
        $houseConditions      = \App\Models\HouseCondition::active()->orderBy('name')->get();
        $housingStatusList    = \App\Models\HousingStatus::active()->orderBy('name')->get();
        $delegateList            = Member::whereNotNull('delegate')
                                         ->where('delegate', '!=', '')
                                         ->distinct()
                                         ->orderBy('delegate')
                                         ->pluck('delegate');

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

        return view('members.index', compact(
            'members', 'search', 'dossierFrom', 'dossierTo', 'totalAmount', 'totalFinalAmount',
            'verificationIds', 'finalStatusIds', 'maritalStatuses', 'genders', 'delegates', 'secondPersons', 'specialCases', 'specialDescriptions', 'addresses', 'associationIds', 'networks', 'fieldVisitStatusIds', 'regionIds', 'housingStatusIds',
            'estimatedFrom', 'estimatedTo', 'finalFrom', 'finalTo',
            'fvHouseTypeIds', 'fvHouseConditionIds', 'fvVisitors', 'fvVisitorList', 'fvCreatedByIds', 'fvCreatedByList', 'fvDateFrom', 'fvDateTo', 'fvAmountFrom', 'fvAmountTo', 'fvNotes', 'fvHasVideo', 'fvHasSpecialCase', 'fvCount',
            'verificationStatuses', 'finalStatusList', 'maritalStatusList', 'delegateList', 'secondPersonList', 'specialDescriptionList', 'addressList', 'associationList',
            'duplicateIbans', 'fieldVisitStatuses', 'regionList', 'houseTypes', 'houseConditions', 'housingStatusList'
        ));
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
        $members = (clone $base)->with('verificationStatus')
                                ->orderBy('dossier_number')
                                ->paginate(60)->withQueryString();

        $totalCount       = (clone $base)->count();
        $withAmount       = (clone $base)->whereNotNull('estimated_amount')->where('estimated_amount', '>', 0)->count();
        $totalAmount      = (clone $base)->sum('estimated_amount');
        $withFinalAmount  = (clone $base)->whereNotNull('final_amount')->where('final_amount', '>', 0)->count();
        $totalFinalAmount = (clone $base)->sum('final_amount');

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
        $finalFrom           = trim($request->get('final_from', ''));
        $finalTo             = trim($request->get('final_to', ''));
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
        $delegateList           = Member::whereNotNull('delegate')->where('delegate', '!=', '')->distinct()->orderBy('delegate')->pluck('delegate');
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
            'housingStatusIds', 'regionIds', 'estimatedFrom', 'estimatedTo', 'finalFrom', 'finalTo', 'shamCash',
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
            'field'      => 'required|in:estimated_amount,final_amount',
            'operation'  => 'required|in:add,subtract,set',
            'amount'     => 'required|numeric|min:0',
            'apply_to'   => 'required|in:selected,filtered',
            'member_ids' => 'array',
        ]);

        $field     = $request->field;
        $amount    = (float) $request->amount;
        $operation = $request->operation;
        $applyTo   = $request->apply_to;

        $query = $applyTo === 'selected'
            ? Member::whereIn('id', $request->input('member_ids', []))
            : $this->buildBulkAmountQuery($request);

        $count = $query->count();

        if ($count === 0) {
            return back()->with('error', 'لم يتم تحديد أي أعضاء.');
        }

        $amtFmt = number_format($amount, 0);
        $operationLabels = ['add' => 'إضافة', 'subtract' => 'طرح', 'set' => 'تعيين'];
        $fieldLabels = ['estimated_amount' => 'المبلغ المقدر', 'final_amount' => 'المبلغ النهائي'];
        $label = "{$operationLabels[$operation]} {$amtFmt} ل.س على {$fieldLabels[$field]} — {$count} عضو";

        if (!$this->isAdmin()) {
            // Resolve member IDs now so the apply() can replay exactly the same set later
            $memberIds = $query->pluck('id')->all();
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => null,
                'action'       => 'bulk_amount',
                'payload'      => [
                    'field'      => $field,
                    'operation'  => $operation,
                    'amount'     => $amount,
                    'member_ids' => $memberIds,
                    'count'      => $count,
                    'label'      => $label,
                ],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return back()->with('success', "تم إرسال طلب تعديل المبالغ ({$label}) وهو بانتظار موافقة المسؤول.");
        }

        switch ($operation) {
            case 'add':
                $query->update([$field => DB::raw('COALESCE(' . $field . ', 0) + ' . $amount)]);
                break;
            case 'subtract':
                $query->update([$field => DB::raw('GREATEST(COALESCE(' . $field . ', 0) - ' . $amount . ', 0)')]);
                break;
            default: // set
                $query->update([$field => $amount]);
        }

        ActivityLogger::log('updated', "تعديل جماعي للمبالغ: {$label}");

        return back()->with('success', "تم {$label} بنجاح.");
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
        $scores = [
            'work_score'             => min(2,  (int)($request->work_score ?? 0)),
            'housing_score'          => min(4,  (int)($request->housing_score ?? 0)),
            'dependents_score'       => min(20, (int)($request->dependents_score ?? 0)),
            'dependent_status_score' => min(2,  (int)($request->dependent_status_score ?? 0)),
            'illness_score'          => min(5,  (int)($request->illness_score ?? 0)),
            'special_cases_score'    => min(10, (int)($request->special_cases_score ?? 0)),
            'score_deduction'        => $deductionPayload,
            'score_deduction_reason' => $request->score_deduction_reason ?? null,
            'total_score'            => max(0, $rawScorePayload - $deductionPayload),
        ];

        $payment = [
            'iban'           => str_replace(' ', '', $request->input('iban')),
            'barcode'        => str_replace(' ', '', $request->input('barcode')),
            'recipient_name' => $request->input('recipient_name'),
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
            'final_status_id'           => $request->input('final_status_id') ?: null,
            'dossier_number'            => $request->input('dossier_number'),
            'current_address'           => $request->input('current_address'),
            'region_id'                 => $request->input('region_id') ?: null,
            'marital_status'            => $request->input('marital_status'),
            'disease_type'              => $request->input('disease_type'),
            'phone'                     => $request->input('phone'),
            'phone2'                    => $request->input('phone2'),
            'network'                   => $request->input('network'),
            'provider_status'           => $request->input('provider_status'),
            'job'                       => $request->input('job'),
            'housing_status_id'         => $request->input('housing_status_id') ?: null,
            'dependents_count'          => $request->input('dependents_count'),
            'illness_details'           => $request->input('illness_details'),
            'special_cases'             => $request->boolean('special_cases'),
            'special_cases_description' => $request->input('special_cases_description'),
            'sham_cash_account'         => in_array($request->input('sham_cash_account'), ['done','manual']) ? $request->input('sham_cash_account') : null,
            'final_amount'              => $request->input('final_amount') ?: null,
            'other_association'         => !empty($request->association_ids),
            'representative_id'         => $request->input('representative_id'),
            'delegate'                  => $request->input('delegate'),
            'second_person'             => $request->input('second_person'),
            'association_id'            => $request->input('association_id'),
            'association_ids'           => $request->input('association_ids', []),
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
                'verification_status_id', 'final_status_id', 'dossier_number', 'current_address', 'region_id',
                'marital_status', 'disease_type', 'phone', 'phone2', 'network', 'provider_status',
                'job', 'second_person', 'housing_status_id', 'dependents_count', 'illness_details',
                'special_cases', 'special_cases_description', 'sham_cash_account',
                'other_association', 'representative_id', 'delegate', 'association_id',
                'score', 'estimated_amount', 'final_amount',
            ]),
            [
                'scores' => $member->scores?->only([
                    'work_score', 'housing_score', 'dependents_score',
                    'dependent_status_score', 'illness_score', 'special_cases_score', 'total_score',
                    'score_deduction', 'score_deduction_reason',
                ]) ?? [],
                'payment' => $member->paymentInfo?->only([
                    'iban', 'barcode', 'iban_image', 'barcode_image', 'recipient_name',
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
            'national_id'                => 'required|string|max:50|unique:members,national_id',
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
            'illness_details'            => 'nullable|string',
            'special_cases_description'  => 'nullable|string',
            'representative_id'          => 'nullable|exists:users,id',
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
            'final_amount'               => 'nullable|numeric|min:0',
            // payment
            'iban'                       => 'nullable|string|max:50',
            'barcode'                    => 'nullable|string|max:100',
            'iban_image'                 => 'nullable|image|max:2048',
            'barcode_image'              => 'nullable|image|max:2048',
            'recipient_name'             => 'nullable|string|max:150',
            // payment AI
            'iban_ai'                    => 'nullable|string|max:50',
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
        // store region_id in data array for use below
        $data['region_id'] = $request->input('region_id') ?: null;
        $rawScore               = $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore;
        $totalScore             = max(0, $rawScore - $scoreDeduction);

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
            'marital_status'             => $data['marital_status'] ?? null,
            'disease_type'               => $data['disease_type'] ?? null,
            'other_association'          => !empty($request->association_ids),
            'phone'                      => $data['phone'] ?? null,
            'representative_id'          => $data['representative_id'] ?? Auth::id(),
            'delegate'                   => $data['delegate'] ?? null,
            'second_person'              => $data['second_person'] ?? null,
            'association_id'             => $data['association_id'] ?? null,
            'network'                    => $data['network'] ?? null,
            'provider_status'            => $data['provider_status'] ?? null,
            'job'                        => $data['job'] ?? null,
            'housing_status_id'          => $data['housing_status_id'] ?? null,
            'dependents_count'           => $data['dependents_count'] ?? null,
            'illness_details'            => $data['illness_details'] ?? null,
            'special_cases'              => $request->boolean('special_cases'),
            'special_cases_description'  => $data['special_cases_description'] ?? null,
            'sham_cash_account'          => in_array($request->input('sham_cash_account'), ['done','manual']) ? $request->input('sham_cash_account') : null,
            'score'                      => $totalScore,
            'estimated_amount'           => $totalScore * 500,
            'final_amount'               => $totalScore * 500,
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
            'member_id'      => $member->id,
            'iban'           => str_replace(' ', '', $request->input('iban')),
            'barcode'        => str_replace(' ', '', $request->input('barcode')),
            'iban_image'     => $ibanImagePath,
            'barcode_image'  => $barcodeImagePath,
            'recipient_name' => $request->input('recipient_name'),
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
            'national_id'                => 'required|string|max:50|unique:members,national_id,' . $member->id,
            'verification_status_id'     => 'required|exists:verification_statuses,id',
            'final_status_id'            => 'nullable|exists:final_statuses,id',
            'dossier_number'             => 'nullable|string|max:50|unique:members,dossier_number,' . $member->id,
            'current_address'            => 'nullable|string',
            'region_id'                  => 'nullable|exists:regions,id',
            'marital_status'             => 'nullable|string|max:100',
            'disease_type'               => 'nullable|string|max:255',
            'phone'                      => 'nullable|string|max:50',
            'phone2'                     => 'nullable|string|max:50',
            'network'                    => 'nullable|in:MTN,SYRIATEL',
            'provider_status'            => 'nullable|string|max:100',
            'job'                        => 'nullable|string|max:150',
            'housing_status_id'          => 'nullable|exists:housing_statuses,id',
            'dependents_count'           => 'nullable|integer|min:0',
            'illness_details'            => 'nullable|string',
            'special_cases_description'  => 'nullable|string',
            'representative_id'          => 'nullable|exists:users,id',
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
            'final_amount'               => 'nullable|numeric|min:0',
            'iban'                       => 'nullable|string|max:50',
            'barcode'                    => 'nullable|string|max:100',
            'iban_image'                 => 'nullable|image|max:2048',
            'barcode_image'              => 'nullable|image|max:2048',
            'recipient_name'             => 'nullable|string|max:150',
            // payment AI
            'iban_ai'                    => 'nullable|string|max:50',
            'barcode_ai'                 => 'nullable|string|max:100',
            'recipient_name_ai'          => 'nullable|string|max:150',
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
        $rawScore             = $workScore + $housingScore + $dependentsScore + $dependentStatusScore + $illnessScore + $specialScore;
        $totalScore           = max(0, $rawScore - $scoreDeduction);

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
            'marital_status'             => $data['marital_status'] ?? null,
            'disease_type'               => $data['disease_type'] ?? null,
            'other_association'          => !empty($request->association_ids),
            'phone'                      => $data['phone'] ?? null,
            'phone2'                     => $data['phone2'] ?? null,
            'representative_id'          => $data['representative_id'] ?? $member->representative_id,
            'delegate'                   => $data['delegate'] ?? null,
            'second_person'              => $data['second_person'] ?? null,
            'association_id'             => $data['association_id'] ?? null,
            'network'                    => $data['network'] ?? null,
            'provider_status'            => $data['provider_status'] ?? null,
            'job'                        => $data['job'] ?? null,
            'housing_status_id'          => $data['housing_status_id'] ?? null,
            'dependents_count'           => $data['dependents_count'] ?? null,
            'illness_details'            => $data['illness_details'] ?? null,
            'special_cases'              => $request->boolean('special_cases'),
            'special_cases_description'  => $data['special_cases_description'] ?? null,
            'sham_cash_account'          => in_array($request->input('sham_cash_account'), ['done','manual']) ? $request->input('sham_cash_account') : null,
            'score'                      => $totalScore,
            'estimated_amount'           => $totalScore * 500,
            'final_amount'               => $totalScore * 500 + ($member->fieldVisits()->latest()->value('estimated_amount') ?? 0),
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
        ])->save();

        $payment = $member->paymentInfo ?? new PaymentInfo(['member_id' => $member->id]);

        if ($request->hasFile('iban_image')) {
            $payment->iban_image = $request->file('iban_image')->store("payment/iban_{$member->id}", 'public');
        }
        if ($request->hasFile('barcode_image')) {
            $payment->barcode_image = $request->file('barcode_image')->store("payment/barcode_{$member->id}", 'public');
        }

        $payment->fill([
            'member_id'      => $member->id,
            'iban'           => str_replace(' ', '', $request->input('iban')),
            'barcode'        => str_replace(' ', '', $request->input('barcode')),
            'recipient_name' => $request->input('recipient_name'),
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
                $member->delete();
            }
            return redirect()->route('members.index')->with('success', "تم حذف {$members->count()} عضو بنجاح.");
        }

        $names = Member::whereIn('id', $ids)->limit(5)->pluck('full_name')->toArray();

        PendingChange::create([
            'model_type'   => 'member',
            'model_id'     => null,
            'action'       => 'bulk_delete',
            'payload'      => ['member_ids' => $ids, 'count' => count($ids), 'names_preview' => $names],
            'original'     => null,
            'requested_by' => Auth::id(),
            'status'       => 'pending',
        ]);

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

        $allowed = ['network', 'marital_status', 'sham_cash_account', 'current_address', 'region_id', 'housing_status_id', 'verification_status_id', 'final_status_id', 'estimated_amount', 'final_amount', 'field_visit_status_id'];
        $data    = [];

        foreach ($fields as $field) {
            if (!in_array($field, $allowed)) continue;
            $value = $request->input("fields.{$field}");
            if ($field === 'sham_cash_account') {
                $data[$field] = in_array($value, ['done', 'manual']) ? $value : null;
            } elseif (in_array($field, ['verification_status_id', 'final_status_id', 'field_visit_status_id', 'housing_status_id'])) {
                $data[$field] = $value ?: null;
            } elseif (in_array($field, ['estimated_amount', 'final_amount'])) {
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
            if (!empty($data)) {
                Member::whereIn('id', $ids)->update($data);
            }
            ActivityLogger::log('updated', "تعديل جماعي على " . count($ids) . " مستفيد: " . implode(', ', array_keys(array_merge($data, array_key_exists('field_visit_status_id', $data ?? []) ? ['field_visit_status_id' => ''] : []))));
            return redirect()->route('members.index')->with('success', "تم تعديل " . count($ids) . " عضو بنجاح.");
        }

        $names = Member::whereIn('id', $ids)->limit(5)->pluck('full_name')->toArray();

        PendingChange::create([
            'model_type'   => 'member',
            'model_id'     => null,
            'action'       => 'bulk_update',
            'payload'      => ['member_ids' => $ids, 'count' => count($ids), 'fields' => $data, 'names_preview' => $names],
            'original'     => null,
            'requested_by' => Auth::id(),
            'status'       => 'pending',
        ]);

        ActivityLogger::log('requested', 'طلب تعديل جماعي لـ ' . count($ids) . ' مستفيد بانتظار موافقة المسؤول');
        return redirect()->route('members.index')->with('pending', 'تم إرسال طلب التعديل الجماعي — بانتظار موافقة المسؤول.');
    }

    public function updateFinalStatus(Request $request, Member $member)
    {
        $request->validate([
            'final_status_id' => 'nullable|exists:final_statuses,id',
        ]);

        $newStatusId = $request->input('final_status_id') ?: null;

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'member',
                'model_id'     => $member->id,
                'action'       => 'update',
                'payload'      => ['final_status_id' => $newStatusId, 'full_name' => $member->full_name],
                'original'     => ['final_status_id' => $member->final_status_id, 'full_name' => $member->full_name],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return back()->with('success', "تم إرسال طلب تغيير الحالة النهائية لـ {$member->full_name} للمراجعة.");
        }

        $oldStatus = $member->finalStatus?->name ?? 'بدون';
        $member->update(['final_status_id' => $newStatusId]);
        $newStatus = $member->fresh()->finalStatus?->name ?? 'بدون';
        ActivityLogger::log('updated', "تغيير الحالة النهائية لـ {$member->full_name}: {$oldStatus} → {$newStatus}", $member);

        return back()->with('success', "تم تحديث الحالة النهائية لـ {$member->full_name}.");
    }
}
