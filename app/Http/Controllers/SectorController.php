<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\FieldVisit;
use App\Models\FieldVisitStatus;
use App\Models\FinalStatus;
use App\Models\HouseCondition;
use App\Models\HouseType;
use App\Models\HousingStatus;
use App\Models\MaritalStatus;
use App\Models\Member;
use App\Models\PaymentInfo;
use App\Models\PendingChange;
use App\Models\Region;
use App\Models\Sector;
use App\Models\User;
use App\Models\VerificationStatus;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectorController extends Controller
{
    private function adminOnly(): void
    {
        abort_if(Auth::user()?->role !== 'admin', 403);
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    private function applyNoneFilter($query, string $column, array $ids, bool $stringColumn = false): void
    {
        if (empty($ids)) return;
        $includeNone = in_array('none', $ids);
        $realIds     = array_values(array_filter($ids, fn($id) => $id !== 'none'));
        $query->where(function ($q) use ($column, $includeNone, $realIds, $stringColumn) {
            if (!empty($realIds)) $q->whereIn($column, $realIds);
            if ($includeNone) {
                $q->orWhereNull($column);
                if ($stringColumn) $q->orWhere($column, '');
            }
        });
    }

    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:sectors,name',
        ]);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'sector',
                'model_id'     => null,
                'action'       => 'create',
                'payload'      => ['name' => $data['name'], 'is_active' => true],
                'original'     => null,
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return response()->json([
                'pending' => true,
                'message' => 'تم إرسال طلب إضافة القطاع "' . $data['name'] . '" — بانتظار موافقة المسؤول.',
            ]);
        }

        $sector = Sector::create(['name' => $data['name'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة قطاع: {$data['name']}");

        return response()->json(['id' => $sector->id, 'name' => $sector->name]);
    }

    public function index()
    {
        $this->adminOnly();
        $sectors = Sector::withCount(['members', 'regions'])->orderBy('name')->get();
        return view('sectors.index', compact('sectors'));
    }

    public function export(Request $request)
    {
        $this->adminOnly();
        $ids      = array_filter(array_map('intval', (array) $request->get('ids', [])));
        $filename = 'قوائم_الدفع_' . now()->format('Y-m-d') . '.xlsx';
        return (new \App\Exports\SectorsMembersExport($ids))->download($filename);
    }

    public function exportSingle(Sector $sector)
    {
        $this->adminOnly();
        $sector->load(['members' => fn($q) => $q->with('paymentInfo')->orderByRaw('CAST(dossier_number AS UNSIGNED)')]);
        $filename = $sector->name . '_' . now()->format('Y-m-d') . '.xlsx';
        return (new \App\Exports\SectorSheetExport($sector))->download($filename);
    }

    private function buildMembersQuery(Request $request, Sector $sector): \Illuminate\Database\Eloquent\Builder
    {
        $search              = trim($request->get('search', ''));
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
        $shamCash            = array_filter((array) $request->get('sham_cash', []));
        $regionIds           = array_filter((array) $request->get('region_id', []));
        $housingStatusIds    = array_filter((array) $request->get('housing_status_id', []));
        $estimatedFrom       = trim($request->get('estimated_from', ''));
        $estimatedTo         = trim($request->get('estimated_to', ''));
        $paymentsCountFrom   = trim($request->get('payments_count_from', ''));
        $paymentsCountTo     = trim($request->get('payments_count_to', ''));
        $paymentDataEntries  = array_filter((array) $request->get('payment_data_entry', []));
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
        $fvHasVideo          = $request->get('fv_has_video', '');
        $fvHasSpecialCase    = $request->get('fv_has_special_case', '');
        $fvCount             = trim($request->get('fv_count', ''));

        $query = $sector->members()->with(['verificationStatus', 'finalStatus', 'region']);

        if ($search !== '') {
            $query->where(fn($q) => $q->where('full_name', 'like', "%{$search}%")
                                      ->orWhere('dossier_number', 'like', "%{$search}%")
                                      ->orWhere('phone', 'like', "%{$search}%")
                                      ->orWhere('national_id', 'like', "%{$search}%")
                                      ->orWhere('second_person', 'like', "%{$search}%"));
        }
        if ($dossierSearch !== '') $query->where('dossier_number', 'like', "%{$dossierSearch}%");
        if ($dossierFrom   !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) >= ?', [(int) $dossierFrom]);
        if ($dossierTo     !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) <= ?', [(int) $dossierTo]);

        if (!empty($verificationIds)) {
            $includeNone = in_array('none', $verificationIds);
            $realIds     = array_values(array_filter($verificationIds, fn($id) => $id !== 'none'));
            $query->where(function ($q) use ($includeNone, $realIds) {
                if (!empty($realIds)) $q->whereIn('verification_status_id', $realIds);
                if ($includeNone)     $q->orWhereNull('verification_status_id');
            });
        }
        $this->applyNoneFilter($query, 'final_status_id', $finalStatusIds);
        $this->applyNoneFilter($query, 'marital_status', $maritalStatuses, true);
        $this->applyNoneFilter($query, 'gender', $genders, true);
        $this->applyNoneFilter($query, 'delegate', $delegates, true);
        $this->applyNoneFilter($query, 'second_person', $secondPersons, true);

        if ($specialCases === '1') {
            $query->where('special_cases', true);
        } elseif ($specialCases === '0') {
            $query->where(function ($q) {
                $q->where('special_cases', false)->orWhereNull('special_cases');
            });
        }
        $this->applyNoneFilter($query, 'special_cases_description', $specialDescriptions, true);
        $this->applyNoneFilter($query, 'current_address', $addresses, true);
        $this->applyNoneFilter($query, 'association_id', $associationIds);
        $this->applyNoneFilter($query, 'network', $networks, true);

        if (!empty($shamCash)) {
            $query->where(function ($q) use ($shamCash) {
                if (in_array('done',   $shamCash)) $q->orWhere('sham_cash_account', 'done');
                if (in_array('manual', $shamCash)) $q->orWhere('sham_cash_account', 'manual');
                if (in_array('none',   $shamCash)) $q->orWhereNull('sham_cash_account');
            });
        }

        $this->applyNoneFilter($query, 'region_id', $regionIds);
        $this->applyNoneFilter($query, 'housing_status_id', $housingStatusIds);

        if ($estimatedFrom !== '') $query->where('estimated_amount', '>=', (float) str_replace(',', '', $estimatedFrom));
        if ($estimatedTo   !== '') $query->where('estimated_amount', '<=', (float) str_replace(',', '', $estimatedTo));
        if ($paymentsCountFrom !== '') $query->where('payments_count', '>=', (int) $paymentsCountFrom);
        if ($paymentsCountTo   !== '') $query->where('payments_count', '<=', (int) $paymentsCountTo);

        if (!empty($paymentDataEntries)) {
            $includeNone = in_array('none', $paymentDataEntries);
            $realNames   = array_values(array_filter($paymentDataEntries, fn($v) => $v !== 'none'));
            $query->where(function ($q) use ($includeNone, $realNames) {
                if (!empty($realNames)) {
                    $q->whereHas('paymentInfo', fn($qi) => $qi->whereIn('data_entry_name', $realNames));
                }
                if ($includeNone) {
                    $q->orWhereDoesntHave('paymentInfo')
                      ->orWhereHas('paymentInfo', fn($qi) => $qi->whereNull('data_entry_name')->orWhere('data_entry_name', ''));
                }
            });
        }

        $includeNoVisit    = in_array('none', $fieldVisitStatusIds);
        $realStatusIds     = array_values(array_filter($fieldVisitStatusIds, fn($id) => $id !== 'none'));
        $hasOtherFvFilters = !empty($fvHouseTypeIds) || !empty($fvHouseConditionIds)
            || !empty($fvVisitors) || !empty($fvCreatedByIds)
            || $fvDateFrom !== '' || $fvDateTo !== ''
            || $fvAmountFrom !== '' || $fvAmountTo !== ''
            || $fvNotes !== '' || $fvHasVideo !== '' || $fvHasSpecialCase !== '';

        $applyFvFilters = function ($q) use ($realStatusIds, $fvHouseTypeIds, $fvHouseConditionIds, $fvVisitors, $fvCreatedByIds, $fvDateFrom, $fvDateTo, $fvAmountFrom, $fvAmountTo, $fvNotes, $fvHasVideo, $fvHasSpecialCase) {
            $this->applyNoneFilter($q, 'field_visit_status_id', $realStatusIds);
            $this->applyNoneFilter($q, 'house_type_id', $fvHouseTypeIds);
            $this->applyNoneFilter($q, 'house_condition_id', $fvHouseConditionIds);
            if (!empty($fvVisitors))     $q->whereIn('visitor', $fvVisitors);
            if (!empty($fvCreatedByIds)) $q->whereIn('created_by', $fvCreatedByIds);
            if ($fvDateFrom !== '')      $q->where('visit_date', '>=', $fvDateFrom);
            if ($fvDateTo   !== '')      $q->where('visit_date', '<=', $fvDateTo);
            if ($fvAmountFrom !== '')    $q->where('estimated_amount', '>=', (float) $fvAmountFrom);
            if ($fvAmountTo   !== '')    $q->where('estimated_amount', '<=', (float) $fvAmountTo);
            if ($fvNotes !== '')         $q->where('notes', 'like', "%{$fvNotes}%");
            if ($fvHasVideo === '1')     $q->where('has_video', true);
            elseif ($fvHasVideo === '0') $q->where(fn($s) => $s->where('has_video', false)->orWhereNull('has_video'));
            if ($fvHasSpecialCase === '1')     $q->where('has_special_case', true);
            elseif ($fvHasSpecialCase === '0') $q->where(fn($s) => $s->where('has_special_case', false)->orWhereNull('has_special_case'));
        };

        if ($includeNoVisit && (!empty($realStatusIds) || $hasOtherFvFilters)) {
            $query->where(fn($q) => $q->doesntHave('fieldVisits')->orWhereHas('fieldVisits', $applyFvFilters));
        } elseif ($includeNoVisit) {
            $query->doesntHave('fieldVisits');
        } elseif (!empty($realStatusIds) || $hasOtherFvFilters) {
            $query->whereHas('fieldVisits', $applyFvFilters);
        }
        if ($fvCount !== '') {
            if ($fvCount === '0') $query->doesntHave('fieldVisits');
            else                  $query->has('fieldVisits', '>=', (int) $fvCount);
        }

        return $query->orderByRaw('CAST(dossier_number AS UNSIGNED) ASC');
    }

    public function show(Request $request, Sector $sector)
    {
        $this->adminOnly();

        $search              = trim($request->get('search', ''));
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
        $shamCash            = array_filter((array) $request->get('sham_cash', []));
        $regionIds           = array_filter((array) $request->get('region_id', []));
        $housingStatusIds    = array_filter((array) $request->get('housing_status_id', []));
        $estimatedFrom       = trim($request->get('estimated_from', ''));
        $estimatedTo         = trim($request->get('estimated_to', ''));
        $paymentsCountFrom   = trim($request->get('payments_count_from', ''));
        $paymentsCountTo     = trim($request->get('payments_count_to', ''));
        $paymentDataEntries  = array_filter((array) $request->get('payment_data_entry', []));
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
        $fvHasVideo          = $request->get('fv_has_video', '');
        $fvHasSpecialCase    = $request->get('fv_has_special_case', '');
        $fvCount             = trim($request->get('fv_count', ''));

        $members = $this->buildMembersQuery($request, $sector)
                        ->paginate(50)
                        ->withQueryString();

        $allSectors             = Sector::active()->orderBy('name')->get();
        $sectorRegions          = $sector->regions()->withCount('members')->orderBy('name')->get();
        $availableRegions       = Region::whereNull('sector_id')->orderBy('name')->get();
        $verificationStatuses   = VerificationStatus::active()->orderBy('name')->get();
        $finalStatuses          = FinalStatus::active()->orderBy('name')->get();
        $maritalStatusList      = MaritalStatus::active()->orderBy('id')->get();
        $associationList        = Association::active()->orderBy('name')->get();
        $housingStatusList      = HousingStatus::active()->orderBy('name')->get();
        $fieldVisitStatuses     = FieldVisitStatus::active()->orderBy('id')->get();
        $houseTypes             = HouseType::active()->orderBy('id')->get();
        $houseConditions        = HouseCondition::active()->orderBy('name')->get();
        $delegateList           = Member::whereNotNull('delegate')->where('delegate', '!=', '')->distinct()->orderBy('delegate')->pluck('delegate');
        $secondPersonList       = Member::whereNotNull('second_person')->where('second_person', '!=', '')->distinct()->orderBy('second_person')->pluck('second_person');
        $specialDescriptionList = Member::whereNotNull('special_cases_description')->where('special_cases_description', '!=', '')->distinct()->orderBy('special_cases_description')->pluck('special_cases_description');
        $addressList            = Member::whereNotNull('current_address')->where('current_address', '!=', '')->distinct()->orderBy('current_address')->pluck('current_address');
        $fvVisitorList          = FieldVisit::whereNotNull('visitor')->where('visitor', '!=', '')->distinct()->orderBy('visitor')->pluck('visitor');
        $fvCreatedByList        = User::whereIn('id', FieldVisit::whereNotNull('created_by')->distinct()->pluck('created_by'))->orderBy('name')->get(['id', 'name']);
        $paymentDataEntryList   = PaymentInfo::whereNotNull('data_entry_name')->where('data_entry_name', '!=', '')->distinct()->orderBy('data_entry_name')->pluck('data_entry_name');

        $hasFvFilters = !empty($fieldVisitStatusIds) || !empty($fvHouseTypeIds) || !empty($fvHouseConditionIds)
            || !empty($fvVisitors) || !empty($fvCreatedByIds)
            || $fvDateFrom !== '' || $fvDateTo !== ''
            || $fvAmountFrom !== '' || $fvAmountTo !== ''
            || $fvNotes !== '' || $fvHasVideo !== '' || $fvHasSpecialCase !== '';

        $hasFilters = $search !== '' || $dossierSearch !== '' || $dossierFrom !== '' || $dossierTo !== ''
            || !empty($verificationIds) || !empty($finalStatusIds) || !empty($maritalStatuses) || !empty($genders)
            || !empty($delegates) || !empty($secondPersons) || $specialCases !== '' || !empty($specialDescriptions)
            || !empty($addresses) || !empty($associationIds) || !empty($networks) || !empty($shamCash)
            || !empty($regionIds) || !empty($housingStatusIds) || !empty($paymentDataEntries)
            || $estimatedFrom !== '' || $estimatedTo !== '' || $paymentsCountFrom !== '' || $paymentsCountTo !== ''
            || $hasFvFilters || $fvCount !== '';

        return view('sectors.show', compact(
            'sector', 'members', 'allSectors',
            'sectorRegions', 'availableRegions',
            'verificationStatuses', 'finalStatuses',
            'maritalStatusList', 'associationList', 'housingStatusList',
            'fieldVisitStatuses', 'houseTypes', 'houseConditions',
            'delegateList', 'secondPersonList', 'specialDescriptionList', 'addressList',
            'fvVisitorList', 'fvCreatedByList', 'paymentDataEntryList',
            'search', 'dossierSearch', 'dossierFrom', 'dossierTo',
            'verificationIds', 'finalStatusIds', 'maritalStatuses', 'genders',
            'delegates', 'secondPersons', 'specialCases', 'specialDescriptions',
            'addresses', 'associationIds', 'networks', 'shamCash',
            'regionIds', 'housingStatusIds', 'paymentDataEntries',
            'estimatedFrom', 'estimatedTo', 'paymentsCountFrom', 'paymentsCountTo',
            'fieldVisitStatusIds', 'fvHouseTypeIds', 'fvHouseConditionIds',
            'fvVisitors', 'fvCreatedByIds', 'fvDateFrom', 'fvDateTo',
            'fvAmountFrom', 'fvAmountTo', 'fvNotes', 'fvHasVideo', 'fvHasSpecialCase', 'fvCount',
            'hasFilters', 'hasFvFilters'
        ));
    }

    public function updateRegions(Request $request, Sector $sector)
    {
        $this->adminOnly();

        $regionIds = array_filter((array) $request->input('region_ids', []));

        // Detach regions currently belonging to this sector that are not in the new list
        Region::where('sector_id', $sector->id)
              ->whereNotIn('id', $regionIds)
              ->update(['sector_id' => null]);

        // Attach new regions (only unassigned or already in this sector)
        if (!empty($regionIds)) {
            Region::whereIn('id', $regionIds)
                  ->where(fn($q) => $q->whereNull('sector_id')->orWhere('sector_id', $sector->id))
                  ->update(['sector_id' => $sector->id]);
        }

        ActivityLogger::log('updated', "تحديث مناطق القطاع: {$sector->name}");
        return redirect()->route('sectors.show', $sector)->with('success', 'تم تحديث مناطق القطاع بنجاح.');
    }

    public function exportSingle(Request $request, Sector $sector)
    {
        $this->adminOnly();
        $members  = $this->buildMembersQuery($request, $sector)->with('paymentInfo')->get();
        $filename = $sector->name . '_' . now()->format('Y-m-d') . '.xlsx';
        return (new \App\Exports\SectorSheetExport($sector, $members))->download($filename);
    }

    public function store(Request $request)
    {
        $this->adminOnly();
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:sectors,name',
        ]);
        Sector::create(['name' => $data['name'], 'is_active' => true]);
        ActivityLogger::log('created', "إضافة قطاع: {$data['name']}");
        return redirect()->route('sectors.index')->with('success', 'تمت إضافة القطاع بنجاح.');
    }

    public function update(Request $request, Sector $sector)
    {
        $this->adminOnly();
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:sectors,name,' . $sector->id,
        ]);
        $sector->update([
            'name'      => $data['name'],
            'is_active' => $request->boolean('is_active'),
        ]);
        ActivityLogger::log('updated', "تعديل قطاع: {$sector->name}");
        return redirect()->route('sectors.index')->with('success', 'تم تحديث القطاع بنجاح.');
    }

    public function destroy(Sector $sector)
    {
        $this->adminOnly();
        $name = $sector->name;
        $sector->delete();
        ActivityLogger::log('deleted', "حذف قطاع: {$name}");
        return redirect()->route('sectors.index')->with('success', 'تم حذف القطاع.');
    }
}
