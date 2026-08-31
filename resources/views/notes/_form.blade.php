@php
    $preIds = collect(old('people',
            isset($note) ? $note->members->pluck('id')->all() : ($prefillMemberIds ?? [])
        ))
        ->filter(fn ($v) => is_numeric($v))->map(fn ($v) => (int) $v)->unique()->values();
    $preMembers = $preIds->isNotEmpty()
        ? \App\Models\Member::whereIn('id', $preIds)->orderBy('full_name')->pluck('full_name', 'id')
        : collect();
    $fieldClass = 'w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition-colors';
    $currentCategory = old('category', $note->category ?? '');
@endphp

<div class="space-y-4 sm:space-y-5">

    {{-- Title --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1.5">العنوان <span class="text-gray-300 font-normal">(اختياري)</span></label>
        <input type="text" name="title" value="{{ old('title', $note->title ?? '') }}" placeholder="عنوان مختصر للملاحظة"
               class="{{ $fieldClass }} @error('title') border-red-400 @enderror">
        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
        {{-- Category --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">التصنيف</label>
            <select name="category" class="{{ $fieldClass }} @error('category') border-red-400 @enderror">
                <option value="">— بدون تصنيف —</option>
                @foreach(\App\Models\Note::CATEGORIES as $key => $c)
                    <option value="{{ $key }}" {{ $currentCategory === $key ? 'selected' : '' }}>{{ $c['label'] }}</option>
                @endforeach
            </select>
            @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Date --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5">التاريخ</label>
            <input type="date" name="note_date"
                   value="{{ old('note_date', isset($note) && $note->note_date ? $note->note_date->format('Y-m-d') : date('Y-m-d')) }}"
                   class="{{ $fieldClass }} @error('note_date') border-red-400 @enderror">
            @error('note_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Body --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1.5">نص الملاحظة <span class="text-red-500">*</span></label>
        <textarea name="body" rows="6" placeholder="اكتب تفاصيل الملاحظة هنا…"
                  class="{{ $fieldClass }} leading-relaxed resize-y @error('body') border-red-400 @enderror">{{ old('body', $note->body ?? '') }}</textarea>
        @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Pinned --}}
    <label class="flex items-center gap-2.5 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 hover:bg-gray-100 transition-colors">
        <input type="hidden" name="pinned" value="0">
        <input type="checkbox" name="pinned" value="1" @checked(old('pinned', $note->pinned ?? false))
               class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-400">
        <span class="text-sm text-gray-700 font-medium">تثبيت في أعلى القائمة</span>
        <svg class="w-4 h-4 text-amber-400 mr-auto" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.958c.3.922-.755 1.688-1.539 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.784.57-1.838-.196-1.539-1.118l1.287-3.958a1 1 0 00-.363-1.118L2.343 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.951-.69l1.286-3.958z"/></svg>
    </label>

    {{-- People (multi-select + browse) --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1.5">المستفيدون المعنيون <span class="text-gray-300 font-normal">(اختياري — أكثر من واحد)</span></label>

        <div id="np-box" class="border border-gray-200 rounded-xl bg-gray-50 focus-within:bg-white focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-500 transition-colors">
            <div id="np-chips" class="flex flex-wrap gap-2 empty:hidden px-3 pt-2.5"></div>
            <div class="relative flex items-center">
                <input type="text" id="np-search" autocomplete="off" placeholder="ابحث بالاسم أو رقم الملف، أو افتح القائمة…"
                       class="flex-1 text-sm outline-none bg-transparent px-3 py-2.5">
                <button type="button" id="np-toggle" aria-label="فتح قائمة المستفيدين"
                        class="shrink-0 px-3 py-2.5 text-gray-400 hover:text-emerald-600 transition-colors">
                    <svg id="np-chevron" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div id="np-results" class="hidden absolute z-30 top-full inset-x-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 sm:max-h-72 overflow-y-auto"></div>
            </div>
        </div>
        <p class="text-[11px] text-gray-400 mt-1">افتح القائمة لاختيار من أوائل المستفيدين، أو اكتب للبحث في الجميع. اضغط على اسم لإضافته أو إزالته.</p>
        <div id="np-hidden"></div>
        @error('people')   <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @error('people.*') <p class="text-red-500 text-xs mt-1">اختيار مستفيد غير صالح.</p> @enderror
    </div>

    {{-- Attachments — only on create (edit adds files via a separate form) --}}
    @unless(isset($note))
    <div>
        <label class="block text-xs font-bold text-gray-500 mb-1.5">ملفات مرفقة <span class="text-gray-300 font-normal">(اختياري)</span></label>
        <div class="border border-dashed border-gray-300 rounded-xl bg-gray-50 px-4 py-4">
            <input type="file" name="attachments[]" multiple id="np-files"
                   class="w-full text-sm text-gray-600 file:ml-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-100 file:text-emerald-700 file:font-semibold hover:file:bg-emerald-200 file:cursor-pointer cursor-pointer">
            <div class="flex items-center gap-2 mt-3 sm:hidden">
                <label class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    التقاط صورة بالكاميرا
                    <input type="file" id="np-cam" accept="image/*" capture="environment" class="hidden">
                </label>
            </div>
            <p id="np-file-hint" class="text-xs text-gray-400 mt-2">حتى 20 ملفاً، 20 ميغابايت لكل ملف — PDF, Word, Excel, صور, نصوص, ZIP.</p>
        </div>
        @error('attachments')   <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        @error('attachments.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
    @endunless

</div>

@push('scripts')
<script>
(function () {
    const box         = document.getElementById('np-box');
    const searchInput = document.getElementById('np-search');
    const resultsBox  = document.getElementById('np-results');
    const chipsBox    = document.getElementById('np-chips');
    const hiddenBox   = document.getElementById('np-hidden');
    const toggleBtn   = document.getElementById('np-toggle');
    const chevron     = document.getElementById('np-chevron');
    const url         = @json(route('members.search-json'));
    const browseList  = @json($browseMembers ?? []);
    let timer, mode = 'browse', serverList = [];

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }
    function hasId(id) {
        return hiddenBox.querySelector('input[data-np-id="' + id + '"]') !== null;
    }
    function removeById(id) {
        const h = hiddenBox.querySelector('input[data-np-id="' + id + '"]');
        if (h) h.remove();
        const c = chipsBox.querySelector('[data-np-chip="' + id + '"]');
        if (c) c.remove();
    }
    function addChip(id, name) {
        if (hasId(id)) return;
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'people[]';
        hidden.value = id;
        hidden.setAttribute('data-np-id', id);
        hiddenBox.appendChild(hidden);

        const chip = document.createElement('span');
        chip.className = 'inline-flex items-center gap-1.5 text-xs bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full ps-3 pe-1.5 py-1 font-medium';
        chip.setAttribute('data-np-chip', id);
        chip.innerHTML = esc(name) +
            '<button type="button" class="w-4 h-4 flex items-center justify-center rounded-full hover:bg-emerald-200 text-emerald-600 text-sm leading-none" aria-label="إزالة">&times;</button>';
        chip.querySelector('button').addEventListener('click', function () {
            removeById(id);
            if (!resultsBox.classList.contains('hidden')) repaint();
        });
        chipsBox.appendChild(chip);
    }

    function rowHtml(m) {
        const on = hasId(m.id);
        return `<button type="button" data-id="${m.id}" data-name="${esc(m.full_name)}"
            class="w-full text-right flex items-center justify-between gap-3 px-3 py-2 transition-colors ${on ? 'bg-emerald-50' : 'hover:bg-emerald-50'}">
            <span class="flex items-center gap-2 min-w-0">
                <svg class="w-4 h-4 shrink-0 ${on ? 'text-emerald-600' : 'text-gray-300'}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm font-semibold text-gray-800 truncate">${esc(m.full_name)}</span>
            </span>
            <span class="text-xs text-gray-400 font-mono shrink-0">${esc(m.dossier_number || '—')}</span>
        </button>`;
    }

    function paint(list, emptyMsg) {
        if (!list.length) {
            resultsBox.innerHTML = `<p class="text-xs text-gray-400 text-center py-3">${emptyMsg}</p>`;
        } else {
            resultsBox.innerHTML = list.map(rowHtml).join('');
            resultsBox.querySelectorAll('button[data-id]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    if (hasId(id)) removeById(id);
                    else addChip(id, this.getAttribute('data-name'));
                    repaint();
                    searchInput.focus();
                });
            });
        }
        openPanel();
    }

    function browseFiltered() {
        const q = searchInput.value.trim().toLowerCase();
        if (!q) return browseList;
        return browseList.filter(m =>
            (m.full_name || '').toLowerCase().includes(q) ||
            String(m.dossier_number || '').toLowerCase().includes(q));
    }

    function repaint() {
        if (mode === 'search') paint(serverList, 'لا توجد نتائج');
        else paint(browseFiltered(), 'لا نتائج ضمن القائمة الظاهرة — تابع الكتابة للبحث في الجميع');
    }

    function openPanel()  { resultsBox.classList.remove('hidden'); chevron.classList.add('rotate-180'); }
    function closePanel() { resultsBox.classList.add('hidden');    chevron.classList.remove('rotate-180'); }

    // Pre-selected members (edit mode, prefill from ?member=, or re-populated after a validation error)
    @foreach($preMembers as $id => $name)
        addChip('{{ $id }}', @json($name));
    @endforeach

    toggleBtn.addEventListener('click', function () {
        if (resultsBox.classList.contains('hidden')) { mode = 'browse'; repaint(); }
        else closePanel();
    });

    searchInput.addEventListener('focus', function () {
        if (resultsBox.classList.contains('hidden')) { mode = 'browse'; repaint(); }
    });

    searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 2) { mode = 'browse'; repaint(); return; }
        timer = setTimeout(() => {
            fetch(url + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(list => { mode = 'search'; serverList = list; repaint(); })
                .catch(() => {});
        }, 280);
    });

    document.addEventListener('click', function (e) {
        if (!box.contains(e.target)) closePanel();
    });

    // ── Camera capture → merge into the main file input ──
    const camInput  = document.getElementById('np-cam');
    const fileInput = document.getElementById('np-files');
    const fileHint  = document.getElementById('np-file-hint');
    if (camInput && fileInput && window.DataTransfer) {
        camInput.addEventListener('change', function () {
            const dt = new DataTransfer();
            for (const f of fileInput.files) dt.items.add(f);
            for (const f of this.files)      dt.items.add(f);
            fileInput.files = dt.files;
            this.value = '';
            if (fileHint) fileHint.textContent = fileInput.files.length + ' ملف مُختار — حتى 20 ملفاً، 20 ميغابايت لكل ملف.';
        });
    }
})();
</script>
@endpush
