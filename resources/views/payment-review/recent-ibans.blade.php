@extends('layouts.app')

@section('title', 'الآيبانات الحديثة — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('payment-review.index') }}" class="hover:text-violet-700 transition-colors">مراجعة الدفع</a>
    <span class="text-gray-300 mx-1">/</span>
    <a href="{{ route('payment-review.duplicate-ibans') }}" class="hover:text-rose-600 transition-colors">تكرار الآيبانات</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">الآيبانات الحديثة</span>
@endsection

@section('content')

@php
    $activeDuplicateMembers = $tab === 'dup-week' ? $weekDuplicateMembers : $monthDuplicateMembers;
@endphp

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-violet-700 via-indigo-600 to-blue-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">الآيبانات الحديثة</h1>
            <p class="text-indigo-200 text-sm mt-0.5">آيبانات أُضيفت خلال آخر أسبوع أو آخر شهر</p>
        </div>
        <div class="flex gap-3 flex-wrap items-center">
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[80px]">
                <p class="text-white font-black text-2xl leading-none">{{ number_format($weekMembers->count()) }}</p>
                <p class="text-indigo-200 text-xs mt-0.5">أسبوع</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[80px]">
                <p class="text-white font-black text-2xl leading-none">{{ number_format($monthMembers->count()) }}</p>
                <p class="text-indigo-200 text-xs mt-0.5">شهر</p>
            </div>
            <div class="bg-red-500/30 border border-red-300/30 rounded-xl px-4 py-2.5 text-center min-w-[80px]">
                <p class="text-white font-black text-2xl leading-none">{{ number_format($weekDuplicateMembers->count()) }}</p>
                <p class="text-red-200 text-xs mt-0.5">مكرر أسبوع</p>
            </div>
            <div class="bg-red-500/30 border border-red-300/30 rounded-xl px-4 py-2.5 text-center min-w-[80px]">
                <p class="text-white font-black text-2xl leading-none">{{ number_format($monthDuplicateMembers->count()) }}</p>
                <p class="text-red-200 text-xs mt-0.5">مكرر شهر</p>
            </div>
            <a href="{{ route('payment-review.duplicate-ibans') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                تكرار الآيبانات
            </a>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 overflow-hidden">
    <div class="flex border-b border-gray-100 flex-wrap">
        {{-- Tab: آخر أسبوع --}}
        <a href="{{ route('payment-review.recent-ibans', ['tab' => 'week']) }}"
           class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold border-b-2 transition-colors
                  {{ $tab === 'week' ? 'border-indigo-500 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-gray-500 hover:text-indigo-500 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            آخر أسبوع
            <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                         {{ $tab === 'week' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                {{ number_format($weekMembers->count()) }}
            </span>
        </a>

        {{-- Tab: آخر شهر --}}
        <a href="{{ route('payment-review.recent-ibans', ['tab' => 'month']) }}"
           class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold border-b-2 transition-colors
                  {{ $tab === 'month' ? 'border-violet-500 text-violet-600 bg-violet-50/50' : 'border-transparent text-gray-500 hover:text-violet-500 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            آخر شهر
            <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                         {{ $tab === 'month' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500' }}">
                {{ number_format($monthMembers->count()) }}
            </span>
        </a>

        {{-- Divider --}}
        <div class="flex items-center px-2">
            <div class="w-px h-6 bg-gray-200"></div>
        </div>

        {{-- Tab: مكرر آخر أسبوع --}}
        <a href="{{ route('payment-review.recent-ibans', ['tab' => 'dup-week']) }}"
           class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold border-b-2 transition-colors
                  {{ $tab === 'dup-week' ? 'border-rose-500 text-rose-600 bg-rose-50/50' : 'border-transparent text-gray-500 hover:text-rose-500 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            مكرر — أسبوع
            <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                         {{ $tab === 'dup-week' ? 'bg-rose-100 text-rose-700' : 'bg-gray-100 text-gray-500' }}">
                {{ number_format($weekDuplicateMembers->count()) }}
            </span>
        </a>

        {{-- Tab: مكرر آخر شهر --}}
        <a href="{{ route('payment-review.recent-ibans', ['tab' => 'dup-month']) }}"
           class="flex items-center gap-2 px-5 py-3.5 text-sm font-bold border-b-2 transition-colors
                  {{ $tab === 'dup-month' ? 'border-red-500 text-red-600 bg-red-50/50' : 'border-transparent text-gray-500 hover:text-red-500 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            مكرر — شهر
            <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                         {{ $tab === 'dup-month' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500' }}">
                {{ number_format($monthDuplicateMembers->count()) }}
            </span>
        </a>
    </div>

    @php
        $isDupTab = in_array($tab, ['dup-week', 'dup-month']);

        if ($tab === 'week') {
            $activeFrom   = $weekStart;
            $accentHover  = 'hover:bg-indigo-50/30';
            $accentColor  = 'text-indigo-700 bg-indigo-50 border-indigo-100';
            $countLabel   = $weekMembers->count() . ' عضو';
        } elseif ($tab === 'month') {
            $activeFrom   = $monthStart;
            $accentHover  = 'hover:bg-violet-50/30';
            $accentColor  = 'text-violet-700 bg-violet-50 border-violet-100';
            $countLabel   = $monthMembers->count() . ' عضو';
        } elseif ($tab === 'dup-week') {
            $activeFrom   = $weekStart;
            $accentHover  = 'hover:bg-rose-50/30';
            $accentColor  = 'text-rose-700 bg-rose-50 border-rose-100';
            $countLabel   = $weekDuplicateMembers->count() . ' آيبان مكرر';
        } else {
            $activeFrom   = $monthStart;
            $accentHover  = 'hover:bg-red-50/30';
            $accentColor  = 'text-red-700 bg-red-50 border-red-100';
            $countLabel   = $monthDuplicateMembers->count() . ' آيبان مكرر';
        }
    @endphp

    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/50">
        <p class="text-sm font-bold text-gray-600">
            {{ $countLabel }}
            <span class="text-xs text-gray-400 font-normal mr-1">منذ {{ $activeFrom->format('Y/m/d') }}</span>
        </p>
    </div>
</div>

{{-- ===== Simple list tabs (week / month) ===== --}}
@if(!$isDupTab)
@php $activeMembers = $tab === 'week' ? $weekMembers : $monthMembers; @endphp

@if($activeMembers->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm text-center py-20">
        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-bold text-base mb-1">لا توجد آيبانات حديثة</p>
        <p class="text-gray-400 text-sm">لم يُضف أي آيبان خلال هذه الفترة</p>
    </div>
@else
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/30 text-right">
                    <th class="font-semibold text-gray-400 text-xs px-4 py-3 whitespace-nowrap">رقم الاضبارة</th>
                    <th class="font-semibold text-gray-400 text-xs px-4 py-3">الاسم</th>
                    <th class="font-semibold text-gray-400 text-xs px-4 py-3 whitespace-nowrap">الآيبان</th>
                    <th class="font-semibold text-gray-400 text-xs px-4 py-3 whitespace-nowrap">تاريخ الإضافة</th>
                    <th class="font-semibold text-gray-400 text-xs px-4 py-3 whitespace-nowrap">الجمعية</th>
                    <th class="font-semibold text-gray-400 text-xs px-4 py-3 whitespace-nowrap">حالة التحقق</th>
                    <th class="font-semibold text-gray-400 text-xs px-4 py-3 whitespace-nowrap">الحالة النهائية</th>
                    <th class="px-4 py-3 w-16"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($activeMembers as $member)
                @php $pi = $member->paymentInfo->first(); @endphp
                <tr class="transition-colors group {{ $accentHover }}">
                    <td class="px-4 py-3 font-mono font-semibold text-gray-600 text-xs whitespace-nowrap">
                        {{ $member->dossier_number ?? '—' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="font-bold text-gray-900 text-sm">{{ $member->full_name }}</span>
                    </td>
                    <td class="px-4 py-3 font-mono text-gray-700 text-xs whitespace-nowrap select-all">
                        {{ $pi?->iban ?? '—' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($pi?->created_at)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold border px-2 py-0.5 rounded-lg {{ $accentColor }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $pi->created_at->format('Y/m/d') }}
                            </span>
                            <span class="text-xs text-gray-400 mr-1">{{ $pi->created_at->diffForHumans() }}</span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                        {{ $member->association?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($member->verificationStatus)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border"
                                  style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}40">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $member->verificationStatus->color }}"></span>
                                {{ $member->verificationStatus->name }}
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($member->finalStatus)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border"
                                  style="background:{{ $member->finalStatus->color }}18; color:{{ $member->finalStatus->color }}; border-color:{{ $member->finalStatus->color }}40">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $member->finalStatus->color }}"></span>
                                {{ $member->finalStatus->name }}
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('members.show', $member) }}"
                           class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-lg text-blue-400 hover:text-blue-600 hover:bg-blue-50 inline-flex">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ===== Duplicate tabs (dup-week / dup-month) ===== --}}
@else
@php $activeDupMembers = $tab === 'dup-week' ? $weekDuplicateMembers : $monthDuplicateMembers; @endphp

@if($activeDupMembers->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm text-center py-20">
        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-bold text-base mb-1">لا توجد آيبانات مكررة حديثة</p>
        <p class="text-gray-400 text-sm">لم يُضف أي آيبان مكرر خلال هذه الفترة</p>
    </div>
@else
<div class="space-y-5">
    @foreach($activeDupMembers as $iban => $members)
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">

        {{-- IBAN header --}}
        <div class="flex items-center justify-between bg-gradient-to-l from-red-50 to-rose-50 border-b border-red-100 px-5 py-3.5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-red-400 mb-0.5">رقم الآيبان</p>
                    <p class="font-mono font-black text-gray-900 text-base tracking-wide select-all">{{ $iban }}</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 bg-red-100 text-red-700 text-xs font-black px-3 py-1.5 rounded-full border border-red-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                {{ $members->count() }} أعضاء
            </span>
        </div>

        {{-- Members table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/60 border-b border-gray-100 text-right">
                        <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">رقم الاضبارة</th>
                        <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">الاسم</th>
                        <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">تاريخ إضافة الآيبان</th>
                        <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">الجمعية</th>
                        <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">حالة التحقق</th>
                        <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">الحالة النهائية</th>
                        <th class="px-4 py-2.5 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                    @php $pi = $member->paymentInfo->first(); @endphp
                    <tr class="hover:bg-rose-50/30 transition-colors group">
                        <td class="px-4 py-3 font-mono font-semibold text-gray-600 text-xs whitespace-nowrap">
                            {{ $member->dossier_number ?? '—' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-bold text-gray-900 text-sm">{{ $member->full_name }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($pi?->created_at)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold border px-2 py-0.5 rounded-lg {{ $accentColor }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $pi->created_at->format('Y/m/d') }}
                                </span>
                                <span class="text-xs text-gray-400 mr-1">{{ $pi->created_at->diffForHumans() }}</span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                            {{ $member->association?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($member->verificationStatus)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border"
                                      style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}40">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $member->verificationStatus->color }}"></span>
                                    {{ $member->verificationStatus->name }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($member->finalStatus)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border"
                                      style="background:{{ $member->finalStatus->color }}18; color:{{ $member->finalStatus->color }}; border-color:{{ $member->finalStatus->color }}40">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $member->finalStatus->color }}"></span>
                                    {{ $member->finalStatus->name }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('members.show', $member) }}"
                               class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-lg text-blue-400 hover:text-blue-600 hover:bg-blue-50 inline-flex">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endif
@endif

@endsection
