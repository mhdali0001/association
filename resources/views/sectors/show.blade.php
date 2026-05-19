@extends('layouts.app')

@section('title', $sector->name . ' — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('sectors.index') }}" class="hover:text-indigo-700 transition-colors">القطاعات</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">{{ $sector->name }}</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-indigo-600 via-violet-500 to-purple-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 border-2 border-white/40 flex items-center justify-center shadow-lg shrink-0">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white">{{ $sector->name }}</h1>
                <p class="text-indigo-100 text-sm mt-0.5">{{ $members->total() }} مستفيد في هذا القطاع</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('sectors.export-single', $sector) }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-white/90 hover:text-white bg-white/10 hover:bg-white/20 border border-white/30 px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                تصدير Excel
            </a>
            <a href="{{ route('sectors.index') }}"
               class="text-sm text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/30 px-4 py-2 rounded-xl transition-colors">
                رجوع
            </a>
        </div>
    </div>
</div>

{{-- Regions Management --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5 overflow-hidden">
    <div class="flex items-center justify-between bg-violet-50/60 border-b border-violet-100 px-5 py-3.5">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-violet-800">المناطق المرتبطة بالقطاع</span>
            <span class="text-xs text-violet-400 bg-violet-100 rounded-full px-2 py-0.5">{{ $sectorRegions->count() }} منطقة</span>
        </div>
        <button type="button" onclick="toggleRegionsPanel()"
                class="text-xs font-semibold text-violet-600 hover:text-violet-800 bg-violet-100 hover:bg-violet-200 px-3 py-1.5 rounded-lg transition-colors">
            تعديل المناطق
        </button>
    </div>

    {{-- Current regions display — clickable filter chips --}}
    <div class="p-4">
        @if($sectorRegions->isEmpty())
            <p class="text-sm text-gray-400 text-center py-3">لا توجد مناطق مرتبطة بهذا القطاع حتى الآن.</p>
        @else
            @php
                $baseQuery = array_diff_key(request()->query(), ['region_id' => null, 'page' => null]);
            @endphp
            <div class="flex flex-wrap gap-2 items-center">
                {{-- "الكل" chip --}}
                @php $noRegionFilter = empty($regionIds); @endphp
                <a href="{{ route('sectors.show', $sector) }}{{ $baseQuery ? '?' . http_build_query($baseQuery) : '' }}"
                   class="inline-flex items-center gap-1 text-sm font-semibold px-3 py-1.5 rounded-full border transition-colors
                          {{ $noRegionFilter ? 'bg-violet-600 text-white border-violet-600 shadow-sm' : 'bg-white text-violet-600 border-violet-300 hover:bg-violet-50' }}">
                    الكل
                </a>

                @foreach($sectorRegions as $region)
                    @php
                        $isActive = in_array($region->id, $regionIds);
                        $newRegionIds = $isActive
                            ? array_values(array_filter($regionIds, fn($id) => $id != $region->id))
                            : array_merge($regionIds, [$region->id]);
                        $filterQuery = $newRegionIds
                            ? array_merge($baseQuery, ['region_id' => $newRegionIds])
                            : $baseQuery;
                    @endphp
                    <a href="{{ route('sectors.show', $sector) }}{{ $filterQuery ? '?' . http_build_query($filterQuery) : '' }}"
                       class="inline-flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-full border transition-colors
                              {{ $isActive
                                  ? 'bg-violet-600 text-white border-violet-600 shadow-sm'
                                  : 'bg-violet-50 text-violet-700 border-violet-200 hover:bg-violet-100 hover:border-violet-400' }}">
                        <svg class="w-3 h-3 {{ $isActive ? 'text-violet-200' : 'text-violet-400' }}" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                        {{ $region->name }}
                        <span class="text-xs {{ $isActive ? 'text-violet-200' : 'text-violet-400' }}">({{ $region->members_count ?? 0 }})</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Edit panel --}}
    <div id="regions-panel" class="hidden border-t border-violet-100">
        <form method="POST" action="{{ route('sectors.update-regions', $sector) }}" id="regions-form">
            @csrf
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Assigned regions --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold text-violet-700 uppercase tracking-wider">مناطق هذا القطاع</p>
                            <button type="button" onclick="removeAllRegions()"
                                    class="text-xs text-red-400 hover:text-red-600 transition-colors">إزالة الكل</button>
                        </div>
                        <div id="assigned-list" class="space-y-1.5 min-h-16 p-3 rounded-xl border-2 border-dashed border-violet-200 bg-violet-50/30">
                            @foreach($sectorRegions as $region)
                            <div class="assigned-region flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-violet-100 shadow-sm"
                                 data-id="{{ $region->id }}" data-name="{{ $region->name }}">
                                <span class="text-sm font-medium text-gray-700">{{ $region->name }}</span>
                                <button type="button" onclick="moveToAvailable({{ $region->id }}, '{{ $region->name }}')"
                                        class="text-red-400 hover:text-red-600 transition-colors p-1 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Available regions --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">مناطق غير مرتبطة</p>
                            <button type="button" onclick="addAllRegions()"
                                    class="text-xs text-violet-600 hover:text-violet-800 transition-colors">إضافة الكل</button>
                        </div>
                        <div class="mb-2">
                            <input type="text" id="region-search" placeholder="بحث عن منطقة…" oninput="filterAvailable(this.value)"
                                   class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-300">
                        </div>
                        <div id="available-list" class="space-y-1.5 max-h-64 overflow-y-auto p-3 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/30">
                            @foreach($availableRegions as $region)
                            <div class="available-region flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-100 shadow-sm"
                                 data-id="{{ $region->id }}" data-name="{{ $region->name }}">
                                <span class="text-sm font-medium text-gray-600">{{ $region->name }}</span>
                                <button type="button" onclick="moveToAssigned({{ $region->id }}, '{{ $region->name }}')"
                                        class="text-violet-500 hover:text-violet-700 transition-colors p-1 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                            @if($availableRegions->isEmpty())
                            <p id="no-available" class="text-center text-xs text-gray-400 py-4">جميع المناطق مرتبطة بقطاعات</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Hidden inputs container --}}
                <div id="region-inputs"></div>

                <div class="flex gap-3 mt-4 pt-4 border-t border-violet-100">
                    <button type="submit" onclick="return prepareRegionForm()"
                            class="flex-1 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                        حفظ المناطق
                    </button>
                    <button type="button" onclick="toggleRegionsPanel()"
                            class="px-5 py-2.5 border border-gray-200 text-gray-500 hover:bg-gray-50 text-sm font-medium rounded-xl transition-colors">
                        إلغاء
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Bulk reassign form --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5 overflow-hidden">
    <div class="flex items-center gap-2 bg-indigo-50/50 border-b border-indigo-100 px-5 py-3">
        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        <span class="text-sm font-bold text-indigo-700">تعديل جماعي للمستفيدين</span>
    </div>
    <div class="p-4">
        <form id="bulk-form" method="POST" action="{{ route('members.bulk-update') }}" onsubmit="return submitBulk()">
            @csrf @method('PATCH')
            <input type="hidden" name="apply_fields[]" value="sector_id">
            <div id="ids-container"></div>

            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">نقل المحددين إلى قطاع</label>
                    <select name="fields[sector_id]"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        <option value="">— بدون قطاع (إزالة) —</option>
                        @foreach($allSectors as $s)
                            <option value="{{ $s->id }}" {{ $s->id === $sector->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="selectAll()" id="select-all-btn"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 border border-indigo-200 hover:bg-indigo-50 px-3 py-2.5 rounded-xl transition-colors">
                        تحديد الكل
                    </button>
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
                        تطبيق على المحددين
                    </button>
                </div>
            </div>
            <p id="bulk-hint" class="mt-2 text-xs text-gray-400">حدد مستفيدين من القائمة أدناه ثم اختر القطاع المراد النقل إليه.</p>
        </form>
    </div>
</div>

{{-- Search & Filters --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
<form method="GET" action="{{ route('sectors.show', $sector) }}" id="filter-form"
      onsubmit="removeEmptyFilters(this)">

    {{-- Search bar + toggle --}}
    <div class="px-5 pt-4 pb-3 border-b border-gray-100 space-y-2.5">
        <div class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="بحث بالاسم، رقم الهوية، الهاتف..."
                       class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50
                              focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition placeholder-gray-300">
            </div>
            <div class="relative w-full sm:w-44">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </span>
                <input type="text" name="dossier_search" value="{{ $dossierSearch }}"
                       placeholder="رقم الاضبارة..."
                       class="w-full pr-10 pl-4 py-3 text-base border border-gray-200 rounded-xl bg-gray-50
                              focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition placeholder-gray-300
                              {{ $dossierSearch ? 'border-indigo-400 bg-indigo-50/30' : '' }}">
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                بحث
            </button>
            <button type="button" onclick="toggleFilters()"
                    class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 hover:bg-white hover:border-indigo-300 transition-colors text-sm font-bold text-gray-600">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                الفلاتر
                <svg id="filter-toggle-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            @if($hasFilters)
                <a href="{{ route('sectors.show', $sector) }}"
                   class="flex items-center gap-1.5 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
                <span class="inline-flex items-center gap-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-sm font-bold px-4 py-2.5 rounded-xl">
                    {{ number_format($members->total()) }} نتيجة
                </span>
            @endif
        </div>
    </div>

    {{-- Collapsible filters --}}
    <div id="filter-body" class="{{ $hasFilters ? '' : 'hidden' }}">
    <div class="p-5">

    {{-- Dossier range --}}
    <div class="flex items-end gap-3 mb-3">
        <div class="flex-1 max-w-xs">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">رقم الاضبارة من</label>
            <input type="text" name="dossier_from" value="{{ $dossierFrom }}" placeholder="مثال: 100"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300 font-mono">
        </div>
        <span class="text-gray-400 pb-2.5">—</span>
        <div class="flex-1 max-w-xs">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
            <input type="text" name="dossier_to" value="{{ $dossierTo }}" placeholder="مثال: 200"
                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300 font-mono">
        </div>
    </div>

    {{-- Amount ranges --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">المبلغ المقدر من</label>
                <input type="number" name="estimated_from" value="{{ $estimatedFrom }}" min="0" step="any" placeholder="0"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300 font-mono">
            </div>
            <span class="text-gray-400 pb-2.5">—</span>
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                <input type="number" name="estimated_to" value="{{ $estimatedTo }}" min="0" step="any" placeholder="∞"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300 font-mono">
            </div>
            <span class="text-xs text-gray-400 pb-2.5 shrink-0">ل.س مقدر</span>
        </div>
        <div class="flex items-end gap-3">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">عدد الدفعات من</label>
                <input type="number" name="payments_count_from" value="{{ $paymentsCountFrom }}" min="0" step="1" placeholder="0"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300 font-mono">
            </div>
            <span class="text-gray-400 pb-2.5">—</span>
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">إلى</label>
                <input type="number" name="payments_count_to" value="{{ $paymentsCountTo }}" min="0" step="1" placeholder="∞"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300 font-mono">
            </div>
            <span class="text-xs text-gray-400 pb-2.5 shrink-0">دفعة</span>
        </div>
    </div>

    {{-- Filter row 1 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-3">

        {{-- Verification status --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">حالة التحقق</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="verification_status_id[]" value="none" {{ in_array('none', $verificationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون حالة
                </label>
                @foreach($verificationStatuses as $vs)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="verification_status_id[]" value="{{ $vs->id }}" {{ in_array($vs->id, $verificationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $vs->color }}"></span>
                        {{ $vs->name }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Final status --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة النهائية</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="final_status_id[]" value="none" {{ in_array('none', $finalStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @foreach($finalStatuses as $fs)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}" {{ in_array($fs->id, $finalStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $fs->color }}"></span>
                        {{ $fs->name }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Marital status --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">الحالة الاجتماعية</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="marital_status[]" value="none" {{ in_array('none', $maritalStatuses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @foreach($maritalStatusList as $ms)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="marital_status[]" value="{{ $ms->name }}" {{ in_array($ms->name, $maritalStatuses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        {{ $ms->name }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Gender --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجنس</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                @foreach(['ذكر', 'أنثى'] as $g)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="gender[]" value="{{ $g }}" {{ in_array($g, $genders) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        {{ $g }}
                    </label>
                @endforeach
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-t border-gray-100">
                    <input type="checkbox" name="gender[]" value="none" {{ in_array('none', $genders) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                    غير محدد
                </label>
            </div>
        </div>

        {{-- Association --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">الجمعية</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="association_id[]" value="none" {{ in_array('none', $associationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @forelse($associationList as $assoc)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="association_id[]" value="{{ $assoc->id }}" {{ in_array($assoc->id, $associationIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
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
                    class="w-full text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                <option value="">— الكل —</option>
                <option value="1" {{ $specialCases === '1' ? 'selected' : '' }}>نعم</option>
                <option value="0" {{ $specialCases === '0' ? 'selected' : '' }}>لا</option>
            </select>
        </div>

        {{-- Sham Cash --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">شام كاش</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                @foreach(['done' => 'تم', 'manual' => 'يدوي', 'none' => 'لا يوجد'] as $val => $lbl)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="sham_cash[]" value="{{ $val }}" {{ in_array($val, $shamCash) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        {{ $lbl }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Region (scoped to sector's regions) --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">المنطقة</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                    <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400" placeholder="بحث في المناطق...">
                </div>
                <div class="overflow-y-auto" style="max-height:200px">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="region_id[]" value="none" {{ in_array('none', $regionIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @forelse($sectorRegions as $reg)
                    <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="region_id[]" value="{{ $reg->id }}" {{ in_array($reg->id, $regionIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        <svg class="w-3.5 h-3.5 text-violet-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $reg->name }}
                    </label>
                @empty
                    <p class="px-3 py-2 text-sm text-gray-400">لا توجد مناطق في هذا القطاع</p>
                @endforelse
                </div>
            </div>
        </div>

        {{-- Housing status --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">وضع السكن</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                @forelse($housingStatusList as $hs)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="housing_status_id[]" value="{{ $hs->id }}" {{ in_array($hs->id, $housingStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hs->color }}"></span>
                        {{ $hs->name }}
                    </label>
                @empty
                    <p class="px-3 py-2 text-xs text-gray-400">لا توجد أوضاع سكن</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Filter row 2 --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">

        {{-- Delegate --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">المندوب</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="delegate[]" value="none" {{ in_array('none', $delegates) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @forelse($delegateList as $d)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="delegate[]" value="{{ $d }}" {{ in_array($d, $delegates) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
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
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="second_person[]" value="none" {{ in_array('none', $secondPersons) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @forelse($secondPersonList as $sp)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="second_person[]" value="{{ $sp }}" {{ in_array($sp, $secondPersons) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
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
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                    <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400" placeholder="بحث في الأوصاف...">
                </div>
                <div class="overflow-y-auto" style="max-height:200px">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="special_cases_description[]" value="none" {{ in_array('none', $specialDescriptions) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @forelse($specialDescriptionList as $sd)
                    <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="special_cases_description[]" value="{{ $sd }}" {{ in_array($sd, $specialDescriptions) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        <span class="truncate">{{ $sd }}</span>
                    </label>
                @empty
                    <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات خاصة مسجلة</p>
                @endforelse
                </div>
            </div>
        </div>

        {{-- Network --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">نوع الشبكة</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="network[]" value="none" {{ in_array('none', $networks) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @foreach(['MTN', 'SYRIATEL'] as $net)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="network[]" value="{{ $net }}" {{ in_array($net, $networks) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        {{ $net }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Payment data entry --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">اسم مدخل الدفع</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="payment_data_entry[]" value="none" {{ in_array('none', $paymentDataEntries) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @forelse($paymentDataEntryList as $pde)
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="payment_data_entry[]" value="{{ $pde }}" {{ in_array($pde, $paymentDataEntries) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        {{ $pde }}
                    </label>
                @empty
                    <p class="px-3 py-2 text-xs text-gray-400">لا توجد بيانات</p>
                @endforelse
            </div>
        </div>

        {{-- Address --}}
        <div class="ms-dropdown relative">
            <label class="block text-sm font-semibold text-gray-600 mb-1.5">العنوان التفصيلي</label>
            <button type="button" class="ms-btn w-full flex items-center justify-between text-base border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                <span class="ms-label text-gray-500 truncate">— الكل —</span>
                <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
                <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                    <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400" placeholder="بحث في العناوين...">
                </div>
                <div class="overflow-y-auto" style="max-height:200px">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="current_address[]" value="none" {{ in_array('none', $addresses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                    بدون
                </label>
                @forelse($addressList as $addr)
                    <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-base text-gray-700">
                        <input type="checkbox" name="current_address[]" value="{{ $addr }}" {{ in_array($addr, $addresses) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        <span class="truncate">{{ $addr }}</span>
                    </label>
                @empty
                    <p class="px-3 py-2 text-xs text-gray-400">لا توجد عناوين مسجلة</p>
                @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- Field Visit Filters --}}
    @php
        $fvActiveCount = (int)!empty($fieldVisitStatusIds) + (int)!empty($fvHouseTypeIds) + (int)!empty($fvHouseConditionIds)
            + (!empty($fvVisitors) ? 1 : 0) + (!empty($fvCreatedByIds) ? 1 : 0)
            + ($fvDateFrom !== '' || $fvDateTo !== '' ? 1 : 0)
            + ($fvAmountFrom !== '' || $fvAmountTo !== '' ? 1 : 0)
            + ($fvNotes !== '' ? 1 : 0) + ($fvHasVideo !== '' ? 1 : 0)
            + ($fvHasSpecialCase !== '' ? 1 : 0);
    @endphp
    <div class="border border-indigo-100 rounded-2xl mb-4">
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

                <div class="ms-dropdown relative">
                    <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة الجولة</label>
                    <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                        <span class="ms-label text-gray-500 truncate">— الكل —</span>
                        <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="field_visit_status_id[]" value="none" {{ in_array('none', $fieldVisitStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-gray-300"></span>
                            بدون جولة ميدانية
                        </label>
                        @forelse($fieldVisitStatuses as $fvs)
                            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                <input type="checkbox" name="field_visit_status_id[]" value="{{ $fvs->id }}" {{ in_array($fvs->id, $fieldVisitStatusIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $fvs->color }}"></span>
                                {{ $fvs->name }}
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
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $ht->color }}"></span>
                                {{ $ht->name }}
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
                            <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                                <input type="checkbox" name="fv_visitors[]" value="{{ $vis }}" {{ in_array($vis, $fvVisitors) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                {{ $vis }}
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
                                <input type="checkbox" name="fv_created_by[]" value="{{ $u->id }}" {{ in_array($u->id, $fvCreatedByIds) ? 'checked' : '' }} class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                {{ $u->name }}
                            </label>
                        @empty
                            <p class="px-3 py-2 text-sm text-gray-400">لا يوجد بيانات بعد</p>
                        @endforelse
                    </div>
                </div>

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

                <div>
                    <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">مبلغ الجولة (ل.س)</label>
                    <div class="flex items-center gap-1.5">
                        <input type="number" name="fv_amount_from" value="{{ $fvAmountFrom }}" placeholder="من" min="0"
                               class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                        <span class="text-xs text-indigo-400 shrink-0">—</span>
                        <input type="number" name="fv_amount_to" value="{{ $fvAmountTo }}" placeholder="إلى" min="0"
                               class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
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
                                <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hc->color }}"></span>
                                {{ $hc->name }}
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
                    <select name="fv_has_video" onwheel="this.blur()"
                            class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                        <option value="">— الكل —</option>
                        <option value="1" {{ $fvHasVideo === '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ $fvHasVideo === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1.5">حالة خاصة</label>
                    <select name="fv_has_special_case" onwheel="this.blur()"
                            class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                        <option value="">— الكل —</option>
                        <option value="1" {{ $fvHasSpecialCase === '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ $fvHasSpecialCase === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>

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

    </div>{{-- /p-5 --}}
    </div>{{-- /filter-body --}}

</form>
</div>

{{-- Members table --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between gap-2 px-5 py-3.5 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">
                المستفيدون
                <span class="text-indigo-600">({{ $members->total() }})</span>
                @if($hasFilters)
                    <span class="text-xs font-normal text-gray-400 mr-1">— نتائج مفلترة</span>
                @endif
            </span>
        </div>
        <span id="selected-count" class="text-xs text-indigo-600 font-semibold hidden">0 محدد</span>
    </div>

    @if($members->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <p class="text-gray-400 font-semibold">لا يوجد مستفيدون في هذا القطاع</p>
        </div>
    @else
        {{-- Mobile cards --}}
        <div class="block sm:hidden divide-y divide-gray-100">
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
                $latestVisit  = $member->fieldVisits->first();
                $memberFinal  = ($member->estimated_amount ?? 0) + ($latestVisit?->estimated_amount ?? 0);
                $memberIban     = trim($member->paymentInfo?->iban ?? '');
                $ibanDuplicated = $memberIban !== '' && isset($duplicateIbans[$memberIban]);
                $shamLabel      = $cash === 'manual' ? 'يدوي' : 'نعم';
                $shamBadgeClass = $cash === 'manual'
                    ? 'text-amber-700 bg-amber-50 border-amber-300'
                    : 'text-emerald-700 bg-emerald-50 border-emerald-200';
            @endphp
            <div class="member-row px-4 py-3.5 {{ $cardBg }}" data-id="{{ $member->id }}">

                {{-- Header: checkbox + name + view button --}}
                <div class="flex items-start gap-3 mb-2.5">
                    <input type="checkbox" value="{{ $member->id }}" class="member-check mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-400 cursor-pointer shrink-0" onchange="updateCount()">
                    <div class="flex-1 min-w-0 flex items-center justify-between gap-2">
                        <span class="font-bold text-gray-900 text-sm leading-snug">{{ $member->full_name }}</span>
                        <a href="{{ route('members.show', $member) }}"
                           class="shrink-0 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg px-2.5 py-1">
                            عرض
                        </a>
                    </div>
                </div>

                <div class="mr-7 space-y-2">

                    {{-- IDs + phone --}}
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                        @if($member->dossier_number)
                            <span class="font-mono font-semibold text-gray-700">{{ $member->dossier_number }}</span>
                        @endif
                        @if($member->national_id)
                            <span class="font-mono">{{ $member->national_id }}</span>
                        @endif
                        @if($member->phone)
                            <span>{{ $member->phone }}</span>
                        @endif
                    </div>

                    {{-- Region / Delegate / Second person --}}
                    @if($member->region || $member->delegate || $member->second_person)
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                        @if($member->region)
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3 text-violet-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <span class="font-medium text-gray-700">{{ $member->region->name }}</span>
                            </span>
                        @endif
                        @if($member->delegate)
                            <span class="text-gray-500">مندوب: <span class="font-medium text-gray-700">{{ $member->delegate }}</span></span>
                        @endif
                        @if($member->second_person)
                            <span class="text-gray-500">فرد 2: <span class="font-medium text-gray-700">{{ $member->second_person }}</span></span>
                        @endif
                    </div>
                    @endif

                    {{-- Marital status / Network / Housing --}}
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

                    {{-- Sham cash --}}
                    @if($cash)
                    <div>
                        @if($ibanDuplicated)
                            <a href="{{ route('payment-review.duplicate-ibans', ['search' => $memberIban]) }}"
                               title="آيبان مكرر: {{ $memberIban }}"
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
                                        class="text-xs font-semibold rounded-full px-2 py-0.5 border cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all"
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
                    <div class="flex flex-wrap items-center gap-2">
                        @if($member->estimated_amount)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg px-2 py-0.5">
                                مقدر: {{ number_format($member->estimated_amount, 0) }}
                            </span>
                        @endif
                        @if($memberFinal > 0)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-purple-700 bg-purple-50 border border-purple-100 rounded-lg px-2 py-0.5">
                                نهائي: {{ number_format($memberFinal, 0) }}
                            </span>
                        @endif
                        @if($member->payments_count !== null)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-sky-700 bg-sky-50 border border-sky-100 rounded-lg px-2 py-0.5">
                                {{ $member->payments_count }} دفعة
                            </span>
                        @endif
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        {{-- Desktop table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100">
                        <th class="w-10 px-4 py-3.5">
                            <input type="checkbox" id="check-all" onchange="toggleAll(this)"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400 cursor-pointer">
                        </th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">رقم الملف</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">العضو</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">رقم الهوية</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">الهاتف</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">المنطقة</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">المندوب</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">الفرد الثاني</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">وضع السكن</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">نوع الشبكة</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">الحالة الاجتماعية</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">شام كاش</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">حالة التحقق</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">الحالة النهائية</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">الجولة الميدانية</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">المبلغ المقدر</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">المبلغ النهائي</th>
                        <th class="font-semibold text-gray-500 text-sm px-4 py-3.5 text-right">الدفعات</th>
                        <th class="px-4 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                    @php
                        $sn   = $member->verificationStatus?->name ?? '';
                        $cash = $member->sham_cash_account;
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
                        $latestVisit  = $member->fieldVisits->first();
                        $memberFinal  = ($member->estimated_amount ?? 0) + ($latestVisit?->estimated_amount ?? 0);
                    @endphp
                    <tr class="transition-colors group {{ $trClass }}">
                        <td class="px-4 py-4">
                            <input type="checkbox" value="{{ $member->id }}" class="member-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400 cursor-pointer" onchange="updateCount()">
                        </td>
                        <td class="px-4 py-4 text-gray-800 font-mono font-semibold text-sm">{{ $member->dossier_number ?? '—' }}</td>
                        <td class="px-4 py-4">
                            <span class="font-bold text-gray-900 text-base group-hover:text-indigo-700 transition-colors">
                                {{ $member->full_name }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-gray-800 font-mono font-semibold text-sm">{{ $member->national_id ?? '—' }}</td>
                        <td class="px-4 py-4 text-gray-800 font-semibold text-sm">{{ $member->phone ?? '—' }}</td>
                        <td class="px-4 py-4 text-gray-700 text-sm">{{ $member->region?->name ?? '—' }}</td>
                        <td class="px-4 py-4 text-gray-700 text-sm">{{ $member->delegate ?? '—' }}</td>
                        <td class="px-4 py-4 text-gray-700 text-sm">{{ $member->second_person ?? '—' }}</td>
                        <td class="px-4 py-4">
                            @if($member->housingStatus)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border"
                                      style="background:{{ $member->housingStatus->color }}22;color:{{ $member->housingStatus->color }};border-color:{{ $member->housingStatus->color }}44">
                                    {{ $member->housingStatus->name }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
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
                                    $memberIban     = trim($member->paymentInfo?->iban ?? '');
                                    $ibanDuplicated = $memberIban !== '' && isset($duplicateIbans[$memberIban]);
                                    $shamLabel      = $member->sham_cash_account === 'manual' ? 'يدوي' : 'نعم';
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
                                      style="background:{{ $member->verificationStatus->color }}18;color:{{ $member->verificationStatus->color }};border-color:{{ $member->verificationStatus->color }}40">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $member->verificationStatus->color }}"></span>
                                    {{ $member->verificationStatus->name }}
                                </span>
                            @else
                                <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if(auth()->user()?->role === 'admin')
                                <form method="POST" action="{{ route('members.final-status.update', $member) }}" class="inline-block">
                                    @csrf @method('PATCH')
                                    <select name="final_status_id" onchange="this.form.submit()"
                                            class="text-sm font-semibold rounded-full px-2.5 py-1 border cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all"
                                            style="@if($member->finalStatus) background:{{ $member->finalStatus->color }}18;color:{{ $member->finalStatus->color }};border-color:{{ $member->finalStatus->color }}40 @else background:#f9fafb;color:#9ca3af;border-color:#e5e7eb @endif">
                                        <option value="">— بدون —</option>
                                        @foreach($finalStatusList as $fs)
                                            <option value="{{ $fs->id }}" {{ $member->final_status_id == $fs->id ? 'selected' : '' }}>{{ $fs->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                @if($member->finalStatus)
                                    <span class="text-sm font-semibold rounded-full px-2.5 py-1 border"
                                          style="background:{{ $member->finalStatus->color }}18;color:{{ $member->finalStatus->color }};border-color:{{ $member->finalStatus->color }}40">
                                        {{ $member->finalStatus->name }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-sm">—</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($latestVisit?->status)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold text-white"
                                      style="background:{{ $latestVisit->status->color }}">
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
                            @if($memberFinal > 0)
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-purple-700 bg-purple-50 border border-purple-100 rounded-lg px-2.5 py-1">
                                    {{ number_format($memberFinal, 0) }}
                                </span>
                            @else
                                <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($member->payments_count !== null)
                                <span class="inline-flex items-center gap-1 text-sm font-bold text-sky-700 bg-sky-50 border border-sky-100 rounded-lg px-2.5 py-1">
                                    {{ $member->payments_count }}
                                </span>
                            @else
                                <span class="text-gray-300 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <a href="{{ route('members.show', $member) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg px-2.5 py-1 transition-colors">
                                عرض
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $members->links() }}
            </div>
        @endif
    @endif
</div>

<script>
// ── Regions panel ─────────────────────────────────────────────────────────────
function toggleRegionsPanel() {
    const panel = document.getElementById('regions-panel');
    panel.classList.toggle('hidden');
    if (!panel.classList.contains('hidden')) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function moveToAssigned(id, name) {
    // Remove from available
    const avail = document.querySelector(`#available-list .available-region[data-id="${id}"]`);
    if (avail) avail.remove();

    // Add to assigned
    const div = document.createElement('div');
    div.className = 'assigned-region flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-violet-100 shadow-sm';
    div.dataset.id   = id;
    div.dataset.name = name;
    div.innerHTML = `
        <span class="text-sm font-medium text-gray-700">${name}</span>
        <button type="button" onclick="moveToAvailable(${id}, '${name}')"
                class="text-red-400 hover:text-red-600 transition-colors p-1 rounded">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    document.getElementById('assigned-list').appendChild(div);
}

function moveToAvailable(id, name) {
    // Remove from assigned
    const assigned = document.querySelector(`#assigned-list .assigned-region[data-id="${id}"]`);
    if (assigned) assigned.remove();

    // Add to available
    const noMsg = document.getElementById('no-available');
    if (noMsg) noMsg.remove();

    const div = document.createElement('div');
    div.className = 'available-region flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-100 shadow-sm';
    div.dataset.id   = id;
    div.dataset.name = name;
    div.innerHTML = `
        <span class="text-sm font-medium text-gray-600">${name}</span>
        <button type="button" onclick="moveToAssigned(${id}, '${name}')"
                class="text-violet-500 hover:text-violet-700 transition-colors p-1 rounded">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
        </button>`;
    document.getElementById('available-list').appendChild(div);
}

function addAllRegions() {
    document.querySelectorAll('#available-list .available-region').forEach(el => {
        moveToAssigned(el.dataset.id, el.dataset.name);
    });
}

function removeAllRegions() {
    document.querySelectorAll('#assigned-list .assigned-region').forEach(el => {
        moveToAvailable(el.dataset.id, el.dataset.name);
    });
}

function filterAvailable(q) {
    const term = q.trim().toLowerCase();
    document.querySelectorAll('#available-list .available-region').forEach(el => {
        el.style.display = el.dataset.name.toLowerCase().includes(term) ? '' : 'none';
    });
}

function prepareRegionForm() {
    const container = document.getElementById('region-inputs');
    container.innerHTML = '';
    document.querySelectorAll('#assigned-list .assigned-region').forEach(el => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'region_ids[]';
        inp.value = el.dataset.id;
        container.appendChild(inp);
    });
    return true;
}

// ── Filter panel toggle ────────────────────────────────────────────────────────
function toggleFilters() {
    const body  = document.getElementById('filter-body');
    const arrow = document.getElementById('filter-toggle-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function toggleFvFilters() {
    const body  = document.getElementById('fv-filter-body');
    const arrow = document.getElementById('fv-filter-arrow');
    body.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function removeEmptyFilters(form) {
    form.querySelectorAll('input[type=checkbox]:not(:checked)').forEach(cb => cb.disabled = true);
    form.querySelectorAll('input[type=text], input[type=number], input[type=date]').forEach(inp => {
        if (inp.value.trim() === '') inp.disabled = true;
    });
    form.querySelectorAll('select').forEach(sel => {
        if (sel.value === '') sel.disabled = true;
    });
    return true;
}

// ── Multi-select dropdowns ────────────────────────────────────────────────────
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
                label.classList.remove('text-indigo-700', 'font-semibold');
                label.classList.add('text-gray-500');
            } else {
                label.textContent = checked.length + ' محدد';
                label.classList.add('text-indigo-700', 'font-semibold');
                label.classList.remove('text-gray-500');
            }
        }

        updateLabel();

        panel.querySelectorAll('label').forEach(function (lbl) {
            lbl.classList.add('ms-option');
        });

        var allOptions  = panel.querySelectorAll('.ms-option');
        var searchInput = null;
        if (allOptions.length >= 4) {
            var stickyHeader = document.createElement('div');
            stickyHeader.className = 'sticky top-0 bg-white z-10 px-2 pt-2 pb-1 border-b border-gray-100';

            searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'ابحث...';
            searchInput.className = 'ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-gray-50';
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
            saBtn.className = 'w-full text-right text-xs text-indigo-600 font-semibold px-3 py-1.5 hover:bg-indigo-50 transition flex items-center gap-1';
            saBtn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span class="sa-text">تحديد الكل</span>';

            function refreshSaBtn() {
                const checkedCount = dropdown.querySelectorAll('.ms-check:checked').length;
                saBtn.querySelector('.sa-text').textContent = checkedCount === checks.length ? 'إلغاء التحديد' : 'تحديد الكل';
            }

            saBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const checkedCount = dropdown.querySelectorAll('.ms-check:checked').length;
                const shouldCheck  = checkedCount < checks.length;
                checks.forEach(function (cb) { cb.checked = shouldCheck; });
                updateLabel();
                refreshSaBtn();
            });

            checks.forEach(function (cb) { cb.addEventListener('change', refreshSaBtn); });
            refreshSaBtn();

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

// ── Members bulk ──────────────────────────────────────────────────────────────
function getChecked() {
    return [...new Set([...document.querySelectorAll('.member-check:checked')].map(c => c.value))];
}

function uniqueTotal() {
    return new Set([...document.querySelectorAll('.member-check')].map(c => c.value)).size;
}

function updateCount() {
    const ids = getChecked();
    const total = uniqueTotal();
    const el = document.getElementById('selected-count');
    el.textContent = ids.length + ' محدد';
    el.classList.toggle('hidden', ids.length === 0);
    const ca = document.getElementById('check-all');
    if (ca) {
        ca.indeterminate = ids.length > 0 && ids.length < total;
        ca.checked = ids.length > 0 && ids.length === total;
    }
}

function toggleAll(master) {
    document.querySelectorAll('.member-check').forEach(c => c.checked = master.checked);
    updateCount();
}

function selectAll() {
    document.querySelectorAll('.member-check').forEach(c => c.checked = true);
    const ca = document.getElementById('check-all');
    if (ca) ca.checked = true;
    updateCount();
}

function submitBulk() {
    const ids = getChecked();
    if (ids.length === 0) {
        alert('يرجى تحديد مستفيد واحد على الأقل.');
        return false;
    }
    const container = document.getElementById('ids-container');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'ids[]';
        inp.value = id;
        container.appendChild(inp);
    });
    return confirm('تطبيق التعديل على ' + ids.length + ' مستفيد؟');
}
</script>

@endsection
