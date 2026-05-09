@extends('layouts.app')

@section('title', 'إدارة النقاط — مسالك النور')
@section('max-width', 'max-w-full')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-violet-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">إدارة النقاط</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-4 py-3 rounded-xl">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-violet-600 via-purple-500 to-fuchsia-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-8 -right-8 w-40 h-40 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-16 w-56 h-56 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">إدارة جدول النقاط</h1>
            <p class="text-violet-100 text-sm mt-0.5">تعديل وتصفير نقاط المستفيدين بشكل مباشر</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white/15 border border-white/25 rounded-2xl px-5 py-3 text-center min-w-[80px]">
                <p class="text-2xl font-black text-white leading-none">{{ number_format($totalWithScores) }}</p>
                <p class="text-violet-200 text-xs mt-1">لديهم نقاط</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-2xl px-5 py-3 text-center min-w-[80px]">
                <p class="text-2xl font-black text-white leading-none">{{ number_format($totalScore) }}</p>
                <p class="text-violet-200 text-xs mt-1">مجموع النقاط</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
@php
    $componentMeta = [
        'work_score'             => ['label' => 'العمل',    'max' => 2,  'labelActive' => 'text-blue-600',   'active' => 'bg-blue-50 text-blue-700 border-blue-300'],
        'housing_score'          => ['label' => 'السكن',    'max' => 4,  'labelActive' => 'text-teal-600',   'active' => 'bg-teal-50 text-teal-700 border-teal-300'],
        'dependents_score'       => ['label' => 'المعالون', 'max' => 20, 'labelActive' => 'text-amber-600',  'active' => 'bg-amber-50 text-amber-700 border-amber-300'],
        'dependent_status_score' => ['label' => 'الإعالة',  'max' => 2,  'labelActive' => 'text-orange-600', 'active' => 'bg-orange-50 text-orange-700 border-orange-300'],
        'illness_score'          => ['label' => 'المرض',    'max' => 5,  'labelActive' => 'text-rose-600',   'active' => 'bg-rose-50 text-rose-700 border-rose-300'],
        'special_cases_score'    => ['label' => 'الخاصة',   'max' => 10, 'labelActive' => 'text-purple-600', 'active' => 'bg-purple-50 text-purple-700 border-purple-300'],
    ];
    $hasActiveScoreFilter = collect($scoreFilters)->contains(fn($v) => $v !== '');
    $hasAnyFilter = $search || $hasScores !== '' || $hasActiveScoreFilter;
@endphp

<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5 overflow-hidden">
    <form method="GET" action="{{ route('members.score-manager') }}">

        {{-- Main filter row --}}
        <div class="flex flex-wrap gap-3 items-end p-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">بحث</label>
                <div class="relative">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="الاسم، رقم الاضبارة..."
                           class="w-full pr-9 pl-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">النقاط</label>
                <select name="has_scores"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:ring-2 focus:ring-violet-400 focus:outline-none min-w-[130px]">
                    <option value="">الكل</option>
                    <option value="1" {{ $hasScores === '1' ? 'selected' : '' }}>لديهم نقاط</option>
                    <option value="0" {{ $hasScores === '0' ? 'selected' : '' }}>بدون نقاط</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">ترتيب</label>
                <select name="sort"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:ring-2 focus:ring-violet-400 focus:outline-none min-w-[150px]">
                    <option value="dossier"    {{ $sortBy === 'dossier'    ? 'selected' : '' }}>رقم الاضبارة</option>
                    <option value="score_desc" {{ $sortBy === 'score_desc' ? 'selected' : '' }}>الأعلى نقاطاً</option>
                    <option value="score_asc"  {{ $sortBy === 'score_asc'  ? 'selected' : '' }}>الأقل نقاطاً</option>
                    <option value="fv_desc"    {{ $sortBy === 'fv_desc'    ? 'selected' : '' }}>أعلى مبلغ جولة</option>
                    <option value="fv_asc"     {{ $sortBy === 'fv_asc'     ? 'selected' : '' }}>أقل مبلغ جولة</option>
                    <option value="name"       {{ $sortBy === 'name'       ? 'selected' : '' }}>الاسم</option>
                </select>
            </div>
            <div class="flex gap-2 items-center">
                <button type="submit"
                        class="bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                    تطبيق
                </button>
                @if($hasAnyFilter)
                    <a href="{{ route('members.score-manager') }}"
                       class="text-sm text-gray-400 hover:text-gray-600 border border-gray-200 hover:bg-gray-50 px-4 py-2.5 rounded-xl transition-colors">
                        مسح الكل
                    </a>
                @endif
            </div>
        </div>

        {{-- Per-component score filters --}}
        <div class="border-t border-gray-100 px-4 py-3 bg-gray-50/40">
            <div class="flex items-center gap-2 mb-2.5">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">فلتر حسب نقاط كل قسم</span>
                @if($hasActiveScoreFilter)
                    <span class="text-xs bg-violet-100 text-violet-700 font-semibold px-2 py-0.5 rounded-full">مفعّل</span>
                @endif
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                @foreach($componentMeta as $col => $meta)
                @php $current = $scoreFilters[$col] ?? ''; @endphp
                <div>
                    <label class="block text-xs font-semibold mb-1 {{ $current !== '' ? $meta['labelActive'] : 'text-gray-500' }}">
                        {{ $meta['label'] }}
                        <span class="font-normal text-gray-300">/{{ $meta['max'] }}</span>
                    </label>
                    <select name="sf_{{ $col }}"
                            class="w-full text-xs border rounded-lg px-2 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 transition
                                {{ $current !== '' ? $meta['active'] : 'bg-white border-gray-200 text-gray-700' }}">
                        <option value="">الكل</option>
                        <option value="0" {{ $current === '0' ? 'selected' : '' }}>بدون نقاط</option>
                        @for($i = 1; $i <= $meta['max']; $i++)
                            <option value="{{ $i }}" {{ (string)$current === (string)$i ? 'selected' : '' }}>
                                {{ $i }}+
                            </option>
                        @endfor
                    </select>
                </div>
                @endforeach
            </div>
        </div>

    </form>
</div>

{{-- Bulk score update panel --}}
<div id="bulk-bar" class="bg-white border border-violet-200 rounded-2xl shadow-sm mb-5 overflow-hidden">
    <div class="flex items-center gap-2 px-4 py-3 border-b border-violet-100 bg-violet-50/50">
        <div class="w-6 h-6 rounded-lg bg-violet-100 flex items-center justify-center shrink-0">
            <svg class="w-3.5 h-3.5 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <span class="text-xs font-bold text-violet-700">تعديل جماعي للنقاط</span>
        <span id="bulk-count" class="text-xs bg-violet-100 text-violet-700 font-semibold px-2 py-0.5 rounded-full">0 محدد</span>
        <button type="button" onclick="selectAllPages()"
                class="text-xs font-semibold text-violet-600 bg-violet-50 hover:bg-violet-100 border border-violet-200 px-3 py-1 rounded-lg transition-colors">
            تحديد جميع الصفحات ({{ number_format(count($allIds)) }})
        </button>
        <button type="button" onclick="clearSelection()" class="mr-auto text-xs text-gray-400 hover:text-red-500 underline transition-colors">إلغاء التحديد</button>
    </div>
    <form method="POST" action="{{ route('members.bulk-score-update') }}" id="bulk-form" onsubmit="return injectBulkIds()">
        @csrf
        <div id="bulk-ids-container"></div>
        <div class="p-4 flex flex-wrap items-end gap-3">
            <div class="flex flex-wrap gap-2 flex-1">
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-bold text-blue-600 text-center">عمل /2</label>
                    <input type="number" name="work_score" min="0" max="2" placeholder="—"
                           class="w-14 text-center text-xs border border-blue-200 bg-blue-50 rounded-lg px-1 py-1.5 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-bold text-teal-600 text-center">سكن /4</label>
                    <input type="number" name="housing_score" min="0" max="4" placeholder="—"
                           class="w-14 text-center text-xs border border-teal-200 bg-teal-50 rounded-lg px-1 py-1.5 focus:ring-2 focus:ring-teal-400 focus:outline-none">
                </div>
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-bold text-amber-600 text-center">معالون /20</label>
                    <input type="number" name="dependents_score" min="0" max="20" placeholder="—"
                           class="w-16 text-center text-xs border border-amber-200 bg-amber-50 rounded-lg px-1 py-1.5 focus:ring-2 focus:ring-amber-400 focus:outline-none">
                </div>
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-bold text-orange-600 text-center">إعالة /2</label>
                    <input type="number" name="dependent_status_score" min="0" max="2" placeholder="—"
                           class="w-14 text-center text-xs border border-orange-200 bg-orange-50 rounded-lg px-1 py-1.5 focus:ring-2 focus:ring-orange-400 focus:outline-none">
                </div>
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-bold text-rose-600 text-center">مرض /5</label>
                    <input type="number" name="illness_score" min="0" max="5" placeholder="—"
                           class="w-14 text-center text-xs border border-rose-200 bg-rose-50 rounded-lg px-1 py-1.5 focus:ring-2 focus:ring-rose-400 focus:outline-none">
                </div>
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-bold text-purple-600 text-center">خاصة /10</label>
                    <input type="number" name="special_cases_score" min="0" max="10" placeholder="—"
                           class="w-14 text-center text-xs border border-purple-200 bg-purple-50 rounded-lg px-1 py-1.5 focus:ring-2 focus:ring-purple-400 focus:outline-none">
                </div>
                <div class="w-px h-8 bg-gray-200 self-end mb-0.5 shrink-0"></div>
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-bold text-emerald-600 text-center">إضافة</label>
                    <input type="number" name="score_addition" min="0" placeholder="—"
                           class="w-14 text-center text-xs border border-emerald-200 bg-emerald-50 rounded-lg px-1 py-1.5 focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                </div>
                <div class="flex flex-col gap-0.5">
                    <label class="text-[10px] font-bold text-red-500 text-center">انقاص</label>
                    <input type="number" name="score_deduction" min="0" placeholder="—"
                           class="w-14 text-center text-xs border border-red-200 bg-red-50 rounded-lg px-1 py-1.5 focus:ring-2 focus:ring-red-400 focus:outline-none">
                </div>
            </div>
            <button type="submit"
                    class="bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors shrink-0">
                تطبيق على المحددين
            </button>
        </div>
    </form>
</div>

{{-- Custom amount exclusion panel --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5 overflow-hidden">
    <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100 bg-gray-50/50">
        <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M12 7h.01M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        <span class="text-xs font-bold text-gray-600">تخريج المبلغ النهائي باستثناء مكونات</span>
        <span id="excl-badge" class="hidden text-xs bg-indigo-100 text-indigo-700 font-semibold px-2 py-0.5 rounded-full">مفعّل</span>
        <button onclick="resetExclusions()" id="excl-reset" class="hidden mr-auto text-xs text-gray-400 hover:text-red-500 underline transition-colors">إعادة تعيين</button>
    </div>
    <div class="p-4">
        <div class="flex flex-wrap gap-2 mb-3">
            <button type="button" data-key="ws"  onclick="toggleExclude(this)"
                    class="excl-chip inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-blue-200 bg-blue-50 text-blue-700 transition-all hover:opacity-80 select-none">
                <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>العمل
            </button>
            <button type="button" data-key="hs"  onclick="toggleExclude(this)"
                    class="excl-chip inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-teal-200 bg-teal-50 text-teal-700 transition-all hover:opacity-80 select-none">
                <span class="w-2 h-2 rounded-full bg-teal-400 inline-block"></span>السكن
            </button>
            <button type="button" data-key="ds"  onclick="toggleExclude(this)"
                    class="excl-chip inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-amber-200 bg-amber-50 text-amber-700 transition-all hover:opacity-80 select-none">
                <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>المعالون
            </button>
            <button type="button" data-key="dss" onclick="toggleExclude(this)"
                    class="excl-chip inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-orange-200 bg-orange-50 text-orange-700 transition-all hover:opacity-80 select-none">
                <span class="w-2 h-2 rounded-full bg-orange-400 inline-block"></span>الإعالة
            </button>
            <button type="button" data-key="is"  onclick="toggleExclude(this)"
                    class="excl-chip inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 transition-all hover:opacity-80 select-none">
                <span class="w-2 h-2 rounded-full bg-rose-400 inline-block"></span>المرض
            </button>
            <button type="button" data-key="ss"  onclick="toggleExclude(this)"
                    class="excl-chip inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-purple-200 bg-purple-50 text-purple-700 transition-all hover:opacity-80 select-none">
                <span class="w-2 h-2 rounded-full bg-purple-400 inline-block"></span>الحالات الخاصة
            </button>
            <button type="button" data-key="add" onclick="toggleExclude(this)"
                    class="excl-chip inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 transition-all hover:opacity-80 select-none">
                <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>الإضافات
            </button>
            <button type="button" data-key="fv" onclick="toggleExclude(this)"
                    class="excl-chip inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl border border-sky-200 bg-sky-50 text-sky-700 transition-all hover:opacity-80 select-none">
                <span class="w-2 h-2 rounded-full bg-sky-400 inline-block"></span>مبلغ الجولة الميدانية
            </button>
        </div>
        <div id="excl-summary" class="hidden flex flex-wrap items-center gap-5 pt-3 border-t border-dashed border-gray-100">
            <div>
                <span class="text-xs text-gray-400">مجموع النقاط المخصصة</span>
                <span id="excl-total-pts" class="text-sm font-black text-indigo-700 mx-1">0</span>
                <span class="text-xs text-gray-400">نقطة</span>
            </div>
            <div>
                <span class="text-xs text-gray-400">مجموع مبالغ النقاط</span>
                <span id="excl-total-amt" class="text-sm font-black text-indigo-700 mx-1">0</span>
                <span class="text-xs text-gray-400">ل.س</span>
            </div>
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-1.5 flex items-center gap-2">
                <span class="text-xs text-indigo-500 font-medium">الإجمالي الكلي المخصص</span>
                <span id="excl-total-grand" class="text-sm font-black text-indigo-700">0</span>
                <span class="text-xs text-indigo-400">ل.س</span>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between gap-2 px-5 py-3.5 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="text-sm font-bold text-gray-700">{{ number_format($members->total()) }} مستفيد</span>
            </div>
            <a id="export-link"
               href="{{ route('members.score-manager.export', request()->except('page')) }}"
               data-base="{{ route('members.score-manager.export', request()->except('page')) }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span id="export-label">تصدير Excel</span>
            </a>
        </div>
        {{-- Legend --}}
        <div class="hidden md:flex items-center gap-3 text-xs text-gray-400">
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-blue-200 inline-block"></span>عمل</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-teal-200 inline-block"></span>سكن</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-amber-200 inline-block"></span>معالون</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-orange-200 inline-block"></span>إعالة</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-rose-200 inline-block"></span>مرض</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-purple-200 inline-block"></span>خاصة</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/60 text-xs">
                    <th class="w-10 px-3 py-3 text-center">
                        <input type="checkbox" id="check-all" onchange="toggleAll(this)"
                               class="rounded border-gray-300 text-violet-600 focus:ring-violet-400 cursor-pointer">
                    </th>
                    <th class="font-semibold text-gray-500 px-4 py-3 text-right whitespace-nowrap">المستفيد</th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap">
                        <span class="text-blue-500">عمل</span>
                        <span class="text-gray-300 font-normal">/2</span>
                    </th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap">
                        <span class="text-teal-500">سكن</span>
                        <span class="text-gray-300 font-normal">/4</span>
                    </th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap">
                        <span class="text-amber-500">معالون</span>
                        <span class="text-gray-300 font-normal">/20</span>
                    </th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap">
                        <span class="text-orange-500">إعالة</span>
                        <span class="text-gray-300 font-normal">/2</span>
                    </th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap">
                        <span class="text-rose-500">مرض</span>
                        <span class="text-gray-300 font-normal">/5</span>
                    </th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap">
                        <span class="text-purple-500">خاصة</span>
                        <span class="text-gray-300 font-normal">/10</span>
                    </th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap text-emerald-500">+ إضافة</th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap text-red-400">− انقاص</th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap bg-violet-50 text-violet-700">الإجمالي</th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap text-gray-500 col-amount-header">
                        <span id="amount-col-label">المبلغ (ل.س)</span>
                    </th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap text-sky-600 bg-sky-50/50">مبلغ الجولة</th>
                    <th class="font-semibold text-center px-3 py-3 whitespace-nowrap text-indigo-600 bg-indigo-50/40">
                        <span id="total-col-label">الإجمالي الكلي</span>
                    </th>
                    <th class="px-3 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($members as $member)
                @php
                    $ws  = (int)($member->work_score ?? 0);
                    $hs  = (int)($member->housing_score ?? 0);
                    $ds  = (int)($member->dependents_score ?? 0);
                    $dss = (int)($member->dependent_status_score ?? 0);
                    $is  = (int)($member->illness_score ?? 0);
                    $ss  = (int)($member->special_cases_score ?? 0);
                    $add = (int)($member->score_addition ?? 0);
                    $ded = (int)($member->score_deduction ?? 0);
                    $tot = (int)($member->total_score ?? 0);
                    $fv  = (float)($member->field_visit_amount ?? 0);
                    $hasAnyScore = $tot > 0 || $add > 0 || $ded > 0;
                    $grandTotal  = (float)($member->estimated_amount ?? 0) + $fv;
                @endphp
                <tr class="hover:bg-violet-50/20 transition-colors member-row"
                    data-ws="{{ $ws }}" data-hs="{{ $hs }}" data-ds="{{ $ds }}"
                    data-dss="{{ $dss }}" data-is="{{ $is }}" data-ss="{{ $ss }}"
                    data-add="{{ $add }}" data-ded="{{ $ded }}" data-fv="{{ $fv }}">
                    <td class="px-3 py-3 text-center">
                        <input type="checkbox" value="{{ $member->id }}" class="member-check rounded border-gray-300 text-violet-600 focus:ring-violet-400 cursor-pointer" onchange="updateBulkBar()">
                    </td>
                    {{-- Name --}}
                    <td class="px-4 py-3 whitespace-nowrap">
                        <a href="{{ route('members.show', $member->id) }}"
                           class="font-semibold text-gray-800 hover:text-violet-700 transition-colors block leading-snug">
                            {{ $member->full_name }}
                        </a>
                        <span class="text-xs text-gray-400 font-mono">{{ $member->dossier_number ?? '—' }}</span>
                    </td>

                    {{-- Score cells --}}
                    @php
                        $scoreCells = [
                            [$ws,  'bg-blue-100 text-blue-700'],
                            [$hs,  'bg-teal-100 text-teal-700'],
                            [$ds,  'bg-amber-100 text-amber-700'],
                            [$dss, 'bg-orange-100 text-orange-700'],
                            [$is,  'bg-rose-100 text-rose-700'],
                            [$ss,  'bg-purple-100 text-purple-700'],
                        ];
                    @endphp
                    @foreach($scoreCells as [$val, $activeClass])
                    <td class="px-3 py-3 text-center">
                        @if($val > 0)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold {{ $activeClass }}">
                                {{ $val }}
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-medium bg-gray-50 text-gray-300">
                                0
                            </span>
                        @endif
                    </td>
                    @endforeach

                    {{-- Addition --}}
                    <td class="px-3 py-3 text-center">
                        @if($add > 0)
                            <span class="inline-flex items-center justify-center gap-0.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-2.5 py-1"
                                  title="{{ $member->score_addition_reason }}">
                                +{{ $add }}
                            </span>
                        @else
                            <span class="text-gray-200 text-sm">—</span>
                        @endif
                    </td>

                    {{-- Deduction --}}
                    <td class="px-3 py-3 text-center">
                        @if($ded > 0)
                            <span class="inline-flex items-center justify-center gap-0.5 text-xs font-bold text-red-600 bg-red-50 border border-red-100 rounded-lg px-2.5 py-1"
                                  title="{{ $member->score_deduction_reason }}">
                                −{{ $ded }}
                            </span>
                        @else
                            <span class="text-gray-200 text-sm">—</span>
                        @endif
                    </td>

                    {{-- Total --}}
                    <td class="px-3 py-3 text-center bg-violet-50/40 cell-total">
                        @if($tot > 0)
                            <span class="inline-flex items-center justify-center text-base font-black text-violet-700 w-10 h-8 rounded-xl bg-violet-100">
                                {{ $tot }}
                            </span>
                        @else
                            <span class="text-gray-300 text-sm font-medium">0</span>
                        @endif
                    </td>

                    {{-- Estimated amount (from score) --}}
                    <td class="px-3 py-3 text-center whitespace-nowrap cell-amount"
                        data-stored="{{ $member->estimated_amount ? number_format((float)$member->estimated_amount, 0) : '' }}">
                        @if($member->estimated_amount)
                            <span class="text-gray-700 font-semibold text-xs">{{ number_format((float)$member->estimated_amount, 0) }}</span>
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>

                    {{-- Field visit amount --}}
                    <td class="px-3 py-3 text-center whitespace-nowrap bg-sky-50/30">
                        @if($fv != 0)
                            <span class="text-xs font-bold {{ $fv > 0 ? 'text-sky-700' : 'text-red-500' }}">
                                {{ number_format($fv, 0) }}
                            </span>
                        @else
                            <span class="text-gray-200 text-sm">—</span>
                        @endif
                    </td>

                    {{-- Grand total (score amount + field visit) --}}
                    <td class="px-3 py-3 text-center whitespace-nowrap bg-indigo-50/30 cell-grand"
                        data-stored-grand="{{ $grandTotal != 0 ? number_format($grandTotal, 0) : '' }}">
                        @if($grandTotal != 0)
                            <span class="text-xs font-black text-indigo-700">{{ number_format($grandTotal, 0) }}</span>
                        @else
                            <span class="text-gray-200 text-sm">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-1 justify-center">
                            <button type="button"
                                    onclick="openEdit({{ json_encode([
                                        'id'                     => $member->id,
                                        'name'                   => $member->full_name,
                                        'work_score'             => $ws,
                                        'housing_score'          => $hs,
                                        'dependents_score'       => $ds,
                                        'dependent_status_score' => $dss,
                                        'illness_score'          => $is,
                                        'special_cases_score'    => $ss,
                                        'score_addition'         => $add,
                                        'score_addition_reason'  => $member->score_addition_reason ?? '',
                                        'score_deduction'        => $ded,
                                        'score_deduction_reason' => $member->score_deduction_reason ?? '',
                                        'url'                    => route('members.score.update', $member->id),
                                    ]) }})"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-violet-400 hover:text-violet-700 hover:bg-violet-100 transition-colors"
                                    title="تعديل النقاط">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @if($hasAnyScore)
                            <form method="POST" action="{{ route('members.score.reset', $member->id) }}"
                                  onsubmit="return confirm('تصفير جميع نقاط {{ addslashes($member->full_name) }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors"
                                        title="تصفير النقاط">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                            </form>
                            @else
                            <span class="w-8 h-8"></span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($members->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center text-gray-400">
            <svg class="w-10 h-10 mb-3 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <p class="text-sm font-medium">لا توجد نتائج</p>
        </div>
    @endif

    @if($members->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $members->links() }}
        </div>
    @endif
</div>

{{-- Edit Modal --}}
<div id="modal-score" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.45)"
     onclick="if(event.target===this) closeModal()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[92vh] overflow-y-auto" onclick="event.stopPropagation()">

        {{-- Modal header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white z-10 rounded-t-2xl">
            <div>
                <h2 class="text-base font-bold text-gray-800">تعديل النقاط</h2>
                <p id="modal-member-name" class="text-xs text-violet-500 font-medium mt-0.5"></p>
            </div>
            <button onclick="closeModal()" class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="modal-form" method="POST" class="p-6 space-y-5">
            @csrf @method('PATCH')

            {{-- Score components --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">مكونات النقاط</p>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-3">
                        <label class="block text-xs font-bold text-blue-600 mb-1.5">العمل <span class="text-blue-300 font-normal">(0 – 2)</span></label>
                        <input type="number" name="work_score" id="f-work" min="0" max="2"
                               class="w-full border border-blue-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none" oninput="calcPreview()">
                    </div>
                    <div class="bg-teal-50/50 border border-teal-100 rounded-xl p-3">
                        <label class="block text-xs font-bold text-teal-600 mb-1.5">السكن <span class="text-teal-300 font-normal">(0 – 4)</span></label>
                        <input type="number" name="housing_score" id="f-housing" min="0" max="4"
                               class="w-full border border-teal-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-teal-400 focus:outline-none" oninput="calcPreview()">
                    </div>
                    <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-3">
                        <label class="block text-xs font-bold text-amber-600 mb-1.5">المعالون <span class="text-amber-300 font-normal">(0 – 20)</span></label>
                        <input type="number" name="dependents_score" id="f-dependents" min="0" max="20"
                               class="w-full border border-amber-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-amber-400 focus:outline-none" oninput="calcPreview()">
                    </div>
                    <div class="bg-orange-50/50 border border-orange-100 rounded-xl p-3">
                        <label class="block text-xs font-bold text-orange-600 mb-1.5">حالة الإعالة <span class="text-orange-300 font-normal">(0 – 2)</span></label>
                        <input type="number" name="dependent_status_score" id="f-dep-status" min="0" max="2"
                               class="w-full border border-orange-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-orange-400 focus:outline-none" oninput="calcPreview()">
                    </div>
                    <div class="bg-rose-50/50 border border-rose-100 rounded-xl p-3">
                        <label class="block text-xs font-bold text-rose-600 mb-1.5">المرض <span class="text-rose-300 font-normal">(0 – 5)</span></label>
                        <input type="number" name="illness_score" id="f-illness" min="0" max="5"
                               class="w-full border border-rose-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-rose-400 focus:outline-none" oninput="calcPreview()">
                    </div>
                    <div class="bg-purple-50/50 border border-purple-100 rounded-xl p-3">
                        <label class="block text-xs font-bold text-purple-600 mb-1.5">الحالات الخاصة <span class="text-purple-300 font-normal">(0 – 10)</span></label>
                        <input type="number" name="special_cases_score" id="f-special" min="0" max="10"
                               class="w-full border border-purple-200 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-400 focus:outline-none" oninput="calcPreview()">
                    </div>
                </div>
            </div>

            {{-- Adjustments --}}
            <div class="border border-gray-100 rounded-xl p-4 space-y-3">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">التعديلات الخاصة</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-emerald-600 mb-1.5">إضافة نقاط</label>
                        <input type="number" name="score_addition" id="f-addition" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-emerald-400 focus:outline-none focus:bg-white" oninput="calcPreview()">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-red-500 mb-1.5">انقاص نقاط</label>
                        <input type="number" name="score_deduction" id="f-deduction" min="0"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-red-400 focus:outline-none focus:bg-white" oninput="calcPreview()">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-emerald-600 mb-1.5">سبب الإضافة</label>
                        <input type="text" name="score_addition_reason" id="f-add-reason"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-emerald-400 focus:outline-none focus:bg-white"
                               placeholder="اختياري...">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-red-500 mb-1.5">سبب الانقاص</label>
                        <input type="text" name="score_deduction_reason" id="f-ded-reason"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-red-400 focus:outline-none focus:bg-white"
                               placeholder="اختياري...">
                    </div>
                </div>
            </div>

            {{-- Live total preview --}}
            <div class="bg-gradient-to-l from-violet-600 to-purple-600 rounded-2xl px-5 py-4 flex items-center justify-between shadow-sm">
                <div>
                    <p class="text-violet-200 text-xs font-medium">النقاط الإجمالية</p>
                    <p id="preview-amount" class="text-violet-300 text-xs mt-0.5"></p>
                </div>
                <div class="text-left">
                    <span id="preview-total" class="text-4xl font-black text-white">0</span>
                    <span class="text-violet-300 text-sm mr-1">نقطة</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-violet-600 hover:bg-violet-700 text-white font-bold py-3 rounded-xl transition-colors text-sm">
                    حفظ التعديلات
                </button>
                <button type="button" onclick="closeModal()"
                        class="px-5 py-3 border border-gray-200 text-gray-500 hover:bg-gray-50 font-medium rounded-xl transition-colors text-sm">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.excl-chip.chip-off {
    opacity: 0.45;
    text-decoration: line-through;
    background-color: #f3f4f6 !important;
    border-color:     #e5e7eb !important;
    color:            #9ca3af !important;
}
</style>

<script>
/* ── Exclusion panel ── */
const allPageIds = @json($allIds);
let allPagesSelected = false;

const excluded = { ws: false, hs: false, ds: false, dss: false, is: false, ss: false, add: false, fv: false };

function toggleExclude(btn) {
    const key = btn.dataset.key;
    excluded[key] = !excluded[key];
    btn.classList.toggle('chip-off', excluded[key]);
    recalcAll();
}

function resetExclusions() {
    Object.keys(excluded).forEach(k => excluded[k] = false);
    document.querySelectorAll('.excl-chip').forEach(btn => btn.classList.remove('chip-off'));
    recalcAll();
}

function recalcAll() {
    const anyExcluded = Object.values(excluded).some(v => v);
    document.getElementById('excl-badge').classList.toggle('hidden', !anyExcluded);
    document.getElementById('excl-reset').classList.toggle('hidden', !anyExcluded);
    document.getElementById('excl-summary').classList.toggle('hidden', !anyExcluded);

    document.getElementById('amount-col-label').textContent  = anyExcluded ? 'المبلغ المخصص (ل.س)' : 'المبلغ (ل.س)';
    document.getElementById('total-col-label').textContent   = anyExcluded ? 'الإجمالي المخصص'      : 'الإجمالي الكلي';

    let pagePoints = 0, pageAmount = 0, pageGrand = 0;

    document.querySelectorAll('tr[data-ws]').forEach(row => {
        const ws  = +row.dataset.ws;
        const hs  = +row.dataset.hs;
        const ds  = +row.dataset.ds;
        const dss = +row.dataset.dss;
        const is_ = +row.dataset.is;
        const ss  = +row.dataset.ss;
        const add = +row.dataset.add;
        const ded = +row.dataset.ded;
        const fv  = +row.dataset.fv;

        const totalCell = row.querySelector('.cell-total');
        const amountCell = row.querySelector('.cell-amount');
        const grandCell  = row.querySelector('.cell-grand');

        if (anyExcluded) {
            const pts = Math.max(0,
                (excluded.ws  ? 0 : ws)  +
                (excluded.hs  ? 0 : hs)  +
                (excluded.ds  ? 0 : ds)  +
                (excluded.dss ? 0 : dss) +
                (excluded.is  ? 0 : is_) +
                (excluded.ss  ? 0 : ss)  +
                (excluded.add ? 0 : add) -
                ded
            );
            const amt   = pts * 500;
            const fvAmt = excluded.fv ? 0 : fv;
            const grand = amt + fvAmt;

            pagePoints += pts;
            pageAmount += amt;
            pageGrand  += grand;

            totalCell.innerHTML = pts > 0
                ? `<span class="inline-flex items-center justify-center text-base font-black text-indigo-700 w-10 h-8 rounded-xl bg-indigo-100">${pts}</span>`
                : `<span class="text-gray-300 text-sm font-medium">0</span>`;

            amountCell.innerHTML = amt > 0
                ? `<span class="text-indigo-700 font-bold text-xs">${amt.toLocaleString('ar-SY')}</span>`
                : `<span class="text-gray-300 text-sm">—</span>`;

            grandCell.innerHTML = grand != 0
                ? `<span class="text-xs font-black text-indigo-700">${grand.toLocaleString('ar-SY')}</span>`
                : `<span class="text-gray-200 text-sm">—</span>`;
        } else {
            const storedAmt   = amountCell.dataset.stored;
            const storedGrand = grandCell.dataset.storedGrand;
            const origPts = Math.max(0, ws + hs + ds + dss + is_ + ss + add - ded);

            totalCell.innerHTML = origPts > 0
                ? `<span class="inline-flex items-center justify-center text-base font-black text-violet-700 w-10 h-8 rounded-xl bg-violet-100">${origPts}</span>`
                : `<span class="text-gray-300 text-sm font-medium">0</span>`;

            amountCell.innerHTML = storedAmt
                ? `<span class="text-gray-700 font-semibold text-xs">${storedAmt}</span>`
                : `<span class="text-gray-300 text-sm">—</span>`;

            grandCell.innerHTML = storedGrand
                ? `<span class="text-xs font-black text-indigo-700">${storedGrand}</span>`
                : `<span class="text-gray-200 text-sm">—</span>`;
        }
    });

    document.getElementById('excl-total-pts').textContent = pagePoints.toLocaleString('ar-SY');
    document.getElementById('excl-total-amt').textContent = pageAmount.toLocaleString('ar-SY');
    document.getElementById('excl-total-grand') && (document.getElementById('excl-total-grand').textContent = pageGrand.toLocaleString('ar-SY'));

    updateExportLink();
}

function updateExportLink() {
    const link = document.getElementById('export-link');
    if (!link) return;
    const base   = link.dataset.base;
    const url    = new URL(base, window.location.origin);
    const active = Object.keys(excluded).filter(k => excluded[k]);
    url.searchParams.delete('excl[]');
    active.forEach(k => url.searchParams.append('excl[]', k));
    link.href = url.toString();

    const label = document.getElementById('export-label');
    if (label) label.textContent = active.length > 0
        ? `تصدير مخصص (${active.length} استثناء)`
        : 'تصدير Excel';
}

/* ── Score edit modal ── */
function openEdit(data) {
    document.getElementById('modal-member-name').textContent = data.name;
    document.getElementById('modal-form').action = data.url;
    document.getElementById('f-work').value        = data.work_score;
    document.getElementById('f-housing').value     = data.housing_score;
    document.getElementById('f-dependents').value  = data.dependents_score;
    document.getElementById('f-dep-status').value  = data.dependent_status_score;
    document.getElementById('f-illness').value     = data.illness_score;
    document.getElementById('f-special').value     = data.special_cases_score;
    document.getElementById('f-addition').value    = data.score_addition;
    document.getElementById('f-add-reason').value  = data.score_addition_reason || '';
    document.getElementById('f-deduction').value   = data.score_deduction;
    document.getElementById('f-ded-reason').value  = data.score_deduction_reason || '';
    calcPreview();
    document.getElementById('modal-score').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal-score').classList.add('hidden');
    document.body.style.overflow = '';
}

function calcPreview() {
    const clamp = (v, max) => Math.min(Math.max(parseInt(v) || 0, 0), max);
    const ws  = clamp(document.getElementById('f-work').value, 2);
    const hs  = clamp(document.getElementById('f-housing').value, 4);
    const ds  = clamp(document.getElementById('f-dependents').value, 20);
    const dss = clamp(document.getElementById('f-dep-status').value, 2);
    const is_ = clamp(document.getElementById('f-illness').value, 5);
    const ss  = clamp(document.getElementById('f-special').value, 10);
    const add = Math.max(0, parseInt(document.getElementById('f-addition').value) || 0);
    const ded = Math.max(0, parseInt(document.getElementById('f-deduction').value) || 0);
    const total = Math.max(0, ws + hs + ds + dss + is_ + ss + add - ded);
    document.getElementById('preview-total').textContent  = total;
    document.getElementById('preview-amount').textContent = total > 0
        ? 'المبلغ المقدر: ' + (total * 500).toLocaleString('ar-SY') + ' ل.س'
        : '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

/* ── Bulk selection ── */
function getCheckedIds() {
    return [...document.querySelectorAll('.member-check:checked')].map(c => c.value);
}

function updateBulkBar() {
    const ids = getCheckedIds();
    const bar = document.getElementById('bulk-bar');
    // panel always visible

    document.getElementById('bulk-count').textContent = ids.length + ' محدد';

    const all  = document.querySelectorAll('.member-check');
    const ca   = document.getElementById('check-all');
    if (ca) {
        ca.indeterminate = ids.length > 0 && ids.length < all.length;
        ca.checked       = ids.length === all.length && all.length > 0;
    }
}

function toggleAll(master) {
    allPagesSelected = false;
    document.querySelectorAll('.member-check').forEach(c => c.checked = master.checked);
    updateBulkBar();
}

function selectAllPages() {
    allPagesSelected = true;
    document.querySelectorAll('.member-check').forEach(c => c.checked = true);
    const ca = document.getElementById('check-all');
    if (ca) { ca.checked = true; ca.indeterminate = false; }
    document.getElementById('bulk-count').textContent = allPageIds.length + ' محدد (جميع الصفحات)';
}

function clearSelection() {
    allPagesSelected = false;
    document.querySelectorAll('.member-check').forEach(c => c.checked = false);
    const ca = document.getElementById('check-all');
    if (ca) { ca.checked = false; ca.indeterminate = false; }
    updateBulkBar();
}

function injectBulkIds() {
    const ids = allPagesSelected ? allPageIds : getCheckedIds();
    if (ids.length === 0) { alert('حدد مستفيداً واحداً على الأقل.'); return false; }

    const inputs = document.querySelectorAll('#bulk-form input[type=number]');
    const hasValue = [...inputs].some(i => i.value !== '');
    if (!hasValue) { alert('أدخل قيمة لحقل واحد على الأقل.'); return false; }

    const container = document.getElementById('bulk-ids-container');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'ids[]';
        inp.value = id;
        container.appendChild(inp);
    });

    return confirm(`تطبيق التعديل على ${ids.length} مستفيد؟`);
}
</script>

@endsection
