@extends('layouts.app')

@section('title', 'تسوية النقاط — مسالك النور')
@section('max-width', 'max-w-6xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-amber-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">تسوية النقاط</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-4 py-3 rounded-xl">{{ session('success') }}</div>
@endif
@if(session('pending'))
    <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium px-4 py-3 rounded-xl">{{ session('pending') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm font-medium px-4 py-3 rounded-xl">{{ session('error') }}</div>
@endif

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-violet-700 via-indigo-600 to-blue-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-8 -left-8 w-40 h-40 bg-white rounded-full"></div>
        <div class="absolute -bottom-12 left-24 w-56 h-56 bg-white rounded-full"></div>
        <div class="absolute top-4 right-12 w-24 h-24 bg-white rounded-full"></div>
    </div>
    <div class="relative flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">تسوية النقاط الجماعية</h1>
            <p class="text-indigo-200 text-sm mt-1 max-w-lg">
                حدد مجموعة من الأعضاء وأدخل نقاطاً مستهدفة موحدة — يُزاد للذي يحتاج زيادة ويُنقص للذي يحتاج انقاصاً تلقائياً
            </p>
        </div>
        <div class="bg-white/15 border border-white/25 rounded-2xl px-5 py-3 text-center">
            <p class="text-white font-black text-3xl leading-none" id="hero-count">0</p>
            <p class="text-indigo-200 text-xs mt-1">عضو محدد</p>
        </div>
    </div>
</div>

{{-- Filter panel --}}
@php
    $eqActiveFilters = array_filter([
        request('search'), request('dossier_from'), request('dossier_to'),
        request('estimated_from'), request('estimated_to'),
        request('payments_count_from'), request('payments_count_to'),
    ], fn($v) => $v !== null && $v !== '') + array_filter([
        request('verification_status_id', []),
        request('final_status_id', []),
        request('marital_status', []),
        request('gender', []),
        request('delegate', []),
        request('current_address', []),
        request('association_id', []),
        request('network', []),
        request('region_id', []),
        request('housing_status_id', []),
    ], fn($v) => !empty($v));
    $eqHasFilters = !empty($eqActiveFilters);
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 overflow-hidden">
    {{-- Header row --}}
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 cursor-pointer select-none"
         onclick="document.getElementById('eq-filter-body').classList.toggle('hidden')">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <span class="text-sm font-bold text-gray-700">فلترة الأعضاء</span>
            @if($eqHasFilters)
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-0.5 rounded-full">فلتر نشط</span>
            @endif
        </div>
        <svg class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>

    <div id="eq-filter-body" class="{{ $eqHasFilters ? '' : 'hidden' }}">
        <form method="GET" action="{{ route('members.score-equalizer') }}" class="p-5 space-y-4">

            {{-- Row 1: Search + Dossier range --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">بحث (اسم / هوية / هاتف / اضبارة)</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث..."
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">رقم الاضبارة من</label>
                    <input type="number" name="dossier_from" value="{{ request('dossier_from') }}" placeholder="من..."
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">رقم الاضبارة إلى</label>
                    <input type="number" name="dossier_to" value="{{ request('dossier_to') }}" placeholder="إلى..."
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none bg-gray-50">
                </div>
            </div>

            {{-- Row 2: Amounts + payments --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">المبلغ المقدر من</label>
                    <input type="number" name="estimated_from" value="{{ request('estimated_from') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">المبلغ المقدر إلى</label>
                    <input type="number" name="estimated_to" value="{{ request('estimated_to') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">عدد الدفعات من</label>
                    <input type="number" name="payments_count_from" value="{{ request('payments_count_from') }}" min="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">عدد الدفعات إلى</label>
                    <input type="number" name="payments_count_to" value="{{ request('payments_count_to') }}" min="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-300 focus:outline-none bg-gray-50">
                </div>
            </div>

            {{-- Row 3: Checkboxes grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Verification status --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">حالة التحقق</p>
                    <div class="space-y-1 max-h-36 overflow-y-auto pr-1">
                        @foreach($verificationStatuses as $vs)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="verification_status_id[]" value="{{ $vs->id }}"
                                   {{ in_array($vs->id, (array)request('verification_status_id', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            <span class="inline-block w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $vs->color }}"></span>
                            {{ $vs->name }}
                        </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="verification_status_id[]" value="none"
                                   {{ in_array('none', (array)request('verification_status_id', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            بدون حالة
                        </label>
                    </div>
                </div>

                {{-- Final status --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">الحالة النهائية</p>
                    <div class="space-y-1 max-h-36 overflow-y-auto pr-1">
                        @foreach($finalStatusList as $fs)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}"
                                   {{ in_array($fs->id, (array)request('final_status_id', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            {{ $fs->name }}
                        </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="final_status_id[]" value="none"
                                   {{ in_array('none', (array)request('final_status_id', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            بدون حالة
                        </label>
                    </div>
                </div>

                {{-- Gender --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">الجنس</p>
                    <div class="space-y-1">
                        @foreach([['ذكر','ذكر'],['أنثى','أنثى']] as [$val,$lbl])
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="gender[]" value="{{ $val }}"
                                   {{ in_array($val, (array)request('gender', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            {{ $lbl }}
                        </label>
                        @endforeach
                    </div>

                    <p class="text-xs font-bold text-gray-500 mb-2 mt-3">الشبكة</p>
                    <div class="space-y-1">
                        @foreach([['MTN','MTN'],['SYRIATEL','سيريتل']] as [$val,$lbl])
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="network[]" value="{{ $val }}"
                                   {{ in_array($val, (array)request('network', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            {{ $lbl }}
                        </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="network[]" value="none"
                                   {{ in_array('none', (array)request('network', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            بدون
                        </label>
                    </div>
                </div>

                {{-- Marital status --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">الحالة الاجتماعية</p>
                    <div class="space-y-1 max-h-36 overflow-y-auto pr-1">
                        @foreach($maritalStatusList as $ms)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="marital_status[]" value="{{ $ms->name }}"
                                   {{ in_array($ms->name, (array)request('marital_status', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            {{ $ms->name }}
                        </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="marital_status[]" value="none"
                                   {{ in_array('none', (array)request('marital_status', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            بدون
                        </label>
                    </div>
                </div>

                {{-- Association --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">الجمعية</p>
                    <div class="space-y-1 max-h-36 overflow-y-auto pr-1">
                        @foreach($associationList as $assoc)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="association_id[]" value="{{ $assoc->id }}"
                                   {{ in_array($assoc->id, (array)request('association_id', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            {{ $assoc->name }}
                        </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="association_id[]" value="none"
                                   {{ in_array('none', (array)request('association_id', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            بدون
                        </label>
                    </div>
                </div>

                {{-- Region + Housing status --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">المنطقة</p>
                    <div class="space-y-1 max-h-28 overflow-y-auto pr-1">
                        @foreach($regionList as $region)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="region_id[]" value="{{ $region->id }}"
                                   {{ in_array($region->id, (array)request('region_id', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            {{ $region->name }}
                        </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="region_id[]" value="none"
                                   {{ in_array('none', (array)request('region_id', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            بدون
                        </label>
                    </div>

                    <p class="text-xs font-bold text-gray-500 mb-2 mt-3">وضع السكن</p>
                    <div class="space-y-1 max-h-28 overflow-y-auto pr-1">
                        @foreach($housingStatusList as $hs)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="housing_status_id[]" value="{{ $hs->id }}"
                                   {{ in_array($hs->id, (array)request('housing_status_id', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            {{ $hs->name }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Delegate --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">المندوب الخارجي</p>
                    <div class="space-y-1 max-h-36 overflow-y-auto pr-1">
                        @foreach($delegateList as $dlg)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="delegate[]" value="{{ $dlg }}"
                                   {{ in_array($dlg, (array)request('delegate', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            {{ $dlg }}
                        </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="delegate[]" value="none"
                                   {{ in_array('none', (array)request('delegate', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            بدون
                        </label>
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <p class="text-xs font-bold text-gray-500 mb-2">العنوان</p>
                    <div class="space-y-1 max-h-36 overflow-y-auto pr-1">
                        @foreach($addressList as $addr)
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="current_address[]" value="{{ $addr }}"
                                   {{ in_array($addr, (array)request('current_address', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            {{ $addr }}
                        </label>
                        @endforeach
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-indigo-700">
                            <input type="checkbox" name="current_address[]" value="none"
                                   {{ in_array('none', (array)request('current_address', [])) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                            بدون
                        </label>
                    </div>
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex gap-2 pt-1 border-t border-gray-100">
                <button type="submit"
                        class="flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    تطبيق الفلتر
                </button>
                @if($eqHasFilters)
                <a href="{{ route('members.score-equalizer') }}"
                   class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold px-4 py-2 rounded-xl transition-colors">
                    مسح الكل
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Members table (bulk form) --}}
<form id="eq-form" method="POST" action="{{ route('members.score-equalizer.apply') }}">
    @csrf
    <input type="hidden" name="target_score" id="eq-target-hidden" value="0">
    <input type="hidden" name="reason"       id="eq-reason-hidden" value="">

@if($members->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
            </svg>
        </div>
        <p class="text-gray-400 text-sm font-medium">لا يوجد أعضاء مطابقون لهذا البحث</p>
    </div>
@else
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-24">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
        <div class="flex items-center gap-3">
            <p class="text-sm font-bold text-gray-600">{{ number_format($members->total()) }} عضو</p>
            @if($members->total() > $members->perPage())
            <button type="button" id="select-all-pages-btn" onclick="eqSelectAllPages()"
                    class="text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-3 py-1 rounded-lg transition-colors">
                تحديد جميع الصفحات ({{ number_format(count($allIds)) }})
            </button>
            <button type="button" id="clear-all-pages-btn" onclick="eqClearAll()" style="display:none"
                    class="text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 px-3 py-1 rounded-lg transition-colors">
                إلغاء تحديد الكل
            </button>
            @endif
        </div>
        <p class="text-xs text-gray-400">صفحة {{ $members->currentPage() }} من {{ $members->lastPage() }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/30">
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" id="eq-select-all"
                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 cursor-pointer"
                               onchange="eqToggleAll(this.checked)">
                    </th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">الاسم</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">رقم الاضبارة</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">النقاط الحالية</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">ستصبح</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">المبلغ الحالي</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="eq-tbody">
                @foreach($members as $member)
                <tr class="transition-colors hover:bg-indigo-50/20 eq-row" data-score="{{ (int)($member->score ?? 0) }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                               class="eq-cb w-4 h-4 rounded border-gray-300 text-indigo-600 cursor-pointer"
                               onchange="eqOnCheck(this)">
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 whitespace-nowrap">{{ $member->full_name }}</p>
                        @if($member->verificationStatus)
                            <span class="text-xs px-1.5 py-0.5 rounded-md font-medium"
                                  style="background: {{ $member->verificationStatus->color }}22; color: {{ $member->verificationStatus->color }};">
                                {{ $member->verificationStatus->name }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs whitespace-nowrap">
                        {{ $member->dossier_number ?: '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block bg-gray-100 text-gray-700 font-black text-sm px-3 py-1 rounded-lg current-score-badge">
                            {{ (int)($member->score ?? 0) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black text-gray-300">—</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                        {{ number_format($member->estimated_amount ?? 0) }} ل.س
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('members.show', $member->id) }}" target="_blank"
                           class="inline-flex items-center gap-1 text-xs font-bold text-gray-400 hover:text-indigo-600 bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-200 px-2.5 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            عرض
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
    <div class="border-t border-gray-100 px-5 py-3 bg-gray-50/30">
        {{ $members->links() }}
    </div>
    @endif
</div>
@endif

</form>

{{-- Sticky action bar --}}
<div id="eq-bar"
     class="fixed bottom-0 right-0 left-0 z-50 transform translate-y-full transition-transform duration-300 ease-in-out"
     style="padding-right: 68px;">
    <div class="mx-auto px-4 pb-4" style="max-width: 72rem;">
        <div class="bg-gray-900 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Preview summary strip --}}
            <div id="eq-preview-strip"
                 class="flex items-center gap-6 px-5 py-2 border-b border-gray-700 bg-gray-800/60 text-xs font-medium flex-wrap">
                <span class="text-gray-400">معاينة:</span>
                <span class="flex items-center gap-1.5 text-emerald-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span id="eq-count-up">0</span> سيُزاد
                </span>
                <span class="flex items-center gap-1.5 text-red-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                    <span id="eq-count-down">0</span> سيُنقص
                </span>
                <span class="flex items-center gap-1.5 text-gray-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                    </svg>
                    <span id="eq-count-same">0</span> بدون تغيير
                </span>
                <span class="text-gray-500 mr-auto">النقاط المستهدفة: <span id="eq-preview-target" class="text-white font-bold">—</span></span>
            </div>

            {{-- Controls --}}
            <div class="flex flex-wrap gap-3 items-center px-5 py-4">

                {{-- Selected count --}}
                <div class="flex items-center gap-2 shrink-0">
                    <span class="bg-indigo-500 text-white text-xs font-black px-2.5 py-1 rounded-full" id="eq-sel-count">0</span>
                    <span class="text-sm text-gray-300">عضو محدد</span>
                </div>

                <div class="w-px h-6 bg-gray-600 hidden sm:block shrink-0"></div>

                {{-- Target score --}}
                <div class="flex items-center gap-2 shrink-0">
                    <label class="text-sm text-gray-300 whitespace-nowrap font-medium">النقاط المستهدفة</label>
                    <input type="number" id="eq-target" min="0" value=""
                           placeholder="0"
                           class="w-24 bg-gray-800 border-2 border-indigo-500 text-white text-lg font-black text-center rounded-xl px-2 py-1.5 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                           oninput="eqOnTargetChange()">
                </div>

                {{-- Reason --}}
                <div class="flex items-center gap-2 flex-1 min-w-[180px]">
                    <label class="text-xs text-gray-400 whitespace-nowrap">السبب</label>
                    <input type="text" id="eq-reason" placeholder="اختياري..."
                           class="flex-1 bg-gray-800 border border-gray-600 text-white text-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-gray-500"
                           oninput="document.getElementById('eq-reason-hidden').value = this.value">
                </div>

                {{-- Apply button --}}
                <button type="button" onclick="eqSubmit()"
                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-colors shrink-0 shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    تطبيق التسوية
                </button>

                {{-- Cancel --}}
                <button type="button" onclick="eqClearAll()"
                        class="text-gray-400 hover:text-white text-xs font-medium transition-colors shrink-0">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const ALL_IDS    = @json($allIds);
    const TOTAL_ALL  = ALL_IDS.length;
    let currentTarget   = null;
    let allPagesSelected = false;

    function getChecked() {
        return Array.from(document.querySelectorAll('.eq-cb:checked'));
    }

    function effectiveCount() {
        return allPagesSelected ? TOTAL_ALL : getChecked().length;
    }

    function eqRefreshBar() {
        const count     = effectiveCount();
        const bar       = document.getElementById('eq-bar');
        const selCount  = document.getElementById('eq-sel-count');
        const heroCount = document.getElementById('hero-count');
        selCount.textContent  = count;
        heroCount.textContent = count;

        bar.classList.toggle('translate-y-full', count === 0);

        // Keep select-all checkbox in sync (only reflects current page)
        if (!allPagesSelected) {
            const all = document.querySelectorAll('.eq-cb');
            const checked = getChecked();
            const sa = document.getElementById('eq-select-all');
            if (sa) {
                sa.indeterminate = checked.length > 0 && checked.length < all.length;
                sa.checked       = all.length > 0 && checked.length === all.length;
            }
        }

        eqRefreshPreview();
    }

    function eqRefreshPreview() {
        if (allPagesSelected) {
            document.getElementById('eq-count-up').textContent   = currentTarget !== null ? TOTAL_ALL : 0;
            document.getElementById('eq-count-down').textContent = 0;
            document.getElementById('eq-count-same').textContent = currentTarget !== null ? 0 : TOTAL_ALL;
            document.getElementById('eq-preview-target').textContent = currentTarget !== null ? currentTarget : '—';
            return;
        }
        const checked = getChecked();
        let up = 0, down = 0, same = 0;
        checked.forEach(cb => {
            const score = parseInt(cb.closest('tr').dataset.score) || 0;
            if (currentTarget === null) { same++; return; }
            if (currentTarget > score) up++;
            else if (currentTarget < score) down++;
            else same++;
        });
        document.getElementById('eq-count-up').textContent   = up;
        document.getElementById('eq-count-down').textContent = down;
        document.getElementById('eq-count-same').textContent = same;
        document.getElementById('eq-preview-target').textContent = currentTarget !== null ? currentTarget : '—';
    }

    function eqUpdateRowBadges() {
        document.querySelectorAll('.eq-row').forEach(row => {
            const cb    = row.querySelector('.eq-cb');
            const badge = row.querySelector('.new-score-badge');
            const score = parseInt(row.dataset.score) || 0;
            const isSelected = allPagesSelected || cb.checked;

            if (!isSelected || currentTarget === null) {
                badge.textContent = '—';
                badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black text-gray-300';
                return;
            }
            badge.textContent = currentTarget;
            if (currentTarget > score)      badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black bg-emerald-100 text-emerald-700';
            else if (currentTarget < score) badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black bg-red-100 text-red-700';
            else                            badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black bg-gray-100 text-gray-500';
        });
    }

    function eqOnCheck(cb) {
        if (allPagesSelected) {
            allPagesSelected = false;
            toggleAllPagesUI(false);
        }
        const row   = cb.closest('tr');
        const badge = row.querySelector('.new-score-badge');
        const score = parseInt(row.dataset.score) || 0;
        if (!cb.checked) {
            badge.textContent = '—';
            badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black text-gray-300';
        } else if (currentTarget !== null) {
            badge.textContent = currentTarget;
            if (currentTarget > score)      badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black bg-emerald-100 text-emerald-700';
            else if (currentTarget < score) badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black bg-red-100 text-red-700';
            else                            badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black bg-gray-100 text-gray-500';
        }
        eqRefreshBar();
    }

    function eqOnTargetChange() {
        const raw = document.getElementById('eq-target').value;
        currentTarget = raw === '' ? null : Math.max(0, parseInt(raw) || 0);
        document.getElementById('eq-target-hidden').value = currentTarget ?? '';
        eqUpdateRowBadges();
        eqRefreshPreview();
    }

    function eqToggleAll(checked) {
        allPagesSelected = false;
        toggleAllPagesUI(false);
        document.querySelectorAll('.eq-cb').forEach(cb => {
            cb.checked = checked;
            const row   = cb.closest('tr');
            const badge = row.querySelector('.new-score-badge');
            const score = parseInt(row.dataset.score) || 0;
            if (!checked) {
                badge.textContent = '—';
                badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black text-gray-300';
            } else if (currentTarget !== null) {
                badge.textContent = currentTarget;
                if (currentTarget > score)      badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black bg-emerald-100 text-emerald-700';
                else if (currentTarget < score) badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black bg-red-100 text-red-700';
                else                            badge.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black bg-gray-100 text-gray-500';
            }
        });
        eqRefreshBar();
    }

    function eqSelectAllPages() {
        allPagesSelected = true;
        // Check all visible rows for visual feedback
        document.querySelectorAll('.eq-cb').forEach(cb => { cb.checked = true; });
        const sa = document.getElementById('eq-select-all');
        if (sa) { sa.checked = true; sa.indeterminate = false; }
        eqUpdateRowBadges();
        toggleAllPagesUI(true);
        eqRefreshBar();
    }

    function toggleAllPagesUI(on) {
        const btnSelect = document.getElementById('select-all-pages-btn');
        const btnClear  = document.getElementById('clear-all-pages-btn');
        if (btnSelect) btnSelect.style.display = on ? 'none' : '';
        if (btnClear)  btnClear.style.display  = on ? '' : 'none';

        // Highlight the bar when all pages selected
        const bar = document.getElementById('eq-bar');
        if (on) bar.querySelector('.bg-gray-900').style.outline = '2px solid #6366f1';
        else    bar.querySelector('.bg-gray-900').style.outline = '';
    }

    function eqClearAll() {
        allPagesSelected = false;
        toggleAllPagesUI(false);
        document.querySelectorAll('.eq-cb').forEach(cb => cb.checked = false);
        document.querySelectorAll('.new-score-badge').forEach(b => {
            b.textContent = '—';
            b.className = 'new-score-badge inline-block px-3 py-1 rounded-lg text-sm font-black text-gray-300';
        });
        const sa = document.getElementById('eq-select-all');
        if (sa) { sa.checked = false; sa.indeterminate = false; }
        eqRefreshBar();
    }

    function eqSubmit() {
        const count = effectiveCount();
        if (count === 0) { alert('الرجاء تحديد عضو واحد على الأقل'); return; }
        if (currentTarget === null || isNaN(currentTarget)) {
            alert('الرجاء إدخال النقاط المستهدفة');
            document.getElementById('eq-target').focus();
            return;
        }

        const up   = parseInt(document.getElementById('eq-count-up').textContent)   || 0;
        const down = parseInt(document.getElementById('eq-count-down').textContent) || 0;
        const same = parseInt(document.getElementById('eq-count-same').textContent) || 0;

        let msg = `تسوية نقاط ${count} عضو إلى ${currentTarget} نقطة`;
        if (allPagesSelected) msg += ' (جميع الصفحات)';
        msg += `.\n↑ سيُزاد: ${up}  ↓ سيُنقص: ${down}  = بدون تغيير: ${same}\n\nهل أنت متأكد؟`;
        if (!confirm(msg)) return;

        const form = document.getElementById('eq-form');
        document.getElementById('eq-target-hidden').value = currentTarget;
        document.getElementById('eq-reason-hidden').value = document.getElementById('eq-reason').value;

        if (allPagesSelected) {
            // Remove existing checkboxes to avoid conflicts, inject all IDs
            form.querySelectorAll('input[name="member_ids[]"]').forEach(el => el.remove());
            ALL_IDS.forEach(id => {
                const inp = document.createElement('input');
                inp.type  = 'hidden';
                inp.name  = 'member_ids[]';
                inp.value = id;
                form.appendChild(inp);
            });
        }

        form.submit();
    }

    // Expose globals for inline handlers
    window.eqToggleAll       = eqToggleAll;
    window.eqOnCheck         = eqOnCheck;
    window.eqOnTargetChange  = eqOnTargetChange;
    window.eqClearAll        = eqClearAll;
    window.eqSubmit          = eqSubmit;
    window.eqSelectAllPages  = eqSelectAllPages;
})();
</script>

@endsection
