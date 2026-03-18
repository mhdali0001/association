@php
    $isEdit  = isset($member);
    $scores  = $isEdit ? $member->scores  : null;
    $payment = $isEdit ? $member->paymentInfo : null;
    $v = fn($field, $default = '') => old($field, $isEdit ? ($member->$field ?? $default) : $default);
@endphp

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- القسم الأول: البيانات الشخصية                         --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-10">
    <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">البيانات الشخصية</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

        {{-- رقم الإضبارة --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الإضبارة</label>
            <input type="text" name="dossier_number" value="{{ $v('dossier_number') }}"
                   placeholder="مثال: 2026/145"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('dossier_number') border-red-400 @enderror">
            @error('dossier_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- الاسم الكامل --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الاسم الكامل <span class="text-red-600">*</span></label>
            <input type="text" name="full_name" value="{{ $v('full_name') }}" required
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('full_name') border-red-400 @enderror">
            @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- العمر --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">العمر</label>
            <input type="number" name="age" value="{{ $v('age') }}" min="0" max="120"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        {{-- اسم الأم --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">اسم الأم</label>
            <input type="text" name="mother_name" value="{{ $v('mother_name') }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        {{-- رقم الهوية --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهوية <span class="text-red-600">*</span></label>
            <input type="text" name="national_id" value="{{ $v('national_id') }}" required
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('national_id') border-red-400 @enderror">
            @error('national_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- رقم الهاتف --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
            <input type="tel" name="phone" value="{{ $v('phone') }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        {{-- الحالة الاجتماعية --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الحالة الاجتماعية</label>
            <select name="marital_status"
                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">—</option>
                @foreach($maritalStatuses as $ms)
                    <option value="{{ $ms->name }}" {{ $v('marital_status') == $ms->name ? 'selected' : '' }}>
                        {{ $ms->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- الجنس --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الجنس</label>
            <select name="gender" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">—</option>
                <option value="ذكر"  {{ $v('gender') === 'ذكر'  ? 'selected' : '' }}>ذكر</option>
                <option value="أنثى" {{ $v('gender') === 'أنثى' ? 'selected' : '' }}>أنثى</option>
            </select>
        </div>

        {{-- المندوب --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">المندوب</label>
            <select name="representative_id" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">— غير محدد —</option>
                @foreach($representatives as $rep)
                    <option value="{{ $rep->id }}"
                        {{ old('representative_id', $isEdit ? $member->representative_id : auth()->id()) == $rep->id ? 'selected' : '' }}>
                        {{ $rep->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- مندوب خارجي --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">مندوب خارجي</label>
            <input type="text" name="delegate" value="{{ $v('delegate') }}"
                   placeholder="اسم المندوب الخارجي"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- القسم الثاني: العنوان والوضع المعيشي                  --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-10">
    <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">العنوان والوضع المعيشي</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">العنوان الحالي</label>
            <textarea name="current_address" rows="2"
                      class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $v('current_address') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الوظيفة / العمل</label>
            <input type="text" name="job" value="{{ $v('job') }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">وضع السكن</label>
            <select name="housing_status" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">—</option>
                <option value="ملك"     {{ $v('housing_status') === 'ملك'     ? 'selected' : '' }}>ملك</option>
                <option value="استضافة" {{ $v('housing_status') === 'استضافة' ? 'selected' : '' }}>استضافة</option>
                <option value="إيجار"   {{ $v('housing_status') === 'إيجار'   ? 'selected' : '' }}>إيجار</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">عدد المعالين</label>
            <input type="number" name="dependents_count" value="{{ $v('dependents_count', 0) }}" min="0"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- القسم الثالث: الحالة الصحية                           --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-10">
    <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">الحالة الصحية والطبية</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">نوع المرض / الحالة</label>
            <input type="text" name="disease_type" value="{{ $v('disease_type') }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <div class="lg:col-span-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">تفاصيل المرض / الإصابة</label>
            <textarea name="illness_details" rows="3"
                      class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $v('illness_details') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="special_cases" id="special_cases" value="1"
                   {{ old('special_cases', $isEdit ? $member->special_cases : false) ? 'checked' : '' }}
                   class="h-5 w-5 rounded text-emerald-600 border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500">
            <label for="special_cases" class="text-sm font-medium text-gray-700">حالات خاصة</label>
        </div>

        <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">وصف الحالات الخاصة</label>
            <textarea name="special_cases_description" rows="2"
                      class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $v('special_cases_description') }}</textarea>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- القسم الرابع: بيانات إضافية                           --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-10">
    <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">بيانات إضافية وتقييم</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الشبكة</label>
            <select name="network" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">—</option>
                <option value="MTN"      {{ $v('network') === 'MTN'      ? 'selected' : '' }}>MTN</option>
                <option value="SYRIATEL" {{ $v('network') === 'SYRIATEL' ? 'selected' : '' }}>Syriatel</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">حالة المشغل</label>
            <input type="text" name="provider_status" value="{{ $v('provider_status') }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>

        <div class="lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                حالة التحقق <span class="text-red-600">*</span>
            </label>
            <select name="verification_status_id" required
                    class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('verification_status_id') border-red-400 @enderror">
                <option value="">— اختر حالة التحقق —</option>
                @foreach($verificationStatuses as $vs)
                    <option value="{{ $vs->id }}"
                            style="color: {{ $vs->color }}"
                            {{ old('verification_status_id', $isEdit ? $member->verification_status_id : '') == $vs->id ? 'selected' : '' }}>
                        {{ $vs->name }}
                    </option>
                @endforeach
            </select>
            @error('verification_status_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- الانتساب لجمعية أخرى --}}
        <div class="lg:col-span-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">الانتساب لجمعية أخرى</label>
            @php
                $hasAssoc      = $isEdit ? $member->other_association : false;
                $hasAssocOld   = old('has_other_association', $hasAssoc ? 'yes' : 'no');
                $checkedAssocs = old('association_ids', $isEdit ? $member->associations->pluck('id')->toArray() : []);
            @endphp
            <div class="flex gap-4 mb-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="has_other_association" value="yes" id="assoc_yes"
                           {{ $hasAssocOld === 'yes' ? 'checked' : '' }}
                           onchange="toggleAssociations(this.value)"
                           class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                    <span class="text-sm font-medium text-gray-700">نعم</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="has_other_association" value="no" id="assoc_no"
                           {{ $hasAssocOld !== 'yes' ? 'checked' : '' }}
                           onchange="toggleAssociations(this.value)"
                           class="h-4 w-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                    <span class="text-sm font-medium text-gray-700">لا</span>
                </label>
            </div>
            <div id="associations_list" class="{{ $hasAssocOld === 'yes' ? '' : 'hidden' }} flex flex-wrap gap-3">
                @foreach($associations as $assoc)
                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 hover:bg-emerald-50 hover:border-emerald-300 transition-colors">
                        <input type="checkbox" name="association_ids[]" value="{{ $assoc->id }}"
                               {{ in_array($assoc->id, (array)$checkedAssocs) ? 'checked' : '' }}
                               class="h-4 w-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        <span class="text-sm text-gray-700">{{ $assoc->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-4 pt-1">
            <label class="flex items-center gap-2 cursor-pointer w-fit">
                <input type="checkbox" name="sham_cash_account" id="sham_cash_account" value="1"
                       {{ old('sham_cash_account', $isEdit ? $member->sham_cash_account : false) ? 'checked' : '' }}
                       class="h-5 w-5 rounded text-emerald-600 border-2 border-gray-300 focus:ring-2 focus:ring-emerald-500">
                <span class="text-sm font-medium text-gray-700">حساب شام كاش</span>
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                الدرجة / التقييم
                <span class="text-xs text-emerald-500">(تُحسب تلقائياً)</span>
            </label>
            <input type="number" id="field_score" name="score" readonly
                   value="{{ old('score', $isEdit ? $member->score : 0) }}"
                   class="w-full border-2 border-emerald-200 bg-emerald-50 text-emerald-700 font-bold rounded-lg px-4 py-2.5 cursor-not-allowed">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                المبلغ المقدر
                <span class="text-xs text-emerald-500">(= النقاط × 500)</span>
            </label>
            <input type="number" id="field_estimated_amount" name="estimated_amount" readonly
                   value="{{ old('estimated_amount', $isEdit ? $member->estimated_amount : 0) }}"
                   class="w-full border-2 border-emerald-200 bg-emerald-50 text-emerald-700 font-bold rounded-lg px-4 py-2.5 cursor-not-allowed">
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- القسم الخامس: نقاط التقييم                            --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-10">
    <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">نقاط التقييم (حالة المعيل)</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                نقاط العمل <span class="text-xs text-gray-400">(أقصى 2)</span>
            </label>
            <input type="number" name="work_score" min="0" max="2" oninput="calcTotal()"
                   value="{{ old('work_score', $scores ? $scores->work_score : 0) }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                نقاط السكن <span class="text-xs text-gray-400">(أقصى 2)</span>
            </label>
            <input type="number" name="housing_score" min="0" max="2" oninput="calcTotal()"
                   value="{{ old('housing_score', $scores ? $scores->housing_score : 0) }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                نقاط عدد الأفراد <span class="text-xs text-gray-400">(أقصى 20)</span>
            </label>
            <input type="number" name="dependents_score" min="0" max="20" oninput="calcTotal()"
                   value="{{ old('dependents_score', $scores ? $scores->dependents_score : 0) }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                نقاط حالة المعيل <span class="text-xs text-gray-400">(أقصى 2)</span>
            </label>
            <input type="number" name="dependent_status_score" min="0" max="2" oninput="calcTotal()"
                   value="{{ old('dependent_status_score', $scores ? $scores->dependent_status_score : 0) }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                نقاط المرض <span class="text-xs text-gray-400">(أقصى 5)</span>
            </label>
            <input type="number" name="illness_score" min="0" max="5" oninput="calcTotal()"
                   value="{{ old('illness_score', $scores ? $scores->illness_score : 0) }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                نقاط الحالات الخاصة <span class="text-xs text-gray-400">(أقصى 10)</span>
            </label>
            <input type="number" name="special_cases_score" min="0" max="10" oninput="calcTotal()"
                   value="{{ old('special_cases_score', $scores ? $scores->special_cases_score : 0) }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">مجموع النقاط</label>
            <div id="total_score_display"
                 class="w-full border-2 border-emerald-300 rounded-lg px-4 py-2.5 bg-emerald-50 text-emerald-700 font-bold text-xl text-center">
                {{ $scores ? $scores->total_score : 0 }}
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════ --}}
{{-- القسم السادس: معلومات الدفع                           --}}
{{-- ═══════════════════════════════════════════════════════ --}}
<div class="mb-2">
    <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-6">معلومات الدفع والحسابات</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الآيبان (IBAN)</label>
            <input type="text" name="iban" value="{{ old('iban', $payment?->iban) }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">الباركود</label>
            <input type="text" name="barcode" value="{{ old('barcode', $payment?->barcode) }}"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">صورة الآيبان</label>
            <input type="file" name="iban_image" accept="image/*"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
            @if($isEdit && $payment?->iban_image)
                <p class="text-xs text-gray-500 mt-1">الملف الحالي: {{ basename($payment->iban_image) }}</p>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">صورة الباركود</label>
            <input type="file" name="barcode_image" accept="image/*"
                   class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500">
            @if($isEdit && $payment?->barcode_image)
                <p class="text-xs text-gray-500 mt-1">الملف الحالي: {{ basename($payment->barcode_image) }}</p>
            @endif
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
    const display = document.getElementById('total_score_display');
    if (display) display.textContent = total;
    const scoreEl = document.getElementById('field_score');
    if (scoreEl) scoreEl.value = total;
    const amountEl = document.getElementById('field_estimated_amount');
    if (amountEl) amountEl.value = total * 500;
}
calcTotal();
</script>
