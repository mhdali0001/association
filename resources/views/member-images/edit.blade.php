@extends('layouts.app')

@section('title', 'تعديل الملف — مسالك النور')
@section('max-width', 'max-w-xl')

@section('breadcrumb')
    <a href="{{ route('member-images.index') }}" class="hover:text-violet-700 transition-colors">أرشيف الصور</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">تعديل</span>
@endsection

@section('content')

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center gap-2.5 bg-gradient-to-l from-violet-50 to-purple-50 border-b border-violet-100 px-6 py-4">
        <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-sm font-bold text-violet-800">تعديل بيانات الملف</h1>
            <p class="text-xs text-violet-400 mt-0.5">{{ $memberImage->file_name }}</p>
        </div>
    </div>

    <div class="p-6 space-y-6">

        {{-- Preview --}}
        <div>
            <p class="text-xs font-semibold text-gray-500 mb-2">معاينة الملف</p>
            @if($memberImage->isImage())
                <a href="{{ $memberImage->url }}" target="_blank" class="block group">
                    <img src="{{ $memberImage->url }}" alt="{{ $memberImage->title ?? $memberImage->file_name }}"
                         class="w-full max-h-64 object-contain rounded-xl border border-gray-200 bg-gray-50 group-hover:opacity-90 transition cursor-zoom-in">
                </a>
            @else
                <a href="{{ $memberImage->url }}" target="_blank"
                   class="flex items-center gap-3 p-4 bg-red-50 hover:bg-red-100 border border-red-100 rounded-xl transition-colors group">
                    <svg class="w-10 h-10 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-bold text-red-700 group-hover:underline">{{ $memberImage->file_name }}</p>
                        <p class="text-xs text-red-400 mt-0.5">{{ $memberImage->file_size_human }} — انقر للفتح</p>
                    </div>
                </a>
            @endif
        </div>

        {{-- Meta --}}
        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                <p class="text-gray-400 mb-0.5">العضو</p>
                <a href="{{ route('members.show', $memberImage->member_id) }}"
                   class="font-bold text-violet-700 hover:underline">{{ $memberImage->member->full_name }}</a>
            </div>
            <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                <p class="text-gray-400 mb-0.5">تاريخ الرفع</p>
                <p class="font-semibold text-gray-700">{{ $memberImage->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                <p class="text-gray-400 mb-0.5">الحجم</p>
                <p class="font-semibold text-gray-700">{{ $memberImage->file_size_human }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                <p class="text-gray-400 mb-0.5">رُفع بواسطة</p>
                <p class="font-semibold text-gray-700">{{ $memberImage->uploader?->name ?? '—' }}</p>
            </div>
        </div>

        {{-- Edit form --}}
        <form action="{{ route('member-images.update', $memberImage) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600 mb-1.5">
                    وصف الملف
                    <span class="text-gray-400 font-normal text-xs">(اختياري)</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $memberImage->title) }}"
                       placeholder="مثال: هوية شخصية، وثيقة تسجيل..."
                       class="w-full text-sm border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition">
                @error('title')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    حفظ التعديلات
                </button>
                <a href="{{ route('member-images.index') }}"
                   class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-5 py-2.5 rounded-xl transition-colors">
                    إلغاء
                </a>
            </div>
        </form>

        {{-- Danger zone --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-semibold text-red-500 mb-2">منطقة الخطر</p>
            <form action="{{ route('member-images.destroy', $memberImage) }}" method="POST"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف نهائياً؟ لا يمكن التراجع.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 border border-red-200 text-red-500 hover:bg-red-50 text-sm font-semibold px-5 py-2 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف هذا الملف نهائياً
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
