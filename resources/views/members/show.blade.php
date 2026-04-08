@extends('layouts.app')

@section('title', $member->full_name . ' — مسالك النور')
@section('max-width', 'max-w-5xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">{{ $member->full_name }}</span>
@endsection

@section('content')

{{-- ── Hero header ── --}}
<div class="relative bg-gradient-to-l from-emerald-600 via-emerald-500 to-teal-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    {{-- Background decoration --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-40 h-40 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-56 h-56 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>

    <div class="relative flex items-start justify-between">
        <div class="flex items-center gap-4">
            {{-- Avatar --}}
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur border-2 border-white/40 flex items-center justify-center shadow-lg shrink-0">
                <span class="text-white font-black text-2xl drop-shadow">{{ mb_substr($member->full_name, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white drop-shadow-sm">{{ $member->full_name }}</h1>
                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                    @if($member->dossier_number)
                        <span class="text-xs bg-white/20 text-white border border-white/30 rounded-full px-2.5 py-0.5 font-medium backdrop-blur-sm">
                            ملف: {{ $member->dossier_number }}
                        </span>
                    @endif
                    @if($member->verificationStatus)
                        <span class="text-xs bg-white/20 text-white border border-white/30 rounded-full px-2.5 py-0.5 font-medium backdrop-blur-sm">
                            {{ $member->verificationStatus->name }}
                        </span>
                    @endif
                    @if($member->marital_status)
                        <span class="text-xs bg-white/20 text-white border border-white/30 rounded-full px-2.5 py-0.5 font-medium backdrop-blur-sm">
                            {{ $member->marital_status }}
                        </span>
                    @endif
                    @if($member->gender)
                        <span class="text-xs bg-white/20 text-white border border-white/30 rounded-full px-2.5 py-0.5 font-medium backdrop-blur-sm">
                            {{ $member->gender }}
                        </span>
                    @endif
                    @if($member->finalStatus)
                        <span class="text-xs rounded-full px-2.5 py-0.5 font-medium border border-white/30 backdrop-blur-sm"
                              style="background:{{ $member->finalStatus->color }}40; color:white;">
                            {{ $member->finalStatus->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('members.edit', $member) }}"
               class="flex items-center gap-2 bg-white text-emerald-700 hover:bg-emerald-50 text-sm font-semibold px-4 py-2 rounded-xl transition-colors shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل
            </a>
            <a href="{{ route('members.index') }}"
               class="text-sm text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/30 px-4 py-2 rounded-xl transition-colors backdrop-blur-sm">
                رجوع
            </a>
        </div>
    </div>
</div>

{{-- ── Stats banner ── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="relative bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-4 text-white shadow-md overflow-hidden">
        <div class="absolute -bottom-3 -left-3 w-16 h-16 bg-white/10 rounded-full"></div>
        <p class="text-emerald-100 text-xs font-medium mb-1">مجموع النقاط</p>
        <p class="text-4xl font-black">{{ $member->score ?? '—' }}</p>
        <p class="text-emerald-200 text-xs mt-0.5">نقطة تقييم</p>
    </div>
    <div class="relative bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-4 text-white shadow-md overflow-hidden">
        <div class="absolute -bottom-3 -left-3 w-16 h-16 bg-white/10 rounded-full"></div>
        <p class="text-blue-100 text-xs font-medium mb-1">المبلغ المقدر</p>
        <p class="text-2xl font-black">{{ $member->estimated_amount ? number_format($member->estimated_amount, 0) : '—' }}</p>
        <p class="text-blue-200 text-xs mt-0.5">ل.س</p>
    </div>
    <div class="relative bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-4 text-white shadow-md overflow-hidden">
        <div class="absolute -bottom-3 -left-3 w-16 h-16 bg-white/10 rounded-full"></div>
        <p class="text-purple-100 text-xs font-medium mb-1">المبلغ النهائي</p>
        <p class="text-2xl font-black">{{ $member->final_amount ? number_format($member->final_amount, 0) : '—' }}</p>
        <p class="text-purple-200 text-xs mt-0.5">ل.س</p>
    </div>
    <div class="relative bg-gradient-to-br from-violet-500 to-violet-700 rounded-2xl p-4 text-white shadow-md overflow-hidden">
        <div class="absolute -bottom-3 -left-3 w-16 h-16 bg-white/10 rounded-full"></div>
        <p class="text-violet-100 text-xs font-medium mb-1">عدد المعالين</p>
        <p class="text-4xl font-black">{{ $member->dependents_count ?? '—' }}</p>
        <p class="text-violet-200 text-xs mt-0.5">فرد</p>
    </div>
    <div class="relative bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl p-4 text-white shadow-md overflow-hidden">
        <div class="absolute -bottom-3 -left-3 w-16 h-16 bg-white/10 rounded-full"></div>
        <p class="text-orange-100 text-xs font-medium mb-1">تاريخ التسجيل</p>
        <p class="text-lg font-black leading-tight">{{ $member->created_at->format('Y/m/d') }}</p>
        <p class="text-orange-200 text-xs mt-0.5">{{ $member->created_at->diffForHumans() }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Right column (2/3) ── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- البيانات الشخصية --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 bg-gradient-to-l from-blue-50 to-indigo-50 border-b border-blue-100 px-6 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-blue-800">البيانات الشخصية</h2>
            </div>
            <div class="grid grid-cols-2 divide-x divide-x-reverse divide-gray-50">
                @php
                    $personal = [
                        'الاسم الكامل'       => $member->full_name,
                        'اسم الأم'           => $member->mother_name,
                        'العمر'              => $member->age ? $member->age . ' سنة' : null,
                        'الجنس'             => $member->gender,
                        'رقم الهوية'         => $member->national_id,
                        'رقم الهاتف'         => $member->phone,
                        'الحالة الاجتماعية'  => $member->marital_status,
                        'الحالة النهائية'    => $member->finalStatus?->name,
                        'المدخل'             => $member->representative?->name,
                        'مندوب'              => $member->delegate,
                        'العنوان'            => $member->current_address,
                    ];
                @endphp
                @foreach($personal as $label => $value)
                    <div class="px-5 py-3.5 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50 transition-colors">
                        <p class="text-xs text-gray-400 mb-0.5 font-medium">{{ $label }}</p>
                        <p class="text-sm font-semibold {{ $value ? 'text-gray-800' : 'text-gray-300' }}">{{ $value ?? '—' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- الوضع المعيشي والعمل --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 bg-gradient-to-l from-amber-50 to-yellow-50 border-b border-amber-100 px-6 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-amber-800">الوضع المعيشي والعمل</h2>
            </div>
            <div class="grid grid-cols-2 divide-x divide-x-reverse divide-gray-50">
                @php
                    $living = [
                        'الوظيفة / العمل' => $member->job,
                        'وضع السكن'       => $member->housing_status,
                        'الشبكة'          => $member->network,
                        'حالة المشغل'     => $member->provider_status,
                    ];
                @endphp
                @foreach($living as $label => $value)
                    <div class="px-5 py-3.5 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50 transition-colors">
                        <p class="text-xs text-gray-400 mb-0.5 font-medium">{{ $label }}</p>
                        <p class="text-sm font-semibold {{ $value ? 'text-gray-800' : 'text-gray-300' }}">{{ $value ?? '—' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- الحالة الصحية --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 bg-gradient-to-l from-rose-50 to-pink-50 border-b border-rose-100 px-6 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-rose-800">الحالة الصحية</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 mb-1 font-medium">نوع المرض / الحالة</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $member->disease_type ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl p-3 {{ $member->special_cases ? 'bg-orange-50 border border-orange-100' : 'bg-gray-50' }} flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                            {{ $member->special_cases ? 'bg-orange-100' : 'bg-gray-200' }}">
                            <svg class="w-4 h-4 {{ $member->special_cases ? 'text-orange-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">حالة خاصة</p>
                            <p class="text-sm font-bold {{ $member->special_cases ? 'text-orange-600' : 'text-gray-400' }}">
                                {{ $member->special_cases ? 'نعم' : 'لا' }}
                            </p>
                        </div>
                    </div>
                </div>
                @if($member->illness_details)
                    <div>
                        <p class="text-xs text-gray-400 mb-1.5 font-medium">تفاصيل المرض</p>
                        <p class="text-sm text-gray-700 leading-relaxed bg-rose-50 rounded-xl p-3.5 border border-rose-100">{{ $member->illness_details }}</p>
                    </div>
                @endif
                @if($member->special_cases && $member->special_cases_description)
                    <div>
                        <p class="text-xs text-gray-400 mb-1.5 font-medium">وصف الحالة الخاصة</p>
                        <p class="text-sm text-gray-700 leading-relaxed bg-orange-50 rounded-xl p-3.5 border border-orange-100">{{ $member->special_cases_description }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- الجمعيات --}}
        @if($member->associations->count())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 bg-gradient-to-l from-cyan-50 to-sky-50 border-b border-cyan-100 px-6 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-cyan-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-cyan-800">الجمعيات المنتسب إليها</h2>
            </div>
            <div class="p-5 flex flex-wrap gap-2">
                @foreach($member->associations as $assoc)
                    <span class="text-sm bg-gradient-to-l from-cyan-50 to-sky-50 text-cyan-700 border border-cyan-200 rounded-xl px-3.5 py-1.5 font-semibold shadow-sm">
                        {{ $assoc->name }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- ── Left column (1/3) ── --}}
    <div class="space-y-5">

        {{-- بيانات إضافية --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 bg-gradient-to-l from-violet-50 to-purple-50 border-b border-violet-100 px-6 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-violet-800">بيانات إضافية</h2>
            </div>
            <div class="p-5 space-y-4">
                @if($member->verificationStatus)
                    <div>
                        <p class="text-xs text-gray-400 mb-1.5 font-medium">حالة التحقق</p>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold rounded-xl px-3 py-1.5 border shadow-sm"
                              style="color: {{ $member->verificationStatus->color }}; border-color: {{ $member->verificationStatus->color }}40; background: {{ $member->verificationStatus->color }}15">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $member->verificationStatus->color }}"></span>
                            {{ $member->verificationStatus->name }}
                        </span>
                    </div>
                @endif

                <div class="space-y-2">
                    {{-- Sham Cash --}}
                    @php
                        $shamLabels = ['done' => ['label' => 'نعم (تم)', 'color' => 'emerald'], 'manual' => ['label' => 'يدوي', 'color' => 'amber']];
                        $shamInfo = $shamLabels[$member->sham_cash_account] ?? null;
                    @endphp
                    <div class="flex items-center justify-between p-2.5 rounded-xl {{ $shamInfo ? 'bg-emerald-50' : 'bg-gray-50' }}">
                        <span class="text-sm font-medium {{ $shamInfo ? 'text-emerald-700' : 'text-gray-500' }}">حساب شام كاش</span>
                        @if($shamInfo)
                            <span class="text-xs bg-{{ $shamInfo['color'] }}-100 text-{{ $shamInfo['color'] }}-700 border border-{{ $shamInfo['color'] }}-200 rounded-full px-2.5 py-0.5 font-bold">{{ $shamInfo['label'] }}</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-400 rounded-full px-2.5 py-0.5 font-medium">لا</span>
                        @endif
                    </div>
                    {{-- Other Association --}}
                    <div class="flex items-center justify-between p-2.5 rounded-xl {{ $member->other_association ? 'bg-blue-50' : 'bg-gray-50' }}">
                        <span class="text-sm font-medium {{ $member->other_association ? 'text-blue-700' : 'text-gray-500' }}">جمعية أخرى</span>
                        @if($member->other_association)
                            <span class="text-xs bg-blue-100 text-blue-700 border border-blue-200 rounded-full px-2.5 py-0.5 font-bold">نعم</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-400 rounded-full px-2.5 py-0.5 font-medium">لا</span>
                        @endif
                    </div>
                </div>

                @if($member->representative)
                    <div class="flex items-center gap-2.5 p-2.5 bg-emerald-50 rounded-xl">
                        <div class="w-7 h-7 rounded-full bg-emerald-200 flex items-center justify-center text-xs font-black text-emerald-700 shrink-0">
                            {{ mb_substr($member->representative->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs text-emerald-600 font-medium">الممثل المسؤول</p>
                            <p class="text-sm font-bold text-emerald-800">{{ $member->representative->name }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- نقاط التقييم --}}
        @if($member->scores)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 bg-gradient-to-l from-emerald-50 to-teal-50 border-b border-emerald-100 px-6 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-emerald-800">نقاط التقييم</h2>
            </div>
            <div class="p-5 space-y-3.5">
                @php
                    $scoreItems = [
                        ['label' => 'نقاط حالة المعيل',    'value' => $member->scores->dependent_status_score, 'max' => 2,  'color' => '#8b5cf6'],
                        ['label' => 'نقاط العمل',           'value' => $member->scores->work_score,             'max' => 2,  'color' => '#f59e0b'],
                        ['label' => 'نقاط السكن',           'value' => $member->scores->housing_score,          'max' => 4,  'color' => '#3b82f6'],
                        ['label' => 'نقاط عدد الأفراد',    'value' => $member->scores->dependents_score,       'max' => 20, 'color' => '#06b6d4'],
                        ['label' => 'نقاط المرض',           'value' => $member->scores->illness_score,          'max' => 5,  'color' => '#ef4444'],
                        ['label' => 'نقاط الحالات الخاصة', 'value' => $member->scores->special_cases_score,   'max' => 10, 'color' => '#f97316'],
                    ];
                @endphp
                @foreach($scoreItems as $item)
                    @php $pct = $item['max'] > 0 ? ($item['value'] / $item['max']) * 100 : 0; @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="text-gray-600 font-medium">{{ $item['label'] }}</span>
                            <span class="font-bold text-gray-700">{{ $item['value'] }} <span class="text-gray-400 font-normal">/ {{ $item['max'] }}</span></span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width: {{ $pct }}%; background: {{ $item['color'] }}"></div>
                        </div>
                    </div>
                @endforeach
                <div class="pt-3 border-t border-gray-100 flex justify-between items-center bg-gradient-to-l from-emerald-50 to-teal-50 rounded-xl px-3 py-2.5 mt-1">
                    <span class="text-sm font-bold text-emerald-700">المجموع الكلي</span>
                    <span class="text-2xl font-black text-emerald-600">{{ $member->scores->total_score }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- معلومات الدفع AI --}}
        @if($member->paymentInfoAI && ($member->paymentInfoAI->iban || $member->paymentInfoAI->barcode || $member->paymentInfoAI->recipient_name))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 bg-gradient-to-l from-violet-50 to-purple-50 border-b border-violet-100 px-6 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-violet-700">معلومات الدفع AI</h2>
            </div>
            <div class="p-5 space-y-4">
                @if($member->paymentInfoAI->recipient_name)
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium">اسم المستلم AI</p>
                        <p class="text-sm font-semibold text-gray-800 bg-violet-50 rounded-xl px-3 py-2 border border-violet-100">{{ $member->paymentInfoAI->recipient_name }}</p>
                    </div>
                @endif
                @if($member->paymentInfoAI->iban)
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium">رقم الآيبان AI</p>
                        <p class="text-sm font-mono font-semibold text-gray-800 bg-violet-50 rounded-xl px-3 py-2 break-all border border-violet-100">{{ $member->paymentInfoAI->iban }}</p>
                    </div>
                @endif
                @if($member->paymentInfoAI->barcode)
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium">الباركود AI</p>
                        <p class="text-sm font-mono font-semibold text-gray-800 bg-violet-50 rounded-xl px-3 py-2 break-all border border-violet-100">{{ $member->paymentInfoAI->barcode }}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- معلومات الدفع --}}
        @if($member->paymentInfo && ($member->paymentInfo->iban || $member->paymentInfo->barcode || $member->paymentInfo->iban_image || $member->paymentInfo->barcode_image || $member->paymentInfo->recipient_name))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center gap-2.5 bg-gradient-to-l from-slate-50 to-gray-50 border-b border-gray-100 px-6 py-3.5">
                <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-slate-700">معلومات الدفع</h2>
            </div>
            <div class="p-5 space-y-4">
                @if($member->paymentInfo->recipient_name)
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium">اسم المستلم</p>
                        <p class="text-sm font-semibold text-gray-800 bg-slate-50 rounded-xl px-3 py-2 border border-slate-100">{{ $member->paymentInfo->recipient_name }}</p>
                    </div>
                @endif
                @if($member->paymentInfo->iban)
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium">رقم الآيبان</p>
                        <p class="text-sm font-mono font-semibold text-gray-800 bg-slate-50 rounded-xl px-3 py-2 break-all border border-slate-100">{{ $member->paymentInfo->iban }}</p>
                    </div>
                @endif
                @if($member->paymentInfo->iban_image)
                    <div>
                        <p class="text-xs text-gray-400 mb-1.5 font-medium">صورة الآيبان</p>
                        <a href="{{ Storage::url($member->paymentInfo->iban_image) }}" target="_blank" class="block group">
                            <img src="{{ Storage::url($member->paymentInfo->iban_image) }}" alt="صورة الآيبان"
                                 class="w-full rounded-xl border border-gray-200 object-contain max-h-40 bg-gray-50 group-hover:opacity-90 transition-opacity cursor-zoom-in shadow-sm">
                        </a>
                    </div>
                @endif
                @if($member->paymentInfo->barcode)
                    <div>
                        <p class="text-xs text-gray-400 mb-1 font-medium">الباركود</p>
                        <p class="text-sm font-mono font-semibold text-gray-800 bg-slate-50 rounded-xl px-3 py-2 break-all border border-slate-100">{{ $member->paymentInfo->barcode }}</p>
                    </div>
                @endif
                @if($member->paymentInfo->barcode_image)
                    <div>
                        <p class="text-xs text-gray-400 mb-1.5 font-medium">صورة الباركود</p>
                        <a href="{{ Storage::url($member->paymentInfo->barcode_image) }}" target="_blank" class="block group">
                            <img src="{{ Storage::url($member->paymentInfo->barcode_image) }}" alt="صورة الباركود"
                                 class="w-full rounded-xl border border-gray-200 object-contain max-h-40 bg-gray-50 group-hover:opacity-90 transition-opacity cursor-zoom-in shadow-sm">
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endif

    </div>

</div>

{{-- ── الجولات الميدانية ── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
    <div class="flex items-center gap-2.5 bg-gradient-to-l from-indigo-50 to-violet-50 border-b border-indigo-100 px-6 py-3.5">
        <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h2 class="text-sm font-bold text-indigo-700">الجولات الميدانية</h2>
        <span class="text-xs text-indigo-500 bg-indigo-100 rounded-full px-2 py-0.5 font-semibold">{{ $member->fieldVisits->count() }}</span>
    </div>

    <div class="p-5 space-y-4">

        {{-- Existing visits --}}
        @forelse($member->fieldVisits as $visit)
        <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50 group relative" id="visit-{{ $visit->id }}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($visit->status)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full text-white"
                                  style="background: {{ $visit->status->color }}">
                                {{ $visit->status->name }}
                            </span>
                        @endif
                        @if($visit->visit_date)
                            <span class="text-xs text-gray-500 font-medium">
                                <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $visit->visit_date->format('Y/m/d') }}
                            </span>
                        @endif
                        @if($visit->visitor)
                            <span class="text-xs text-gray-500">
                                <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $visit->visitor }}
                            </span>
                        @endif
                    </div>
                    @if($visit->estimated_amount !== null)
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-emerald-700">{{ number_format($visit->estimated_amount) }} ل.س</span>
                            @if($visit->amount_reason)
                                <span class="text-xs text-gray-500 italic">— {{ $visit->amount_reason }}</span>
                            @endif
                        </div>
                    @endif
                    @if($visit->notes)
                        <p class="text-xs text-gray-600 bg-white rounded-lg px-3 py-2 border border-gray-100">{{ $visit->notes }}</p>
                    @endif
                </div>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                    <button onclick="toggleEditVisit({{ $visit->id }})"
                            class="p-1.5 rounded-lg text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('field-visits.destroy', [$member, $visit]) }}"
                          onsubmit="return confirm('حذف هذه الجولة؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Edit form (hidden) --}}
            <div id="edit-visit-{{ $visit->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                <form method="POST" action="{{ route('field-visits.update', [$member, $visit]) }}">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">الحالة</label>
                            <select name="field_visit_status_id" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">— غير محدد —</option>
                                @foreach($fieldVisitStatuses as $s)
                                    <option value="{{ $s->id }}" {{ $visit->field_visit_status_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">تاريخ الزيارة</label>
                            <input type="date" name="visit_date" value="{{ $visit->visit_date?->format('Y-m-d') }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">اسم الزائر</label>
                            <input type="text" name="visitor" value="{{ $visit->visitor }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">المبلغ المقدر (ل.س)</label>
                            <input type="number" name="estimated_amount" value="{{ $visit->estimated_amount }}" min="0" step="0.01"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">سبب المبلغ</label>
                            <input type="text" name="amount_reason" value="{{ $visit->amount_reason }}"
                                   placeholder="وصف موجز لسبب تحديد هذا المبلغ"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2"
                                      class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ $visit->notes }}</textarea>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors">حفظ التعديلات</button>
                        <button type="button" onclick="toggleEditVisit({{ $visit->id }})"
                                class="text-xs text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-4">لا توجد جولات ميدانية مسجّلة بعد.</p>
        @endforelse

        {{-- Add new visit form --}}
        <div class="border-t border-dashed border-gray-200 pt-4">
            <button onclick="toggleAddVisit()" id="add-visit-btn"
                    class="flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                إضافة جولة ميدانية
            </button>
            <div id="add-visit-form" class="hidden mt-4">
                <form method="POST" action="{{ route('field-visits.store', $member) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">الحالة</label>
                            <select name="field_visit_status_id" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">— غير محدد —</option>
                                @foreach($fieldVisitStatuses as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">تاريخ الزيارة</label>
                            <input type="date" name="visit_date"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">اسم الزائر</label>
                            <input type="text" name="visitor" placeholder="الاسم الكامل للزائر"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">المبلغ المقدر (ل.س)</label>
                            <input type="number" name="estimated_amount" min="0" step="0.01" placeholder="0"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">سبب المبلغ</label>
                            <input type="text" name="amount_reason" placeholder="وصف موجز لسبب تحديد هذا المبلغ"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2" placeholder="ملاحظات الجولة..."
                                      class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors">إضافة الجولة</button>
                        <button type="button" onclick="toggleAddVisit()"
                                class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- ── أرشيف الصور ── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
        <div class="flex items-center justify-between bg-gradient-to-l from-violet-50 to-purple-50 border-b border-violet-100 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-violet-800">أرشيف الصور والمستندات</h2>
                    <p class="text-xs text-violet-400 mt-0.5">
                        {{ $member->images->count() ? $member->images->count() . ' ملف مرفق' : 'لا توجد ملفات مرفقة' }}
                    </p>
                </div>
            </div>
            <button onclick="document.getElementById('upload-panel').classList.toggle('hidden')"
                    class="flex items-center gap-2 text-sm bg-violet-600 hover:bg-violet-700 text-white font-semibold px-4 py-2 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                رفع ملف
            </button>
        </div>

        {{-- Upload form (hidden by default) --}}
        <div id="upload-panel" class="hidden border-b border-violet-100 bg-violet-50/50 px-6 py-5">
            @if(session('success'))
                <div class="mb-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('member-images.store', $member) }}" method="POST" enctype="multipart/form-data"
                  class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">الملف <span class="text-red-400">*</span></label>
                    <input type="file" name="image" accept="image/*,.pdf" required
                           class="block w-full text-sm text-gray-600 file:mr-0 file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-100 file:text-violet-700 hover:file:bg-violet-200 border border-gray-200 rounded-xl bg-white px-2 py-2 cursor-pointer transition">
                    @error('image')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-full sm:w-64">
                    <label class="block text-sm font-semibold text-gray-500 mb-1.5">وصف الملف (اختياري)</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="مثال: هوية شخصية..."
                           class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition">
                </div>
                <button type="submit"
                        class="flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-colors shadow-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    رفع
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-3">الأنواع المدعومة: JPG, PNG, GIF, WEBP, PDF — الحجم الأقصى: 10 ميغابايت</p>
        </div>

        {{-- Gallery --}}
        <div class="p-6">
            @if($member->images->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 18h18M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v12a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/>
                        </svg>
                    </div>
                    <p class="text-base text-gray-400 font-semibold">لا توجد صور أو مستندات مرفقة</p>
                    <p class="text-sm text-gray-300 mt-1">استخدم زر "رفع ملف" لإضافة أول مستند</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
                    @foreach($member->images as $img)
                        <div class="group relative bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden hover:border-violet-300 hover:shadow-lg transition-all">
                            {{-- Thumbnail --}}
                            @if($img->isImage())
                                <a href="{{ $img->url }}" target="_blank" class="block overflow-hidden bg-gray-100" style="height: 200px">
                                    <img src="{{ $img->url }}" alt="{{ $img->title ?? $img->file_name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 cursor-zoom-in">
                                </a>
                            @else
                                <a href="{{ $img->url }}" target="_blank"
                                   class="flex flex-col items-center justify-center bg-red-50 hover:bg-red-100 transition-colors cursor-pointer" style="height: 200px">
                                    <svg class="w-14 h-14 text-red-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-red-500 uppercase tracking-widest">PDF</span>
                                </a>
                            @endif

                            {{-- Info bar --}}
                            <div class="px-4 py-3 border-t border-gray-100 bg-white">
                                <p class="text-sm font-semibold text-gray-700 truncate" title="{{ $img->title ?? $img->file_name }}">
                                    {{ $img->title ?: $img->file_name }}
                                </p>
                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="text-xs text-gray-400">{{ $img->file_size_human }}</span>
                                    <span class="text-xs text-gray-400">{{ $img->created_at->format('d/m/Y') }}</span>
                                </div>
                                @if($img->uploader)
                                    <p class="text-xs text-violet-400 truncate mt-1">{{ $img->uploader->name }}</p>
                                @endif
                            </div>

                            {{-- Delete button --}}
                            <form action="{{ route('member-images.destroy', $img) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟')"
                                  class="absolute top-2.5 left-2.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center bg-red-500 hover:bg-red-600 text-white rounded-lg shadow-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

{{-- Auto-open upload panel if there are validation errors on the image field --}}
@if($errors->has('image'))
<script>document.getElementById('upload-panel').classList.remove('hidden');</script>
@endif

<script>
function toggleAddVisit() {
    const form = document.getElementById('add-visit-form');
    const btn  = document.getElementById('add-visit-btn');
    form.classList.toggle('hidden');
    btn.classList.toggle('hidden');
}
function toggleEditVisit(id) {
    document.getElementById('edit-visit-' + id).classList.toggle('hidden');
}
</script>

@endsection
