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
@endphp

{{-- Back button --}}
<div class="mb-5">
    <a href="{{ route('members.payment-batches') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        العودة إلى سجل الدفعات
    </a>
</div>

{{-- Batch Header Card --}}
<div class="relative bg-gradient-to-l {{ $grad }} rounded-3xl overflow-hidden shadow-xl mb-6">
    {{-- Background circles --}}
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute -top-8 -right-8 w-48 h-48 bg-white rounded-full"></div>
        <div class="absolute -bottom-12 right-24 w-64 h-64 bg-white rounded-full"></div>
        <div class="absolute top-4 -left-6 w-32 h-32 bg-white rounded-full"></div>
    </div>

    <div class="relative p-6 sm:p-8">
        {{-- Top row: title + operation badge --}}
        <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
            <div>
                <p class="text-white/60 text-xs font-semibold uppercase tracking-widest mb-1">دفعة #{{ $batch->id }}</p>
                <h1 class="text-white font-black text-2xl sm:text-3xl">{{ $batch->label ?: 'دفعة بدون اسم' }}</h1>
                @if($batch->notes)
                    <p class="text-white/70 text-sm mt-1.5">{{ $batch->notes }}</p>
                @endif
            </div>
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

        {{-- Stats row --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                <p class="text-white/60 text-xs font-semibold mb-1">تاريخ الدفعة</p>
                <p class="text-white font-black text-xl">{{ $batch->payment_date?->format('Y/m/d') ?? '—' }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                <p class="text-white/60 text-xs font-semibold mb-1">عدد الأعضاء</p>
                <p class="text-white font-black text-xl">{{ $fmt($batch->members_count) }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                <p class="text-white/60 text-xs font-semibold mb-1">إجمالي المبلغ النهائي</p>
                <p class="text-white font-black text-lg leading-tight">{{ $fmtMoney($batch->total_estimated_amount) }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-4 border border-white/20">
                <p class="text-white/60 text-xs font-semibold mb-1">طبّقها</p>
                <p class="text-white font-black text-base">{{ $batch->appliedBy?->name ?? '—' }}</p>
                <p class="text-white/50 text-xs mt-0.5 font-mono">{{ $batch->created_at->format('Y/m/d H:i') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
    <form method="GET" action="{{ route('members.payment-batches.show', $batch) }}">
        <div class="flex flex-wrap items-end gap-3 p-4">

            {{-- Search --}}
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-bold text-gray-500 mb-1.5">بحث</label>
                <div class="relative">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $search }}" placeholder="اسم العضو أو رقم الملف..."
                           class="w-full border border-gray-200 rounded-xl pr-9 pl-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:bg-white transition placeholder-gray-300">
                </div>
            </div>

            {{-- Diff filter --}}
            <div class="min-w-36">
                <label class="block text-xs font-bold text-gray-500 mb-1.5">التغيير</label>
                <select name="diff" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition">
                    <option value="">الكل</option>
                    <option value="added"      {{ $diffFilter === 'added'      ? 'selected' : '' }}>مضاف</option>
                    <option value="subtracted" {{ $diffFilter === 'subtracted' ? 'selected' : '' }}>منقوص</option>
                    <option value="same"       {{ $diffFilter === 'same'       ? 'selected' : '' }}>بدون تغيير</option>
                </select>
            </div>

            {{-- Amount range --}}
            <div class="flex items-end gap-2">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">المبلغ من</label>
                    <input type="number" name="amount_from" value="{{ $amountFrom }}" placeholder="0" min="0"
                           class="w-28 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition placeholder-gray-300">
                </div>
                <span class="text-gray-400 pb-2">—</span>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">إلى</label>
                    <input type="number" name="amount_to" value="{{ $amountTo }}" placeholder="∞" min="0"
                           class="w-28 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 transition placeholder-gray-300">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    بحث
                </button>
                @if($search || $diffFilter || $amountFrom !== '' || $amountTo !== '')
                    <a href="{{ route('members.payment-batches.show', $batch) }}"
                       class="flex items-center gap-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        مسح
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Members Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    {{-- Table header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
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
    </div>

    @if($members->isEmpty())
        <div class="flex flex-col items-center justify-center py-16">
            <p class="text-gray-300 font-semibold">لا يوجد أعضاء في هذه الدفعة</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-500 text-xs font-semibold border-b border-gray-100">
                        <th class="px-5 py-3 text-right">رقم الملف</th>
                        <th class="px-5 py-3 text-right">الاسم</th>
                        <th class="px-5 py-3 text-center">قبل</th>
                        <th class="px-5 py-3 text-center">بعد</th>
                        <th class="px-5 py-3 text-center">التغيير</th>
                        <th class="px-5 py-3 text-right">المبلغ النهائي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $line)
                        @php
                            $diff     = $line->new_count - $line->previous_count;
                            $diffPos  = $diff > 0;
                            $diffNeg  = $diff < 0;
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
                                    <span class="inline-flex items-center justify-center gap-0.5 text-xs font-black text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg w-12 py-1">
                                        +{{ $diff }}
                                    </span>
                                @elseif($diffNeg)
                                    <span class="inline-flex items-center justify-center gap-0.5 text-xs font-black text-red-600 bg-red-50 border border-red-100 rounded-lg w-12 py-1">
                                        {{ $diff }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-2.5 py-1">
                                    {{ $fmtMoney($line->estimated_amount) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $members->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
