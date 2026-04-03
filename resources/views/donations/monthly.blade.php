@extends('layouts.app')

@section('title', 'المتابعة الشهرية — التبرعات')

@section('breadcrumb')
    <a href="{{ route('donations.index') }}" class="text-emerald-600 hover:underline">التبرعات</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700">المتابعة الشهرية</span>
@endsection

@section('content')

@php
    $hasFilters = $search || $dossierFrom !== '' || $dossierTo !== ''
               || !empty($finalStatusIds) || !empty($genders) || !empty($associationIds) || $specialCases !== ''
               || !empty($specialDescriptions) || !empty($delegates) || !empty($networks)
               || !empty($addresses) || !empty($maritalStatuses);
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">المتابعة الشهرية للتبرعات</h1>
        <p class="text-sm text-gray-400 mt-0.5">من تبرعنا لهم ومن لم نتبرع لهم بعد</p>
    </div>
</div>

@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-4">
    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 text-sm font-medium rounded-2xl px-5 py-3.5 mb-4">
    <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    {{ session('error') }}
</div>
@endif

{{-- Filter Panel --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5">
    <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100">
        <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-gray-700">الفلاتر</span>
    </div>

    <form method="GET" action="{{ route('donations.monthly') }}" class="p-5">

        {{-- Row 1: Month + Search --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الشهر</label>
                <input type="month" name="month" value="{{ $month }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">بحث بالاسم</label>
                <div class="relative">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="الاسم..."
                           class="w-full pr-9 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition placeholder-gray-300">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">رقم الاضبارة (من — إلى)</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="dossier_from" value="{{ $dossierFrom }}"
                           placeholder="من"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition placeholder-gray-300 font-mono">
                    <span class="text-gray-400 text-sm shrink-0">—</span>
                    <input type="text" name="dossier_to" value="{{ $dossierTo }}"
                           placeholder="إلى"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">نوع الشبكة</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 space-y-1">
                    @foreach(['MTN', 'SYRIATEL'] as $net)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="network[]" value="{{ $net }}"
                                   {{ in_array($net, $networks) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            {{ $net }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Row 2: Checkboxes --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة الاجتماعية</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @foreach($maritalStatusList as $ms)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="marital_status[]" value="{{ $ms->name }}"
                                   {{ in_array($ms->name, $maritalStatuses) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            {{ $ms->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الجنس</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 space-y-1">
                    @foreach(['ذكر', 'أنثى'] as $g)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="gender[]" value="{{ $g }}"
                                   {{ in_array($g, $genders) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            {{ $g }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة النهائية</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @forelse($finalStatusList as $fs)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}"
                                   {{ in_array($fs->id, $finalStatusIds) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <span class="w-2 h-2 rounded-full inline-block flex-shrink-0" style="background:{{ $fs->color }}"></span>
                            {{ $fs->name }}
                        </label>
                    @empty
                        <p class="text-xs text-gray-400">لا توجد حالات نهائية</p>
                    @endforelse
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الجمعية</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @forelse($associationList as $assoc)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="association_id[]" value="{{ $assoc->id }}"
                                   {{ in_array($assoc->id, $associationIds) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            {{ $assoc->name }}
                        </label>
                    @empty
                        <p class="text-xs text-gray-400">لا توجد جمعيات</p>
                    @endforelse
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالات الخاصة</label>
                <select name="special_cases"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
                    <option value="">— الكل —</option>
                    <option value="1" {{ $specialCases === '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ $specialCases === '0' ? 'selected' : '' }}>لا</option>
                </select>
            </div>

        </div>

        {{-- Row 3: Address + Delegate + Special Description --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">

            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">المنطقة / العنوان</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @forelse($addressList as $addr)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="current_address[]" value="{{ $addr }}"
                                   {{ in_array($addr, $addresses) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <span class="truncate">{{ $addr }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-gray-400">لا توجد مناطق</p>
                    @endforelse
                </div>
            </div>

            @if($delegateList->isNotEmpty())
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">المندوب الخارجي</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @foreach($delegateList as $d)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="delegate[]" value="{{ $d }}"
                                   {{ in_array($d, $delegates) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            {{ $d }}
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

            @if($specialDescriptionList->isNotEmpty())
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">وصف الحالة الخاصة</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @foreach($specialDescriptionList as $sd)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="special_cases_description[]" value="{{ $sd }}"
                                   {{ in_array($sd, $specialDescriptions) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <span class="truncate">{{ $sd }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <div class="flex items-center gap-2">
            <button type="submit"
                    class="flex items-center gap-2 bg-gradient-to-l from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                تطبيق
            </button>
            @if($hasFilters)
                <a href="{{ route('donations.monthly', ['month' => $month]) }}"
                   class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
            @endif
            <span class="text-sm text-gray-500 ms-auto">
                <span class="font-bold text-indigo-700">{{ $donatedMembers->count() + $notDonatedMembers->count() }}</span> عضو في النتائج
            </span>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    {{-- Counts --}}
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center">
        <p class="text-3xl font-bold text-emerald-700">{{ $donatedMembers->count() }}</p>
        <p class="text-xs text-emerald-600 mt-1 font-medium">تم التبرع لهم</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-center">
        <p class="text-3xl font-bold text-red-600">{{ $notDonatedMembers->count() }}</p>
        <p class="text-xs text-red-500 mt-1 font-medium">لم يُتبرع لهم بعد</p>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 text-center">
        <p class="text-3xl font-bold text-gray-700">{{ $donatedMembers->count() + $notDonatedMembers->count() }}</p>
        <p class="text-xs text-gray-500 mt-1 font-medium">إجمالي الأعضاء</p>
    </div>
    {{-- Amounts --}}
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center">
        <p class="text-lg font-black text-emerald-700 leading-tight">{{ number_format((float)$totalDonated, 0, '.', ',') }}</p>
        <p class="text-xs text-emerald-600 mt-1 font-medium">مجموع ما تبرع به (ل.س)</p>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-center">
        <p class="text-lg font-black text-red-600 leading-tight">{{ number_format((float)$totalNotDonated, 0, '.', ',') }}</p>
        <p class="text-xs text-red-500 mt-1 font-medium">مجموع ما لم يُتبرع به (ل.س)</p>
    </div>
    <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4 text-center">
        <p class="text-lg font-black text-indigo-700 leading-tight">{{ number_format((float)$totalGrand, 0, '.', ',') }}</p>
        <p class="text-xs text-indigo-500 mt-1 font-medium">المجموع الكلي (ل.س)</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ✅ Donated --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-emerald-50 border-b border-emerald-100 px-5 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-emerald-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                تم التبرع لهم
            </h2>
            <span class="text-xs bg-emerald-100 text-emerald-700 rounded-full px-2.5 py-0.5">{{ $donatedMembers->count() }}</span>
        </div>

        @if($donatedMembers->isEmpty())
            <p class="text-center text-gray-400 text-sm py-8">لا أحد بعد هذا الشهر.</p>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($donatedMembers as $member)
                @php $donations = $monthDonations->get($member->id, collect()); @endphp
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $member->full_name }}</p>
                            <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                @foreach($donations as $d)
                                    <span class="text-xs text-gray-400">
                                        {{ number_format($d->amount, 0) }} ل.س
                                        @if($d->type === 'sham_cash')
                                            <span class="text-blue-500">(شام كاش)</span>
                                        @endif
                                    </span>
                                @endforeach
                                @if($member->estimated_amount > 0)
                                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-full px-2 py-0.5">
                                        {{ number_format((float)$member->estimated_amount, 0, '.', ',') }} ل.س مقدر
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('donations.create') }}?member_id={{ $member->id }}"
                       class="shrink-0 text-xs text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 px-2 py-1 rounded-lg transition-colors">
                        + إضافة
                    </a>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

    {{-- ❌ Not Donated --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-red-50 border-b border-red-100 px-5 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-red-700 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                لم يُتبرع لهم بعد
            </h2>
            <span class="text-xs bg-red-100 text-red-600 rounded-full px-2.5 py-0.5">{{ $notDonatedMembers->count() }}</span>
        </div>

        @if($notDonatedMembers->isEmpty())
            <div class="text-center py-10">
                <p class="text-2xl mb-2">🎉</p>
                <p class="text-sm text-emerald-600 font-medium">رائع! تم التبرع لجميع الأعضاء هذا الشهر.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($notDonatedMembers as $member)
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-700 truncate">{{ $member->full_name }}</p>
                            @if($member->estimated_amount > 0)
                                <p class="text-xs text-gray-400">{{ number_format((float)$member->estimated_amount, 0, '.', ',') }} ل.س</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        @if($member->estimated_amount > 0)
                            <form method="POST" action="{{ route('donations.monthly.quick') }}">
                                @csrf
                                <input type="hidden" name="member_id" value="{{ $member->id }}">
                                <input type="hidden" name="month" value="{{ $month }}">
                                <button type="submit"
                                        class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg transition-colors font-medium">
                                    تبرع فوري
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('donations.create') }}?member_id={{ $member->id }}"
                           class="text-xs text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 px-2 py-1.5 rounded-lg transition-colors">
                            يدوي
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>

@endsection
