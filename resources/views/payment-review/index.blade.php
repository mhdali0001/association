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
        <a href="{{ route('payment-review.duplicate-ibans') }}"
           class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            تكرار الآيبانات
        </a>
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
            <div class="relative">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="بحث بالاسم..."
                       class="w-full pr-10 pl-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300">
            </div>
        </form>

        {{-- Filter buttons --}}
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-semibold text-gray-400 ml-1">حالة المراجعة:</span>

            @php
            $filterOptions = [
                'all'      => ['label' => 'الكل',           'count' => $totalCount,    'active' => 'bg-gray-800 text-white border-gray-800',             'inactive' => 'bg-white text-gray-600 border-gray-200 hover:border-gray-400'],
                'pending'  => ['label' => 'لم يُراجَع',     'count' => $pendingCount,  'active' => 'bg-amber-500 text-white border-amber-500',            'inactive' => 'bg-amber-50 text-amber-700 border-amber-200 hover:border-amber-400'],
                'reviewed' => ['label' => 'تمت المراجعة',   'count' => $reviewedCount, 'active' => 'bg-blue-500 text-white border-blue-500',              'inactive' => 'bg-blue-50 text-blue-700 border-blue-200 hover:border-blue-400'],
                'match'    => ['label' => 'تطابق',           'count' => $matchCount,    'active' => 'bg-emerald-500 text-white border-emerald-500',        'inactive' => 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:border-emerald-400'],
                'mismatch' => ['label' => 'لا يتطابق',      'count' => $mismatchCount, 'active' => 'bg-red-500 text-white border-red-500',                'inactive' => 'bg-red-50 text-red-600 border-red-200 hover:border-red-400'],
            ];
            @endphp

            @foreach($filterOptions as $key => $opt)
                <a href="{{ route('payment-review.index', ['filter' => $key, 'search' => $search]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border text-sm font-bold transition-all {{ $filter === $key ? $opt['active'] : $opt['inactive'] }}">
                    {{ $opt['label'] }}
                    <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-xs font-black
                        {{ $filter === $key ? 'bg-white/25 text-white' : 'bg-white/60 text-current' }}">
                        {{ $opt['count'] }}
                    </span>
                </a>
            @endforeach

            @if($search || $filter !== 'all')
                <a href="{{ route('payment-review.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors mr-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
            @endif
        </div>

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
                <span class="text-xs font-bold px-2.5 py-1 rounded-full
                    {{ $filter === 'pending'  ? 'bg-amber-100 text-amber-700'   : '' }}
                    {{ $filter === 'reviewed' ? 'bg-blue-100 text-blue-700'     : '' }}
                    {{ $filter === 'match'    ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $filter === 'mismatch' ? 'bg-red-100 text-red-600'       : '' }}">
                    {{ $filterOptions[$filter]['label'] }}
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
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-right">
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5">العضو</th>
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5 text-center w-16">تطابق</th>
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5">الآيبان — payment_info</th>
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5">الآيبان — AI</th>
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5 text-center">حالة المراجعة</th>
                        <th class="font-semibold text-gray-500 text-xs px-4 py-3.5">تفاصيل</th>
                        <th class="px-4 py-3.5 w-44"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                        @php
                            $review  = $member->paymentReview;
                            $pi      = $member->paymentInfo;
                            $ai      = $member->paymentInfoAI;
                            $rowClass = match($review?->status ?? 'pending') {
                                'match'    => 'bg-emerald-50/40 hover:bg-emerald-50',
                                'mismatch' => 'bg-red-50/40 hover:bg-red-50',
                                default    => 'hover:bg-gray-50',
                            };
                        @endphp
                        <tr class="{{ $rowClass }} transition-colors group" id="row-{{ $member->id }}">

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
                                @else
                                    <span title="لا يتطابق تلقائياً" class="inline-flex items-center justify-center w-7 h-7 bg-red-100 rounded-full">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </span>
                                @endif
                            </td>

                            {{-- IBAN payment_info --}}
                            <td class="px-4 py-3.5">
                                @if($pi?->iban)
                                    <span class="font-mono text-xs @if(!$member->auto_match && $ai?->iban) bg-red-100 text-red-700 px-1.5 py-0.5 rounded @else text-gray-700 @endif">
                                        {{ $pi->iban }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>

                            {{-- IBAN AI --}}
                            <td class="px-4 py-3.5">
                                @if($ai?->iban)
                                    <span class="font-mono text-xs @if(!$member->auto_match && $pi?->iban) bg-red-100 text-red-700 px-1.5 py-0.5 rounded @else text-gray-700 @endif">
                                        {{ $ai->iban }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Review status badge --}}
                            <td class="px-4 py-3.5 text-center">
                                @if(!$review || $review->isPending())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span>
                                        لم يُراجَع
                                    </span>
                                @elseif($review->isMatch())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        تطابق
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        لا يتطابق
                                    </span>
                                @endif
                            </td>

                            {{-- Reviewer / reviewed_at / notes --}}
                            <td class="px-4 py-3.5 text-xs text-gray-400 max-w-[160px]">
                                @if($review && !$review->isPending())
                                    <p class="font-medium text-gray-600 truncate">{{ $review->reviewer?->name }}</p>
                                    <p>{{ $review->reviewed_at?->format('Y/m/d H:i') }}</p>
                                    @if($review->notes)
                                        <p class="truncate text-gray-500 mt-0.5 italic" title="{{ $review->notes }}">{{ Str::limit($review->notes, 40) }}</p>
                                    @endif
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1">
                                    {{-- Quick match --}}
                                    <form method="POST" action="{{ route('payment-review.store', $member) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="match">
                                        <button type="submit"
                                                title="تطابق"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            تطابق
                                        </button>
                                    </form>
                                    {{-- Quick mismatch --}}
                                    <form method="POST" action="{{ route('payment-review.store', $member) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="mismatch">
                                        <button type="submit"
                                                title="لا يتطابق"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            لا يتطابق
                                        </button>
                                    </form>
                                    {{-- Notes / detail toggle --}}
                                    <button type="button"
                                            onclick="toggleNotes({{ $member->id }})"
                                            title="إضافة ملاحظة"
                                            class="p-1.5 rounded-lg text-gray-400 hover:text-violet-600 hover:bg-violet-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Notes form (hidden by default) --}}
                                <div id="notes-{{ $member->id }}" class="hidden mt-2">
                                    <form method="POST" action="{{ route('payment-review.store', $member) }}" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $review?->status ?? 'pending' }}">
                                        <textarea name="notes" rows="2"
                                                  placeholder="ملاحظات..."
                                                  class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 resize-none">{{ $review?->notes }}</textarea>
                                        <div class="flex gap-1.5">
                                            <button type="submit"
                                                    class="text-xs bg-violet-600 hover:bg-violet-700 text-white font-bold px-3 py-1.5 rounded-lg transition-colors">
                                                حفظ
                                            </button>
                                            <button type="button" onclick="toggleNotes({{ $member->id }})"
                                                    class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                                إلغاء
                                            </button>
                                        </div>
                                    </form>
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
function toggleNotes(id) {
    const el = document.getElementById('notes-' + id);
    el.classList.toggle('hidden');
    if (!el.classList.contains('hidden')) {
        el.querySelector('textarea').focus();
    }
}

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
