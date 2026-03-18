@extends('layouts.app')

@section('title', 'المصروفات — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">المصروفات</span>
@endsection

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">المصروفات</h1>
        <p class="text-sm text-gray-400 mt-0.5">إدارة وتتبع مصروفات الجمعية</p>
    </div>
    <a href="{{ route('expenses.create') }}"
       class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
        + إضافة مصروف
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('expenses.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="بحث في العنوان أو الوصف…"
               class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        <select name="category" class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="">كل الفئات</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ $from }}"
               class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        <input type="date" name="to" value="{{ $to }}"
               class="border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
    </div>
    <div class="flex gap-2 mt-3">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2 rounded-xl transition-colors">تطبيق</button>
        <a href="{{ route('expenses.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-5 py-2 rounded-xl transition-colors">مسح</a>
    </div>
</form>

{{-- Total --}}
<div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-6 py-4 mb-6 flex items-center justify-between">
    <span class="text-sm text-emerald-700 font-medium">إجمالي المصروفات المعروضة</span>
    <span class="text-2xl font-bold text-emerald-700">{{ number_format($total, 2) }} ل.س</span>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($expenses->isEmpty())
        <div class="p-10 text-center">
            <p class="text-gray-400 text-sm">لا توجد مصروفات. <a href="{{ route('expenses.create') }}" class="text-emerald-600 underline">أضف أول مصروف</a>.</p>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium">العنوان</th>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium">الفئة</th>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium">المبلغ</th>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium hidden md:table-cell">الجهة المستفيدة</th>
                    <th class="text-right px-5 py-3 text-gray-500 font-medium">التاريخ</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($expenses as $expense)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5 font-medium text-gray-800">
                        <a href="{{ route('expenses.show', $expense) }}" class="hover:text-emerald-700 transition-colors">
                            {{ $expense->title }}
                        </a>
                    </td>
                    <td class="px-5 py-3.5">
                        @if($expense->category)
                            <span class="text-xs bg-gray-100 text-gray-600 rounded-full px-2.5 py-1">{{ $expense->category }}</span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-red-600">{{ number_format($expense->amount, 2) }}</td>
                    <td class="px-5 py-3.5 text-gray-500 hidden md:table-cell">{{ $expense->recipient ?: '—' }}</td>
                    <td class="px-5 py-3.5 text-gray-500">{{ $expense->date->format('Y/m/d') }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-1 justify-end">
                            <a href="{{ route('expenses.edit', $expense) }}"
                               class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟')">
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
            {{ $expenses->links() }}
        </div>
    @endif
</div>

@endsection
