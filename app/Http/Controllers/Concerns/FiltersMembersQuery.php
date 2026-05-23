<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Member;
use Illuminate\Http\Request;

trait FiltersMembersQuery
{
    protected function applyNoneFilter($query, string $column, array $ids, bool $stringColumn = false): void
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

    protected function buildFilteredQuery(Request $request): \Illuminate\Database\Eloquent\Builder
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
        $secondPersons       = array_filter((array) $request->get('second_person', []));
        $specialCases        = $request->get('special_cases', '');
        $specialDescriptions = array_filter((array) $request->get('special_cases_description', []));
        $addresses           = array_filter((array) $request->get('current_address', []));
        $associationIds      = array_filter((array) $request->get('association_id', []));
        $networks            = array_filter((array) $request->get('network', []));
        $shamCash            = array_filter((array) $request->get('sham_cash', []));
        $paymentDataEntries  = array_filter((array) $request->get('payment_data_entry', []));
        $regionIds           = array_filter((array) $request->get('region_id', []));
        $sectorIds           = array_filter((array) $request->get('sector_id', []));
        $housingStatusIds    = array_filter((array) $request->get('housing_status_id', []));
        $estimatedFrom       = trim($request->get('estimated_from', ''));
        $estimatedTo         = trim($request->get('estimated_to', ''));
        $paymentsCountFrom   = trim($request->get('payments_count_from', ''));
        $paymentsCountTo     = trim($request->get('payments_count_to', ''));
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
        $fvHasVideo          = $request->get('fv_has_video', '');
        $fvHasSpecialCase    = $request->get('fv_has_special_case', '');
        $fvCount             = trim($request->get('fv_count', ''));

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
        if ($dossierSearch !== '') $query->where('dossier_number', 'like', "%{$dossierSearch}%");
        if ($dossierFrom !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) >= ?', [(int) $dossierFrom]);
        if ($dossierTo   !== '') $query->whereRaw('CAST(dossier_number AS UNSIGNED) <= ?', [(int) $dossierTo]);
        if (!empty($verificationIds)) {
            $includeNone = in_array('none', $verificationIds);
            $realIds     = array_values(array_filter($verificationIds, fn($id) => $id !== 'none'));
            $query->where(function ($q) use ($includeNone, $realIds) {
                if (!empty($realIds))  $q->whereIn('verification_status_id', $realIds);
                if ($includeNone)      $q->orWhereNull('verification_status_id');
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
        $includeNoVisit    = in_array('none', $fieldVisitStatusIds);
        $realStatusIds     = array_values(array_filter($fieldVisitStatusIds, fn($id) => $id !== 'none'));
        $hasOtherFvFilters = !empty($fvHouseTypeIds) || !empty($fvHouseConditionIds) || !empty($fvVisitors)
            || !empty($fvCreatedByIds)
            || $fvDateFrom !== '' || $fvDateTo !== ''
            || $fvAmountFrom !== '' || $fvAmountTo !== ''
            || $fvNotes !== '' || $fvHasVideo !== '' || $fvHasSpecialCase !== '';
        $applyFvFilters = function ($q) use ($realStatusIds, $fvHouseTypeIds, $fvHouseConditionIds, $fvVisitors, $fvCreatedByIds, $fvDateFrom, $fvDateTo, $fvAmountFrom, $fvAmountTo, $fvNotes, $fvHasVideo, $fvHasSpecialCase) {
            $this->applyNoneFilter($q, 'field_visit_status_id', $realStatusIds);
            $this->applyNoneFilter($q, 'house_type_id', $fvHouseTypeIds);
            $this->applyNoneFilter($q, 'house_condition_id', $fvHouseConditionIds);
            if (!empty($fvVisitors))            $q->whereIn('visitor', $fvVisitors);
            if (!empty($fvCreatedByIds))        $q->whereIn('created_by', $fvCreatedByIds);
            if ($fvDateFrom !== '')             $q->where('visit_date', '>=', $fvDateFrom);
            if ($fvDateTo !== '')               $q->where('visit_date', '<=', $fvDateTo);
            if ($fvAmountFrom !== '')           $q->where('estimated_amount', '>=', (float) $fvAmountFrom);
            if ($fvAmountTo !== '')             $q->where('estimated_amount', '<=', (float) $fvAmountTo);
            if ($fvNotes !== '')                $q->where('notes', 'like', "%{$fvNotes}%");
            if ($fvHasVideo === '1')            $q->where('has_video', true);
            elseif ($fvHasVideo === '0')        $q->where(fn($s) => $s->where('has_video', false)->orWhereNull('has_video'));
            if ($fvHasSpecialCase === '1')      $q->where('has_special_case', true);
            elseif ($fvHasSpecialCase === '0')  $q->where(fn($s) => $s->where('has_special_case', false)->orWhereNull('has_special_case'));
        };
        if ($includeNoVisit && (!empty($realStatusIds) || $hasOtherFvFilters)) {
            $query->where(fn($q) => $q->doesntHave('fieldVisits')->orWhereHas('fieldVisits', $applyFvFilters));
        } elseif ($includeNoVisit) {
            $query->doesntHave('fieldVisits');
        } elseif (!empty($realStatusIds) || $hasOtherFvFilters) {
            $query->whereHas('fieldVisits', $applyFvFilters);
        }
        if ($fvCount !== '') {
            if ($fvCount === '0') {
                $query->doesntHave('fieldVisits');
            } else {
                $query->has('fieldVisits', '>=', (int) $fvCount);
            }
        }
        $this->applyNoneFilter($query, 'region_id', $regionIds);
        $this->applyNoneFilter($query, 'sector_id', $sectorIds);
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

        return $query;
    }

    protected function filterViewData(Request $request): array
    {
        return [
            'search'              => $request->get('search', ''),
            'dossierSearch'       => trim($request->get('dossier_search', '')),
            'dossierFrom'         => trim($request->get('dossier_from', '')),
            'dossierTo'           => trim($request->get('dossier_to', '')),
            'verificationIds'     => array_filter((array) $request->get('verification_status_id', [])),
            'finalStatusIds'      => array_filter((array) $request->get('final_status_id', [])),
            'maritalStatuses'     => array_filter((array) $request->get('marital_status', [])),
            'genders'             => array_filter((array) $request->get('gender', [])),
            'delegates'           => array_filter((array) $request->get('delegate', [])),
            'secondPersons'       => array_filter((array) $request->get('second_person', [])),
            'specialCases'        => $request->get('special_cases', ''),
            'specialDescriptions' => array_filter((array) $request->get('special_cases_description', [])),
            'addresses'           => array_filter((array) $request->get('current_address', [])),
            'associationIds'      => array_filter((array) $request->get('association_id', [])),
            'networks'            => array_filter((array) $request->get('network', [])),
            'shamCash'            => array_filter((array) $request->get('sham_cash', [])),
            'paymentDataEntries'  => array_filter((array) $request->get('payment_data_entry', [])),
            'regionIds'           => array_filter((array) $request->get('region_id', [])),
            'sectorIds'           => array_filter((array) $request->get('sector_id', [])),
            'housingStatusIds'    => array_filter((array) $request->get('housing_status_id', [])),
            'estimatedFrom'       => trim($request->get('estimated_from', '')),
            'estimatedTo'         => trim($request->get('estimated_to', '')),
            'paymentsCountFrom'   => trim($request->get('payments_count_from', '')),
            'paymentsCountTo'     => trim($request->get('payments_count_to', '')),
            'fieldVisitStatusIds' => array_filter((array) $request->get('field_visit_status_id', [])),
            'fvHouseTypeIds'      => array_filter((array) $request->get('fv_house_type_id', [])),
            'fvHouseConditionIds' => array_filter((array) $request->get('fv_house_condition_id', [])),
            'fvVisitors'          => array_filter((array) $request->get('fv_visitors', [])),
            'fvCreatedByIds'      => array_filter((array) $request->get('fv_created_by', [])),
            'fvDateFrom'          => trim($request->get('fv_date_from', '')),
            'fvDateTo'            => trim($request->get('fv_date_to', '')),
            'fvAmountFrom'        => trim($request->get('fv_amount_from', '')),
            'fvAmountTo'          => trim($request->get('fv_amount_to', '')),
            'fvNotes'             => trim($request->get('fv_notes', '')),
            'fvHasVideo'          => $request->get('fv_has_video', ''),
            'fvHasSpecialCase'    => $request->get('fv_has_special_case', ''),
            'fvCount'             => trim($request->get('fv_count', '')),
        ];
    }

    protected function filterListData(): array
    {
        return [
            'verificationStatuses'  => \App\Models\VerificationStatus::active()->orderBy('name')->get(),
            'finalStatusList'       => \App\Models\FinalStatus::active()->orderBy('name')->get(),
            'maritalStatusList'     => \App\Models\MaritalStatus::active()->orderBy('id')->get(),
            'associationList'       => \App\Models\Association::active()->orderBy('name')->get(),
            'regionList'            => \App\Models\Region::active()->orderBy('name')->get(),
            'sectorList'            => \App\Models\Sector::active()->orderBy('name')->get(),
            'houseTypes'            => \App\Models\HouseType::active()->orderBy('id')->get(),
            'houseConditions'       => \App\Models\HouseCondition::active()->orderBy('name')->get(),
            'housingStatusList'     => \App\Models\HousingStatus::active()->orderBy('name')->get(),
            'fieldVisitStatuses'    => \App\Models\FieldVisitStatus::active()->orderBy('id')->get(),
            'delegateList'          => \App\Models\Delegate::orderBy('name')->pluck('name'),
            'secondPersonList'      => Member::whereNotNull('second_person')->where('second_person', '!=', '')->distinct()->orderBy('second_person')->pluck('second_person'),
            'specialDescriptionList'=> Member::whereNotNull('special_cases_description')->where('special_cases_description', '!=', '')->distinct()->orderBy('special_cases_description')->pluck('special_cases_description'),
            'addressList'           => Member::whereNotNull('current_address')->where('current_address', '!=', '')->distinct()->orderBy('current_address')->pluck('current_address'),
            'fvVisitorList'         => \App\Models\FieldVisit::whereNotNull('visitor')->where('visitor', '!=', '')->distinct()->orderBy('visitor')->pluck('visitor'),
            'fvCreatedByList'       => \App\Models\User::whereIn('id', \App\Models\FieldVisit::whereNotNull('created_by')->distinct()->pluck('created_by'))->orderBy('name')->get(['id', 'name']),
            'paymentDataEntryList'  => \App\Models\PaymentInfo::whereNotNull('data_entry_name')->where('data_entry_name', '!=', '')->distinct()->orderBy('data_entry_name')->pluck('data_entry_name'),
        ];
    }
}
