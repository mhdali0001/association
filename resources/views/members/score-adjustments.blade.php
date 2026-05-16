@extends('layouts.app')

@section('title', 'تعديلات النقاط — مسالك النور')
@section('max-width', 'max-w-7xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-amber-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">تعديلات النقاط</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium px-4 py-3 rounded-xl">{{ session('success') }}</div>
@endif
@if(session('pending'))
    <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium px-4 py-3 rounded-xl">{{ session('pending') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 text-sm font-medium px-4 py-3 rounded-xl">{{ session('error') }}</div>
@endif

{{-- Hero stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="relative bg-gradient-to-l from-red-600 via-red-500 to-orange-500 rounded-3xl p-5 overflow-hidden shadow-lg">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-6 -left-6 w-28 h-28 bg-white rounded-full"></div>
            <div class="absolute -bottom-8 left-12 w-40 h-40 bg-white rounded-full"></div>
        </div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                    </svg>
                </div>
                <h2 class="text-white font-bold text-base">الانقاصات</h2>
            </div>
            <div class="flex gap-4">
                <div>
                    <p class="text-white font-black text-2xl leading-none">{{ number_format($totalDeductCount) }}</p>
                    <p class="text-red-200 text-xs mt-0.5">عضو</p>
                </div>
                <div class="border-r border-white/30 pr-4">
                    <p class="text-white font-black text-2xl leading-none">{{ number_format($totalDeduction, 1) }}</p>
                    <p class="text-orange-200 text-xs mt-0.5">مجموع النقاط</p>
                </div>
            </div>
        </div>
    </div>

    <div class="relative bg-gradient-to-l from-emerald-600 via-emerald-500 to-teal-500 rounded-3xl p-5 overflow-hidden shadow-lg">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-6 -left-6 w-28 h-28 bg-white rounded-full"></div>
            <div class="absolute -bottom-8 left-12 w-40 h-40 bg-white rounded-full"></div>
        </div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <h2 class="text-white font-bold text-base">الإضافات</h2>
            </div>
            <div class="flex gap-4">
                <div>
                    <p class="text-white font-black text-2xl leading-none">{{ number_format($totalAddCount) }}</p>
                    <p class="text-emerald-200 text-xs mt-0.5">عضو</p>
                </div>
                <div class="border-r border-white/30 pr-4">
                    <p class="text-white font-black text-2xl leading-none">{{ number_format($totalAddition, 1) }}</p>
                    <p class="text-teal-200 text-xs mt-0.5">مجموع النقاط</p>
                </div>
            </div>
        </div>
    </div>

    <div class="relative bg-gradient-to-l from-violet-600 via-violet-500 to-purple-500 rounded-3xl p-5 overflow-hidden shadow-lg">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-6 -left-6 w-28 h-28 bg-white rounded-full"></div>
            <div class="absolute -bottom-8 left-12 w-40 h-40 bg-white rounded-full"></div>
        </div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h2 class="text-white font-bold text-base">بحاجة إعادة حساب</h2>
            </div>
            <div class="flex gap-4">
                <div>
                    <p class="text-white font-black text-2xl leading-none">{{ number_format($recalcCount) }}</p>
                    <p class="text-violet-200 text-xs mt-0.5">عضو لديه نقاط سكن</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs + Filters --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5 overflow-hidden">
    <div class="flex border-b border-gray-100">
        <a href="{{ route('members.score-adjustments', array_merge(request()->only(['search', 'sort']), ['tab' => 'deductions'])) }}"
           class="flex items-center gap-2 px-6 py-3.5 text-sm font-bold border-b-2 transition-colors
                  {{ $tab === 'deductions' ? 'border-red-500 text-red-600 bg-red-50/50' : 'border-transparent text-gray-500 hover:text-red-500 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
            </svg>
            المنقوصة نقاطهم
            <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                         {{ $tab === 'deductions' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500' }}">
                {{ number_format($totalDeductCount) }}
            </span>
        </a>
        <a href="{{ route('members.score-adjustments', array_merge(request()->only(['search', 'sort']), ['tab' => 'additions'])) }}"
           class="flex items-center gap-2 px-6 py-3.5 text-sm font-bold border-b-2 transition-colors
                  {{ $tab === 'additions' ? 'border-emerald-500 text-emerald-600 bg-emerald-50/50' : 'border-transparent text-gray-500 hover:text-emerald-500 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            المضافة نقاطهم
            <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                         {{ $tab === 'additions' ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                {{ number_format($totalAddCount) }}
            </span>
        </a>
        <a href="{{ route('members.score-adjustments', array_merge(request()->only(['search', 'sort']), ['tab' => 'recalculate'])) }}"
           class="flex items-center gap-2 px-6 py-3.5 text-sm font-bold border-b-2 transition-colors
                  {{ $tab === 'recalculate' ? 'border-violet-500 text-violet-600 bg-violet-50/50' : 'border-transparent text-gray-500 hover:text-violet-500 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            إعادة الحساب
            <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                         {{ $tab === 'recalculate' ? 'bg-violet-100 text-violet-600' : 'bg-gray-100 text-gray-500' }}">
                {{ number_format($recalcCount) }}
            </span>
        </a>
    </div>

    <div class="p-5">
        <form method="GET" action="{{ route('members.score-adjustments') }}" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 mb-1">بحث (اسم / رقم اضبارة / هوية)</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="ابحث..."
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:outline-none bg-gray-50
                              {{ $tab === 'additions' ? 'focus:ring-emerald-300' : ($tab === 'recalculate' ? 'focus:ring-violet-300' : 'focus:ring-red-300') }}">
            </div>
            @if($tab === 'recalculate')
                <div class="min-w-[220px]">
                    <label class="block text-xs font-bold text-gray-500 mb-1">تصفية بالمكوّن</label>
                    <select name="component" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-violet-300 focus:outline-none bg-gray-50">
                        <option value="housing_score"          {{ $recalcComponent === 'housing_score'          ? 'selected' : '' }}>نقاط السكن (مجمّد)</option>
                        <option value="work_score"             {{ $recalcComponent === 'work_score'             ? 'selected' : '' }}>نقاط العمل</option>
                        <option value="dependents_score"       {{ $recalcComponent === 'dependents_score'       ? 'selected' : '' }}>نقاط المعالين</option>
                        <option value="dependent_status_score" {{ $recalcComponent === 'dependent_status_score' ? 'selected' : '' }}>نقاط الوضع الاجتماعي</option>
                        <option value="illness_score"          {{ $recalcComponent === 'illness_score'          ? 'selected' : '' }}>نقاط المرض</option>
                        <option value="special_cases_score"    {{ $recalcComponent === 'special_cases_score'    ? 'selected' : '' }}>نقاط الحالات الخاصة</option>
                    </select>
                </div>
            @else
                <div class="min-w-[200px]">
                    <label class="block text-xs font-bold text-gray-500 mb-1">
                        {{ $tab === 'additions' ? 'سبب الإضافة' : 'سبب الانقاص' }}
                    </label>
                    <input type="text" name="reason" value="{{ $reasonFilter }}"
                           placeholder="{{ $tab === 'additions' ? 'ابحث في سبب الإضافة...' : 'ابحث في سبب الانقاص...' }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:outline-none bg-gray-50
                                  {{ $tab === 'additions' ? 'focus:ring-emerald-300' : 'focus:ring-red-300' }}"
                           list="reason-list">
                    <datalist id="reason-list">
                        @foreach($reasonList as $r)
                            <option value="{{ $r }}">
                        @endforeach
                    </datalist>
                </div>
                @if($tab === 'deductions')
                <div class="min-w-[150px]">
                    <label class="block text-xs font-bold text-gray-500 mb-1">التاريخ من</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:outline-none bg-gray-50">
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-xs font-bold text-gray-500 mb-1">التاريخ إلى</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-300 focus:outline-none bg-gray-50">
                </div>
                @endif
            @endif
            <div class="min-w-[180px]">
                <label class="block text-xs font-bold text-gray-500 mb-1">الترتيب</label>
                <select name="sort" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:outline-none bg-gray-50
                                           {{ $tab === 'additions' ? 'focus:ring-emerald-300' : ($tab === 'recalculate' ? 'focus:ring-violet-300' : 'focus:ring-red-300') }}">
                    @if($tab === 'additions')
                        <option value="addition_desc" {{ $sortBy === 'addition_desc' ? 'selected' : '' }}>الإضافة (الأعلى أولاً)</option>
                        <option value="addition_asc"  {{ $sortBy === 'addition_asc'  ? 'selected' : '' }}>الإضافة (الأقل أولاً)</option>
                    @elseif($tab === 'recalculate')
                        <option value="housing_desc" {{ $sortBy === 'housing_desc' ? 'selected' : '' }}>نقاط السكن (الأعلى أولاً)</option>
                        <option value="housing_asc"  {{ $sortBy === 'housing_asc'  ? 'selected' : '' }}>نقاط السكن (الأقل أولاً)</option>
                        <option value="diff_desc"    {{ $sortBy === 'diff_desc'    ? 'selected' : '' }}>الفرق (الأعلى أولاً)</option>
                    @else
                        <option value="deduction_desc" {{ $sortBy === 'deduction_desc' ? 'selected' : '' }}>الانقاص (الأعلى أولاً)</option>
                        <option value="deduction_asc"  {{ $sortBy === 'deduction_asc'  ? 'selected' : '' }}>الانقاص (الأقل أولاً)</option>
                        <option value="date_desc"      {{ $sortBy === 'date_desc'      ? 'selected' : '' }}>التاريخ (الأحدث أولاً)</option>
                        <option value="date_asc"       {{ $sortBy === 'date_asc'       ? 'selected' : '' }}>التاريخ (الأقدم أولاً)</option>
                    @endif
                    <option value="name"    {{ $sortBy === 'name'    ? 'selected' : '' }}>الاسم</option>
                    <option value="dossier" {{ $sortBy === 'dossier' ? 'selected' : '' }}>رقم الاضبارة</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex items-center gap-1.5 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors
                               {{ $tab === 'additions' ? 'bg-emerald-600 hover:bg-emerald-700' : ($tab === 'recalculate' ? 'bg-violet-600 hover:bg-violet-700' : 'bg-red-600 hover:bg-red-700') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    بحث
                </button>
                @php $hasClear = $search || $reasonFilter || $dateFrom || $dateTo || ($tab === 'recalculate' && $recalcComponent !== 'housing_score'); @endphp
                @if($hasClear)
                <a href="{{ route('members.score-adjustments', ['tab' => $tab]) }}"
                   class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold px-4 py-2 rounded-xl transition-colors">
                    مسح
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
@if($members->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4
                    {{ $tab === 'additions' ? 'bg-emerald-50' : ($tab === 'recalculate' ? 'bg-violet-50' : 'bg-red-50') }}">
            <svg class="w-8 h-8 {{ $tab === 'additions' ? 'text-emerald-300' : ($tab === 'recalculate' ? 'text-violet-300' : 'text-red-300') }}"
                 fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                @if($tab === 'additions')
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                @elseif($tab === 'recalculate')
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                @endif
            </svg>
        </div>
        <p class="text-gray-400 text-sm font-medium">
            {{ $tab === 'additions' ? 'لا يوجد أعضاء مضافة نقاطهم' : ($tab === 'recalculate' ? 'لا يوجد أعضاء بحاجة إعادة حساب' : 'لا يوجد أعضاء منقوصة نقاطهم') }}
        </p>
    </div>
@else

@if($tab === 'recalculate')
{{-- ── Recalculate form ──────────────────────────────────── --}}
<form id="bulk-recalc-form" method="POST" action="{{ route('members.bulk-recalculate-score') }}">
    @csrf

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-violet-50/30">
        <p class="text-sm font-bold text-gray-600">
            {{ number_format($members->total()) }} عضو
            @if($search)
                <span class="text-xs text-gray-400 font-normal mr-1">(نتائج البحث)</span>
            @endif
        </p>
        <div class="flex items-center gap-3 flex-wrap">
            <p class="text-xs text-gray-400">صفحة {{ $members->currentPage() }} من {{ $members->lastPage() }}</p>
            <span class="text-xs bg-violet-100 text-violet-700 font-semibold px-2.5 py-1 rounded-lg">
                بدون نقاط السكن
            </span>
            <form method="POST" action="{{ route('members.bulk-recalculate-score') }}"
                  onsubmit="return confirm('هل أنت متأكد من إعادة حساب نقاط جميع الـ {{ $members->total() }} عضو في كل الصفحات؟\nلا يمكن التراجع عن هذا الإجراء.')">
                @csrf
                <input type="hidden" name="recalc_all" value="1">
                <input type="hidden" name="component"  value="{{ $recalcComponent }}">
                <input type="hidden" name="search"     value="{{ $search }}">
                <button type="submit"
                        class="flex items-center gap-1.5 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    إعادة حساب الكل ({{ number_format($members->total()) }})
                </button>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/30">
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" id="rc-select-all"
                               class="w-4 h-4 rounded border-gray-300 text-violet-600 cursor-pointer"
                               onchange="rcToggleAll(this.checked)">
                    </th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">الاسم</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">رقم الاضبارة</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">نقاط السكن</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">المجموع الحالي</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">المجموع الصحيح</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">الفرق</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($members as $member)
                @php
                    $currentTotal = (float)($member->total_score ?? $member->score ?? 0);
                    $correctTotal = (float)($member->correct_total ?? 0);
                    $diff         = $currentTotal - $correctTotal;
                @endphp
                <tr class="transition-colors hover:bg-violet-50/30">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                               class="rc-checkbox w-4 h-4 rounded border-gray-300 text-violet-600 cursor-pointer"
                               onchange="rcUpdateBar()">
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 whitespace-nowrap">{{ $member->full_name }}</p>
                        @if($member->verificationStatus)
                            <span class="text-xs px-1.5 py-0.5 rounded-md font-medium"
                                  style="background: {{ $member->verificationStatus->color }}22; color: {{ $member->verificationStatus->color }};">
                                {{ $member->verificationStatus->name }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs whitespace-nowrap">
                        {{ $member->dossier_number ?: '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 font-bold text-sm px-2.5 py-1 rounded-lg">
                            {{ number_format($member->housing_score ?? 0, 1) }}
                            <span class="text-xs text-slate-400 font-normal">مجمّد</span>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-bold text-gray-700">{{ number_format($currentTotal, 1) }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-black text-violet-700">{{ number_format($correctTotal, 1) }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($diff > 0.001)
                            <span class="inline-flex items-center gap-0.5 text-red-600 font-bold text-sm bg-red-50 px-2 py-0.5 rounded-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                {{ number_format($diff, 1) }}
                            </span>
                        @elseif($diff < -0.001)
                            <span class="inline-flex items-center gap-0.5 text-emerald-600 font-bold text-sm bg-emerald-50 px-2 py-0.5 rounded-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                {{ number_format(abs($diff), 1) }}
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">لا فرق</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('members.show', $member->id) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 bg-gray-100 border border-gray-200 px-2.5 py-1.5 rounded-lg transition-colors whitespace-nowrap hover:text-violet-600 hover:bg-violet-50 hover:border-violet-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            عرض
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
    <div class="border-t border-gray-100 px-5 py-3 bg-gray-50/30">
        {{ $members->appends(['tab' => $tab, 'component' => $recalcComponent])->links() }}
    </div>
    @endif
</div>
</form>

@else
{{-- ── Deductions / Additions form ──────────────────────── --}}
<form id="bulk-set-form" method="POST" action="{{ route('members.bulk-set-score') }}">
    @csrf
    <input type="hidden" name="mode" id="bs-mode" value="{{ $tab === 'additions' ? 'addition' : 'deduction' }}">

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
        <p class="text-sm font-bold text-gray-600">
            {{ number_format($members->total()) }} عضو
            @if($search || $reasonFilter || $dateFrom || $dateTo)
                <span class="text-xs text-gray-400 font-normal mr-1">(نتائج البحث)</span>
            @endif
        </p>
        <p class="text-xs text-gray-400">صفحة {{ $members->currentPage() }} من {{ $members->lastPage() }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/30">
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" id="select-all"
                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 cursor-pointer"
                               onchange="bsToggleAll(this.checked)">
                    </th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">الاسم</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">رقم الاضبارة</th>
                    @if($tab === 'additions')
                        <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">النقاط المضافة</th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3">سبب الإضافة</th>
                    @else
                        <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">النقاط المنقوصة</th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3">سبب الانقاص</th>
                        <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">تاريخ الانقاص</th>
                    @endif
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">النقاط الحالية</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">المبلغ المقدر</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">المبلغ النهائي</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($members as $member)
                <tr class="transition-colors {{ $tab === 'additions' ? 'hover:bg-emerald-50/30' : 'hover:bg-red-50/30' }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="member_ids[]" value="{{ $member->id }}"
                               class="row-checkbox w-4 h-4 rounded border-gray-300 text-indigo-600 cursor-pointer"
                               onchange="bsUpdateBar()">
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 whitespace-nowrap">{{ $member->full_name }}</p>
                        @if($member->verificationStatus)
                            <span class="text-xs px-1.5 py-0.5 rounded-md font-medium"
                                  style="background: {{ $member->verificationStatus->color }}22; color: {{ $member->verificationStatus->color }};">
                                {{ $member->verificationStatus->name }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs whitespace-nowrap">
                        {{ $member->dossier_number ?: '—' }}
                    </td>
                    @if($tab === 'additions')
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 font-black text-sm px-2.5 py-1 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ number_format($member->score_addition, 1) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs max-w-[200px]">
                        {{ $member->score_addition_reason ?: '—' }}
                    </td>
                    @else
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 font-black text-sm px-2.5 py-1 rounded-lg">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                            </svg>
                            {{ number_format($member->score_deduction, 1) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs max-w-[200px]">
                        {{ $member->score_deduction_reason ?: '—' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($member->score_updated_at)
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($member->score_updated_at)->format('Y/m/d') }}</span>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </td>
                    @endif
                    <td class="px-4 py-3 text-center">
                        <span class="font-bold text-gray-700">{{ number_format($member->score ?? $member->total_score, 1) }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                        {{ number_format($member->estimated_amount ?? 0) }} ل.س
                    </td>
                    <td class="px-4 py-3 text-purple-700 font-semibold whitespace-nowrap">
                        {{ number_format($member->final_amount) }} ل.س
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('members.show', $member->id) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 bg-gray-100 border border-gray-200 px-2.5 py-1.5 rounded-lg transition-colors whitespace-nowrap
                                  {{ $tab === 'additions' ? 'hover:text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200' : 'hover:text-red-600 hover:bg-red-50 hover:border-red-200' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            عرض
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
    <div class="border-t border-gray-100 px-5 py-3 bg-gray-50/30">
        {{ $members->appends(['tab' => $tab])->links() }}
    </div>
    @endif
</div>
</form>
@endif

@endif

{{-- Sticky bulk-set action bar (deductions / additions) --}}
<div id="bs-bar"
     class="fixed bottom-0 right-0 left-0 z-50 transform translate-y-full transition-transform duration-300 ease-in-out"
     style="padding-right: 68px;">
    <div class="mx-auto px-4 pb-4" style="max-width: 80rem;">
        <div class="bg-gray-900 text-white rounded-2xl shadow-2xl px-5 py-4 flex flex-wrap gap-3 items-center">

            <div class="flex items-center gap-2 shrink-0">
                <span class="bg-indigo-500 text-white text-xs font-black px-2.5 py-1 rounded-full" id="bs-count">0</span>
                <span class="text-sm font-medium text-gray-300">عضو محدد</span>
            </div>

            <div class="w-px h-6 bg-gray-600 hidden sm:block shrink-0"></div>

            <div class="flex rounded-xl overflow-hidden border border-gray-600 shrink-0" id="bs-mode-toggle">
                <button type="button" id="bs-btn-deduction"
                        onclick="bsSetMode('deduction')"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold transition-colors bg-red-600 text-white">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                    </svg>
                    انقاص
                </button>
                <button type="button" id="bs-btn-addition"
                        onclick="bsSetMode('addition')"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold transition-colors bg-gray-700 text-gray-300 hover:bg-gray-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة
                </button>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <label class="text-xs text-gray-400 whitespace-nowrap">النقاط</label>
                <input type="number" name="amount" form="bulk-set-form"
                       min="0" value="0"
                       class="w-20 bg-gray-800 border border-gray-600 text-white text-sm font-bold text-center rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div class="flex items-center gap-2 flex-1 min-w-[180px]">
                <label class="text-xs text-gray-400 whitespace-nowrap">السبب</label>
                <input type="text" name="reason" form="bulk-set-form"
                       placeholder="اختياري..."
                       class="flex-1 bg-gray-800 border border-gray-600 text-white text-sm rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-gray-500">
            </div>

            <button type="submit" form="bulk-set-form"
                    class="flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors shrink-0"
                    onclick="return confirm('هل أنت متأكد من تعيين هذه النقاط للأعضاء المحددين؟')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                تعيين
            </button>

            <button type="button" onclick="bsClearAll()"
                    class="text-gray-400 hover:text-white text-xs font-medium transition-colors shrink-0">
                إلغاء
            </button>
        </div>
    </div>
</div>

{{-- Sticky recalculate action bar --}}
<div id="rc-bar"
     class="fixed bottom-0 right-0 left-0 z-50 transform translate-y-full transition-transform duration-300 ease-in-out"
     style="padding-right: 68px;">
    <div class="mx-auto px-4 pb-4" style="max-width: 80rem;">
        <div class="bg-gray-900 text-white rounded-2xl shadow-2xl px-5 py-4 flex flex-wrap gap-3 items-center">

            <div class="flex items-center gap-2 shrink-0">
                <span class="bg-violet-500 text-white text-xs font-black px-2.5 py-1 rounded-full" id="rc-count">0</span>
                <span class="text-sm font-medium text-gray-300">عضو محدد</span>
            </div>

            <div class="w-px h-6 bg-gray-600 hidden sm:block shrink-0"></div>

            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span class="text-sm text-gray-300">إعادة الحساب بالمعادلة الصحيحة (بدون نقاط السكن)</span>
            </div>

            <div class="flex-1"></div>

            <button type="submit" form="bulk-recalc-form"
                    class="flex items-center gap-1.5 bg-violet-600 hover:bg-violet-500 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors shrink-0"
                    onclick="return confirm('هل أنت متأكد من إعادة حساب نقاط الأعضاء المحددين؟ سيتم تحديث المجموع الكلي لكل عضو.')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                إعادة الحساب
            </button>

            <button type="button" onclick="rcClearAll()"
                    class="text-gray-400 hover:text-white text-xs font-medium transition-colors shrink-0">
                إلغاء
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const currentMode = '{{ $tab === "additions" ? "addition" : "deduction" }}';

    function bsUpdateBar() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const bar     = document.getElementById('bs-bar');
        const countEl = document.getElementById('bs-count');
        if (!bar) return;
        if (checked.length > 0) {
            bar.classList.remove('translate-y-full');
            countEl.textContent = checked.length;
        } else {
            bar.classList.add('translate-y-full');
        }
        const all = document.querySelectorAll('.row-checkbox');
        const sa  = document.getElementById('select-all');
        if (sa) {
            sa.indeterminate = checked.length > 0 && checked.length < all.length;
            sa.checked       = all.length > 0 && checked.length === all.length;
        }
    }

    function bsToggleAll(checked) {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = checked);
        bsUpdateBar();
    }

    function bsSetMode(mode) {
        const modeInput = document.getElementById('bs-mode');
        if (!modeInput) return;
        modeInput.value = mode;
        const deductBtn   = document.getElementById('bs-btn-deduction');
        const additionBtn = document.getElementById('bs-btn-addition');
        if (mode === 'deduction') {
            deductBtn.className   = deductBtn.className.replace('bg-gray-700 text-gray-300 hover:bg-gray-600', 'bg-red-600 text-white');
            additionBtn.className = additionBtn.className.replace('bg-emerald-600 text-white', 'bg-gray-700 text-gray-300 hover:bg-gray-600');
        } else {
            additionBtn.className = additionBtn.className.replace('bg-gray-700 text-gray-300 hover:bg-gray-600', 'bg-emerald-600 text-white');
            deductBtn.className   = deductBtn.className.replace('bg-red-600 text-white', 'bg-gray-700 text-gray-300 hover:bg-gray-600');
        }
    }

    function bsClearAll() {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        const sa = document.getElementById('select-all');
        if (sa) { sa.checked = false; sa.indeterminate = false; }
        bsUpdateBar();
    }

    if (document.getElementById('bs-mode')) {
        bsSetMode(currentMode);
    }

    window.bsToggleAll = bsToggleAll;
    window.bsUpdateBar = bsUpdateBar;
    window.bsSetMode   = bsSetMode;
    window.bsClearAll  = bsClearAll;
})();

(function () {
    function rcUpdateBar() {
        const checked = document.querySelectorAll('.rc-checkbox:checked');
        const bar     = document.getElementById('rc-bar');
        const countEl = document.getElementById('rc-count');
        if (!bar) return;
        if (checked.length > 0) {
            bar.classList.remove('translate-y-full');
            countEl.textContent = checked.length;
        } else {
            bar.classList.add('translate-y-full');
        }
        const all = document.querySelectorAll('.rc-checkbox');
        const sa  = document.getElementById('rc-select-all');
        if (sa) {
            sa.indeterminate = checked.length > 0 && checked.length < all.length;
            sa.checked       = all.length > 0 && checked.length === all.length;
        }
    }

    function rcToggleAll(checked) {
        document.querySelectorAll('.rc-checkbox').forEach(cb => cb.checked = checked);
        rcUpdateBar();
    }

    function rcClearAll() {
        document.querySelectorAll('.rc-checkbox').forEach(cb => cb.checked = false);
        const sa = document.getElementById('rc-select-all');
        if (sa) { sa.checked = false; sa.indeterminate = false; }
        rcUpdateBar();
    }

    window.rcUpdateBar = rcUpdateBar;
    window.rcToggleAll = rcToggleAll;
    window.rcClearAll  = rcClearAll;
})();
</script>

@endsection
