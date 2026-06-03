@extends('layouts.app')

@section('title', 'رفع وتخفيض المبلغ — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="text-emerald-600 hover:underline">الأعضاء</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700">رفع وتخفيض المبلغ</span>
@endsection

@section('content')

@php
    $qs  = request()->getQueryString();
    $fmt = fn($n) => number_format((float)$n, 0, '.', ',');

    $fvActiveCount = (int)!empty($fieldVisitStatusIds) + (int)!empty($fvHouseTypeIds) + (int)!empty($fvHouseConditionIds)
        + (!empty($fvVisitors) ? 1 : 0) + (!empty($fvCreatedByIds) ? 1 : 0)
        + ($fvDateFrom !== '' || $fvDateTo !== '' ? 1 : 0)
        + ($fvAmountFrom !== '' || $fvAmountTo !== '' ? 1 : 0)
        + ($fvNotes !== '' ? 1 : 0) + ($fvHasVideo !== '' ? 1 : 0) + ($fvHasSpecialCase !== '' ? 1 : 0)
        + ($fvCount !== '' ? 1 : 0);

    $mainActiveCount =
        ($dossierFrom !== '' || $dossierTo !== '' ? 1 : 0)
        + ($estimatedFrom !== '' || $estimatedTo !== '' ? 1 : 0)
        + (!empty($verificationIds)  ? 1 : 0) + (!empty($finalStatusIds) ? 1 : 0)
        + (!empty($associationIds)   ? 1 : 0) + (!empty($regionIds)      ? 1 : 0)
        + (!empty($sectorIds)        ? 1 : 0) + (!empty($delegates)      ? 1 : 0)
        + (!empty($shamCash)         ? 1 : 0)
        + ($fvReductionApplied !== '' ? 1 : 0);
    $hasMainFilters = $mainActiveCount > 0 || $fvActiveCount > 0;
@endphp

<style>#action-bar { box-shadow: 0 -4px 24px rgba(0,0,0,0.08); }</style>

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-orange-600 via-amber-500 to-yellow-400 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-black text-white">رفع وتخفيض المبلغ</h1>
            <p class="text-amber-100 text-sm mt-0.5">تطبيق رفع أو تخفيض نسبي على المبلغ المقدّر للأعضاء عبر الجولة الميدانية</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-white font-black text-2xl leading-none">{{ $fmt($totalCount) }}</p>
                <p class="text-amber-200 text-xs mt-0.5">إجمالي النتائج</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-white font-black text-2xl leading-none">{{ $fmt($withAmount) }}</p>
                <p class="text-amber-200 text-xs mt-0.5">لديهم مبلغ</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[120px]">
                <p class="text-white font-black text-xl leading-none">{{ $fmt($totalAmount) }}</p>
                <p class="text-amber-200 text-xs mt-0.5">مجموع المبالغ (ل.س)</p>
            </div>
        </div>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
    <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    {{ session('error') }}
</div>
@endif
@if(session('pending'))
<div class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('pending') }}
</div>
@endif

{{-- ===== FILTER FORM ===== --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <form method="GET" action="{{ route('members.fv-reduction') }}" id="fvr-filter-form"
          onsubmit="removeFvrEmptyFilters(this)">

        <div class="px-5 pt-4 pb-3 border-b border-gray-100 space-y-2.5">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="بحث بالاسم، رقم الهوية، الهاتف..."
                           class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition placeholder-gray-300">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                    بحث
                </button>
                <button type="button" onclick="toggleFvrFilters()"
                        class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-amber-300 transition-colors text-sm font-bold text-gray-600">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    الفلاتر
                    @if($mainActiveCount > 0)
                        <span class="text-xs bg-amber-500 text-white rounded-full px-1.5 py-0.5 font-black">{{ $mainActiveCount }}</span>
                    @endif
                    <svg id="fvr-filter-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200 {{ $hasMainFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>
        </div>

        <div id="fvr-filter-body" class="{{ $hasMainFilters ? '' : 'hidden' }}">
        <div class="p-5">

        {{-- Dossier + amount range --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">رقم الاضبارة من</label>
                    <input type="text" name="dossier_from" value="{{ $dossierFrom }}" placeholder="100"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="text" name="dossier_to" value="{{ $dossierTo }}" placeholder="200"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
            </div>
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">المبلغ المقدر من</label>
                    <input type="number" name="estimated_from" value="{{ $estimatedFrom }}" min="0" placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition placeholder-gray-300">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="estimated_to" value="{{ $estimatedTo }}" min="0" placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition placeholder-gray-300">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">ل.س</span>
            </div>
        </div>

        {{-- FV Reduction applied + Sham Cash --}}
        <div class="flex flex-wrap gap-4 mb-3">
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">حالة الانقاص</label>
                <select name="fv_reduction_applied"
                        class="w-full sm:w-52 text-sm border {{ $fvReductionApplied !== '' ? 'border-amber-400 bg-amber-50 font-semibold text-amber-700' : 'border-gray-200 bg-gray-50 text-gray-500' }} rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition">
                    <option value="">— الكل —</option>
                    <option value="yes" {{ $fvReductionApplied === 'yes' ? 'selected' : '' }}>تم الانقاص</option>
                    <option value="no"  {{ $fvReductionApplied === 'no'  ? 'selected' : '' }}>لم يتم الانقاص</option>
                </select>
            </div>
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">شام كاش</label>
                <button type="button" class="ms-btn w-full sm:w-52 flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    @foreach(['done' => 'تم', 'manual' => 'يدوي', 'none' => 'لا يوجد'] as $val => $lbl)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="sham_cash[]" value="{{ $val }}" {{ in_array($val, (array)$shamCash) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                            {{ $lbl }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Status + association + region + sector + delegate --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">حالة التحقق</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @foreach($verificationStatuses as $vs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="verification_status_id[]" value="{{ $vs->id }}" {{ in_array($vs->id, $verificationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $vs->color }}"></span>
                            {{ $vs->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة النهائية</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @foreach($finalStatusList as $fs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}" {{ in_array($fs->id, $finalStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $fs->color }}"></span>
                            {{ $fs->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجمعية</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($associationList as $assoc)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="association_id[]" value="{{ $assoc->id }}" {{ in_array($assoc->id, $associationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                            {{ $assoc->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد جمعيات</p>
                    @endforelse
                </div>
            </div>
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المندوب</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-amber-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($delegateList as $d)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="delegate[]" value="{{ $d }}" {{ in_array($d, $delegates) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                            {{ $d }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا يوجد مندوبون</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Field visit filters --}}
        <div class="border border-orange-100 rounded-2xl mb-4">
            <button type="button" onclick="toggleFvrFvFilters()"
                    class="w-full flex items-center justify-between gap-3 px-5 py-3 bg-orange-50/60 hover:bg-orange-50 transition-colors text-right rounded-t-2xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-orange-700">فلاتر الجولة الميدانية</span>
                    @if($fvActiveCount > 0)
                        <span class="text-xs bg-orange-600 text-white rounded-full px-2 py-0.5 font-bold">{{ $fvActiveCount }} فعّال</span>
                    @endif
                </div>
                <svg id="fvr-fv-arrow" class="w-4 h-4 text-orange-400 transition-transform duration-200 {{ $hasFvFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="fvr-fv-body" class="{{ $hasFvFilters ? '' : 'hidden' }} px-5 pb-5 pt-4 bg-orange-50/20 rounded-b-2xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">

                    {{-- حالة الجولة --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-orange-600 uppercase tracking-wide mb-1.5">حالة الجولة</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-orange-200 rounded-xl px-3 py-2.5 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-orange-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-orange-50 cursor-pointer text-sm text-gray-700">
                                <input type="checkbox" name="field_visit_status_id[]" value="none" {{ in_array('none', $fieldVisitStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-orange-600 focus:ring-orange-400">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-gray-300"></span>
                                بدون جولة ميدانية
                            </label>
                            @forelse($fieldVisitStatuses as $fvs)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-orange-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="field_visit_status_id[]" value="{{ $fvs->id }}" {{ in_array($fvs->id, $fieldVisitStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-orange-600 focus:ring-orange-400">
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
                        <label class="block text-xs font-bold text-orange-600 uppercase tracking-wide mb-1.5">نوع البيت</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-orange-200 rounded-xl px-3 py-2.5 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-orange-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($houseTypes as $ht)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-orange-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_house_type_id[]" value="{{ $ht->id }}" {{ in_array($ht->id, $fvHouseTypeIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-orange-600 focus:ring-orange-400">
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
                        <label class="block text-xs font-bold text-orange-600 uppercase tracking-wide mb-1.5">الزائر</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-orange-200 rounded-xl px-3 py-2.5 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-orange-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($fvVisitorList as $vis)
                                <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-orange-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_visitors[]" value="{{ $vis }}" {{ in_array($vis, $fvVisitors) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-orange-600 focus:ring-orange-400">
                                    {{ $vis }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا يوجد زوار</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- من أضاف الجولة --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-orange-600 uppercase tracking-wide mb-1.5">من أضاف الجولة</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-orange-200 rounded-xl px-3 py-2.5 bg-white hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-orange-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($fvCreatedByList as $u)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-orange-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_created_by[]" value="{{ $u->id }}" {{ in_array($u->id, $fvCreatedByIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-orange-600 focus:ring-orange-400">
                                    {{ $u->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا يوجد بيانات</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- تاريخ الزيارة --}}
                    <div>
                        <label class="block text-xs font-bold text-orange-600 uppercase tracking-wide mb-1.5">تاريخ الزيارة</label>
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="fv_date_from" value="{{ $fvDateFrom }}"
                                   class="flex-1 min-w-0 text-sm border border-orange-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
                            <span class="text-xs text-orange-400 shrink-0">—</span>
                            <input type="date" name="fv_date_to" value="{{ $fvDateTo }}"
                                   class="flex-1 min-w-0 text-sm border border-orange-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
                        </div>
                    </div>

                    {{-- عدد الجولات --}}
                    <div>
                        <label class="block text-xs font-bold text-orange-600 uppercase tracking-wide mb-1.5">عدد الجولات</label>
                        <select name="fv_count" onwheel="this.blur()"
                                class="w-full text-sm border border-orange-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="0" {{ $fvCount === '0' ? 'selected' : '' }}>بدون جولات</option>
                            <option value="1" {{ $fvCount === '1' ? 'selected' : '' }}>جولة واحدة فأكثر</option>
                            <option value="2" {{ $fvCount === '2' ? 'selected' : '' }}>جولتان فأكثر</option>
                            <option value="3" {{ $fvCount === '3' ? 'selected' : '' }}>3 جولات فأكثر</option>
                        </select>
                    </div>

                    {{-- يوجد فيديو --}}
                    <div>
                        <label class="block text-xs font-bold text-orange-600 uppercase tracking-wide mb-1.5">يوجد فيديو</label>
                        <select name="fv_has_video" onwheel="this.blur()"
                                class="w-full text-sm border border-orange-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $fvHasVideo === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $fvHasVideo === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    {{-- حالة خاصة --}}
                    <div>
                        <label class="block text-xs font-bold text-orange-600 uppercase tracking-wide mb-1.5">حالة خاصة</label>
                        <select name="fv_has_special_case" onwheel="this.blur()"
                                class="w-full text-sm border border-orange-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $fvHasSpecialCase === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $fvHasSpecialCase === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2 flex-wrap">
            <button type="submit" class="flex items-center gap-2 bg-gradient-to-l from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                تطبيق الفلاتر
            </button>
            @if($qs)
                <a href="{{ route('members.fv-reduction') }}" class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    مسح الفلاتر
                </a>
            @endif
            <span class="text-sm text-gray-500 ms-auto">
                <span class="font-bold text-amber-600">{{ $fmt($totalCount) }}</span> عضو في النتائج
            </span>
        </div>

        </div>{{-- /p-5 --}}
        </div>{{-- /fvr-filter-body --}}
    </form>
</div>

{{-- ===== BULK ACTION FORM ===== --}}
<form method="POST"
      action="{{ route('members.fv-reduction.apply') }}{{ $qs ? '?' . $qs : '' }}"
      id="fvr-bulk-form">
    @csrf

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-52 sm:mb-32">

        {{-- Table header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-gradient-to-l from-amber-50 to-white">
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="select-all-cb"
                           class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400 cursor-pointer">
                    <span class="text-sm font-bold text-gray-700">تحديد الكل</span>
                </label>
                <span id="selected-badge" class="hidden bg-amber-100 text-amber-700 text-xs font-bold rounded-full px-2.5 py-1">0 محدد</span>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-3 py-1">
                {{ $fmt($members->total()) }} عضو
            </span>
        </div>

        @if($members->isEmpty())
            <div class="text-center py-20 text-gray-400 text-sm">لا توجد نتائج مطابقة. جرب تعديل الفلاتر.</div>
        @else

        {{-- Desktop table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100">
                        <th class="px-4 py-3.5 w-10"></th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3.5">رقم الملف</th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3.5">الاسم</th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3.5 hidden md:table-cell">الجمعية</th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3.5 hidden lg:table-cell">حالة الجولة</th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3.5">المبلغ الحالي</th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3.5 hidden sm:table-cell" id="col-after">بعد التخفيض</th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3.5 hidden lg:table-cell" id="col-pts">نقاط تُخصم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                    @php
                        $memberAmt  = (float) ($member->estimated_amount ?? 0);
                        $visitAmt   = (float) ($member->latestFieldVisit?->estimated_amount ?? 0);
                        $currentAmt = $memberAmt + $visitAmt;
                        $pct        = 50; // preview at 50%
                        $previewPts = (int) floor(floor($memberAmt * $pct / 100) / 500);
                        $previewAmt = max(0, $memberAmt - $previewPts * 500) + $visitAmt;
                    @endphp
                    <tr class="hover:bg-amber-50/30 transition-colors group cursor-pointer fvr-row" onclick="toggleFvrCheck(this)">
                        <td class="px-4 py-3.5" onclick="event.stopPropagation()">
                            <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                                   class="fvr-cb w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400 cursor-pointer">
                        </td>
                        <td class="px-4 py-3.5 font-mono font-semibold text-gray-700">{{ $member->dossier_number ?? '—' }}</td>
                        <td class="px-4 py-3.5" onclick="event.stopPropagation()">
                            <a href="{{ route('members.show', $member) }}"
                               class="font-bold text-gray-800 hover:text-amber-600 hover:underline">{{ $member->full_name }}</a>
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 hidden md:table-cell">{{ $member->association?->name ?? '—' }}</td>
                        <td class="px-4 py-3.5 hidden lg:table-cell">
                            @if($member->latestFieldVisit?->status)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold border"
                                      style="background:{{ $member->latestFieldVisit->status->color }}18; color:{{ $member->latestFieldVisit->status->color }}; border-color:{{ $member->latestFieldVisit->status->color }}40">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $member->latestFieldVisit->status->color }}"></span>
                                    {{ $member->latestFieldVisit->status->name }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">بدون جولة</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($currentAmt > 0)
                                <span class="font-black text-gray-800">{{ $fmt($currentAmt) }}</span>
                                <span class="text-xs text-gray-400 mr-0.5">ل.س</span>
                            @else
                                <span class="text-gray-300 text-xs italic">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 hidden sm:table-cell">
                            @if($memberAmt > 0)
                                <span class="preview-amt {{ $previewPts > 0 ? 'font-bold text-orange-600' : 'text-gray-300 text-xs italic' }}"
                                      data-amount="{{ $memberAmt }}"
                                      data-visit="{{ $visitAmt }}">{{ $previewPts > 0 ? $fmt($previewAmt) : 'لا تغيير' }}</span>
                                @if($previewPts > 0)<span class="text-xs text-gray-400 mr-0.5">ل.س</span>@endif
                            @else
                                <span class="text-gray-300 text-xs italic">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 hidden lg:table-cell">
                            @if($memberAmt > 0)
                                <span class="preview-pts {{ $previewPts > 0 ? 'font-bold text-red-600' : 'text-gray-300 text-xs italic' }}"
                                      data-amount="{{ $memberAmt }}">{{ $previewPts > 0 ? $previewPts : 'لا تغيير' }}</span>
                                @if($previewPts > 0)<span class="text-xs text-gray-400">نقطة</span>@endif
                            @else
                                <span class="text-gray-300 text-xs italic">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
        <div class="px-5 py-3.5 border-t border-gray-100">
            {{ $members->withQueryString()->links() }}
        </div>
        @endif
        @endif

    </div>

    {{-- ===== STICKY ACTION BAR ===== --}}
    <div id="action-bar" class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200">
        <input type="hidden" name="apply_to" id="apply-to-input" value="selected">
        <input type="hidden" name="mode" id="mode-input" value="reduce">

        <div class="max-w-7xl mx-auto flex flex-wrap items-center gap-4 px-6 py-4">

            {{-- Mode toggle --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
                <button type="button" id="mode-reduce-btn" onclick="setMode('reduce')"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold transition-all bg-white shadow-sm text-orange-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5"/></svg>
                    تخفيض
                </button>
                <button type="button" id="mode-raise-btn" onclick="setMode('raise')"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold transition-all text-gray-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
                    رفع
                </button>
            </div>

            {{-- Percentage presets --}}
            <div class="flex items-center gap-1.5">
                <span id="pct-label" class="text-sm font-bold text-gray-600 shrink-0 ml-1">النسبة</span>
                <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1">
                    @foreach([25, 50, 75] as $preset)
                        <button type="button" onclick="setPercentage({{ $preset }})"
                                class="preset-btn px-3 py-1.5 rounded-lg text-sm font-bold transition-all text-gray-500 {{ $preset === 50 ? 'bg-white shadow-sm text-orange-600' : '' }}"
                                data-pct="{{ $preset }}">
                            {{ $preset }}%
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Custom percentage --}}
            <div class="flex items-center gap-1.5">
                <span class="text-sm text-gray-500 shrink-0">أو</span>
                <div id="pct-border" class="flex items-center gap-1.5 border-2 border-orange-300 rounded-xl px-3 py-1.5 focus-within:border-orange-400 transition-colors">
                    <input type="number" name="percentage" id="pct-input" min="1" max="100" step="0.5" value="50"
                           class="w-16 text-center text-lg font-black focus:outline-none"
                           oninput="updatePreviews(this.value)">
                    <span class="text-gray-500 font-bold">%</span>
                </div>
            </div>

            {{-- Reason --}}
            <div class="flex items-center gap-1.5 flex-1 min-w-48">
                <input type="text" name="reason" placeholder="السبب (اختياري)..."
                       class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition placeholder-gray-300">
            </div>

            {{-- Apply buttons --}}
            <div class="flex items-center gap-2">
                <button type="button" onclick="fvrApplySelected()"
                        id="apply-selected-btn"
                        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span id="apply-selected-label">تخفيض المحدّدين</span>
                    <span id="selected-count-label" class="bg-white/20 rounded-full px-2 py-0.5 text-xs">0</span>
                </button>
                <button type="button" onclick="fvrApplyFiltered()"
                        id="apply-filtered-btn"
                        class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span id="apply-filtered-label">تخفيض الكل</span>
                    <span class="bg-white/20 rounded-full px-2 py-0.5 text-xs">{{ $fmt($totalCount) }}</span>
                </button>
            </div>

            <span id="action-bar-badge" class="text-sm text-gray-400 ms-auto hidden">
                <span id="ab-count" class="font-bold text-orange-600">0</span> محدد
            </span>
        </div>
    </div>

</form>

@push('scripts')
<script>
// ── Filter toggles ─────────────────────────────────────────────────────────
function toggleFvrFilters() {
    const body  = document.getElementById('fvr-filter-body');
    const arrow = document.getElementById('fvr-filter-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}
function toggleFvrFvFilters() {
    const body  = document.getElementById('fvr-fv-body');
    const arrow = document.getElementById('fvr-fv-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

// ── Clean empty inputs before GET submit ───────────────────────────────────
function removeFvrEmptyFilters(form) {
    form.querySelectorAll('input[type=checkbox]:not(:checked)').forEach(cb => cb.disabled = true);
    form.querySelectorAll('input[type=text], input[type=date], input[type=number]').forEach(el => {
        if (el.value === '') el.disabled = true;
    });
    form.querySelectorAll('select').forEach(el => {
        if (el.value === '') el.disabled = true;
    });
}

// ── Multi-select dropdowns ─────────────────────────────────────────────────
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
        label.textContent = checked.length
            ? Array.from(checked).map(c => c.closest('label').textContent.trim()).join('، ')
            : '— الكل —';
        label.classList.toggle('text-amber-700', checked.length > 0);
        label.classList.toggle('text-gray-500', checked.length === 0);
    };
    dd.querySelectorAll('.ms-check').forEach(cb => cb.addEventListener('change', updateLabel));
    updateLabel();
});
document.addEventListener('click', () => {
    document.querySelectorAll('.ms-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.ms-arrow').forEach(a => a.classList.remove('rotate-180'));
});

// ── Checkboxes ────────────────────────────────────────────────────────────
const selectAllCb = document.getElementById('select-all-cb');
selectAllCb?.addEventListener('change', () => {
    document.querySelectorAll('.fvr-cb').forEach(cb => cb.checked = selectAllCb.checked);
    updateBadge();
});

function toggleFvrCheck(row) {
    const cb = row.querySelector('.fvr-cb');
    cb.checked = !cb.checked;
    updateBadge();
}

document.querySelectorAll('.fvr-cb').forEach(cb => cb.addEventListener('change', updateBadge));

function updateBadge() {
    const count    = document.querySelectorAll('.fvr-cb:checked').length;
    const allCount = document.querySelectorAll('.fvr-cb').length;

    const badge = document.getElementById('selected-badge');
    if (badge) { badge.textContent = count + ' محدد'; badge.classList.toggle('hidden', count === 0); }

    const scl = document.getElementById('selected-count-label');
    if (scl) scl.textContent = count;

    const ab = document.getElementById('action-bar-badge');
    const abCount = document.getElementById('ab-count');
    if (ab && abCount) { abCount.textContent = count; ab.classList.toggle('hidden', count === 0); }

    if (selectAllCb) {
        selectAllCb.checked      = allCount > 0 && count === allCount;
        selectAllCb.indeterminate = count > 0 && count < allCount;
    }
}

// ── Mode toggle ───────────────────────────────────────────────────────────
let currentMode = 'reduce';

function setMode(mode) {
    currentMode = mode;
    const isRaise = mode === 'raise';

    document.getElementById('mode-input').value = mode;

    // toggle buttons
    const reduceBtn = document.getElementById('mode-reduce-btn');
    const raiseBtn  = document.getElementById('mode-raise-btn');
    reduceBtn.classList.toggle('bg-white',       !isRaise);
    reduceBtn.classList.toggle('shadow-sm',      !isRaise);
    reduceBtn.classList.toggle('text-orange-600', !isRaise);
    reduceBtn.classList.toggle('text-gray-500',   isRaise);
    raiseBtn.classList.toggle('bg-white',        isRaise);
    raiseBtn.classList.toggle('shadow-sm',       isRaise);
    raiseBtn.classList.toggle('text-emerald-600', isRaise);
    raiseBtn.classList.toggle('text-gray-500',   !isRaise);

    // update preset active color
    const activeColor = isRaise ? 'text-emerald-600' : 'text-orange-600';
    const inactiveColor = isRaise ? 'text-orange-600' : 'text-emerald-600';

    // column headers
    document.getElementById('col-after').textContent = isRaise ? 'بعد الرفع' : 'بعد التخفيض';
    document.getElementById('col-pts').textContent   = isRaise ? 'نقاط تُضاف' : 'نقاط تُخصم';

    // button labels
    document.getElementById('apply-selected-label').textContent = isRaise ? 'رفع المحدّدين'  : 'تخفيض المحدّدين';
    document.getElementById('apply-filtered-label').textContent = isRaise ? 'رفع الكل' : 'تخفيض الكل';

    // button colors
    const selBtn = document.getElementById('apply-selected-btn');
    const filBtn = document.getElementById('apply-filtered-btn');
    selBtn.className = selBtn.className
        .replace(/bg-\w+-500|hover:bg-\w+-600/g, '')
        .trim()
        + (isRaise ? ' bg-emerald-500 hover:bg-emerald-600' : ' bg-orange-500 hover:bg-orange-600');

    // re-run previews
    updatePreviews(document.getElementById('pct-input').value);
}

// ── Percentage presets & live preview ─────────────────────────────────────
function setPercentage(pct) {
    document.getElementById('pct-input').value = pct;
    document.querySelectorAll('.preset-btn').forEach(btn => {
        const active = parseInt(btn.dataset.pct) === pct;
        const color  = currentMode === 'raise' ? 'text-emerald-600' : 'text-orange-600';
        btn.classList.toggle('bg-white',  active);
        btn.classList.toggle('shadow-sm', active);
        btn.classList.remove('text-orange-600', 'text-emerald-600', 'text-gray-500');
        btn.classList.add(active ? color : 'text-gray-500');
    });
    updatePreviews(pct);
}

function updatePreviews(pct) {
    const p       = parseFloat(pct || 0);
    const isRaise = currentMode === 'raise';

    document.querySelectorAll('.preview-amt').forEach(el => {
        const amt      = parseFloat(el.dataset.amount || 0);
        const visitAmt = parseFloat(el.dataset.visit  || 0);
        const delta    = Math.floor(amt * p / 100);
        const pts      = Math.floor(delta / 500);
        if (pts > 0) {
            const newAmt   = isRaise
                ? (amt + pts * 500) + visitAmt
                : Math.max(0, amt - pts * 500) + visitAmt;
            el.textContent = newAmt.toLocaleString('ar-SA');
            el.className   = isRaise ? 'preview-amt font-bold text-emerald-600' : 'preview-amt font-bold text-orange-600';
        } else {
            el.textContent = 'لا تغيير';
            el.className   = 'preview-amt text-gray-300 text-xs italic';
        }
    });
    document.querySelectorAll('.preview-pts').forEach(el => {
        const amt   = parseFloat(el.dataset.amount || 0);
        const delta = Math.floor(amt * p / 100);
        const pts   = Math.floor(delta / 500);
        if (pts > 0) {
            el.textContent = (isRaise ? '+' : '') + pts;
            el.className   = isRaise ? 'preview-pts font-bold text-emerald-600' : 'preview-pts font-bold text-red-600';
        } else {
            el.textContent = 'لا تغيير';
            el.className   = 'preview-pts text-gray-300 text-xs italic';
        }
    });
    // sync presets
    document.querySelectorAll('.preset-btn').forEach(btn => {
        const active = parseFloat(btn.dataset.pct) === p;
        const color  = currentMode === 'raise' ? 'text-emerald-600' : 'text-orange-600';
        btn.classList.toggle('bg-white',  active);
        btn.classList.toggle('shadow-sm', active);
        btn.classList.remove('text-orange-600', 'text-emerald-600', 'text-gray-500');
        btn.classList.add(active ? color : 'text-gray-500');
    });
}

// Initial preview
updatePreviews(50);

// ── Apply actions ─────────────────────────────────────────────────────────
function fvrApplySelected() {
    const count = document.querySelectorAll('.fvr-cb:checked').length;
    if (count === 0) { alert('يرجى تحديد أعضاء أولاً.'); return; }
    document.getElementById('apply-to-input').value = 'selected';
    document.getElementById('fvr-bulk-form').submit();
}

function fvrApplyFiltered() {
    const pct  = parseFloat(document.getElementById('pct-input').value);
    const verb = currentMode === 'raise' ? 'رفع' : 'تخفيض';
    if (!confirm(`سيتم ${verb} ${pct}% من المبلغ المقدّر لجميع الأعضاء المفلترين. هل أنت متأكد؟`)) return;
    document.getElementById('apply-to-input').value = 'filtered';
    document.getElementById('fvr-bulk-form').submit();
}
</script>
@endpush

@endsection
