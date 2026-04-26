@extends('layouts.app')

@section('title', 'تعديل المبالغ — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="text-emerald-600 hover:underline">الأعضاء</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700">تعديل المبالغ</span>
@endsection

@section('content')

@php
    $qs  = request()->getQueryString();
    $fmt = fn($n) => number_format((float)$n, 0, '.', ',');
@endphp

<style>
#action-bar { box-shadow: 0 -4px 24px rgba(0,0,0,0.08); }
</style>

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-violet-600 via-purple-500 to-indigo-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-black text-white">تعديل المبالغ</h1>
            <p class="text-purple-100 text-sm mt-0.5">تعديل جماعي للمبلغ المقدر أو النهائي دون تغيير الدرجات</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-white font-black text-2xl leading-none">{{ $fmt($totalCount) }}</p>
                <p class="text-purple-200 text-xs mt-0.5">إجمالي النتائج</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-white font-black text-2xl leading-none">{{ $fmt($withAmount) }}</p>
                <p class="text-purple-200 text-xs mt-0.5">لديهم مقدر</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[140px]">
                <p class="text-white font-black text-xl leading-none">{{ $fmt($totalAmount) }}</p>
                <p class="text-purple-200 text-xs mt-0.5">مجموع المقدر (ل.س)</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                <p class="text-white font-black text-2xl leading-none">{{ $fmt($withFinalAmount) }}</p>
                <p class="text-purple-200 text-xs mt-0.5">لديهم نهائي</p>
            </div>
            <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[140px]">
                <p class="text-white font-black text-xl leading-none">{{ $fmt($totalFinalAmount) }}</p>
                <p class="text-purple-200 text-xs mt-0.5">مجموع النهائي (ل.س)</p>
            </div>
        </div>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
    <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- ===== FILTER FORM (GET) ===== --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <form method="GET" action="{{ route('members.bulk-amount') }}" id="ba-filter-form"
          onsubmit="removeEmptyBaFilters(this)">

        {{-- Always visible: search + filter toggle --}}
        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-gray-100">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="بحث بالاسم، رقم الهوية، الهاتف، أو رقم الملف..."
                       class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50
                              focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300">
            </div>
            <button type="button" onclick="toggleBaFilters()"
                    class="flex items-center gap-2 px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-violet-300 transition-colors text-sm font-bold text-gray-600 shrink-0">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                الفلاتر
                <svg id="ba-filter-toggle-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <span class="text-sm text-gray-500 shrink-0 hidden sm:block">
                <span class="text-xs text-gray-400">الفلاتر تؤثر على "تطبيق على الكل"</span>
            </span>
        </div>

        {{-- Collapsible filter body --}}
        <div id="ba-filter-body">
        <div class="p-5">

        {{-- Dossier + amount ranges --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-3">

            {{-- Dossier range --}}
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">رقم الاضبارة من</label>
                    <input type="text" name="dossier_from" value="{{ $dossierFrom }}"
                           placeholder="مثال: 100"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="text" name="dossier_to" value="{{ $dossierTo }}"
                           placeholder="مثال: 200"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
            </div>

            {{-- Estimated amount range --}}
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">المبلغ المقدر من</label>
                    <input type="number" name="estimated_from" value="{{ $estimatedFrom }}" min="0" step="any"
                           placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="estimated_to" value="{{ $estimatedTo }}" min="0" step="any"
                           placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">ل.س مقدر</span>
            </div>

            {{-- Final amount range --}}
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">المبلغ النهائي من</label>
                    <input type="number" name="final_from" value="{{ $finalFrom }}" min="0" step="any"
                           placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-gray-400 pb-2.5">—</span>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                    <input type="number" name="final_to" value="{{ $finalTo }}" min="0" step="any"
                           placeholder="∞"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
                <span class="text-xs text-gray-400 pb-2.5 shrink-0">ل.س نهائي</span>
            </div>

        </div>

        {{-- Filter row 1 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-3">

            {{-- Verification status --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">حالة التحقق</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
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
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $vs->color }}"></span>
                            {{ $vs->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Final status --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة النهائية</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
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
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $fs->color }}"></span>
                            {{ $fs->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Marital status --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة الاجتماعية</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
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
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $ms->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Gender --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجنس</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
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
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $g }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Association --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجمعية</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
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
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $assoc->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد جمعيات</p>
                    @endforelse
                </div>
            </div>

            {{-- Special cases --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالات الخاصة</label>
                <select name="special_cases" onwheel="this.blur()"
                        class="w-full text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1" {{ $specialCases === '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ $specialCases === '0' ? 'selected' : '' }}>لا</option>
                </select>
            </div>

            {{-- Has amount --}}
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المبلغ المقدر</label>
                <select name="has_amount" onwheel="this.blur()"
                        class="w-full text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1" {{ ($hasAmount ?? '') === '1' ? 'selected' : '' }}>لديهم مبلغ</option>
                    <option value="0" {{ ($hasAmount ?? '') === '0' ? 'selected' : '' }}>بدون مبلغ</option>
                </select>
            </div>

            {{-- Network --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">نوع الشبكة</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
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
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $net }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Region --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المنطقة</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400" placeholder="بحث في المناطق...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    @forelse($regionList as $reg)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="region_id[]" value="{{ $reg->id }}"
                                   {{ in_array($reg->id, $regionIds) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            <svg class="w-3.5 h-3.5 text-violet-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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

            {{-- Delegate --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المندوب</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
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
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $d }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا يوجد مندوبون</p>
                    @endforelse
                </div>
            </div>

            {{-- Second person --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">الفرد الثاني</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($secondPersonList as $sp)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="second_person[]" value="{{ $sp }}"
                                   {{ in_array($sp, $secondPersons) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $sp }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا يوجد أفراد ثانيون</p>
                    @endforelse
                </div>
            </div>

            {{-- Special cases description --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">وصف الحالة الخاصة</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400" placeholder="بحث في الأوصاف...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    @forelse($specialDescriptionList as $sd)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="special_cases_description[]" value="{{ $sd }}"
                                   {{ in_array($sd, $specialDescriptions) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            <span class="truncate">{{ $sd }}</span>
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات خاصة مسجلة</p>
                    @endforelse
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">العنوان التفصيلي</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                    <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                        <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400" placeholder="بحث في العناوين...">
                    </div>
                    <div class="overflow-y-auto" style="max-height:200px">
                    @forelse($addressList as $addr)
                        <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="current_address[]" value="{{ $addr }}"
                                   {{ in_array($addr, $addresses) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            <span class="truncate">{{ $addr }}</span>
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد مناطق مسجلة</p>
                    @endforelse
                    </div>
                </div>
            </div>

            {{-- Housing Status --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">وضع السكن</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
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
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hs->color }}"></span>
                            {{ $hs->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد أوضاع سكن</p>
                    @endforelse
                </div>
            </div>

            {{-- Sham Cash --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">شام كاش</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-violet-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                    @foreach(['done' => 'تم', 'manual' => 'يدوي', 'none' => 'لا يوجد'] as $val => $lbl)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                            <input type="checkbox" name="sham_cash[]" value="{{ $val }}"
                                   {{ in_array($val, (array)($shamCash ?? [])) ? 'checked' : '' }}
                                   class="ms-check rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $lbl }}
                        </label>
                    @endforeach
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
                + ($fvNotes !== '' ? 1 : 0)
                + ($fvHasVideo !== '' ? 1 : 0) + ($fvHasSpecialCase !== '' ? 1 : 0);
        @endphp
        <div class="border border-indigo-100 rounded-2xl mb-4">
            <button type="button" onclick="toggleBaFvFilters()"
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
                <svg id="ba-fv-filter-arrow" class="w-4 h-4 text-indigo-400 transition-transform duration-200 {{ $hasFvFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="ba-fv-filter-body" class="{{ $hasFvFilters ? '' : 'hidden' }} px-5 pb-5 pt-4 bg-indigo-50/20 rounded-b-2xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">

                    {{-- حالة الجولة --}}
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

                    {{-- من أضاف الجولة --}}
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
                    class="flex items-center gap-2 bg-gradient-to-l from-violet-600 to-indigo-500 hover:from-violet-700 hover:to-indigo-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                تطبيق الفلاتر
            </button>
            @if($qs)
                <a href="{{ route('members.bulk-amount') }}"
                   class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
            @endif
            <span class="text-sm text-gray-500 ms-auto">
                <span class="font-bold text-violet-700">{{ $fmt($totalCount) }}</span> عضو في النتائج الحالية
            </span>
        </div>

        </div>{{-- /p-5 --}}
        </div>{{-- /ba-filter-body --}}
    </form>
</div>

{{-- ===== BULK ACTION FORM (POST) ===== --}}
<form method="POST"
      action="{{ route('members.bulk-amount.apply') }}{{ $qs ? '?' . $qs : '' }}"
      id="bulk-form">
    @csrf

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-32">

        {{-- Table header bar --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gradient-to-l from-violet-50 to-white">
            <div class="flex items-center gap-3">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="select-all-cb"
                           class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-400 cursor-pointer">
                    <span class="text-sm font-bold text-gray-700">تحديد الكل في الصفحة</span>
                </label>
                <span id="selected-badge"
                      class="hidden bg-violet-100 text-violet-700 text-xs font-bold rounded-full px-2.5 py-1">
                    0 محدد
                </span>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-3 py-1">
                صفحة {{ $members->currentPage() }} · {{ $fmt($members->total()) }} عضو
            </span>
        </div>

        @if($members->isEmpty())
            <div class="text-center py-20 text-gray-400 text-sm">
                لا توجد نتائج مطابقة. جرب تعديل الفلاتر.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/70 border-b border-gray-100">
                            <th class="px-4 py-3.5 w-10"></th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">رقم الملف</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الاسم</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">حالة التحقق</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5 hidden md:table-cell">الشبكة</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5 hidden md:table-cell">العنوان</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">المبلغ المقدر</th>
                            <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">المبلغ النهائي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($members as $member)
                        <tr class="hover:bg-violet-50/30 transition-colors group cursor-pointer member-row"
                            onclick="toggleRowCheck(this)">
                            <td class="px-4 py-3.5" onclick="event.stopPropagation()">
                                <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                                       class="member-cb w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-400 cursor-pointer">
                            </td>
                            <td class="px-4 py-3.5 font-mono font-semibold text-gray-700 text-sm">
                                {{ $member->dossier_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5 font-bold text-gray-800">{{ $member->full_name }}</td>
                            <td class="px-4 py-3.5">
                                @if($member->verificationStatus)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border"
                                          style="background:{{ $member->verificationStatus->color }}18; color:{{ $member->verificationStatus->color }}; border-color:{{ $member->verificationStatus->color }}40">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:{{ $member->verificationStatus->color }}"></span>
                                        {{ $member->verificationStatus->name }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                @if($member->network)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-700 border border-cyan-100">
                                        {{ $member->network }}
                                    </span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-gray-500 text-sm hidden md:table-cell max-w-[160px] truncate">
                                {{ $member->current_address ?: '—' }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if($member->estimated_amount)
                                    <span class="inline-flex items-center gap-1 text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-2.5 py-1">
                                        {{ $fmt($member->estimated_amount) }}
                                        <span class="text-xs font-normal text-emerald-500">ل.س</span>
                                    </span>
                                @else
                                    <span class="text-sm text-gray-300 italic">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if($member->final_amount)
                                    <span class="inline-flex items-center gap-1 text-sm font-bold text-purple-700 bg-purple-50 border border-purple-100 rounded-lg px-2.5 py-1">
                                        {{ $fmt($member->final_amount) }}
                                        <span class="text-xs font-normal text-purple-400">ل.س</span>
                                    </span>
                                @else
                                    <span class="text-sm text-gray-300 italic">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $members->links() }}
            </div>
        @endif
    </div>

    {{-- ===== STICKY ACTION BAR ===== --}}
    <div id="action-bar"
         class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center gap-4 flex-wrap">

            {{-- Selection info --}}
            <div class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 leading-none">محدد</p>
                    <p class="font-black text-gray-800 text-lg leading-tight">
                        <span id="action-selected-count">0</span>
                        <span class="text-xs font-normal text-gray-400">/ {{ $members->count() }} في الصفحة</span>
                    </p>
                </div>
            </div>

            <div class="h-8 w-px bg-gray-200 hidden sm:block shrink-0"></div>

            {{-- Field --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 shrink-0">
                @foreach(['estimated_amount' => ['label' => 'المقدر', 'color' => 'emerald'], 'final_amount' => ['label' => 'النهائي', 'color' => 'purple']] as $fld => $fmeta)
                <label class="field-label flex items-center cursor-pointer">
                    <input type="radio" name="field" value="{{ $fld }}"
                           class="sr-only peer" {{ $fld === 'estimated_amount' ? 'checked' : '' }}>
                    <span class="px-3 py-1.5 rounded-lg text-sm font-semibold text-gray-500
                                 peer-checked:bg-white peer-checked:text-{{ $fmeta['color'] }}-700 peer-checked:shadow-sm
                                 hover:text-gray-700 transition-all select-none">
                        {{ $fmeta['label'] }}
                    </span>
                </label>
                @endforeach
            </div>

            <div class="h-8 w-px bg-gray-200 hidden sm:block shrink-0"></div>

            {{-- Operation --}}
            <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 shrink-0">
                @foreach(['add' => ['label' => '＋ إضافة', 'color' => 'emerald'], 'subtract' => ['label' => '− طرح', 'color' => 'red'], 'set' => ['label' => '= تعيين', 'color' => 'blue']] as $op => $meta)
                <label class="operation-label flex items-center cursor-pointer">
                    <input type="radio" name="operation" value="{{ $op }}"
                           class="sr-only peer" {{ $op === 'add' ? 'checked' : '' }}>
                    <span class="px-3 py-1.5 rounded-lg text-sm font-semibold text-gray-500
                                 peer-checked:bg-white peer-checked:text-{{ $meta['color'] }}-700 peer-checked:shadow-sm
                                 hover:text-gray-700 transition-all select-none">
                        {{ $meta['label'] }}
                    </span>
                </label>
                @endforeach
            </div>

            {{-- Amount --}}
            <div class="flex items-center gap-2 flex-1 min-w-[180px] max-w-xs">
                <div class="relative flex-1">
                    <input type="number" name="amount" id="amount-input" min="0" step="1" placeholder="0"
                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-base font-bold text-gray-800
                                  focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-violet-400 transition text-center">
                </div>
                <span class="text-sm text-gray-400 font-medium shrink-0">ل.س</span>
            </div>

            <div class="h-8 w-px bg-gray-200 hidden sm:block shrink-0"></div>

            {{-- Action Buttons --}}
            <div class="flex gap-2 flex-wrap">
                {{-- Apply to selected --}}
                <button type="submit" name="apply_to" value="selected"
                        id="apply-selected-btn"
                        onclick="return confirmApply('selected')"
                        class="flex items-center gap-2 bg-violet-600 hover:bg-violet-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed
                               text-white font-bold px-5 py-2.5 rounded-xl transition-colors text-sm shadow-sm"
                        disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    تطبيق على المحدد
                    <span id="apply-selected-count" class="bg-white/20 rounded-full px-1.5 py-0.5 text-xs">0</span>
                </button>

                {{-- Apply to all filtered --}}
                <button type="submit" name="apply_to" value="filtered"
                        onclick="return confirmApply('filtered')"
                        class="flex items-center gap-2 bg-indigo-500 hover:bg-indigo-600
                               text-white font-bold px-5 py-2.5 rounded-xl transition-colors text-sm shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    تطبيق على الكل
                    <span class="bg-white/20 rounded-full px-1.5 py-0.5 text-xs">{{ $fmt($totalCount) }}</span>
                </button>
            </div>

        </div>
    </div>

</form>

<script>
// ── Filter toggle ──────────────────────────────────────────────────────

function removeEmptyBaFilters(form) {
    Array.from(form.elements).forEach(function(el) {
        if (!el.name) return;
        if (el.type === 'checkbox' || el.type === 'radio') return;
        if (el.value === '' || el.value === null) el.disabled = true;
    });
}

function toggleBaFilters() {
    var body  = document.getElementById('ba-filter-body');
    var arrow = document.getElementById('ba-filter-toggle-arrow');
    var hidden = body.style.display === 'none';
    body.style.display    = hidden ? '' : 'none';
    arrow.style.transform = hidden ? '' : 'rotate(-90deg)';
    localStorage.setItem('baFiltersHidden', hidden ? '0' : '1');
}

function toggleBaFvFilters() {
    var body  = document.getElementById('ba-fv-filter-body');
    var arrow = document.getElementById('ba-fv-filter-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

document.addEventListener('DOMContentLoaded', function () {
    // Default closed unless user explicitly opened
    var body  = document.getElementById('ba-filter-body');
    var arrow = document.getElementById('ba-filter-toggle-arrow');
    if (localStorage.getItem('baFiltersHidden') === '0') {
        // keep open
    } else {
        if (body)  body.style.display    = 'none';
        if (arrow) arrow.style.transform = 'rotate(-90deg)';
    }

    // ms-dropdown logic
    document.querySelectorAll('.ms-dropdown').forEach(function (dropdown) {
        var btn   = dropdown.querySelector('.ms-btn');
        var panel = dropdown.querySelector('.ms-panel');
        var label = dropdown.querySelector('.ms-label');
        var arrow = dropdown.querySelector('.ms-arrow');

        function updateLabel() {
            var checked = dropdown.querySelectorAll('.ms-check:checked');
            if (checked.length === 0) {
                label.textContent = '— الكل —';
                label.classList.remove('text-violet-700', 'font-semibold');
                label.classList.add('text-gray-500');
            } else {
                label.textContent = checked.length + ' محدد';
                label.classList.add('text-violet-700', 'font-semibold');
                label.classList.remove('text-gray-500');
            }
        }
        updateLabel();

        panel.querySelectorAll('label').forEach(function (lbl) {
            lbl.classList.add('ms-option');
        });

        var allOptions = panel.querySelectorAll('.ms-option');
        var searchInput = null;
        if (allOptions.length >= 4) {
            var stickyHeader = document.createElement('div');
            stickyHeader.className = 'sticky top-0 bg-white z-10 px-2 pt-2 pb-1 border-b border-gray-100';
            searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'ابحث...';
            searchInput.className = 'ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-violet-400 bg-gray-50';
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

        var checks = dropdown.querySelectorAll('.ms-check');
        if (checks.length >= 2) {
            var saBtn = document.createElement('button');
            saBtn.type = 'button';
            saBtn.className = 'w-full text-right text-xs text-violet-600 font-semibold px-3 py-1.5 hover:bg-violet-50 transition flex items-center gap-1';
            saBtn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span class="sa-text">تحديد الكل</span>';
            function refreshSaBtn() {
                saBtn.querySelector('.sa-text').textContent =
                    dropdown.querySelectorAll('.ms-check:checked').length === checks.length ? 'إلغاء التحديد' : 'تحديد الكل';
            }
            saBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var shouldCheck = dropdown.querySelectorAll('.ms-check:checked').length < checks.length;
                checks.forEach(function (cb) { cb.checked = shouldCheck; });
                updateLabel(); refreshSaBtn();
            });
            checks.forEach(function (cb) { cb.addEventListener('change', refreshSaBtn); });
            refreshSaBtn();
            var searchEl = panel.querySelector('.ms-search');
            if (searchEl) {
                saBtn.classList.add('mt-1', 'border-t', 'border-gray-100');
                searchEl.parentElement.appendChild(saBtn);
            } else {
                saBtn.classList.add('border-b', 'border-gray-100', 'mb-1');
                panel.insertBefore(saBtn, panel.firstChild);
            }
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = !panel.classList.contains('hidden');
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
});

// ── Checkbox Logic ─────────────────────────────────────────────────────

var selectedCount = 0;

function updateUI() {
    selectedCount = document.querySelectorAll('.member-cb:checked').length;
    var total     = document.querySelectorAll('.member-cb').length;

    document.getElementById('selected-badge').textContent = selectedCount + ' محدد';
    document.getElementById('selected-badge').classList.toggle('hidden', selectedCount === 0);

    document.getElementById('action-selected-count').textContent = selectedCount;
    document.getElementById('apply-selected-count').textContent  = selectedCount;

    var applyBtn = document.getElementById('apply-selected-btn');
    applyBtn.disabled = selectedCount === 0;

    // highlight selected rows
    document.querySelectorAll('.member-row').forEach(function(row) {
        var cb = row.querySelector('.member-cb');
        row.classList.toggle('bg-violet-50', cb && cb.checked);
    });

    // update select-all state
    var selectAll = document.getElementById('select-all-cb');
    selectAll.indeterminate = selectedCount > 0 && selectedCount < total;
    selectAll.checked = total > 0 && selectedCount === total;
}

function toggleRowCheck(row) {
    var cb = row.querySelector('.member-cb');
    if (cb) { cb.checked = !cb.checked; updateUI(); }
}

document.getElementById('select-all-cb').addEventListener('change', function() {
    document.querySelectorAll('.member-cb').forEach(function(cb) {
        cb.checked = document.getElementById('select-all-cb').checked;
    });
    updateUI();
});

document.querySelectorAll('.member-cb').forEach(function(cb) {
    cb.addEventListener('change', updateUI);
});

// ── Confirm before apply ───────────────────────────────────────────────

function confirmApply(type) {
    var amount = document.getElementById('amount-input').value;
    if (!amount || parseFloat(amount) < 0) {
        alert('يرجى إدخال مبلغ صحيح.');
        return false;
    }

    var fldEl  = document.querySelector('input[name="field"]:checked');
    var fldMap = { estimated_amount: 'المبلغ المقدر', final_amount: 'المبلغ النهائي' };
    var fld    = fldEl ? (fldMap[fldEl.value] || fldEl.value) : '?';

    var opEl  = document.querySelector('input[name="operation"]:checked');
    var opMap = { add: 'إضافة', subtract: 'طرح', set: 'تعيين' };
    var op    = opEl ? opMap[opEl.value] : '?';
    var cnt   = type === 'selected' ? selectedCount : {{ $totalCount }};

    return confirm('هل أنت متأكد؟\n\nالحقل: ' + fld + '\nالعملية: ' + op + ' ' + parseFloat(amount).toLocaleString() + ' ل.س\nعدد الأعضاء: ' + cnt);
}

// init
updateUI();
</script>

@endsection
