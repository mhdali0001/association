<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Member --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">العضو المستفيد <span class="text-red-500">*</span></label>
        <select name="member_id"
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('member_id') border-red-400 @enderror">
            <option value="">— اختر العضو —</option>
            @foreach($members as $member)
                <option value="{{ $member->id }}"
                    {{ old('member_id', $donation->member_id ?? '') == $member->id ? 'selected' : '' }}>
                    {{ $member->full_name }}
                </option>
            @endforeach
        </select>
        @error('member_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Amount --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ل.س) <span class="text-red-500">*</span></label>
        <input type="number" name="amount" value="{{ old('amount', $donation->amount ?? '') }}" step="0.01" min="0"
               class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('amount') border-red-400 @enderror">
        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Month --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">شهر التبرع <span class="text-red-500">*</span></label>
        <input type="month" name="donation_month"
               value="{{ old('donation_month', isset($donation) ? $donation->donation_month->format('Y-m') : now()->format('Y-m')) }}"
               class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('donation_month') border-red-400 @enderror">
        @error('donation_month') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Type --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">طريقة الصرف <span class="text-red-500">*</span></label>
        <select name="type" id="type-select"
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="manual"    {{ old('type', $donation->type ?? 'manual') === 'manual'    ? 'selected' : '' }}>يدوي</option>
            <option value="sham_cash" {{ old('type', $donation->type ?? '')       === 'sham_cash' ? 'selected' : '' }}>شام كاش</option>
        </select>
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">الحالة <span class="text-red-500">*</span></label>
        <select name="status"
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="paid"      {{ old('status', $donation->status ?? 'paid') === 'paid'      ? 'selected' : '' }}>✅ مدفوع</option>
            <option value="pending"   {{ old('status', $donation->status ?? '')     === 'pending'   ? 'selected' : '' }}>⏳ معلّق</option>
            <option value="cancelled" {{ old('status', $donation->status ?? '')     === 'cancelled' ? 'selected' : '' }}>❌ ملغي</option>
        </select>
    </div>

    {{-- Reference Number (Sham Cash) --}}
    <div id="ref-field" class="{{ old('type', $donation->type ?? 'manual') === 'sham_cash' ? '' : 'hidden' }} md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">رقم العملية (شام كاش)</label>
        <input type="text" name="reference_number"
               value="{{ old('reference_number', $donation->reference_number ?? '') }}"
               placeholder="مثال: SC-20260315-001"
               class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('reference_number') border-red-400 @enderror">
        @error('reference_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Notes --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
        <textarea name="notes" rows="2"
                  class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('notes', $donation->notes ?? '') }}</textarea>
    </div>

</div>

<script>
    document.getElementById('type-select').addEventListener('change', function () {
        document.getElementById('ref-field').classList.toggle('hidden', this.value !== 'sham_cash');
    });
</script>
