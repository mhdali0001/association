@extends('layouts.app')

@section('title', ($batch->label ?: 'دفعة #' . $batch->id) . ' — مسالك النور')
@section('max-width', 'max-w-6xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-amber-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <a href="{{ route('members.payment-batches') }}" class="hover:text-amber-700 transition-colors">سجل الدفعات</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">{{ $batch->label ?: 'دفعة #' . $batch->id }}</span>
@endsection

@section('content')

@php
    $fmt      = fn($n) => number_format($n);
    $fmtMoney = fn($n) => number_format($n, 0) . ' ل.س';
    $isAdd    = $batch->operation === 'add';
    $isSub    = $batch->operation === 'subtract';
    $grad     = $isAdd ? 'from-emerald-600 via-emerald-500 to-teal-500'
                      : ($isSub ? 'from-red-600 via-red-500 to-orange-500'
                                : 'from-blue-600 via-blue-500 to-cyan-500');
    $lightBg  = $isAdd ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                       : ($isSub ? 'bg-red-50 text-red-600 border-red-200'
                                 : 'bg-blue-50 text-blue-700 border-blue-200');
    $hasFilters = $search || $diffFilter || $amountFrom !== '' || $amountTo !== '';
@endphp

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold">
    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm font-semibold">
    <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- Back button --}}
<div class="mb-4">
    <a href="{{ route('members.payment-batches') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        العودة إلى سجل الدفعات
    </a>
</div>

{{-- ══════════════ Batch Header ══════════════ --}}
<div class="relative bg-gradient-to-l {{ $grad }} rounded-3xl overflow-hidden shadow-xl mb-5">
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute -top-8 -right-8 w-48 h-48 bg-white rounded-full"></div>
        <div class="absolute -bottom-12 right-24 w-64 h-64 bg-white rounded-full"></div>
        <div class="absolute top-4 -left-6 w-32 h-32 bg-white rounded-full"></div>
    </div>

    <div class="relative p-5 sm:p-8">
        {{-- Title row --}}
        <div class="flex items-start justify-between flex-wrap gap-3 mb-5">
            <div>
                <p class="text-white/60 text-xs font-semibold uppercase tracking-widest mb-1">دفعة #{{ $batch->id }}</p>
                <h1 class="text-white font-black text-xl sm:text-3xl">{{ $batch->label ?: 'دفعة بدون اسم' }}</h1>
                @if($batch->notes)
                    <p class="text-white/70 text-sm mt-1">{{ $batch->notes }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2 flex-wrap shrink-0">
            <a href="{{ route('members.payment-batches.export', $batch) }}"
               class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 text-white text-sm font-bold rounded-2xl px-4 py-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                تصدير Excel
            </a>
            <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm border border-white/30 text-white text-sm font-black rounded-2xl px-4 py-2">
                @if($isAdd)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    إضافة {{ $batch->amount }} دفعة
                @elseif($isSub)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    طرح {{ $batch->amount }} دفعة
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعيين {{ $batch->amount }} دفعة
                @endif
            </span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-3 sm:p-4 border border-white/20">
                <p class="text-white/60 text-xs font-semibold mb-1">تاريخ الدفعة</p>
                <p class="text-white font-black text-lg sm:text-xl">{{ $batch->payment_date?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-3 sm:p-4 border border-white/20">
                <p class="text-white/60 text-xs font-semibold mb-1">عدد الأعضاء</p>
                <p class="text-white font-black text-lg sm:text-xl">{{ $fmt($batch->members_count) }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-3 sm:p-4 border border-white/20">
                <p class="text-white/60 text-xs font-semibold mb-1">إجمالي المبلغ</p>
                <p class="text-white font-black text-base sm:text-lg leading-tight">{{ $fmtMoney($batch->total_estimated_amount) }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-3 sm:p-4 border border-white/20">
                <p class="text-white/60 text-xs font-semibold mb-1">طبّقها</p>
                <p class="text-white font-black text-base">{{ $batch->appliedBy?->name ?? '—' }}</p>
                <p class="text-white/50 text-xs mt-0.5 font-mono">{{ $batch->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ FILTER BAR (members style) ══════════════ --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-4">
    <form method="GET" action="{{ route('members.payment-batches.show', $batch) }}" id="batch-show-filter-form">

        {{-- Always visible: search + toggle --}}
        <div class="px-4 pt-4 pb-3 border-b border-gray-100 space-y-2.5">
            <div class="relative">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="بحث باسم العضو أو رقم الملف أو الرقم الوطني..."
                       class="w-full pr-10 pl-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50
                              focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    بحث
                </button>
                <button type="button" onclick="toggleBatchShowFilters()"
                        class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-teal-300 transition-colors text-sm font-bold text-gray-600">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    الفلاتر
                    @if($hasFilters)
                        <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                    @endif
                    <svg id="batch-show-filter-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                @if($hasFilters)
                    <a href="{{ route('members.payment-batches.show', $batch) }}"
                       class="flex items-center gap-1.5 px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-500 text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        مسح
                    </a>
                @endif
            </div>
        </div>

        {{-- Collapsible advanced filters --}}
        <div id="batch-show-filter-body" class="{{ $hasFilters ? '' : 'hidden' }}">
            <div class="p-4 space-y-3">

                {{-- Row 1: diff + amount range --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    {{-- Diff --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1.5">التغيير</label>
                        <select name="diff" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                            <option value="">— الكل —</option>
                            <option value="added"      {{ $diffFilter === 'added'      ? 'selected' : '' }}>مضاف ↑</option>
                            <option value="subtracted" {{ $diffFilter === 'subtracted' ? 'selected' : '' }}>منقوص ↓</option>
                            <option value="same"       {{ $diffFilter === 'same'       ? 'selected' : '' }}>بدون تغيير</option>
                        </select>
                    </div>

                    {{-- Amount range --}}
                    <div class="sm:col-span-2 flex items-end gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">المبلغ النهائي من (ل.س)</label>
                            <input type="number" name="amount_from" value="{{ $amountFrom }}" placeholder="0" min="0"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition placeholder-gray-300">
                        </div>
                        <span class="text-gray-400 pb-2.5 shrink-0">—</span>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 mb-1.5">إلى</label>
                            <input type="number" name="amount_to" value="{{ $amountTo }}" placeholder="∞" min="0"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition placeholder-gray-300">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active filter chips --}}
            @if($hasFilters)
            <div class="flex flex-wrap items-center gap-2 px-4 pb-3 border-t border-gray-50 pt-3">
                <span class="text-xs text-gray-400 font-medium shrink-0">نشط:</span>
                @if($search)
                    <span class="inline-flex items-center text-xs font-bold bg-teal-50 text-teal-700 border border-teal-100 rounded-full px-2.5 py-1">
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                        {{ $search }}
                    </span>
                @endif
                @if($diffFilter)
                    <span class="inline-flex items-center text-xs font-bold bg-violet-50 text-violet-700 border border-violet-100 rounded-full px-2.5 py-1">
                        {{ match($diffFilter) { 'added' => 'مضاف ↑', 'subtracted' => 'منقوص ↓', default => 'بدون تغيير' } }}
                    </span>
                @endif
                @if($amountFrom !== '' || $amountTo !== '')
                    <span class="inline-flex items-center text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100 rounded-full px-2.5 py-1">
                        مبلغ: {{ $amountFrom ?: '0' }} — {{ $amountTo ?: '∞' }} ل.س
                    </span>
                @endif
            </div>
            @endif
        </div>
    </form>
</div>

{{-- ══════════════ MEMBERS TABLE / CARDS ══════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    {{-- Table header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gray-50 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-800 text-sm">الأعضاء المشمولون</h2>
                <p class="text-xs text-gray-400">{{ $fmt($members->total()) }} عضو</p>
            </div>
        </div>
        <button type="button" onclick="toggleAddMemberPanel()"
                class="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            إضافة عضو
        </button>
    </div>

    {{-- Add member panel --}}
    <div id="add-member-panel" class="hidden border-b border-gray-100 px-5 py-4 bg-gray-50/60">
        <p class="text-xs font-bold text-gray-500 mb-3">بحث عن عضو لإضافته إلى هذه الدفعة</p>
        <div class="flex gap-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" id="add-member-search" placeholder="اسم العضو أو رقم الملف..."
                       class="w-full pr-10 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 transition placeholder-gray-300">
            </div>
        </div>
        <div id="add-member-results" class="mt-3 space-y-1.5 hidden"></div>
    </div>

    @if($members->isEmpty())
        <div class="flex flex-col items-center justify-center py-16">
            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-gray-400 text-sm font-semibold">لا يوجد أعضاء مطابقون</p>
        </div>
    @else

        {{-- ── Desktop table ── --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs font-semibold border-b border-gray-100">
                        <th class="px-5 py-3 text-right">رقم الملف</th>
                        <th class="px-5 py-3 text-right">الاسم</th>
                        <th class="px-5 py-3 text-center">قبل</th>
                        <th class="px-5 py-3 text-center">بعد</th>
                        <th class="px-5 py-3 text-center">التغيير</th>
                        <th class="px-5 py-3 text-right">المبلغ النهائي</th>
                        <th class="px-3 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $line)
                    @php
                        $diff    = $line->new_count - $line->previous_count;
                        $diffPos = $diff > 0;
                        $diffNeg = $diff < 0;
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5">
                            @if($line->member)
                                <a href="{{ route('members.show', $line->member) }}"
                                   class="font-mono text-xs text-teal-600 hover:text-teal-800 bg-teal-50 hover:bg-teal-100 border border-teal-100 rounded-lg px-2 py-0.5 transition-colors">
                                    {{ $line->member->dossier_number ?? '—' }}
                                </a>
                            @else
                                <span class="font-mono text-xs text-gray-400 bg-gray-50 border border-gray-100 rounded-lg px-2 py-0.5">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($line->member)
                                <a href="{{ route('members.show', $line->member) }}"
                                   class="font-semibold text-gray-800 hover:text-teal-700 transition-colors">
                                    {{ $line->member->full_name }}
                                </a>
                            @else
                                <span class="text-gray-300 italic text-xs">محذوف</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="font-mono text-gray-400 text-sm">{{ $line->previous_count }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="font-black text-gray-800 text-sm">{{ $line->new_count }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($diffPos)
                                <span class="inline-flex items-center justify-center text-xs font-black text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg w-12 py-1">+{{ $diff }}</span>
                            @elseif($diffNeg)
                                <span class="inline-flex items-center justify-center text-xs font-black text-red-600 bg-red-50 border border-red-100 rounded-lg w-12 py-1">{{ $diff }}</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-2.5 py-1">
                                {{ $fmtMoney($line->estimated_amount) }}
                            </span>
                        </td>
                        <td class="px-3 py-3.5">
                            @if($line->member)
                            <form method="POST" action="{{ route('members.payment-batches.members.remove', [$batch, $line->member]) }}"
                                  onsubmit="return confirm('هل تريد إزالة «{{ $line->member->full_name }}» من هذه الدفعة؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-red-50 hover:bg-red-100 border border-red-100 text-red-400 hover:text-red-600 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── Mobile cards ── --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @foreach($members as $line)
            @php
                $diff    = $line->new_count - $line->previous_count;
                $diffPos = $diff > 0;
                $diffNeg = $diff < 0;
            @endphp
            <div class="flex items-center gap-3 px-4 py-3.5">
                {{-- Change indicator --}}
                <div class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center
                    {{ $diffPos ? 'bg-emerald-50 border border-emerald-100' : ($diffNeg ? 'bg-red-50 border border-red-100' : 'bg-gray-50 border border-gray-100') }}">
                    @if($diffPos)
                        <span class="text-xs font-black text-emerald-700">+{{ $diff }}</span>
                    @elseif($diffNeg)
                        <span class="text-xs font-black text-red-600">{{ $diff }}</span>
                    @else
                        <span class="text-xs font-bold text-gray-400">—</span>
                    @endif
                </div>

                {{-- Member info --}}
                <div class="flex-1 min-w-0">
                    @if($line->member)
                        <a href="{{ route('members.show', $line->member) }}"
                           class="font-bold text-sm text-gray-900 hover:text-teal-700 transition-colors block truncate">
                            {{ $line->member->full_name }}
                        </a>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">ملف: {{ $line->member->dossier_number ?? '—' }}</p>
                    @else
                        <p class="text-sm text-gray-300 italic">عضو محذوف</p>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="shrink-0 text-left">
                    <p class="text-xs text-gray-400 font-mono">
                        <span class="text-gray-500">{{ $line->previous_count }}</span>
                        <span class="text-gray-300 mx-1">→</span>
                        <span class="font-black text-gray-800">{{ $line->new_count }}</span>
                    </p>
                    <p class="text-xs font-bold text-amber-600 mt-1">{{ $fmtMoney($line->estimated_amount) }}</p>
                </div>

                {{-- Remove button --}}
                @if($line->member)
                <form method="POST" action="{{ route('members.payment-batches.members.remove', [$batch, $line->member]) }}"
                      onsubmit="return confirm('إزالة «{{ $line->member->full_name }}» من الدفعة؟')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="shrink-0 w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 hover:bg-red-100 border border-red-100 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>

        @if($members->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $members->links() }}
            </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
function toggleBatchShowFilters() {
    const body  = document.getElementById('batch-show-filter-body');
    const arrow = document.getElementById('batch-show-filter-arrow');
    const hidden = body.classList.toggle('hidden');
    arrow.style.transform = hidden ? '' : 'rotate(180deg)';
}

@if($hasFilters)
document.getElementById('batch-show-filter-arrow').style.transform = 'rotate(180deg)';
@endif

function toggleAddMemberPanel() {
    const panel = document.getElementById('add-member-panel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        document.getElementById('add-member-search').focus();
    }
}

(function () {
    const searchInput   = document.getElementById('add-member-search');
    const resultsBox    = document.getElementById('add-member-results');
    const searchJsonUrl = '{{ route('members.search-json') }}';
    const addUrl        = '{{ route('members.payment-batches.members.add', $batch) }}';
    const csrfToken     = document.querySelector('meta[name="csrf-token"]')?.content || '';

    let debounceTimer;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            resultsBox.classList.add('hidden');
            resultsBox.innerHTML = '';
            return;
        }
        debounceTimer = setTimeout(() => fetchMembers(q), 280);
    });

    function fetchMembers(q) {
        fetch(searchJsonUrl + '?q=' + encodeURIComponent(q), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => renderResults(data))
        .catch(() => {});
    }

    function renderResults(members) {
        if (!members.length) {
            resultsBox.classList.remove('hidden');
            resultsBox.innerHTML = '<p class="text-xs text-gray-400 text-center py-2">لا توجد نتائج</p>';
            return;
        }

        resultsBox.classList.remove('hidden');
        resultsBox.innerHTML = members.map(m => `
            <div class="flex items-center justify-between gap-3 bg-white border border-gray-100 rounded-xl px-4 py-2.5 hover:border-teal-200 transition-colors">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">${escHtml(m.full_name)}</p>
                    <p class="text-xs text-gray-400 font-mono mt-0.5">ملف: ${escHtml(m.dossier_number || '—')} · دفعات: ${m.payments_count ?? 0}</p>
                </div>
                <form method="POST" action="${addUrl}">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="member_id" value="${m.id}">
                    <button type="submit"
                            class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة
                    </button>
                </form>
            </div>
        `).join('');
    }

    function escHtml(str) {
        return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
})();
</script>
@endpush

@endsection
