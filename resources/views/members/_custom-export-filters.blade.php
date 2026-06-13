<div class="space-y-3">

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">بحث (الاسم / الهاتف / الهوية)</label>
        <input type="text" name="search" placeholder="ابحث..."
               class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300">
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">المنطقة</label>
        <select name="region_id"
                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-gray-500">
            <option value="">— الكل —</option>
            @foreach(\App\Models\Region::active()->orderBy('name')->get() as $r)
                <option value="{{ $r->id }}">{{ $r->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة النهائية</label>
        <select name="final_status_id[]" multiple
                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"
                style="min-height:80px">
            @foreach(\App\Models\FinalStatus::active()->orderBy('name')->get() as $fs)
                <option value="{{ $fs->id }}">{{ $fs->name }}</option>
            @endforeach
        </select>
        <p class="text-[10px] text-gray-400 mt-1">اضغط Ctrl/Cmd للتعدد</p>
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">القطاع</label>
        <select name="sector_id[]" multiple
                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition"
                style="min-height:60px">
            @foreach(\App\Models\Sector::active()->orderBy('name')->get() as $sec)
                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالات الخاصة</label>
        <select name="special_cases"
                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-gray-500">
            <option value="">— الكل —</option>
            <option value="1">نعم</option>
            <option value="0">لا</option>
        </select>
    </div>

    <div class="flex gap-2">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">اضبارة من</label>
            <input type="text" name="dossier_from" placeholder="100"
                   class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition placeholder-gray-300 font-mono">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">إلى</label>
            <input type="text" name="dossier_to" placeholder="999"
                   class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition placeholder-gray-300 font-mono">
        </div>
    </div>

</div>
