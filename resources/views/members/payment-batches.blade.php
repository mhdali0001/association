@extends('layouts.app')

@section('title', 'سجل الدفعات — مسالك النور')
@section('max-width', 'max-w-7xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-amber-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <a href="{{ route('members.bulk-payments') }}" class="hover:text-amber-700 transition-colors">الدفعات الجماعية</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">سجل الدفعات</span>
@endsection

@section('content')

@php
    $fmt      = fn($n) => number_format($n);
    $fmtMoney = fn($n) => number_format($n, 0) . ' ل.س';
    $hasFilters = $search || $operation || $dateFrom || $dateTo || $appliedBy
               || $amountMin !== '' || $amountMax !== ''
               || $membersMin !== '' || $membersMax !== ''
               || $totalMin !== '' || $totalMax !== ''
               || $filterYear || $filterMonth || $memberSearch || $hasNotes;
@endphp

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-4 py-3 rounded-2xl">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Page Header --}}
<div class="flex items-center justify-between mb-5 gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-gray-900">سجل الدفعات</h1>
        <p class="text-xs sm:text-sm text-gray-400 mt-0.5">تاريخ جميع عمليات الدفعات الجماعية</p>
    </div>
    <a href="{{ route('members.bulk-payments') }}"
       class="shrink-0 flex items-center gap-1.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-3 sm:px-4 py-2.5 rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span class="hidden sm:inline">دفعة جديدة</span>
        <span class="sm:hidden">جديد</span>
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 mb-5">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-4 flex items-center gap-2.5">
        <div class="w-9 h-9 sm:w-12 sm:h-12 bg-teal-50 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-4.5 h-4.5 sm:w-6 sm:h-6 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-lg sm:text-2xl font-black text-gray-900 leading-none">{{ $fmt($totalBatches) }}</p>
            <p class="text-[11px] sm:text-xs text-gray-400 font-medium mt-0.5">إجمالي الدفعات</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-4 flex items-center gap-2.5">
        <div class="w-9 h-9 sm:w-12 sm:h-12 bg-violet-50 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-4.5 h-4.5 sm:w-6 sm:h-6 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-lg sm:text-2xl font-black text-gray-900 leading-none">{{ $fmt($totalMembers) }}</p>
            <p class="text-[11px] sm:text-xs text-gray-400 font-medium mt-0.5">عضو مشمول</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-4 flex items-center gap-2.5">
        <div class="w-9 h-9 sm:w-12 sm:h-12 bg-amber-50 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-4.5 h-4.5 sm:w-6 sm:h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-xs sm:text-lg font-black text-gray-900 leading-tight">{{ $fmtMoney($totalAmount) }}</p>
            <p class="text-[11px] sm:text-xs text-gray-400 font-medium mt-0.5">إجمالي المبالغ</p>
        </div>
    </div>
    <div class="bg-gradient-to-l from-teal-600 to-cyan-500 rounded-2xl shadow-sm p-3 sm:p-4 flex items-center gap-2.5">
        <div class="w-9 h-9 sm:w-12 sm:h-12 bg-white/20 rounded-xl sm:rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-4.5 h-4.5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-lg sm:text-2xl font-black text-white leading-none">{{ $fmt($batches->total()) }}</p>
            <p class="text-[11px] sm:text-xs text-teal-100 font-medium mt-0.5">نتائج الفلتر</p>
        </div>
    </div>
</div>

{{-- ══════════════════ FILTER BAR ══════════════════ --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <form method="GET" action="{{ route('members.payment-batches') }}" id="batch-filter-form">

        {{-- Always visible: search + toggle --}}
        <div class="px-4 pt-4 pb-3 border-b border-gray-100 space-y-2.5">
            <div class="flex gap-2">
                {{-- Batch search --}}
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="بحث باسم الدفعة أو ملاحظة..."
                           class="w-full pr-10 pl-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50
                                  focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
                </div>
                {{-- Member search --}}
                <div class="relative flex-1 hidden sm:block">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    <input type="text" name="member_search" value="{{ $memberSearch }}"
                           placeholder="بحث عن عضو (اسم، رقم وطني، ملف)..."
                           class="w-full pr-10 pl-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50
                                  focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
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
                <button type="button" onclick="toggleBatchFilters()"
                        class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-teal-300 transition-colors text-sm font-bold text-gray-600">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    الفلاتر
                    @if($hasFilters)
                        <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                    @endif
                    <svg id="batch-filter-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                @if($hasFilters)
                    <a href="{{ route('members.payment-batches') }}"
                       class="flex items-center gap-1.5 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-500 text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        مسح
                    </a>
                @endif
            </div>
        </div>

        {{-- Collapsible filter body --}}
        <div id="batch-filter-body" class="{{ $hasFilters ? '' : 'hidden' }}">
            <div class="p-4 space-y-4">

                {{-- Row 1: operation, applied by, year, month --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">نوع العملية</label>
                        <select name="operation" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                            <option value="">— الكل —</option>
                            <option value="add"      {{ $operation === 'add'      ? 'selected' : '' }}>إضافة</option>
                            <option value="subtract" {{ $operation === 'subtract' ? 'selected' : '' }}>طرح</option>
                            <option value="set"      {{ $operation === 'set'      ? 'selected' : '' }}>تعيين</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">المنفّذ</label>
                        <select name="applied_by" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                            <option value="">— الكل —</option>
                            @foreach($userList as $u)
                                <option value="{{ $u->id }}" {{ $appliedBy == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">السنة</label>
                        <select name="year" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                            <option value="">— الكل —</option>
                            @foreach($availableYears as $yr)
                                <option value="{{ $yr }}" {{ $filterYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">الشهر</label>
                        <select name="month" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                            <option value="">— الكل —</option>
                            @foreach([1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'] as $num => $name)
                                <option value="{{ $num }}" {{ $filterMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Row 2: date range + member search (mobile) + has notes --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">من تاريخ</label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                        </div>
                        <span class="text-gray-400 pb-2.5">—</span>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">إلى تاريخ</label>
                            <input type="date" name="date_to" value="{{ $dateTo }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                        </div>
                    </div>
                    {{-- Member search on mobile (also in always-visible on sm+) --}}
                    <div class="sm:hidden">
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">بحث عن عضو</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <input type="text" name="member_search" value="{{ $memberSearch }}"
                                   placeholder="اسم، رقم وطني، ملف..."
                                   class="w-full pr-10 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                                          focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">الملاحظات</label>
                        <select name="has_notes" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                            <option value="">— الكل —</option>
                            <option value="yes" {{ $hasNotes === 'yes' ? 'selected' : '' }}>لديها ملاحظات</option>
                            <option value="no"  {{ $hasNotes === 'no'  ? 'selected' : '' }}>بدون ملاحظات</option>
                        </select>
                    </div>
                </div>

                {{-- Row 3: numeric ranges --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">عدد الدفعات من</label>
                            <input type="number" name="amount_min" value="{{ $amountMin }}" min="0" placeholder="0"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                        </div>
                        <span class="text-gray-400 pb-2.5">—</span>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">إلى</label>
                            <input type="number" name="amount_max" value="{{ $amountMax }}" min="0" placeholder="∞"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">عدد الأعضاء من</label>
                            <input type="number" name="members_min" value="{{ $membersMin }}" min="0" placeholder="0"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                        </div>
                        <span class="text-gray-400 pb-2.5">—</span>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">إلى</label>
                            <input type="number" name="members_max" value="{{ $membersMax }}" min="0" placeholder="∞"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">المبلغ الإجمالي من (ل.س)</label>
                            <input type="number" name="total_min" value="{{ $totalMin }}" min="0" placeholder="0"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                        </div>
                        <span class="text-gray-400 pb-2.5">—</span>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">إلى</label>
                            <input type="number" name="total_max" value="{{ $totalMax }}" min="0" placeholder="∞"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                        </div>
                    </div>
                </div>

            </div>

            {{-- Active filter chips --}}
            @if($hasFilters)
            <div class="flex flex-wrap items-center gap-2 px-4 pb-3 border-t border-gray-50 pt-3">
                <span class="text-xs text-gray-400 font-medium shrink-0">نشط:</span>
                @if($search)
                    <span class="chip bg-teal-50 text-teal-700 border-teal-100">{{ $search }}</span>
                @endif
                @if($memberSearch)
                    <span class="chip bg-orange-50 text-orange-700 border-orange-100">عضو: {{ $memberSearch }}</span>
                @endif
                @if($operation)
                    <span class="chip bg-violet-50 text-violet-700 border-violet-100">{{ match($operation) { 'add' => 'إضافة', 'subtract' => 'طرح', default => 'تعيين' } }}</span>
                @endif
                @if($appliedBy)
                    <span class="chip bg-indigo-50 text-indigo-700 border-indigo-100">{{ $userList->firstWhere('id', $appliedBy)?->name ?? $appliedBy }}</span>
                @endif
                @if($filterYear)
                    <span class="chip bg-cyan-50 text-cyan-700 border-cyan-100">{{ $filterYear }}</span>
                @endif
                @if($filterMonth)
                    @php $months=[1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر']; @endphp
                    <span class="chip bg-cyan-50 text-cyan-700 border-cyan-100">{{ $months[(int)$filterMonth] ?? $filterMonth }}</span>
                @endif
                @if($dateFrom || $dateTo)
                    <span class="chip bg-amber-50 text-amber-700 border-amber-100">{{ $dateFrom ?: '…' }} — {{ $dateTo ?: '…' }}</span>
                @endif
                @if($hasNotes)
                    <span class="chip bg-yellow-50 text-yellow-700 border-yellow-100">{{ $hasNotes === 'yes' ? 'لديها ملاحظات' : 'بدون ملاحظات' }}</span>
                @endif
                @if($amountMin !== '' || $amountMax !== '')
                    <span class="chip bg-emerald-50 text-emerald-700 border-emerald-100">دفعات: {{ $amountMin ?: '0' }}–{{ $amountMax ?: '∞' }}</span>
                @endif
                @if($membersMin !== '' || $membersMax !== '')
                    <span class="chip bg-sky-50 text-sky-700 border-sky-100">أعضاء: {{ $membersMin ?: '0' }}–{{ $membersMax ?: '∞' }}</span>
                @endif
                @if($totalMin !== '' || $totalMax !== '')
                    <span class="chip bg-rose-50 text-rose-700 border-rose-100">مبلغ: {{ number_format((float)$totalMin) ?: '0' }}–{{ $totalMax !== '' ? number_format((float)$totalMax) : '∞' }} ل.س</span>
                @endif
            </div>
            @endif
        </div>
    </form>
</div>

<style>
.chip { display:inline-flex; align-items:center; font-size:0.7rem; font-weight:700; border-width:1px; border-style:solid; border-radius:9999px; padding:3px 10px; }
</style>

{{-- ══════════════════ BATCH LIST ══════════════════ --}}
@if($batches->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-20 text-center px-6">
        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <p class="text-base font-bold text-gray-300 mb-1">لا توجد دفعات مسجّلة</p>
        <p class="text-sm text-gray-300 mb-5">ابدأ بإنشاء دفعة جماعية جديدة</p>
        <a href="{{ route('members.bulk-payments') }}"
           class="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            إنشاء دفعة جديدة
        </a>
    </div>
@else
    <div class="space-y-3">
        @foreach($batches as $batch)
        @php
            $isAdd  = $batch->operation === 'add';
            $isSub  = $batch->operation === 'subtract';
            $opBg     = $isAdd ? 'bg-emerald-50'   : ($isSub ? 'bg-red-50'    : 'bg-blue-50');
            $opText   = $isAdd ? 'text-emerald-700' : ($isSub ? 'text-red-600'  : 'text-blue-700');
            $opBorder = $isAdd ? 'border-emerald-100': ($isSub ? 'border-red-100' : 'border-blue-100');
            $opDot    = $isAdd ? 'bg-emerald-400'   : ($isSub ? 'bg-red-400'   : 'bg-blue-400');
            $opIcon   = $isAdd ? 'M12 4v16m8-8H4'   : ($isSub ? 'M20 12H4'    : 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z');
            $dateBg   = $batch->payment_date ? 'bg-teal-50 text-teal-700 border-teal-100' : 'bg-gray-50 text-gray-400 border-gray-100';
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition-all group overflow-hidden">

            {{-- Mobile layout --}}
            <div class="sm:hidden">
                {{-- Color bar + header --}}
                <div class="flex items-start">
                    <div class="w-1 self-stretch rounded-r-2xl shrink-0 {{ $opDot }}"></div>
                    <div class="flex-1 px-3 pt-3 pb-2.5">
                        {{-- Top: badges + applied-by --}}
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="inline-flex items-center gap-1 border rounded-lg px-2 py-1 text-[11px] font-bold {{ $dateBg }}">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $batch->payment_date?->format('d/m/Y') ?? 'بدون تاريخ' }}
                                </span>
                                <span class="inline-flex items-center gap-1 border rounded-lg px-2 py-1 text-[11px] font-bold {{ $opBg }} {{ $opText }} {{ $opBorder }}">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $opIcon }}"/></svg>
                                    {{ $batch->operation_label }}
                                </span>
                            </div>
                            {{-- Applied-by avatar --}}
                            <div class="flex items-center gap-1.5 shrink-0">
                                <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-[11px] font-black text-gray-500">
                                    {{ mb_substr($batch->appliedBy?->name ?? '؟', 0, 1) }}
                                </div>
                                <div class="text-right">
                                    <p class="text-[11px] font-semibold text-gray-600 leading-none">{{ $batch->appliedBy?->name ?? 'غير معروف' }}</p>
                                    <p class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $batch->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        {{-- Label + notes --}}
                        <p class="font-bold text-gray-900 text-sm leading-snug">{{ $batch->label ?: 'دفعة #' . $batch->id }}</p>
                        @if($batch->notes)
                            <p class="text-[11px] text-gray-400 mt-0.5 line-clamp-1">{{ $batch->notes }}</p>
                        @endif
                    </div>
                </div>
                {{-- Stats grid --}}
                <div class="grid grid-cols-3 divide-x divide-x-reverse divide-gray-100 border-t border-gray-100 text-center">
                    <div class="py-2 px-1">
                        <p class="text-base font-black text-gray-900">{{ $batch->amount }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">دفعة</p>
                    </div>
                    <div class="py-2 px-1">
                        <p class="text-base font-black text-gray-900">{{ $fmt($batch->members_count) }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">عضو</p>
                    </div>
                    <div class="py-2 px-1">
                        <p class="text-xs font-black text-amber-600 leading-snug">{{ $fmtMoney($batch->total_estimated_amount) }}</p>
                        <p class="text-[10px] text-gray-400 font-medium">المبلغ</p>
                    </div>
                </div>
                {{-- Action buttons — full-width 4-col grid --}}
                <div class="grid grid-cols-4 divide-x divide-x-reverse divide-gray-100 border-t border-gray-100">
                    <button type="button"
                            onclick="openEditBatch({{ $batch->id }}, {{ json_encode($batch->label) }}, {{ json_encode($batch->payment_date?->format('Y-m-d') ?? '') }}, {{ json_encode($batch->notes ?? '') }})"
                            class="flex flex-col items-center justify-center gap-1 py-2.5 text-amber-600 hover:bg-amber-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="text-[10px] font-bold">تعديل</span>
                    </button>
                    <a href="{{ route('members.payment-batches.show', $batch) }}"
                       class="flex flex-col items-center justify-center gap-1 py-2.5 text-teal-600 hover:bg-teal-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="text-[10px] font-bold">تفاصيل</span>
                    </a>
                    <a href="{{ route('members.payment-batches.export', $batch) }}"
                       class="flex flex-col items-center justify-center gap-1 py-2.5 text-emerald-600 hover:bg-emerald-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span class="text-[10px] font-bold">تصدير</span>
                    </a>
                    <button type="button"
                            onclick="openDeleteBatch({{ $batch->id }}, {{ json_encode($batch->label ?: 'دفعة #' . $batch->id) }})"
                            class="flex flex-col items-center justify-center gap-1 py-2.5 text-red-500 hover:bg-red-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span class="text-[10px] font-bold">حذف</span>
                    </button>
                </div>
            </div>

            {{-- Desktop layout --}}
            <div class="hidden sm:flex items-center gap-0 divide-x divide-x-reverse divide-gray-100">
                <div class="w-1.5 self-stretch rounded-r-2xl shrink-0 {{ $opDot }}"></div>
                <div class="flex-1 flex flex-wrap items-center gap-x-6 gap-y-3 px-5 py-4 min-w-0">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 border rounded-xl px-3 py-1.5 text-sm font-black {{ $dateBg }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $batch->payment_date?->format('d/m/Y') ?? 'بدون تاريخ' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 border rounded-xl px-2.5 py-1 text-xs font-bold {{ $opBg }} {{ $opText }} {{ $opBorder }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $opIcon }}"/></svg>
                                {{ $batch->operation_label }}
                            </span>
                        </div>
                        <p class="font-bold text-gray-800 mt-2 text-base truncate">{{ $batch->label ?: 'دفعة #' . $batch->id }}</p>
                        @if($batch->notes)
                            <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $batch->notes }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-6 shrink-0">
                        <div class="text-center">
                            <p class="text-2xl font-black text-gray-900 leading-none">{{ $batch->amount }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">دفعة</p>
                        </div>
                        <div class="w-px h-10 bg-gray-100"></div>
                        <div class="text-center">
                            <p class="text-2xl font-black text-gray-900 leading-none">{{ $fmt($batch->members_count) }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">عضو</p>
                        </div>
                        <div class="w-px h-10 bg-gray-100"></div>
                        <div class="text-center">
                            <p class="text-base font-black text-amber-600 leading-none">{{ $fmtMoney($batch->total_estimated_amount) }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">المبلغ النهائي</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 shrink-0 border-r border-gray-100 pr-6">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-sm font-black text-gray-500 shrink-0">
                            {{ mb_substr($batch->appliedBy?->name ?? '؟', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600">{{ $batch->appliedBy?->name ?? 'غير معروف' }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $batch->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-4 shrink-0 flex items-center gap-1.5">
                    <button type="button"
                            onclick="openEditBatch({{ $batch->id }}, {{ json_encode($batch->label) }}, {{ json_encode($batch->payment_date?->format('Y-m-d') ?? '') }}, {{ json_encode($batch->notes ?? '') }})"
                            class="flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-amber-700 hover:bg-amber-50 hover:border-amber-200 border border-transparent rounded-xl px-3 py-2.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل
                    </button>
                    <a href="{{ route('members.payment-batches.show', $batch) }}"
                       class="flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-teal-700 group-hover:bg-teal-50 hover:border-teal-200 border border-transparent rounded-xl px-3 py-2.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        تفاصيل
                    </a>
                    <a href="{{ route('members.payment-batches.export', $batch) }}"
                       class="flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 hover:border-emerald-200 border border-transparent rounded-xl px-3 py-2.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        تصدير
                    </a>
                    <button type="button"
                            onclick="openDeleteBatch({{ $batch->id }}, {{ json_encode($batch->label ?: 'دفعة #' . $batch->id) }})"
                            class="flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-red-600 hover:bg-red-50 hover:border-red-200 border border-transparent rounded-xl px-3 py-2.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        حذف
                    </button>
                </div>
            </div>

        </div>
        @endforeach
    </div>

    @if($batches->hasPages())
        <div class="mt-5">
            {{ $batches->withQueryString()->links() }}
        </div>
    @endif
@endif

{{-- ══ Edit Batch Modal ══ --}}
<div id="edit-batch-modal"
     class="fixed inset-0 z-50 hidden flex items-center justify-center p-4"
     role="dialog" aria-modal="true">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditBatch()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-800">تعديل بيانات الدفعة</h2>
            </div>
            <button type="button" onclick="closeEditBatch()"
                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form id="edit-batch-form" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">اسم الدفعة</label>
                <input type="text" name="label" id="edit-batch-label"
                       placeholder="مثال: دفعة رمضان 2026"
                       class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition placeholder-gray-300">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">تاريخ الدفع</label>
                <input type="text" id="edit-batch-date" inputmode="numeric" maxlength="10"
                       placeholder="يي.شش.سسسس"
                       oninput="ebFormatDate()"
                       class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition" dir="ltr">
                <input type="hidden" name="payment_date" id="edit-batch-date-h">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1.5">ملاحظات</label>
                <textarea name="notes" id="edit-batch-notes" rows="3"
                          placeholder="أي ملاحظات إضافية..."
                          class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition resize-none placeholder-gray-300"></textarea>
            </div>

            <div class="flex items-center gap-2.5 pt-1">
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    حفظ التعديلات
                </button>
                <button type="button" onclick="closeEditBatch()"
                        class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ Delete Batch Modal ══ --}}
<div id="delete-batch-modal"
     class="fixed inset-0 z-50 hidden flex items-center justify-center p-4"
     role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteBatch()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h2 class="text-base font-bold text-gray-800 mb-1">حذف الدفعة</h2>
            <p class="text-sm text-gray-500 mb-1">هل أنت متأكد من حذف</p>
            <p class="text-sm font-bold text-gray-800 mb-4" id="delete-batch-name"></p>
            <p class="text-xs text-red-500 bg-red-50 border border-red-100 rounded-xl px-3 py-2 mb-5">
                سيتم حذف الدفعة وجميع سجلاتها نهائياً ولا يمكن التراجع.
            </p>
            <div class="flex gap-2.5">
                <form id="delete-batch-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        نعم، احذف
                    </button>
                </form>
                <button type="button" onclick="closeDeleteBatch()"
                        class="flex-1 px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function isoToDisplay(iso) {
    if (!iso) return '';
    const parts = iso.split('-');
    return parts.length === 3 ? parts[2] + '.' + parts[1] + '.' + parts[0] : '';
}

function ebFormatDate() {
    const el = document.getElementById('edit-batch-date');
    let v = el.value.replace(/\D/g, '');
    if (v.length > 2) v = v.slice(0,2) + '.' + v.slice(2);
    if (v.length > 5) v = v.slice(0,5) + '.' + v.slice(5);
    el.value = v.slice(0, 10);
    const hidden = document.getElementById('edit-batch-date-h');
    const parts  = el.value.split('.');
    hidden.value = (parts.length === 3 && parts[2].length === 4)
        ? parts[2] + '-' + parts[1] + '-' + parts[0]
        : '';
}

function openEditBatch(id, label, date, notes) {
    const modal  = document.getElementById('edit-batch-modal');
    const form   = document.getElementById('edit-batch-form');
    const baseUrl = '{{ url("members/payment-batches") }}';
    form.action = baseUrl + '/' + id;
    document.getElementById('edit-batch-label').value  = label || '';
    document.getElementById('edit-batch-date').value   = isoToDisplay(date);
    document.getElementById('edit-batch-date-h').value = date || '';
    document.getElementById('edit-batch-notes').value  = notes || '';
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    document.getElementById('edit-batch-label').focus();
}

function closeEditBatch() {
    document.getElementById('edit-batch-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

function openDeleteBatch(id, label) {
    const baseUrl = '{{ url("members/payment-batches") }}';
    document.getElementById('delete-batch-form').action = baseUrl + '/' + id;
    document.getElementById('delete-batch-name').textContent = label;
    document.getElementById('delete-batch-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteBatch() {
    document.getElementById('delete-batch-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeEditBatch(); closeDeleteBatch(); }
});

function toggleBatchFilters() {
    const body  = document.getElementById('batch-filter-body');
    const arrow = document.getElementById('batch-filter-arrow');
    const hidden = body.classList.toggle('hidden');
    arrow.style.transform = hidden ? '' : 'rotate(180deg)';
}

// Auto-open if filters are active
@if($hasFilters)
document.getElementById('batch-filter-arrow').style.transform = 'rotate(180deg)';
@endif
</script>
@endpush

@endsection
