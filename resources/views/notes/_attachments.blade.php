{{-- Expects: $note (with attachments loaded), $canManage (bool) --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
            الملفات المرفقة
        </h2>
        <span class="text-xs font-semibold text-gray-400 bg-gray-50 border border-gray-100 rounded-full px-2.5 py-0.5">{{ $note->attachments->count() }}</span>
    </div>

    @if($canManage)
    <form method="POST" action="{{ route('notes.attachments.store', $note) }}" enctype="multipart/form-data"
          class="mb-5 bg-gray-50 border border-dashed border-gray-200 rounded-xl p-4 space-y-3">
        @csrf
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <input type="file" name="attachments[]" multiple id="na-files"
                   class="flex-1 text-sm text-gray-600 file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-100 file:text-emerald-700 file:font-semibold hover:file:bg-emerald-200 file:cursor-pointer cursor-pointer">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                رفع
            </button>
        </div>
        <div class="flex items-center gap-2 sm:hidden">
            <label class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                التقاط صورة بالكاميرا
                <input type="file" id="na-cam" accept="image/*" capture="environment" class="hidden">
            </label>
        </div>
        <p id="na-hint" class="text-xs text-gray-400">اختر ملفاً واحداً على الأقل — حتى 20 ملفاً، 20 ميغابايت لكل ملف.</p>
    </form>
    @error('attachments')   <p class="text-red-500 text-xs -mt-3 mb-3">{{ $message }}</p> @enderror
    @error('attachments.*') <p class="text-red-500 text-xs -mt-3 mb-3">{{ $message }}</p> @enderror
    @endif

    @if($note->attachments->isEmpty())
        <p class="text-sm text-gray-400 text-center py-6">لا توجد ملفات مرفقة.</p>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3">
            @foreach($note->attachments as $att)
                <div class="border border-gray-100 rounded-xl overflow-hidden bg-white hover:border-emerald-200 hover:shadow-sm transition-all">
                    <a href="{{ route('notes.attachments.download', $att) }}" target="_blank"
                       class="block bg-gray-50 aspect-[4/3] flex items-center justify-center overflow-hidden">
                        @if($att->isImage())
                            <img src="{{ $att->url }}" alt="{{ $att->file_name }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <svg class="w-9 h-9 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        @endif
                    </a>
                    <div class="p-2.5">
                        <p class="text-xs font-medium text-gray-700 truncate" title="{{ $att->file_name }}">{{ $att->file_name }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $att->file_size_human }}</p>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-50">
                            <a href="{{ route('notes.attachments.download', $att) }}"
                               class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 hover:text-emerald-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                تنزيل
                            </a>
                            @if($canManage)
                            <form method="POST" action="{{ route('notes.attachments.destroy', $att) }}" onsubmit="return confirm('حذف هذا الملف؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[11px] font-semibold text-gray-400 hover:text-red-500 transition-colors">حذف</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@if($canManage)
@push('scripts')
<script>
(function () {
    const cam  = document.getElementById('na-cam');
    const files = document.getElementById('na-files');
    const hint = document.getElementById('na-hint');
    if (cam && files && window.DataTransfer) {
        cam.addEventListener('change', function () {
            const dt = new DataTransfer();
            for (const f of files.files) dt.items.add(f);
            for (const f of this.files)  dt.items.add(f);
            files.files = dt.files;
            this.value = '';
            if (hint) hint.textContent = files.files.length + ' ملف مُختار للرفع.';
        });
    }
})();
</script>
@endpush
@endif
