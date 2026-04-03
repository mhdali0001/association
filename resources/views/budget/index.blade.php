@extends('layouts.app')

@section('title', 'الميزانية والمدفوعات — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">الميزانية والمدفوعات</span>
@endsection

@section('content')

@php
    $fmt = fn($n) => number_format((float)$n, 0, '.', ',');
@endphp

<style>
.progress-bar-fill { transition: width 0.8s ease-in-out; }
</style>

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-emerald-700 via-teal-600 to-cyan-500 rounded-3xl p-7 mb-8 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-8 -left-8 w-48 h-48 bg-white rounded-full"></div>
        <div class="absolute -bottom-12 left-24 w-64 h-64 bg-white rounded-full"></div>
        <div class="absolute top-6 right-16 w-24 h-24 bg-white rounded-full"></div>
    </div>
    <div class="relative flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white mb-1">الميزانية والمدفوعات</h1>
            <p class="text-teal-100 text-sm">نظرة شاملة على الصرف والمستفيدين</p>
        </div>
        {{-- Set Budget Form --}}
        <form method="POST" action="{{ route('budget.set-total') }}" class="flex items-center gap-2">
            @csrf
            <div class="relative">
                <input type="number" name="total_amount" value="{{ $budget->total_amount }}" min="0" step="1"
                       placeholder="إجمالي الميزانية"
                       class="bg-white/20 text-white placeholder-teal-200 border-2 border-white/30 rounded-xl px-4 py-2.5 text-sm w-52 focus:outline-none focus:border-white">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-teal-200 text-xs">ل.س</span>
            </div>
            <button type="submit"
                    class="bg-white text-teal-700 hover:bg-teal-50 font-bold px-4 py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                تحديث الميزانية
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-5 py-3.5 mb-6 text-sm font-medium flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- Overview Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">

    {{-- Total Budget --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 overflow-hidden relative">
        <div class="absolute -bottom-3 -left-3 w-20 h-20 bg-teal-50 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 bg-teal-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">إجمالي الميزانية</p>
            </div>
            <p class="text-3xl font-black text-gray-800 leading-none">{{ $fmt($budget->total_amount) }}</p>
            <p class="text-xs text-gray-400 mt-1">ليرة سورية</p>
        </div>
    </div>

    {{-- Total Spent (clickable) --}}
    <button onclick="toggleSection('payments-section')"
            class="bg-white rounded-2xl border-2 border-red-100 shadow-sm p-6 overflow-hidden relative text-right hover:border-red-300 hover:shadow-md transition-all group cursor-pointer">
        <div class="absolute -bottom-3 -left-3 w-20 h-20 bg-red-50 rounded-full"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">تم صرفه</p>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-red-400 ms-auto transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <p class="text-3xl font-black text-red-600 leading-none">{{ $fmt($totalSpent) }}</p>
            <p class="text-xs text-red-400 mt-1">{{ $expenses->count() }} عملية دفع · اضغط للتفاصيل</p>
        </div>
    </button>

    {{-- Remaining (clickable) --}}
    <button onclick="toggleSection('beneficiaries-section')"
            class="bg-white rounded-2xl border-2 {{ $remaining >= 0 ? 'border-emerald-100' : 'border-red-200' }} shadow-sm p-6 overflow-hidden relative text-right hover:border-emerald-300 hover:shadow-md transition-all group cursor-pointer">
        <div class="absolute -bottom-3 -left-3 w-20 h-20 {{ $remaining >= 0 ? 'bg-emerald-50' : 'bg-red-50' }} rounded-full"></div>
        <div class="relative">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-9 h-9 {{ $remaining >= 0 ? 'bg-emerald-100' : 'bg-red-100' }} rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $remaining >= 0 ? 'text-emerald-600' : 'text-red-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-500 font-medium">المتبقي</p>
                <svg class="w-4 h-4 text-gray-300 group-hover:text-emerald-400 ms-auto transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
            <p class="text-3xl font-black {{ $remaining >= 0 ? 'text-emerald-600' : 'text-red-600' }} leading-none">{{ $fmt(abs($remaining)) }}</p>
            <p class="text-xs {{ $remaining >= 0 ? 'text-emerald-400' : 'text-red-400' }} mt-1">
                {{ $remaining >= 0 ? 'متبقٍ · اضغط لعرض المستفيدين' : 'تجاوز الميزانية!' }}
            </p>
        </div>
    </button>

</div>

{{-- Progress Bar --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
    <div class="flex items-center justify-between mb-3">
        <span class="text-sm font-semibold text-gray-700">
            تم دفع <span class="text-red-600 font-black">{{ $fmt($totalSpent) }}</span>
            من أصل <span class="text-gray-800 font-black">{{ $fmt($budget->total_amount) }}</span> ل.س
        </span>
        <span class="text-sm font-bold {{ $percent >= 90 ? 'text-red-600' : ($percent >= 70 ? 'text-amber-500' : 'text-emerald-600') }}">
            {{ $percent }}%
        </span>
    </div>
    <div class="h-5 bg-gray-100 rounded-full overflow-hidden">
        <div class="progress-bar-fill h-full rounded-full {{ $percent >= 100 ? 'bg-red-500' : ($percent >= 90 ? 'bg-orange-500' : ($percent >= 70 ? 'bg-amber-400' : 'bg-emerald-500')) }}"
             style="width: {{ min(100,$percent) }}%"></div>
    </div>
    <div class="flex justify-between mt-2 text-xs text-gray-400">
        <span>0</span>
        <span>المتبقي: <strong class="{{ $remaining >= 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $fmt(abs($remaining)) }} ل.س</strong></span>
        <span>{{ $fmt($budget->total_amount) }}</span>
    </div>
</div>

{{-- ===== PAYMENTS SECTION ===== --}}
<div id="payments-section" class="mb-8 hidden">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-1 h-5 bg-gradient-to-b from-red-500 to-rose-600 rounded-full"></div>
        <h2 class="text-lg font-bold text-gray-800">تفاصيل المدفوعات</h2>
        <span class="bg-red-100 text-red-700 text-xs font-bold rounded-full px-2.5 py-1 ms-1">{{ $expenses->count() }} دفعة</span>
        <button onclick="toggleSection('payments-section')" class="ms-auto text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($expenses->isEmpty())
            <div class="p-10 text-center text-gray-400 text-sm">لا توجد مدفوعات مسجلة بعد.</div>
        @else
            {{-- Summary bar --}}
            <div class="px-5 py-3.5 bg-red-50 border-b border-red-100 flex items-center justify-between">
                <span class="text-sm text-red-700 font-medium">إجمالي المدفوعات</span>
                <span class="text-xl font-black text-red-700">{{ $fmt($totalSpent) }} ل.س</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">#</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">العنوان</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">المستلم / الجهة</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">المبلغ</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">التاريخ</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium hidden md:table-cell">ملاحظات</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium hidden lg:table-cell">المستفيد</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($expenses as $i => $expense)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                            <td class="px-5 py-3.5 font-medium text-gray-800">
                                <a href="{{ route('expenses.show', $expense) }}" class="hover:text-red-600 transition-colors">
                                    {{ $expense->title }}
                                </a>
                                @if($expense->category)
                                    <span class="ms-1 text-xs bg-gray-100 text-gray-500 rounded-full px-2 py-0.5">{{ $expense->category }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $expense->recipient ?: '—' }}</td>
                            <td class="px-5 py-3.5 font-bold text-red-600">{{ $fmt($expense->amount) }}</td>
                            <td class="px-5 py-3.5 text-gray-500">{{ $expense->date->format('Y/m/d') }}</td>
                            <td class="px-5 py-3.5 text-gray-400 text-xs hidden md:table-cell max-w-xs truncate">
                                {{ $expense->description ?: '—' }}
                            </td>
                            <td class="px-5 py-3.5 hidden lg:table-cell">
                                @if($expense->beneficiary)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-100">
                                        {{ $expense->beneficiary->name }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ===== BENEFICIARIES SECTION ===== --}}
<div id="beneficiaries-section" class="mb-8 hidden">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-1 h-5 bg-gradient-to-b from-teal-500 to-emerald-600 rounded-full"></div>
        <h2 class="text-lg font-bold text-gray-800">المستفيدون</h2>
        <span class="bg-teal-100 text-teal-700 text-xs font-bold rounded-full px-2.5 py-1 ms-1">{{ $beneficiaries->count() }} مستفيد</span>
        <button onclick="toggleSection('beneficiaries-section')" class="ms-auto text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    @if($beneficiaries->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center text-gray-400 text-sm">
            لم تُضف أي مستفيدين بعد. أضف مستفيدين من القسم أدناه.
        </div>
    @else
        {{-- Status summary --}}
        @php
            $fullyPaid   = $beneficiaries->filter(fn($b) => $b->statusLabel() === 'تم الدفع بالكامل')->count();
            $partialPaid = $beneficiaries->filter(fn($b) => $b->statusLabel() === 'تم الدفع جزئياً')->count();
            $notPaid     = $beneficiaries->filter(fn($b) => $b->statusLabel() === 'لم يتم الدفع')->count();
            $totalAllocated = $beneficiaries->sum('allocated_amount');
            $totalPaidBen   = $beneficiaries->sum(fn($b) => $b->totalPaid());
        @endphp
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-emerald-700">{{ $fullyPaid }}</p>
                <p class="text-xs text-emerald-600 mt-1">تم الدفع بالكامل</p>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-amber-600">{{ $partialPaid }}</p>
                <p class="text-xs text-amber-600 mt-1">تم الدفع جزئياً</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                <p class="text-2xl font-black text-red-600">{{ $notPaid }}</p>
                <p class="text-xs text-red-500 mt-1">لم يتم الدفع</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">المستفيد</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">المبلغ المخصص</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">تم دفعه</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">المتبقي</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium">الحالة</th>
                            <th class="text-right px-5 py-3 text-gray-500 font-medium hidden md:table-cell">ملاحظات</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($beneficiaries as $ben)
                        @php
                            $paid      = $ben->totalPaid();
                            $remaining = $ben->remaining();
                            $status    = $ben->statusLabel();
                            $color     = $ben->statusColor();
                            $benPct    = $ben->allocated_amount > 0 ? min(100, round($paid / $ben->allocated_amount * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 font-semibold text-gray-800">{{ $ben->name }}</td>
                            <td class="px-5 py-4 text-gray-700">{{ $fmt($ben->allocated_amount) }}</td>
                            <td class="px-5 py-4">
                                <div>
                                    <span class="font-bold text-red-600">{{ $fmt($paid) }}</span>
                                    <div class="w-24 h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                        <div class="h-full rounded-full bg-{{ $color }}-500" style="width: {{ $benPct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 font-semibold {{ $remaining > 0 ? 'text-amber-600' : ($remaining == 0 ? 'text-gray-400' : 'text-red-600') }}">
                                {{ $fmt(abs($remaining)) }}
                                @if($remaining < 0) <span class="text-xs text-red-400">(تجاوز)</span> @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                    bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-400 text-xs hidden md:table-cell max-w-xs truncate">
                                {{ $ben->notes ?: '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1 justify-end">
                                    <button onclick="openEditModal({{ $ben->id }}, '{{ addslashes($ben->name) }}', {{ $ben->allocated_amount }}, '{{ addslashes($ben->notes ?? '') }}')"
                                            class="p-1.5 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('beneficiaries.destroy', $ben) }}"
                                          onsubmit="return confirm('هل تريد حذف هذا المستفيد؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
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
                    <tfoot class="bg-gray-50 border-t-2 border-gray-100">
                        <tr>
                            <td class="px-5 py-3.5 font-bold text-gray-700">الإجمالي</td>
                            <td class="px-5 py-3.5 font-bold text-gray-800">{{ $fmt($totalAllocated) }}</td>
                            <td class="px-5 py-3.5 font-bold text-red-600">{{ $fmt($totalPaidBen) }}</td>
                            <td class="px-5 py-3.5 font-bold text-amber-600">{{ $fmt($totalAllocated - $totalPaidBen) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- ===== MANAGE BENEFICIARIES ===== --}}
<div class="flex items-center gap-2 mb-4 mt-8">
    <div class="w-1 h-5 bg-gradient-to-b from-indigo-500 to-violet-600 rounded-full"></div>
    <h2 class="text-lg font-bold text-gray-800">إدارة المستفيدين</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Add Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2">
            <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            إضافة مستفيد جديد
        </h3>
        <form method="POST" action="{{ route('beneficiaries.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الاسم <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-400 @enderror"
                       placeholder="اسم الشخص أو الجهة">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">المبلغ المخصص (ل.س) <span class="text-red-500">*</span></label>
                <input type="number" name="allocated_amount" value="{{ old('allocated_amount') }}" required min="0" step="1"
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('allocated_amount') border-red-400 @enderror"
                       placeholder="0">
                @error('allocated_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">ملاحظات</label>
                <textarea name="notes" rows="2"
                          class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="ملاحظات اختيارية...">{{ old('notes') }}</textarea>
            </div>
            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                إضافة المستفيد
            </button>
        </form>
    </div>

    {{-- Beneficiaries quick list --}}
    <div class="lg:col-span-2">
        @if($beneficiaries->isEmpty())
            <div class="bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 p-10 text-center">
                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-sm">لم تُضف أي مستفيدين بعد</p>
                <p class="text-gray-300 text-xs mt-1">أضف مستفيداً من النموذج بالجانب</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($beneficiaries as $ben)
                @php
                    $paid   = $ben->totalPaid();
                    $alloc  = (float) $ben->allocated_amount;
                    $benPct = $alloc > 0 ? min(100, round($paid / $alloc * 100)) : 0;
                    $color  = $ben->statusColor();
                @endphp
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 hover:border-gray-200 transition-colors">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div>
                            <p class="font-bold text-gray-800">{{ $ben->name }}</p>
                            @if($ben->notes)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $ben->notes }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                            bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100">
                            {{ $ben->statusLabel() }}
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center mb-3">
                        <div class="bg-gray-50 rounded-lg p-2">
                            <p class="text-xs text-gray-400 mb-0.5">مخصص</p>
                            <p class="font-bold text-gray-700 text-sm">{{ $fmt($alloc) }}</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-2">
                            <p class="text-xs text-red-400 mb-0.5">مدفوع</p>
                            <p class="font-bold text-red-600 text-sm">{{ $fmt($paid) }}</p>
                        </div>
                        <div class="bg-{{ $ben->remaining() >= 0 ? 'amber' : 'red' }}-50 rounded-lg p-2">
                            <p class="text-xs text-{{ $ben->remaining() >= 0 ? 'amber' : 'red' }}-400 mb-0.5">متبقي</p>
                            <p class="font-bold text-{{ $ben->remaining() >= 0 ? 'amber' : 'red' }}-600 text-sm">{{ $fmt(abs($ben->remaining())) }}</p>
                        </div>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-{{ $color }}-500 transition-all" style="width: {{ $benPct }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 text-left">{{ $benPct }}%</p>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-modal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-gray-800 text-lg">تعديل المستفيد</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الاسم <span class="text-red-500">*</span></label>
                <input type="text" id="edit-name" name="name" required
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">المبلغ المخصص (ل.س) <span class="text-red-500">*</span></label>
                <input type="number" id="edit-amount" name="allocated_amount" required min="0" step="1"
                       class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">ملاحظات</label>
                <textarea id="edit-notes" name="notes" rows="2"
                          class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            <div class="flex gap-3 mt-2">
                <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                    حفظ التعديلات
                </button>
                <button type="button" onclick="closeEditModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 rounded-xl text-sm transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSection(id) {
    const el = document.getElementById(id);
    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        el.classList.add('hidden');
    }
}

function openEditModal(id, name, amount, notes) {
    document.getElementById('edit-name').value   = name;
    document.getElementById('edit-amount').value = amount;
    document.getElementById('edit-notes').value  = notes;
    document.getElementById('edit-form').action  = '/beneficiaries/' + id;
    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>

@endsection
