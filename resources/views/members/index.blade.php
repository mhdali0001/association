@extends('layouts.app')

@section('title', 'الأعضاء — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">الأعضاء</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-emerald-600 via-emerald-500 to-teal-500 rounded-3xl p-5 sm:p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between sm:flex-wrap">

        {{-- Title + stats --}}
        <div class="flex items-start justify-between gap-3 sm:block">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white">الأعضاء</h1>
                <p class="text-emerald-100 text-xs sm:text-sm mt-0.5">إجمالي المسجلين: <span class="font-bold text-white">{{ number_format($members->total()) }}</span> عضو</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:flex gap-2 sm:gap-3">
            <div class="bg-white/15 border border-white/25 rounded-xl px-3 sm:px-4 py-2.5 text-center">
                <p class="text-white font-black text-base sm:text-xl leading-none">{{ number_format((float)$totalAmount, 0, '.', ',') }}</p>
                <p class="text-emerald-200 text-xs mt-0.5">المبالغ المقدرة (ل.س)</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-3 sm:px-4 py-2.5 text-center">
                <p class="text-white font-black text-base sm:text-xl leading-none">{{ number_format((float)$totalFinalAmount, 0, '.', ',') }}</p>
                <p class="text-purple-200 text-xs mt-0.5">المبالغ النهائية (ل.س)</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:flex-wrap">
            {{-- Primary --}}
            <a href="{{ route('members.create') }}"
               class="flex items-center justify-center gap-2 bg-white text-emerald-700 hover:bg-emerald-50 text-sm font-bold px-5 py-2.5 sm:py-2 rounded-xl transition-colors shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة عضو جديد
            </a>
            {{-- Secondary --}}
            <div class="grid grid-cols-2 gap-2 sm:flex sm:gap-2">
                <a href="{{ route('members.import.show') }}"
                   class="flex items-center justify-center gap-1.5 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-xs sm:text-sm font-semibold px-3 py-2 rounded-xl transition-colors backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    استيراد
                </a>
                <a href="{{ route('members.export', request()->query()) }}"
                   class="flex items-center justify-center gap-1.5 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-xs sm:text-sm font-semibold px-3 py-2 rounded-xl transition-colors backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    تصدير
                </a>
                <a href="{{ route('members.duplicates') }}"
                   class="flex items-center justify-center gap-1.5 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-xs sm:text-sm font-semibold px-3 py-2 rounded-xl transition-colors backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    التكرارات
                </a>
                <a href="{{ route('members.bulk-amount') }}"
                   class="flex items-center justify-center gap-1.5 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-xs sm:text-sm font-semibold px-3 py-2 rounded-xl transition-colors backdrop-blur-sm">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    المبالغ
                </a>
            </div>
        </div>

    </div>
</div>

@if (session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- Filters --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <form method="GET" action="{{ route('members.index') }}" id="filter-form"
          onsubmit="removeEmptyFilters(this)">

        {{-- Always visible: search + filter toggle button --}}
        <div class="px-5 pt-4 pb-3 border-b border-gray-100 space-y-2.5">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           placeholder="بحث بالاسم، رقم الهوية، الهاتف..."
                           class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50
                                  focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300">
                </div>
                <div class="relative w-full sm:w-44">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <input type="text" name="dossier_search" value="{{ $dossierSearch ?? '' }}"
                           placeholder="رقم الاضبارة..."
                           class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50
                                  focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300
                                  {{ ($dossierSearch ?? '') ? 'border-emerald-400 bg-emerald-50/30' : '' }}">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    بحث
                </button>
                <button type="button" onclick="toggleFilters()"
                        class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-emerald-300 transition-colors text-sm font-bold text-gray-600">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    الفلاتر
                    <svg id="filter-toggle-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Collapsible filters --}}
        <div id="filter-body">
        <div class="p-5">

        {{-- Dossier range --}}
        <div class="flex items-end gap-3 mb-3">
            <div class="flex-1 max-w-xs">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">رقم الاضبارة من</label>
                <input type="text" name="dossier_from" value="{{ $dossierFrom }}"
                       placeholder="مثال: 100"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300 font-mono">
            </div>
            <span class="text-gray-400 pb-2.5">—</span>
            <div class="flex-1 max-w-xs">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                <input type="text" name="dossier_to" value="{{ $dossierTo }}"
                       placeholder="مثال: 200"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300 font-mono">
            </div>
        </div>

        {{-- Amount ranges --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">

            {{-- Estimated amount range --}}
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">المبلغ المقدر من</label>
                    <input type="number" name="estimated_from" value="{{ $estimatedFrom }}" min="0" step="any"
                           placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="estimated_to" value="{{ $estimatedTo }}" min="0" step="any"
                           placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">ل.س مقدر</span>
            </div>

            {{-- Payments count range --}}
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">عدد الدفعات من</label>
                    <input type="number" name="payments_count_from" value="{{ $paymentsCountFrom }}" min="0" step="1"
                           placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="payments_count_to" value="{{ $paymentsCountTo }}" min="0" step="1"
                           placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">دفعة</span>
            </div>

        </div>

        {{-- Filter row 1 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-3">

            {{-- Verification status multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">حالة التحقق</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="verification_status_id[]" value="none"
                               {{ in_array('none', $verificationIds) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون حالة
                    </label>
                    @foreach($verificationStatuses as $vs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="verification_status_id[]" value="{{ $vs->id }}"
                                   {{ in_array($vs->id, $verificationIds) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $vs->color }}"></span>
                            {{ $vs->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Final status multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة النهائية</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="final_status_id[]" value="none"
                               {{ in_array('none', $finalStatusIds) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @foreach($finalStatusList as $fs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}"
                                   {{ in_array($fs->id, $finalStatusIds) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $fs->color }}"></span>
                            {{ $fs->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Marital status multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة الاجتماعية</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="marital_status[]" value="none"
                               {{ in_array('none', $maritalStatuses) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @foreach($maritalStatusList as $ms)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="marital_status[]" value="{{ $ms->name }}"
                                   {{ in_array($ms->name, $maritalStatuses) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            {{ $ms->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Gender multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجنس</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    @foreach(['ذكر', 'أنثى'] as $g)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="gender[]" value="{{ $g }}"
                                   {{ in_array($g, $genders) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            {{ $g }}
                        </label>
                    @endforeach
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-t border-gray-100">
                        <input type="checkbox" name="gender[]" value="none"
                               {{ in_array('none', $genders) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                        غير محدد
                    </label>
                </div>
            </div>

            {{-- Association multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجمعية</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="association_id[]" value="none"
                               {{ in_array('none', $associationIds) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @forelse($associationList as $assoc)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="association_id[]" value="{{ $assoc->id }}"
                                   {{ in_array($assoc->id, $associationIds) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            {{ $assoc->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد جمعيات</p>
                    @endforelse
                </div>
            </div>

            {{-- Special cases boolean --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالات الخاصة</label>
                <select name="special_cases" onwheel="this.blur()"
                        class="w-full text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1" {{ $specialCases === '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ $specialCases === '0' ? 'selected' : '' }}>لا</option>
                </select>
            </div>

            {{-- Sham Cash multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">شام كاش</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    @foreach(['done' => 'تم', 'manual' => 'يدوي', 'none' => 'لا يوجد'] as $val => $lbl)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="sham_cash[]" value="{{ $val }}"
                                   {{ in_array($val, (array) request('sham_cash', [])) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            {{ $lbl }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Sector filter --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">القطاع</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400" placeholder="بحث في القطاعات...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="sector_id[]" value="none"
                               {{ in_array('none', $sectorIds) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @forelse($sectorList as $sec)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="sector_id[]" value="{{ $sec->id }}"
                                   {{ in_array($sec->id, $sectorIds) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ $sec->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-sm text-gray-400">لا توجد قطاعات</p>
                    @endforelse
                    </div>
                </div>
            </div>

            {{-- Region filter --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المنطقة</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="بحث في المناطق...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="region_id[]" value="none"
                               {{ in_array('none', $regionIds) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @forelse($regionList as $reg)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="region_id[]" value="{{ $reg->id }}"
                                   {{ in_array($reg->id, $regionIds) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            <svg class="w-3.5 h-3.5 text-teal-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $reg->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-sm text-gray-400">لا توجد مناطق</p>
                    @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Filter row 2 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">

            {{-- Delegate multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المندوب</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="delegate[]" value="none"
                               {{ in_array('none', $delegates) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @forelse($delegateList as $d)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="delegate[]" value="{{ $d }}"
                                   {{ in_array($d, $delegates) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            {{ $d }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا يوجد مندوبون</p>
                    @endforelse
                </div>
            </div>

            {{-- Second person multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الفرد الثاني</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="second_person[]" value="none"
                               {{ in_array('none', $secondPersons) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @forelse($secondPersonList as $sp)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="second_person[]" value="{{ $sp }}"
                                   {{ in_array($sp, $secondPersons) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            {{ $sp }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا يوجد أفراد ثانيون</p>
                    @endforelse
                </div>
            </div>

            {{-- Special cases description multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">وصف الحالة الخاصة</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="بحث في الأوصاف...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="special_cases_description[]" value="none"
                               {{ in_array('none', $specialDescriptions) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @forelse($specialDescriptionList as $sd)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="special_cases_description[]" value="{{ $sd }}"
                                   {{ in_array($sd, $specialDescriptions) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            <span class="truncate">{{ $sd }}</span>
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات خاصة مسجلة</p>
                    @endforelse
                    </div>
                </div>
            </div>

            {{-- Network multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">نوع الشبكة</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="network[]" value="none"
                               {{ in_array('none', $networks) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @foreach(['MTN', 'SYRIATEL'] as $net)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="network[]" value="{{ $net }}"
                                   {{ in_array($net, $networks) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            {{ $net }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Payment data entry name multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">اسم مدخل الدفع</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="payment_data_entry[]" value="none"
                               {{ in_array('none', $paymentDataEntries) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @forelse($paymentDataEntryList as $pde)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="payment_data_entry[]" value="{{ $pde }}"
                                   {{ in_array($pde, $paymentDataEntries) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            {{ $pde }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>

            {{-- Address / region multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">العنوان التفصيلي</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="بحث في العناوين...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="current_address[]" value="none"
                               {{ in_array('none', $addresses) ? 'checked' : '' }}
                               class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                        بدون
                    </label>
                    @forelse($addressList as $addr)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="current_address[]" value="{{ $addr }}"
                                   {{ in_array($addr, $addresses) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            <span class="truncate">{{ $addr }}</span>
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد مناطق مسجلة</p>
                    @endforelse
                    </div>
                </div>
            </div>

            {{-- Housing Status multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">وضع السكن</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($housingStatusList as $hs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="housing_status_id[]" value="{{ $hs->id }}"
                                   {{ in_array($hs->id, $housingStatusIds) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hs->color }}"></span>
                            {{ $hs->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد أوضاع سكن</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Field Visit Filters (dedicated section) --}}
        @php
            $hasFvFilters = !empty($fieldVisitStatusIds) || !empty($fvHouseTypeIds) || !empty($fvHouseConditionIds) || !empty($fvVisitors)
                || !empty($fvCreatedByIds)
                || $fvDateFrom !== '' || $fvDateTo !== ''
                || $fvAmountFrom !== '' || $fvAmountTo !== ''
                || $fvNotes !== '' || $fvHasVideo !== '' || $fvHasSpecialCase !== '';
            $fvActiveCount = (int)!empty($fieldVisitStatusIds) + (int)!empty($fvHouseTypeIds) + (int)!empty($fvHouseConditionIds)
                + (!empty($fvVisitors) ? 1 : 0) + (!empty($fvCreatedByIds) ? 1 : 0) + ($fvDateFrom !== '' || $fvDateTo !== '' ? 1 : 0)
                + ($fvAmountFrom !== '' || $fvAmountTo !== '' ? 1 : 0)
                + ($fvNotes !== '' ? 1 : 0)
                + ($fvHasVideo !== '' ? 1 : 0) + ($fvHasSpecialCase !== '' ? 1 : 0);
        @endphp
        <div class="border border-indigo-100 rounded-2xl mb-4" id="fv-filter-section">
            <button type="button" onclick="toggleFvFilters()"
                    class="w-full flex items-center justify-between gap-3 px-5 py-3 bg-indigo-50/60 hover:bg-indigo-50 transition-colors text-right rounded-t-2xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-bold text-indigo-700">فلاتر الجولة الميدانية</span>
                    @if($fvActiveCount > 0)
                        <span class="text-xs bg-indigo-600 text-white rounded-full px-2 py-0.5 font-bold">{{ $fvActiveCount }} فعّال</span>
                    @endif
                </div>
                <svg id="fv-filter-arrow" class="w-4 h-4 text-indigo-400 transition-transform duration-200 {{ $hasFvFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="fv-filter-body" class="{{ $hasFvFilters ? '' : 'hidden' }} px-5 pb-5 pt-4 bg-indigo-50/20 rounded-b-2xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">

                    {{-- حالة الجولة الميدانية --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة الجولة</label>
                        <button type="button"
                                class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white
                                       hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                <input type="checkbox" name="field_visit_status_id[]" value="none"
                                       {{ in_array('none', $fieldVisitStatusIds) ? 'checked' : '' }}
                                       class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-gray-300"></span>
                                بدون جولة ميدانية
                            </label>
                            @forelse($fieldVisitStatuses as $fvs)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="field_visit_status_id[]" value="{{ $fvs->id }}"
                                           {{ in_array($fvs->id, $fieldVisitStatusIds) ? 'checked' : '' }}
                                           class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $fvs->color }}"></span>
                                    {{ $fvs->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا توجد حالات</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- نوع البيت --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">نوع البيت</label>
                        <button type="button"
                                class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white
                                       hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($houseTypes as $ht)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_house_type_id[]" value="{{ $ht->id }}"
                                           {{ in_array($ht->id, $fvHouseTypeIds) ? 'checked' : '' }}
                                           class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $ht->color }}"></span>
                                    {{ $ht->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا توجد أنواع</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- الزائر --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">الزائر</label>
                        <button type="button"
                                class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white
                                       hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($fvVisitorList as $vis)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700 ms-option">
                                    <input type="checkbox" name="fv_visitors[]" value="{{ $vis }}"
                                           {{ in_array($vis, $fvVisitors) ? 'checked' : '' }}
                                           class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    {{ $vis }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا يوجد زوار مسجّلون</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- من أضاف الجولة (مستخدم النظام) --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">من أضاف الجولة</label>
                        <button type="button"
                                class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white
                                       hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($fvCreatedByList as $u)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_created_by[]" value="{{ $u->id }}"
                                           {{ in_array($u->id, $fvCreatedByIds) ? 'checked' : '' }}
                                           class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    {{ $u->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا يوجد بيانات بعد</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- تاريخ الزيارة --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">تاريخ الزيارة</label>
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="fv_date_from" value="{{ $fvDateFrom }}"
                                   class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                            <span class="text-xs text-indigo-400 shrink-0">—</span>
                            <input type="date" name="fv_date_to" value="{{ $fvDateTo }}"
                                   class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                        </div>
                    </div>

                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    {{-- مبلغ الجولة --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">مبلغ الجولة (ل.س)</label>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="fv_amount_from" value="{{ $fvAmountFrom }}"
                                   placeholder="من" min="0"
                                   class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                            <span class="text-xs text-indigo-400 shrink-0">—</span>
                            <input type="number" name="fv_amount_to" value="{{ $fvAmountTo }}"
                                   placeholder="إلى" min="0"
                                   class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                        </div>
                    </div>

                    {{-- حالة البيت --}}
                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة البيت</label>
                        <button type="button"
                                class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white
                                       hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($houseConditions as $hc)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_house_condition_id[]" value="{{ $hc->id }}"
                                           {{ in_array($hc->id, $fvHouseConditionIds) ? 'checked' : '' }}
                                           class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hc->color }}"></span>
                                    {{ $hc->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- الملاحظات --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">الملاحظات</label>
                        <input type="text" name="fv_notes" value="{{ $fvNotes }}"
                               placeholder="بحث في الملاحظات..."
                               class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                    </div>

                    {{-- يوجد فيديو --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">يوجد فيديو</label>
                        <select name="fv_has_video" onwheel="this.blur()"
                                class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $fvHasVideo === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $fvHasVideo === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    {{-- حالة خاصة --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة خاصة</label>
                        <select name="fv_has_special_case" onwheel="this.blur()"
                                class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $fvHasSpecialCase === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $fvHasSpecialCase === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    {{-- عدد الجولات --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">عدد الجولات</label>
                        <select name="fv_count" onwheel="this.blur()"
                                class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="0" {{ $fvCount === '0' ? 'selected' : '' }}>بدون جولات</option>
                            <option value="1" {{ $fvCount === '1' ? 'selected' : '' }}>جولة واحدة فأكثر</option>
                            <option value="2" {{ $fvCount === '2' ? 'selected' : '' }}>جولتان فأكثر</option>
                            <option value="3" {{ $fvCount === '3' ? 'selected' : '' }}>3 جولات فأكثر</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- Actions row --}}
        <div class="flex items-center gap-2 flex-wrap">
            <button type="submit"
                    class="flex items-center gap-2 bg-gradient-to-l from-emerald-600 to-teal-500 hover:from-emerald-700 hover:to-teal-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                تطبيق الفلاتر
            </button>

            @php
                $hasFilters = $search || $dossierFrom !== '' || $dossierTo !== '' || !empty($verificationIds) || !empty($finalStatusIds) || !empty($maritalStatuses) || !empty($genders) || !empty($delegates) || !empty($secondPersons) || $specialCases !== '' || !empty($specialDescriptions) || !empty($addresses) || !empty($associationIds) || !empty($networks) || !empty($housingStatusIds) || !empty($paymentDataEntries) || $hasFvFilters;
            @endphp

            @if($hasFilters)
                <span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-bold px-4 py-2.5 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ number_format($members->total()) }} نتيجة
                </span>

                <a href="{{ route('members.index') }}"
                   class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
            @endif

            {{-- Active filter badges --}}
            @if($hasFilters)
            @php
                function badgeRemoveUrl(string $param, $value = null): string {
                    $q = request()->query();
                    if ($value === null) {
                        unset($q[$param]);
                    } elseif (is_array($q[$param] ?? null)) {
                        $q[$param] = array_values(array_filter($q[$param], fn($v) => $v != $value));
                        if (empty($q[$param])) unset($q[$param]);
                    }
                    unset($q['page']);
                    $qs = http_build_query($q);
                    return request()->url() . ($qs ? '?' . $qs : '');
                }
            @endphp
            <div class="flex items-center gap-1.5 flex-wrap mr-auto">
                @if($search)
                    <a href="{{ badgeRemoveUrl('search') }}" class="inline-flex items-center gap-1 text-sm bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1 font-medium hover:bg-emerald-100 transition-colors">
                        بحث: {{ Str::limit($search, 20) }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @if($dossierFrom !== '' || $dossierTo !== '')
                    <a href="{{ badgeRemoveUrl('dossier_from') . '&' . http_build_query(['dossier_to' => '']) }}" class="inline-flex items-center gap-1 text-sm bg-gray-50 text-gray-700 border border-gray-200 rounded-full px-3 py-1 font-medium font-mono hover:bg-gray-100 transition-colors">
                        اضبارة: {{ $dossierFrom ?: '…' }} — {{ $dossierTo ?: '…' }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @foreach($verificationIds as $vid)
                    <a href="{{ badgeRemoveUrl('verification_status_id', $vid) }}" class="inline-flex items-center gap-1 text-sm bg-blue-50 text-blue-700 border border-blue-200 rounded-full px-3 py-1 font-medium hover:bg-blue-100 transition-colors">
                        {{ $verificationStatuses->firstWhere('id', $vid)?->name }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($finalStatusIds as $fid)
                    <a href="{{ badgeRemoveUrl('final_status_id', $fid) }}" class="inline-flex items-center gap-1 text-sm bg-slate-50 text-slate-700 border border-slate-200 rounded-full px-3 py-1 font-medium hover:bg-slate-100 transition-colors">
                        {{ $finalStatusList->firstWhere('id', $fid)?->name }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($maritalStatuses as $ms)
                    <a href="{{ badgeRemoveUrl('marital_status', $ms) }}" class="inline-flex items-center gap-1 text-sm bg-purple-50 text-purple-700 border border-purple-200 rounded-full px-3 py-1 font-medium hover:bg-purple-100 transition-colors">
                        {{ $ms }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($genders as $g)
                    <a href="{{ badgeRemoveUrl('gender', $g) }}" class="inline-flex items-center gap-1 text-sm bg-orange-50 text-orange-700 border border-orange-200 rounded-full px-3 py-1 font-medium hover:bg-orange-100 transition-colors">
                        {{ $g }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($delegates as $d)
                    <a href="{{ badgeRemoveUrl('delegate', $d) }}" class="inline-flex items-center gap-1 text-sm bg-teal-50 text-teal-700 border border-teal-200 rounded-full px-3 py-1 font-medium hover:bg-teal-100 transition-colors">
                        {{ $d }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($secondPersons as $sp)
                    <a href="{{ badgeRemoveUrl('second_person', $sp) }}" class="inline-flex items-center gap-1 text-sm bg-purple-50 text-purple-700 border border-purple-200 rounded-full px-3 py-1 font-medium hover:bg-purple-100 transition-colors">
                        {{ $sp }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @if($specialCases !== '')
                    <a href="{{ badgeRemoveUrl('special_cases') }}" class="inline-flex items-center gap-1 text-sm bg-rose-50 text-rose-700 border border-rose-200 rounded-full px-3 py-1 font-medium hover:bg-rose-100 transition-colors">
                        حالات خاصة: {{ $specialCases === '1' ? 'نعم' : 'لا' }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @foreach($specialDescriptions as $sd)
                    <a href="{{ badgeRemoveUrl('special_cases_description', $sd) }}" class="inline-flex items-center gap-1 text-sm bg-amber-50 text-amber-700 border border-amber-200 rounded-full px-3 py-1 font-medium max-w-[200px] truncate hover:bg-amber-100 transition-colors">
                        {{ $sd }}
                        <svg class="w-3 h-3 opacity-60 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($addresses as $addr)
                    <a href="{{ badgeRemoveUrl('current_address', $addr) }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        {{ $addr }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($associationIds as $aid)
                    <a href="{{ badgeRemoveUrl('association_id', $aid) }}" class="inline-flex items-center gap-1 text-sm bg-cyan-50 text-cyan-700 border border-cyan-200 rounded-full px-3 py-1 font-medium hover:bg-cyan-100 transition-colors">
                        {{ $associationList->firstWhere('id', $aid)?->name }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($networks as $net)
                    <a href="{{ badgeRemoveUrl('network', $net) }}" class="inline-flex items-center gap-1 text-sm bg-violet-50 text-violet-700 border border-violet-200 rounded-full px-3 py-1 font-medium hover:bg-violet-100 transition-colors">
                        {{ $net }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($housingStatusIds as $hsId)
                    <a href="{{ badgeRemoveUrl('housing_status_id', $hsId) }}" class="inline-flex items-center gap-1 text-sm bg-lime-50 text-lime-700 border border-lime-200 rounded-full px-3 py-1 font-medium hover:bg-lime-100 transition-colors">
                        وضع السكن: {{ $housingStatusList->firstWhere('id', $hsId)?->name }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($paymentDataEntries as $pde)
                    <a href="{{ badgeRemoveUrl('payment_data_entry', $pde) }}" class="inline-flex items-center gap-1 text-sm bg-sky-50 text-sky-700 border border-sky-200 rounded-full px-3 py-1 font-medium hover:bg-sky-100 transition-colors">
                        مدخل الدفع: {{ $pde === 'none' ? 'بدون' : $pde }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($fieldVisitStatusIds as $fvsId)
                    <a href="{{ badgeRemoveUrl('field_visit_status_id', $fvsId) }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $fvsId === 'none' ? 'بدون جولة ميدانية' : $fieldVisitStatuses->firstWhere('id', $fvsId)?->name }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($fvHouseTypeIds as $htId)
                    <a href="{{ badgeRemoveUrl('fv_house_type_id', $htId) }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21H15v-6H9v6H3V9.75z"/></svg>
                        {{ $houseTypes->firstWhere('id', $htId)?->name }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @foreach($fvVisitors as $vis)
                    <a href="{{ badgeRemoveUrl('fv_visitors', $vis) }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ Str::limit($vis, 20) }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @if($fvDateFrom !== '' || $fvDateTo !== '')
                    <a href="{{ badgeRemoveUrl('fv_date_from') }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        تاريخ الجولة: {{ $fvDateFrom ?: '…' }} — {{ $fvDateTo ?: '…' }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @if($fvAmountFrom !== '' || $fvAmountTo !== '')
                    <a href="{{ badgeRemoveUrl('fv_amount_from') }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        مبلغ الجولة: {{ $fvAmountFrom ?: '…' }} — {{ $fvAmountTo ?: '…' }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @foreach($fvHouseConditionIds as $hcId)
                    <a href="{{ badgeRemoveUrl('fv_house_condition_id', $hcId) }}" class="inline-flex items-center gap-1 text-sm bg-amber-50 text-amber-700 border border-amber-200 rounded-full px-3 py-1 font-medium hover:bg-amber-100 transition-colors">
                        <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21H15v-6H9v6H3V9.75z"/></svg>
                        {{ $houseConditions->firstWhere('id', $hcId)?->name }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endforeach
                @if($fvNotes !== '')
                    <a href="{{ badgeRemoveUrl('fv_notes') }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        ملاحظات الجولة: {{ Str::limit($fvNotes, 20) }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @if($fvHasVideo !== '')
                    <a href="{{ badgeRemoveUrl('fv_has_video') }}" class="inline-flex items-center gap-1 text-sm bg-rose-50 text-rose-700 border border-rose-200 rounded-full px-3 py-1 font-medium hover:bg-rose-100 transition-colors">
                        فيديو: {{ $fvHasVideo === '1' ? 'نعم' : 'لا' }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @if($fvHasSpecialCase !== '')
                    <a href="{{ badgeRemoveUrl('fv_has_special_case') }}" class="inline-flex items-center gap-1 text-sm bg-orange-50 text-orange-700 border border-orange-200 rounded-full px-3 py-1 font-medium hover:bg-orange-100 transition-colors">
                        حالة خاصة: {{ $fvHasSpecialCase === '1' ? 'نعم' : 'لا' }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @if($fvCount !== '')
                    <a href="{{ badgeRemoveUrl('fv_count') }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        عدد الجولات: {{ $fvCount === '0' ? 'بدون' : $fvCount.'+' }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
            @endif
        </div>
        </div>{{-- /p-5 --}}
        </div>{{-- /filter-body --}}
    </form>
</div>

{{-- Bulk Edit --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <button type="button" onclick="toggleBulkEdit()"
            class="w-full flex items-center justify-between gap-2 px-5 py-3.5 border-b border-gray-100 hover:bg-gray-50 transition-colors text-right">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">التعديل الجماعي</span>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2.5 py-0.5">حدد الأعضاء أولاً من الجدول</span>
        </div>
        <svg id="bulk-edit-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200 rotate-[-90deg]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div id="bulk-edit-body" class="hidden p-5">
        <form id="bulk-edit-form" method="POST" action="{{ route('members.bulk-update') }}" onsubmit="return injectBulkEditIds()">
            @csrf
            @method('PATCH')
            <div id="bulk-edit-ids-container"></div>
            <input type="hidden" name="select_all" id="bulk-edit-select-all" value="0">
            @foreach(request()->except(['page','_token']) as $key => $val)
                @if(is_array($val))
                    @foreach($val as $v)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endif
            @endforeach

            <p class="text-xs text-gray-400 mb-4">فعّل الحقول التي تريد تعديلها فقط — الحقول غير المفعّلة لن تُطبَّق.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">

                {{-- Current Address --}}
                <div class="be-field" data-field="current_address">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="current_address" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">العنوان التفصيلي</label>
                    </div>
                    <select name="fields[current_address]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— بدون —</option>
                        @foreach($addressList as $addr)
                            <option value="{{ $addr }}">{{ $addr }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Region --}}
                <div class="be-field" data-field="region_id">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="region_id" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">المنطقة</label>
                    </div>
                    <select name="fields[region_id]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— بدون —</option>
                        @foreach($regionList as $reg)
                            <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sector --}}
                <div class="be-field" data-field="sector_id">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="sector_id" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">القطاع</label>
                    </div>
                    <select name="fields[sector_id]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                        <option value="">— بدون —</option>
                        @foreach($sectorList as $sec)
                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Network --}}
                <div class="be-field" data-field="network">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="network" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">نوع الشبكة</label>
                    </div>
                    <select name="fields[network]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— بدون —</option>
                        <option value="MTN">MTN</option>
                        <option value="SYRIATEL">SYRIATEL</option>
                    </select>
                </div>

                {{-- Marital Status --}}
                <div class="be-field" data-field="marital_status">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="marital_status" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">الحالة الاجتماعية</label>
                    </div>
                    <select name="fields[marital_status]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— بدون —</option>
                        @foreach($maritalStatusList as $ms)
                            <option value="{{ $ms->name }}">{{ $ms->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sham Cash (admin only) --}}
                @if(auth()->user()?->role === 'admin')
                <div class="be-field" data-field="sham_cash_account">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="sham_cash_account" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">شام كاش</label>
                    </div>
                    <select name="fields[sham_cash_account]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="done">تم</option>
                        <option value="manual">يدوي</option>
                        <option value="">لا</option>
                    </select>
                </div>
                @endif

                {{-- Housing Status --}}
                <div class="be-field" data-field="housing_status_id">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="housing_status_id" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">وضع السكن</label>
                    </div>
                    <select name="fields[housing_status_id]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— بدون —</option>
                        @foreach($housingStatusList as $hs)
                            <option value="{{ $hs->id }}">{{ $hs->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Verification Status --}}
                <div class="be-field" data-field="verification_status_id">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="verification_status_id" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">حالة التحقق</label>
                    </div>
                    <select name="fields[verification_status_id]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— بدون —</option>
                        @foreach($verificationStatuses as $vs)
                            <option value="{{ $vs->id }}">{{ $vs->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Final Status (admin only) --}}
                @if(auth()->user()?->role === 'admin')
                <div class="be-field" data-field="final_status_id">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="final_status_id" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">الحالة النهائية</label>
                    </div>
                    <select name="fields[final_status_id]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— بدون —</option>
                        @foreach($finalStatusList as $fs)
                            <option value="{{ $fs->id }}">{{ $fs->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Field Visit Status --}}
                <div class="be-field" data-field="field_visit_status_id">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="field_visit_status_id" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">حالة الجولة الميدانية</label>
                    </div>
                    <select name="fields[field_visit_status_id]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— بدون —</option>
                        @foreach($fieldVisitStatuses as $fvs)
                            <option value="{{ $fvs->id }}">{{ $fvs->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Field Visit Visitor --}}
                <div class="be-field" data-field="fv_visitor">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="fv_visitor" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">الزائر</label>
                    </div>
                    <div class="relative visitor-combo" id="visitor-combo-be">
                        <div class="flex gap-1">
                            <input type="text" name="fields[fv_visitor]" id="visitor-input-be" disabled
                                   placeholder="اكتب أو اختر اسم الزائر..."
                                   autocomplete="off"
                                   class="be-input flex-1 text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                            <button type="button" id="visitor-dropdown-btn-be" disabled
                                    onclick="toggleVisitorDropdown()"
                                    class="be-input shrink-0 px-2.5 py-2 border border-gray-200 rounded-xl bg-gray-100 text-gray-300 hover:bg-gray-50 transition focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                        <div id="visitor-panel-be"
                             class="hidden absolute z-50 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden"
                             style="max-height:220px">
                            <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                                <input type="text" id="visitor-search-be" placeholder="بحث في الأسماء..."
                                       oninput="filterVisitorList(this.value)"
                                       class="w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div id="visitor-list-be" class="overflow-y-auto" style="max-height:160px">
                                @forelse($fvVisitorList as $v)
                                    <div class="visitor-opt px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 cursor-pointer"
                                         onclick="selectVisitor('{{ addslashes($v) }}')">
                                        {{ $v }}
                                    </div>
                                @empty
                                    <p class="px-3 py-2 text-xs text-gray-400">لا توجد أسماء مسجلة</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Data Entry Name --}}
                <div class="be-field" data-field="payment_data_entry_name">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="payment_data_entry_name" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">اسم مدخل الدفع</label>
                    </div>
                    <input type="text" name="fields[payment_data_entry_name]" disabled placeholder="اسم مدخل الدفع..."
                           list="payment-data-entry-list-be"
                           class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    <datalist id="payment-data-entry-list-be">
                        @foreach($paymentDataEntryList as $pde)
                            <option value="{{ $pde }}">
                        @endforeach
                    </datalist>
                </div>

                {{-- Delegate --}}
                <div class="be-field" data-field="delegate">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="delegate" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">المندوب</label>
                    </div>
                    <select name="fields[delegate]" disabled
                            class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                        <option value="">— بدون —</option>
                        @forelse($delegateList as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @empty
                        @endforelse
                    </select>
                </div>

                {{-- Estimated Amount --}}
                <div class="be-field" data-field="estimated_amount">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="estimated_amount" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">المبلغ المقدر (ل.س)</label>
                    </div>
                    <input type="number" name="fields[estimated_amount]" min="0" disabled placeholder="أدخل المبلغ..."
                           class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>

                {{-- Payments Count --}}
                <div class="be-field" data-field="payments_count">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="payments_count" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">عدد الدفعات</label>
                    </div>
                    <input type="number" name="fields[payments_count]" min="0" step="1" disabled placeholder="أدخل عدد الدفعات..."
                           class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>


            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="flex items-center gap-2 bg-gradient-to-l from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    تطبيق التعديلات على المحددين
                </button>
                <span id="bulk-edit-selected-count" class="text-sm text-gray-400"></span>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleFvFilters() {
    const body  = document.getElementById('fv-filter-body');
    const arrow = document.getElementById('fv-filter-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ms-dropdown').forEach(function (dropdown) {
        const btn   = dropdown.querySelector('.ms-btn');
        const panel = dropdown.querySelector('.ms-panel');
        const label = dropdown.querySelector('.ms-label');
        const arrow = dropdown.querySelector('.ms-arrow');

        function updateLabel() {
            const checked = dropdown.querySelectorAll('.ms-check:checked');
            if (checked.length === 0) {
                label.textContent = '— الكل —';
                label.classList.remove('text-emerald-700', 'font-semibold');
                label.classList.add('text-gray-500');
            } else {
                label.textContent = checked.length + ' محدد';
                label.classList.add('text-emerald-700', 'font-semibold');
                label.classList.remove('text-gray-500');
            }
        }

        updateLabel();

        // Mark all label items as ms-option for search filtering
        panel.querySelectorAll('label').forEach(function (lbl) {
            lbl.classList.add('ms-option');
        });

        // Inject sticky search box for panels with 4+ options
        var allOptions = panel.querySelectorAll('.ms-option');
        var searchInput = null;
        if (allOptions.length >= 4) {
            var stickyHeader = document.createElement('div');
            stickyHeader.className = 'sticky top-0 bg-white z-10 px-2 pt-2 pb-1 border-b border-gray-100';

            searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'ابحث...';
            searchInput.className = 'ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-gray-50';
            searchInput.setAttribute('dir', 'rtl');

            stickyHeader.appendChild(searchInput);
            panel.insertBefore(stickyHeader, panel.firstChild);

            searchInput.addEventListener('input', function () {
                var q = searchInput.value.trim().toLowerCase();
                panel.querySelectorAll('.ms-option').forEach(function (opt) {
                    var text = opt.textContent.trim().toLowerCase();
                    opt.style.display = (!q || text.includes(q)) ? '' : 'none';
                });
            });

            searchInput.addEventListener('click', function (e) { e.stopPropagation(); });
        }

        // Inject "تحديد الكل / إلغاء التحديد" button
        const checks = dropdown.querySelectorAll('.ms-check');
        if (checks.length >= 2) {
            const saBtn = document.createElement('button');
            saBtn.type = 'button';
            saBtn.className = 'w-full text-right text-xs text-emerald-600 font-semibold px-3 py-1.5 hover:bg-emerald-50 transition flex items-center gap-1';
            saBtn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span class="sa-text">تحديد الكل</span>';

            function refreshSaBtn() {
                const checkedCount = dropdown.querySelectorAll('.ms-check:checked').length;
                saBtn.querySelector('.sa-text').textContent = checkedCount === checks.length ? 'إلغاء التحديد' : 'تحديد الكل';
            }

            saBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const checkedCount = dropdown.querySelectorAll('.ms-check:checked').length;
                const shouldCheck = checkedCount < checks.length;
                checks.forEach(function (cb) { cb.checked = shouldCheck; });
                updateLabel();
                refreshSaBtn();
            });

            checks.forEach(function (cb) {
                cb.addEventListener('change', refreshSaBtn);
            });

            refreshSaBtn();

            // Insert: inside sticky header if searchable, else at panel top
            const searchEl = panel.querySelector('.ms-search');
            if (searchEl) {
                const stickyDiv = searchEl.parentElement;
                saBtn.classList.add('mt-1', 'border-t', 'border-gray-100');
                stickyDiv.appendChild(saBtn);
            } else {
                saBtn.classList.add('border-b', 'border-gray-100', 'mb-1');
                panel.insertBefore(saBtn, panel.firstChild);
            }
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = !panel.classList.contains('hidden');
            document.querySelectorAll('.ms-panel').forEach(function (p) {
                resetMobilePanel(p);
                p.classList.add('hidden');
                p.closest('.ms-dropdown').querySelector('.ms-arrow').classList.remove('rotate-180');
            });
            if (!isOpen) {
                panel.classList.remove('hidden');
                arrow.classList.add('rotate-180');
                positionMobilePanel(btn, panel);
                // Reset search and show all options when opening
                if (searchInput) {
                    searchInput.value = '';
                    panel.querySelectorAll('.ms-option').forEach(function (opt) { opt.style.display = ''; });
                    setTimeout(function () { searchInput.focus(); }, 50);
                }
            }
        });

        dropdown.querySelectorAll('.ms-check').forEach(function (cb) {
            cb.addEventListener('change', updateLabel);
        });
    });

    // Mobile: position panel as fixed so it's never clipped by parent overflow
    function positionMobilePanel(btn, panel) {
        if (window.innerWidth >= 640) return;
        const rect = btn.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom - 8;
        const spaceAbove = rect.top - 8;
        panel.style.position = 'fixed';
        panel.style.left     = rect.left + 'px';
        panel.style.width    = rect.width + 'px';
        panel.style.zIndex   = '9999';
        if (spaceBelow >= 220 || spaceBelow >= spaceAbove) {
            panel.style.top        = (rect.bottom + 4) + 'px';
            panel.style.bottom     = '';
            panel.style.maxHeight  = Math.min(spaceBelow, window.innerHeight * 0.55) + 'px';
        } else {
            panel.style.bottom     = (window.innerHeight - rect.top + 4) + 'px';
            panel.style.top        = '';
            panel.style.maxHeight  = Math.min(spaceAbove, window.innerHeight * 0.55) + 'px';
        }
        // make inner scroll div fill the fixed panel
        const inner = panel.querySelector('[style*="max-height:200px"], [style*="max-height: 200px"]');
        if (inner) inner.style.maxHeight = 'calc(100% - 80px)';
        panel.style.overflowY = 'auto';
    }

    function resetMobilePanel(panel) {
        if (window.innerWidth >= 640) return;
        panel.style.position  = '';
        panel.style.left      = '';
        panel.style.width     = '';
        panel.style.zIndex    = '';
        panel.style.top       = '';
        panel.style.bottom    = '';
        panel.style.maxHeight = '';
        panel.style.overflowY = '';
        const inner = panel.querySelector('[style*="max-height"]');
        if (inner) inner.style.maxHeight = '';
    }

    window.addEventListener('scroll', function () {
        document.querySelectorAll('.ms-panel:not(.hidden)').forEach(function (p) {
            const btn = p.closest('.ms-dropdown').querySelector('.ms-btn');
            if (btn) positionMobilePanel(btn, p);
        });
    }, { passive: true });

    document.addEventListener('click', function () {
        document.querySelectorAll('.ms-panel').forEach(function (p) {
            resetMobilePanel(p);
            p.classList.add('hidden');
            p.closest('.ms-dropdown').querySelector('.ms-arrow').classList.remove('rotate-180');
        });
    });

    document.querySelectorAll('.ms-panel').forEach(function (p) {
        p.addEventListener('click', function (e) { e.stopPropagation(); });
    });

    // Live search inside searchable dropdowns
    document.querySelectorAll('.ms-search').forEach(function (input) {
        input.addEventListener('input', function () {
            const q = input.value.trim().toLowerCase();
            const panel = input.closest('.ms-panel');
            panel.querySelectorAll('.ms-option').forEach(function (opt) {
                const text = opt.textContent.trim().toLowerCase();
                opt.style.display = (!q || text.includes(q)) ? '' : 'none';
            });
        });
    });

    // Bulk select
    var selectAll       = document.getElementById('select-all');
    var selectAllMobile = document.getElementById('select-all-mobile');
    var bulkBar         = document.getElementById('bulk-action-bar');
    var bulkCount       = document.getElementById('bulk-count');
    var bulkIdsContainer = document.getElementById('bulk-ids-container');

    function updateBulkBar() {
        // Deduplicate by member ID — mobile + desktop both render the same member
        var checkedElems = document.querySelectorAll('.row-checkbox:checked');
        var allElems     = document.querySelectorAll('.row-checkbox');

        var checkedIdMap = {};
        checkedElems.forEach(function(cb) { checkedIdMap[cb.value] = true; });
        var checkedIds = Object.keys(checkedIdMap);

        var allIdMap = {};
        allElems.forEach(function(cb) { allIdMap[cb.value] = true; });
        var allCount = Object.keys(allIdMap).length;

        if (checkedIds.length > 0) {
            bulkBar.classList.remove('hidden');
            bulkBar.classList.add('flex');
            bulkCount.textContent = 'تم تحديد ' + checkedIds.length + ' عضو';

            bulkIdsContainer.innerHTML = '';
            var editIds = document.getElementById('bulk-edit-ids-container');
            if (editIds) editIds.innerHTML = '';

            checkedIds.forEach(function(id) {
                var inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
                bulkIdsContainer.appendChild(inp);

                if (editIds) {
                    var inp2 = document.createElement('input');
                    inp2.type = 'hidden'; inp2.name = 'ids[]'; inp2.value = id;
                    editIds.appendChild(inp2);
                }
            });

            // Show select-all-pages banner only when all on page are checked and there are more pages
            var banner = document.getElementById('select-all-pages-banner');
            if (checkedIds.length === allCount && totalMembersCount > pageCount && !allPagesSelected) {
                banner.classList.remove('hidden');
                banner.classList.add('flex');
                var msg = document.getElementById('select-all-pages-msg');
                msg.textContent = 'تم تحديد ' + pageCount + ' عضو في هذه الصفحة — يوجد ' + totalMembersCount.toLocaleString('ar') + ' عضو إجمالاً.';
                msg.className = 'text-amber-800 font-medium';
                document.getElementById('select-all-pages-btn').classList.remove('hidden');
                document.getElementById('cancel-all-pages-btn').classList.add('hidden');
            } else if (!allPagesSelected) {
                banner.classList.add('hidden');
                banner.classList.remove('flex');
            }
        } else {
            bulkBar.classList.add('hidden');
            bulkBar.classList.remove('flex');
            bulkIdsContainer.innerHTML = '';
            allPagesSelected = false;
            document.getElementById('bulk-delete-select-all').value = '0';
            document.getElementById('bulk-edit-select-all').value   = '0';
        }

        if (selectAll) {
            selectAll.checked = allCount > 0 && checkedIds.length === allCount;
            selectAll.indeterminate = checkedIds.length > 0 && checkedIds.length < allCount;
        }

        if (selectAllMobile) {
            selectAllMobile.checked = allCount > 0 && checkedIds.length === allCount;
            selectAllMobile.indeterminate = checkedIds.length > 0 && checkedIds.length < allCount;
        }

        var beCount = document.getElementById('bulk-edit-selected-count');
        if (beCount) {
            beCount.textContent = checkedIds.length > 0 ? ('(' + checkedIds.length + ' عضو محدد)') : '';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox-desktop').forEach(function(cb) {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });
    }

    if (selectAllMobile) {
        selectAllMobile.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox-mobile').forEach(function(cb) {
                cb.checked = selectAllMobile.checked;
            });
            updateBulkBar();
        });
    }

    document.querySelectorAll('.row-checkbox').forEach(function(cb) {
        cb.addEventListener('change', updateBulkBar);
    });

    // Duplicate toggle: show count on load
    var dupRows = document.querySelectorAll('tr[data-duplicate="1"]');
    var countBadge = document.getElementById('toggle-duplicates-count');
    if (countBadge) {
        if (dupRows.length > 0) {
            countBadge.textContent = dupRows.length;
        } else {
            var btn = document.getElementById('toggle-duplicates-btn');
            if (btn) btn.style.display = 'none';
        }
    }
});

var allPagesSelected = false;
var totalMembersCount = {{ $members->total() }};
var pageCount = {{ $members->count() }};

function selectAllPages() {
    allPagesSelected = true;
    document.getElementById('bulk-delete-select-all').value = '1';
    document.getElementById('bulk-edit-select-all').value   = '1';
    document.getElementById('bulk-ids-container').innerHTML = '';
    document.getElementById('bulk-edit-ids-container').innerHTML = '';

    var msg    = document.getElementById('select-all-pages-msg');
    var btnSel = document.getElementById('select-all-pages-btn');
    var btnCnl = document.getElementById('cancel-all-pages-btn');
    msg.textContent = 'تم تحديد جميع الـ ' + totalMembersCount.toLocaleString('ar') + ' عضو.';
    msg.className = 'text-emerald-800 font-bold';
    btnSel.classList.add('hidden');
    btnCnl.classList.remove('hidden');

    var bulkCount = document.getElementById('bulk-count');
    bulkCount.textContent = 'تم تحديد جميع الـ ' + totalMembersCount.toLocaleString('ar') + ' عضو';

    var beCount = document.getElementById('bulk-edit-selected-count');
    if (beCount) beCount.textContent = '(جميع الـ ' + totalMembersCount.toLocaleString('ar') + ' عضو)';
}

function cancelSelectAllPages() {
    allPagesSelected = false;
    document.getElementById('bulk-delete-select-all').value = '0';
    document.getElementById('bulk-edit-select-all').value   = '0';

    var msg    = document.getElementById('select-all-pages-msg');
    var btnSel = document.getElementById('select-all-pages-btn');
    var btnCnl = document.getElementById('cancel-all-pages-btn');
    msg.textContent = 'تم تحديد ' + pageCount + ' عضو في هذه الصفحة.';
    msg.className = 'text-amber-800 font-medium';
    btnSel.classList.remove('hidden');
    btnCnl.classList.add('hidden');

    updateBulkBar();
}

function toggleBulkEdit() {
    var body  = document.getElementById('bulk-edit-body');
    var arrow = document.getElementById('bulk-edit-arrow');
    var hidden = body.classList.contains('hidden');
    body.classList.toggle('hidden', !hidden);
    arrow.style.transform = hidden ? '' : 'rotate(-90deg)';
}

function toggleBulkField(cb) {
    var field = cb.closest('.be-field');
    field.querySelectorAll('.be-input').forEach(function(input) {
        input.disabled = !cb.checked;
        if (cb.checked) {
            input.classList.remove('bg-gray-100', 'text-gray-400');
            input.classList.add('bg-white', 'text-gray-800');
        } else {
            input.classList.add('bg-gray-100', 'text-gray-400');
            input.classList.remove('bg-white', 'text-gray-800');
            // Close visitor dropdown if field is disabled
            if (input.id === 'visitor-dropdown-btn-be') {
                document.getElementById('visitor-panel-be')?.classList.add('hidden');
            }
        }
    });
}

// ── Visitor combobox ──────────────────────────────────────────────────────
function toggleVisitorDropdown() {
    var panel = document.getElementById('visitor-panel-be');
    var btn = document.getElementById('visitor-dropdown-btn-be');
    if (btn.disabled) return;
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        document.getElementById('visitor-search-be').value = '';
        filterVisitorList('');
        setTimeout(function() { document.getElementById('visitor-search-be').focus(); }, 50);
    }
}

function selectVisitor(name) {
    document.getElementById('visitor-input-be').value = name;
    document.getElementById('visitor-panel-be').classList.add('hidden');
}

function filterVisitorList(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#visitor-list-be .visitor-opt').forEach(function(opt) {
        opt.style.display = opt.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

// Close visitor dropdown on outside click
document.addEventListener('click', function(e) {
    var combo = document.getElementById('visitor-combo-be');
    if (combo && !combo.contains(e.target)) {
        document.getElementById('visitor-panel-be')?.classList.add('hidden');
    }
});

function injectBulkEditIds() {
    var enabledFields = document.querySelectorAll('#bulk-edit-form .be-toggle:checked');
    if (enabledFields.length === 0) {
        alert('يرجى تفعيل حقل واحد على الأقل للتعديل.');
        return false;
    }
    if (allPagesSelected) {
        return confirm('سيتم تعديل جميع الـ ' + totalMembersCount.toLocaleString('ar') + ' عضو. هل أنت متأكد؟');
    }
    var checkedElems = document.querySelectorAll('.row-checkbox:checked');
    var idMap = {};
    checkedElems.forEach(function(cb) { idMap[cb.value] = true; });
    var uniqueCount = Object.keys(idMap).length;
    if (uniqueCount === 0) {
        alert('يرجى تحديد أعضاء من الجدول أولاً.');
        return false;
    }
    return confirm('سيتم تعديل ' + uniqueCount + ' عضو. هل أنت متأكد؟');
}

function removeEmptyFilters(form) {
    Array.from(form.elements).forEach(function(el) {
        if (!el.name) return;
        if (el.type === 'checkbox' || el.type === 'radio') return;
        if (el.value === '' || el.value === null) el.disabled = true;
    });
}

function toggleFilters() {
    var body  = document.getElementById('filter-body');
    var arrow = document.getElementById('filter-toggle-arrow');
    var hidden = body.style.display === 'none';
    body.style.display  = hidden ? '' : 'none';
    arrow.style.transform = hidden ? '' : 'rotate(-90deg)';
    localStorage.setItem('filtersHidden', hidden ? '0' : '1');
}

document.addEventListener('DOMContentLoaded', function() {
    var body  = document.getElementById('filter-body');
    var arrow = document.getElementById('filter-toggle-arrow');
    if (localStorage.getItem('filtersHidden') === '0') {
        // user explicitly opened it before — keep open
    } else {
        // default: closed
        if (body)  body.style.display  = 'none';
        if (arrow) arrow.style.transform = 'rotate(-90deg)';
    }
});

function clearSelection() {
    allPagesSelected = false;
    document.querySelectorAll('.row-checkbox').forEach(function(cb) { cb.checked = false; });
    var sa = document.getElementById('select-all');
    if (sa) { sa.checked = false; sa.indeterminate = false; }
    var sam = document.getElementById('select-all-mobile');
    if (sam) { sam.checked = false; sam.indeterminate = false; }
    var bar = document.getElementById('bulk-action-bar');
    bar.classList.add('hidden');
    bar.classList.remove('flex');
    document.getElementById('bulk-ids-container').innerHTML = '';
    document.getElementById('bulk-count').textContent = '';
    document.getElementById('bulk-delete-select-all').value = '0';
    document.getElementById('bulk-edit-select-all').value   = '0';
    var banner = document.getElementById('select-all-pages-banner');
    banner.classList.add('hidden'); banner.classList.remove('flex');
    var beCount = document.getElementById('bulk-edit-selected-count');
    if (beCount) beCount.textContent = '';
    var editIds = document.getElementById('bulk-edit-ids-container');
    if (editIds) editIds.innerHTML = '';
}

var duplicatesHidden = false;

function toggleDuplicates() {
    var rows  = document.querySelectorAll('tr[data-duplicate="1"]');
    var label = document.getElementById('toggle-duplicates-label');
    var btn   = document.getElementById('toggle-duplicates-btn');

    duplicatesHidden = !duplicatesHidden;

    rows.forEach(function(row) {
        row.style.display = duplicatesHidden ? 'none' : '';
    });

    if (duplicatesHidden) {
        label.textContent = 'إظهار التكرارات';
        btn.classList.remove('bg-red-50', 'text-red-600', 'border-red-200', 'hover:bg-red-100');
        btn.classList.add('bg-gray-100', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-200');
    } else {
        label.textContent = 'إخفاء التكرارات';
        btn.classList.remove('bg-gray-100', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-200');
        btn.classList.add('bg-red-50', 'text-red-600', 'border-red-200', 'hover:bg-red-100');
    }
}
</script>
@endpush

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Table header --}}
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gradient-to-l from-gray-50 to-white">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">قائمة الأعضاء</span>
        </div>
        <div class="flex items-center gap-2">
            <button id="toggle-duplicates-btn" onclick="toggleDuplicates()"
                    class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border transition-colors
                           bg-red-50 text-red-600 border-red-200 hover:bg-red-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
                <span id="toggle-duplicates-label">إخفاء التكرارات</span>
                <span id="toggle-duplicates-count" class="bg-red-200 text-red-700 rounded-full px-1.5 py-0.5 text-xs font-bold"></span>
            </button>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-3 py-1">{{ number_format($members->total()) }} عضو</span>
        </div>
    </div>

    {{-- Bulk action bar --}}
    <div id="bulk-action-bar" class="hidden flex-col border-b border-red-200">
        {{-- Select-all-pages banner --}}
        <div id="select-all-pages-banner" class="hidden items-center justify-center gap-3 px-5 py-2 bg-amber-50 border-b border-amber-200 text-sm">
            <span id="select-all-pages-msg" class="text-amber-800 font-medium"></span>
            <button type="button" onclick="selectAllPages()" id="select-all-pages-btn"
                    class="text-amber-700 font-bold underline hover:text-amber-900">تحديد الكل</button>
            <button type="button" onclick="cancelSelectAllPages()" id="cancel-all-pages-btn" class="hidden text-gray-500 font-medium hover:text-gray-700">تراجع</button>
        </div>
        {{-- Actions row --}}
        <div class="flex items-center gap-3 px-5 py-3 bg-red-50">
            <span id="bulk-count" class="text-sm font-bold text-red-700"></span>
            <form id="bulk-delete-form" method="POST" action="{{ route('members.bulk-destroy') }}"
                  data-confirm="هل أنت متأكد من حذف الأعضاء المحددين؟">
                @csrf
                @method('DELETE')
                <div id="bulk-ids-container"></div>
                <input type="hidden" name="select_all" id="bulk-delete-select-all" value="0">
                @foreach(request()->except(['page','_token']) as $key => $val)
                    @if(is_array($val))
                        @foreach($val as $v)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                    @endif
                @endforeach
                <button type="submit"
                        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف المحدد
                </button>
            </form>
            <button onclick="clearSelection()" class="text-sm text-gray-500 hover:text-gray-700 font-medium px-3 py-2 rounded-xl hover:bg-gray-100 transition-colors">إلغاء التحديد</button>
        </div>
    </div>

    @if($members->isEmpty())
        <div class="text-center py-20">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium mb-1">لا توجد نتائج مطابقة</p>
            <p class="text-gray-400 text-xs mb-4">جرب تغيير الفلاتر أو كلمة البحث</p>
            <a href="{{ route('members.index') }}"
               class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 text-sm font-semibold hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                مسح الفلاتر
            </a>
        </div>
    @else
        {{-- Mobile cards --}}
        <div class="block sm:hidden divide-y divide-gray-100">

            {{-- Mobile select-all bar --}}
            <div class="flex items-center gap-3 px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="select-all-mobile"
                           class="rounded border-gray-300 text-red-600 focus:ring-red-400">
                    <span class="text-sm font-semibold text-gray-700">تحديد الكل</span>
                </label>
                <span class="text-xs text-gray-400 mr-auto">{{ $members->count() }} في هذه الصفحة</span>
            </div>

            @foreach($members as $member)
            @php
                $sn   = $member->verificationStatus?->name ?? '';
                $cash = $member->sham_cash_account;
                if (str_contains($sn, 'رفض')) {
                    $cardBg = 'bg-rose-100';
                } elseif (str_contains($sn, 'طلب إلغاء')) {
                    $cardBg = 'bg-orange-100';
                } elseif (str_contains($sn, 'تقييد')) {
                    $cardBg = 'bg-violet-100';
                } elseif (str_contains($sn, 'تكرار')) {
                    $cardBg = 'bg-red-50';
                } elseif (str_contains($sn, 'تم') && $cash) {
                    $cardBg = 'bg-emerald-50';
                } elseif (str_contains($sn, 'تم') && !$cash) {
                    $cardBg = 'bg-blue-50';
                } elseif (str_contains($sn, 'نقص')) {
                    $cardBg = 'bg-amber-50';
                } else {
                    $cardBg = '';
                }
                $latestVisit    = $member->fieldVisits->first();
                $memberFinal    = ($member->estimated_amount ?? 0) + ($latestVisit?->estimated_amount ?? 0);
                $memberIban     = trim($member->paymentInfo?->iban ?? '');
                $ibanDuplicated = $memberIban !== '' && isset($duplicateIbans[$memberIban]);
                $shamLabel      = $cash === 'manual' ? 'يدوي' : 'نعم';
                $shamBadgeClass = $cash === 'manual'
                    ? 'text-amber-700 bg-amber-50 border-amber-300'
                    : 'text-emerald-700 bg-emerald-50 border-emerald-200';
            @endphp
            <div class="px-4 py-3.5 {{ $cardBg }}">

                {{-- Header: checkbox + name + actions --}}
                <div class="flex items-start gap-2.5 mb-2.5">
                    <input type="checkbox" class="row-checkbox row-checkbox-mobile mt-1 rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer shrink-0" value="{{ $member->id }}">
                    <div class="flex-1 min-w-0 flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <div class="font-bold text-gray-900 text-sm leading-snug truncate">{{ $member->full_name }}</div>
                            <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-500">
                                @if($member->dossier_number)<span class="font-mono font-semibold text-gray-700">{{ $member->dossier_number }}</span>@endif
                                @if($member->national_id)<span class="font-mono">{{ $member->national_id }}</span>@endif
                                @if($member->phone)<span>{{ $member->phone }}</span>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <a href="{{ route('members.show', $member) }}"
                               class="p-1.5 rounded-lg text-blue-500 hover:text-blue-700 hover:bg-blue-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('members.edit', $member) }}"
                               class="p-1.5 rounded-lg text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('members.destroy', $member) }}"
                                  data-confirm-name="{{ $member->full_name }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="mr-7 space-y-2">

                    {{-- Region / Sector / Delegate / Second person --}}
                    @if($member->region || $member->sector || $member->delegate || $member->second_person)
                    <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500">
                        @if($member->region)
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-violet-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="font-medium text-gray-700">{{ $member->region->name }}</span>
                            </span>
                        @endif
                        @if($member->sector)
                            <span class="text-gray-500">قطاع: <span class="font-medium text-gray-700">{{ $member->sector->name }}</span></span>
                        @endif
                        @if($member->delegate)
                            <span class="text-gray-500">مندوب: <span class="font-medium text-gray-700">{{ $member->delegate }}</span></span>
                        @endif
                        @if($member->second_person)
                            <span class="text-gray-500">فرد 2: <span class="font-medium text-gray-700">{{ $member->second_person }}</span></span>
                        @endif
                    </div>
                    @endif

                    {{-- Badges: marital / network / housing --}}
                    @if($member->marital_status || $member->network || $member->housingStatus)
                    <div class="flex flex-wrap gap-1.5">
                        @if($member->marital_status)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-100">{{ $member->marital_status }}</span>
                        @endif
                        @if($member->network)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-cyan-50 text-cyan-700 border border-cyan-100">{{ $member->network }}</span>
                        @endif
                        @if($member->housingStatus)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                                  style="background:{{ $member->housingStatus->color }}22;color:{{ $member->housingStatus->color }};border-color:{{ $member->housingStatus->color }}44">
                                {{ $member->housingStatus->name }}
                            </span>
                        @endif
                    </div>
                    @endif

                    {{-- Sham cash --}}
                    @if($cash)
                    <div>
                        @if($ibanDuplicated)
                            <a href="{{ route('payment-review.duplicate-ibans', ['search' => $memberIban]) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-50 border border-red-300 rounded-full px-2.5 py-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                شام كاش: {{ $shamLabel }}
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $shamBadgeClass }} border rounded-full px-2.5 py-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                شام كاش: {{ $shamLabel }}
                            </span>
                        @endif
                    </div>
                    @endif

                    {{-- Verification + Final status --}}
                    <div class="flex flex-wrap gap-1.5 items-center">
                        @if($member->verificationStatus)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                                  style="color:{{ $member->verificationStatus->color }};border-color:{{ $member->verificationStatus->color }}40;background:{{ $member->verificationStatus->color }}15">
                                {{ $member->verificationStatus->name }}
                            </span>
                        @endif
                        @if(auth()->user()?->role === 'admin')
                            <form method="POST" action="{{ route('members.final-status.update', $member) }}" class="inline-block">
                                @csrf @method('PATCH')
                                <select name="final_status_id" onchange="this.form.submit()"
                                        class="text-xs font-semibold rounded-full px-2 py-0.5 border cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all"
                                        style="@if($member->finalStatus) background:{{ $member->finalStatus->color }}18;color:{{ $member->finalStatus->color }};border-color:{{ $member->finalStatus->color }}40 @else background:#f9fafb;color:#9ca3af;border-color:#e5e7eb @endif">
                                    <option value="">— بدون —</option>
                                    @foreach($finalStatusList as $fs)
                                        <option value="{{ $fs->id }}" {{ $member->final_status_id == $fs->id ? 'selected' : '' }}>{{ $fs->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @elseif($member->finalStatus)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full border"
                                  style="color:{{ $member->finalStatus->color }};border-color:{{ $member->finalStatus->color }}40;background:{{ $member->finalStatus->color }}15">
                                {{ $member->finalStatus->name }}
                            </span>
                        @endif
                    </div>

                    {{-- Field visit --}}
                    @if($latestVisit)
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if($latestVisit->status)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold text-white"
                                  style="background:{{ $latestVisit->status->color }}">
                                {{ $latestVisit->status->name }}
                            </span>
                        @else
                            <span class="text-gray-400">جولة بدون حالة</span>
                        @endif
                        @if($latestVisit->visit_date)
                            <span class="text-gray-400">{{ $latestVisit->visit_date->format('Y/m/d') }}</span>
                        @endif
                        @if($latestVisit->visitor)
                            <span class="text-gray-500">{{ $latestVisit->visitor }}</span>
                        @endif
                    </div>
                    @endif

                    {{-- Amounts + payments --}}
                    @if($member->estimated_amount || $memberFinal > 0 || $member->payments_count !== null)
                    <div class="grid grid-cols-2 gap-2">
                        @if($member->estimated_amount)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-3 py-2">
                            <p class="text-xs text-emerald-600 font-medium mb-0.5">المبلغ المقدر</p>
                            <p class="text-sm font-black text-emerald-800 leading-none">
                                {{ number_format($member->estimated_amount, 0) }}
                                <span class="text-xs font-normal text-emerald-500">ل.س</span>
                            </p>
                        </div>
                        @endif
                        @if($memberFinal > 0)
                        <div class="bg-purple-50 border border-purple-200 rounded-xl px-3 py-2">
                            <p class="text-xs text-purple-600 font-medium mb-0.5">المبلغ النهائي</p>
                            <p class="text-sm font-black text-purple-800 leading-none">
                                {{ number_format($memberFinal, 0) }}
                                <span class="text-xs font-normal text-purple-400">ل.س</span>
                            </p>
                        </div>
                        @endif
                        @if($member->payments_count !== null)
                        <div class="bg-sky-50 border border-sky-200 rounded-xl px-3 py-2 {{ (!$member->estimated_amount && !($memberFinal > 0)) ? 'col-span-2' : '' }}">
                            <p class="text-xs text-sky-600 font-medium mb-0.5">الدفعات</p>
                            <p class="text-sm font-black text-sky-800 leading-none">{{ $member->payments_count }} <span class="text-xs font-normal text-sky-400">دفعة</span></p>
                        </div>
                        @endif
                    </div>
                    @endif

                </div>
            </div>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <div class="hidden sm:block">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200" style="background:linear-gradient(to left,#f8fafc,#f1f5f9)">
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-400 cursor-pointer">
                        </th>
                        <th class="text-right px-4 py-3.5">
                            <span class="text-[10px] font-bold tracking-widest text-gray-400 uppercase">العضو</span>
                        </th>
                        <th class="text-right px-4 py-3.5">
                            <span class="text-[10px] font-bold tracking-widest text-gray-400 uppercase">الموقع</span>
                        </th>
                        <th class="text-right px-4 py-3.5">
                            <span class="text-[10px] font-bold tracking-widest text-gray-400 uppercase">التفاصيل</span>
                        </th>
                        <th class="text-center px-4 py-3.5">
                            <span class="text-[10px] font-bold tracking-widest text-gray-400 uppercase">شام كاش</span>
                        </th>
                        <th class="text-right px-4 py-3.5">
                            <span class="text-[10px] font-bold tracking-widest text-gray-400 uppercase">الحالة</span>
                        </th>
                        <th class="text-right px-4 py-3.5">
                            <span class="text-[10px] font-bold tracking-widest text-gray-400 uppercase">الجولة الميدانية</span>
                        </th>
                        <th class="text-right px-4 py-3.5">
                            <span class="text-[10px] font-bold tracking-widest text-gray-400 uppercase">المبالغ والدفعات</span>
                        </th>
                        <th class="w-24 px-4 py-3.5"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                        @php
                            $sn   = $member->verificationStatus?->name ?? '';
                            $cash = $member->sham_cash_account;

                            if (str_contains($sn, 'رفض')) {
                                $trBg    = 'bg-rose-50 hover:bg-rose-100/70';
                                $trBorder = 'border-rose-100';
                                $isDark  = true;
                            } elseif (str_contains($sn, 'طلب إلغاء')) {
                                $trBg    = 'bg-orange-50 hover:bg-orange-100/70';
                                $trBorder = 'border-orange-100';
                                $isDark  = true;
                            } elseif (str_contains($sn, 'تقييد')) {
                                $trBg    = 'bg-violet-50 hover:bg-violet-100/70';
                                $trBorder = 'border-violet-100';
                                $isDark  = true;
                            } elseif (str_contains($sn, 'تكرار')) {
                                $trBg    = 'bg-red-50/60 hover:bg-red-50';
                                $trBorder = 'border-red-100';
                                $isDark  = false;
                            } elseif (str_contains($sn, 'تم') && $cash) {
                                $trBg    = 'bg-emerald-50/60 hover:bg-emerald-50';
                                $trBorder = 'border-emerald-100';
                                $isDark  = false;
                            } elseif (str_contains($sn, 'تم') && !$cash) {
                                $trBg    = 'bg-blue-50/60 hover:bg-blue-50';
                                $trBorder = 'border-blue-100';
                                $isDark  = false;
                            } elseif (str_contains($sn, 'نقص')) {
                                $trBg    = 'bg-amber-50/60 hover:bg-amber-50';
                                $trBorder = 'border-amber-100';
                                $isDark  = false;
                            } else {
                                $trBg    = 'bg-white hover:bg-slate-50/80';
                                $trBorder = 'border-gray-100';
                                $isDark  = false;
                            }

                            // Avatar color from name
                            $avatarColors = ['#6366f1','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6'];
                            $avatarColor  = $avatarColors[mb_ord(mb_substr($member->full_name, 0, 1)) % count($avatarColors)];
                            $avatarLetter = mb_strtoupper(mb_substr($member->full_name, 0, 1));
                        @endphp
                        <tr class="group border-b transition-all duration-150 {{ $trBg }} {{ $trBorder }} {{ $isDark ? 'dark-row' : '' }}"
                            {{ str_contains($sn, 'تكرار') ? 'data-duplicate="1"' : '' }}>

                            {{-- Checkbox --}}
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" class="row-checkbox row-checkbox-desktop rounded border-gray-300 text-emerald-600 focus:ring-emerald-400 cursor-pointer" value="{{ $member->id }}">
                            </td>

                            {{-- Col 1: Avatar + name + IDs + phone --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm font-bold shrink-0 shadow-sm"
                                         style="background:{{ $avatarColor }}">{{ $avatarLetter }}</div>
                                    <div class="min-w-0">
                                        <a href="{{ route('members.show', $member) }}"
                                           class="block font-bold text-gray-900 text-sm leading-tight group-hover:text-emerald-700 transition-colors truncate">
                                            {{ $member->full_name }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                            @if($member->dossier_number)
                                                <span class="inline-flex items-center gap-0.5 font-mono text-[11px] font-semibold text-gray-500 bg-gray-100 rounded px-1.5 py-px">#{{ $member->dossier_number }}</span>
                                            @endif
                                            @if($member->national_id)
                                                <span class="font-mono text-[11px] text-gray-400">{{ $member->national_id }}</span>
                                            @endif
                                            @if($member->phone)
                                                <span class="text-[11px] text-gray-400 flex items-center gap-0.5">
                                                    <svg class="w-2.5 h-2.5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    {{ $member->phone }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Col 2: Region / Sector / Delegate / Second person --}}
                            <td class="px-4 py-3.5">
                                @if($member->region)
                                    <div class="flex items-center gap-1 text-sm text-gray-800 font-medium leading-tight">
                                        <svg class="w-3 h-3 text-violet-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $member->region->name }}
                                        @if($member->sector)
                                            <span class="text-gray-300 font-normal">/</span>
                                            <span class="text-gray-500 font-normal text-xs">{{ $member->sector->name }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                                @if($member->delegate || $member->second_person)
                                    <div class="flex items-center gap-1 mt-1 text-[11px] text-gray-400">
                                        <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        @if($member->delegate)<span>{{ $member->delegate }}</span>@endif
                                        @if($member->delegate && $member->second_person)<span class="text-gray-300">·</span>@endif
                                        @if($member->second_person)<span>{{ $member->second_person }}</span>@endif
                                    </div>
                                @endif
                            </td>

                            {{-- Col 3: Housing + Network + Marital --}}
                            <td class="px-4 py-3.5">
                                <div class="flex flex-col gap-1">
                                    @if($member->housingStatus)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold w-fit border"
                                              style="background:{{ $member->housingStatus->color }}18; color:{{ $member->housingStatus->color }}; border-color:{{ $member->housingStatus->color }}35">
                                            {{ $member->housingStatus->name }}
                                        </span>
                                    @endif
                                    <div class="flex flex-wrap gap-1">
                                        @if($member->network)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-cyan-50 text-cyan-700 border border-cyan-100">{{ $member->network }}</span>
                                        @endif
                                        @if($member->marital_status)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">{{ $member->marital_status }}</span>
                                        @endif
                                    </div>
                                    @if(!$member->housingStatus && !$member->network && !$member->marital_status)
                                        <span class="text-gray-300 text-sm">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Col 4: Sham cash --}}
                            <td class="px-4 py-3.5 text-center">
                                @if($member->sham_cash_account)
                                    @php
                                        $memberIban     = trim($member->paymentInfo?->iban ?? '');
                                        $ibanDuplicated = $memberIban !== '' && isset($duplicateIbans[$memberIban]);
                                        $shamLabel      = $member->sham_cash_account === 'manual' ? 'يدوي' : 'نعم';
                                        $shamBadgeClass = $member->sham_cash_account === 'manual'
                                            ? 'text-amber-700 bg-amber-50 border-amber-200 shadow-amber-100'
                                            : 'text-emerald-700 bg-emerald-50 border-emerald-200 shadow-emerald-100';
                                    @endphp
                                    @if($ibanDuplicated)
                                        <a href="{{ route('payment-review.duplicate-ibans', ['search' => $memberIban]) }}"
                                           title="آيبان مكرر: {{ $memberIban }}"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg px-2.5 py-1 shadow-sm shadow-red-100 hover:bg-red-100 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                            {{ $shamLabel }}
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $shamBadgeClass }} border rounded-lg px-2.5 py-1 shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            {{ $shamLabel }}
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-300 text-xs">✕</span>
                                @endif
                            </td>

                            {{-- Col 5: Verification + Final status --}}
                            <td class="px-4 py-3.5">
                                <div class="flex flex-col gap-1.5">
                                    @if($member->verificationStatus)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border w-fit shadow-sm"
                                              style="background:{{ $member->verificationStatus->color }}12; color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}30; box-shadow:0 1px 2px {{ $member->verificationStatus->color }}18">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background:{{ $member->verificationStatus->color }}"></span>
                                            {{ $member->verificationStatus->name }}
                                        </span>
                                    @endif
                                    @if(auth()->user()?->role === 'admin')
                                        <form method="POST" action="{{ route('members.final-status.update', $member) }}">
                                            @csrf @method('PATCH')
                                            <select name="final_status_id" onchange="this.form.submit()"
                                                    class="text-xs font-semibold rounded-lg px-2.5 py-1 border cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all shadow-sm"
                                                    style="@if($member->finalStatus) background:{{ $member->finalStatus->color }}12; color:{{ $member->finalStatus->color }}; border-color:{{ $member->finalStatus->color }}30 @else background:#f9fafb; color:#9ca3af; border-color:#e5e7eb @endif">
                                                <option value="">— الحالة النهائية —</option>
                                                @foreach($finalStatusList as $fs)
                                                    <option value="{{ $fs->id }}" {{ $member->final_status_id == $fs->id ? 'selected' : '' }}>{{ $fs->name }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @elseif($member->finalStatus)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border w-fit shadow-sm"
                                              style="background:{{ $member->finalStatus->color }}12; color:{{ $member->finalStatus->color }}; border-color:{{ $member->finalStatus->color }}30">
                                            {{ $member->finalStatus->name }}
                                        </span>
                                    @elseif(!$member->verificationStatus)
                                        <span class="text-gray-300 text-sm">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Col 6: Field visit --}}
                            <td class="px-4 py-3.5">
                                @php $latestVisit = $member->fieldVisits->first(); @endphp
                                @if($latestVisit?->status)
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold text-white w-fit shadow-sm"
                                              style="background:{{ $latestVisit->status->color }}; box-shadow:0 1px 3px {{ $latestVisit->status->color }}55">
                                            {{ $latestVisit->status->name }}
                                        </span>
                                        @if($latestVisit->visit_date)
                                            <span class="text-[11px] text-gray-400 flex items-center gap-1">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                {{ $latestVisit->visit_date->format('Y/m/d') }}
                                            </span>
                                        @endif
                                        @if($latestVisit->visitor)
                                            <span class="text-[11px] text-gray-400">{{ $latestVisit->visitor }}</span>
                                        @endif
                                    </div>
                                @elseif($latestVisit)
                                    <span class="text-xs text-gray-400">{{ $latestVisit->visit_date?->format('Y/m/d') ?? 'بدون حالة' }}</span>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>

                            {{-- Col 7: Amounts + payments --}}
                            <td class="px-4 py-3.5">
                                @php $memberFinal = ($member->estimated_amount ?? 0) + ($member->fieldVisits->first()?->estimated_amount ?? 0); @endphp
                                @if($member->estimated_amount || $memberFinal > 0 || $member->payments_count !== null)
                                    <div class="flex flex-col gap-1">
                                        @if($member->estimated_amount)
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-sm font-bold text-emerald-700">{{ number_format($member->estimated_amount, 0) }}</span>
                                                <span class="text-[10px] text-emerald-500 font-medium">ل.س مقدر</span>
                                            </div>
                                        @endif
                                        @if($memberFinal > 0)
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-sm font-bold text-purple-700">{{ number_format($memberFinal, 0) }}</span>
                                                <span class="text-[10px] text-purple-400 font-medium">ل.س نهائي</span>
                                            </div>
                                        @endif
                                        @if($member->payments_count !== null)
                                            <div class="flex items-center gap-1">
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-sky-700 bg-sky-50 border border-sky-100 rounded-md px-2 py-0.5">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                    {{ $member->payments_count }} دفعة
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                    <a href="{{ route('members.show', $member) }}"
                                       title="عرض"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        عرض
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}"
                                       title="تعديل"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        تعديل
                                    </a>
                                    <form method="POST" action="{{ route('members.destroy', $member) }}"
                                          data-confirm-name="{{ $member->full_name }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="حذف"
                                                class="inline-flex items-center p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $members->links() }}
            </div>
        @endif
    @endif
</div>

<style>
/* Dark-background rows keep text legible */
tr.dark-row td,
tr.dark-row td span:not([style]),
tr.dark-row td a { color: rgba(0,0,0,0.80) !important; }
tr.dark-row td .text-gray-300,
tr.dark-row td .text-gray-400 { color: rgba(0,0,0,0.40) !important; }

/* Smooth action button reveal on row hover */
tr .opacity-0 { transition: opacity 0.15s ease; }

/* Subtle left accent bar on hovered rows */
tr:hover td:first-child {
    box-shadow: inset 3px 0 0 #10b981;
}
</style>

@endsection
