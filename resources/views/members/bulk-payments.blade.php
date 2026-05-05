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

        {{-- Search + toggle --}}
        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="بحث بالاسم، رقم الهوية، الهاتف، أو رقم الملف..."
                       class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50
                              focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
            </div>
            <button type="button" onclick="toggleBpFilters()"
                    class="flex items-center gap-2 px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-teal-300 transition-colors text-sm font-bold text-gray-600 shrink-0">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                الفلاتر
                <svg id="bp-filter-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        {{-- Collapsible filters --}}
        <div id="bp-filter-body" class="hidden">
        <div class="p-5">

        {{-- Dossier range --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-3">
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">رقم الاضبارة من</label>
                    <input type="text" name="dossier_from" value="{{ $dossierFrom }}" placeholder="مثال: 100"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="text" name="dossier_to" value="{{ $dossierTo }}" placeholder="مثال: 200"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
            </div>

            {{-- Has payments filter --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الدفعات</label>
                <select name="has_payments" onwheel="this.blur()"
                        class="w-full text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1" {{ $hasPayments === '1' ? 'selected' : '' }}>لديهم دفعات</option>
                    <option value="0" {{ $hasPayments === '0' ? 'selected' : '' }}>بدون دفعات</option>
                </select>
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
                    @foreach($finalStatusList as $fs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}" {{ in_array($fs->id, $finalStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $fs->color }}"></span>
                            {{ $fs->name }}
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

            {{-- Marital --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة الاجتماعية</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @foreach($maritalStatusList as $ms)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="marital_status[]" value="{{ $ms->name }}" {{ in_array($ms->name, $maritalStatuses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $ms->name }}
                        </label>
                    @endforeach
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

            {{-- Network --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">نوع الشبكة</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    @foreach(['MTN', 'SYRIATEL'] as $net)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="network[]" value="{{ $net }}" {{ in_array($net, $networks) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                            {{ $net }}
                        </label>
                    @endforeach
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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-36">

        {{-- Table header bar --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gradient-to-l from-teal-50 to-white">
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="select-all-cb"
                           class="w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-400 cursor-pointer">
                    <span class="text-sm font-bold text-gray-700">تحديد الكل في الصفحة</span>
                </label>
                <span id="selected-badge" class="hidden bg-teal-100 text-teal-700 text-xs font-bold rounded-full px-2.5 py-1">0 محدد</span>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-3 py-1">
                صفحة {{ $members->currentPage() }} · {{ $fmt($members->total()) }} عضو
            </span>
        </div>

        @if($members->isEmpty())
            <div class="text-center py-20 text-gray-400 text-sm">لا توجد نتائج مطابقة. جرب تعديل الفلاتر.</div>
        @else
            <div class="overflow-x-auto">
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
                        <tr class="hover:bg-teal-50/30 transition-colors group cursor-pointer member-row" onclick="toggleRowCheck(this)">
                            <td class="px-4 py-3.5" onclick="event.stopPropagation()">
                                <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                                       class="member-cb w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-400 cursor-pointer">
                            </td>
                            <td class="px-4 py-3.5 font-mono font-semibold text-gray-700 text-sm">{{ $member->dossier_number ?? '—' }}</td>
                            <td class="px-4 py-3.5 font-bold text-gray-800">{{ $member->full_name }}</td>
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
         class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 px-6 py-4">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center gap-4">

            {{-- Operation mode --}}
            <div class="flex items-center gap-1.5 bg-gray-100 rounded-xl p-1">
                <label class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer transition-all has-[:checked]:bg-white has-[:checked]:shadow-sm has-[:checked]:text-teal-700 text-gray-500 text-sm font-semibold">
                    <input type="radio" name="operation" value="add" checked class="hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    إضافة
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

            {{-- Apply to --}}
            <div class="flex items-center gap-2">
                <input type="hidden" name="apply_to" id="apply-to-input" value="selected">
                <button type="button" onclick="applyToSelected()"
                        id="btn-selected"
                        class="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    تطبيق على المحدّدين
                    <span id="selected-count-label" class="bg-white/20 rounded-full px-2 py-0.5 text-xs">0</span>
                </button>
                <button type="button" onclick="applyToFiltered()"
                        id="btn-filtered"
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

// ── Remove empty array params before GET submit ────────────────────────────
function removeBpEmptyFilters(form) {
    form.querySelectorAll('input[type=checkbox]:not(:checked)').forEach(cb => cb.disabled = true);
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

// ── Checkbox selection ─────────────────────────────────────────────────────
const selectAll = document.getElementById('select-all-cb');
selectAll?.addEventListener('change', () => {
    document.querySelectorAll('.member-cb').forEach(cb => cb.checked = selectAll.checked);
    updateBadge();
});

function toggleRowCheck(row) {
    const cb = row.querySelector('.member-cb');
    cb.checked = !cb.checked;
    updateBadge();
}

document.querySelectorAll('.member-cb').forEach(cb => cb.addEventListener('change', updateBadge));

function updateBadge() {
    const count = document.querySelectorAll('.member-cb:checked').length;
    document.getElementById('selected-badge').textContent = count + ' محدد';
    document.getElementById('selected-badge').classList.toggle('hidden', count === 0);
    document.getElementById('selected-count-label').textContent = count;
    const ab = document.getElementById('action-bar-badge');
    const abCount = document.getElementById('ab-count');
    abCount.textContent = count;
    ab.classList.toggle('hidden', count === 0);
}

// ── Apply actions ──────────────────────────────────────────────────────────
function applyToSelected() {
    const count = document.querySelectorAll('.member-cb:checked').length;
    if (count === 0) { alert('يرجى تحديد أعضاء أولاً.'); return; }
    document.getElementById('apply-to-input').value = 'selected';
    document.getElementById('bulk-form').submit();
}

function applyToFiltered() {
    if (!confirm('سيتم تطبيق العملية على جميع الأعضاء المفلترين. هل أنت متأكد؟')) return;
    document.getElementById('apply-to-input').value = 'filtered';
    document.getElementById('bulk-form').submit();
}

// ── Radio toggle visual ────────────────────────────────────────────────────
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
</script>
@endpush

@endsection
