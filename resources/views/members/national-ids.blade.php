@extends('layouts.app')

@section('title', 'دراسة أرقام الهوية — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="text-gray-500 hover:text-gray-700 transition">الأعضاء</a>
    <svg class="w-4 h-4 text-gray-400 mx-1 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    <span class="text-gray-700">دراسة أرقام الهوية</span>
@endsection

@section('content')

{{-- ══════════════ HEADER ══════════════ --}}
<div class="relative bg-gradient-to-l from-cyan-600 via-cyan-500 to-teal-600 rounded-3xl p-5 sm:p-6 mb-5 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute -top-6 -right-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 right-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 left-12 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-white">دراسة أرقام الهوية</h1>
                <p class="text-cyan-200 text-xs sm:text-sm mt-0.5">مراجعة وتحديث أرقام الهوية الوطنية للمستفيدين</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30 shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/>
                </svg>
            </div>
        </div>

        {{-- Stats row --}}
        @php $complete = $totalAll - $totalMissing; $pct = $totalAll ? round($complete / $totalAll * 100) : 0; @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3 mt-4">
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-3 border border-white/20 text-center">
                <p class="text-white/70 text-xs font-medium">ناقص هوية</p>
                <p class="text-white font-black text-xl sm:text-2xl mt-0.5">{{ number_format($totalMissing) }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-3 border border-white/20 text-center">
                <p class="text-white/70 text-xs font-medium">أرقام غير صحيحة</p>
                <p class="text-white font-black text-xl sm:text-2xl mt-0.5">{{ number_format($totalInvalid) }}</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-3 border border-white/20 text-center">
                <p class="text-white/70 text-xs font-medium">أرقام مكررة</p>
                <p class="text-white font-black text-xl sm:text-2xl mt-0.5">{{ number_format($totalDupMembers) }}</p>
                <p class="text-white/60 text-[10px]">{{ $totalDuplicates }} رقم متكرر</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-3 border border-white/20 text-center">
                <p class="text-white/70 text-xs font-medium">نسبة الاكتمال</p>
                <p class="text-white font-black text-xl sm:text-2xl mt-0.5">{{ $pct }}%</p>
                <p class="text-white/60 text-[10px]">{{ number_format($complete) }} / {{ number_format($totalAll) }}</p>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mt-3">
            <div class="bg-white/20 rounded-full h-2.5 overflow-hidden">
                <div class="bg-white rounded-full h-2.5 transition-all duration-700" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ IMPORT RESULT ══════════════ --}}
@if(session('import_results'))
@php $res = session('import_results'); @endphp
<div class="mb-5 bg-white rounded-2xl border shadow-sm overflow-hidden {{ count($res['errors']) ? 'border-amber-200' : 'border-emerald-200' }}">
    <div class="flex items-center gap-3 px-5 py-4 {{ count($res['errors']) ? 'bg-amber-50' : 'bg-emerald-50' }}">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ count($res['errors']) ? 'bg-amber-100' : 'bg-emerald-100' }}">
            <svg class="w-5 h-5 {{ count($res['errors']) ? 'text-amber-600' : 'text-emerald-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ count($res['errors']) ? 'M12 9v2m0 4h.01' : 'M5 13l4 4L19 7' }}"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold {{ count($res['errors']) ? 'text-amber-800' : 'text-emerald-800' }}">نتائج الاستيراد</p>
            <p class="text-xs {{ count($res['errors']) ? 'text-amber-600' : 'text-emerald-600' }}">
                تم تحديث <strong>{{ $res['updated'] }}</strong> سجل &nbsp;·&nbsp;
                تم تخطي <strong>{{ $res['skipped'] }}</strong> سطر
                @if(count($res['errors'])) &nbsp;·&nbsp; <strong>{{ count($res['errors']) }}</strong> خطأ @endif
            </p>
        </div>
    </div>
    @if(count($res['errors']))
    <div class="px-5 py-3 border-t border-amber-100 max-h-36 overflow-y-auto">
        @foreach($res['errors'] as $err)
            <p class="text-xs text-amber-700 py-0.5">• {{ $err }}</p>
        @endforeach
    </div>
    @endif
</div>
@endif

{{-- ══════════════ REGION STATS ══════════════ --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <button type="button" onclick="toggleRegionStats()"
            class="w-full flex items-center justify-between px-5 py-4 text-right">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-teal-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">إحصائيات حسب المنطقة</span>
            <span class="text-xs text-gray-400">({{ $regionStats->where('missing_members','>',0)->count() }} منطقة بها نقص)</span>
        </div>
        <svg id="region-stats-arrow" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div id="region-stats-body" class="hidden border-t border-gray-100">
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($regionStats as $rs)
            @php
                $rpct = $rs->total_members ? round(($rs->total_members - $rs->missing_members) / $rs->total_members * 100) : 100;
                $color = $rpct >= 80 ? 'emerald' : ($rpct >= 50 ? 'amber' : 'red');
                $barColors = ['emerald'=>'bg-emerald-400','amber'=>'bg-amber-400','red'=>'bg-red-400'];
                $textColors = ['emerald'=>'text-emerald-700','amber'=>'text-amber-600','red'=>'text-red-600'];
            @endphp
            <a href="{{ route('members.national-ids', ['filter'=>'missing','region_id'=>$rs->id]) }}"
               class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-cyan-200 hover:bg-cyan-50/30 transition-colors group">
                <div class="shrink-0 w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center">
                    <span class="text-sm font-black {{ $textColors[$color] }}">{{ $rpct }}%</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-gray-800 truncate group-hover:text-cyan-700 transition">{{ $rs->name }}</p>
                    <div class="mt-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        <div class="{{ $barColors[$color] }} h-1.5 rounded-full transition-all" style="width: {{ $rpct }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">ناقص: <span class="font-bold {{ $textColors[$color] }}">{{ $rs->missing_members }}</span> / {{ $rs->total_members }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ══════════════ FILTERS + TOOLS ══════════════ --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <form method="GET" action="{{ route('members.national-ids') }}" id="filter-form">

        {{-- Search row --}}
        <div class="px-4 pt-4 pb-3 space-y-2.5">
            <div class="relative">
                <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="بحث بالاسم أو رقم الملف أو رقم الهوية..."
                       class="w-full pr-10 pl-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50
                              focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:bg-white transition placeholder-gray-300">
            </div>

            {{-- Filter tabs + region + actions --}}
            <div class="flex items-center gap-2 flex-wrap">
                @php
                    $tabs = [
                        'missing'    => ['label' => 'ناقص ('    .$totalMissing.')',    'on' => 'bg-red-500 text-white border-red-500',    'off' => ''],
                        'invalid'    => ['label' => 'غير صحيح ('.$totalInvalid.')',    'on' => 'bg-amber-500 text-white border-amber-500', 'off' => ''],
                        'duplicates' => ['label' => 'مكرر ('    .$totalDupMembers.')', 'on' => 'bg-purple-600 text-white border-purple-600','off' => ''],
                        'all'        => ['label' => 'الكل ('    .$totalAll.')',        'on' => 'bg-gray-700 text-white border-gray-700',   'off' => ''],
                    ];
                @endphp
                @foreach($tabs as $val => $tab)
                <button type="button" onclick="setFilter('{{ $val }}')"
                        class="px-3.5 py-2 rounded-xl text-sm font-bold border transition-colors
                               {{ $filter === $val
                                  ? $tab['on'].' shadow-sm'
                                  : 'bg-gray-50 text-gray-500 border-gray-200 hover:border-gray-300 hover:bg-white' }}">
                    {{ $tab['label'] }}
                </button>
                @endforeach
                <input type="hidden" name="filter" id="filter-input" value="{{ $filter }}">

                <div class="flex-1 hidden sm:block"></div>

                {{-- Region --}}
                <select name="region_id" onchange="this.form.submit()"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-gray-50
                               focus:outline-none focus:ring-2 focus:ring-cyan-400 transition">
                    <option value="">كل المناطق</option>
                    @foreach($regions as $r)
                        <option value="{{ $r->id }}" {{ $region == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-bold rounded-xl transition-colors">
                    بحث
                </button>

                @if($search || $region || $filter !== 'missing')
                <a href="{{ route('members.national-ids') }}"
                   class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-500 text-sm font-semibold rounded-xl transition-colors">
                    مسح
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Action buttons --}}
    <div class="flex items-center gap-2 px-4 pb-4 flex-wrap border-t border-gray-100 pt-3">
        {{-- Export --}}
        <a href="{{ route('members.national-ids.export', array_filter(['filter'=>$filter,'region_id'=>$region,'search'=>$search])) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            تصدير Excel
            <span class="text-emerald-200 text-xs font-normal">({{ number_format($members->total()) }} سجل)</span>
        </a>

        {{-- Import --}}
        <button type="button" onclick="document.getElementById('import-panel').classList.toggle('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            استيراد من Excel
        </button>

        <span class="text-xs text-gray-400 mr-auto hidden sm:block">
            اضغط Enter في أي حقل لحفظه والانتقال للتالي
        </span>
    </div>

    {{-- Import panel --}}
    <div id="import-panel" class="hidden border-t border-violet-100 bg-violet-50/40 p-4">
        <form method="POST" action="{{ route('members.national-ids.import') }}" enctype="multipart/form-data"
              class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
            @csrf
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="hidden" name="region_id" value="{{ $region }}">
            <div class="flex-1">
                <label class="block text-xs font-bold text-violet-700 mb-1.5">ملف Excel (xlsx, xls, csv)</label>
                <p class="text-xs text-gray-500 mb-2">
                    الملف يجب أن يحتوي أعمدة: <span class="font-mono bg-white px-1 rounded border border-gray-200">رقم الملف</span> ثم <span class="font-mono bg-yellow-50 px-1 rounded border border-yellow-200">رقم الهوية الجديد</span> في العمود الخامس (E).
                    يمكنك تصدير القائمة أولاً ثم ملء العمود E وإعادة رفعها.
                </p>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="w-full text-sm border border-violet-200 rounded-xl px-3 py-2.5 bg-white
                              focus:outline-none focus:ring-2 focus:ring-violet-400 file:ml-3 file:text-xs
                              file:font-bold file:bg-violet-100 file:text-violet-700 file:border-0
                              file:rounded-lg file:px-3 file:py-1.5 file:cursor-pointer">
            </div>
            <button type="submit"
                    class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-700
                           text-white text-sm font-bold rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                استيراد
            </button>
        </form>
    </div>
</div>

{{-- ══════════════ TOASTS ══════════════ --}}
<div id="save-toast" class="hidden fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-emerald-600 text-white text-sm font-bold px-5 py-2.5 rounded-2xl shadow-xl">
    ✓ تم حفظ رقم الهوية
</div>
<div id="error-toast" class="hidden fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-red-600 text-white text-sm font-bold px-5 py-2.5 rounded-2xl shadow-xl"></div>

{{-- ══════════════ LIST ══════════════ --}}
@php
    // Build a map of national_id → [member ids] for duplicate detection in the current page
    $dupNidMap = [];
    if ($filter === 'duplicates') {
        foreach ($members as $m) {
            if ($m->national_id) $dupNidMap[$m->national_id][] = $m->id;
        }
    }
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between gap-2.5 px-5 py-4 border-b border-gray-100 bg-gradient-to-l from-gray-50 to-white">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-cyan-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">
                النتائج
                <span class="text-gray-400 font-medium">({{ number_format($members->total()) }})</span>
            </span>
            @if($filter === 'duplicates')
            <span class="text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200 px-2.5 py-1 rounded-full">
                مرتبة حسب الرقم المكرر
            </span>
            @endif
        </div>
        <span class="text-xs text-gray-400 shrink-0">الصفحة {{ $members->currentPage() }} / {{ $members->lastPage() }}</span>
    </div>

    @if($members->isEmpty())
        <div class="text-center py-16">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-gray-700 text-sm font-bold">لا توجد سجلات!</p>
            <p class="text-gray-400 text-xs mt-1">جميع المستفيدين في هذا الفلتر لديهم أرقام هوية صحيحة</p>
        </div>
    @else

    {{-- Desktop table --}}
    <div class="hidden sm:block overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/70 border-b border-gray-100">
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs w-20">الملف</th>
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">الاسم</th>
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">المنطقة</th>
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">رقم الهوية الحالي</th>
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs w-56">رقم الهوية الجديد</th>
                    <th class="px-5 py-3.5 w-24"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="members-tbody">
                @foreach($members as $member)
                @php
                    $hasId  = !empty($member->national_id);
                    $valid  = $hasId && strlen($member->national_id) === 11 && ctype_digit($member->national_id);
                    $status = !$hasId ? 'missing' : (!$valid ? 'invalid' : 'ok');
                    $isDup  = $filter === 'duplicates' && isset($dupNidMap[$member->national_id]) && count($dupNidMap[$member->national_id]) > 1;
                    $dupSiblings = $isDup ? collect($dupNidMap[$member->national_id])->filter(fn($id)=>$id!=$member->id)->count() : 0;
                @endphp
                <tr class="hover:bg-gray-50/50 transition-colors" id="row-{{ $member->id }}" data-order="{{ $loop->index }}">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('members.show', $member) }}" target="_blank"
                           class="text-xs font-black text-cyan-700 hover:underline font-mono">
                            {{ $member->dossier_number }}
                        </a>
                    </td>
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-gray-800 text-sm">{{ $member->full_name }}</p>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs text-gray-500">{{ $member->region?->name ?? '—' }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($status === 'missing')
                            <span class="inline-flex items-center gap-1 text-xs font-bold bg-red-50 text-red-500 border border-red-200 px-2.5 py-1 rounded-xl">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                غير موجود
                            </span>
                        @elseif($isDup)
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs font-mono text-purple-700 bg-purple-50 border border-purple-200 px-2.5 py-1 rounded-xl font-bold">{{ $member->national_id }}</span>
                                <span class="text-[10px] text-purple-500 bg-purple-50 border border-purple-100 px-1.5 py-0.5 rounded-lg font-bold">مكرر مع {{ $dupSiblings }} آخر</span>
                            </div>
                        @elseif($status === 'invalid')
                            <span class="inline-flex items-center gap-1 text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 px-2.5 py-1 rounded-xl font-mono">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                {{ $member->national_id }}
                            </span>
                        @else
                            <span class="text-xs font-mono text-gray-500 bg-gray-50 px-2.5 py-1 rounded-lg">{{ $member->national_id }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <input type="text" inputmode="numeric" maxlength="11"
                               id="nid-{{ $member->id }}"
                               data-member="{{ $member->id }}"
                               data-url="{{ route('members.national-id.update', $member) }}"
                               data-order="{{ $loop->index }}"
                               placeholder="11 رقم..."
                               class="nid-input w-full text-sm border rounded-xl px-3 py-2 font-mono
                                      border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cyan-400
                                      focus:bg-white transition placeholder-gray-300"
                               dir="ltr"
                               onkeydown="handleNidKeydown(event, this, {{ $member->id }})"
                               oninput="validateNidInput(this)">
                    </td>
                    <td class="px-5 py-3.5">
                        <button type="button"
                                id="btn-{{ $member->id }}"
                                onclick="saveMember({{ $member->id }})"
                                class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-xl
                                       bg-cyan-50 text-cyan-700 border border-cyan-200 hover:bg-cyan-100
                                       disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            حفظ
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden divide-y divide-gray-100">
        @foreach($members as $member)
        @php
            $hasId  = !empty($member->national_id);
            $valid  = $hasId && strlen($member->national_id) === 11 && ctype_digit($member->national_id);
            $status = !$hasId ? 'missing' : (!$valid ? 'invalid' : 'ok');
            $isDup  = $filter === 'duplicates' && isset($dupNidMap[$member->national_id]) && count($dupNidMap[$member->national_id]) > 1;
            $dupSiblings = $isDup ? collect($dupNidMap[$member->national_id])->filter(fn($id)=>$id!=$member->id)->count() : 0;
        @endphp
        <div class="px-4 py-4" id="mrow-{{ $member->id }}" data-order="{{ $loop->index }}">
            <div class="flex items-start justify-between gap-2 mb-2.5">
                <div class="min-w-0">
                    <a href="{{ route('members.show', $member) }}" target="_blank"
                       class="text-sm font-bold text-gray-800 hover:text-cyan-700 transition leading-snug block">
                        {{ $member->full_name }}
                    </a>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span class="text-xs font-mono font-black text-cyan-700">{{ $member->dossier_number }}</span>
                        @if($member->region?->name)
                            <span class="text-xs text-gray-400">· {{ $member->region->name }}</span>
                        @endif
                    </div>
                </div>
                @if($status === 'missing')
                    <span class="shrink-0 inline-flex items-center gap-1 text-xs font-bold bg-red-50 text-red-500 border border-red-200 px-2 py-0.5 rounded-lg">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        ناقص
                    </span>
                @elseif($isDup)
                    <span class="shrink-0 inline-flex items-center gap-1 text-xs font-bold bg-purple-50 text-purple-600 border border-purple-200 px-2 py-0.5 rounded-lg">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        مكرر
                    </span>
                @elseif($status === 'invalid')
                    <span class="shrink-0 inline-flex items-center gap-1 text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 px-2 py-0.5 rounded-lg">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        غير صحيح
                    </span>
                @endif
            </div>

            @if($hasId)
            <div class="mb-2.5 flex items-center gap-2">
                <span class="text-xs text-gray-400">الحالي:</span>
                <span class="text-xs font-mono {{ $isDup ? 'text-purple-700 font-bold' : ($valid ? 'text-gray-500' : 'text-amber-600 font-bold') }}">{{ $member->national_id }}</span>
                @if($isDup)
                    <span class="text-[10px] text-purple-500 font-bold">مكرر مع {{ $dupSiblings }} آخر</span>
                @endif
            </div>
            @endif

            <div class="flex items-center gap-2">
                <input type="text" inputmode="numeric" maxlength="11"
                       id="nid-m-{{ $member->id }}"
                       data-member="{{ $member->id }}"
                       data-url="{{ route('members.national-id.update', $member) }}"
                       data-order="{{ $loop->index }}"
                       placeholder="أدخل 11 رقماً..."
                       class="nid-input flex-1 text-sm border rounded-xl px-3 py-2.5 font-mono
                              border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cyan-400
                              focus:bg-white transition placeholder-gray-300"
                       dir="ltr"
                       onkeydown="handleNidKeydown(event, this, {{ $member->id }})"
                       oninput="validateNidInput(this)">
                <button type="button"
                        id="btn-m-{{ $member->id }}"
                        onclick="saveMember({{ $member->id }})"
                        class="shrink-0 inline-flex items-center gap-1.5 text-sm font-bold px-4 py-2.5 rounded-xl
                               bg-cyan-600 text-white hover:bg-cyan-700
                               disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    حفظ
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if($members->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $members->links() }}
        </div>
    @endif
    @endif
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ── Filter ──
function setFilter(val) {
    document.getElementById('filter-input').value = val;
    document.getElementById('filter-form').submit();
}

// ── Region stats toggle ──
function toggleRegionStats() {
    const body  = document.getElementById('region-stats-body');
    const arrow = document.getElementById('region-stats-arrow');
    body.classList.toggle('hidden');
    arrow.style.transform = body.classList.contains('hidden') ? '' : 'rotate(180deg)';
}

// ── Keyboard: Enter saves + moves to next row ──
function handleNidKeydown(e, input, memberId) {
    if (e.key === 'Enter') {
        e.preventDefault();
        saveMember(memberId, function() {
            // Move focus to next row's input
            const order   = parseInt(input.dataset.order ?? -1);
            const nextOrder = order + 1;
            const nextInput = document.querySelector(`[data-order="${nextOrder}"].nid-input`);
            if (nextInput) {
                nextInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => nextInput.focus(), 100);
            }
        });
    }
}

// ── Validate input ──
function validateNidInput(input) {
    input.value = input.value.replace(/\D/g, '').slice(0, 11);
    const len = input.value.length;
    if (len === 11) {
        input.classList.remove('border-gray-200', 'border-amber-300');
        input.classList.add('border-emerald-400', 'ring-1', 'ring-emerald-200');
    } else if (len > 0) {
        input.classList.remove('border-gray-200', 'border-emerald-400', 'ring-1', 'ring-emerald-200');
        input.classList.add('border-amber-300');
    } else {
        input.classList.remove('border-emerald-400', 'border-amber-300', 'ring-1', 'ring-emerald-200');
        input.classList.add('border-gray-200');
    }
}

// ── Save ──
function saveMember(memberId, onSuccess) {
    const desktopInput = document.getElementById('nid-' + memberId);
    const mobileInput  = document.getElementById('nid-m-' + memberId);
    const input = (desktopInput?.value.trim()) ? desktopInput : (mobileInput?.value.trim() ? mobileInput : desktopInput || mobileInput);
    if (!input) return;

    const val = input.value.trim();
    if (!val) { showError('يرجى إدخال رقم الهوية أولاً'); return; }
    if (!/^\d{11}$/.test(val)) { showError('رقم الهوية يجب أن يكون 11 رقماً بالضبط'); return; }

    const url  = input.dataset.url;
    const btn  = document.getElementById('btn-' + memberId);
    const btnM = document.getElementById('btn-m-' + memberId);

    const spinHTML  = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>';
    const saveHTML  = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> حفظ';
    const saveMHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> حفظ';

    [btn, btnM].filter(Boolean).forEach(b => { b.disabled = true; b.innerHTML = spinHTML; });
    [input, desktopInput, mobileInput].filter(Boolean).forEach(i => i.disabled = true);

    fetch(url, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: JSON.stringify({ national_id: val }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success !== false && !data.errors) {
            ['row-' + memberId, 'mrow-' + memberId].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.style.transition = 'opacity .4s'; el.style.opacity = '0.35'; }
            });
            showToast();
            setTimeout(() => {
                [desktopInput, mobileInput].filter(Boolean).forEach(i => { i.disabled = false; i.value = ''; });
                if (btn)  { btn.disabled = false; btn.innerHTML  = saveHTML; }
                if (btnM) { btnM.disabled = false; btnM.innerHTML = saveMHTML; }
                ['row-' + memberId, 'mrow-' + memberId].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.style.opacity = '1';
                });
                if (onSuccess) onSuccess();
            }, 800);
        } else {
            const msg = data.message || data.errors?.national_id?.[0] || 'حدث خطأ';
            showError(msg);
            restore();
        }
    })
    .catch(() => { showError('حدث خطأ في الاتصال'); restore(); });

    function restore() {
        [desktopInput, mobileInput].filter(Boolean).forEach(i => i && (i.disabled = false));
        if (btn)  { btn.disabled = false; btn.innerHTML  = saveHTML; }
        if (btnM) { btnM.disabled = false; btnM.innerHTML = saveMHTML; }
    }
}

function showToast() {
    const t = document.getElementById('save-toast');
    t.classList.remove('hidden'); t.style.opacity = '1';
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.classList.add('hidden'), 300); }, 2000);
}
function showError(msg) {
    const t = document.getElementById('error-toast');
    t.textContent = '✕ ' + msg;
    t.classList.remove('hidden'); t.style.opacity = '1';
    setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.classList.add('hidden'), 300); }, 3000);
}
</script>
@endpush

@endsection
