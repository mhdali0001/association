<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Title --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">العنوان <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $expense->title ?? '') }}"
               class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('title') border-red-400 @enderror">
        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Amount --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (ل.س) <span class="text-red-500">*</span></label>
        <input type="number" name="amount" value="{{ old('amount', $expense->amount ?? '') }}" step="0.01" min="0"
               class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('amount') border-red-400 @enderror">
        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Date --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ <span class="text-red-500">*</span></label>
        <input type="date" name="date" value="{{ old('date', isset($expense) ? $expense->date->format('Y-m-d') : date('Y-m-d')) }}"
               class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('date') border-red-400 @enderror">
        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Category --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">الفئة</label>
        <select name="category"
                class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <option value="">— اختر فئة —</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ old('category', $expense->category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Recipient --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">الجهة المستفيدة</label>
        <input type="text" name="recipient" value="{{ old('recipient', $expense->recipient ?? '') }}"
               class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('recipient') border-red-400 @enderror">
        @error('recipient') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
        <textarea name="description" rows="3"
                  class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 @error('description') border-red-400 @enderror">{{ old('description', $expense->description ?? '') }}</textarea>
        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

</div>
