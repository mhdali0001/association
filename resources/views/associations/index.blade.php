@extends('layouts.app')

@section('title', 'الجمعيات — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">الجمعيات</span>
@endsection

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">الجمعيات</h1>
            <p class="text-sm text-gray-400 mt-0.5">إدارة قائمة الجمعيات المتاحة</p>
        </div>
    </div>

    {{-- Add form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-3">
            <h2 class="text-sm font-semibold text-gray-700">إضافة جمعية جديدة</h2>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('associations.store') }}" class="flex gap-3">
                @csrf
                <div class="flex-1">
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="اسم الجمعية"
                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors shrink-0">
                    + إضافة
                </button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-100 px-6 py-3 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">القائمة الحالية</h2>
            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2.5 py-0.5">{{ $associations->count() }} جمعية</span>
        </div>

        @if($associations->isEmpty())
            <div class="p-10 text-center">
                <p class="text-gray-400 text-sm">لا توجد جمعيات بعد. أضف الأولى أعلاه.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-50">
                @foreach($associations as $assoc)
                <li class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50 transition-colors" id="row-{{ $assoc->id }}">

                    {{-- View mode --}}
                    <div class="flex items-center gap-3 view-mode-{{ $assoc->id }}">
                        <span class="w-2 h-2 rounded-full {{ $assoc->is_active ? 'bg-emerald-400' : 'bg-gray-300' }}"></span>
                        <a href="{{ route('members.index', ['association_id[]' => $assoc->id]) }}"
                           class="text-sm font-medium text-gray-800 hover:text-emerald-600 hover:underline transition-colors">
                            {{ $assoc->name }}
                        </a>
                        @if(!$assoc->is_active)
                            <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">معطّل</span>
                        @endif
                        @if($assoc->members_count)
                            <span class="text-xs text-blue-600 bg-blue-50 border border-blue-100 rounded-full px-2 py-0.5">
                                {{ $assoc->members_count }} عضو
                            </span>
                        @endif
                    </div>

                    {{-- Edit mode --}}
                    <form method="POST" action="{{ route('associations.update', $assoc) }}"
                          class="flex items-center gap-2 flex-1 mr-4 hidden edit-mode-{{ $assoc->id }}">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $assoc->name }}"
                               class="flex-1 border-2 border-emerald-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer shrink-0">
                            <input type="checkbox" name="is_active" value="1" {{ $assoc->is_active ? 'checked' : '' }}
                                   class="h-4 w-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                            فعّال
                        </label>
                        <button type="submit" class="bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors shrink-0">حفظ</button>
                        <button type="button" onclick="cancelEdit({{ $assoc->id }})"
                                class="text-gray-400 hover:text-gray-600 text-xs px-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors shrink-0">إلغاء</button>
                    </form>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 actions-{{ $assoc->id }}">
                        <button onclick="startEdit({{ $assoc->id }})"
                                class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('associations.destroy', $assoc) }}"
                              onsubmit="return confirm('هل أنت متأكد من حذف «{{ $assoc->name }}»؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>

<script>
function startEdit(id) {
    document.querySelector('.view-mode-' + id).classList.add('hidden');
    document.querySelector('.edit-mode-' + id).classList.remove('hidden');
    document.querySelector('.actions-' + id).classList.add('hidden');
}
function cancelEdit(id) {
    document.querySelector('.view-mode-' + id).classList.remove('hidden');
    document.querySelector('.edit-mode-' + id).classList.add('hidden');
    document.querySelector('.actions-' + id).classList.remove('hidden');
}
</script>

@endsection
