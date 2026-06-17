@extends('layouts.app')

@section('title', 'الدفعات الجماعية — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="text-emerald-600 hover:underline">الأعضاء</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700">الدفعات الجماعية</span>
@endsection

@section('content')

@php
    $qs  = request()->getQueryString();
    $fmt = fn($n) => number_format((int)$n, 0, '.', ',');

    $fvActiveCount = (int)!empty($fieldVisitStatusIds) + (int)!empty($fvHouseTypeIds) + (int)!empty($fvHouseConditionIds)
        + (!empty($fvVisitors) ? 1 : 0) + (!empty($fvCreatedByIds) ? 1 : 0)
        + ($fvDateFrom !== '' || $fvDateTo !== '' ? 1 : 0)
        + ($fvAmountFrom !== '' || $fvAmountTo !== '' ? 1 : 0)
        + ($fvNotes !== '' ? 1 : 0) + ($fvHasVideo !== '' ? 1 : 0) + ($fvHasSpecialCase !== '' ? 1 : 0)
        + ($fvCount !== '' ? 1 : 0);

    $mainActiveCount =
        ($dossierFrom !== '' || $dossierTo !== '' ? 1 : 0)
        + ($estimatedFrom !== '' || $estimatedTo !== '' ? 1 : 0)
        + ($paymentsCountFrom !== '' || $paymentsCountTo !== '' ? 1 : 0)
        + (!empty($verificationIds)    ? 1 : 0)
        + (!empty($finalStatusIds)     ? 1 : 0)
        + (!empty($maritalStatuses)    ? 1 : 0)
        + (!empty($genders)            ? 1 : 0)
        + (!empty($associationIds)     ? 1 : 0)
        + (!empty($delegates)          ? 1 : 0)
        + (!empty($secondPersons)      ? 1 : 0)
        + (!empty($specialDescriptions)? 1 : 0)
        + (!empty($addresses)          ? 1 : 0)
        + (!empty($networks)           ? 1 : 0)
        + (!empty($shamCash)           ? 1 : 0)
        + (!empty($sectorIds)          ? 1 : 0)
        + (!empty($regionIds)          ? 1 : 0)
        + (!empty($housingStatusIds)   ? 1 : 0)
        + (!empty($paymentDataEntries) ? 1 : 0)
        + ($specialCases  !== '' ? 1 : 0)
        + ($hasPayments   !== '' ? 1 : 0);

    $paymentActiveCount =
        ($hasIban !== '' ? 1 : 0)
        + ($hasBarcode !== '' ? 1 : 0)
        + (!empty($paymentReviewStatus) ? 1 : 0)
        + ($lastBatchDateFrom !== '' || $lastBatchDateTo !== '' ? 1 : 0)
        + (!empty($batchIds) ? 1 : 0);

    // Panel auto-opens for any active filter
    $hasMainFilters = $mainActiveCount > 0 || $fvActiveCount > 0 || $paymentActiveCount > 0;
@endphp

<style>
#action-bar { box-shadow: 0 -4px 24px rgba(0,0,0,0.08); }
</style>

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-teal-600 via-cyan-500 to-sky-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-black text-white">الدفعات الجماعية</h1>
            <p class="text-cyan-100 text-sm mt-0.5">إضافة أو تعيين عدد الدفعات لمجموعة من المستفيدين دفعةً واحدة</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-white font-black text-2xl leading-none">{{ $fmt($totalCount) }}</p>
                <p class="text-cyan-200 text-xs mt-0.5">إجمالي النتائج</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-white font-black text-2xl leading-none">{{ $fmt($withPayments) }}</p>
                <p class="text-cyan-200 text-xs mt-0.5">لديهم دفعات</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[120px]">
                <p class="text-white font-black text-2xl leading-none">{{ $fmt($totalPayments) }}</p>
                <p class="text-cyan-200 text-xs mt-0.5">مجموع الدفعات</p>
            </div>
        </div>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
    <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    {{ session('error') }}
</div>
@endif
@if(session('pending'))
<div class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('pending') }}
</div>
@endif

{{-- ===== FILTER FORM (GET) ===== --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <form method="GET" action="{{ route('members.bulk-payments') }}" id="bp-filter-form"
          onsubmit="removeBpEmptyFilters(this)">

        {{-- Search + dossier search + toggle --}}
        <div class="px-5 pt-4 pb-3 border-b border-gray-100 space-y-2.5">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="بحث بالاسم، رقم الهوية، الهاتف..."
                           class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
                </div>
                <div class="relative w-full sm:w-44">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <input type="text" name="dossier_search" value="{{ $dossierSearch ?? '' }}"
                           placeholder="رقم الاضبارة..."
                           class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    بحث
                </button>
                <button type="button" onclick="toggleBpFilters()"
                        class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-teal-300 transition-colors text-sm font-bold text-gray-600">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    الفلاتر
                    @php $totalActiveCount = $mainActiveCount + $fvActiveCount + $paymentActiveCount; @endphp
                    @if($totalActiveCount > 0)
                        <span class="text-xs bg-teal-600 text-white rounded-full px-1.5 py-0.5 font-black">{{ $totalActiveCount }}</span>
                    @endif
                    <svg id="bp-filter-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200 {{ $hasMainFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Collapsible filters --}}
        <div id="bp-filter-body" class="{{ $hasMainFilters ? '' : 'hidden' }}">
        <div class="p-5">

        {{-- Dossier range --}}
        <div class="flex items-end gap-3 mb-3">
            <div class="flex-1 max-w-xs">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">رقم الاضبارة من</label>
                <input type="text" name="dossier_from" value="{{ $dossierFrom }}" placeholder="مثال: 100"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300 font-mono">
            </div>
            <span class="text-gray-400 pb-2.5">—</span>
            <div class="flex-1 max-w-xs">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                <input type="text" name="dossier_to" value="{{ $dossierTo }}" placeholder="مثال: 200"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300 font-mono">
            </div>
        </div>

        {{-- Amount + Payments ranges --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">المبلغ المقدر من</label>
                    <input type="number" name="estimated_from" value="{{ $estimatedFrom }}" min="0" step="any" placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="estimated_to" value="{{ $estimatedTo }}" min="0" step="any" placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">ل.س</span>
            </div>
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">عدد الدفعات من</label>
                    <input type="number" name="payments_count_from" value="{{ $paymentsCountFrom }}" min="0" step="1" placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="payments_count_to" value="{{ $paymentsCountTo }}" min="0" step="1" placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">دفعة</span>
            </div>
        </div>

        {{-- Filter row 1 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-3">

            {{-- Verification status --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">حالة التحقق</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="verification_status_id[]" value="none" {{ in_array('none', $verificationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون حالة
                    </label>
                    @foreach($verificationStatuses as $vs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="verification_status_id[]" value="{{ $vs->id }}" {{ in_array($vs->id, $verificationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $vs->color }}"></span>
                            {{ $vs->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Final status --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة النهائية</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="final_status_id[]" value="none" {{ in_array('none', $finalStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @foreach($finalStatusList as $fs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}" {{ in_array($fs->id, $finalStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $fs->color }}"></span>
                            {{ $fs->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Marital status --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة الاجتماعية</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="marital_status[]" value="none" {{ in_array('none', $maritalStatuses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @foreach($maritalStatusList as $ms)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="marital_status[]" value="{{ $ms->name }}" {{ in_array($ms->name, $maritalStatuses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $ms->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Gender --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجنس</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    @foreach(['ذكر', 'أنثى'] as $g)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="gender[]" value="{{ $g }}" {{ in_array($g, $genders) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $g }}
                        </label>
                    @endforeach
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-t border-gray-100">
                        <input type="checkbox" name="gender[]" value="none" {{ in_array('none', $genders) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        غير محدد
                    </label>
                </div>
            </div>

            {{-- Association --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجمعية</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="association_id[]" value="none" {{ in_array('none', $associationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @forelse($associationList as $assoc)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="association_id[]" value="{{ $assoc->id }}" {{ in_array($assoc->id, $associationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $assoc->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد جمعيات</p>
                    @endforelse
                </div>
            </div>

            {{-- Special cases --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالات الخاصة</label>
                <select name="special_cases" onwheel="this.blur()"
                        class="w-full text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1" {{ $specialCases === '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ $specialCases === '0' ? 'selected' : '' }}>لا</option>
                </select>
            </div>

            {{-- Sham cash --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">شام كاش</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    @foreach(['done' => 'تم', 'manual' => 'يدوي', 'none' => 'لا يوجد'] as $val => $lbl)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="sham_cash[]" value="{{ $val }}" {{ in_array($val, (array) $shamCash) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $lbl }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Sector --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">القطاع</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400" placeholder="بحث في القطاعات...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="sector_id[]" value="none" {{ in_array('none', $sectorIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @forelse($sectorList as $sec)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="sector_id[]" value="{{ $sec->id }}" {{ in_array($sec->id, $sectorIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $sec->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-sm text-gray-400">لا توجد قطاعات</p>
                    @endforelse
                    </div>
                </div>
            </div>

            {{-- Region --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المنطقة</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400" placeholder="بحث في المناطق...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="region_id[]" value="none" {{ in_array('none', $regionIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @forelse($regionList as $reg)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="region_id[]" value="{{ $reg->id }}" {{ in_array($reg->id, $regionIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            <svg class="w-3.5 h-3.5 text-teal-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $reg->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-sm text-gray-400">لا توجد مناطق</p>
                    @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Filter row 2 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">

            {{-- Delegate --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المندوب</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="delegate[]" value="none" {{ in_array('none', $delegates) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @forelse($delegateList as $d)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="delegate[]" value="{{ $d }}" {{ in_array($d, $delegates) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $d }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا يوجد مندوبون</p>
                    @endforelse
                </div>
            </div>

            {{-- Second person --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الفرد الثاني</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="second_person[]" value="none" {{ in_array('none', $secondPersons) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @forelse($secondPersonList as $sp)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="second_person[]" value="{{ $sp }}" {{ in_array($sp, $secondPersons) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $sp }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا يوجد أفراد ثانيون</p>
                    @endforelse
                </div>
            </div>

            {{-- Special cases description --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">وصف الحالة الخاصة</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400" placeholder="بحث في الأوصاف...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="special_cases_description[]" value="none" {{ in_array('none', $specialDescriptions) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @forelse($specialDescriptionList as $sd)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="special_cases_description[]" value="{{ $sd }}" {{ in_array($sd, $specialDescriptions) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            <span class="truncate">{{ $sd }}</span>
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات خاصة مسجلة</p>
                    @endforelse
                    </div>
                </div>
            </div>

            {{-- Network --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">نوع الشبكة</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="network[]" value="none" {{ in_array('none', $networks) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @foreach(['MTN', 'SYRIATEL'] as $net)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="network[]" value="{{ $net }}" {{ in_array($net, $networks) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $net }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Payment data entry name --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">اسم مدخل الدفع</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="payment_data_entry[]" value="none" {{ in_array('none', $paymentDataEntries) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @forelse($paymentDataEntryList as $pde)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="payment_data_entry[]" value="{{ $pde }}" {{ in_array($pde, $paymentDataEntries) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $pde }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>

            {{-- Address --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">العنوان التفصيلي</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400" placeholder="بحث في العناوين...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="current_address[]" value="none" {{ in_array('none', $addresses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        بدون
                    </label>
                    @forelse($addressList as $addr)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="current_address[]" value="{{ $addr }}" {{ in_array($addr, $addresses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            <span class="truncate">{{ $addr }}</span>
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد عناوين مسجلة</p>
                    @endforelse
                    </div>
                </div>
            </div>

            {{-- Housing status --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">وضع السكن</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($housingStatusList as $hs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="housing_status_id[]" value="{{ $hs->id }}" {{ in_array($hs->id, $housingStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hs->color }}"></span>
                            {{ $hs->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد أوضاع سكن</p>
                    @endforelse
                </div>
            </div>

            {{-- Has payments --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الدفعات</label>
                <select name="has_payments" onwheel="this.blur()"
                        class="w-full text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1" {{ $hasPayments === '1' ? 'selected' : '' }}>لديهم دفعات</option>
                    <option value="0" {{ $hasPayments === '0' ? 'selected' : '' }}>بدون دفعات</option>
                </select>
            </div>

        </div>

        {{-- Field visit filters --}}
        <div class="border border-indigo-100 rounded-2xl mb-4">
            <button type="button" onclick="toggleBpFvFilters()"
                    class="w-full flex items-center justify-between gap-3 px-5 py-3 bg-indigo-50/60 hover:bg-indigo-50 transition-colors text-right rounded-t-2xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-indigo-700">فلاتر الجولة الميدانية</span>
                    @if($fvActiveCount > 0)
                        <span class="text-xs bg-indigo-600 text-white rounded-full px-2 py-0.5 font-bold">{{ $fvActiveCount }} فعّال</span>
                    @endif
                </div>
                <svg id="bp-fv-filter-arrow" class="w-4 h-4 text-indigo-400 transition-transform duration-200 {{ $hasFvFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="bp-fv-filter-body" class="{{ $hasFvFilters ? '' : 'hidden' }} px-5 pb-5 pt-4 bg-indigo-50/20 rounded-b-2xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">

                    {{-- حالة الجولة --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة الجولة</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                <input type="checkbox" name="field_visit_status_id[]" value="none" {{ in_array('none', $fieldVisitStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-gray-300"></span>
                                بدون جولة ميدانية
                            </label>
                            @forelse($fieldVisitStatuses as $fvs)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="field_visit_status_id[]" value="{{ $fvs->id }}" {{ in_array($fvs->id, $fieldVisitStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $fvs->color }}"></span>
                                    {{ $fvs->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا توجد حالات</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- نوع البيت --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">نوع البيت</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($houseTypes as $ht)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_house_type_id[]" value="{{ $ht->id }}" {{ in_array($ht->id, $fvHouseTypeIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $ht->color }}"></span>
                                    {{ $ht->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا توجد أنواع</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- الزائر --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">الزائر</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($fvVisitorList as $vis)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700 ms-option">
                                    <input type="checkbox" name="fv_visitors[]" value="{{ $vis }}" {{ in_array($vis, $fvVisitors) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    {{ $vis }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا يوجد زوار مسجّلون</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- من أضاف الجولة --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">من أضاف الجولة</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($fvCreatedByList as $u)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_created_by[]" value="{{ $u->id }}" {{ in_array($u->id, $fvCreatedByIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    {{ $u->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا يوجد بيانات بعد</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- تاريخ الزيارة --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">تاريخ الزيارة</label>
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="fv_date_from" value="{{ $fvDateFrom }}"
                                   class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                            <span class="text-xs text-indigo-400 shrink-0">—</span>
                            <input type="date" name="fv_date_to" value="{{ $fvDateTo }}"
                                   class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                        </div>
                    </div>

                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    {{-- مبلغ الجولة --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">مبلغ الجولة (ل.س)</label>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="fv_amount_from" value="{{ $fvAmountFrom }}" placeholder="من" min="0"
                                   class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                            <span class="text-xs text-indigo-400 shrink-0">—</span>
                            <input type="number" name="fv_amount_to" value="{{ $fvAmountTo }}" placeholder="إلى" min="0"
                                   class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                        </div>
                    </div>

                    {{-- حالة البيت --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة البيت</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($houseConditions as $hc)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_house_condition_id[]" value="{{ $hc->id }}" {{ in_array($hc->id, $fvHouseConditionIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hc->color }}"></span>
                                    {{ $hc->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- الملاحظات --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">الملاحظات</label>
                        <input type="text" name="fv_notes" value="{{ $fvNotes }}" placeholder="بحث في الملاحظات..."
                               class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                    </div>

                    {{-- يوجد فيديو --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">يوجد فيديو</label>
                        <select name="fv_has_video" onwheel="this.blur()"
                                class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $fvHasVideo === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $fvHasVideo === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    {{-- حالة خاصة --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة خاصة</label>
                        <select name="fv_has_special_case" onwheel="this.blur()"
                                class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $fvHasSpecialCase === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $fvHasSpecialCase === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    {{-- عدد الجولات --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">عدد الجولات</label>
                        <select name="fv_count" onwheel="this.blur()"
                                class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="0" {{ $fvCount === '0' ? 'selected' : '' }}>بدون جولات</option>
                            <option value="1" {{ $fvCount === '1' ? 'selected' : '' }}>جولة واحدة فأكثر</option>
                            <option value="2" {{ $fvCount === '2' ? 'selected' : '' }}>جولتان فأكثر</option>
                            <option value="3" {{ $fvCount === '3' ? 'selected' : '' }}>3 جولات فأكثر</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- Payment-specific filters --}}
        <div class="border border-emerald-100 rounded-2xl mb-4">
            <button type="button" onclick="toggleBpPayFilters()"
                    class="w-full flex items-center justify-between gap-3 px-5 py-3 bg-emerald-50/60 hover:bg-emerald-50 transition-colors text-right rounded-t-2xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-emerald-700">فلاتر خاصة بالدفعات</span>
                    @if($paymentActiveCount > 0)
                        <span class="text-xs bg-emerald-600 text-white rounded-full px-2 py-0.5 font-bold">{{ $paymentActiveCount }} فعّال</span>
                    @endif
                </div>
                <svg id="bp-pay-filter-arrow" class="w-4 h-4 text-emerald-400 transition-transform duration-200 {{ $paymentActiveCount > 0 ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="bp-pay-filter-body" class="{{ $paymentActiveCount > 0 ? '' : 'hidden' }} px-5 pb-5 pt-4 bg-emerald-50/20 rounded-b-2xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-3">

                    {{-- IBAN --}}
                    <div>
                        <label class="block text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1.5">يوجد IBAN</label>
                        <select name="has_iban" onwheel="this.blur()"
                                class="w-full text-sm border border-emerald-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $hasIban === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $hasIban === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    {{-- Barcode --}}
                    <div>
                        <label class="block text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1.5">يوجد باركود</label>
                        <select name="has_barcode" onwheel="this.blur()"
                                class="w-full text-sm border border-emerald-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $hasBarcode === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $hasBarcode === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    {{-- Payment review status --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1.5">حالة مراجعة الدفع</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-emerald-200 rounded-xl px-3 py-2.5 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-emerald-100 rounded-xl shadow-lg py-1">
                            @foreach(['none' => 'بدون مراجعة', 'pending' => 'قيد المراجعة', 'match' => 'متطابق', 'mismatch' => 'غير متطابق'] as $val => $lbl)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-emerald-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="payment_review_status[]" value="{{ $val }}" {{ in_array($val, $paymentReviewStatus) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                                    <span class="inline-block w-2 h-2 rounded-full shrink-0 @if($val==='match') bg-green-500 @elseif($val==='mismatch') bg-red-500 @elseif($val==='pending') bg-amber-500 @else bg-gray-300 @endif"></span>
                                    {{ $lbl }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    {{-- Last batch date range --}}
                    <div>
                        <label class="block text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1.5">تاريخ الدفعة (من — إلى)</label>
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="last_batch_date_from" value="{{ $lastBatchDateFrom }}"
                                   class="flex-1 min-w-0 text-sm border border-emerald-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition">
                            <span class="text-xs text-emerald-400 shrink-0">—</span>
                            <input type="date" name="last_batch_date_to" value="{{ $lastBatchDateTo }}"
                                   class="flex-1 min-w-0 text-sm border border-emerald-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition">
                        </div>
                        <p class="text-[10px] text-emerald-400/80 mt-1">يعرض الأعضاء الذين وُجدوا في دفعة ضمن هذا النطاق</p>
                    </div>

                    {{-- Specific batch --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-emerald-600 uppercase tracking-wide mb-1.5">دفعة محددة</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-emerald-200 rounded-xl px-3 py-2.5 bg-white hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-emerald-100 rounded-xl shadow-lg overflow-hidden" style="max-height:280px">
                            <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                                <input type="text" class="ms-search w-full text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="بحث في الدفعات...">
                            </div>
                            <div class="overflow-y-auto" style="max-height:220px">
                            @forelse($batchList as $batch)
                                <label class="ms-option flex items-start gap-2.5 px-3 py-2.5 hover:bg-emerald-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="batch_id[]" value="{{ $batch->id }}" {{ in_array($batch->id, $batchIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400 mt-0.5 shrink-0">
                                    <span class="min-w-0">
                                        <span class="block font-medium leading-tight truncate">{{ $batch->label ?: '—' }}</span>
                                        <span class="block text-xs text-gray-400">{{ $batch->payment_date?->format('Y-m-d') }} · {{ $batch->operationLabel }} {{ $batch->amount }}</span>
                                    </span>
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا توجد دفعات مسجلة</p>
                            @endforelse
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Actions row --}}
        <div class="flex items-center gap-2 flex-wrap">
            <button type="submit" class="flex items-center gap-2 bg-gradient-to-l from-teal-600 to-cyan-500 hover:from-teal-700 hover:to-cyan-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                تطبيق الفلاتر
            </button>
            @if($qs)
                <a href="{{ route('members.bulk-payments') }}" class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
            @endif
            <span class="text-sm text-gray-500 ms-auto">
                <span class="font-bold text-teal-700">{{ $fmt($totalCount) }}</span> عضو في النتائج الحالية
            </span>
        </div>

        </div>{{-- /p-5 --}}
        </div>{{-- /bp-filter-body --}}
    </form>
</div>

{{-- ===== BULK ACTION FORM (POST) ===== --}}
<form method="POST"
      action="{{ route('members.bulk-payments.apply') }}{{ $qs ? '?' . $qs : '' }}"
      id="bulk-form">
    @csrf

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-72 sm:mb-52 lg:mb-36">

        {{-- Table header bar --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gradient-to-l from-teal-50 to-white">
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="select-all-cb"
                           class="w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-400 cursor-pointer">
                    <span class="text-sm font-bold text-gray-700">تحديد الكل</span>
                </label>
                <span id="selected-badge" class="hidden bg-teal-100 text-teal-700 text-xs font-bold rounded-full px-2.5 py-1">0 محدد</span>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-3 py-1">
                {{ $fmt($members->total()) }} عضو
            </span>
        </div>

        @if($members->isEmpty())
            <div class="text-center py-20 text-gray-400 text-sm">لا توجد نتائج مطابقة. جرب تعديل الفلاتر.</div>
        @else

            {{-- ── Mobile cards (xs → sm) ── --}}
            <div class="block sm:hidden divide-y divide-gray-100">
                @foreach($members as $member)
                <div class="flex items-start gap-3 px-4 py-3.5 active:bg-teal-50/40 transition-colors cursor-pointer"
                     onclick="toggleMobileCheck(this)">
                    <input type="checkbox"
                           class="member-cb member-cb-mobile mt-0.5 w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-400 cursor-pointer shrink-0"
                           value="{{ $member->id }}"
                           onclick="event.stopPropagation()">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <a href="{{ route('members.show', $member) }}"
                                   onclick="event.stopPropagation()"
                                   class="font-bold text-gray-800 text-sm leading-tight truncate hover:text-teal-700 hover:underline block">{{ $member->full_name }}</a>
                                <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $member->dossier_number ?? '—' }}</p>
                            </div>
                            <div class="shrink-0">
                                @if($member->payments_count !== null)
                                    <span class="inline-flex items-center gap-1 text-sm font-black text-teal-700 bg-teal-50 border border-teal-100 rounded-lg px-2.5 py-1">
                                        {{ $member->payments_count }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-300 italic">بدون</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @if($member->verificationStatus)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border"
                                      style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}40">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $member->verificationStatus->color }}"></span>
                                    {{ $member->verificationStatus->name }}
                                </span>
                            @endif
                            @if($member->finalStatus)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border"
                                      style="background:{{ $member->finalStatus->color }}18; color:{{ $member->finalStatus->color }}; border-color:{{ $member->finalStatus->color }}40">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $member->finalStatus->color }}"></span>
                                    {{ $member->finalStatus->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ── Desktop table (sm+) ── --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100">
                            <th class="px-4 py-3.5 w-10"></th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">رقم الملف</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الاسم</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">حالة التحقق</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الحالة النهائية</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5 hidden md:table-cell">الجمعية</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5 hidden md:table-cell">المنطقة</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">عدد الدفعات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($members as $member)
                        <tr class="hover:bg-teal-50/30 transition-colors group cursor-pointer member-row-desktop" onclick="toggleDesktopCheck(this)">
                            <td class="px-4 py-3.5" onclick="event.stopPropagation()">
                                <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                                       class="member-cb member-cb-desktop w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-400 cursor-pointer">
                            </td>
                            <td class="px-4 py-3.5 font-mono font-semibold text-gray-700 text-sm">{{ $member->dossier_number ?? '—' }}</td>
                            <td class="px-4 py-3.5" onclick="event.stopPropagation()">
                                <a href="{{ route('members.show', $member) }}"
                                   class="font-bold text-gray-800 hover:text-teal-700 hover:underline">{{ $member->full_name }}</a>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($member->verificationStatus)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border"
                                          style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}40">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $member->verificationStatus->color }}"></span>
                                        {{ $member->verificationStatus->name }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($member->finalStatus)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border"
                                          style="background:{{ $member->finalStatus->color }}18; color:{{ $member->finalStatus->color }}; border-color:{{ $member->finalStatus->color }}40">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $member->finalStatus->color }}"></span>
                                        {{ $member->finalStatus->name }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-gray-500 text-sm hidden md:table-cell">{{ $member->association?->name ?? '—' }}</td>
                            <td class="px-4 py-3.5 text-gray-500 text-sm hidden md:table-cell">{{ $member->region?->name ?? '—' }}</td>
                            <td class="px-4 py-3.5">
                                @if($member->payments_count !== null)
                                    <span class="inline-flex items-center gap-1 text-sm font-black text-teal-700 bg-teal-50 border border-teal-100 rounded-lg px-2.5 py-1">
                                        {{ $member->payments_count }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-300 italic">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($members->hasPages())
            <div class="px-5 py-3.5 border-t border-gray-100">
                {{ $members->withQueryString()->links() }}
            </div>
            @endif
        @endif

    </div>

    {{-- ===== STICKY ACTION BAR ===== --}}
    <div id="action-bar"
         class="fixed bottom-16 lg:bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200">

        <input type="hidden" name="apply_to" id="apply-to-input" value="selected">

        {{-- Mobile layout (< sm) --}}
        <div class="sm:hidden">
            {{-- Row 1: operation toggle + count + badge --}}
            <div class="flex items-center gap-2 px-3 pt-2.5 pb-2 border-b border-gray-100">
                {{-- Visual-only toggles — no name attr; JS syncs to desktop radios --}}
                <div class="flex items-center gap-0.5 bg-gray-100 rounded-xl p-0.5 shrink-0" id="mobile-op-toggle">
                    <button type="button" data-op="add"
                            class="mobile-op-btn active flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition-all bg-white shadow-sm text-teal-700 text-xs font-semibold"
                            onclick="setMobileOp('add')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        إضافة
                    </button>
                    <button type="button" data-op="subtract"
                            class="mobile-op-btn flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition-all text-gray-500 text-xs font-semibold"
                            onclick="setMobileOp('subtract')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                        طرح
                    </button>
                    <button type="button" data-op="set"
                            class="mobile-op-btn flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg transition-all text-gray-500 text-xs font-semibold"
                            onclick="setMobileOp('set')">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        تعيين
                    </button>
                </div>
                {{-- No name attr — JS mirrors value to #payments-count-input before submit --}}
                <input type="number" min="0" step="1" value="1"
                       class="w-16 text-center text-base font-black border-2 border-teal-300 rounded-xl px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-teal-400 transition"
                       id="payments-count-input-mobile">
                <span class="text-xs text-gray-400 me-auto">دفعة</span>
                <span id="ab-count-mobile-wrap" class="hidden">
                    <span class="text-xs font-bold text-teal-700 bg-teal-50 border border-teal-100 rounded-full px-2.5 py-1">
                        <span id="ab-count-mobile">0</span> محدد
                    </span>
                </span>
            </div>
            {{-- Row 1.5: batch label + date + notes --}}
            <div class="flex flex-col gap-2 px-3 py-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <input type="text" name="batch_label" placeholder="اسم الدفعة"
                           class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
                    <input type="text" id="bp-date-mobile" inputmode="numeric" maxlength="10"
                           value="{{ now()->format('d.m.Y') }}" placeholder="يي.شش.سسسس"
                           oninput="bpFormatDate(this,'bp-date-mobile-h')"
                           class="w-32 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition" dir="ltr">
                    <input type="hidden" name="payment_date" id="bp-date-mobile-h" value="{{ now()->toDateString() }}">
                </div>
                <input type="text" name="batch_notes" placeholder="ملاحظات (اختياري)"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
            </div>

            {{-- Row 2: action buttons --}}
            <div class="flex items-center gap-2 px-3 py-2.5">
                <button type="button" onclick="applyToSelected()"
                        class="flex-1 flex items-center justify-center gap-1.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-3 py-2.5 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>المحدّدين</span>
                    <span id="selected-count-label-mobile" class="bg-white/25 rounded-full px-1.5 py-0.5 text-xs">0</span>
                </button>
                <button type="button" onclick="applyToFiltered()"
                        class="flex-1 flex items-center justify-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-3 py-2.5 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>الكل</span>
                    <span class="bg-white/25 rounded-full px-1.5 py-0.5 text-xs">{{ $fmt($totalCount) }}</span>
                </button>
            </div>
        </div>

        {{-- Desktop layout (≥ sm) --}}
        <div class="hidden sm:block">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center gap-4 px-6 py-4">

                {{-- Operation mode --}}
                <div class="flex items-center gap-1.5 bg-gray-100 rounded-xl p-1">
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition-all has-[:checked]:bg-white has-[:checked]:shadow-sm has-[:checked]:text-teal-700 text-gray-500 text-sm font-semibold">
                        <input type="radio" name="operation" value="add" checked class="hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        إضافة
                    </label>
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition-all has-[:checked]:bg-white has-[:checked]:shadow-sm has-[:checked]:text-red-600 text-gray-500 text-sm font-semibold">
                        <input type="radio" name="operation" value="subtract" class="hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                        طرح
                    </label>
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition-all has-[:checked]:bg-white has-[:checked]:shadow-sm has-[:checked]:text-teal-700 text-gray-500 text-sm font-semibold">
                        <input type="radio" name="operation" value="set" class="hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        تعيين
                    </label>
                </div>

                {{-- Payment count input --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-gray-600 shrink-0">عدد الدفعات</label>
                    <input type="number" name="payments_count" min="0" step="1" value="1"
                           class="w-24 text-center text-lg font-black border-2 border-teal-300 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-teal-400 transition"
                           id="payments-count-input">
                </div>

                {{-- Batch label --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-gray-600 shrink-0">اسم الدفعة</label>
                    <input type="text" name="batch_label" placeholder="مثال: دفعة يونيو 2026"
                           class="w-44 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
                </div>

                {{-- Batch date --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-gray-600 shrink-0">تاريخ الدفعة</label>
                    <input type="text" id="bp-date-desktop" inputmode="numeric" maxlength="10"
                           value="{{ now()->format('d.m.Y') }}" placeholder="يي.شش.سسسس"
                           oninput="bpFormatDate(this,'bp-date-desktop-h')"
                           class="w-32 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition" dir="ltr">
                    <input type="hidden" name="payment_date" id="bp-date-desktop-h" value="{{ now()->toDateString() }}">
                </div>

                {{-- Batch notes --}}
                <div class="flex items-center gap-2">
                    <label class="text-sm font-bold text-gray-600 shrink-0">ملاحظات</label>
                    <input type="text" name="batch_notes" placeholder="اختياري"
                           class="w-40 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
                </div>

                {{-- Apply to --}}
                <div class="flex items-center gap-2">
                    <button type="button" onclick="applyToSelected()"
                            class="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        تطبيق على المحدّدين
                        <span id="selected-count-label" class="bg-white/20 rounded-full px-2 py-0.5 text-xs">0</span>
                    </button>
                    <button type="button" onclick="applyToFiltered()"
                            class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        تطبيق على الكل المفلتر
                        <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs">{{ $fmt($totalCount) }}</span>
                    </button>
                </div>

                {{-- Selected badge --}}
                <span id="action-bar-badge" class="text-sm text-gray-400 ms-auto hidden">
                    <span id="ab-count" class="font-bold text-teal-700">0</span> محدد
                </span>

            </div>
        </div>

    </div>

</form>

@push('scripts')
<script>
// ── Filter toggle ──────────────────────────────────────────────────────────
function toggleBpFilters() {
    const body  = document.getElementById('bp-filter-body');
    const arrow = document.getElementById('bp-filter-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function toggleBpFvFilters() {
    const body  = document.getElementById('bp-fv-filter-body');
    const arrow = document.getElementById('bp-fv-filter-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function toggleBpPayFilters() {
    const body  = document.getElementById('bp-pay-filter-body');
    const arrow = document.getElementById('bp-pay-filter-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

// ── Remove empty params before GET submit ─────────────────────────────────
function removeBpEmptyFilters(form) {
    form.querySelectorAll('input[type=checkbox]:not(:checked)').forEach(cb => cb.disabled = true);
    form.querySelectorAll('input[type=text], input[type=date], input[type=number]').forEach(el => {
        if (el.value === '') el.disabled = true;
    });
    form.querySelectorAll('select').forEach(el => {
        if (el.value === '') el.disabled = true;
    });
}

// ── Multi-select dropdown JS ───────────────────────────────────────────────
document.querySelectorAll('.ms-dropdown').forEach(dd => {
    const btn   = dd.querySelector('.ms-btn');
    const panel = dd.querySelector('.ms-panel');
    const label = dd.querySelector('.ms-label');
    const arrow = dd.querySelector('.ms-arrow');
    const search = dd.querySelector('.ms-search');

    btn.addEventListener('click', e => {
        e.stopPropagation();
        document.querySelectorAll('.ms-panel').forEach(p => { if (p !== panel) p.classList.add('hidden'); });
        document.querySelectorAll('.ms-arrow').forEach(a => { if (a !== arrow) a.classList.remove('rotate-180'); });
        panel.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    });

    if (search) {
        search.addEventListener('input', () => {
            const q = search.value.toLowerCase();
            dd.querySelectorAll('.ms-option').forEach(opt => {
                opt.style.display = opt.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    const updateLabel = () => {
        const checked = dd.querySelectorAll('.ms-check:checked');
        label.textContent = checked.length ? Array.from(checked).map(c => c.closest('label').textContent.trim()).join('، ') : '— الكل —';
        label.classList.toggle('text-teal-700', checked.length > 0);
        label.classList.toggle('text-gray-500', checked.length === 0);
    };
    dd.querySelectorAll('.ms-check').forEach(cb => cb.addEventListener('change', updateLabel));
    updateLabel();
});
document.addEventListener('click', () => {
    document.querySelectorAll('.ms-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.ms-arrow').forEach(a => a.classList.remove('rotate-180'));
});

// ── Checkbox selection (mobile + desktop synced) ───────────────────────────
const selectAllCb = document.getElementById('select-all-cb');

selectAllCb?.addEventListener('change', () => {
    const isMobile = window.innerWidth < 640;
    if (isMobile) {
        document.querySelectorAll('.member-cb-mobile').forEach(cb => cb.checked = selectAllCb.checked);
        // sync desktop
        document.querySelectorAll('.member-cb-mobile').forEach(cb => {
            const d = document.querySelector('.member-cb-desktop[value="' + cb.value + '"]');
            if (d) d.checked = cb.checked;
        });
    } else {
        document.querySelectorAll('.member-cb-desktop').forEach(cb => cb.checked = selectAllCb.checked);
        // sync mobile
        document.querySelectorAll('.member-cb-desktop').forEach(cb => {
            const m = document.querySelector('.member-cb-mobile[value="' + cb.value + '"]');
            if (m) m.checked = cb.checked;
        });
    }
    updateBadge();
});

function toggleMobileCheck(row) {
    const cb = row.querySelector('.member-cb-mobile');
    cb.checked = !cb.checked;
    const desktop = document.querySelector('.member-cb-desktop[value="' + cb.value + '"]');
    if (desktop) desktop.checked = cb.checked;
    updateBadge();
}

function toggleDesktopCheck(row) {
    const cb = row.querySelector('.member-cb-desktop');
    cb.checked = !cb.checked;
    const mobile = document.querySelector('.member-cb-mobile[value="' + cb.value + '"]');
    if (mobile) mobile.checked = cb.checked;
    updateBadge();
}

document.querySelectorAll('.member-cb-mobile').forEach(cb => cb.addEventListener('change', () => {
    const d = document.querySelector('.member-cb-desktop[value="' + cb.value + '"]');
    if (d) d.checked = cb.checked;
    updateBadge();
}));
document.querySelectorAll('.member-cb-desktop').forEach(cb => cb.addEventListener('change', () => {
    const m = document.querySelector('.member-cb-mobile[value="' + cb.value + '"]');
    if (m) m.checked = cb.checked;
    updateBadge();
}));

function updateBadge() {
    // Always count from desktop checkboxes (they carry the form names)
    const count = document.querySelectorAll('.member-cb-desktop:checked').length;
    const allCount = document.querySelectorAll('.member-cb-desktop').length;

    // Header badge
    const badge = document.getElementById('selected-badge');
    if (badge) { badge.textContent = count + ' محدد'; badge.classList.toggle('hidden', count === 0); }

    // Desktop action bar
    const scl = document.getElementById('selected-count-label');
    if (scl) scl.textContent = count;
    const ab = document.getElementById('action-bar-badge');
    const abCount = document.getElementById('ab-count');
    if (ab && abCount) { abCount.textContent = count; ab.classList.toggle('hidden', count === 0); }

    // Mobile action bar
    const sclM = document.getElementById('selected-count-label-mobile');
    if (sclM) sclM.textContent = count;
    const abcMWrap = document.getElementById('ab-count-mobile-wrap');
    const abcM = document.getElementById('ab-count-mobile');
    if (abcMWrap && abcM) { abcM.textContent = count; abcMWrap.classList.toggle('hidden', count === 0); }

    // Select-all state
    if (selectAllCb) {
        selectAllCb.checked = allCount > 0 && count === allCount;
        selectAllCb.indeterminate = count > 0 && count < allCount;
    }
}

// ── Sync mobile payments_count input to desktop hidden field ───────────────
const mobileCountInput = document.getElementById('payments-count-input-mobile');
const desktopCountInput = document.getElementById('payments-count-input');
if (mobileCountInput && desktopCountInput) {
    mobileCountInput.addEventListener('input', () => { desktopCountInput.value = mobileCountInput.value; });
    desktopCountInput.addEventListener('input', () => { mobileCountInput.value = desktopCountInput.value; });
}

// ── Mobile operation buttons (visual only — syncs to desktop radio) ────────
function setMobileOp(val) {
    document.querySelectorAll('.mobile-op-btn').forEach(btn => {
        const active = btn.dataset.op === val;
        btn.classList.toggle('bg-white', active);
        btn.classList.toggle('shadow-sm', active);
        btn.classList.toggle('text-gray-500', !active);
        btn.classList.toggle('text-teal-700', active && val !== 'subtract');
        btn.classList.toggle('text-red-600',  active && val === 'subtract');
        if (!active) { btn.classList.remove('text-teal-700', 'text-red-600'); }
    });
    // Sync to desktop radio
    const desktopRadio = document.querySelector('.hidden.sm\\:block input[name="operation"][value="' + val + '"]');
    if (desktopRadio) desktopRadio.checked = true;
}

// ── Desktop radio visual update ────────────────────────────────────────────
document.querySelectorAll('input[name="operation"]').forEach(r => {
    r.addEventListener('change', () => {
        document.querySelectorAll('input[name="operation"]').forEach(rb => {
            rb.closest('label').classList.toggle('bg-white', rb.checked);
            rb.closest('label').classList.toggle('shadow-sm', rb.checked);
            rb.closest('label').classList.toggle('text-teal-700', rb.checked);
            rb.closest('label').classList.toggle('text-gray-500', !rb.checked);
        });
    });
});

// ── Apply actions ──────────────────────────────────────────────────────────
function applyToSelected() {
    const count = document.querySelectorAll('.member-cb-desktop:checked').length;
    if (count === 0) { alert('يرجى تحديد أعضاء أولاً.'); return; }
    document.getElementById('apply-to-input').value = 'selected';
    // Sync mobile count input to desktop before submit
    if (mobileCountInput && desktopCountInput && window.innerWidth < 640) {
        desktopCountInput.value = mobileCountInput.value;
    }
    document.getElementById('bulk-form').submit();
}

function applyToFiltered() {
    if (!confirm('سيتم تطبيق العملية على جميع الأعضاء المفلترين. هل أنت متأكد؟')) return;
    document.getElementById('apply-to-input').value = 'filtered';
    if (mobileCountInput && desktopCountInput && window.innerWidth < 640) {
        desktopCountInput.value = mobileCountInput.value;
    }
    document.getElementById('bulk-form').submit();
}

function bpFormatDate(el, hiddenId) {
    let v = el.value.replace(/\D/g, '');
    if (v.length > 2) v = v.slice(0,2) + '.' + v.slice(2);
    if (v.length > 5) v = v.slice(0,5) + '.' + v.slice(5);
    el.value = v.slice(0, 10);
    const hidden = document.getElementById(hiddenId);
    const parts  = el.value.split('.');
    hidden.value = (parts.length === 3 && parts[2].length === 4)
        ? parts[2] + '-' + parts[1] + '-' + parts[0]
        : '';
}
</script>
@endpush

@endsection
