@php
    $isEdit     = isset($member);
    $scores     = $isEdit ? $member->scores      : null;
    $payment    = $isEdit ? $member->paymentInfo   : null;
    $paymentAI  = $isEdit ? $member->paymentInfoAI : null;
    $v = fn($field, $default = '') => old($field, $isEdit ? ($member->$field ?? $default) : $default);

    $inputClass = 'w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 focus:bg-white transition placeholder-gray-300';
    $selectClass = 'w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 focus:bg-white transition';
    $labelClass  = 'block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5';
    $errorInput  = 'border-red-300 bg-red-50 focus:ring-red-400 focus:border-red-400';
@endphp

@php
    function sectionHeader(string $title, string $icon, string $from, string $to, string $textColor): string {
        return '<div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br '.$from.' '.$to.' flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$icon.'"/></svg>
            </div>
            <h2 class="text-sm font-black '.$textColor.' uppercase tracking-wide">'.$title.'</h2>
            <div class="flex-1 h-px bg-gradient-to-l '.$from.' '.$to.' opacity-20"></div>
        </div>';
    }
@endphp

{{-- ════════════════════════════════════════ --}}
{{-- ١ · البيانات الشخصية                    --}}
{{-- ════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <h2 class="text-sm font-black text-blue-700 uppercase tracking-wide">البيانات الشخصية</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <div>
            <label class="{{ $labelClass }}">رقم الإضبارة</label>
            <input type="text" name="dossier_number" value="{{ $v('dossier_number') }}"
                   placeholder="مثال: 2026/145"
                   class="{{ $inputClass }} @error('dossier_number') {{ $errorInput }} @enderror">
            @error('dossier_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="{{ $labelClass }}">الاسم الكامل <span class="text-red-500 normal-case">*</span></label>
            <input type="text" name="full_name" value="{{ $v('full_name') }}" required
                   placeholder="الاسم الرباعي"
                   class="{{ $inputClass }} @error('full_name') {{ $errorInput }} @enderror">
            @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="{{ $labelClass }}">اسم الأم</label>
            <input type="text" name="mother_name" value="{{ $v('mother_name') }}"
                   class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $labelClass }}">رقم الهوية <span class="text-red-500 normal-case">*</span></label>
            <input type="text" name="national_id" value="{{ $v('national_id') }}" required
                   class="{{ $inputClass }} font-mono @error('national_id') {{ $errorInput }} @enderror">
            @error('national_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="{{ $labelClass }}">رقم الهاتف</label>
            <input type="tel" name="phone" value="{{ $v('phone') }}"
                   placeholder="09XXXXXXXX"
                   class="{{ $inputClass }} font-mono">
        </div>

        <div>
            <label class="{{ $labelClass }}">رقم الهاتف الثاني</label>
            <input type="tel" name="phone2" value="{{ $v('phone2') }}"
                   placeholder="09XXXXXXXX"
                   class="{{ $inputClass }} font-mono">
        </div>

        <div>
            <label class="{{ $labelClass }}">العمر</label>
            <input type="number" name="age" value="{{ $v('age') }}" min="0" max="120"
                   placeholder="بالسنوات"
                   class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $labelClass }}">الجنس</label>
            <select name="gender" class="{{ $selectClass }}">
                <option value="">— غير محدد —</option>
                <option value="ذكر"  {{ $v('gender') === 'ذكر'  ? 'selected' : '' }}>ذكر</option>
                <option value="أنثى" {{ $v('gender') === 'أنثى' ? 'selected' : '' }}>أنثى</option>
            </select>
        </div>

        <div>
            <label class="{{ $labelClass }}">الحالة الاجتماعية</label>
            <select name="marital_status" class="{{ $selectClass }}">
                <option value="">— غير محدد —</option>
                @foreach($maritalStatuses as $ms)
                    <option value="{{ $ms->name }}" {{ $v('marital_status') == $ms->name ? 'selected' : '' }}>
                        {{ $ms->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="{{ $labelClass }}">المدخل</label>
            <select name="representative_id" class="{{ $selectClass }}">
                <option value="">— غير محدد —</option>
                @foreach($representatives as $rep)
                    <option value="{{ $rep->id }}"
                        {{ old('representative_id', $isEdit ? $member->representative_id : auth()->id()) == $rep->id ? 'selected' : '' }}>
                        {{ $rep->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="{{ $labelClass }}">اسم المدخل (يدوي)</label>
            <input type="text" name="data_entry_name" value="{{ $v('data_entry_name') }}"
                   placeholder="اسم مدخل البيانات"
                   class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $labelClass }}">مندوب</label>
            <input type="text" name="delegate" value="{{ $v('delegate') }}"
                   placeholder="اسم المندوب"
                   class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $labelClass }}">الفرد الثاني</label>
            <input type="text" name="second_person" value="{{ $v('second_person') }}"
                   placeholder="اسم الفرد الثاني"
                   class="{{ $inputClass }}">
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════ --}}
{{-- ٢ · العنوان والوضع المعيشي              --}}
{{-- ════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <h2 class="text-sm font-black text-amber-700 uppercase tracking-wide">العنوان والوضع المعيشي</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-2">
            <label class="{{ $labelClass }}">العنوان التفصيلي</label>
            <textarea name="current_address" rows="2" placeholder="المحافظة / المدينة / الحي"
                      class="{{ $inputClass }} resize-none">{{ $v('current_address') }}</textarea>
        </div>

        <div>
            <label class="{{ $labelClass }}">المنطقة</label>
            @php
                $selRegionId   = (int) old('region_id', $member->region_id ?? 0);
                $selRegion     = ($regionsList ?? collect())->firstWhere('id', $selRegionId);
                $selRegionName = $selRegion?->name ?? '';
            @endphp
            <input type="hidden" name="region_id" id="region_id_select" value="{{ $selRegionId ?: '' }}">
            <div class="flex gap-2 items-start">
                <div class="relative flex-1" id="form-region-dropdown">
                    <button type="button" onclick="toggleFormRegionDropdown(event)"
                            class="w-full flex items-center justify-between gap-2 border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 hover:border-emerald-400 focus:outline-none transition text-right">
                        <span id="form-region-label" class="truncate {{ $selRegionName ? 'text-gray-800' : 'text-gray-400' }}">
                            {{ $selRegionName ?: '— غير محدد —' }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" id="form-region-chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="form-region-panel" class="hidden absolute z-50 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                        <div class="p-2 border-b border-gray-100">
                            <input type="text" id="form-region-search" placeholder="ابحث في المناطق..."
                                   oninput="filterFormRegions(this.value)" autocomplete="off"
                                   class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:outline-none">
                        </div>
                        <ul id="form-region-list" class="max-h-52 overflow-y-auto py-1">
                            <li class="form-region-item">
                                <button type="button" onclick="selectFormRegion('', '— غير محدد —')"
                                        class="w-full text-right px-3 py-2 text-sm text-gray-400 hover:bg-gray-50 transition-colors">
                                    — غير محدد —
                                </button>
                            </li>
                            @foreach($regionsList ?? [] as $region)
                            <li class="form-region-item">
                                <button type="button"
                                        onclick="selectFormRegion('{{ $region->id }}', '{{ addslashes($region->name) }}', '{{ $region->sector_id ?? '' }}', '{{ addslashes($region->sector?->name ?? '') }}')"
                                        data-name="{{ mb_strtolower($region->name) }}"
                                        data-sector-id="{{ $region->sector_id ?? '' }}"
                                        data-sector-name="{{ $region->sector?->name ?? '' }}"
                                        class="w-full text-right px-3 py-2 text-sm transition-colors hover:bg-emerald-50
                                               {{ $selRegionId === $region->id ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-gray-700' }}">
                                    {{ $region->name }}
                                    @if($region->sector)
                                        <span class="text-xs text-gray-400 mr-1">({{ $region->sector->name }})</span>
                                    @endif
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        <div id="form-region-no-results" class="hidden px-3 py-2 text-xs text-gray-400 text-center">لا توجد نتائج</div>
                    </div>
                </div>
                <button type="button" onclick="toggleAddRegion()"
                        title="إضافة منطقة جديدة"
                        class="shrink-0 w-10 h-10 flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-xl text-emerald-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </div>
            {{-- Inline add region --}}
            <div id="add-region-panel" class="hidden mt-2 flex gap-2 items-center">
                <input type="text" id="new-region-input" placeholder="اسم المنطقة الجديدة..."
                       class="flex-1 border border-emerald-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 bg-white"
                       onkeydown="if(event.key==='Enter'){event.preventDefault();submitNewRegion();}">
                <button type="button" onclick="submitNewRegion()"
                        class="shrink-0 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors">
                    حفظ
                </button>
                <button type="button" onclick="toggleAddRegion()"
                        class="shrink-0 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-colors">
                    إلغاء
                </button>
            </div>
            <p id="add-region-error" class="hidden text-red-500 text-xs mt-1"></p>
        </div>

        <div>
            <label class="{{ $labelClass }}">القطاع</label>
            @php
                $selSectorId   = (int) old('sector_id', $member->sector_id ?? 0);
                $selSector     = ($sectorsList ?? collect())->firstWhere('id', $selSectorId);
                $selSectorName = $selSector?->name ?? '';
                $isAdmin       = auth()->user()?->role === 'admin';
            @endphp
            <input type="hidden" name="sector_id" id="sector_id_select" value="{{ $selSectorId ?: '' }}">

            @if($isAdmin)
                {{-- Admin: full editable dropdown + add button --}}
                <div class="flex gap-2">
                <div class="relative flex-1" id="form-sector-dropdown">
                    <button type="button" onclick="toggleFormSectorDropdown(event)"
                            class="w-full flex items-center justify-between gap-2 border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 hover:border-indigo-400 focus:outline-none transition text-right">
                        <span id="form-sector-label" class="truncate {{ $selSectorName ? 'text-gray-800' : 'text-gray-400' }}">
                            {{ $selSectorName ?: '— غير محدد —' }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" id="form-sector-chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div id="form-sector-panel" class="hidden absolute z-50 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                        <div class="p-2 border-b border-gray-100">
                            <input type="text" id="form-sector-search" placeholder="ابحث في القطاعات..."
                                   oninput="filterFormSectors(this.value)" autocomplete="off"
                                   class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        </div>
                        <ul id="form-sector-list" class="max-h-48 overflow-y-auto py-1">
                            <li class="form-sector-item">
                                <button type="button" onclick="selectFormSector('', '— غير محدد —')"
                                        class="w-full text-right px-3 py-2 text-sm text-gray-400 hover:bg-gray-50 transition-colors">
                                    — غير محدد —
                                </button>
                            </li>
                            @foreach($sectorsList ?? [] as $sector)
                            <li class="form-sector-item">
                                <button type="button"
                                        onclick="selectFormSector('{{ $sector->id }}', '{{ addslashes($sector->name) }}')"
                                        data-name="{{ mb_strtolower($sector->name) }}"
                                        class="w-full text-right px-3 py-2 text-sm transition-colors hover:bg-indigo-50
                                               {{ $selSectorId === $sector->id ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-700' }}">
                                    {{ $sector->name }}
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        <div id="form-sector-no-results" class="hidden px-3 py-2 text-xs text-gray-400 text-center">لا توجد نتائج</div>
                    </div>
                </div>
                <button type="button" onclick="toggleAddSector()"
                        title="إضافة قطاع جديد"
                        class="shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-xl text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                </div>
                {{-- Inline add sector --}}
                <div id="add-sector-panel" class="hidden mt-2 flex gap-2 items-center">
                    <input type="text" id="new-sector-input" placeholder="اسم القطاع الجديد..."
                           class="flex-1 border border-indigo-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 bg-white"
                           onkeydown="if(event.key==='Enter'){event.preventDefault();submitNewSector();}">
                    <button type="button" onclick="submitNewSector()"
                            class="shrink-0 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors">
                        حفظ
                    </button>
                    <button type="button" onclick="toggleAddSector()"
                            class="shrink-0 px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold rounded-xl transition-colors">
                        إلغاء
                    </button>
                </div>
                <p id="add-sector-error" class="hidden text-red-500 text-xs mt-1"></p>
            @else
                {{-- Non-admin: read-only display, set automatically from region --}}
                <div id="form-sector-dropdown" class="w-full flex items-center gap-2 border border-gray-100 rounded-xl px-4 py-2.5 text-sm bg-gray-50/60 text-right">
                    <svg class="w-4 h-4 text-indigo-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span id="form-sector-label" class="truncate flex-1 {{ $selSectorName ? 'text-gray-700' : 'text-gray-400' }}">
                        {{ $selSectorName ?: '— يُحدَّد تلقائياً من المنطقة —' }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">يُعيَّن القطاع تلقائياً عند اختيار المنطقة</p>
            @endif
        </div>

        <div>
            <label class="{{ $labelClass }}">الوظيفة / العمل</label>
            <input type="text" name="job" value="{{ $v('job') }}"
                   class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $labelClass }}">وضع السكن</label>
            <select name="housing_status_id" class="{{ $selectClass }}">
                <option value="">— غير محدد —</option>
                @foreach($housingStatuses as $hs)
                    <option value="{{ $hs->id }}" {{ (int)$v('housing_status_id') === $hs->id ? 'selected' : '' }}>
                        {{ $hs->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="{{ $labelClass }}">عدد المعالين</label>
            <input type="number" name="dependents_count" value="{{ $v('dependents_count', 0) }}" min="0"
                   class="{{ $inputClass }}">
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════ --}}
{{-- ٣ · الحالة الصحية                       --}}
{{-- ════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>
        <h2 class="text-sm font-black text-rose-700 uppercase tracking-wide">الحالة الصحية والطبية</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <div>
            <label class="{{ $labelClass }}">نوع المرض / الحالة</label>
            <input type="text" name="disease_type" value="{{ $v('disease_type') }}"
                   class="{{ $inputClass }}">
        </div>

        <div class="lg:col-span-3">
            <label class="{{ $labelClass }}">تفاصيل المرض / الإصابة</label>
            <textarea name="illness_details" rows="3" placeholder="وصف تفصيلي للحالة الصحية..."
                      class="{{ $inputClass }} resize-none">{{ $v('illness_details') }}</textarea>
        </div>

        <div>
            <label class="{{ $labelClass }} mb-2">حالة خاصة</label>
            <label class="flex items-center gap-3 cursor-pointer bg-orange-50 border border-orange-200 rounded-xl px-4 py-3 hover:bg-orange-100 transition-colors w-fit">
                <input type="checkbox" name="special_cases" id="special_cases" value="1"
                       {{ old('special_cases', $isEdit ? $member->special_cases : false) ? 'checked' : '' }}
                       class="h-4 w-4 rounded text-orange-500 border-gray-300 focus:ring-orange-400">
                <span class="text-sm font-semibold text-orange-700">يوجد حالة خاصة</span>
            </label>
        </div>

        <div class="lg:col-span-2">
            <label class="{{ $labelClass }}">وصف الحالات الخاصة</label>
            <textarea name="special_cases_description" rows="2" placeholder="وصف الحالة الخاصة..."
                      class="{{ $inputClass }} resize-none">{{ $v('special_cases_description') }}</textarea>
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════ --}}
{{-- ٤ · بيانات إضافية وتقييم               --}}
{{-- ════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
        </div>
        <h2 class="text-sm font-black text-violet-700 uppercase tracking-wide">بيانات إضافية وتقييم</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <div>
            <label class="{{ $labelClass }}">الشبكة</label>
            <select name="network" class="{{ $selectClass }}">
                <option value="">— غير محدد —</option>
                <option value="MTN"      {{ $v('network') === 'MTN'      ? 'selected' : '' }}>MTN</option>
                <option value="SYRIATEL" {{ $v('network') === 'SYRIATEL' ? 'selected' : '' }}>Syriatel</option>
            </select>
        </div>

        <div>
            <label class="{{ $labelClass }}">حالة المشغل</label>
            <input type="text" name="provider_status" value="{{ $v('provider_status') }}"
                   class="{{ $inputClass }}">
        </div>

        <div class="lg:col-span-2">
            <label class="{{ $labelClass }}">حالة التحقق <span class="text-red-500 normal-case">*</span></label>
            <select name="verification_status_id" required
                    class="{{ $selectClass }} @error('verification_status_id') {{ $errorInput }} @enderror">
                <option value="">— اختر حالة التحقق —</option>
                @foreach($verificationStatuses as $vs)
                    <option value="{{ $vs->id }}"
                            {{ old('verification_status_id', $isEdit ? $member->verification_status_id : '') == $vs->id ? 'selected' : '' }}>
                        {{ $vs->name }}
                    </option>
                @endforeach
            </select>
            @error('verification_status_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="lg:col-span-2">
            <label class="{{ $labelClass }}">الحالة النهائية</label>
            @if(auth()->user()?->role === 'admin')
                <select name="final_status_id"
                        class="{{ $selectClass }} @error('final_status_id') {{ $errorInput }} @enderror">
                    <option value="">— غير محدد —</option>
                    @foreach($finalStatuses as $fs)
                        <option value="{{ $fs->id }}"
                                {{ old('final_status_id', $isEdit ? $member->final_status_id : '') == $fs->id ? 'selected' : '' }}>
                            {{ $fs->name }}
                        </option>
                    @endforeach
                </select>
                @error('final_status_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @else
                @php $fs = $isEdit ? $member->finalStatus : null; @endphp
                <div class="flex items-center gap-2 px-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm text-gray-500">
                    @if($fs)
                        <span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $fs->color }}"></span>
                        <span style="color:{{ $fs->color }}">{{ $fs->name }}</span>
                    @else
                        <span class="text-gray-400">— غير محدد —</span>
                    @endif
                    <span class="mr-auto text-xs text-gray-400">للمسؤولين فقط</span>
                </div>
            @endif
        </div>

        {{-- الانتساب لجمعية --}}
        <div class="lg:col-span-4">
            <label class="{{ $labelClass }} mb-2">الانتساب لجمعية أخرى</label>
            @php
                $hasAssoc      = $isEdit ? $member->other_association : false;
                $hasAssocOld   = old('has_other_association', $hasAssoc ? 'yes' : 'no');
                $checkedAssocs = old('association_ids', $isEdit ? $member->associations->pluck('id')->toArray() : []);
            @endphp
            <div class="flex gap-3 mb-3">
                <label class="flex items-center gap-2.5 cursor-pointer bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-2.5 hover:bg-emerald-100 transition-colors">
                    <input type="radio" name="has_other_association" value="yes" id="assoc_yes"
                           {{ $hasAssocOld === 'yes' ? 'checked' : '' }}
                           onchange="toggleAssociations(this.value)"
                           class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                    <span class="text-sm font-semibold text-emerald-700">نعم</span>
                </label>
                <label class="flex items-center gap-2.5 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 hover:bg-gray-100 transition-colors">
                    <input type="radio" name="has_other_association" value="no" id="assoc_no"
                           {{ $hasAssocOld !== 'yes' ? 'checked' : '' }}
                           onchange="toggleAssociations(this.value)"
                           class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                    <span class="text-sm font-semibold text-gray-600">لا</span>
                </label>
            </div>
            <div id="associations_list" class="{{ $hasAssocOld === 'yes' ? '' : 'hidden' }} flex flex-wrap gap-2">
                @foreach($associations as $assoc)
                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2 hover:bg-emerald-50 hover:border-emerald-300 transition-colors text-sm">
                        <input type="checkbox" name="association_ids[]" value="{{ $assoc->id }}"
                               {{ in_array($assoc->id, (array)$checkedAssocs) ? 'checked' : '' }}
                               class="h-4 w-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        <span class="text-gray-700 font-medium">{{ $assoc->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-4">
            @php
                $shamVal   = old('sham_cash_account', $isEdit ? ($member->sham_cash_account ?? '') : '');
                $canEditSham = auth()->user()?->role === 'admin';
                $shamLabels  = ['' => 'لا', 'done' => 'نعم (تم)', 'manual' => 'يدوي'];
            @endphp
            <label class="{{ $labelClass }}">
                شام كاش
                @if(!$canEditSham)
                    <span class="mr-1 text-xs font-normal text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded-md">للمسؤول فقط</span>
                @endif
            </label>
            @if($canEditSham)
                <div class="flex items-center gap-3 flex-wrap mt-1">
                    @foreach($shamLabels as $optVal => $optLabel)
                    <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-xl border text-sm font-medium transition-colors
                        {{ $shamVal === $optVal ? 'bg-emerald-50 border-emerald-400 text-emerald-700' : 'bg-white border-gray-200 text-gray-600 hover:border-emerald-300' }}">
                        <input type="radio" name="sham_cash_account" value="{{ $optVal }}"
                               {{ $shamVal === $optVal ? 'checked' : '' }}
                               class="text-emerald-600 focus:ring-emerald-500">
                        {{ $optLabel }}
                    </label>
                    @endforeach
                </div>
            @else
                <input type="hidden" name="sham_cash_account" value="{{ $shamVal }}">
                <div class="mt-1 px-3 py-2 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-500 inline-flex items-center gap-2 cursor-not-allowed select-none">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    {{ $shamLabels[$shamVal] ?? 'لا' }}
                </div>
            @endif
        </div>

        {{-- Score & Amount (readonly) --}}
        <div>
            <label class="{{ $labelClass }}">الدرجة <span class="text-emerald-500 normal-case font-medium">(تلقائي)</span></label>
            <div class="relative">
                <input type="number" id="field_score" name="score" readonly
                       value="{{ old('score', $isEdit ? $member->score : 0) }}"
                       class="w-full border border-emerald-200 bg-gradient-to-l from-emerald-50 to-teal-50 text-emerald-700 font-black text-lg rounded-xl px-4 py-2.5 cursor-not-allowed text-center">
            </div>
        </div>

        <div>
            <label class="{{ $labelClass }}">المبلغ المقدر <span class="text-blue-500 normal-case font-medium">(× 500 ل.س)</span></label>
            <input type="number" id="field_estimated_amount" name="estimated_amount" readonly
                   value="{{ old('estimated_amount', $isEdit ? $member->estimated_amount : 0) }}"
                   class="w-full border border-blue-200 bg-gradient-to-l from-blue-50 to-indigo-50 text-blue-700 font-black rounded-xl px-4 py-2.5 cursor-not-allowed text-center">
        </div>

        <div>
            <label class="{{ $labelClass }}">عدد الدفعات</label>
            <input type="number" name="payments_count" min="0" step="1"
                   value="{{ old('payments_count', $isEdit ? $member->payments_count : '') }}"
                   placeholder="0"
                   class="{{ $inputClass }} @error('payments_count') {{ $errorInput }} @enderror">
            @error('payments_count') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="lg:col-span-4">
            <label class="{{ $labelClass }}">ملاحظة</label>
            <textarea name="notes" rows="3" placeholder="ملاحظات عن المستفيد..."
                      class="{{ $inputClass }} resize-none">{{ old('notes', $isEdit ? $member->notes : '') }}</textarea>
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════ --}}
{{-- ٥ · نقاط التقييم                        --}}
{{-- ════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-5">
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h2 class="text-sm font-black text-emerald-700 uppercase tracking-wide">نقاط التقييم</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        @php
        $scoreFields = [
            ['name' => 'dependent_status_score', 'label' => 'نقاط حالة المعيل', 'max' => 2,  'color' => 'violet'],
            ['name' => 'work_score',              'label' => 'نقاط العمل',        'max' => 2,  'color' => 'amber'],
            ['name' => 'housing_score',           'label' => 'نقاط السكن',        'max' => 4,  'color' => 'blue'],
            ['name' => 'dependents_score',        'label' => 'نقاط عدد الأفراد', 'max' => 20, 'color' => 'cyan'],
            ['name' => 'illness_score',           'label' => 'نقاط المرض',        'max' => 5,  'color' => 'red'],
            ['name' => 'special_cases_score',     'label' => 'نقاط الحالات الخاصة','max' => 10,'color' => 'orange'],
        ];
        $colorBorder = ['violet'=>'border-violet-200','amber'=>'border-amber-200','blue'=>'border-blue-200','cyan'=>'border-cyan-200','red'=>'border-red-200','orange'=>'border-orange-200'];
        $colorBg     = ['violet'=>'bg-violet-50','amber'=>'bg-amber-50','blue'=>'bg-blue-50','cyan'=>'bg-cyan-50','red'=>'bg-red-50','orange'=>'bg-orange-50'];
        $colorText   = ['violet'=>'text-violet-700','amber'=>'text-amber-700','blue'=>'text-blue-700','cyan'=>'text-cyan-700','red'=>'text-red-700','orange'=>'text-orange-700'];
        $colorLabel  = ['violet'=>'text-violet-600','amber'=>'text-amber-600','blue'=>'text-blue-600','cyan'=>'text-cyan-600','red'=>'text-red-600','orange'=>'text-orange-600'];
        @endphp

        @foreach($scoreFields as $sf)
        @php
            $c = $sf['color'];
            $val = old($sf['name'], $scores ? $scores->{$sf['name']} : 0);
        @endphp
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5 flex items-center gap-1.5">
                {{ $sf['label'] }}
                <span class="{{ $colorLabel[$c] }} normal-case font-medium">(أقصى {{ $sf['max'] }})</span>
            </label>
            <input type="number" name="{{ $sf['name'] }}" min="0" max="{{ $sf['max'] }}"
                   oninput="calcTotal()"
                   value="{{ $val }}"
                   class="w-full border {{ $colorBorder[$c] }} {{ $colorBg[$c] }} {{ $colorText[$c] }} font-bold rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-{{ $c }}-400 transition text-center">
        </div>
        @endforeach

        {{-- إضافة نقاط --}}
        <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
            <div>
                <label class="block text-xs font-bold text-emerald-700 uppercase tracking-wide mb-1.5">إضافة نقاط</label>
                <input type="number" name="score_addition" min="0" oninput="calcTotal()"
                       value="{{ $scores?->score_addition ?? 0 }}"
                       class="w-full border border-emerald-200 bg-white text-emerald-700 font-bold rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition text-center">
            </div>
            <div>
                <label class="block text-xs font-bold text-emerald-700 uppercase tracking-wide mb-1.5">سبب الإضافة</label>
                <input type="text" name="score_addition_reason"
                       value="{{ $scores?->score_addition_reason ?? '' }}"
                       placeholder="سبب إضافة النقاط..."
                       class="w-full border border-emerald-200 bg-white text-gray-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 transition">
            </div>
        </div>

        {{-- انقاص النقاط --}}
        <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 bg-red-50 border border-red-200 rounded-2xl">
            <div>
                <label class="block text-xs font-bold text-red-600 uppercase tracking-wide mb-1.5">انقاص النقاط</label>
                <input type="number" name="score_deduction" min="0" oninput="calcTotal()"
                       value="{{ $scores?->score_deduction ?? 0 }}"
                       class="w-full border border-red-200 bg-white text-red-700 font-bold rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition text-center">
            </div>
            <div>
                <label class="block text-xs font-bold text-red-600 uppercase tracking-wide mb-1.5">سبب الانقاص</label>
                <input type="text" name="score_deduction_reason"
                       value="{{ $scores?->score_deduction_reason ?? '' }}"
                       placeholder="سبب انقاص النقاط..."
                       class="w-full border border-red-200 bg-white text-gray-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 transition">
            </div>
        </div>

        {{-- Total --}}
        <div class="lg:col-span-3">
            <label class="{{ $labelClass }} mb-2">المجموع الكلي</label>
            <div class="flex items-center gap-4 bg-gradient-to-l from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl px-6 py-4">
                <div id="total_score_display"
                     class="text-5xl font-black text-emerald-600 min-w-[60px] text-center">
                    {{ $scores ? $scores->total_score : 0 }}
                </div>
                <div class="flex-1">
                    <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div id="total_score_bar" class="h-full bg-gradient-to-l from-emerald-400 to-teal-500 rounded-full transition-all duration-300"
                             style="width: {{ $scores ? min(100, ($scores->total_score / 43) * 100) : 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">من أصل 43 نقطة كحدٍّ أقصى</p>
                </div>
                <div class="text-left">
                    <p class="text-xs text-gray-400">المبلغ المقدر</p>
                    <p id="amount_display" class="text-lg font-black text-blue-600">
                        {{ number_format(($scores ? $scores->total_score : 0) * 500) }} ل.س
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════ --}}
{{-- ٦ · معلومات الدفع                       --}}
{{-- ════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-2">
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-slate-500 to-gray-600 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <h2 class="text-sm font-black text-slate-700 uppercase tracking-wide">معلومات الدفع والحسابات</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label class="{{ $labelClass }}">رقم الآيبان (IBAN)</label>
            <input type="text" name="iban" value="{{ old('iban', $payment?->iban) }}"
                   placeholder="SY00 0000 0000 0000" dir="ltr"
                   class="{{ $inputClass }} font-mono">
        </div>

        <div>
            <label class="{{ $labelClass }}">الباركود</label>
            <input type="text" name="barcode" value="{{ old('barcode', $payment?->barcode) }}"
                   dir="ltr" class="{{ $inputClass }} font-mono">
        </div>

        <div>
            <label class="{{ $labelClass }}">اسم المستلم</label>
            <input type="text" name="recipient_name" value="{{ old('recipient_name', $payment?->recipient_name) }}"
                   placeholder="الاسم الكامل للشخص المستلم"
                   class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $labelClass }}">اسم مدخل البيانات</label>
            <input type="text" name="payment_data_entry_name" value="{{ old('payment_data_entry_name', $payment?->data_entry_name) }}"
                   placeholder="اسم من أدخل البيانات"
                   class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $labelClass }}">صورة الآيبان</label>
            <input type="file" name="iban_image" accept="image/*"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 file:ml-3 file:bg-emerald-600 file:text-white file:text-xs file:font-semibold file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:cursor-pointer hover:file:bg-emerald-700 transition">
            @if($isEdit && $payment?->iban_image)
                <p class="text-xs text-emerald-600 mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    {{ basename($payment->iban_image) }}
                </p>
            @endif
        </div>

        <div>
            <label class="{{ $labelClass }}">صورة الباركود</label>
            <input type="file" name="barcode_image" accept="image/*"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-400 file:ml-3 file:bg-emerald-600 file:text-white file:text-xs file:font-semibold file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:cursor-pointer hover:file:bg-emerald-700 transition">
            @if($isEdit && $payment?->barcode_image)
                <p class="text-xs text-emerald-600 mt-1.5 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    {{ basename($payment->barcode_image) }}
                </p>
            @endif
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════ --}}
{{-- ٧ · معلومات الدفع AI                   --}}
{{-- ════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-2">
    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h2 class="text-sm font-black text-violet-700 uppercase tracking-wide">معلومات الدفع AI</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label class="{{ $labelClass }}">رقم الآيبان AI (IBAN)</label>
            <input type="text" name="iban_ai" value="{{ old('iban_ai', $paymentAI?->iban) }}"
                   placeholder="SY00 0000 0000 0000" dir="ltr"
                   class="{{ $inputClass }} font-mono">
        </div>

        <div>
            <label class="{{ $labelClass }}">الباركود AI</label>
            <input type="text" name="barcode_ai" value="{{ old('barcode_ai', $paymentAI?->barcode) }}"
                   dir="ltr" class="{{ $inputClass }} font-mono">
        </div>

        <div class="md:col-span-2">
            <label class="{{ $labelClass }}">اسم المستلم AI</label>
            <input type="text" name="recipient_name_ai" value="{{ old('recipient_name_ai', $paymentAI?->recipient_name) }}"
                   placeholder="الاسم الكامل للشخص المستلم"
                   class="{{ $inputClass }}">
        </div>

    </div>
</div>

<script>
function toggleAssociations(val) {
    const list = document.getElementById('associations_list');
    if (val === 'yes') {
        list.classList.remove('hidden');
    } else {
        list.classList.add('hidden');
        list.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    }
}

function toggleFormRegionDropdown(e) {
    e.stopPropagation();
    const panel = document.getElementById('form-region-panel');
    const isHidden = panel.classList.contains('hidden');
    panel.classList.toggle('hidden', !isHidden);
    document.getElementById('form-region-chevron').style.transform = isHidden ? 'rotate(180deg)' : '';
    if (isHidden) {
        const s = document.getElementById('form-region-search');
        s.value = '';
        filterFormRegions('');
        setTimeout(() => s.focus(), 50);
    }
}

function selectFormRegion(id, name, sectorId, sectorName) {
    document.getElementById('region_id_select').value = id;
    const lbl = document.getElementById('form-region-label');
    lbl.textContent = name;
    lbl.className = id ? 'truncate text-gray-800' : 'truncate text-gray-400';
    document.getElementById('form-region-panel').classList.add('hidden');
    document.getElementById('form-region-chevron').style.transform = '';

    // Auto-select sector based on region (always update, clearing if no sector)
    selectFormSector(sectorId ? String(sectorId) : '', sectorName || '');
}

function filterFormRegions(q) {
    q = q.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('#form-region-list .form-region-item').forEach((li, i) => {
        if (i === 0) { li.style.display = q ? 'none' : ''; return; }
        const btn = li.querySelector('button');
        const match = !q || (btn.dataset.name || '').includes(q);
        li.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('form-region-no-results').classList.toggle('hidden', visible > 0 || !q);
}

function addFormRegionOption(id, name) {
    const list = document.getElementById('form-region-list');
    const li = document.createElement('li');
    li.className = 'form-region-item';
    li.innerHTML = `<button type="button" onclick="selectFormRegion('${id}', '${name.replace(/'/g,"\\'")}'"
                            data-name="${name.toLowerCase()}"
                            class="w-full text-right px-3 py-2 text-sm text-gray-700 hover:bg-emerald-50 transition-colors">
                        ${name}
                    </button>`;
    list.appendChild(li);
}

document.addEventListener('click', function(e) {
    const dd = document.getElementById('form-region-dropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('form-region-panel')?.classList.add('hidden');
        const ch = document.getElementById('form-region-chevron');
        if (ch) ch.style.transform = '';
    }
});

function toggleAddRegion() {
    const panel = document.getElementById('add-region-panel');
    const input = document.getElementById('new-region-input');
    const err   = document.getElementById('add-region-error');
    panel.classList.toggle('hidden');
    err.classList.add('hidden');
    if (!panel.classList.contains('hidden')) {
        input.value = '';
        input.focus();
    }
}

async function submitNewRegion() {
    const input = document.getElementById('new-region-input');
    const err   = document.getElementById('add-region-error');
    const name  = input.value.trim();
    err.classList.add('hidden');

    if (!name) {
        err.textContent = 'يرجى إدخال اسم المنطقة.';
        err.classList.remove('hidden');
        input.focus();
        return;
    }

    try {
        const res = await fetch('{{ route("regions.quick-store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                             || '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name }),
        });

        const data = await res.json();

        if (!res.ok) {
            err.textContent = data.errors?.name?.[0] ?? data.message ?? 'حدث خطأ.';
            err.classList.remove('hidden');
            return;
        }

        document.getElementById('add-region-panel').classList.add('hidden');

        if (data.pending) {
            err.textContent = data.message;
            err.classList.remove('hidden');
            err.classList.replace('text-red-500', 'text-amber-600');
            return;
        }

        addFormRegionOption(data.id, data.name);
        selectFormRegion(String(data.id), data.name);
    } catch {
        err.textContent = 'تعذّر الاتصال بالخادم.';
        err.classList.remove('hidden');
    }
}

function toggleFormSectorDropdown(e) {
    e.stopPropagation();
    const panel = document.getElementById('form-sector-panel');
    const isHidden = panel.classList.contains('hidden');
    panel.classList.toggle('hidden', !isHidden);
    document.getElementById('form-sector-chevron').style.transform = isHidden ? 'rotate(180deg)' : '';
    if (isHidden) {
        const s = document.getElementById('form-sector-search');
        s.value = '';
        filterFormSectors('');
        setTimeout(() => s.focus(), 50);
    }
}

function selectFormSector(id, name) {
    document.getElementById('sector_id_select').value = id;
    const lbl = document.getElementById('form-sector-label');
    if (lbl) {
        lbl.textContent = name || '— يُحدَّد تلقائياً من المنطقة —';
        lbl.className = id ? 'truncate flex-1 text-gray-700' : 'truncate flex-1 text-gray-400';
    }
    document.getElementById('form-sector-panel')?.classList.add('hidden');
    const ch = document.getElementById('form-sector-chevron');
    if (ch) ch.style.transform = '';
}

function filterFormSectors(q) {
    q = q.toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('#form-sector-list .form-sector-item').forEach((li, i) => {
        if (i === 0) { li.style.display = q ? 'none' : ''; return; }
        const btn = li.querySelector('button');
        const match = !q || btn.dataset.name.includes(q);
        li.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('form-sector-no-results').classList.toggle('hidden', visible > 0 || !q);
}

document.addEventListener('click', function(e) {
    const dd = document.getElementById('form-sector-dropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('form-sector-panel')?.classList.add('hidden');
        const ch = document.getElementById('form-sector-chevron');
        if (ch) ch.style.transform = '';
    }
});

function addFormSectorOption(id, name) {
    const list = document.getElementById('form-sector-list');
    const li = document.createElement('li');
    li.className = 'form-sector-item';
    li.innerHTML = `<button type="button" onclick="selectFormSector('${id}', '${name.replace(/'/g,"\\'")}'"
                            data-name="${name.toLowerCase()}"
                            class="w-full text-right px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition-colors">
                        ${name}
                    </button>`;
    list.appendChild(li);
}

function toggleAddSector() {
    const panel = document.getElementById('add-sector-panel');
    const input = document.getElementById('new-sector-input');
    const err   = document.getElementById('add-sector-error');
    panel.classList.toggle('hidden');
    err.classList.add('hidden');
    if (!panel.classList.contains('hidden')) {
        input.value = '';
        input.focus();
    }
}

async function submitNewSector() {
    const input = document.getElementById('new-sector-input');
    const err   = document.getElementById('add-sector-error');
    const name  = input.value.trim();
    err.classList.add('hidden');

    if (!name) {
        err.textContent = 'يرجى إدخال اسم القطاع.';
        err.classList.remove('hidden');
        input.focus();
        return;
    }

    try {
        const res = await fetch('{{ route("sectors.quick-store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                             || '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name }),
        });

        const data = await res.json();

        if (!res.ok) {
            err.textContent = data.errors?.name?.[0] ?? data.message ?? 'حدث خطأ.';
            err.classList.remove('hidden');
            return;
        }

        document.getElementById('add-sector-panel').classList.add('hidden');

        if (data.pending) {
            err.textContent = data.message;
            err.classList.remove('hidden');
            err.classList.replace('text-red-500', 'text-amber-600');
            return;
        }

        addFormSectorOption(data.id, data.name);
        selectFormSector(String(data.id), data.name);
    } catch {
        err.textContent = 'تعذّر الاتصال بالخادم.';
        err.classList.remove('hidden');
    }
}

function calcTotal() {
    const fields = ['work_score', 'housing_score', 'dependents_score', 'dependent_status_score', 'illness_score', 'special_cases_score'];
    let total = 0;
    fields.forEach(f => {
        const el = document.querySelector('[name="' + f + '"]');
        if (el) total += Math.max(0, parseInt(el.value) || 0);
    });
    const additionEl = document.querySelector('[name="score_addition"]');
    const addition = additionEl ? Math.max(0, parseInt(additionEl.value) || 0) : 0;
    const deductionEl = document.querySelector('[name="score_deduction"]');
    const deduction = deductionEl ? Math.max(0, parseInt(deductionEl.value) || 0) : 0;
    total = Math.max(0, total + addition - deduction);
    const display = document.getElementById('total_score_display');
    if (display) display.textContent = total;
    const bar = document.getElementById('total_score_bar');
    if (bar) bar.style.width = Math.min(100, (total / 43) * 100) + '%';
    const amountDisplay = document.getElementById('amount_display');
    if (amountDisplay) amountDisplay.textContent = (total * 500).toLocaleString('ar') + ' ل.س';
    const scoreEl = document.getElementById('field_score');
    if (scoreEl) scoreEl.value = total;
    const amountEl = document.getElementById('field_estimated_amount');
    if (amountEl) amountEl.value = total * 500;
}
calcTotal();
</script>
