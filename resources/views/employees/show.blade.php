@extends('layouts.app')

@section('title', $employee->name . ' — مسالك النور')
@section('max-width', 'max-w-6xl')

@section('breadcrumb')
    <a href="{{ route('employees.index') }}" class="hover:text-slate-700 transition-colors">الموظفون</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">{{ $employee->name }}</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-slate-900 via-slate-800 to-slate-700 rounded-3xl overflow-hidden shadow-xl mb-6">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-20 left-8 w-80 h-80 bg-white/5 rounded-full"></div>
        <div class="absolute inset-0 opacity-5" style="background-image:linear-gradient(rgba(255,255,255,.2) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.2) 1px,transparent 1px);background-size:40px 40px"></div>
    </div>
    <div class="relative p-7">
        <div class="flex items-start justify-between flex-wrap gap-5">
            {{-- Employee identity --}}
            <div class="flex items-center gap-5">
                <div class="relative shrink-0">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center text-white font-black text-3xl">
                        {{ mb_substr($employee->name, 0, 1) }}
                    </div>
                    <div class="absolute -bottom-1 -left-1 w-5 h-5 rounded-full border-2 border-slate-800 flex items-center justify-center {{ $employee->is_active ? 'bg-emerald-400' : 'bg-gray-400' }}">
                        @if($employee->is_active)
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @else
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 mb-0.5">
                        <h1 class="text-2xl font-black text-white">{{ $employee->name }}</h1>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $employee->is_active ? 'bg-emerald-400/25 text-emerald-200 border border-emerald-400/30' : 'bg-gray-400/25 text-gray-300 border border-gray-400/30' }}">
                            {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    @if($employee->job_title)
                        <p class="text-slate-300 text-sm">{{ $employee->job_title }}</p>
                    @endif
                    @if($employee->phone)
                        <p class="text-slate-400 text-xs mt-1.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $employee->phone }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Actions + base salary --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="bg-white/10 border border-white/20 rounded-2xl px-5 py-3 text-center">
                    <p class="text-white font-black text-xl leading-none">
                        {{ ($employee->base_salary_currency ?? 'SYP') === 'USD' ? number_format((float)$employee->base_salary, 2) : number_format((float)$employee->base_salary) }}
                        <span class="text-sm font-normal text-slate-300">{{ ($employee->base_salary_currency ?? 'SYP') === 'USD' ? '$' : 'ل.س' }}</span>
                    </p>
                    <p class="text-slate-400 text-xs mt-1">الراتب الأساسي</p>
                </div>
                <button onclick="document.getElementById('modal-edit').classList.remove('hidden')"
                        class="flex items-center gap-2 bg-white/15 hover:bg-white/25 border border-white/25 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل البيانات
                </button>
                <a href="{{ route('employees.index') }}"
                   class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white/80 hover:text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    الموظفون
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Stats --}}
@php
    $sypNet = $totals['SYP']['net'];
    $usdNet = $totals['USD']['net'];
@endphp
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <span class="text-xs text-gray-400">الرواتب</span>
        </div>
        @if($totals['SYP']['salary'] > 0)
            <p class="text-blue-600 font-black text-lg leading-tight">{{ number_format($totals['SYP']['salary']) }} <span class="text-xs font-normal text-gray-400">ل.س</span></p>
        @endif
        @if($totals['USD']['salary'] > 0)
            <p class="text-blue-600 font-black text-lg leading-tight">{{ number_format($totals['USD']['salary'], 2) }} <span class="text-xs font-normal text-gray-400">$</span></p>
        @endif
        @if($totals['SYP']['salary'] == 0 && $totals['USD']['salary'] == 0)
            <p class="text-blue-600 font-black text-xl leading-none">—</p>
        @endif
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs text-gray-400">الإضافات</span>
        </div>
        @if($totals['SYP']['additions'] > 0)
            <p class="text-emerald-600 font-black text-lg leading-tight">{{ number_format($totals['SYP']['additions']) }} <span class="text-xs font-normal text-gray-400">ل.س</span></p>
        @endif
        @if($totals['USD']['additions'] > 0)
            <p class="text-emerald-600 font-black text-lg leading-tight">{{ number_format($totals['USD']['additions'], 2) }} <span class="text-xs font-normal text-gray-400">$</span></p>
        @endif
        @if($totals['SYP']['additions'] == 0 && $totals['USD']['additions'] == 0)
            <p class="text-emerald-600 font-black text-xl leading-none">—</p>
        @endif
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs text-gray-400">الخصومات</span>
        </div>
        @if($totals['SYP']['deductions'] > 0)
            <p class="text-red-600 font-black text-lg leading-tight">{{ number_format($totals['SYP']['deductions']) }} <span class="text-xs font-normal text-gray-400">ل.س</span></p>
        @endif
        @if($totals['USD']['deductions'] > 0)
            <p class="text-red-600 font-black text-lg leading-tight">{{ number_format($totals['USD']['deductions'], 2) }} <span class="text-xs font-normal text-gray-400">$</span></p>
        @endif
        @if($totals['SYP']['deductions'] == 0 && $totals['USD']['deductions'] == 0)
            <p class="text-red-600 font-black text-xl leading-none">—</p>
        @endif
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
            </div>
            <span class="text-xs text-gray-400">السلف</span>
        </div>
        @if($totals['SYP']['advances'] > 0)
            <p class="text-amber-600 font-black text-lg leading-tight">{{ number_format($totals['SYP']['advances']) }} <span class="text-xs font-normal text-gray-400">ل.س</span></p>
        @endif
        @if($totals['USD']['advances'] > 0)
            <p class="text-amber-600 font-black text-lg leading-tight">{{ number_format($totals['USD']['advances'], 2) }} <span class="text-xs font-normal text-gray-400">$</span></p>
        @endif
        @if($totals['SYP']['advances'] == 0 && $totals['USD']['advances'] == 0)
            <p class="text-amber-600 font-black text-xl leading-none">—</p>
        @endif
    </div>
    @php $netPositive = $sypNet >= 0 && $usdNet >= 0; @endphp
    <div class="bg-white rounded-2xl border {{ $netPositive ? 'border-emerald-100' : 'border-red-100' }} shadow-sm p-4 col-span-2 sm:col-span-1">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-7 h-7 rounded-lg {{ $netPositive ? 'bg-emerald-100' : 'bg-red-100' }} flex items-center justify-center shrink-0">
                <svg class="w-3.5 h-3.5 {{ $netPositive ? 'text-emerald-600' : 'text-red-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <span class="text-xs text-gray-400 block leading-tight">المتبقي</span>
                <span class="text-[10px] text-gray-300 leading-tight">راتب + إضافات + مكافآت − سلف − خصومات</span>
            </div>
        </div>
        @if($totals['SYP']['salary'] + $totals['SYP']['additions'] + $totals['SYP']['advances'] + $totals['SYP']['bonuses'] + $totals['SYP']['deductions'] > 0)
            <p class="font-black text-lg leading-tight {{ $sypNet >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                {{ $sypNet < 0 ? '-' : '' }}{{ number_format(abs($sypNet)) }} <span class="text-xs font-normal text-gray-400">ل.س</span>
            </p>
        @endif
        @if($totals['USD']['salary'] + $totals['USD']['additions'] + $totals['USD']['advances'] + $totals['USD']['bonuses'] + $totals['USD']['deductions'] > 0)
            <p class="font-black text-lg leading-tight {{ $usdNet >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                {{ $usdNet < 0 ? '-' : '' }}{{ number_format(abs($usdNet), 2) }} <span class="text-xs font-normal text-gray-400">$</span>
            </p>
        @endif
        @if(array_sum($totals['SYP']) == 0 && array_sum($totals['USD']) == 0)
            <p class="font-black text-xl leading-none text-gray-400">—</p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ===== Transaction Form ===== --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden sticky top-6">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-sm">إضافة عملية جديدة</h2>
                <p class="text-xs text-gray-400 mt-0.5">اختر نوع العملية وأدخل التفاصيل</p>
            </div>

            {{-- Type cards --}}
            <div class="p-4 pb-0">
                <div class="grid grid-cols-2 gap-2" id="type-selector">
                    <button type="button" data-type="salary" onclick="selectType('salary')"
                            class="type-card flex items-center gap-2.5 px-3 py-3 rounded-xl border-2 border-blue-400 bg-blue-50 text-blue-700 transition-all cursor-pointer text-right">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold">راتب</span>
                    </button>
                    <button type="button" data-type="addition" onclick="selectType('addition')"
                            class="type-card flex items-center gap-2.5 px-3 py-3 rounded-xl border-2 border-gray-100 bg-white text-gray-400 hover:border-gray-200 hover:bg-gray-50 transition-all cursor-pointer text-right">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold">إضافة</span>
                    </button>
                    <button type="button" data-type="deduction" onclick="selectType('deduction')"
                            class="type-card flex items-center gap-2.5 px-3 py-3 rounded-xl border-2 border-gray-100 bg-white text-gray-400 hover:border-gray-200 hover:bg-gray-50 transition-all cursor-pointer text-right">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold">خصم</span>
                    </button>
                    <button type="button" data-type="advance" onclick="selectType('advance')"
                            class="type-card flex items-center gap-2.5 px-3 py-3 rounded-xl border-2 border-gray-100 bg-white text-gray-400 hover:border-gray-200 hover:bg-gray-50 transition-all cursor-pointer text-right">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        </div>
                        <span class="text-xs font-bold">سلفة</span>
                    </button>
                    <button type="button" data-type="bonus" onclick="selectType('bonus')"
                            class="type-card col-span-2 flex items-center gap-2.5 px-3 py-3 rounded-xl border-2 border-gray-100 bg-white text-gray-400 hover:border-gray-200 hover:bg-gray-50 transition-all cursor-pointer text-right">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                        </div>
                        <span class="text-xs font-bold">مكافأة</span>
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('employees.transactions.store', $employee) }}" class="p-4 space-y-3">
                @csrf
                <input type="hidden" name="type" id="tx-type" value="salary">

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5" id="amount-label">قيمة الراتب</label>
                    <div class="flex gap-2">
                        <input type="number" name="amount" min="0.01" step="0.01" required placeholder="0"
                               class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-bold focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50 text-gray-800">
                        <div class="flex rounded-xl border border-gray-200 overflow-hidden text-xs font-bold shrink-0">
                            <label class="flex items-center justify-center px-3 py-2.5 cursor-pointer transition-colors has-[:checked]:bg-emerald-600 has-[:checked]:text-white text-gray-500 hover:bg-gray-50">
                                <input type="radio" name="currency" value="USD" checked id="cur-usd" class="sr-only">
                                $
                            </label>
                            <label class="flex items-center justify-center px-3 py-2.5 cursor-pointer transition-colors has-[:checked]:bg-slate-700 has-[:checked]:text-white text-gray-500 hover:bg-gray-50 border-r border-gray-200">
                                <input type="radio" name="currency" value="SYP" id="cur-syp" class="sr-only">
                                ل.س
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">السبب / الوصف</label>
                    <textarea name="reason" rows="2" id="reason-input" placeholder="راتب شهر مايو..."
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">التاريخ</label>
                    <input type="date" name="transaction_date" required value="{{ date('Y-m-d') }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50">
                </div>

                <button type="submit" id="submit-btn"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-3 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span id="submit-label">تسجيل الراتب</span>
                </button>
            </form>

            @if($employee->notes)
            <div class="px-4 pb-4">
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">
                    <p class="text-xs font-bold text-amber-700 mb-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        ملاحظات
                    </p>
                    <p class="text-xs text-amber-700/80">{{ $employee->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== Transaction History ===== --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Filter bar --}}
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <form method="GET" action="{{ route('employees.show', $employee) }}" class="flex flex-wrap gap-2 items-center">
                    <div class="relative flex-1 min-w-[160px]">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="بحث في السبب..."
                               class="w-full pr-9 pl-3 py-2 text-xs border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-slate-300 focus:outline-none">
                    </div>
                    <div class="flex rounded-xl border border-gray-200 overflow-hidden text-xs font-bold">
                        @foreach(['all' => 'الكل', 'salary' => 'راتب', 'addition' => 'إضافة', 'deduction' => 'خصم', 'advance' => 'سلفة', 'bonus' => 'مكافأة'] as $val => $lbl)
                        <a href="{{ route('employees.show', $employee) }}?type={{ $val }}&search={{ $search }}"
                           class="px-3 py-2 transition-colors {{ $typeFilter === $val
                               ? ($val === 'salary' ? 'bg-blue-600 text-white' : ($val === 'addition' ? 'bg-emerald-600 text-white' : ($val === 'deduction' ? 'bg-red-600 text-white' : ($val === 'advance' ? 'bg-amber-500 text-white' : ($val === 'bonus' ? 'bg-violet-600 text-white' : 'bg-slate-700 text-white')))))
                               : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                            {{ $lbl }}
                        </a>
                        @endforeach
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition-colors">بحث</button>
                </form>
            </div>

            {{-- Count + sum bar --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 bg-white">
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full {{ $typeFilter !== 'all' ? 'bg-slate-500' : 'bg-gray-300' }}"></div>
                    <p class="text-sm font-bold text-gray-600">{{ number_format($transactions->count()) }} عملية</p>
                </div>
                @if($typeFilter !== 'all')
                    <p class="text-xs text-gray-400">
                        المجموع: <span class="font-black text-gray-600">{{ number_format($transactions->sum('amount')) }} ل.س</span>
                    </p>
                @endif
            </div>

            @if($transactions->isEmpty())
                <div class="text-center py-20">
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <p class="text-gray-500 text-sm font-semibold">لا توجد عمليات</p>
                    <p class="text-gray-400 text-xs mt-1">أضف عملية من النموذج على اليمين</p>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/40 text-right">
                            <th class="font-semibold text-gray-400 text-xs px-4 py-3">النوع</th>
                            <th class="font-semibold text-gray-400 text-xs px-4 py-3">المبلغ</th>
                            <th class="font-semibold text-gray-400 text-xs px-4 py-3">السبب</th>
                            <th class="font-semibold text-gray-400 text-xs px-4 py-3 whitespace-nowrap">التاريخ</th>
                            <th class="font-semibold text-gray-400 text-xs px-4 py-3 whitespace-nowrap">بواسطة</th>
                            <th class="w-20 px-3 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($transactions as $tx)
                        @php
                            $typeMeta = [
                                'salary'    => ['icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'badge' => 'bg-blue-100 text-blue-700', 'iconColor' => 'text-blue-500', 'amtClass' => 'text-blue-700'],
                                'addition'  => ['icon' => 'M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z', 'badge' => 'bg-emerald-100 text-emerald-700', 'iconColor' => 'text-emerald-500', 'amtClass' => 'text-emerald-700'],
                                'deduction' => ['icon' => 'M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z', 'badge' => 'bg-red-100 text-red-700', 'iconColor' => 'text-red-500', 'amtClass' => 'text-red-600'],
                                'advance'   => ['icon' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6', 'badge' => 'bg-amber-100 text-amber-700', 'iconColor' => 'text-amber-500', 'amtClass' => 'text-amber-700'],
                                'bonus'     => ['icon' => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7', 'badge' => 'bg-violet-100 text-violet-700', 'iconColor' => 'text-violet-500', 'amtClass' => 'text-violet-700'],
                            ];
                            $meta = $typeMeta[$tx->type] ?? ['icon' => '', 'badge' => 'bg-gray-100 text-gray-700', 'iconColor' => 'text-gray-400', 'amtClass' => 'text-gray-700'];
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors group">
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1.5 rounded-lg {{ $meta['badge'] }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}"/></svg>
                                    {{ $tx->typeLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-black text-base {{ $meta['amtClass'] }}">
                                    {{ $tx->isCredit() ? '+' : '−' }}{{ $tx->currency === 'USD' ? number_format((float)$tx->amount, 2) : number_format((float)$tx->amount) }}
                                </span>
                                <span class="text-xs font-normal {{ $tx->currency === 'USD' ? 'text-emerald-500 font-bold' : 'text-gray-400' }}">
                                    {{ $tx->currencySymbol() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs max-w-[180px]">
                                <span class="line-clamp-2" title="{{ $tx->reason }}">{{ $tx->reason ?: '—' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-gray-500 font-medium">{{ $tx->transaction_date->format('Y/m/d') }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-xs text-gray-400">{{ $tx->creator?->name ?? '—' }}</span>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                    <button type="button"
                                            onclick="openEditTx({
                                                id: {{ $tx->id }},
                                                type: '{{ $tx->type }}',
                                                amount: '{{ (float)$tx->amount }}',
                                                currency: '{{ $tx->currency }}',
                                                reason: {{ json_encode($tx->reason) }},
                                                date: '{{ $tx->transaction_date->format('Y-m-d') }}',
                                                url: '{{ route('employees.transactions.update', [$employee, $tx]) }}'
                                            })"
                                            class="w-7 h-7 rounded-lg text-gray-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('employees.transactions.destroy', [$employee, $tx]) }}"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه العملية؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-7 h-7 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    {{-- Footer total --}}
                    @if($transactions->isNotEmpty())
                    @php
                        $footSYP = $transactions->where('currency', 'SYP')->sum('amount');
                        $footUSD = $transactions->where('currency', 'USD')->sum('amount');
                    @endphp
                    <tfoot>
                        <tr class="border-t-2 border-gray-100 bg-gray-50/50">
                            <td class="px-4 py-3 text-xs font-bold text-gray-500 whitespace-nowrap" colspan="2">
                                المجموع:
                                @if($footSYP > 0)
                                    <span class="text-gray-700">{{ number_format($footSYP) }} ل.س</span>
                                @endif
                                @if($footSYP > 0 && $footUSD > 0)
                                    <span class="text-gray-400 mx-1">·</span>
                                @endif
                                @if($footUSD > 0)
                                    <span class="text-gray-700">{{ number_format($footUSD, 2) }} $</span>
                                @endif
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== Modal: تعديل الموظف ===== --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">تعديل بيانات الموظف</h2>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('employees.update', $employee) }}" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ $employee->name }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">المسمى الوظيفي</label>
                    <input type="text" name="job_title" value="{{ $employee->job_title }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الهاتف</label>
                    <input type="text" name="phone" value="{{ $employee->phone }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">الراتب الأساسي</label>
                    <input type="number" name="base_salary" min="0" value="{{ (float)$employee->base_salary }}"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">عملة الراتب</label>
                    <div class="flex rounded-xl border border-gray-200 overflow-hidden text-sm font-bold">
                        <label class="flex-1 flex items-center justify-center py-2.5 cursor-pointer has-[:checked]:bg-emerald-600 has-[:checked]:text-white text-gray-500 hover:bg-gray-50 transition-colors">
                            <input type="radio" name="base_salary_currency" value="USD" checked class="sr-only">
                            $ دولار
                        </label>
                        <label class="flex-1 flex items-center justify-center py-2.5 cursor-pointer has-[:checked]:bg-slate-700 has-[:checked]:text-white text-gray-500 hover:bg-gray-50 transition-colors border-r border-gray-200">
                            <input type="radio" name="base_salary_currency" value="SYP" class="sr-only">
                            ل.س
                        </label>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ $employee->is_active ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-slate-600 focus:ring-slate-400">
                        <span class="text-sm font-semibold text-gray-600">موظف نشط</span>
                    </label>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-400 focus:outline-none bg-gray-50 resize-none">{{ $employee->notes }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="flex-1 bg-slate-700 hover:bg-slate-800 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                    حفظ التعديلات
                </button>
                <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
        <div class="px-6 pb-5 pt-2">
            <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف وجميع عملياته نهائياً؟')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-semibold flex items-center gap-1.5 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    حذف الموظف نهائياً مع جميع عملياته
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ===== Modal: تعديل العملية ===== --}}
<div id="modal-edit-tx" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">تعديل العملية</h2>
            <button onclick="document.getElementById('modal-edit-tx').classList.add('hidden')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" id="edit-tx-form" class="p-6 space-y-4">
            @csrf @method('PUT')

            {{-- Type selector --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-2">نوع العملية</label>
                <div class="grid grid-cols-5 gap-1.5" id="edit-type-selector">
                    @foreach([
                        ['salary',    'راتب',   'blue'],
                        ['addition',  'إضافة',  'emerald'],
                        ['deduction', 'خصم',    'red'],
                        ['advance',   'سلفة',   'amber'],
                        ['bonus',     'مكافأة', 'violet'],
                    ] as [$val, $lbl, $clr])
                    <button type="button" data-type="{{ $val }}"
                            onclick="selectEditType('{{ $val }}')"
                            class="edit-type-btn py-2 rounded-xl border-2 text-xs font-bold transition-all
                                   border-gray-100 bg-white text-gray-400 hover:border-gray-200 hover:bg-gray-50">
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="type" id="edit-tx-type" value="salary">
            </div>

            {{-- Amount + currency --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">المبلغ</label>
                <div class="flex gap-2">
                    <input type="number" name="amount" id="edit-tx-amount" min="0.01" step="0.01" required placeholder="0"
                           class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-bold focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50">
                    <div class="flex rounded-xl border border-gray-200 overflow-hidden text-xs font-bold shrink-0">
                        <label class="flex items-center justify-center px-3 py-2.5 cursor-pointer transition-colors has-[:checked]:bg-emerald-600 has-[:checked]:text-white text-gray-500 hover:bg-gray-50">
                            <input type="radio" name="currency" value="USD" id="edit-cur-usd" class="sr-only">
                            $
                        </label>
                        <label class="flex items-center justify-center px-3 py-2.5 cursor-pointer transition-colors has-[:checked]:bg-slate-700 has-[:checked]:text-white text-gray-500 hover:bg-gray-50 border-r border-gray-200">
                            <input type="radio" name="currency" value="SYP" id="edit-cur-syp" class="sr-only">
                            ل.س
                        </label>
                    </div>
                </div>
            </div>

            {{-- Reason --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">السبب / الوصف</label>
                <textarea name="reason" id="edit-tx-reason" rows="2"
                          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50 resize-none"></textarea>
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">التاريخ</label>
                <input type="date" name="transaction_date" id="edit-tx-date" required
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50">
            </div>

            <div class="flex gap-3 pt-1 border-t border-gray-100">
                <button type="submit" id="edit-tx-submit"
                        class="flex-1 bg-slate-700 hover:bg-slate-800 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                    حفظ التعديلات
                </button>
                <button type="button" onclick="document.getElementById('modal-edit-tx').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const typeConfig = {
    salary:    {
        amountLabel: 'قيمة الراتب (ل.س)',
        submitLabel: 'تسجيل الراتب',
        submitClass: 'bg-blue-600 hover:bg-blue-700',
        placeholder: 'راتب شهر...',
        cardClass:   'border-blue-400 bg-blue-50 text-blue-700',
        iconClass:   'bg-blue-100',
    },
    addition:  {
        amountLabel: 'قيمة الإضافة (ل.س)',
        submitLabel: 'تسجيل الإضافة',
        submitClass: 'bg-emerald-600 hover:bg-emerald-700',
        placeholder: 'سبب الإضافة...',
        cardClass:   'border-emerald-400 bg-emerald-50 text-emerald-700',
        iconClass:   'bg-emerald-100',
    },
    deduction: {
        amountLabel: 'قيمة الخصم (ل.س)',
        submitLabel: 'تسجيل الخصم',
        submitClass: 'bg-red-600 hover:bg-red-700',
        placeholder: 'سبب الخصم...',
        cardClass:   'border-red-400 bg-red-50 text-red-700',
        iconClass:   'bg-red-100',
    },
    advance:   {
        amountLabel: 'قيمة السلفة (ل.س)',
        submitLabel: 'تسجيل السلفة',
        submitClass: 'bg-amber-500 hover:bg-amber-600',
        placeholder: 'ملاحظة السلفة...',
        cardClass:   'border-amber-400 bg-amber-50 text-amber-700',
        iconClass:   'bg-amber-100',
    },
    bonus:     {
        amountLabel: 'قيمة المكافأة',
        submitLabel: 'تسجيل المكافأة',
        submitClass: 'bg-violet-600 hover:bg-violet-700',
        placeholder: 'سبب المكافأة...',
        cardClass:   'border-violet-400 bg-violet-50 text-violet-700',
        iconClass:   'bg-violet-100',
    },
};

const BASE_CARD = 'type-card flex items-center gap-2.5 px-3 py-3 rounded-xl border-2 transition-all cursor-pointer text-right';
const INACTIVE  = 'border-gray-100 bg-white text-gray-400 hover:border-gray-200 hover:bg-gray-50';

function selectType(type) {
    const cfg = typeConfig[type];

    // Update hidden input
    document.getElementById('tx-type').value = type;
    document.getElementById('amount-label').textContent = cfg.amountLabel;
    document.getElementById('reason-input').placeholder = cfg.placeholder;

    // Update submit button
    const btn = document.getElementById('submit-btn');
    btn.className = `w-full text-white text-sm font-bold py-3 rounded-xl transition-colors flex items-center justify-center gap-2 ${cfg.submitClass}`;
    document.getElementById('submit-label').textContent = cfg.submitLabel;

    // Update cards
    document.querySelectorAll('.type-card').forEach(card => {
        const icon = card.querySelector('div');
        if (card.dataset.type === type) {
            card.className = `${BASE_CARD} ${cfg.cardClass}`;
            if (icon) icon.className = `w-8 h-8 rounded-lg ${cfg.iconClass} flex items-center justify-center shrink-0`;
        } else {
            card.className = `${BASE_CARD} ${INACTIVE}`;
            if (icon) icon.className = 'w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0';
        }
    });
}

document.getElementById('modal-edit').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.getElementById('modal-edit-tx').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});

const editTypeColors = {
    salary:    { border: 'border-blue-400',    bg: 'bg-blue-50',    text: 'text-blue-700',    submit: 'bg-blue-600 hover:bg-blue-700' },
    addition:  { border: 'border-emerald-400', bg: 'bg-emerald-50', text: 'text-emerald-700', submit: 'bg-emerald-600 hover:bg-emerald-700' },
    deduction: { border: 'border-red-400',     bg: 'bg-red-50',     text: 'text-red-700',     submit: 'bg-red-600 hover:bg-red-700' },
    advance:   { border: 'border-amber-400',   bg: 'bg-amber-50',   text: 'text-amber-700',   submit: 'bg-amber-500 hover:bg-amber-600' },
    bonus:     { border: 'border-violet-400',  bg: 'bg-violet-50',  text: 'text-violet-700',  submit: 'bg-violet-600 hover:bg-violet-700' },
};

function selectEditType(type) {
    document.getElementById('edit-tx-type').value = type;
    const clr = editTypeColors[type];
    document.querySelectorAll('.edit-type-btn').forEach(btn => {
        if (btn.dataset.type === type) {
            btn.className = `edit-type-btn py-2 rounded-xl border-2 text-xs font-bold transition-all ${clr.border} ${clr.bg} ${clr.text}`;
        } else {
            btn.className = 'edit-type-btn py-2 rounded-xl border-2 text-xs font-bold transition-all border-gray-100 bg-white text-gray-400 hover:border-gray-200 hover:bg-gray-50';
        }
    });
    const submit = document.getElementById('edit-tx-submit');
    submit.className = `flex-1 text-white text-sm font-bold py-2.5 rounded-xl transition-colors ${clr.submit}`;
}

function openEditTx(tx) {
    document.getElementById('edit-tx-form').action = tx.url;
    document.getElementById('edit-tx-amount').value = tx.amount;
    document.getElementById('edit-tx-reason').value = tx.reason || '';
    document.getElementById('edit-tx-date').value = tx.date;
    document.getElementById('edit-cur-usd').checked = true;
    document.getElementById('edit-cur-syp').checked = false;
    selectEditType(tx.type);
    document.getElementById('modal-edit-tx').classList.remove('hidden');
}
</script>

@endsection
