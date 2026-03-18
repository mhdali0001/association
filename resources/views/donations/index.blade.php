@extends('layouts.app')

@section('title', 'التبرعات — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">التبرعات</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">التبرعات</h1>
        <p class="text-sm text-gray-400 mt-0.5">سجل جميع التبرعات الصادرة للأعضاء</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('donations.monthly') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            متابعة شهرية
        </a>
        <a href="{{ route('donations.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
            + تسجيل تبرع
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('donations.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="اسم العضو…"
               class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        <input type="month" name="month" value="{{ $month }}"
               class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        <select name="type" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="">كل الطرق</option>
            <option value="manual"    {{ $type === 'manual'    ? 'selected' : '' }}>يدوي</option>
            <option value="sham_cash" {{ $type === 'sham_cash' ? 'selected' : '' }}>شام كاش</option>
        </select>
        <select name="status" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="">كل الحالات</option>
            <option value="paid"      {{ $status === 'paid'      ? 'selected' : '' }}>مدفوع</option>
            <option value="pending"   {{ $status === 'pending'   ? 'selected' : '' }}>معلّق</option>
            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
        </select>
    </div>
    <div class="flex gap-2 mt-3">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">تطبيق</button>
        <a href="{{ route('donations.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-5 py-2 rounded-xl transition-colors">مسح</a>
    </div>
</form>

{{-- Total --}}
<div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-6 py-4 mb-6 flex items-center justify-between">
    <span class="text-sm text-emerald-700 font-medium">إجمالي التبرعات المعروضة</span>
    <span class="text-2xl font-bold text-emerald-700">{{ number_format($total, 2) }} ل.س</span>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($donations->isEmpty())
        <div class="p-10 text-center">
            <p class="text-gray-400 text-sm">لا توجد تبرعات مسجّلة. <a href="{{ route('donations.create') }}" class="text-emerald-600 underline">سجّل أول تبرع</a>.</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium">العضو</th>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium">المبلغ</th>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium">الشهر</th>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium hidden md:table-cell">الطريقة</th>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium">الحالة</th>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium hidden lg:table-cell">رقم العملية</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($donations as $donation)
                @php
                    $color = \App\Models\Donation::statusColor($donation->status);
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5 font-medium text-gray-800">{{ $donation->member->full_name }}</td>
                    <td class="px-5 py-3.5 font-semibold text-emerald-700">{{ number_format($donation->amount, 2) }}</td>
                    <td class="px-5 py-3.5 text-gray-500">{{ $donation->donation_month->format('Y/m') }}</td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        @if($donation->type === 'sham_cash')
                            <span class="text-xs bg-blue-100 text-blue-700 rounded-full px-2.5 py-1">شام كاش</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2.5 py-1">يدوي</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs bg-{{ $color }}-100 text-{{ $color }}-700 rounded-full px-2.5 py-1 font-medium">
                            {{ \App\Models\Donation::statusLabel($donation->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-400 font-mono text-xs hidden lg:table-cell">
                        {{ $donation->reference_number ?: '—' }}
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-1 justify-end">
                            <a href="{{ route('donations.edit', $donation) }}"
                               class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('donations.destroy', $donation) }}"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا التبرع؟')">
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
        </table>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $donations->links() }}
        </div>
    @endif
</div>

@endsection
