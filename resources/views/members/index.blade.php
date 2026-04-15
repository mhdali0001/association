@extends('layouts.app')

@section('title', 'الأعضاء — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">الأعضاء</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-emerald-600 via-emerald-500 to-teal-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">الأعضاء</h1>
            <p class="text-emerald-100 text-sm mt-0.5">إجمالي المسجلين: <span class="font-bold text-white">{{ number_format($members->total()) }}</span> عضو</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[160px]">
                <p class="text-white font-black text-xl leading-none">{{ number_format((float)$totalAmount, 0, '.', ',') }}</p>
                <p class="text-emerald-200 text-xs mt-0.5">مجموع المبالغ المقدرة (ل.س)</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[160px]">
                <p class="text-white font-black text-xl leading-none">{{ number_format((float)$totalFinalAmount, 0, '.', ',') }}</p>
                <p class="text-purple-200 text-xs mt-0.5">مجموع المبالغ النهائية (ل.س)</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('members.import.show') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors backdrop-blur-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                استيراد Excel
            </a>
            <a href="{{ route('members.export', request()->query()) }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors backdrop-blur-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                تصدير Excel
            </a>
            <a href="{{ route('members.duplicates') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors backdrop-blur-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                كشف التكرارات
            </a>
            <a href="{{ route('members.bulk-amount') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors backdrop-blur-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                تعديل المبالغ
            </a>
            <a href="{{ route('members.create') }}"
               class="flex items-center gap-2 bg-white text-emerald-700 hover:bg-emerald-50 text-sm font-bold px-5 py-2 rounded-xl transition-colors shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة عضو جديد
            </a>
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
    {{-- Filter header --}}
    <button type="button" onclick="toggleFilters()"
            class="w-full flex items-center justify-between gap-2 px-5 py-3.5 border-b border-gray-100 hover:bg-gray-50 transition-colors text-right">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">الفلاتر والبحث</span>
        </div>
        <svg id="filter-toggle-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div id="filter-body">
    <form method="GET" action="{{ route('members.index') }}" id="filter-form" class="p-5"
          onsubmit="removeEmptyFilters(this)">

        {{-- Search bar --}}
        <div class="relative mb-4">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
            </span>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="بحث بالاسم، رقم الهوية، الهاتف، أو رقم الملف..."
                   class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50
                          focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300">
        </div>

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

            {{-- Final amount range --}}
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">المبلغ النهائي من</label>
                    <input type="number" name="final_from" value="{{ $finalFrom }}" min="0" step="any"
                           placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="final_to" value="{{ $finalTo }}" min="0" step="any"
                           placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">ل.س نهائي</span>
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

        </div>

        {{-- Field Visit Filters (dedicated section) --}}
        @php
            $hasFvFilters = !empty($fieldVisitStatusIds) || !empty($fvHouseTypeIds) || $fvVisitor !== ''
                || $fvDateFrom !== '' || $fvDateTo !== ''
                || $fvAmountFrom !== '' || $fvAmountTo !== ''
                || $fvHouseCondition !== '' || $fvNotes !== '';
            $fvActiveCount = (int)!empty($fieldVisitStatusIds) + (int)!empty($fvHouseTypeIds)
                + ($fvVisitor !== '' ? 1 : 0) + ($fvDateFrom !== '' || $fvDateTo !== '' ? 1 : 0)
                + ($fvAmountFrom !== '' || $fvAmountTo !== '' ? 1 : 0)
                + ($fvHouseCondition !== '' ? 1 : 0) + ($fvNotes !== '' ? 1 : 0);
        @endphp
        <div class="border border-indigo-100 rounded-2xl overflow-hidden mb-4" id="fv-filter-section">
            <button type="button" onclick="toggleFvFilters()"
                    class="w-full flex items-center justify-between gap-3 px-5 py-3 bg-indigo-50/60 hover:bg-indigo-50 transition-colors text-right">
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
            <div id="fv-filter-body" class="{{ $hasFvFilters ? '' : 'hidden' }} px-5 pb-5 pt-4 bg-indigo-50/20">
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

                    {{-- اسم الزائر --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">اسم الزائر</label>
                        <input type="text" name="fv_visitor" value="{{ $fvVisitor }}"
                               placeholder="بحث باسم الزائر..."
                               class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
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
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة البيت</label>
                        <input type="text" name="fv_house_condition" value="{{ $fvHouseCondition }}"
                               placeholder="بحث في حالة البيت..."
                               class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                    </div>

                    {{-- الملاحظات --}}
                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">الملاحظات</label>
                        <input type="text" name="fv_notes" value="{{ $fvNotes }}"
                               placeholder="بحث في الملاحظات..."
                               class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
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
                $hasFilters = $search || $dossierFrom !== '' || $dossierTo !== '' || !empty($verificationIds) || !empty($finalStatusIds) || !empty($maritalStatuses) || !empty($genders) || !empty($delegates) || $specialCases !== '' || !empty($specialDescriptions) || !empty($addresses) || !empty($associationIds) || !empty($networks) || $hasFvFilters;
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
                @foreach($fieldVisitStatusIds as $fvsId)
                    <a href="{{ badgeRemoveUrl('field_visit_status_id', $fvsId) }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        <svg class="w-3 h-3 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $fieldVisitStatuses->firstWhere('id', $fvsId)?->name }}
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
                @if($fvVisitor !== '')
                    <a href="{{ badgeRemoveUrl('fv_visitor') }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        زائر: {{ Str::limit($fvVisitor, 20) }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
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
                @if($fvHouseCondition !== '')
                    <a href="{{ badgeRemoveUrl('fv_house_condition') }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        حالة البيت: {{ Str::limit($fvHouseCondition, 20) }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
                @if($fvNotes !== '')
                    <a href="{{ badgeRemoveUrl('fv_notes') }}" class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium hover:bg-indigo-100 transition-colors">
                        ملاحظات الجولة: {{ Str::limit($fvNotes, 20) }}
                        <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
            @endif
        </div>
    </form>
    </div>
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

                {{-- Sham Cash --}}
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

                {{-- Final Status --}}
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

                {{-- Estimated Amount --}}
                <div class="be-field" data-field="estimated_amount">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="estimated_amount" class="be-toggle rounded border-gray-300 text-blue-600 focus:ring-blue-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">المبلغ المقدر (ل.س)</label>
                    </div>
                    <input type="number" name="fields[estimated_amount]" min="0" disabled placeholder="أدخل المبلغ..."
                           class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                </div>

                {{-- Final Amount --}}
                <div class="be-field" data-field="final_amount">
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="checkbox" name="apply_fields[]" value="final_amount" class="be-toggle rounded border-gray-300 text-purple-600 focus:ring-purple-400 cursor-pointer" onchange="toggleBulkField(this)">
                        <label class="text-sm font-semibold text-gray-600 cursor-pointer select-none">المبلغ النهائي (ل.س)</label>
                    </div>
                    <input type="number" name="fields[final_amount]" min="0" step="0.01" disabled placeholder="أدخل المبلغ..."
                           class="be-input w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-100 text-gray-400 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-400 transition">
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
                p.classList.add('hidden');
                p.closest('.ms-dropdown').querySelector('.ms-arrow').classList.remove('rotate-180');
            });
            if (!isOpen) {
                panel.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            }
        });

        dropdown.querySelectorAll('.ms-check').forEach(function (cb) {
            cb.addEventListener('change', updateLabel);
        });
    });

    document.addEventListener('click', function () {
        document.querySelectorAll('.ms-panel').forEach(function (p) {
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
    var selectAll    = document.getElementById('select-all');
    var bulkBar      = document.getElementById('bulk-action-bar');
    var bulkCount    = document.getElementById('bulk-count');
    var bulkIdsContainer = document.getElementById('bulk-ids-container');

    function updateBulkBar() {
        var checked = document.querySelectorAll('.row-checkbox:checked');
        var all     = document.querySelectorAll('.row-checkbox');

        if (checked.length > 0) {
            bulkBar.classList.remove('hidden');
            bulkBar.classList.add('flex');
            bulkCount.textContent = 'تم تحديد ' + checked.length + ' عضو';

            bulkIdsContainer.innerHTML = '';
            var editIds = document.getElementById('bulk-edit-ids-container');
            if (editIds) editIds.innerHTML = '';

            checked.forEach(function(cb) {
                var inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
                bulkIdsContainer.appendChild(inp);

                if (editIds) {
                    var inp2 = document.createElement('input');
                    inp2.type = 'hidden'; inp2.name = 'ids[]'; inp2.value = cb.value;
                    editIds.appendChild(inp2);
                }
            });

            // Show select-all-pages banner only when all on page are checked and there are more pages
            var banner = document.getElementById('select-all-pages-banner');
            if (checked.length === all.length && totalMembersCount > pageCount && !allPagesSelected) {
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
            selectAll.checked = all.length > 0 && checked.length === all.length;
            selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
        }

        var beCount = document.getElementById('bulk-edit-selected-count');
        if (beCount) {
            beCount.textContent = checked.length > 0 ? ('(' + checked.length + ' عضو محدد)') : '';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(function(cb) {
                cb.checked = selectAll.checked;
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
    var input = field.querySelector('.be-input');
    input.disabled = !cb.checked;
    if (cb.checked) {
        input.classList.remove('bg-gray-100', 'text-gray-400');
        input.classList.add('bg-white', 'text-gray-800');
    } else {
        input.classList.add('bg-gray-100', 'text-gray-400');
        input.classList.remove('bg-white', 'text-gray-800');
    }
}

function injectBulkEditIds() {
    var enabledFields = document.querySelectorAll('#bulk-edit-form .be-toggle:checked');
    if (enabledFields.length === 0) {
        alert('يرجى تفعيل حقل واحد على الأقل للتعديل.');
        return false;
    }
    if (allPagesSelected) {
        return confirm('سيتم تعديل جميع الـ ' + totalMembersCount.toLocaleString('ar') + ' عضو. هل أنت متأكد؟');
    }
    var checked = document.querySelectorAll('.row-checkbox:checked');
    if (checked.length === 0) {
        alert('يرجى تحديد أعضاء من الجدول أولاً.');
        return false;
    }
    return confirm('سيتم تعديل ' + checked.length + ' عضو. هل أنت متأكد؟');
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
    if (localStorage.getItem('filtersHidden') === '1') {
        var body  = document.getElementById('filter-body');
        var arrow = document.getElementById('filter-toggle-arrow');
        if (body)  body.style.display  = 'none';
        if (arrow) arrow.style.transform = 'rotate(-90deg)';
    }
});

function clearSelection() {
    allPagesSelected = false;
    document.querySelectorAll('.row-checkbox').forEach(function(cb) { cb.checked = false; });
    var sa = document.getElementById('select-all');
    if (sa) { sa.checked = false; sa.indeterminate = false; }
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
            <form id="bulk-delete-form" method="POST" action="{{ route('members.bulk-destroy') }}">
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
                        onclick="return confirm('هل أنت متأكد من حذف الأعضاء المحددين؟ لا يمكن التراجع عن هذا الإجراء.')"
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
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100">
                        <th class="px-4 py-3.5 w-10">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer">
                        </th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">رقم الملف</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">العضو</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">رقم الهوية</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الهاتف</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">المنطقة</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">المندوب</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">نوع الشبكة</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الحالة الاجتماعية</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">شام كاش</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">حالة التحقق</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الحالة النهائية</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الجولة الميدانية</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">المبلغ المقدر</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">المبلغ النهائي</th>
                        <th class="px-4 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                        @php
                            $sn       = $member->verificationStatus?->name ?? '';
                            $cash     = $member->sham_cash_account;

                            if (str_contains($sn, 'رفض')) {
                                $trClass = 'dark-row bg-rose-200 hover:bg-rose-300 divide-rose-300';
                            } elseif (str_contains($sn, 'طلب إلغاء')) {
                                $trClass = 'dark-row bg-orange-200 hover:bg-orange-300 divide-orange-300';
                            } elseif (str_contains($sn, 'تقييد')) {
                                $trClass = 'dark-row bg-violet-200 hover:bg-violet-300 divide-violet-300';
                            } elseif (str_contains($sn, 'تكرار')) {
                                $trClass = 'bg-red-50 hover:bg-red-100 divide-red-100';
                            } elseif (str_contains($sn, 'تم') && $cash) {
                                $trClass = 'bg-emerald-50 hover:bg-emerald-100 divide-emerald-100';
                            } elseif (str_contains($sn, 'تم') && !$cash) {
                                $trClass = 'bg-blue-50 hover:bg-blue-100 divide-blue-100';
                            } elseif (str_contains($sn, 'نقص')) {
                                $trClass = 'bg-amber-50 hover:bg-amber-100 divide-amber-100';
                            } else {
                                $trClass = 'hover:bg-gray-50 divide-gray-50';
                            }
                        @endphp
                        <tr class="transition-colors group {{ $trClass }}" {{ str_contains($sn, 'تكرار') ? 'data-duplicate="1"' : '' }}>
                            <td class="px-4 py-4">
                                <input type="checkbox" class="row-checkbox rounded border-gray-300 text-red-600 focus:ring-red-400 cursor-pointer" value="{{ $member->id }}">
                            </td>
                            <td class="px-4 py-4 text-gray-800 font-mono font-semibold text-sm">{{ $member->dossier_number ?? '—' }}</td>
                            <td class="px-4 py-4">
                                <span class="font-bold text-gray-900 text-base group-hover:text-emerald-700 transition-colors">
                                    {{ $member->full_name }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-gray-800 font-mono font-semibold text-sm">{{ $member->national_id ?? '—' }}</td>
                            <td class="px-4 py-4 text-gray-800 font-semibold text-sm">{{ $member->phone ?? '—' }}</td>
                            <td class="px-4 py-4 text-gray-700 text-sm">{{ $member->region?->name ?? '—' }}</td>
                            <td class="px-4 py-4 text-gray-700 text-sm">{{ $member->delegate ?? '—' }}</td>
                            <td class="px-4 py-4">
                                @if($member->network)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold bg-cyan-50 text-cyan-700 border border-cyan-100">
                                        {{ $member->network }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($member->marital_status)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold bg-purple-50 text-purple-700 border border-purple-100">
                                        {{ $member->marital_status }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($member->sham_cash_account)
                                    @php
                                        $memberIban    = trim($member->paymentInfo?->iban ?? '');
                                        $ibanDuplicated = $memberIban !== '' && isset($duplicateIbans[$memberIban]);
                                        $shamLabel     = $member->sham_cash_account === 'manual' ? 'يدوي' : 'نعم';
                                        $shamBadgeClass = $member->sham_cash_account === 'manual'
                                            ? 'text-amber-700 bg-amber-50 border-amber-300'
                                            : 'text-emerald-700 bg-emerald-50 border-emerald-200';
                                    @endphp
                                    @if($ibanDuplicated)
                                        <a href="{{ route('payment-review.duplicate-ibans', ['search' => $memberIban]) }}"
                                           title="آيبان مكرر: {{ $memberIban }}"
                                           class="inline-flex items-center gap-1 text-sm font-semibold text-red-700 bg-red-50 border border-red-300 rounded-full px-2.5 py-0.5 hover:bg-red-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                            {{ $shamLabel }}
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-sm font-semibold {{ $shamBadgeClass }} border rounded-full px-2.5 py-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            {{ $shamLabel }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-300 font-medium">لا</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($member->verificationStatus)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold border"
                                          style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}40">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $member->verificationStatus->color }}"></span>
                                        {{ $member->verificationStatus->name }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <form method="POST" action="{{ route('members.final-status.update', $member) }}" class="inline-block">
                                    @csrf @method('PATCH')
                                    <select name="final_status_id" onchange="this.form.submit()"
                                            class="text-sm font-semibold rounded-full px-2.5 py-1 border cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all"
                                            style="@if($member->finalStatus) background:{{ $member->finalStatus->color }}18; color:{{ $member->finalStatus->color }}; border-color:{{ $member->finalStatus->color }}40 @else background:#f9fafb; color:#9ca3af; border-color:#e5e7eb @endif">
                                        <option value="">— بدون —</option>
                                        @foreach($finalStatusList as $fs)
                                            <option value="{{ $fs->id }}" {{ $member->final_status_id == $fs->id ? 'selected' : '' }}>{{ $fs->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-4 py-4">
                                @php $latestVisit = $member->fieldVisits->first(); @endphp
                                @if($latestVisit?->status)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                          style="background: {{ $latestVisit->status->color }}">
                                        {{ $latestVisit->status->name }}
                                    </span>
                                @elseif($latestVisit)
                                    <span class="text-xs text-gray-400 font-medium">{{ $latestVisit->visit_date?->format('Y/m/d') ?? 'جولة بدون حالة' }}</span>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($member->estimated_amount)
                                    <span class="inline-flex items-center gap-1 text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-2.5 py-1">
                                        {{ number_format($member->estimated_amount, 0) }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @php $memberFinal = ($member->estimated_amount ?? 0) + ($member->fieldVisits->first()?->estimated_amount ?? 0); @endphp
                                @if($memberFinal > 0)
                                    <span class="inline-flex items-center gap-1 text-sm font-bold text-purple-700 bg-purple-50 border border-purple-100 rounded-lg px-2.5 py-1">
                                        {{ number_format($memberFinal, 0) }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('members.show', $member) }}"
                                       title="عرض"
                                       class="p-1.5 rounded-lg text-blue-500 hover:text-blue-700 hover:bg-blue-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('members.edit', $member) }}"
                                       title="تعديل"
                                       class="p-1.5 rounded-lg text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('members.destroy', $member) }}"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="حذف"
                                                class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
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
tr.dark-row td,
tr.dark-row td span:not([style]),
tr.dark-row td a { color: rgba(0,0,0,0.85) !important; }
tr.dark-row td .text-gray-300 { color: rgba(0,0,0,0.35) !important; }
</style>

@endsection
