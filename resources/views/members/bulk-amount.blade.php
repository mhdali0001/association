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
    <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100">
        <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-gray-700">الفلاتر</span>
        <span class="text-xs text-gray-400 ms-auto">الفلاتر تؤثر على "تطبيق على الكل"</span>
    </div>
    <form method="GET" action="{{ route('members.bulk-amount') }}" class="p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">

            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">بحث</label>
                <div class="relative">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="الاسم أو رقم الهوية أو رقم الملف..."
                           class="w-full pr-9 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300">
                </div>
            </div>

            {{-- Dossier range --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">رقم الاضبارة (من — إلى)</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="dossier_from" value="{{ $dossierFrom }}"
                           placeholder="من"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300 font-mono">
                    <span class="text-gray-400 shrink-0">—</span>
                    <input type="text" name="dossier_to" value="{{ $dossierTo }}"
                           placeholder="إلى"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition placeholder-gray-300 font-mono">
                </div>
            </div>

            {{-- Has amount --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">المبلغ المقدر</label>
                <select name="has_amount"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition">
                    <option value="">— الكل —</option>
                    <option value="1" {{ request('has_amount') === '1' ? 'selected' : '' }}>لديهم مبلغ</option>
                    <option value="0" {{ request('has_amount') === '0' ? 'selected' : '' }}>بدون مبلغ</option>
                </select>
            </div>

            {{-- Network --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">نوع الشبكة</label>
                <select name="network[]" multiple
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition h-[42px]">
                    @foreach(['MTN', 'SYRIATEL'] as $net)
                        <option value="{{ $net }}" {{ in_array($net, (array)request('network', [])) ? 'selected' : '' }}>{{ $net }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-3">

            {{-- Verification status --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">حالة التحقق</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @foreach($verificationStatuses as $vs)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="verification_status_id[]" value="{{ $vs->id }}"
                                   {{ in_array($vs->id, (array)request('verification_status_id', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            <span class="w-2 h-2 rounded-full inline-block flex-shrink-0" style="background:{{ $vs->color }}"></span>
                            {{ $vs->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Final status --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة النهائية</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @forelse($finalStatusList as $fs)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}"
                                   {{ in_array($fs->id, (array)request('final_status_id', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            <span class="w-2 h-2 rounded-full inline-block flex-shrink-0" style="background:{{ $fs->color }}"></span>
                            {{ $fs->name }}
                        </label>
                    @empty
                        <p class="text-xs text-gray-400">لا توجد حالات نهائية</p>
                    @endforelse
                </div>
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">المنطقة / العنوان</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @forelse($addressList as $addr)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="current_address[]" value="{{ $addr }}"
                                   {{ in_array($addr, (array)request('current_address', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            <span class="truncate">{{ $addr }}</span>
                        </label>
                    @empty
                        <p class="text-xs text-gray-400">لا توجد مناطق</p>
                    @endforelse
                </div>
            </div>

            {{-- Marital status --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة الاجتماعية</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @foreach(['widow' => 'أرملة', 'divorced' => 'مطلقة'] as $val => $label)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="marital_status[]" value="{{ $val }}"
                                   {{ in_array($val, (array)request('marital_status', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">

            {{-- Gender --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الجنس</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 space-y-1">
                    @foreach(['ذكر', 'أنثى'] as $g)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="gender[]" value="{{ $g }}"
                                   {{ in_array($g, (array)request('gender', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $g }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Association --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الجمعية</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-32 overflow-y-auto space-y-1">
                    @forelse($associationList as $assoc)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="association_id[]" value="{{ $assoc->id }}"
                                   {{ in_array($assoc->id, (array)request('association_id', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $assoc->name }}
                        </label>
                    @empty
                        <p class="text-xs text-gray-400">لا توجد جمعيات</p>
                    @endforelse
                </div>
            </div>

            {{-- Special cases --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالات الخاصة</label>
                <select name="special_cases"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition">
                    <option value="">— الكل —</option>
                    <option value="1" {{ request('special_cases') === '1' ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ request('special_cases') === '0' ? 'selected' : '' }}>لا</option>
                </select>

                @if($specialDescriptionList->isNotEmpty())
                <div class="mt-2">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">وصف الحالة الخاصة</label>
                    <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-28 overflow-y-auto space-y-1">
                        @foreach($specialDescriptionList as $sd)
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                                <input type="checkbox" name="special_cases_description[]" value="{{ $sd }}"
                                       {{ in_array($sd, (array)request('special_cases_description', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                                <span class="truncate">{{ $sd }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Delegate --}}
            @if($delegateList->isNotEmpty())
            <div class="lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">المندوب</label>
                <div class="border border-gray-200 rounded-xl bg-gray-50 px-3 py-2 max-h-28 overflow-y-auto flex flex-wrap gap-x-4 gap-y-1">
                    @foreach($delegateList as $d)
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-gray-900">
                            <input type="checkbox" name="delegate[]" value="{{ $d }}"
                                   {{ in_array($d, (array)request('delegate', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-violet-600 focus:ring-violet-400">
                            {{ $d }}
                        </label>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <div class="flex items-center gap-2">
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
                    مسح
                </a>
            @endif
            <span class="text-sm text-gray-500 ms-auto">
                <span class="font-bold text-violet-700">{{ $fmt($totalCount) }}</span> عضو في النتائج الحالية
            </span>
        </div>
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
