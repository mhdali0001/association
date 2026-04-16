@extends('layouts.app')

@section('title', 'حالات البيوت — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">حالات البيوت</span>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">حالات البيوت</h1>
        <p class="text-sm text-gray-400 mt-0.5">إدارة قائمة حالات البيوت المستخدمة في الجولات الميدانية</p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Add form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="bg-gray-50 border-b border-gray-100 px-5 py-3">
            <h2 class="text-sm font-semibold text-gray-700">إضافة حالة جديدة</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('house-conditions.store') }}">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="اسم الحالة (مثال: جيدة، متوسطة، رديئة…)"
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('name') border-red-400 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-3 items-center">
                        <div class="flex items-center gap-2">
                            <label class="text-xs text-gray-500 font-medium">اللون</label>
                            <input type="color" name="color" value="{{ old('color', '#f59e0b') }}"
                                   class="h-11 w-16 border-2 border-gray-300 rounded-xl cursor-pointer p-1 focus:ring-2 focus:ring-amber-500">
                        </div>
                        <button type="submit" class="flex-1 sm:flex-none bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                            + إضافة
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-5 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">القائمة الحالية</h2>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2.5 py-0.5">{{ $conditions->count() }} حالة</span>
        </div>

        @if($conditions->isEmpty())
            <div class="p-10 text-center">
                <p class="text-gray-400 text-sm">لا توجد حالات بعد.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($conditions as $condition)
                <div class="px-5 py-4">

                    {{-- Row --}}
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full shrink-0 ring-2 ring-offset-2" style="background:{{ $condition->color }}; ring-color:{{ $condition->color }}"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-sm text-gray-900">{{ $condition->name }}</span>
                                @if(!$condition->is_active)
                                    <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">معطّل</span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-400">{{ number_format($condition->field_visits_count) }} جولة</span>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <button type="button" onclick="toggleEdit({{ $condition->id }})"
                                    class="p-2 rounded-xl text-amber-400 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('house-conditions.destroy', $condition) }}"
                                  onsubmit="return confirm('حذف الحالة: {{ $condition->name }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit panel (hidden by default) --}}
                    <div id="edit-{{ $condition->id }}" class="hidden mt-4 pt-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('house-conditions.update', $condition) }}">
                            @csrf @method('PUT')
                            <div class="flex flex-col gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">اسم الحالة</label>
                                    <input type="text" name="name" value="{{ $condition->name }}"
                                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-3">
                                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">اللون</label>
                                        <input type="color" name="color" value="{{ $condition->color }}"
                                               class="h-12 w-20 border-2 border-gray-200 rounded-xl cursor-pointer p-1 focus:ring-2 focus:ring-amber-400">
                                    </div>
                                    <label class="flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" name="is_active" value="1" {{ $condition->is_active ? 'checked' : '' }}
                                               class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                                        <span class="text-sm text-gray-600 font-medium">نشط</span>
                                    </label>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                                        حفظ التعديلات
                                    </button>
                                    <button type="button" onclick="toggleEdit({{ $condition->id }})"
                                            class="px-4 py-2.5 border border-gray-200 text-gray-500 hover:bg-gray-50 text-sm font-medium rounded-xl transition-colors">
                                        إلغاء
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<script>
function toggleEdit(id) {
    const panel = document.getElementById('edit-' + id);
    panel.classList.toggle('hidden');
}
</script>

@endsection
