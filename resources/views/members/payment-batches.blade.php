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
@endphp

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-4 py-3 rounded-2xl">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-black text-gray-900">سجل الدفعات</h1>
        <p class="text-sm text-gray-400 mt-0.5">تاريخ جميع عمليات الدفعات الجماعية</p>
    </div>
    <a href="{{ route('members.bulk-payments') }}"
       class="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        دفعة جديدة
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Total batches --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-teal-50 rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-black text-gray-900">{{ $fmt($totalBatches) }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">إجمالي الدفعات</p>
        </div>
    </div>

    {{-- Unique members --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-violet-50 rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-black text-gray-900">{{ $fmt($totalMembers) }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">عضو مشمول</p>
        </div>
    </div>

    {{-- Total amount --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-lg font-black text-gray-900 leading-tight">{{ $fmtMoney($totalAmount) }}</p>
            <p class="text-xs text-gray-400 font-medium mt-0.5">إجمالي المبالغ</p>
        </div>
    </div>

    {{-- Filtered results --}}
    <div class="bg-gradient-to-l from-teal-600 to-cyan-500 rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-black text-white">{{ $fmt($batches->total()) }}</p>
            <p class="text-xs text-teal-100 font-medium mt-0.5">نتائج الفلتر</p>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 overflow-hidden">
    <form method="GET" action="{{ route('members.payment-batches') }}">
        <div class="flex flex-wrap items-end gap-3 p-4">
            {{-- Search --}}
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-bold text-gray-500 mb-1.5">بحث</label>
                <div class="relative">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="اسم الدفعة أو ملاحظة..."
                           class="w-full border border-gray-200 rounded-xl pr-9 pl-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
                </div>
            </div>

            {{-- Operation --}}
            <div class="min-w-36">
                <label class="block text-xs font-bold text-gray-500 mb-1.5">نوع العملية</label>
                <select name="operation" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                    <option value="">الكل</option>
                    <option value="add"      {{ $operation === 'add'      ? 'selected' : '' }}>إضافة</option>
                    <option value="subtract" {{ $operation === 'subtract' ? 'selected' : '' }}>طرح</option>
                    <option value="set"      {{ $operation === 'set'      ? 'selected' : '' }}>تعيين</option>
                </select>
            </div>

            {{-- Date range --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">من تاريخ</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    بحث
                </button>
                @if($search || $operation || $dateFrom || $dateTo)
                    <a href="{{ route('members.payment-batches') }}"
                       class="flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        مسح
                    </a>
                @endif
            </div>
        </div>

        {{-- Active filters --}}
        @if($search || $operation || $dateFrom || $dateTo)
            <div class="flex flex-wrap items-center gap-2 px-4 pb-3 border-t border-gray-50 pt-3">
                <span class="text-xs text-gray-400 font-medium">الفلاتر النشطة:</span>
                @if($search)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-100 rounded-full px-2.5 py-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                        {{ $search }}
                    </span>
                @endif
                @if($operation)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100 rounded-full px-2.5 py-1">
                        {{ match($operation) { 'add' => 'إضافة', 'subtract' => 'طرح', default => 'تعيين' } }}
                    </span>
                @endif
                @if($dateFrom || $dateTo)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100 rounded-full px-2.5 py-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $dateFrom ?: '…' }} — {{ $dateTo ?: '…' }}
                    </span>
                @endif
            </div>
        @endif
    </form>
</div>

{{-- Batches List --}}
@if($batches->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-24 text-center px-6">
        <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mb-5">
            <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <p class="text-lg font-bold text-gray-300 mb-1">لا توجد دفعات مسجّلة</p>
        <p class="text-sm text-gray-300 mb-6">ابدأ بإنشاء دفعة جماعية جديدة</p>
        <a href="{{ route('members.bulk-payments') }}"
           class="flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            إنشاء دفعة جديدة
        </a>
    </div>
@else
    <div class="space-y-3">
        @foreach($batches as $batch)
            @php
                $isAdd  = $batch->operation === 'add';
                $isSub  = $batch->operation === 'subtract';
                $opBg   = $isAdd ? 'bg-emerald-50'  : ($isSub ? 'bg-red-50'    : 'bg-blue-50');
                $opText = $isAdd ? 'text-emerald-700': ($isSub ? 'text-red-600'  : 'text-blue-700');
                $opBorder = $isAdd ? 'border-emerald-100': ($isSub ? 'border-red-100' : 'border-blue-100');
                $opDot  = $isAdd ? 'bg-emerald-400'  : ($isSub ? 'bg-red-400'   : 'bg-blue-400');
                $opIcon = $isAdd ? 'M12 4v16m8-8H4'  : ($isSub ? 'M20 12H4'    : 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z');
                $dateBg = $batch->payment_date ? 'bg-teal-50 text-teal-700 border-teal-100' : 'bg-gray-50 text-gray-400 border-gray-100';
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition-all group">
                <div class="flex items-center gap-0 divide-x divide-x-reverse divide-gray-100">

                    {{-- Left accent bar --}}
                    <div class="w-1.5 self-stretch rounded-r-2xl shrink-0 {{ $opDot }}"></div>

                    {{-- Main content --}}
                    <div class="flex-1 flex flex-wrap items-center gap-x-6 gap-y-3 px-5 py-4 min-w-0">

                        {{-- Date + Label --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 border rounded-xl px-3 py-1.5 text-sm font-black {{ $dateBg }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $batch->payment_date?->format('Y/m/d') ?? 'بدون تاريخ' }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 border rounded-xl px-2.5 py-1 text-xs font-bold {{ $opBg }} {{ $opText }} {{ $opBorder }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $opIcon }}"/>
                                    </svg>
                                    {{ $batch->operation_label }}
                                </span>
                            </div>
                            <p class="font-bold text-gray-800 mt-2 text-base truncate">
                                {{ $batch->label ?: 'دفعة #' . $batch->id }}
                            </p>
                            @if($batch->notes)
                                <p class="text-xs text-gray-400 mt-0.5 truncate max-w-xs">{{ $batch->notes }}</p>
                            @endif
                        </div>

                        {{-- Stats group --}}
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
                                <p class="text-xs text-gray-400 mt-0.5">المبلغ المقدر</p>
                            </div>
                        </div>

                        {{-- Applied by + date --}}
                        <div class="flex items-center gap-2.5 shrink-0 border-r border-gray-100 pr-6">
                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-sm font-black text-gray-500 shrink-0">
                                {{ mb_substr($batch->appliedBy?->name ?? '؟', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-600">{{ $batch->appliedBy?->name ?? 'غير معروف' }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $batch->created_at->format('Y/m/d H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Action button --}}
                    <div class="px-4 shrink-0">
                        <a href="{{ route('members.payment-batches.show', $batch) }}"
                           class="flex items-center gap-1.5 text-xs font-bold text-gray-400 hover:text-teal-700 group-hover:bg-teal-50 hover:border-teal-200 border border-transparent rounded-xl px-3 py-2.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            تفاصيل
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($batches->hasPages())
        <div class="mt-5">
            {{ $batches->withQueryString()->links() }}
        </div>
    @endif
@endif

@endsection
