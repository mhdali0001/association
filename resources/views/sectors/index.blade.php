@extends('layouts.app')

@section('title', 'القطاعات — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">القطاعات</span>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">القطاعات</h1>
        <p class="text-sm text-gray-400 mt-0.5">إدارة قائمة القطاعات للمستفيدين</p>
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
            <h2 class="text-sm font-semibold text-gray-700">إضافة قطاع جديد</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('sectors.store') }}">
                @csrf
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="اسم القطاع…"
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-400 @enderror">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shrink-0">
                        + إضافة
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-5 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">قائمة القطاعات</h2>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2.5 py-0.5">{{ $sectors->count() }} قطاع</span>
        </div>

        @if($sectors->isEmpty())
            <div class="p-10 text-center">
                <p class="text-gray-400 text-sm">لا توجد قطاعات بعد.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach($sectors as $sector)
                <div class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <a href="{{ route('sectors.show', $sector) }}" class="flex-1 min-w-0 group/link">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-semibold text-sm text-gray-900 group-hover/link:text-indigo-700 transition-colors">{{ $sector->name }}</span>
                                @if(!$sector->is_active)
                                    <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">معطّل</span>
                                @endif
                            </div>
                            <span class="text-xs text-indigo-400 group-hover/link:text-indigo-600 transition-colors">{{ number_format($sector->members_count) }} مستفيد ←</span>
                        </a>
                        <div class="flex gap-1 shrink-0">
                            <button type="button" onclick="toggleEdit({{ $sector->id }})"
                                    class="p-2 rounded-xl text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('sectors.destroy', $sector) }}"
                                  onsubmit="return confirm('حذف القطاع: {{ $sector->name }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit panel --}}
                    <div id="edit-{{ $sector->id }}" class="hidden mt-4 pt-4 border-t border-gray-100">
                        <form method="POST" action="{{ route('sectors.update', $sector) }}">
                            @csrf @method('PUT')
                            <div class="flex flex-col gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">اسم القطاع</label>
                                    <input type="text" name="name" value="{{ $sector->name }}"
                                           class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                                </div>
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="checkbox" name="is_active" value="1" {{ $sector->is_active ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                    <span class="text-sm text-gray-600 font-medium">نشط</span>
                                </label>
                                <div class="flex gap-2">
                                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                                        حفظ التعديلات
                                    </button>
                                    <button type="button" onclick="toggleEdit({{ $sector->id }})"
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
