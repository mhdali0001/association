@extends('layouts.app')

@section('title', '{{ $expense->title }} — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('expenses.index') }}" class="text-emerald-600 hover:underline">المصروفات</a>
    <span class="mx-2 text-gray-400">/</span>
    <span class="text-gray-700">{{ $expense->title }}</span>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $expense->title }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ $expense->date->format('Y/m/d') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('expenses.edit', $expense) }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                تعديل
            </a>
            <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold px-4 py-2 rounded-xl transition-colors">
                    حذف
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <dl class="divide-y divide-gray-50">
            @php
                $rows = [
                    'المبلغ'          => number_format($expense->amount, 2) . ' ل.س',
                    'الفئة'           => $expense->category ?: '—',
                    'الجهة المستفيدة' => $expense->recipient ?: '—',
                    'التاريخ'         => $expense->date->format('Y/m/d'),
                    'أضافه'           => $expense->user?->name ?: '—',
                ];
            @endphp
            @foreach($rows as $label => $value)
            <div class="flex px-6 py-4">
                <dt class="w-40 shrink-0 text-sm text-gray-500">{{ $label }}</dt>
                <dd class="text-sm font-medium text-gray-800">{{ $value }}</dd>
            </div>
            @endforeach
            @if($expense->description)
            <div class="px-6 py-4">
                <dt class="text-sm text-gray-500 mb-1">ملاحظات</dt>
                <dd class="text-sm text-gray-700 whitespace-pre-line">{{ $expense->description }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <div class="mt-4">
        <a href="{{ route('expenses.index') }}" class="text-sm text-emerald-600 hover:underline">← العودة إلى قائمة المصروفات</a>
    </div>

</div>

@endsection
