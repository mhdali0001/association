@extends('layouts.app')

@section('title', 'تكرار الآيبانات — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('payment-review.index') }}" class="hover:text-violet-700 transition-colors">مراجعة الدفع</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">تكرار الآيبانات</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-red-600 via-rose-500 to-pink-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">تكرار الآيبانات</h1>
            <p class="text-rose-100 text-sm mt-0.5">آيبانات مسجلة لأكثر من عضو واحد</p>
        </div>
        <div class="flex gap-3 flex-wrap items-center">
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-white font-black text-2xl leading-none">{{ $totalDuplicateIbans }}</p>
                <p class="text-rose-200 text-xs mt-0.5">آيبان مكرر</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-white font-black text-2xl leading-none">{{ $totalAffectedMembers }}</p>
                <p class="text-rose-200 text-xs mt-0.5">عضو متأثر</p>
            </div>
            <a href="{{ route('payment-review.index') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                مراجعة الدفع
            </a>
        </div>
    </div>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('payment-review.duplicate-ibans') }}" class="mb-5 flex gap-3">
    <div class="relative flex-1 max-w-sm">
        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
        </span>
        <input type="text" name="search" value="{{ $search }}"
               placeholder="بحث بالآيبان أو الاسم..."
               class="w-full pr-10 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:bg-white transition placeholder-gray-300">
    </div>
    <button type="submit"
            class="flex items-center gap-2 bg-gradient-to-l from-red-600 to-rose-500 hover:from-red-700 hover:to-rose-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
        بحث
    </button>
    @if($search)
        <a href="{{ route('payment-review.duplicate-ibans') }}"
           class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-500 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            مسح
        </a>
    @endif
</form>

{{-- Results --}}
@if($membersByIban->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm text-center py-20">
        <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-gray-700 font-bold text-base mb-1">لا توجد آيبانات مكررة</p>
        <p class="text-gray-400 text-sm">جميع الآيبانات المسجلة فريدة</p>
    </div>
@else
    <div class="space-y-5">
        @foreach($membersByIban as $iban => $members)
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
                            <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">رقم الهوية</th>
                            <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">الهاتف</th>
                            <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">الجمعية</th>
                            <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">حالة التحقق</th>
                            <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">الحالة النهائية</th>
                            <th class="font-semibold text-gray-400 text-xs px-4 py-2.5">المبلغ المقدر</th>
                            <th class="px-4 py-2.5 w-16"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($members as $member)
                        <tr class="hover:bg-rose-50/30 transition-colors group">
                            <td class="px-4 py-3 font-mono font-semibold text-gray-600 text-xs">
                                {{ $member->dossier_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-gray-900 text-sm">{{ $member->full_name }}</span>
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-600 text-xs">
                                {{ $member->national_id ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                {{ $member->phone ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                {{ $member->association?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($member->verificationStatus)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold border"
                                          style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}40">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $member->verificationStatus->color }}"></span>
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
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $member->finalStatus->color }}"></span>
                                        {{ $member->finalStatus->name }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($member->estimated_amount)
                                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-2 py-0.5">
                                        {{ number_format((float)$member->estimated_amount, 0, '.', ',') }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('members.show', $member) }}"
                                   class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-lg text-blue-400 hover:text-blue-600 hover:bg-blue-50 inline-flex"
                                   title="عرض الملف">
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

@endsection
