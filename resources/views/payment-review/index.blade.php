@extends('layouts.app')

@section('title', 'مراجعة بيانات الدفع — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">مراجعة بيانات الدفع</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-violet-600 via-purple-500 to-indigo-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">مراجعة بيانات الدفع</h1>
            <p class="text-violet-100 text-sm mt-0.5">مقارنة payment_info مع payment_info_AI وتسجيل نتيجة المراجعة</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('payment-review.export-matched') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                تصدير المتطابقين
            </a>
            <a href="{{ route('payment-review.import.show') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                استيراد الآيبان
            </a>
            <a href="{{ route('payment-review.duplicate-ibans') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                تكرار الآيبانات
            </a>
        </div>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- Filter panel --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100">
        <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-gray-700">الفلاتر والبحث</span>
    </div>

    <div class="p-5 space-y-4">

        {{-- Search --}}
        <form method="GET" action="{{ route('payment-review.index') }}" id="filter-form">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="hidden" name="sham_cash" value="{{ $shamCash }}">
            <input type="hidden" name="date_from" value="{{ $dateFrom }}">
            <input type="hidden" name="date_to" value="{{ $dateTo }}">
            <div class="relative">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="بحث بالاسم أو رقم الملف..."
                       class="w-full pr-10 pl-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300">
            </div>
        </form>

        {{-- Filter buttons --}}
        @php
        $autoFilters = [
            'all'              => ['label' => 'الكل',                    'count' => $totalCount,           'active' => 'bg-gray-800 text-white border-gray-800',        'inactive' => 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'],
            'auto_match'       => ['label' => 'يتطابق تلقائياً',        'count' => $autoMatchCount,       'active' => 'bg-teal-600 text-white border-teal-600',        'inactive' => 'bg-teal-50 text-teal-700 border-teal-200 hover:border-teal-400'],
            'auto_mismatch'    => ['label' => 'لا يتطابق',              'count' => $autoMismatchCount,    'active' => 'bg-orange-500 text-white border-orange-500',    'inactive' => 'bg-orange-50 text-orange-700 border-orange-200 hover:border-orange-400'],
            'mismatch_one'     => ['label' => 'خطأ رقم واحد',          'count' => $mismatchOneCount,     'active' => 'bg-blue-500 text-white border-blue-500',        'inactive' => 'bg-blue-50 text-blue-700 border-blue-200 hover:border-blue-400'],
            'mismatch_partial' => ['label' => 'عدم تطابق جزئي',        'count' => $mismatchPartialCount, 'active' => 'bg-amber-500 text-white border-amber-500',      'inactive' => 'bg-amber-50 text-amber-700 border-amber-200 hover:border-amber-400'],
            'mismatch_full'    => ['label' => 'عدم تطابق كلي',         'count' => $mismatchFullCount,    'active' => 'bg-red-600 text-white border-red-600',          'inactive' => 'bg-red-50 text-red-700 border-red-200 hover:border-red-400'],
        ];
        @endphp
        <div class="flex items-center gap-2 flex-wrap">
            @foreach($autoFilters as $key => $opt)
                <a href="{{ route('payment-review.index', array_filter(['filter' => $key, 'search' => $search, 'sham_cash' => $shamCash, 'date_from' => $dateFrom, 'date_to' => $dateTo], fn($v) => $v !== '')) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border text-sm font-bold transition-all {{ $filter === $key ? $opt['active'] : $opt['inactive'] }}">
                    {{ $opt['label'] }}
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-xs font-black
                        {{ $filter === $key ? 'bg-white/25 text-white' : 'bg-white/60 text-current' }}">
                        {{ $opt['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Date range filter --}}
        <div class="flex items-center gap-2 flex-wrap pt-1">
            <span class="text-xs font-semibold text-gray-500 ml-1">تاريخ الإضافة:</span>
            <input type="date" name="date_from" value="{{ $dateFrom }}" form="filter-form"
                   class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 transition">
            <span class="text-xs text-gray-400">—</span>
            <input type="date" name="date_to" value="{{ $dateTo }}" form="filter-form"
                   class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 transition">
            <button type="submit" form="filter-form"
                    class="text-sm bg-violet-600 hover:bg-violet-700 text-white font-semibold px-3 py-1.5 rounded-xl transition-colors">
                تطبيق
            </button>
        </div>

        {{-- Sham cash filter --}}
        <div class="flex items-center gap-2 flex-wrap pt-1">
            <span class="text-xs font-semibold text-gray-500 ml-1">شام كاش:</span>
            @foreach(['' => 'الكل', 'done' => 'نعم', 'done_no_iban' => 'نعم — بدون آيبان', 'manual' => 'يدوي', 'none' => 'لا'] as $val => $lbl)
                <a href="{{ route('payment-review.index', array_filter(['filter' => $filter, 'search' => $search, 'sham_cash' => $val, 'date_from' => $dateFrom, 'date_to' => $dateTo], fn($v) => $v !== '')) }}"
                   class="inline-flex items-center px-3 py-1.5 rounded-xl border text-sm font-semibold transition-all
                       @php
                           $activeClass = match($val) {
                               'done'         => 'bg-emerald-600 text-white border-emerald-600',
                               'done_no_iban' => 'bg-teal-600 text-white border-teal-600',
                               'manual'       => 'bg-amber-500 text-white border-amber-500',
                               'none'         => 'bg-red-500 text-white border-red-500',
                               default        => 'bg-gray-800 text-white border-gray-800',
                           };
                           $inactiveClass = match($val) {
                               'done'         => 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:border-emerald-400',
                               'done_no_iban' => 'bg-teal-50 text-teal-700 border-teal-200 hover:border-teal-400',
                               'manual'       => 'bg-amber-50 text-amber-700 border-amber-200 hover:border-amber-400',
                               'none'         => 'bg-red-50 text-red-700 border-red-200 hover:border-red-400',
                               default        => 'bg-white text-gray-600 border-gray-200 hover:border-gray-400',
                           };
                       @endphp
                       {{ $shamCash === $val ? $activeClass : $inactiveClass }}">
                    {{ $lbl }}
                </a>
            @endforeach
        </div>

        @if($search || $filter !== 'all' || $shamCash !== '' || $dateFrom !== '' || $dateTo !== '')
            <div class="flex">
                <a href="{{ route('payment-review.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
            </div>
        @endif

    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gradient-to-l from-gray-50 to-white">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">نتائج المراجعة</span>
            @if($filter !== 'all')
                @php
                $filterLabel = $autoFilters[$filter]['label'] ?? $filter;
                $filterBadgeClass = match($filter) {
                    'auto_match'       => 'bg-teal-100 text-teal-700',
                    'auto_mismatch'    => 'bg-orange-100 text-orange-700',
                    'mismatch_one'     => 'bg-blue-100 text-blue-700',
                    'mismatch_partial' => 'bg-amber-100 text-amber-700',
                    'mismatch_full'    => 'bg-red-100 text-red-700',
                    default            => 'bg-gray-100 text-gray-600',
                };
                @endphp
                <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $filterBadgeClass }}">
                    {{ $filterLabel }}
                </span>
            @endif
        </div>
        <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-3 py-1">{{ $members->count() }} سجل</span>
    </div>

    @if($members->isEmpty())
        <div class="text-center py-20">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-gray-400 text-sm">لا توجد سجلات مطابقة</p>
        </div>
    @else
        {{-- Bulk action bar --}}
        <div id="bulk-bar" class="hidden items-center gap-3 px-4 py-3 bg-red-50 border-b border-red-100">
            <span id="bulk-count" class="text-sm font-bold text-red-700">0 محدد</span>
            <form id="bulk-delete-form" action="{{ route('payment-review.bulk-delete') }}" method="POST"
                  onsubmit="return confirm('حذف بيانات الدفع للسجلات المحددة؟ لا يمكن التراجع عن هذه العملية.')">
                @csrf
                <div id="bulk-ids-container"></div>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف المحدد
                </button>
            </form>
            <button type="button" onclick="clearSelection()"
                    class="text-sm text-gray-500 hover:text-gray-700 underline">إلغاء التحديد</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-right">
                        <th class="px-4 py-3.5 w-10">
                            <input type="checkbox" id="select-all-pr" class="rounded border-gray-300 text-red-600 focus:ring-red-400">
                        </th>
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5">العضو</th>
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5 text-center w-16">تطابق</th>
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5">الآيبان — payment_info</th>
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5">الآيبان — AI</th>
                        <th class="px-4 py-3.5 w-36"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                        @php
                            $pi = $member->paymentInfo;
                            $ai = $member->paymentInfoAI;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors group" id="row-{{ $member->id }}">
                            <td class="px-4 py-3.5 w-10">
                                <input type="checkbox" class="pr-row-check rounded border-gray-300 text-red-600 focus:ring-red-400" value="{{ $member->id }}">
                            </td>

                            {{-- Member --}}
                            <td class="px-4 py-3.5">
                                <a href="{{ route('members.show', $member) }}"
                                   class="font-bold text-gray-900 hover:text-violet-700 transition-colors text-sm">
                                    {{ $member->full_name }}
                                </a>
                                @if($member->dossier_number)
                                    <p class="text-xs text-gray-400 font-mono">{{ $member->dossier_number }}</p>
                                @endif
                            </td>

                            {{-- Auto match indicator --}}
                            <td class="px-4 py-3.5 text-center">
                                @if(!$pi && !$ai)
                                    <span class="text-gray-300 text-xs">—</span>
                                @elseif($member->auto_match)
                                    <span title="تطابق تلقائي" class="inline-flex items-center justify-center w-7 h-7 bg-emerald-100 rounded-full">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                @elseif($member->mismatch_type === 'one_digit')
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-100 rounded-full">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </span>
                                        <span class="text-xs font-bold text-blue-600 leading-none">رقم واحد</span>
                                    </div>
                                @elseif($member->mismatch_type === 'partial')
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="inline-flex items-center justify-center w-7 h-7 bg-amber-100 rounded-full">
                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                            </svg>
                                        </span>
                                        <span class="text-xs font-bold text-amber-600 leading-none">جزئي</span>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="inline-flex items-center justify-center w-7 h-7 bg-red-100 rounded-full">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </span>
                                        <span class="text-xs font-bold text-red-600 leading-none">كلي</span>
                                    </div>
                                @endif
                            </td>

                            {{-- IBAN payment_info --}}
                            <td class="px-4 py-3.5">
                                <div id="iban-display-{{ $member->id }}">
                                    @if($pi?->iban)
                                        <span class="font-mono text-xs @if(!$member->auto_match && $ai?->iban) bg-red-100 text-red-700 px-1.5 py-0.5 rounded @else text-gray-700 @endif">
                                            {{ $pi->iban }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </div>
                                <div id="iban-edit-{{ $member->id }}" class="hidden mt-1">
                                    <form method="POST" action="{{ route('payment-review.update-iban', $member) }}" class="space-y-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="space-y-1">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">payment_info</p>
                                            <input type="text" name="iban" value="{{ $pi?->iban }}"
                                                   class="w-full font-mono text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white"
                                                   placeholder="IBAN...">
                                            <input type="text" name="barcode" value="{{ $pi?->barcode }}"
                                                   class="w-full font-mono text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white"
                                                   placeholder="باركود...">
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">AI</p>
                                            <input type="text" name="iban_ai" value="{{ $ai?->iban }}"
                                                   class="w-full font-mono text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white"
                                                   placeholder="IBAN AI...">
                                            <input type="text" name="barcode_ai" value="{{ $ai?->barcode }}"
                                                   class="w-full font-mono text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white"
                                                   placeholder="باركود AI...">
                                        </div>
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-violet-600 hover:bg-violet-700 text-white transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            حفظ
                                        </button>
                                    </form>
                                </div>
                            </td>

                            {{-- IBAN AI --}}
                            <td class="px-4 py-3.5">
                                <div id="iban-ai-display-{{ $member->id }}">
                                    @if($ai?->iban)
                                        <span class="font-mono text-xs @if(!$member->auto_match && $pi?->iban) bg-red-100 text-red-700 px-1.5 py-0.5 rounded @else text-gray-700 @endif">
                                            {{ $ai->iban }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </div>
                                <div id="iban-ai-edit-{{ $member->id }}" class="hidden"></div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3.5 text-center">
                                <div id="iban-action-{{ $member->id }}">
                                    <button type="button" onclick="toggleIbanEdit({{ $member->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-violet-50 text-violet-700 border border-violet-200 hover:bg-violet-100 hover:border-violet-300 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        تعديل الآيبان
                                    </button>
                                </div>
                                <div id="iban-cancel-{{ $member->id }}" class="hidden">
                                    <button type="button" onclick="toggleIbanEdit({{ $member->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        إلغاء
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleIbanEdit(id) {
    const display   = document.getElementById('iban-display-'    + id);
    const edit      = document.getElementById('iban-edit-'       + id);
    const aiDisplay = document.getElementById('iban-ai-display-' + id);
    const action    = document.getElementById('iban-action-'     + id);
    const cancel    = document.getElementById('iban-cancel-'     + id);
    display.classList.toggle('hidden');
    edit.classList.toggle('hidden');
    if (aiDisplay) aiDisplay.classList.toggle('hidden');
    if (action)   action.classList.toggle('hidden');
    if (cancel)   cancel.classList.toggle('hidden');
    if (!edit.classList.contains('hidden')) {
        edit.querySelector('input').focus();
    }
}

// Bulk select logic
(function () {
    var selectAll   = document.getElementById('select-all-pr');
    var bulkBar     = document.getElementById('bulk-bar');
    var bulkCount   = document.getElementById('bulk-count');
    var idsContainer = document.getElementById('bulk-ids-container');

    function getChecked() {
        return document.querySelectorAll('.pr-row-check:checked');
    }

    function updateBulkBar() {
        var checked = getChecked();
        if (checked.length > 0) {
            bulkBar.classList.remove('hidden');
            bulkBar.classList.add('flex');
            bulkCount.textContent = checked.length + ' محدد';
        } else {
            bulkBar.classList.add('hidden');
            bulkBar.classList.remove('flex');
        }
        // Rebuild hidden inputs
        idsContainer.innerHTML = '';
        checked.forEach(function (cb) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'ids[]';
            inp.value = cb.value;
            idsContainer.appendChild(inp);
        });
        // Update select-all state
        if (selectAll) {
            var all = document.querySelectorAll('.pr-row-check');
            selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
            selectAll.checked = all.length > 0 && checked.length === all.length;
        }
    }

    document.querySelectorAll('.pr-row-check').forEach(function (cb) {
        cb.addEventListener('change', updateBulkBar);
    });

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.pr-row-check').forEach(function (cb) {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });
    }

    window.clearSelection = function () {
        document.querySelectorAll('.pr-row-check').forEach(function (cb) { cb.checked = false; });
        if (selectAll) selectAll.checked = false;
        updateBulkBar();
    };
})();

// Submit search form on Enter
document.getElementById('filter-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const search = this.querySelector('input[name="search"]').value;
    const filter = this.querySelector('input[name="filter"]').value;
    const url = new URL(this.action);
    if (search) url.searchParams.set('search', search);
    if (filter && filter !== 'all') url.searchParams.set('filter', filter);
    window.location = url.toString();
});
</script>
@endpush

@endsection
