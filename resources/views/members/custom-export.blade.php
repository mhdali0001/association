@extends('layouts.app')

@section('title', 'تصدير مخصص')
@section('breadcrumb', 'تصدير مخصص')

@section('content')

@php
$groupIcons = [
    'بيانات أساسية'        => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    'معلومات التواصل'       => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
    'الموقع الجغرافي'      => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
    'المعلومات الاجتماعية' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    'الحالة الصحية'        => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    'الحالات الخاصة'       => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    'الحالة والتصنيف'      => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
    'النقاط والتقييم'      => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'المعلومات المالية'    => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
    'معلومات إدارية'       => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    'الجولة الميدانية'     => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z',
];
$groupColors = [
    'بيانات أساسية'        => ['bg'=>'bg-emerald-100','text'=>'text-emerald-700','check'=>'text-emerald-600','badge'=>'bg-emerald-600','btn'=>'text-emerald-600 hover:bg-emerald-50'],
    'معلومات التواصل'       => ['bg'=>'bg-sky-100',    'text'=>'text-sky-700',    'check'=>'text-sky-600',    'badge'=>'bg-sky-600',    'btn'=>'text-sky-600 hover:bg-sky-50'],
    'الموقع الجغرافي'      => ['bg'=>'bg-teal-100',   'text'=>'text-teal-700',   'check'=>'text-teal-600',   'badge'=>'bg-teal-600',   'btn'=>'text-teal-600 hover:bg-teal-50'],
    'المعلومات الاجتماعية' => ['bg'=>'bg-pink-100',   'text'=>'text-pink-700',   'check'=>'text-pink-600',   'badge'=>'bg-pink-600',   'btn'=>'text-pink-600 hover:bg-pink-50'],
    'الحالة الصحية'        => ['bg'=>'bg-red-100',    'text'=>'text-red-700',    'check'=>'text-red-600',    'badge'=>'bg-red-600',    'btn'=>'text-red-600 hover:bg-red-50'],
    'الحالات الخاصة'       => ['bg'=>'bg-amber-100',  'text'=>'text-amber-700',  'check'=>'text-amber-600',  'badge'=>'bg-amber-600',  'btn'=>'text-amber-600 hover:bg-amber-50'],
    'الحالة والتصنيف'      => ['bg'=>'bg-violet-100', 'text'=>'text-violet-700', 'check'=>'text-violet-600', 'badge'=>'bg-violet-600', 'btn'=>'text-violet-600 hover:bg-violet-50'],
    'النقاط والتقييم'      => ['bg'=>'bg-indigo-100', 'text'=>'text-indigo-700', 'check'=>'text-indigo-600', 'badge'=>'bg-indigo-600', 'btn'=>'text-indigo-600 hover:bg-indigo-50'],
    'المعلومات المالية'    => ['bg'=>'bg-orange-100', 'text'=>'text-orange-700', 'check'=>'text-orange-600', 'badge'=>'bg-orange-600', 'btn'=>'text-orange-600 hover:bg-orange-50'],
    'معلومات إدارية'       => ['bg'=>'bg-slate-100',  'text'=>'text-slate-700',  'check'=>'text-slate-600',  'badge'=>'bg-slate-600',  'btn'=>'text-slate-600 hover:bg-slate-50'],
    'الجولة الميدانية'     => ['bg'=>'bg-cyan-100',   'text'=>'text-cyan-700',   'check'=>'text-cyan-600',   'badge'=>'bg-cyan-600',   'btn'=>'text-cyan-600 hover:bg-cyan-50'],
];
@endphp

<form method="POST" action="{{ route('members.custom-export.download') }}" id="export-form">
@csrf

{{-- ══════════════════════════════════════════════════════
     MOBILE STICKY BOTTOM BAR
══════════════════════════════════════════════════════ --}}
<div class="lg:hidden fixed bottom-[64px] inset-x-0 z-40 bg-white border-t border-gray-100 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] px-4 py-3 flex items-center gap-3">
    <div class="flex-1 bg-emerald-50 border border-emerald-100 rounded-xl px-3 py-2 text-center">
        <span class="text-lg font-black text-emerald-700" id="mob-count">0</span>
        <span class="text-xs text-emerald-600 mr-1">عمود</span>
    </div>
    <button type="button" onclick="document.getElementById('mob-filters-panel').classList.toggle('hidden')"
            class="flex items-center gap-1.5 px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition-colors shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
        </svg>
        فلاتر
    </button>
    <button type="submit" id="mob-dl-btn" disabled
            class="flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-200 disabled:text-gray-400 text-white text-sm font-bold rounded-xl transition-colors shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        تصدير
    </button>
</div>

{{-- ══════════════════════════════════════════════════════
     MOBILE FILTERS PANEL (slide-up overlay)
══════════════════════════════════════════════════════ --}}
<div id="mob-filters-panel" class="lg:hidden hidden fixed inset-0 z-50 flex flex-col justify-end">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="document.getElementById('mob-filters-panel').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-t-2xl shadow-2xl max-h-[80vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 shrink-0">
            <h3 class="font-bold text-gray-800">فلاتر الأعضاء</h3>
            <button type="button" onclick="document.getElementById('mob-filters-panel').classList.add('hidden')"
                    class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 px-5 py-4 space-y-4">
            @include('members._custom-export-filters')
        </div>
        <div class="px-5 py-4 border-t border-gray-100 shrink-0">
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                تصدير Excel
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════════════════════ --}}
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-black text-gray-900">تصدير مخصص</h1>
        <p class="text-sm text-gray-500 mt-0.5">اختر الأعمدة التي تريد تصديرها</p>
    </div>
    <a href="{{ route('members.index') }}"
       class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span class="hidden sm:inline">العودة للأعضاء</span>
        <span class="sm:hidden">رجوع</span>
    </a>
</div>

{{-- ══════════════════════════════════════════════════════
     PRESETS (horizontal scroll on mobile)
══════════════════════════════════════════════════════ --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm px-4 py-3 mb-4 overflow-x-auto">
    <div class="flex items-center gap-2 min-w-max sm:min-w-0 sm:flex-wrap">
        <span class="text-xs font-bold text-gray-500 shrink-0">اختيار سريع:</span>
        <button type="button" onclick="applyPreset('basic')"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg hover:bg-emerald-100 transition-colors whitespace-nowrap shrink-0">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            البيانات الأساسية
        </button>
        <button type="button" onclick="applyPreset('contact')"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-sky-700 bg-sky-50 border border-sky-100 rounded-lg hover:bg-sky-100 transition-colors whitespace-nowrap shrink-0">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            التواصل
        </button>
        <button type="button" onclick="applyPreset('financial')"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-orange-700 bg-orange-50 border border-orange-100 rounded-lg hover:bg-orange-100 transition-colors whitespace-nowrap shrink-0">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            المالية
        </button>
        <button type="button" onclick="applyPreset('all')"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors whitespace-nowrap shrink-0">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            كل الأعمدة
        </button>
        <div class="w-px h-5 bg-gray-200 shrink-0 hidden sm:block"></div>
        <button type="button" onclick="selectAll()"
                class="px-3 py-1.5 text-xs font-bold text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors whitespace-nowrap shrink-0 border border-emerald-100">
            تحديد الكل
        </button>
        <button type="button" onclick="deselectAll()"
                class="px-3 py-1.5 text-xs font-bold text-gray-500 hover:bg-gray-50 rounded-lg transition-colors whitespace-nowrap shrink-0 border border-gray-100">
            إلغاء الكل
        </button>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     SEARCH + COUNT BAR
══════════════════════════════════════════════════════ --}}
<div class="flex items-center gap-3 mb-4">
    <div class="flex-1 relative">
        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
        </svg>
        <input type="text" id="col-search" placeholder="ابحث في الأعمدة..." oninput="filterColumns()"
               class="w-full text-sm border border-gray-200 rounded-xl pr-9 pl-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white placeholder-gray-400">
    </div>
    <span id="selected-count"
          class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-3 py-2.5 rounded-xl whitespace-nowrap shrink-0">
        0 محدد
    </span>
</div>

{{-- ══════════════════════════════════════════════════════
     MAIN LAYOUT
══════════════════════════════════════════════════════ --}}
<div class="flex flex-col xl:flex-row gap-5">

    {{-- ── Column Groups ── --}}
    <div class="flex-1 min-w-0 space-y-3 pb-32 lg:pb-0">

        @foreach($groups as $groupName => $cols)
        @php
            $color = $groupColors[$groupName] ?? ['bg'=>'bg-gray-100','text'=>'text-gray-700','check'=>'text-gray-600','badge'=>'bg-gray-600','btn'=>'text-gray-600 hover:bg-gray-50'];
            $icon  = $groupIcons[$groupName] ?? 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2';
            $gid   = 'grp-' . preg_replace('/[^a-z0-9]+/', '-', mb_strtolower($groupName));
        @endphp
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden col-group" data-group="{{ $groupName }}">

            {{-- Group header --}}
            <button type="button"
                    class="w-full flex items-center justify-between px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors text-right"
                    onclick="toggleGroup('{{ $gid }}')">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg {{ $color['bg'] }} flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <span class="font-bold text-gray-800 text-sm">{{ $groupName }}</span>
                    <span class="group-badge text-[10px] font-black text-white px-1.5 py-0.5 rounded-full {{ $color['badge'] }} hidden"
                          data-group-id="{{ $gid }}">0</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span onclick="event.stopPropagation(); selectGroup('{{ $gid }}')"
                          class="text-[11px] font-semibold px-2 py-1 rounded-md {{ $color['btn'] }} transition-colors">الكل</span>
                    <span onclick="event.stopPropagation(); deselectGroup('{{ $gid }}')"
                          class="text-[11px] font-semibold px-2 py-1 rounded-md text-gray-400 hover:bg-gray-100 transition-colors">لا</span>
                    <svg id="{{ $gid }}-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200 rotate-180 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>

            {{-- Columns grid --}}
            <div id="{{ $gid }}" class="p-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($cols as $key => $label)
                <label class="col-item flex items-center gap-2 p-2.5 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition-all"
                       data-label="{{ $label }}" data-group-id="{{ $gid }}">
                    <input type="checkbox" name="columns[]" value="{{ $key }}"
                           class="col-check w-4 h-4 rounded border-gray-300 {{ $color['check'] }} focus:ring-0 cursor-pointer shrink-0"
                           data-group-id="{{ $gid }}"
                           onchange="onCheckChange()">
                    <span class="text-xs text-gray-700 leading-snug font-medium">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach

    </div>

    {{-- ── Desktop Side Panel ── --}}
    <div class="hidden xl:block w-72 shrink-0">
        <div class="sticky top-[76px] space-y-4">

            {{-- Download card --}}
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-4 mb-4 text-center">
                    <p class="text-3xl font-black text-emerald-700" id="side-count">0</p>
                    <p class="text-xs text-emerald-600 mt-1">عمود محدد</p>
                </div>

                <button type="submit" id="dl-btn" disabled
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed text-white text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    تصدير Excel
                </button>
                <p class="text-[11px] text-gray-400 text-center mt-2">يصدر حسب الفلاتر أدناه</p>
            </div>

            {{-- Filters --}}
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                <p class="text-xs font-bold text-gray-500 mb-4 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    فلاتر الأعضاء
                </p>
                @include('members._custom-export-filters')
            </div>

        </div>
    </div>

</div>

</form>

@endsection

@push('scripts')
<script>
const PRESETS = {
    basic:     ['dossier_number','full_name','national_id','age','gender','mother_name','region','sector','marital_status','dependents_count'],
    contact:   ['dossier_number','full_name','phone','phone2','network','current_address','region'],
    financial: ['dossier_number','full_name','estimated_amount','payments_count','iban','barcode','recipient_name','payment_data_entry','sham_cash'],
    all:       null,
};

function applyPreset(name) {
    deselectAll();
    if (PRESETS[name] === null) { selectAll(); return; }
    PRESETS[name].forEach(function(key) {
        document.querySelectorAll('input[name="columns[]"][value="' + key + '"]').forEach(cb => { cb.checked = true; });
    });
    onCheckChange();
}

function selectAll()         { document.querySelectorAll('.col-check').forEach(cb => cb.checked = true);  onCheckChange(); }
function deselectAll()       { document.querySelectorAll('.col-check').forEach(cb => cb.checked = false); onCheckChange(); }
function selectGroup(gid)    { document.querySelectorAll('[data-group-id="'+gid+'"].col-check').forEach(cb => cb.checked = true);  onCheckChange(); }
function deselectGroup(gid)  { document.querySelectorAll('[data-group-id="'+gid+'"].col-check').forEach(cb => cb.checked = false); onCheckChange(); }

function onCheckChange() {
    const total = document.querySelectorAll('.col-check:checked').length;

    // counts
    ['selected-count'].forEach(id => { const el = document.getElementById(id); if (el) el.textContent = total + ' محدد'; });
    ['side-count','mob-count'].forEach(id => { const el = document.getElementById(id); if (el) el.textContent = total; });

    // buttons
    ['dl-btn','mob-dl-btn'].forEach(id => { const el = document.getElementById(id); if (el) el.disabled = total === 0; });

    // per-group badges
    document.querySelectorAll('.group-badge').forEach(function(badge) {
        const gid   = badge.dataset.groupId;
        const count = document.querySelectorAll('[data-group-id="' + gid + '"].col-check:checked').length;
        badge.textContent = count;
        badge.classList.toggle('hidden', count === 0);
    });

    // highlight items
    document.querySelectorAll('.col-item').forEach(function(item) {
        const cb = item.querySelector('.col-check');
        item.classList.toggle('border-emerald-200', cb.checked);
        item.classList.toggle('bg-emerald-50',      cb.checked);
        item.classList.toggle('border-gray-100',    !cb.checked);
    });
}

function toggleGroup(gid) {
    const body  = document.getElementById(gid);
    const arrow = document.getElementById(gid + '-arrow');
    if (!body) return;
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function filterColumns() {
    const q = document.getElementById('col-search').value.trim().toLowerCase();
    document.querySelectorAll('.col-item').forEach(function(item) {
        item.style.display = (!q || item.dataset.label.toLowerCase().includes(q)) ? '' : 'none';
    });
    if (q) {
        document.querySelectorAll('[id^="grp-"]').forEach(function(body) {
            body.classList.remove('hidden');
            const arrow = document.getElementById(body.id + '-arrow');
            if (arrow) arrow.classList.add('rotate-180');
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    applyPreset('basic');

    document.getElementById('export-form').addEventListener('submit', function(e) {
        if (document.querySelectorAll('.col-check:checked').length === 0) {
            e.preventDefault();
            alert('يرجى اختيار عمود واحد على الأقل.');
        }
    });
});
</script>
@endpush
