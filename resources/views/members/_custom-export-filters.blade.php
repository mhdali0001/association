<div class="space-y-3">

    {{-- Search --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">بحث (الاسم / الهاتف / الهوية)</label>
        <input type="text" name="search" placeholder="ابحث..."
               class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300">
    </div>

    {{-- Dossier search --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">رقم الاضبارة</label>
        <input type="text" name="dossier_search" placeholder="بحث باضبارة..."
               class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition placeholder-gray-300 font-mono">
    </div>

    {{-- Dossier range --}}
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

    {{-- Estimated amount range --}}
    <div class="flex gap-2">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">المبلغ المقدر من</label>
            <input type="number" name="estimated_from" placeholder="0" min="0"
                   class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition placeholder-gray-300 font-mono">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">إلى</label>
            <input type="number" name="estimated_to" placeholder="∞" min="0"
                   class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition placeholder-gray-300 font-mono">
        </div>
    </div>

    {{-- Payments count range --}}
    <div class="flex gap-2">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">عدد الدفعات من</label>
            <input type="number" name="payments_count_from" placeholder="0" min="0" step="1"
                   class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition placeholder-gray-300 font-mono">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1.5">إلى</label>
            <input type="number" name="payments_count_to" placeholder="∞" min="0" step="1"
                   class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 transition placeholder-gray-300 font-mono">
        </div>
    </div>

    {{-- Verification status --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">حالة التحقق</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                <input type="checkbox" name="verification_status_id[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                بدون حالة
            </label>
            @foreach($verificationStatuses as $vs)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="verification_status_id[]" value="{{ $vs->id }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $vs->color }}"></span>
                    {{ $vs->name }}
                </label>
            @endforeach
        </div>
    </div>

    {{-- Final status --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة النهائية</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                <input type="checkbox" name="final_status_id[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                بدون
            </label>
            @foreach($finalStatusList as $fs)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="final_status_id[]" value="{{ $fs->id }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $fs->color }}"></span>
                    {{ $fs->name }}
                </label>
            @endforeach
        </div>
    </div>

    {{-- Marital status --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة الاجتماعية</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                <input type="checkbox" name="marital_status[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                <span class="inline-block w-2 h-2 rounded-full flex-shrink-0 bg-gray-300"></span>
                بدون
            </label>
            @foreach($maritalStatusList as $ms)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="marital_status[]" value="{{ $ms->name }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    {{ $ms->name }}
                </label>
            @endforeach
        </div>
    </div>

    {{-- Gender --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">الجنس</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
            @foreach(['ذكر', 'أنثى'] as $g)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="gender[]" value="{{ $g }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    {{ $g }}
                </label>
            @endforeach
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-t border-gray-100">
                <input type="checkbox" name="gender[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                غير محدد
            </label>
        </div>
    </div>

    {{-- Association --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">الجمعية</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                <input type="checkbox" name="association_id[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                بدون
            </label>
            @forelse($associationList as $assoc)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="association_id[]" value="{{ $assoc->id }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    {{ $assoc->name }}
                </label>
            @empty
                <p class="px-3 py-2 text-xs text-gray-400">لا توجد جمعيات</p>
            @endforelse
        </div>
    </div>

    {{-- Special cases --}}
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">الحالات الخاصة</label>
        <select name="special_cases" onwheel="this.blur()"
                class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:bg-white transition text-gray-500">
            <option value="">— الكل —</option>
            <option value="1">نعم</option>
            <option value="0">لا</option>
        </select>
    </div>

    {{-- Sham cash --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">شام كاش</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
            @foreach(['done' => 'تم', 'manual' => 'يدوي', 'none' => 'لا يوجد'] as $val => $lbl)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="sham_cash[]" value="{{ $val }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    {{ $lbl }}
                </label>
            @endforeach
        </div>
    </div>

    {{-- Sector --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">القطاع</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
            <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400" placeholder="بحث في القطاعات...">
            </div>
            <div class="overflow-y-auto" style="max-height:200px">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="sector_id[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    بدون
                </label>
                @forelse($sectorList as $sec)
                    <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                        <input type="checkbox" name="sector_id[]" value="{{ $sec->id }}" class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        {{ $sec->name }}
                    </label>
                @empty
                    <p class="px-3 py-2 text-xs text-gray-400">لا توجد قطاعات</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Representative --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">المندوب المسؤول</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                <input type="checkbox" name="representative_id[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                بدون
            </label>
            @forelse($representativeList as $rep)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="representative_id[]" value="{{ $rep->id }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    {{ $rep->name }}
                </label>
            @empty
                <p class="px-3 py-2 text-xs text-gray-400">لا يوجد مندوبون</p>
            @endforelse
        </div>
    </div>

    {{-- Region --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">المنطقة</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
            <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="بحث في المناطق...">
            </div>
            <div class="overflow-y-auto" style="max-height:200px">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="region_id[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    بدون
                </label>
                @forelse($regionList as $reg)
                    <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                        <input type="checkbox" name="region_id[]" value="{{ $reg->id }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                        {{ $reg->name }}
                    </label>
                @empty
                    <p class="px-3 py-2 text-xs text-gray-400">لا توجد مناطق</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Delegate --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">المندوب</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                <input type="checkbox" name="delegate[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                بدون
            </label>
            @forelse($delegateList as $d)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="delegate[]" value="{{ $d }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    {{ $d }}
                </label>
            @empty
                <p class="px-3 py-2 text-xs text-gray-400">لا يوجد مندوبون</p>
            @endforelse
        </div>
    </div>

    {{-- Second person --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">الفرد الثاني</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                <input type="checkbox" name="second_person[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                بدون
            </label>
            @forelse($secondPersonList as $sp)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="second_person[]" value="{{ $sp }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    {{ $sp }}
                </label>
            @empty
                <p class="px-3 py-2 text-xs text-gray-400">لا يوجد أفراد ثانيون</p>
            @endforelse
        </div>
    </div>

    {{-- Special cases description --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">وصف الحالة الخاصة</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
            <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="بحث في الأوصاف...">
            </div>
            <div class="overflow-y-auto" style="max-height:200px">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="special_cases_description[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    بدون
                </label>
                @forelse($specialDescriptionList as $sd)
                    <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                        <input type="checkbox" name="special_cases_description[]" value="{{ $sd }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                        <span class="truncate">{{ $sd }}</span>
                    </label>
                @empty
                    <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات خاصة مسجلة</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Network --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">نوع الشبكة</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1">
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                <input type="checkbox" name="network[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                بدون
            </label>
            @foreach(['MTN', 'SYRIATEL'] as $net)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="network[]" value="{{ $net }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    {{ $net }}
                </label>
            @endforeach
        </div>
    </div>

    {{-- Payment data entry --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">اسم مدخل الدفع</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
            <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                <input type="checkbox" name="payment_data_entry[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                بدون
            </label>
            @forelse($paymentDataEntryList as $pde)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="payment_data_entry[]" value="{{ $pde }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    {{ $pde }}
                </label>
            @empty
                <p class="px-3 py-2 text-xs text-gray-400">لا توجد بيانات</p>
            @endforelse
        </div>
    </div>

    {{-- Current address --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">العنوان التفصيلي</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden" style="max-height:260px">
            <div class="p-2 border-b border-gray-100 sticky top-0 bg-white">
                <input type="text" class="ms-search w-full text-sm border border-gray-200 rounded-lg px-2.5 py-1.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="بحث في العناوين...">
            </div>
            <div class="overflow-y-auto" style="max-height:200px">
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-500 border-b border-gray-100">
                    <input type="checkbox" name="current_address[]" value="none" class="ms-check rounded border-gray-300 text-gray-500 focus:ring-gray-400">
                    بدون
                </label>
                @forelse($addressList as $addr)
                    <label class="ms-option flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                        <input type="checkbox" name="current_address[]" value="{{ $addr }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                        <span class="truncate">{{ $addr }}</span>
                    </label>
                @empty
                    <p class="px-3 py-2 text-xs text-gray-400">لا توجد عناوين مسجلة</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Housing status --}}
    <div class="ms-dropdown relative">
        <label class="block text-xs font-semibold text-gray-500 mb-1.5">وضع السكن</label>
        <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-right">
            <span class="ms-label text-gray-500 truncate">— الكل —</span>
            <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
            @forelse($housingStatusList as $hs)
                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-gray-50 cursor-pointer text-sm text-gray-700">
                    <input type="checkbox" name="housing_status_id[]" value="{{ $hs->id }}" class="ms-check rounded border-gray-300 text-emerald-600 focus:ring-emerald-400">
                    <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hs->color }}"></span>
                    {{ $hs->name }}
                </label>
            @empty
                <p class="px-3 py-2 text-xs text-gray-400">لا توجد أوضاع سكن</p>
            @endforelse
        </div>
    </div>

    {{-- Field visit filters --}}
    <div class="border border-indigo-100 rounded-xl overflow-hidden">
        <button type="button" onclick="toggleFvFiltersExport()"
                class="w-full flex items-center justify-between gap-2 px-3 py-2.5 bg-indigo-50/60 hover:bg-indigo-50 transition-colors text-right">
            <div class="flex items-center gap-2">
                <div class="w-5 h-5 rounded-md bg-indigo-100 flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-indigo-700">فلاتر الجولة الميدانية</span>
            </div>
            <svg id="fv-export-arrow" class="w-4 h-4 text-indigo-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div id="fv-export-body" class="hidden px-3 py-3 bg-indigo-50/20 space-y-3">

            {{-- حالة الجولة --}}
            <div class="ms-dropdown relative">
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">حالة الجولة</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                        <input type="checkbox" name="field_visit_status_id[]" value="none" class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-gray-300"></span>
                        بدون جولة ميدانية
                    </label>
                    @forelse($fieldVisitStatuses as $fvs)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="field_visit_status_id[]" value="{{ $fvs->id }}" class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $fvs->color }}"></span>
                            {{ $fvs->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-sm text-gray-400">لا توجد حالات</p>
                    @endforelse
                </div>
            </div>

            {{-- نوع البيت --}}
            <div class="ms-dropdown relative">
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">نوع البيت</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($houseTypes as $ht)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="fv_house_type_id[]" value="{{ $ht->id }}" class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $ht->color }}"></span>
                            {{ $ht->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-sm text-gray-400">لا توجد أنواع</p>
                    @endforelse
                </div>
            </div>

            {{-- حالة البيت --}}
            <div class="ms-dropdown relative">
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">حالة البيت</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($houseConditions as $hc)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="fv_house_condition_id[]" value="{{ $hc->id }}" class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $hc->color }}"></span>
                            {{ $hc->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-xs text-gray-400">لا توجد حالات</p>
                    @endforelse
                </div>
            </div>

            {{-- الزائر --}}
            <div class="ms-dropdown relative">
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">الزائر</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($fvVisitorList as $vis)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700 ms-option">
                            <input type="checkbox" name="fv_visitors[]" value="{{ $vis }}" class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            {{ $vis }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-sm text-gray-400">لا يوجد زوار مسجّلون</p>
                    @endforelse
                </div>
            </div>

            {{-- من أضاف الجولة --}}
            <div class="ms-dropdown relative">
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">من أضاف الجولة</label>
                <button type="button" class="ms-btn w-full flex items-center justify-between text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-right">
                    <span class="ms-label text-gray-500 truncate">— الكل —</span>
                    <svg class="ms-arrow w-4 h-4 text-gray-400 flex-shrink-0 mr-1 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="ms-panel hidden absolute z-30 top-full mt-1 w-full bg-white border border-indigo-100 rounded-xl shadow-lg py-1 max-h-56 overflow-y-auto">
                    @forelse($fvCreatedByList as $u)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-indigo-50 cursor-pointer text-sm text-gray-700">
                            <input type="checkbox" name="fv_created_by[]" value="{{ $u->id }}" class="ms-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                            {{ $u->name }}
                        </label>
                    @empty
                        <p class="px-3 py-2 text-sm text-gray-400">لا يوجد بيانات بعد</p>
                    @endforelse
                </div>
            </div>

            {{-- تاريخ الزيارة --}}
            <div>
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">تاريخ الزيارة</label>
                <div class="flex items-center gap-1.5">
                    <input type="date" name="fv_date_from"
                           class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                    <span class="text-xs text-indigo-400 shrink-0">—</span>
                    <input type="date" name="fv_date_to"
                           class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                </div>
            </div>

            {{-- مبلغ الجولة --}}
            <div>
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">مبلغ الجولة (ل.س)</label>
                <div class="flex items-center gap-1.5">
                    <input type="number" name="fv_amount_from" placeholder="من" min="0"
                           class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                    <span class="text-xs text-indigo-400 shrink-0">—</span>
                    <input type="number" name="fv_amount_to" placeholder="إلى" min="0"
                           class="flex-1 min-w-0 text-sm border border-indigo-200 rounded-xl px-2.5 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
                </div>
            </div>

            {{-- الملاحظات --}}
            <div>
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">الملاحظات</label>
                <input type="text" name="fv_notes" placeholder="بحث في الملاحظات..."
                       class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition placeholder-gray-300">
            </div>

            {{-- يوجد فيديو --}}
            <div>
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">يوجد فيديو</label>
                <select name="fv_has_video" onwheel="this.blur()"
                        class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1">نعم</option>
                    <option value="0">لا</option>
                </select>
            </div>

            {{-- حالة خاصة --}}
            <div>
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">حالة خاصة</label>
                <select name="fv_has_special_case" onwheel="this.blur()"
                        class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="1">نعم</option>
                    <option value="0">لا</option>
                </select>
            </div>

            {{-- عدد الجولات --}}
            <div>
                <label class="block text-xs font-bold text-indigo-600 mb-1.5">عدد الجولات</label>
                <select name="fv_count" onwheel="this.blur()"
                        class="w-full text-sm border border-indigo-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400 transition text-gray-500">
                    <option value="">— الكل —</option>
                    <option value="0">بدون جولات</option>
                    <option value="1">جولة واحدة فأكثر</option>
                    <option value="2">جولتان فأكثر</option>
                    <option value="3">3 جولات فأكثر</option>
                </select>
            </div>

        </div>
    </div>

</div>
