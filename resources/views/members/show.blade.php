@extends('layouts.app')

@section('title', $member->full_name . ' — مسالك النور')
@section('max-width', 'max-w-5xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">{{ $member->full_name }}</span>
@endsection

@section('content')

{{-- ── Page header ── --}}
<div class="flex items-start justify-between mb-6">
    <div class="flex items-center gap-4">
        {{-- Avatar --}}
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-md shrink-0">
            <span class="text-white font-black text-xl">{{ mb_substr($member->full_name, 0, 1) }}</span>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $member->full_name }}</h1>
            <div class="flex items-center gap-2 mt-1">
                @if($member->dossier_number)
                    <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2.5 py-0.5 font-medium">
                        ملف: {{ $member->dossier_number }}
                    </span>
                @endif
                @if($member->verificationStatus)
                    <span class="text-xs rounded-full px-2.5 py-0.5 font-medium border"
                          style="color: {{ $member->verificationStatus->color }}; border-color: {{ $member->verificationStatus->color }}20; background: {{ $member->verificationStatus->color }}12">
                        {{ $member->verificationStatus->name }}
                    </span>
                @endif
                @if($member->marital_status)
                    <span class="text-xs bg-purple-50 text-purple-700 border border-purple-100 rounded-full px-2.5 py-0.5 font-medium">
                        {{ $member->marital_status }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="{{ route('members.edit', $member) }}"
           class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            تعديل
        </a>
        <a href="{{ route('members.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 border border-gray-200 hover:border-gray-300 px-4 py-2 rounded-lg transition-colors">
            رجوع
        </a>
    </div>
</div>

{{-- ── Score banner ── --}}
@if($member->score)
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-2xl p-4 text-white col-span-1">
        <p class="text-emerald-100 text-xs mb-1">مجموع النقاط</p>
        <p class="text-3xl font-black">{{ $member->score }}</p>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-xs mb-1">المبلغ المقدر</p>
        <p class="text-xl font-bold text-gray-800">{{ number_format($member->estimated_amount, 0) }}</p>
        <p class="text-gray-400 text-xs">ل.س</p>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-xs mb-1">عدد المعالين</p>
        <p class="text-xl font-bold text-gray-800">{{ $member->dependents_count ?? '—' }}</p>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-xs mb-1">تاريخ التسجيل</p>
        <p class="text-sm font-semibold text-gray-800">{{ $member->created_at->format('Y/m/d') }}</p>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- ── Right column (2/3) ── --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- البيانات الشخصية --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700">البيانات الشخصية</h2>
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
                        'المندوب'            => $member->representative?->name,
                        'مندوب خارجي'        => $member->delegate,
                        'العنوان'            => $member->current_address,
                    ];
                @endphp
                @foreach($personal as $label => $value)
                    <div class="px-5 py-3 border-b border-gray-50 last:border-b-0">
                        <p class="text-xs text-gray-400 mb-0.5">{{ $label }}</p>
                        <p class="text-sm font-medium text-gray-800">{{ $value ?? '—' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- الوضع المعيشي والعمل --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700">الوضع المعيشي والعمل</h2>
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
                    <div class="px-5 py-3 border-b border-gray-50 last:border-b-0">
                        <p class="text-xs text-gray-400 mb-0.5">{{ $label }}</p>
                        <p class="text-sm font-medium text-gray-800">{{ $value ?? '—' }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- الحالة الصحية --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700">الحالة الصحية</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">نوع المرض / الحالة</p>
                        <p class="text-sm font-medium text-gray-800">{{ $member->disease_type ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0
                            {{ $member->special_cases ? 'bg-orange-100' : 'bg-gray-100' }}">
                            <span class="w-2 h-2 rounded-full {{ $member->special_cases ? 'bg-orange-500' : 'bg-gray-300' }}"></span>
                        </span>
                        <div>
                            <p class="text-xs text-gray-400">حالة خاصة</p>
                            <p class="text-sm font-medium {{ $member->special_cases ? 'text-orange-600' : 'text-gray-400' }}">
                                {{ $member->special_cases ? 'نعم' : 'لا' }}
                            </p>
                        </div>
                    </div>
                </div>
                @if($member->illness_details)
                    <div>
                        <p class="text-xs text-gray-400 mb-1">تفاصيل المرض</p>
                        <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 rounded-xl p-3">{{ $member->illness_details }}</p>
                    </div>
                @endif
                @if($member->special_cases && $member->special_cases_description)
                    <div>
                        <p class="text-xs text-gray-400 mb-1">وصف الحالة الخاصة</p>
                        <p class="text-sm text-gray-700 leading-relaxed bg-orange-50 rounded-xl p-3 border border-orange-100">{{ $member->special_cases_description }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- الجمعيات المنتسب إليها --}}
        @if($member->associations->count())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700">الجمعيات المنتسب إليها</h2>
            </div>
            <div class="p-5 flex flex-wrap gap-2">
                @foreach($member->associations as $assoc)
                    <span class="text-sm bg-blue-50 text-blue-700 border border-blue-100 rounded-lg px-3 py-1.5 font-medium">
                        {{ $assoc->name }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- ── Left column (1/3) ── --}}
    <div class="space-y-5">

        {{-- حالة التحقق والبيانات الإضافية --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700">بيانات إضافية</h2>
            </div>
            <div class="p-5 space-y-4">
                @if($member->verificationStatus)
                    <div>
                        <p class="text-xs text-gray-400 mb-1">حالة التحقق</p>
                        <span class="inline-block text-xs font-semibold rounded-lg px-3 py-1.5 border"
                              style="color: {{ $member->verificationStatus->color }}; border-color: {{ $member->verificationStatus->color }}30; background: {{ $member->verificationStatus->color }}15">
                            {{ $member->verificationStatus->name }}
                        </span>
                    </div>
                @endif

                <div class="space-y-2.5">
                    @php
                        $flags = [
                            ['label' => 'حساب شام كاش',    'value' => $member->sham_cash_account],
                            ['label' => 'جمعية أخرى',       'value' => $member->other_association],
                        ];
                    @endphp
                    @foreach($flags as $flag)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ $flag['label'] }}</span>
                            @if($flag['value'])
                                <span class="text-xs bg-emerald-100 text-emerald-700 rounded-full px-2 py-0.5 font-medium">نعم</span>
                            @else
                                <span class="text-xs bg-gray-100 text-gray-400 rounded-full px-2 py-0.5">لا</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($member->representative)
                    <div>
                        <p class="text-xs text-gray-400 mb-1">الممثل المسؤول</p>
                        <p class="text-sm font-medium text-gray-800">{{ $member->representative->name }}</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- نقاط التقييم --}}
        @if($member->scores)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700">نقاط التقييم</h2>
            </div>
            <div class="p-5 space-y-3">
                @php
                    $scoreItems = [
                        ['label' => 'نقاط العمل',           'value' => $member->scores->work_score,          'max' => 2],
                        ['label' => 'نقاط السكن',           'value' => $member->scores->housing_score,        'max' => 4],
                        ['label' => 'نقاط عدد الأفراد',    'value' => $member->scores->dependents_score,          'max' => 20],
                        ['label' => 'نقاط حالة المعيل',    'value' => $member->scores->dependent_status_score,   'max' => 2],
                        ['label' => 'نقاط المرض',           'value' => $member->scores->illness_score,            'max' => 5],
                        ['label' => 'نقاط الحالات الخاصة', 'value' => $member->scores->special_cases_score,  'max' => 10],
                    ];
                @endphp
                @foreach($scoreItems as $item)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-500">{{ $item['label'] }}</span>
                            <span class="font-semibold text-gray-700">{{ $item['value'] }} / {{ $item['max'] }}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all"
                                 style="width: {{ $item['max'] > 0 ? ($item['value'] / $item['max']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
                <div class="pt-2 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">المجموع</span>
                    <span class="text-lg font-black text-emerald-600">{{ $member->scores->total_score }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- معلومات الدفع --}}
        @if($member->paymentInfo && ($member->paymentInfo->iban || $member->paymentInfo->barcode || $member->paymentInfo->iban_image || $member->paymentInfo->barcode_image))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
                <h2 class="text-sm font-semibold text-gray-700">معلومات الدفع</h2>
            </div>
            <div class="p-5 space-y-4">

                {{-- IBAN --}}
                @if($member->paymentInfo->iban)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">رقم الآيبان</p>
                        <p class="text-sm font-mono font-medium text-gray-800 bg-gray-50 rounded-lg px-3 py-1.5 break-all">{{ $member->paymentInfo->iban }}</p>
                    </div>
                @endif

                {{-- IBAN image --}}
                @if($member->paymentInfo->iban_image)
                    <div>
                        <p class="text-xs text-gray-400 mb-1.5">صورة الآيبان</p>
                        <a href="{{ Storage::url($member->paymentInfo->iban_image) }}" target="_blank" class="block group">
                            <img src="{{ Storage::url($member->paymentInfo->iban_image) }}"
                                 alt="صورة الآيبان"
                                 class="w-full rounded-xl border border-gray-200 object-contain max-h-40 bg-gray-50 group-hover:opacity-90 transition-opacity cursor-zoom-in">
                        </a>
                    </div>
                @endif

                {{-- Barcode --}}
                @if($member->paymentInfo->barcode)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">الباركود</p>
                        <p class="text-sm font-mono font-medium text-gray-800 bg-gray-50 rounded-lg px-3 py-1.5 break-all">{{ $member->paymentInfo->barcode }}</p>
                    </div>
                @endif

                {{-- Barcode image --}}
                @if($member->paymentInfo->barcode_image)
                    <div>
                        <p class="text-xs text-gray-400 mb-1.5">صورة الباركود</p>
                        <a href="{{ Storage::url($member->paymentInfo->barcode_image) }}" target="_blank" class="block group">
                            <img src="{{ Storage::url($member->paymentInfo->barcode_image) }}"
                                 alt="صورة الباركود"
                                 class="w-full rounded-xl border border-gray-200 object-contain max-h-40 bg-gray-50 group-hover:opacity-90 transition-opacity cursor-zoom-in">
                        </a>
                    </div>
                @endif

            </div>
        </div>
        @endif

    </div>

</div>

@endsection
