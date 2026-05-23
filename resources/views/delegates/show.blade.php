@extends('layouts.app')

@section('title', $delegate . ' — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('delegates.index') }}" class="hover:text-sky-700 transition-colors">المندوبون</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">{{ $delegate }}</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-sky-600 via-blue-500 to-indigo-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 border-2 border-white/40 flex items-center justify-center shadow-lg shrink-0">
                <span class="text-white font-black text-2xl">{{ mb_substr($delegate, 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white">{{ $delegate }}</h1>
                <p class="text-sky-100 text-sm mt-0.5">{{ number_format($members->total()) }} عضو عند هذا المندوب</p>
            </div>
        </div>
        <a href="{{ route('delegates.index') }}"
           class="text-sm text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/30 px-4 py-2 rounded-xl transition-colors">
            رجوع
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <form method="GET" action="{{ route('delegates.show', $delegate) }}" id="filter-form"
          onsubmit="removeEmptyFilters(this)">

        {{-- Always visible: search + filter toggle --}}
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
                                  focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300">
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
                                  focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300
                                  {{ ($dossierSearch ?? '') ? 'border-sky-400 bg-sky-50/30' : '' }}">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    بحث
                </button>
                <button type="button" onclick="toggleFilters()"
                        class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-sky-300 transition-colors text-sm font-bold text-gray-600">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300 font-mono">
            </div>
            <span class="text-gray-400 pb-2.5">—</span>
            <div class="flex-1 max-w-xs">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                <input type="text" name="dossier_to" value="{{ $dossierTo }}"
                       placeholder="مثال: 200"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300 font-mono">
            </div>
        </div>

        {{-- Amount ranges --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">المبلغ المقدر من</label>
                    <input type="number" name="estimated_from" value="{{ $estimatedFrom }}" min="0" step="any"
                           placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="estimated_to" value="{{ $estimatedTo }}" min="0" step="any"
                           placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">ل.س مقدر</span>
            </div>
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">عدد الدفعات من</label>
                    <input type="number" name="payments_count_from" value="{{ $paymentsCountFrom }}" min="0" step="1"
                           placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="payments_count_to" value="{{ $paymentsCountTo }}" min="0" step="1"
                           placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">دفعة</span>
            </div>
        </div>

        {{-- Row 1: status / marital / gender / association / special --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-3">

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">حالة التحقق</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="verification_status_id[]" value="none" {{ in_array('none', $verificationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>بدون حالة
                    </label>
                    @foreach($verificationStatuses as $vs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="verification_status_id[]" value="{{ $vs->id }}" {{ in_array($vs->id, $verificationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $vs->color }}"></span>{{ $vs->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة النهائية</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="final_status_id[]" value="none" {{ in_array('none', $finalStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>بدون
                    </label>
                    @foreach($finalStatusList as $fs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}" {{ in_array($fs->id, $finalStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $fs->color }}"></span>{{ $fs->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة الاجتماعية</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="marital_status[]" value="none" {{ in_array('none', $maritalStatuses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>بدون
                    </label>
                    @foreach($maritalStatusList as $ms)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="marital_status[]" value="{{ $ms->name }}" {{ in_array($ms->name, $maritalStatuses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">
                            {{ $ms->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجنس</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    @foreach(['ذكر', 'أنثى'] as $g)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="gender[]" value="{{ $g }}" {{ in_array($g, $genders) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">{{ $g }}
                        </label>
                    @endforeach
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-t border-gray-100">
                        <input type="checkbox" name="gender[]" value="none" {{ in_array('none', $genders) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">غير محدد
                    </label>
                </div>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجمعية</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="association_id[]" value="none" {{ in_array('none', $associationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>بدون
                    </label>
                    @forelse($associationList as $assoc)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="association_id[]" value="{{ $assoc->id }}" {{ in_array($assoc->id, $associationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">{{ $assoc->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد جمعيات</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Row 2 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالات الخاصة</label>
                <select name="special_cases" onwheel="this.blur()"
                        class="w-full text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1" {{ $specialCases === '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ $specialCases === '0' ? 'selected' : '' }}>لا</option>
                </select>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">شام كاش</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    @foreach(['done' => 'تم', 'manual' => 'يدوي', 'none' => 'لا يوجد'] as $val => $lbl)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="sham_cash[]" value="{{ $val }}" {{ in_array($val, (array) request('sham_cash', [])) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">{{ $lbl }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">القطاع</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400" placeholder="بحث في القطاعات...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                            <input type="checkbox" name="sector_id[]" value="none" {{ in_array('none', $sectorIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>بدون
                        </label>
                        @forelse($sectorList as $sec)
                            <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                                <input type="checkbox" name="sector_id[]" value="{{ $sec->id }}" {{ in_array($sec->id, $sectorIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $sec->name }}
                            </label>
                        @empty
                            <p class="px-3 py-2 text-sm text-gray-400">لا توجد قطاعات</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المنطقة</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400" placeholder="بحث في المناطق...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                            <input type="checkbox" name="region_id[]" value="none" {{ in_array('none', $regionIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>بدون
                        </label>
                        @forelse($regionList as $reg)
                            <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                                <input type="checkbox" name="region_id[]" value="{{ $reg->id }}" {{ in_array($reg->id, $regionIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">
                                <svg class="w-3.5 h-3.5 text-teal-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $reg->name }}
                            </label>
                        @empty
                            <p class="px-3 py-2 text-sm text-gray-400">لا توجد مناطق</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Row 3 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">وضع السكن</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($housingStatusList as $hs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="housing_status_id[]" value="{{ $hs->id }}" {{ in_array($hs->id, $housingStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hs->color }}"></span>{{ $hs->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد أوضاع سكن</p>
                    @endforelse
                </div>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">نوع الشبكة</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                        <input type="checkbox" name="network[]" value="none" {{ in_array('none', $networks) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>بدون
                    </label>
                    @foreach(['MTN', 'SYRIATEL'] as $net)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="network[]" value="{{ $net }}" {{ in_array($net, $networks) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">{{ $net }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">وصف الحالة الخاصة</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400" placeholder="بحث في الأوصاف...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                            <input type="checkbox" name="special_cases_description[]" value="none" {{ in_array('none', $specialDescriptions) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>بدون
                        </label>
                        @forelse($specialDescriptionList as $sd)
                            <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                                <input type="checkbox" name="special_cases_description[]" value="{{ $sd }}" {{ in_array($sd, $specialDescriptions) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">
                                <span class="truncate">{{ $sd }}</span>
                            </label>
                        @empty
                            <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات خاصة</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">العنوان التفصيلي</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-sky-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400" placeholder="بحث في العناوين...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                            <input type="checkbox" name="current_address[]" value="none" {{ in_array('none', $addresses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>بدون
                        </label>
                        @forelse($addressList as $addr)
                            <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                                <input type="checkbox" name="current_address[]" value="{{ $addr }}" {{ in_array($addr, $addresses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-sky-600 focus:ring-sky-400">
                                <span class="truncate">{{ $addr }}</span>
                            </label>
                        @empty
                            <p class="px-3 py-2 text-xs text-gray-400">لا توجد عناوين</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- Field Visit Filters --}}
        @php
            $hasFvFilters = !empty($fieldVisitStatusIds) || !empty($fvHouseTypeIds) || !empty($fvHouseConditionIds) || !empty($fvVisitors)
                || !empty($fvCreatedByIds)
                || $fvDateFrom !== '' || $fvDateTo !== ''
                || $fvAmountFrom !== '' || $fvAmountTo !== ''
                || $fvNotes !== '' || $fvHasVideo !== '' || $fvHasSpecialCase !== '';
            $fvActiveCount = (int)!empty($fieldVisitStatusIds) + (int)!empty($fvHouseTypeIds) + (int)!empty($fvHouseConditionIds)
                + (!empty($fvVisitors) ? 1 : 0) + (!empty($fvCreatedByIds) ? 1 : 0) + ($fvDateFrom !== '' || $fvDateTo !== '' ? 1 : 0)
                + ($fvAmountFrom !== '' || $fvAmountTo !== '' ? 1 : 0)
                + ($fvNotes !== '' ? 1 : 0) + ($fvHasVideo !== '' ? 1 : 0) + ($fvHasSpecialCase !== '' ? 1 : 0);
        @endphp
        <div class="border border-indigo-100 rounded-2xl mb-4">
            <button type="button" onclick="toggleFvFilters()"
                    class="w-full flex items-center justify-between gap-3 px-5 py-3 bg-indigo-50/60 hover:bg-indigo-50 transition-colors text-right rounded-t-2xl">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-indigo-700">فلاتر الجولة الميدانية</span>
                    @if($fvActiveCount > 0)
                        <span class="text-xs bg-indigo-600 text-white rounded-full px-2 py-0.5 font-bold">{{ $fvActiveCount }} فعّال</span>
                    @endif
                </div>
                <svg id="fv-filter-arrow" class="w-4 h-4 text-indigo-400 transition-transform duration-200 {{ $hasFvFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="fv-filter-body" class="{{ $hasFvFilters ? '' : 'hidden' }} px-5 pb-5 pt-4 bg-indigo-50/20 rounded-b-2xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">

                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة الجولة</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                <input type="checkbox" name="field_visit_status_id[]" value="none" {{ in_array('none', $fieldVisitStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-gray-300"></span>بدون جولة ميدانية
                            </label>
                            @forelse($fieldVisitStatuses as $fvs)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="field_visit_status_id[]" value="{{ $fvs->id }}" {{ in_array($fvs->id, $fieldVisitStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $fvs->color }}"></span>{{ $fvs->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا توجد حالات</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">نوع البيت</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($houseTypes as $ht)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_house_type_id[]" value="{{ $ht->id }}" {{ in_array($ht->id, $fvHouseTypeIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $ht->color }}"></span>{{ $ht->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا توجد أنواع</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">الزائر</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($fvVisitorList as $vis)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700 ms-option">
                                    <input type="checkbox" name="fv_visitors[]" value="{{ $vis }}" {{ in_array($vis, $fvVisitors) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">{{ $vis }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا يوجد زوار مسجّلون</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">من أضاف الجولة</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($fvCreatedByList as $u)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_created_by[]" value="{{ $u->id }}" {{ in_array($u->id, $fvCreatedByIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">{{ $u->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-sm text-gray-400">لا يوجد بيانات</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">تاريخ الزيارة</label>
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="fv_date_from" value="{{ $fvDateFrom }}" class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                            <span class="text-xs text-indigo-400 shrink-0">—</span>
                            <input type="date" name="fv_date_to" value="{{ $fvDateTo }}" class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                        </div>
                    </div>

                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">مبلغ الجولة (ل.س)</label>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="fv_amount_from" value="{{ $fvAmountFrom }}" placeholder="من" min="0" class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                            <span class="text-xs text-indigo-400 shrink-0">—</span>
                            <input type="number" name="fv_amount_to" value="{{ $fvAmountTo }}" placeholder="إلى" min="0" class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                        </div>
                    </div>

                    <div class="ms-dropdown relative">
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة البيت</label>
                        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                            <span class="ms-label text-gray-500 truncate">— الكل —</span>
                            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                            @forelse($houseConditions as $hc)
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                    <input type="checkbox" name="fv_house_condition_id[]" value="{{ $hc->id }}" {{ in_array($hc->id, $fvHouseConditionIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hc->color }}"></span>{{ $hc->name }}
                                </label>
                            @empty
                                <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">الملاحظات</label>
                        <input type="text" name="fv_notes" value="{{ $fvNotes }}" placeholder="بحث في الملاحظات..."
                               class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">يوجد فيديو</label>
                        <select name="fv_has_video" onwheel="this.blur()" class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $fvHasVideo === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $fvHasVideo === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة خاصة</label>
                        <select name="fv_has_special_case" onwheel="this.blur()" class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                            <option value="">— الكل —</option>
                            <option value="1" {{ $fvHasSpecialCase === '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ $fvHasSpecialCase === '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">عدد الجولات</label>
                        <select name="fv_count" onwheel="this.blur()" class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
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
        @php
            $hasFilters = $search || $dossierSearch !== '' || $dossierFrom !== '' || $dossierTo !== ''
                || !empty($verificationIds) || !empty($finalStatusIds) || !empty($maritalStatuses) || !empty($genders)
                || !empty($secondPersons) || $specialCases !== '' || !empty($specialDescriptions) || !empty($addresses)
                || !empty($associationIds) || !empty($networks) || !empty($housingStatusIds) || !empty($shamCash)
                || !empty($regionIds) || !empty($sectorIds) || $estimatedFrom !== '' || $estimatedTo !== ''
                || $paymentsCountFrom !== '' || $paymentsCountTo !== '' || $hasFvFilters;
        @endphp
        <div class="flex items-center gap-2 flex-wrap">
            <button type="submit"
                    class="flex items-center gap-2 bg-gradient-to-l from-sky-600 to-blue-500 hover:from-sky-700 hover:to-blue-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm hover:shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                تطبيق الفلاتر
            </button>
            @if($hasFilters)
                <span class="inline-flex items-center gap-1.5 bg-sky-50 border border-sky-200 text-sky-700 text-sm font-bold px-4 py-2.5 rounded-xl">
                    {{ number_format($members->total()) }} نتيجة
                </span>
                <a href="{{ route('delegates.show', $delegate) }}"
                   class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
            @endif
        </div>

        </div>{{-- end p-5 --}}
        </div>{{-- end filter-body --}}

    </form>
</div>

{{-- Members table --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100">
        <div class="w-7 h-7 rounded-lg bg-sky-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-gray-700">الأعضاء ({{ number_format($members->total()) }})</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">رقم الاضبارة</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">الاسم</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">الهاتف</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">حالة التحقق</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">الحالة النهائية</th>
                    <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3">المبلغ المقدر</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($members as $member)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-4 py-3.5 text-sm text-gray-400 font-mono">{{ $member->dossier_number ?? '—' }}</td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('members.show', $member) }}"
                           class="font-semibold text-gray-800 text-sm hover:text-sky-700 hover:underline">{{ $member->full_name }}</a>
                    </td>
                    <td class="px-4 py-3.5 text-sm text-gray-600 font-mono">{{ $member->phone ?? '—' }}</td>
                    <td class="px-4 py-3.5">
                        @if($member->verificationStatus)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full border"
                                  style="color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}40; background:{{ $member->verificationStatus->color }}15">
                                <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $member->verificationStatus->color }}"></span>
                                {{ $member->verificationStatus->name }}
                            </span>
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        @if($member->finalStatus)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full border"
                                  style="color:{{ $member->finalStatus->color }}; border-color:{{ $member->finalStatus->color }}40; background:{{ $member->finalStatus->color }}15">
                                <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $member->finalStatus->color }}"></span>
                                {{ $member->finalStatus->name }}
                            </span>
                        @else
                            <span class="text-gray-300 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-sm font-semibold text-gray-700">
                        {{ $member->estimated_amount ? number_format($member->estimated_amount, 0) . ' ل.س' : '—' }}
                    </td>
                    <td class="px-4 py-3.5">
                        <a href="{{ route('members.show', $member) }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold text-sky-600 hover:text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-lg px-2.5 py-1 transition-colors">
                            عرض
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">لا توجد نتائج تطابق الفلاتر المحددة.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $members->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleFvFilters() {
    const body  = document.getElementById('fv-filter-body');
    const arrow = document.getElementById('fv-filter-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function toggleFilters() {
    var body  = document.getElementById('filter-body');
    var arrow = document.getElementById('filter-toggle-arrow');
    var hidden = body.style.display === 'none';
    body.style.display  = hidden ? '' : 'none';
    arrow.style.transform = hidden ? '' : 'rotate(-90deg)';
    localStorage.setItem('delegateFiltersHidden', hidden ? '0' : '1');
}

function removeEmptyFilters(form) {
    Array.from(form.elements).forEach(function(el) {
        if (!el.name) return;
        if (el.type === 'checkbox' || el.type === 'radio') return;
        if (el.value === '' || el.value === null) el.disabled = true;
    });
}

document.addEventListener('DOMContentLoaded', function () {
    // Restore filter panel state
    var body  = document.getElementById('filter-body');
    var arrow = document.getElementById('filter-toggle-arrow');
    if (localStorage.getItem('delegateFiltersHidden') !== '0') {
        if (body)  body.style.display  = 'none';
        if (arrow) arrow.style.transform = 'rotate(-90deg)';
    }

    // Multi-select dropdowns
    document.querySelectorAll('.ms-dropdown').forEach(function (dropdown) {
        const btn   = dropdown.querySelector('.ms-btn');
        const panel = dropdown.querySelector('.ms-panel');
        const label = dropdown.querySelector('.ms-label');
        const arrow = dropdown.querySelector('.ms-arrow');

        function updateLabel() {
            const checked = dropdown.querySelectorAll('.ms-check:checked');
            if (checked.length === 0) {
                label.textContent = '— الكل —';
                label.classList.remove('text-sky-700', 'font-semibold');
                label.classList.add('text-gray-500');
            } else {
                label.textContent = checked.length + ' محدد';
                label.classList.add('text-sky-700', 'font-semibold');
                label.classList.remove('text-gray-500');
            }
        }

        updateLabel();

        panel.querySelectorAll('label').forEach(function (lbl) { lbl.classList.add('ms-option'); });

        var allOptions = panel.querySelectorAll('.ms-option');
        var searchInput = null;
        if (allOptions.length >= 4 && !panel.querySelector('.ms-search')) {
            var stickyHeader = document.createElement('div');
            stickyHeader.className = 'sticky top-0 bg-white z-10 px-2 pt-2 pb-1 border-b border-gray-100';
            searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'ابحث...';
            searchInput.className = 'ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-sky-400 bg-gray-50';
            searchInput.setAttribute('dir', 'rtl');
            stickyHeader.appendChild(searchInput);
            panel.insertBefore(stickyHeader, panel.firstChild);
            searchInput.addEventListener('input', function () {
                var q = searchInput.value.trim().toLowerCase();
                panel.querySelectorAll('.ms-option').forEach(function (opt) {
                    opt.style.display = (!q || opt.textContent.trim().toLowerCase().includes(q)) ? '' : 'none';
                });
            });
            searchInput.addEventListener('click', function (e) { e.stopPropagation(); });
        }

        const checks = dropdown.querySelectorAll('.ms-check');
        if (checks.length >= 2) {
            const saBtn = document.createElement('button');
            saBtn.type = 'button';
            saBtn.className = 'w-full text-right text-xs text-sky-600 font-semibold px-3 py-1.5 hover:bg-sky-50 transition flex items-center gap-1';
            saBtn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span class="sa-text">تحديد الكل</span>';
            function refreshSaBtn() {
                const c = dropdown.querySelectorAll('.ms-check:checked').length;
                saBtn.querySelector('.sa-text').textContent = c === checks.length ? 'إلغاء التحديد' : 'تحديد الكل';
            }
            saBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const shouldCheck = dropdown.querySelectorAll('.ms-check:checked').length < checks.length;
                checks.forEach(function (cb) { cb.checked = shouldCheck; });
                updateLabel(); refreshSaBtn();
            });
            checks.forEach(function (cb) { cb.addEventListener('change', refreshSaBtn); });
            refreshSaBtn();
            const searchEl = panel.querySelector('.ms-search');
            if (searchEl) { saBtn.classList.add('mt-1', 'border-t', 'border-gray-100'); searchEl.parentElement.appendChild(saBtn); }
            else { saBtn.classList.add('border-b', 'border-gray-100', 'mb-1'); panel.insertBefore(saBtn, panel.firstChild); }
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
                if (searchInput) {
                    searchInput.value = '';
                    panel.querySelectorAll('.ms-option').forEach(function (opt) { opt.style.display = ''; });
                    setTimeout(function () { searchInput.focus(); }, 50);
                }
            }
        });

        dropdown.querySelectorAll('.ms-check').forEach(function (cb) { cb.addEventListener('change', updateLabel); });
    });

    document.addEventListener('click', function () {
        document.querySelectorAll('.ms-panel').forEach(function (p) {
            p.classList.add('hidden');
            p.closest('.ms-dropdown').querySelector('.ms-arrow').classList.remove('rotate-180');
        });
    });

    document.querySelectorAll('.ms-panel').forEach(function (p) { p.addEventListener('click', function (e) { e.stopPropagation(); }); });

    document.querySelectorAll('.ms-search').forEach(function (input) {
        input.addEventListener('input', function () {
            const q = input.value.trim().toLowerCase();
            const panel = input.closest('.ms-panel');
            panel.querySelectorAll('.ms-option').forEach(function (opt) {
                opt.style.display = (!q || opt.textContent.trim().toLowerCase().includes(q)) ? '' : 'none';
            });
        });
    });
});
</script>
@endpush

@endsection
