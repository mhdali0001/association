@extends('layouts.app')

@section('title', 'المناطق — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">المناطق</span>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">المناطق</h1>
        <p class="text-sm text-gray-400 mt-0.5">إدارة قائمة المناطق الجغرافية للمستفيدين</p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Add form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="bg-gray-50 border-b border-gray-100 px-5 py-3">
            <h2 class="text-sm font-semibold text-gray-700">إضافة منطقة جديدة</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('regions.store') }}">
                @csrf
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="اسم المنطقة (مثال: دمشق، ريف دمشق…)"
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('name') border-red-400 @enderror">
                    </div>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shrink-0">
                        + إضافة
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-5 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">قائمة المناطق</h2>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2.5 py-0.5">{{ $regions->count() }} منطقة</span>
        </div>

        @if($regions->isEmpty())
            <div class="p-10 text-center">
                <p class="text-gray-400 text-sm">لا توجد مناطق بعد.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($regions as $region)
                <div class="px-5 py-4">

                    {{-- Row --}}
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-teal-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-sm text-gray-900">{{ $region->name }}</span>
                                @if(!$region->is_active)
                                    <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">معطّل</span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-400">{{ number_format($region->members_count) }} مستفيد</span>
                        </div>
                        <div class="flex gap-1 shrink-0">
                            <button type="button" onclick="toggleEdit({{ $region->id }})"
                                    class="p-2 rounded-xl text-teal-400 hover:text-teal-600 hover:bg-teal-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('regions.destroy', $region) }}"
                                  onsubmit="return confirm('حذف المنطقة: {{ $region->name }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit panel --}}
                    <div id="edit-{{ $region->id }}" class="hidden mt-4 pt-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('regions.update', $region) }}">
                            @csrf @method('PUT')
                            <div class="flex flex-col gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">اسم المنطقة</label>
                                    <input type="text" name="name" value="{{ $region->name }}"
                                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-400 focus:border-teal-400">
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" name="is_active" value="1" {{ $region->is_active ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-gray-300 text-teal-600 focus:ring-teal-400">
                                    <span class="text-sm text-gray-600 font-medium">نشطة</span>
                                </label>
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                                        حفظ التعديلات
                                    </button>
                                    <button type="button" onclick="toggleEdit({{ $region->id }})"
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
    document.getElementById('edit-' + id).classList.toggle('hidden');
}
</script>

@endsection
