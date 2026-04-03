@extends('layouts.app')

@section('title', 'أرشيف الصور والمستندات — مسالك النور')
@section('max-width', 'max-w-6xl')

@section('breadcrumb')
    <span class="text-gray-700">أرشيف الصور والمستندات</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-violet-600 via-violet-500 to-purple-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-40 h-40 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-56 h-56 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white mb-1">أرشيف الصور والمستندات</h1>
            <p class="text-violet-200 text-sm">إجمالي الملفات المحفوظة: <span class="font-bold text-white">{{ \App\Models\MemberImage::count() }}</span></p>
        </div>
        <button onclick="document.getElementById('upload-panel').classList.toggle('hidden')"
                class="flex items-center gap-2 bg-white text-violet-700 hover:bg-violet-50 text-sm font-bold px-5 py-2.5 rounded-xl transition-colors shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            رفع ملف جديد
        </button>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="mb-5 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- Upload Panel --}}
<div id="upload-panel" class="{{ $errors->any() ? '' : 'hidden' }} bg-white rounded-2xl border border-violet-100 shadow-sm overflow-hidden mb-6">
    <div class="flex items-center gap-2.5 bg-gradient-to-l from-violet-50 to-purple-50 border-b border-violet-100 px-6 py-3.5">
        <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
        </div>
        <h2 class="text-sm font-bold text-violet-800">رفع ملف جديد</h2>
    </div>
    <div class="p-6">
        <form action="{{ route('member-images.store-global') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- Member autocomplete data --}}
            <script>
                const membersList = @json($members->map(fn($m) => ['id' => $m->id, 'name' => $m->full_name]));
            </script>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">العضو / الاضبارة <span class="text-red-400">*</span></label>
                    <div class="relative" id="member-ac-wrap">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <input type="text" id="member-ac-input" autocomplete="off"
                               placeholder="ابحث باسم الشخص..."
                               value="{{ old('member_id') ? $members->firstWhere('id', old('member_id'))?->full_name : '' }}"
                               class="w-full pr-9 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition">
                        <input type="hidden" name="member_id" id="member-ac-value" value="{{ old('member_id') }}" required>
                        <ul id="member-ac-list"
                            class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-52 overflow-y-auto text-sm"></ul>
                    </div>
                    @error('member_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">الملف <span class="text-red-400">*</span></label>
                    <input type="file" name="image" accept="image/*,.pdf" required
                           class="block w-full text-sm text-gray-600 file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-violet-100 file:text-violet-700 hover:file:bg-violet-200 border border-gray-200 rounded-xl bg-gray-50 px-2 py-1.5 cursor-pointer transition">
                    @error('image')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">وصف الملف (اختياري)</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="مثال: هوية شخصية، وثيقة سكن..."
                           class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition">
                </div>
            </div>
            <div class="flex items-center gap-3 mt-4">
                <button type="submit"
                        class="flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    رفع الملف
                </button>
                <p class="text-xs text-gray-400">الأنواع المدعومة: JPG, PNG, GIF, WEBP, PDF — الحجم الأقصى: 10 ميغابايت</p>
            </div>
        </form>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('member-images.index') }}" class="flex flex-wrap gap-3 items-end">

        {{-- Search by member name --}}
        <div class="flex-1 min-w-52">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">بحث باسم الشخص</label>
            <div class="relative">
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <input type="text" name="member_name" value="{{ $memberName ?? '' }}"
                       placeholder="اكتب اسم الشخص..."
                       class="w-full pr-9 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition">
            </div>
        </div>

        {{-- Search by file title/name --}}
        <div class="flex-1 min-w-44">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">بحث في الملفات</label>
            <div class="relative">
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="وصف الملف أو اسمه..."
                       class="w-full pr-9 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:bg-white transition">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit"
                    class="flex items-center gap-2 bg-gradient-to-l from-violet-600 to-purple-500 hover:from-violet-700 hover:to-purple-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
                بحث
            </button>
            @if($search || ($memberName ?? ''))
                <a href="{{ route('member-images.index') }}"
                   class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح
                </a>
                <span class="text-sm font-bold text-violet-700 bg-violet-50 border border-violet-200 rounded-xl px-4 py-2.5">
                    {{ number_format($images->total()) }} نتيجة
                </span>
            @endif
        </div>
    </form>
</div>

{{-- Images Grid --}}
@if($images->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 18h18M3.75 4.5h16.5a1.5 1.5 0 011.5 1.5v12a1.5 1.5 0 01-1.5 1.5H3.75a1.5 1.5 0 01-1.5-1.5V6a1.5 1.5 0 011.5-1.5z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-semibold mb-1">لا توجد ملفات</p>
        <p class="text-sm text-gray-400">استخدم زر "رفع ملف جديد" لإضافة أول مستند</p>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        @foreach($images as $img)
            <div class="group relative bg-white rounded-2xl border border-gray-100 overflow-hidden hover:border-violet-200 hover:shadow-lg transition-all flex flex-col">

                {{-- Thumbnail --}}
                @if($img->isImage())
                    <a href="{{ $img->url }}" target="_blank" class="block overflow-hidden bg-gray-100 aspect-square">
                        <img src="{{ $img->url }}" alt="{{ $img->title ?? $img->file_name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 cursor-zoom-in">
                    </a>
                @else
                    <a href="{{ $img->url }}" target="_blank"
                       class="flex flex-col items-center justify-center aspect-square bg-red-50 hover:bg-red-100 transition-colors">
                        <svg class="w-10 h-10 text-red-400 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                        <span class="text-xs font-black text-red-500 uppercase tracking-wider">PDF</span>
                    </a>
                @endif

                {{-- Info --}}
                <div class="p-3 flex flex-col gap-1 flex-1">
                    <p class="text-xs font-bold text-gray-800 truncate leading-snug" title="{{ $img->title ?? $img->file_name }}">
                        {{ $img->title ?: $img->file_name }}
                    </p>
                    <a href="{{ route('members.show', $img->member_id) }}"
                       class="text-xs text-violet-600 hover:text-violet-800 font-semibold truncate transition-colors"
                       title="{{ $img->member->full_name }}">
                        {{ $img->member->full_name }}
                    </a>
                    <div class="flex items-center justify-between mt-auto pt-1">
                        <span class="text-xs text-gray-400">{{ $img->file_size_human }}</span>
                        <span class="text-xs text-gray-400">{{ $img->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                {{-- Actions (show on hover) --}}
                <div class="flex border-t border-gray-100 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a href="{{ route('member-images.edit', $img) }}"
                       class="flex-1 flex items-center justify-center gap-1 py-2 text-xs font-semibold text-violet-600 hover:bg-violet-50 transition-colors border-l border-gray-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل
                    </a>
                    <form action="{{ route('member-images.destroy', $img) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="flex items-center justify-center gap-1 px-3 py-2 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($images->hasPages())
        <div class="flex justify-center">
            {{ $images->links() }}
        </div>
    @endif
@endif

<script>
(function () {
    const input  = document.getElementById('member-ac-input');
    const hidden = document.getElementById('member-ac-value');
    const list   = document.getElementById('member-ac-list');

    if (!input) return;

    function render(items) {
        list.innerHTML = '';
        if (!items.length) {
            list.innerHTML = '<li class="px-4 py-3 text-gray-400 text-sm text-center">لا توجد نتائج</li>';
        } else {
            items.slice(0, 30).forEach(function(m) {
                const li = document.createElement('li');
                li.textContent = m.name;
                li.className = 'px-4 py-2.5 cursor-pointer hover:bg-violet-50 hover:text-violet-700 transition-colors border-b border-gray-50 last:border-0';
                li.addEventListener('mousedown', function() {
                    input.value  = m.name;
                    hidden.value = m.id;
                    list.classList.add('hidden');
                });
                list.appendChild(li);
            });
        }
        list.classList.remove('hidden');
    }

    input.addEventListener('input', function() {
        hidden.value = '';
        const q = input.value.trim();
        if (!q) { list.classList.add('hidden'); return; }
        render(membersList.filter(function(m) { return m.name.includes(q); }));
    });

    input.addEventListener('focus', function() {
        const q = input.value.trim();
        if (q) render(membersList.filter(function(m) { return m.name.includes(q); }));
    });

    document.addEventListener('click', function(e) {
        if (!document.getElementById('member-ac-wrap').contains(e.target)) {
            list.classList.add('hidden');
            if (!hidden.value) input.value = '';
        }
    });
})();
</script>

@endsection
