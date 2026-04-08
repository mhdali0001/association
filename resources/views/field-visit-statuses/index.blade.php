@extends('layouts.app')

@section('title', 'حالات الجولات الميدانية — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">حالات الجولات الميدانية</span>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">حالات الجولات الميدانية</h1>
        <p class="text-sm text-gray-400 mt-0.5">إدارة قائمة حالات الجولات الميدانية وألوانها</p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Add form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
            <h2 class="text-sm font-semibold text-gray-700">إضافة حالة جديدة</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('field-visit-statuses.store') }}" class="flex gap-3 items-start">
                @csrf
                <div class="flex-1">
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="اسم الحالة (مثال: تمت الزيارة، قيد الجدولة…)"
                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-400 @enderror">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="shrink-0">
                    <input type="color" name="color" value="{{ old('color', '#6366f1') }}"
                           class="h-[46px] w-14 border-2 border-gray-300 rounded-xl cursor-pointer p-1 focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shrink-0">
                    + إضافة
                </button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">القائمة الحالية</h2>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2.5 py-0.5">{{ $statuses->count() }} حالة</span>
        </div>

        @if($statuses->isEmpty())
            <div class="p-10 text-center">
                <p class="text-gray-400 text-sm">لا توجد حالات بعد.</p>
            </div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach($statuses as $status)
                <div class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50 group">
                    <div class="w-4 h-4 rounded-full shrink-0 ring-2 ring-offset-1" style="background: {{ $status->color }}; ring-color: {{ $status->color }}"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm text-gray-900">{{ $status->name }}</span>
                            @if(!$status->is_active)
                                <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">معطّل</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400">{{ number_format($status->field_visits_count) }} جولة</span>
                    </div>

                    {{-- Edit form --}}
                    <form method="POST" action="{{ route('field-visit-statuses.update', $status) }}" class="flex gap-2 items-center opacity-0 group-hover:opacity-100 transition-opacity">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $status->name }}"
                               class="border border-gray-200 rounded-lg px-3 py-1.5 text-xs w-40 focus:ring-2 focus:ring-indigo-400">
                        <input type="color" name="color" value="{{ $status->color }}"
                               class="h-8 w-10 border border-gray-200 rounded-lg cursor-pointer p-0.5">
                        <label class="flex items-center gap-1 text-xs text-gray-500 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $status->is_active ? 'checked' : '' }} class="rounded">
                            نشط
                        </label>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors">حفظ</button>
                    </form>

                    {{-- Delete --}}
                    <form method="POST" action="{{ route('field-visit-statuses.destroy', $status) }}"
                          onsubmit="return confirm('حذف الحالة: {{ $status->name }}؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity p-1.5 rounded-lg hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection
