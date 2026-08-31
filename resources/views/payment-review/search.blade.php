@extends('layouts.app')

@section('title', 'بحث معلومات الدفع — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('payment-review.index') }}" class="hover:text-violet-700 transition-colors">مراجعة الدفع</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">بحث معلومات الدفع</span>
@endsection

@section('content')

@php
    $hl = function ($text, $needle) {
        $text = (string) $text;
        if (trim($text) === '') return '—';
        $safe = e($text);
        if (trim((string) $needle) === '') return $safe;
        $out = preg_replace('/(' . preg_quote($needle, '/') . ')/iu',
            '<mark class="bg-amber-200 text-amber-900 rounded px-0.5">$1</mark>', $safe);
        return $out ?? $safe;
    };

    $nq    = mb_strtolower($q);
    $bareQ = mb_strtolower(preg_replace('/[\s\-]+/', '', $q));

    $matches = function ($info) use ($nq, $bareQ) {
        if (! $info) return false;
        $iban = mb_strtolower(str_replace([' ', '-'], '', (string) $info->iban));
        $rcpt = mb_strtolower((string) $info->recipient_name);
        return ($bareQ !== '' && $iban !== '' && str_contains($iban, $bareQ))
            || ($nq !== '' && $rcpt !== '' && str_contains($rcpt, $nq));
    };
@endphp

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-violet-700 via-indigo-600 to-blue-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white">بحث معلومات الدفع</h1>
                <p class="text-indigo-200 text-sm mt-1">ابحث برقم الآيبان أو اسم المستلم في السجلّين: العادي و AI</p>
            </div>
        </div>
        @if($members)
        <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
            <p class="text-white font-black text-2xl leading-none">{{ number_format($members->total()) }}</p>
            <p class="text-indigo-200 text-xs mt-0.5">نتيجة</p>
        </div>
        @endif
    </div>
</div>

{{-- Search form --}}
<form method="GET" action="{{ route('payment-review.search') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5 mb-6">
    <div class="relative">
        <svg class="w-5 h-5 text-gray-400 absolute top-1/2 -translate-y-1/2 right-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="q" value="{{ $q }}" autofocus
               placeholder="رقم الآيبان، اسم المستلم، اسم المستفيد، أو رقم الاضبارة…"
               class="w-full border-2 border-gray-200 rounded-xl ps-11 pe-3 py-3 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition-colors">
    </div>

    <div class="flex flex-wrap items-center gap-2 mt-3">
        @foreach(['all' => 'الكل', 'iban' => 'رقم الآيبان', 'recipient' => 'اسم المستلم'] as $key => $label)
            <label class="cursor-pointer">
                <input type="radio" name="field" value="{{ $key }}" class="peer sr-only" {{ $field === $key ? 'checked' : '' }}
                       onchange="this.form.submit()">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                             border-gray-200 text-gray-500 hover:bg-gray-50
                             peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600">
                    {{ $label }}
                </span>
            </label>
        @endforeach

        <button type="submit" class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors mr-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            بحث
        </button>
        @if($q !== '')
        <a href="{{ route('payment-review.search') }}" class="inline-flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold px-4 py-2 rounded-xl transition-colors">مسح</a>
        @endif
    </div>
    <p class="text-[11px] text-gray-400 mt-2">يمكن لصق الآيبان مع أو بدون مسافات/شرطات — البحث يتجاهلها.</p>
</form>

{{-- Results --}}
@if($members === null)
    <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-14 text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-indigo-50 flex items-center justify-center mb-4">
            <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <p class="text-gray-500 text-sm">اكتب رقم آيبان أو اسم مستلم في الحقل أعلاه لبدء البحث.</p>
    </div>
@elseif($members->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-14 text-center">
        <p class="text-gray-500 text-sm">لا توجد نتائج مطابقة لـ «<span class="font-bold text-gray-700">{{ $q }}</span>».</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($members as $member)
            @php
                $mNormal = $matches($member->paymentInfo);
                $mAi     = $matches($member->paymentInfoAI);
                $mMember = ! $mNormal && ! $mAi;
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Member header --}}
                <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-3.5 border-b border-gray-100 bg-gray-50/60">
                    <div class="flex flex-wrap items-center gap-2 min-w-0">
                        <a href="{{ route('members.show', $member) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-700 transition-colors truncate">
                            {{ $member->full_name }}
                        </a>
                        @if($member->dossier_number)
                            <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-full px-2 py-0.5">ملف {{ $member->dossier_number }}</span>
                        @endif
                        @if($member->verificationStatus)
                            <span class="text-[11px] text-gray-500 bg-gray-100 border border-gray-200 rounded-full px-2 py-0.5">{{ $member->verificationStatus->name }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($member->phone)
                            <span class="text-xs text-gray-500 font-mono" dir="ltr">{{ $member->phone }}</span>
                        @endif
                        @if($mNormal)<span class="text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-full px-2 py-0.5">تطابق: العادي</span>@endif
                        @if($mAi)<span class="text-[10px] font-bold text-violet-700 bg-violet-50 border border-violet-200 rounded-full px-2 py-0.5">تطابق: AI</span>@endif
                        @if($mMember)<span class="text-[10px] font-bold text-gray-600 bg-gray-100 border border-gray-200 rounded-full px-2 py-0.5">تطابق: بيانات المستفيد</span>@endif
                    </div>
                </div>

                {{-- Two sources --}}
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-gray-100">
                    {{-- العادي --}}
                    <div class="p-5 {{ $mNormal ? 'bg-blue-50/40' : '' }}">
                        <p class="text-xs font-bold text-blue-700 mb-2 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> السجل العادي
                        </p>
                        @if($member->paymentInfo && ($member->paymentInfo->iban || $member->paymentInfo->recipient_name))
                            <div class="space-y-2">
                                <div>
                                    <p class="text-[11px] text-gray-400">رقم الآيبان</p>
                                    <p class="text-sm font-mono text-gray-800 break-all" dir="ltr">{!! $hl($member->paymentInfo->iban, $q) !!}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400">اسم المستلم</p>
                                    <p class="text-sm text-gray-800">{!! $hl($member->paymentInfo->recipient_name, $q) !!}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-gray-300">لا توجد بيانات دفع عادية</p>
                        @endif
                    </div>

                    {{-- AI --}}
                    <div class="p-5 {{ $mAi ? 'bg-violet-50/40' : '' }}">
                        <p class="text-xs font-bold text-violet-700 mb-2 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-violet-400"></span> سجل AI
                        </p>
                        @if($member->paymentInfoAI && ($member->paymentInfoAI->iban || $member->paymentInfoAI->recipient_name))
                            <div class="space-y-2">
                                <div>
                                    <p class="text-[11px] text-gray-400">رقم الآيبان</p>
                                    <p class="text-sm font-mono text-gray-800 break-all" dir="ltr">{!! $hl($member->paymentInfoAI->iban, $q) !!}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] text-gray-400">اسم المستلم</p>
                                    <p class="text-sm text-gray-800">{!! $hl($member->paymentInfoAI->recipient_name, $q) !!}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-gray-300">لا توجد بيانات دفع AI</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $members->links() }}
    </div>
@endif

@endsection
