@extends('layouts.app')

@section('title', 'الموظفون — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">الموظفون</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Hero --}}
<div class="relative rounded-3xl overflow-hidden shadow-2xl mb-8" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0f172a 100%)">
    {{-- Decorative blobs --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-16 -right-16 w-72 h-72 rounded-full opacity-10" style="background:radial-gradient(circle,#6366f1,transparent)"></div>
        <div class="absolute -bottom-24 -left-10 w-96 h-96 rounded-full opacity-10" style="background:radial-gradient(circle,#06b6d4,transparent)"></div>
        <div class="absolute top-0 left-0 right-0 h-px" style="background:linear-gradient(90deg,transparent,rgba(255,255,255,0.12),transparent)"></div>
    </div>

    <div class="relative px-8 pt-8 pb-7">
        {{-- Title row --}}
        <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
            <div>
                <p class="text-xs font-bold tracking-[0.2em] uppercase mb-2" style="color:#94a3b8">إدارة الكوادر البشرية</p>
                <h1 class="text-4xl font-black text-white leading-none tracking-tight">الموظفون</h1>
            </div>
            <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                    class="group flex items-center gap-2.5 text-sm font-bold px-5 py-3 rounded-2xl transition-all duration-200 shadow-lg"
                    style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;box-shadow:0 4px 24px rgba(99,102,241,0.4)">
                <svg class="w-4 h-4 transition-transform group-hover:rotate-90 duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                إضافة موظف
            </button>
        </div>

        {{-- Stats row --}}
        @php
            $globalNetSYP  = $totalPaidSYP - $totalDeductedSYP - $totalAdvancesSYP;
            $globalNetUSD  = $totalPaidUSD - $totalDeductedUSD - $totalAdvancesUSD;
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">

            {{-- Total employees --}}
            <div class="rounded-2xl border px-5 py-4" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.08)">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold tracking-widest uppercase" style="color:#64748b">إجمالي</span>
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.08)">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-black text-white leading-none mb-1">{{ $employees->count() }}</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(52,211,153,0.15);color:#34d399">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span>
                        {{ $employees->where('is_active', true)->count() }} نشط
                    </span>
                    <span class="text-[11px]" style="color:#475569">· {{ $employees->where('is_active', false)->count() }} متوقف</span>
                </div>
            </div>

            {{-- Total paid --}}
            <div class="rounded-2xl border px-5 py-4" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.08)">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold tracking-widest uppercase" style="color:#64748b">المدفوع</span>
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(52,211,153,0.15)">
                        <svg class="w-3.5 h-3.5" style="color:#34d399" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    </div>
                </div>
                @if($totalPaidSYP > 0)
                    <p class="text-xl font-black leading-tight" style="color:#34d399">{{ number_format($totalPaidSYP) }} <span class="text-xs font-normal" style="color:#64748b">ل.س</span></p>
                @endif
                @if($totalPaidUSD > 0)
                    <p class="text-xl font-black leading-tight" style="color:#34d399">{{ number_format($totalPaidUSD, 2) }} <span class="text-xs font-normal" style="color:#64748b">$</span></p>
                @endif
                @if($totalPaidSYP == 0 && $totalPaidUSD == 0)
                    <p class="text-2xl font-black text-white leading-none">—</p>
                @endif
                <p class="text-[11px] mt-2" style="color:#475569">رواتب · إضافات · مكافآت</p>
            </div>

            {{-- Deductions --}}
            <div class="rounded-2xl border px-5 py-4" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.08)">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold tracking-widest uppercase" style="color:#64748b">الخصومات</span>
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(248,113,113,0.15)">
                        <svg class="w-3.5 h-3.5" style="color:#f87171" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    </div>
                </div>
                @if($totalDeductedSYP > 0)
                    <p class="text-xl font-black leading-tight" style="color:#f87171">{{ number_format($totalDeductedSYP) }} <span class="text-xs font-normal" style="color:#64748b">ل.س</span></p>
                @endif
                @if($totalDeductedUSD > 0)
                    <p class="text-xl font-black leading-tight" style="color:#f87171">{{ number_format($totalDeductedUSD, 2) }} <span class="text-xs font-normal" style="color:#64748b">$</span></p>
                @endif
                @if($totalDeductedSYP == 0 && $totalDeductedUSD == 0)
                    <p class="text-2xl font-black text-white leading-none">—</p>
                @endif
                <p class="text-[11px] mt-2" style="color:#475569">خصومات فقط</p>
            </div>

            {{-- Advances --}}
            <div class="rounded-2xl border px-5 py-4" style="background:rgba(245,158,11,0.1);border-color:rgba(245,158,11,0.25)">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold tracking-widest uppercase" style="color:#92400e">السلف</span>
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(245,158,11,0.2)">
                        <svg class="w-3.5 h-3.5" style="color:#fbbf24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    </div>
                </div>
                @if($totalAdvancesSYP > 0)
                    <p class="text-xl font-black leading-tight" style="color:#fbbf24">{{ number_format($totalAdvancesSYP) }} <span class="text-xs font-normal" style="color:#64748b">ل.س</span></p>
                @endif
                @if($totalAdvancesUSD > 0)
                    <p class="text-xl font-black leading-tight" style="color:#fbbf24">{{ number_format($totalAdvancesUSD, 2) }} <span class="text-xs font-normal" style="color:#64748b">$</span></p>
                @endif
                @if($totalAdvancesSYP == 0 && $totalAdvancesUSD == 0)
                    <p class="text-2xl font-black text-white leading-none">—</p>
                @endif
                <p class="text-[11px] mt-2" style="color:#d97706">السلف المدفوعة</p>
            </div>

            {{-- Net remaining from payments --}}
            <div class="rounded-2xl border px-5 py-4" style="background:rgba(99,102,241,0.12);border-color:rgba(99,102,241,0.25)">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold tracking-widest uppercase" style="color:#818cf8">المتبقي</span>
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(99,102,241,0.2)">
                        <svg class="w-3.5 h-3.5" style="color:#818cf8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                @if($globalNetSYP != 0 || ($totalPaidSYP == 0 && $totalPaidUSD == 0))
                    <p class="text-xl font-black leading-tight" style="color:{{ $globalNetSYP >= 0 ? '#a5b4fc' : '#f87171' }}">
                        {{ $globalNetSYP < 0 ? '−' : '' }}{{ number_format(abs($globalNetSYP)) }}
                        <span class="text-xs font-normal" style="color:#64748b">ل.س</span>
                    </p>
                @endif
                @if($globalNetUSD != 0)
                    <p class="text-xl font-black leading-tight" style="color:{{ $globalNetUSD >= 0 ? '#a5b4fc' : '#f87171' }}">
                        {{ $globalNetUSD < 0 ? '−' : '' }}{{ number_format(abs($globalNetUSD), 2) }}
                        <span class="text-xs font-normal" style="color:#64748b">$</span>
                    </p>
                @endif
                <p class="text-[11px] mt-2" style="color:#6366f1">المتبقي من الدفعات</p>
            </div>

            {{-- True net (paid - deductions - advances) per employee avg --}}
            <div class="rounded-2xl border px-5 py-4" style="background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.08)">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold tracking-widest uppercase" style="color:#64748b">الإنفاق الكلي</span>
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(255,255,255,0.08)">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                @php
                    $totalSpentSYP = $totalPaidSYP + $totalAdvancesSYP;
                    $totalSpentUSD = $totalPaidUSD + $totalAdvancesUSD;
                @endphp
                @if($totalSpentSYP > 0)
                    <p class="text-xl font-black leading-tight text-white">{{ number_format($totalSpentSYP) }} <span class="text-xs font-normal" style="color:#64748b">ل.س</span></p>
                @endif
                @if($totalSpentUSD > 0)
                    <p class="text-xl font-black leading-tight text-white">{{ number_format($totalSpentUSD, 2) }} <span class="text-xs font-normal" style="color:#64748b">$</span></p>
                @endif
                @if($totalSpentSYP == 0 && $totalSpentUSD == 0)
                    <p class="text-2xl font-black text-white leading-none">—</p>
                @endif
                <p class="text-[11px] mt-2" style="color:#475569">مدفوع + سلف</p>
            </div>

        </div>
    </div>
</div>

@if($employees->isEmpty())
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm text-center py-32">
        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p class="text-gray-700 font-bold text-lg mb-1">لا يوجد موظفون بعد</p>
        <p class="text-gray-400 text-sm mb-6">ابدأ بإضافة أول موظف للفريق</p>
        <button onclick="document.getElementById('modal-create').classList.remove('hidden')"
                class="inline-flex items-center gap-2 text-white text-sm font-bold px-6 py-3 rounded-2xl transition-colors"
                style="background:linear-gradient(135deg,#6366f1,#4f46e5)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            إضافة موظف
        </button>
    </div>
@else

{{-- Toolbar --}}
<div class="flex items-center gap-3 mb-5">
    <div class="relative flex-1">
        <span class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
        </span>
        <input type="text" id="emp-search" placeholder="ابحث باسم الموظف أو المسمى الوظيفي..."
               class="w-full pr-11 pl-4 py-3 bg-white border border-gray-200 rounded-2xl text-sm shadow-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 focus:outline-none transition-all"
               oninput="filterCards(this.value)">
    </div>
    <div class="shrink-0 bg-white border border-gray-200 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-500 shadow-sm" id="visible-count">
        {{ $employees->count() }} موظف
    </div>
</div>

{{-- Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="emp-grid">
    @foreach($employees as $employee)
    @php
        $netSYP   = $employee->netBalance('SYP');
        $netUSD   = $employee->netBalance('USD');
        $hasSYP   = $employee->hasCurrency('SYP');
        $hasUSD   = $employee->hasCurrency('USD');
        $hasAny   = $hasSYP || $hasUSD;
        $positive = !$hasAny || ((!$hasSYP || $netSYP >= 0) && (!$hasUSD || $netUSD >= 0));
    @endphp
    <a href="{{ route('employees.show', $employee) }}"
       data-name="{{ mb_strtolower($employee->name . ' ' . $employee->job_title) }}"
       class="emp-card group relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-200 overflow-hidden flex flex-col">

        {{-- Top accent bar --}}
        <div class="h-0.5 w-full {{ $employee->is_active ? '' : 'bg-gray-200' }}"
             style="{{ $employee->is_active ? 'background:linear-gradient(90deg,#6366f1,#06b6d4)' : '' }}"></div>

        <div class="p-5 flex-1 flex flex-col">

            {{-- Header --}}
            <div class="flex items-start justify-between mb-5">
                <div class="flex items-center gap-3">
                    {{-- Avatar --}}
                    <div class="relative shrink-0">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white font-black text-lg shadow-sm"
                             style="{{ $employee->is_active ? 'background:linear-gradient(135deg,#6366f1,#4f46e5)' : 'background:#e2e8f0;color:#94a3b8' }}">
                            {{ mb_substr($employee->name, 0, 1) }}
                        </div>
                        @if($employee->is_active)
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2 border-white shadow-sm"></span>
                        @endif
                    </div>
                    {{-- Name --}}
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 text-sm leading-tight truncate">{{ $employee->name }}</p>
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ $employee->job_title ?: '—' }}</p>
                    </div>
                </div>
                {{-- Status badge --}}
                <span class="shrink-0 text-[10px] font-bold px-2 py-1 rounded-lg {{ $employee->is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-gray-100 text-gray-400 border border-gray-200' }}">
                    {{ $employee->is_active ? 'نشط' : 'متوقف' }}
                </span>
            </div>

            {{-- Balance block --}}
            <div class="flex-1 rounded-xl px-4 py-4 mb-4 flex flex-col items-center justify-center text-center
                        {{ $hasAny ? ($positive ? '' : '') : '' }}"
                 style="{{ $hasAny ? ($positive ? 'background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0' : 'background:linear-gradient(135deg,#fff1f2,#fee2e2);border:1px solid #fecaca') : 'background:#f8fafc;border:1px solid #e2e8f0' }}">

                <p class="text-[9px] font-black uppercase tracking-[0.2em] mb-3
                           {{ $hasAny ? ($positive ? 'text-emerald-500' : 'text-red-400') : 'text-gray-400' }}">
                    الرصيد المتبقي
                </p>

                @if(!$hasAny)
                    <p class="text-2xl font-black text-gray-300">—</p>
                    <p class="text-[11px] text-gray-400 mt-1.5">لا توجد معاملات</p>
                @else
                    @if($hasSYP)
                    <div class="flex items-baseline gap-1 leading-none">
                        <span class="text-[11px] font-bold {{ $netSYP >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $netSYP >= 0 ? '+' : '−' }}</span>
                        <span class="text-3xl font-black tracking-tight {{ $netSYP >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format(abs($netSYP)) }}</span>
                        <span class="text-xs font-bold {{ $netSYP >= 0 ? 'text-emerald-500' : 'text-red-400' }}">ل.س</span>
                    </div>
                    @endif
                    @if($hasUSD)
                    <div class="flex items-baseline gap-1 leading-none {{ $hasSYP ? 'mt-2.5' : '' }}">
                        <span class="text-[11px] font-bold {{ $netUSD >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ $netUSD >= 0 ? '+' : '−' }}</span>
                        <span class="text-3xl font-black tracking-tight {{ $netUSD >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format(abs($netUSD), 2) }}</span>
                        <span class="text-xs font-bold {{ $netUSD >= 0 ? 'text-emerald-500' : 'text-red-400' }}">$</span>
                    </div>
                    @endif
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                @if($employee->base_salary > 0)
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-md bg-indigo-50 flex items-center justify-center shrink-0">
                        <svg class="w-2.5 h-2.5 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <span class="text-[11px] text-gray-400">
                        <span class="font-semibold text-gray-600">
                            {{ ($employee->base_salary_currency ?? 'SYP') === 'USD' ? number_format((float)$employee->base_salary, 2).'$' : number_format((float)$employee->base_salary).' ل.س' }}
                        </span>
                        /شهر
                    </span>
                </div>
                @else
                <span></span>
                @endif
                <div class="flex items-center gap-1 text-[11px] text-gray-400">
                    <span class="font-semibold text-gray-500">{{ $employee->transactions_count }}</span>
                    عملية
                    <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </div>
            </div>

        </div>
    </a>
    @endforeach
</div>

{{-- No results --}}
<div id="no-results" class="hidden text-center py-20">
    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
    </div>
    <p class="text-gray-500 font-semibold text-sm">لا توجد نتائج</p>
    <p class="text-gray-400 text-xs mt-1">جرّب كلمة بحث مختلفة</p>
</div>
@endif

{{-- Modal: إضافة موظف --}}
<div id="modal-create" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4" style="background:rgba(0,0,0,0.6);backdrop-filter:blur(4px)">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-black text-gray-900">موظف جديد</h2>
                <p class="text-xs text-gray-400 mt-0.5">أدخل بيانات الموظف</p>
            </div>
            <button onclick="document.getElementById('modal-create').classList.add('hidden')"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('employees.store') }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">الاسم <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="اسم الموظف الكامل"
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 focus:outline-none bg-gray-50 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">المسمى الوظيفي</label>
                    <input type="text" name="job_title" placeholder="مثال: محاسب"
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 focus:outline-none bg-gray-50 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">القسم / الإدارة</label>
                    <input type="text" name="department" placeholder="مثال: المالية"
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 focus:outline-none bg-gray-50 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">الهاتف</label>
                    <input type="text" name="phone" placeholder="رقم الهاتف"
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 focus:outline-none bg-gray-50 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">تاريخ التعيين</label>
                    <input type="date" name="hire_date"
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 focus:outline-none bg-gray-50 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">الراتب الأساسي</label>
                    <input type="number" name="base_salary" min="0" placeholder="0"
                           class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 focus:outline-none bg-gray-50 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">العملة</label>
                    <div class="flex rounded-xl border border-gray-200 overflow-hidden text-sm font-bold">
                        <label class="flex-1 flex items-center justify-center gap-1.5 py-2.5 cursor-pointer has-[:checked]:bg-emerald-600 has-[:checked]:text-white text-gray-500 hover:bg-gray-50 transition-colors">
                            <input type="radio" name="base_salary_currency" value="USD" checked class="sr-only">
                            $ دولار
                        </label>
                        <label class="flex-1 flex items-center justify-center gap-1.5 py-2.5 cursor-pointer has-[:checked]:bg-slate-700 has-[:checked]:text-white text-gray-500 hover:bg-gray-50 transition-colors border-r border-gray-200">
                            <input type="radio" name="base_salary_currency" value="SYP" class="sr-only">
                            ل.س
                        </label>
                    </div>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">ملاحظات</label>
                    <textarea name="notes" rows="2" placeholder="ملاحظات اختيارية..."
                              class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-300 focus:outline-none bg-gray-50 resize-none transition-all"></textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 text-white text-sm font-bold py-3 rounded-xl transition-all shadow-sm hover:shadow-md"
                        style="background:linear-gradient(135deg,#6366f1,#4f46e5)">
                    إضافة الموظف
                </button>
                <button type="button" onclick="document.getElementById('modal-create').classList.add('hidden')"
                        class="px-5 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let totalCount = {{ $employees->count() }};

function filterCards(q) {
    q = (q || '').toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('.emp-card').forEach(card => {
        const match = !q || (card.dataset.name || '').includes(q);
        card.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    const noRes = document.getElementById('no-results');
    const countEl = document.getElementById('visible-count');
    if (noRes) noRes.classList.toggle('hidden', visible > 0);
    if (countEl) countEl.textContent = (visible === totalCount ? totalCount : visible + ' من ' + totalCount) + ' موظف';
}

document.getElementById('modal-create').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('modal-create').classList.add('hidden');
    }
});
</script>

@endsection
