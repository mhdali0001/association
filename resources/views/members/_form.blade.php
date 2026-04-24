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
            <select name="region_id" class="{{ $selectClass }}">
                <option value="">— غير محدد —</option>
                @foreach($regionsList ?? [] as $region)
                    <option value="{{ $region->id }}"
                        {{ (int)old('region_id', $member->region_id ?? '') === $region->id ? 'selected' : '' }}>
                        {{ $region->name }}
                    </option>
                @endforeach
            </select>
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
            @php $shamVal = old('sham_cash_account', $isEdit ? ($member->sham_cash_account ?? '') : ''); @endphp
            <label class="{{ $labelClass }}">شام كاش</label>
            <div class="flex items-center gap-3 flex-wrap mt-1">
                @foreach(['' => 'لا', 'done' => 'نعم (تم)', 'manual' => 'يدوي'] as $optVal => $optLabel)
                <label class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-xl border text-sm font-medium transition-colors
                    {{ $shamVal === $optVal ? 'bg-emerald-50 border-emerald-400 text-emerald-700' : 'bg-white border-gray-200 text-gray-600 hover:border-emerald-300' }}">
                    <input type="radio" name="sham_cash_account" value="{{ $optVal }}"
                           {{ $shamVal === $optVal ? 'checked' : '' }}
                           class="text-emerald-600 focus:ring-emerald-500">
                    {{ $optLabel }}
                </label>
                @endforeach
            </div>
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
            <label class="{{ $labelClass }}">
                المبلغ النهائي
                <span class="text-purple-400 normal-case font-normal">
                    = مقدر
                    @if(($visitAmount ?? 0) > 0)
                        + {{ number_format($visitAmount, 0) }} ل.س (زيارة)
                    @endif
                    <span class="text-gray-400 text-xs">(تلقائي)</span>
                </span>
            </label>
            <input type="number" step="0.01" id="field_final_amount" name="final_amount" readonly
                   value="{{ old('final_amount', $isEdit ? ($member->final_amount ?? 0) : 0) }}"
                   class="w-full border border-purple-200 bg-gradient-to-l from-purple-50 to-fuchsia-50 text-purple-700 font-black rounded-xl px-4 py-2.5 cursor-not-allowed text-center">
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
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                {{ $sf['label'] }}
                <span class="{{ $colorLabel[$c] }} normal-case font-medium">(أقصى {{ $sf['max'] }})</span>
            </label>
            <input type="number" name="{{ $sf['name'] }}" min="0" max="{{ $sf['max'] }}" oninput="calcTotal()"
                   value="{{ $val }}"
                   class="w-full border {{ $colorBorder[$c] }} {{ $colorBg[$c] }} {{ $colorText[$c] }} font-bold rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-{{ $c }}-400 transition text-center">
        </div>
        @endforeach

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

        <div class="md:col-span-2">
            <label class="{{ $labelClass }}">اسم المستلم</label>
            <input type="text" name="recipient_name" value="{{ old('recipient_name', $payment?->recipient_name) }}"
                   placeholder="الاسم الكامل للشخص المستلم"
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

function calcTotal() {
    const fields = ['work_score', 'housing_score', 'dependents_score', 'dependent_status_score', 'illness_score', 'special_cases_score'];
    let total = 0;
    fields.forEach(f => {
        const el = document.querySelector('[name="' + f + '"]');
        if (el) total += Math.max(0, parseInt(el.value) || 0);
    });
    const deductionEl = document.querySelector('[name="score_deduction"]');
    const deduction = deductionEl ? Math.max(0, parseInt(deductionEl.value) || 0) : 0;
    total = Math.max(0, total - deduction);
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
    const visitAmt = {{ (int)($visitAmount ?? 0) }};
    const finalEl = document.getElementById('field_final_amount');
    if (finalEl) finalEl.value = total * 500 + visitAmt;
}
calcTotal();
</script>
