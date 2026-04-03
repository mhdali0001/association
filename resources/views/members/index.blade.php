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
        <div class="bg-white/15 border border-white/25 rounded-xl px-4 py-2.5 text-center min-w-[160px]">
            <p class="text-white font-black text-xl leading-none">{{ number_format((float)$totalAmount, 0, '.', ',') }}</p>
            <p class="text-emerald-200 text-xs mt-0.5">مجموع المبالغ المقدرة (ل.س)</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('members.import.show') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/30 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors backdrop-blur-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                استيراد Excel
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
    <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100">
        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-gray-700">الفلاتر والبحث</span>
    </div>

    <form method="GET" action="{{ route('members.index') }}" id="filter-form" class="p-5">

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
                <select name="special_cases"
                        class="w-full text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1" {{ $specialCases === '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ $specialCases === '0' ? 'selected' : '' }}>لا</option>
                </select>
            </div>

        </div>

        {{-- Filter row 2 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">

            {{-- Delegate multi-select --}}
            <div class="ms-dropdown relative">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المندوب الخارجي</label>
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
                        <p class="px-3 py-2 text-xs text-gray-400">لا يوجد مندوبون خارجيون</p>
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
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($specialDescriptionList as $sd)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
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
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المنطقة / العنوان</label>
                <button type="button"
                        class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                               hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($addressList as $addr)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
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
                $hasFilters = $search || $dossierFrom !== '' || $dossierTo !== '' || !empty($verificationIds) || !empty($finalStatusIds) || !empty($maritalStatuses) || !empty($genders) || !empty($delegates) || $specialCases !== '' || !empty($specialDescriptions) || !empty($addresses) || !empty($associationIds) || !empty($networks);
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
            <div class="flex items-center gap-1.5 flex-wrap mr-auto">
                @if($search)
                    <span class="inline-flex items-center gap-1 text-sm bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1 font-medium">
                        بحث: {{ Str::limit($search, 20) }}
                    </span>
                @endif
                @if($dossierFrom !== '' || $dossierTo !== '')
                    <span class="inline-flex items-center gap-1 text-sm bg-gray-50 text-gray-700 border border-gray-200 rounded-full px-3 py-1 font-medium font-mono">
                        اضبارة: {{ $dossierFrom ?: '…' }} — {{ $dossierTo ?: '…' }}
                    </span>
                @endif
                @foreach($verificationIds as $vid)
                    <span class="inline-flex items-center gap-1 text-sm bg-blue-50 text-blue-700 border border-blue-200 rounded-full px-3 py-1 font-medium">
                        {{ $verificationStatuses->firstWhere('id', $vid)?->name }}
                    </span>
                @endforeach
                @foreach($finalStatusIds as $fid)
                    <span class="inline-flex items-center gap-1 text-sm bg-slate-50 text-slate-700 border border-slate-200 rounded-full px-3 py-1 font-medium">
                        {{ $finalStatusList->firstWhere('id', $fid)?->name }}
                    </span>
                @endforeach
                @foreach($maritalStatuses as $ms)
                    <span class="inline-flex items-center gap-1 text-sm bg-purple-50 text-purple-700 border border-purple-200 rounded-full px-3 py-1 font-medium">{{ $ms }}</span>
                @endforeach
                @foreach($genders as $g)
                    <span class="inline-flex items-center gap-1 text-sm bg-orange-50 text-orange-700 border border-orange-200 rounded-full px-3 py-1 font-medium">{{ $g }}</span>
                @endforeach
                @foreach($delegates as $d)
                    <span class="inline-flex items-center gap-1 text-sm bg-teal-50 text-teal-700 border border-teal-200 rounded-full px-3 py-1 font-medium">{{ $d }}</span>
                @endforeach
                @if($specialCases !== '')
                    <span class="inline-flex items-center gap-1 text-sm bg-rose-50 text-rose-700 border border-rose-200 rounded-full px-3 py-1 font-medium">
                        حالات خاصة: {{ $specialCases === '1' ? 'نعم' : 'لا' }}
                    </span>
                @endif
                @foreach($specialDescriptions as $sd)
                    <span class="inline-flex items-center gap-1 text-sm bg-amber-50 text-amber-700 border border-amber-200 rounded-full px-3 py-1 font-medium max-w-[200px] truncate">{{ $sd }}</span>
                @endforeach
                @foreach($addresses as $addr)
                    <span class="inline-flex items-center gap-1 text-sm bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1 font-medium">{{ $addr }}</span>
                @endforeach
                @foreach($associationIds as $aid)
                    <span class="inline-flex items-center gap-1 text-sm bg-cyan-50 text-cyan-700 border border-cyan-200 rounded-full px-3 py-1 font-medium">
                        {{ $associationList->firstWhere('id', $aid)?->name }}
                    </span>
                @endforeach
                @foreach($networks as $net)
                    <span class="inline-flex items-center gap-1 text-sm bg-violet-50 text-violet-700 border border-violet-200 rounded-full px-3 py-1 font-medium">{{ $net }}</span>
                @endforeach
            </div>
            @endif
        </div>
    </form>
</div>

@push('scripts')
<script>
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
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">رقم الملف</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">العضو</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">رقم الهوية</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الهاتف</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">نوع الشبكة</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الحالة الاجتماعية</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">شام كاش</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">حالة التحقق</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">الحالة النهائية</th>
                        <th class="text-right font-semibold text-gray-500 text-sm px-4 py-3.5">المبلغ المقدر</th>
                        <th class="px-4 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                        @php
                            $sn       = $member->verificationStatus?->name ?? '';
                            $cash     = $member->sham_cash_account;

                            if (str_contains($sn, 'رفض')) {
                                $trClass = 'dark-row bg-gray-900 hover:bg-gray-800 divide-gray-700';
                            } elseif (str_contains($sn, 'طلب إلغاء')) {
                                $trClass = 'dark-row bg-slate-800 hover:bg-slate-700 divide-slate-600';
                            } elseif (str_contains($sn, 'تقييد')) {
                                $trClass = 'dark-row bg-indigo-900 hover:bg-indigo-800 divide-indigo-700';
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
                            <td class="px-4 py-4 text-gray-800 font-mono font-semibold text-sm">{{ $member->dossier_number ?? '—' }}</td>
                            <td class="px-4 py-4">
                                <span class="font-bold text-gray-900 text-base group-hover:text-emerald-700 transition-colors">
                                    {{ $member->full_name }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-gray-800 font-mono font-semibold text-sm">{{ $member->national_id ?? '—' }}</td>
                            <td class="px-4 py-4 text-gray-800 font-semibold text-sm">{{ $member->phone ?? '—' }}</td>
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
                                        $memberIban = trim($member->paymentInfo?->iban ?? '');
                                        $ibanDuplicated = $memberIban !== '' && isset($duplicateIbans[$memberIban]);
                                    @endphp
                                    @if($ibanDuplicated)
                                        <a href="{{ route('payment-review.duplicate-ibans', ['search' => $memberIban]) }}"
                                           title="آيبان مكرر: {{ $memberIban }}"
                                           class="inline-flex items-center gap-1 text-sm font-semibold text-red-700 bg-red-50 border border-red-300 rounded-full px-2.5 py-0.5 hover:bg-red-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                            نعم
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-0.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            نعم
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
                                @if($member->estimated_amount)
                                    <span class="inline-flex items-center gap-1 text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-2.5 py-1">
                                        {{ number_format($member->estimated_amount, 0) }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
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
tr.dark-row td a { color: rgba(255,255,255,0.9) !important; }
tr.dark-row td .text-gray-300 { color: rgba(255,255,255,0.3) !important; }
</style>

@endsection
