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
        <a href="{{ route('sectors.index') }}"
           class="text-sm text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/30 px-4 py-2 rounded-xl transition-colors">
            رجوع
        </a>
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

    {{-- Current regions display --}}
    <div class="p-4">
        @if($sectorRegions->isEmpty())
            <p class="text-sm text-gray-400 text-center py-3">لا توجد مناطق مرتبطة بهذا القطاع حتى الآن.</p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach($sectorRegions as $region)
                    <span class="inline-flex items-center gap-1.5 text-sm font-medium px-3 py-1.5 rounded-full bg-violet-50 text-violet-700 border border-violet-200">
                        <svg class="w-3 h-3 text-violet-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                        {{ $region->name }}
                        <span class="text-xs text-violet-400">({{ $region->members_count ?? 0 }})</span>
                    </span>
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
<form method="GET" action="{{ route('sectors.show', $sector) }}" id="filter-form">
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden mb-5">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            <span class="text-sm font-bold text-gray-700">بحث وفلترة</span>
        </div>
        @if($hasFilters)
            <a href="{{ route('sectors.show', $sector) }}" class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                مسح الفلاتر
            </a>
        @endif
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

        {{-- Search --}}
        <div class="lg:col-span-2 relative">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="بحث بالاسم، رقم الاضبارة، الهاتف…"
                   class="w-full border border-gray-200 rounded-xl pr-9 pl-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 transition"
                   oninput="debounceSearch()">
        </div>

        {{-- Region filter --}}
        <div>
            <select name="region_id" onchange="this.form.submit()"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition {{ $regionId ? 'border-indigo-400 bg-indigo-50' : '' }}">
                <option value="">— كل المناطق —</option>
                @foreach($sectorRegions as $region)
                    <option value="{{ $region->id }}" {{ $regionId == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Verification status --}}
        <div>
            <select name="verification_status_id" onchange="this.form.submit()"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition {{ $vsId ? 'border-indigo-400 bg-indigo-50' : '' }}">
                <option value="">— كل حالات التحقق —</option>
                @foreach($verificationStatuses as $vs)
                    <option value="{{ $vs->id }}" {{ $vsId == $vs->id ? 'selected' : '' }}>{{ $vs->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Final status --}}
        <div>
            <select name="final_status_id" onchange="this.form.submit()"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-300 transition {{ $fsId ? 'border-indigo-400 bg-indigo-50' : '' }}">
                <option value="">— كل الحالات النهائية —</option>
                @foreach($finalStatuses as $fs)
                    <option value="{{ $fs->id }}" {{ $fsId == $fs->id ? 'selected' : '' }}>{{ $fs->name }}</option>
                @endforeach
            </select>
        </div>

    </div>

    @if($hasFilters)
    <div class="px-4 pb-3 flex flex-wrap gap-2">
        @if($search !== '')
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full">
                بحث: "{{ $search }}"
                <a href="{{ route('sectors.show', array_merge(['sector' => $sector->id], request()->except(['search','page']))) }}" class="text-indigo-400 hover:text-indigo-700">×</a>
            </span>
        @endif
        @if($regionId !== '')
            @php $rName = $sectorRegions->firstWhere('id', $regionId)?->name ?? $regionId; @endphp
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-violet-100 text-violet-700 px-3 py-1 rounded-full">
                المنطقة: {{ $rName }}
                <a href="{{ route('sectors.show', array_merge(['sector' => $sector->id], request()->except(['region_id','page']))) }}" class="text-violet-400 hover:text-violet-700">×</a>
            </span>
        @endif
        @if($vsId !== '')
            @php $vsName = $verificationStatuses->firstWhere('id', $vsId)?->name ?? $vsId; @endphp
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                التحقق: {{ $vsName }}
                <a href="{{ route('sectors.show', array_merge(['sector' => $sector->id], request()->except(['verification_status_id','page']))) }}" class="text-blue-400 hover:text-blue-700">×</a>
            </span>
        @endif
        @if($fsId !== '')
            @php $fsName = $finalStatuses->firstWhere('id', $fsId)?->name ?? $fsId; @endphp
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full">
                الحالة: {{ $fsName }}
                <a href="{{ route('sectors.show', array_merge(['sector' => $sector->id], request()->except(['final_status_id','page']))) }}" class="text-emerald-400 hover:text-emerald-700">×</a>
            </span>
        @endif
    </div>
    @endif
</div>
</form>

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
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox" id="check-all" onchange="toggleAll(this)"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400 cursor-pointer">
                        </th>
                        <th class="font-semibold text-gray-500 px-4 py-3">رقم الاضبارة</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">الاسم الكامل</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">المنطقة</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">حالة التحقق</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">الحالة النهائية</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">المبلغ المقدر</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                    <tr class="hover:bg-indigo-50/30 transition-colors member-row" data-id="{{ $member->id }}">
                        <td class="px-4 py-3.5">
                            <input type="checkbox" value="{{ $member->id }}" class="member-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400 cursor-pointer" onchange="updateCount()">
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 font-mono text-xs">{{ $member->dossier_number ?? '—' }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800">{{ $member->full_name }}</td>
                        <td class="px-4 py-3.5 text-gray-600">{{ $member->region?->name ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            @if($member->verificationStatus)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full border"
                                      style="color:{{ $member->verificationStatus->color }};border-color:{{ $member->verificationStatus->color }}40;background:{{ $member->verificationStatus->color }}15">
                                    {{ $member->verificationStatus->name }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($member->finalStatus)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full border"
                                      style="color:{{ $member->finalStatus->color }};border-color:{{ $member->finalStatus->color }}40;background:{{ $member->finalStatus->color }}15">
                                    {{ $member->finalStatus->name }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-gray-700">
                            {{ $member->estimated_amount ? number_format($member->estimated_amount, 0) . ' ل.س' : '—' }}
                        </td>
                        <td class="px-4 py-3.5">
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

// ── Search debounce ───────────────────────────────────────────────────────────
let _searchTimer = null;
function debounceSearch() {
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(() => document.getElementById('filter-form').submit(), 450);
}

// ── Members bulk ──────────────────────────────────────────────────────────────
function getChecked() {
    return [...document.querySelectorAll('.member-check:checked')].map(c => c.value);
}

function updateCount() {
    const ids = getChecked();
    const el = document.getElementById('selected-count');
    el.textContent = ids.length + ' محدد';
    el.classList.toggle('hidden', ids.length === 0);
    document.getElementById('check-all').indeterminate =
        ids.length > 0 && ids.length < document.querySelectorAll('.member-check').length;
    document.getElementById('check-all').checked =
        ids.length === document.querySelectorAll('.member-check').length;
}

function toggleAll(master) {
    document.querySelectorAll('.member-check').forEach(c => c.checked = master.checked);
    updateCount();
}

function selectAll() {
    document.querySelectorAll('.member-check').forEach(c => c.checked = true);
    document.getElementById('check-all').checked = true;
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
