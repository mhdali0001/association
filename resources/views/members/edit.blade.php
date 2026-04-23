@extends('layouts.app')

@section('title', 'تعديل عضو — مسالك النور')
@section('max-width', 'max-w-5xl')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">تعديل: {{ $member->full_name }}</span>
@endsection

@section('content')

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <h1 class="text-xl font-bold text-gray-800 mb-6">تعديل بيانات المستفيد</h1>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('members.update', $member) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('members._form')
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-8 py-2.5 rounded-lg transition-colors">
                    تحديث البيانات
                </button>
                <a href="{{ route('members.index') }}"
                   class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors text-sm">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

{{-- ── الجولات الميدانية ── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-6">
    <div class="flex items-center gap-2.5 bg-gradient-to-l from-indigo-50 to-violet-50 border-b border-indigo-100 px-6 py-3.5">
        <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h2 class="text-sm font-bold text-indigo-700">الجولات الميدانية</h2>
        <span class="text-xs text-indigo-500 bg-indigo-100 rounded-full px-2 py-0.5 font-semibold">{{ $member->fieldVisits->count() }}</span>
    </div>

    <div class="p-5 space-y-4">

        @forelse($member->fieldVisits as $visit)
        <div class="border border-gray-100 rounded-xl p-4 bg-gray-50/50 group relative" id="ev-visit-{{ $visit->id }}">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($visit->status)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full text-white"
                                  style="background: {{ $visit->status->color }}">
                                {{ $visit->status->name }}
                            </span>
                        @endif
                        @if($visit->houseType)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full text-white"
                                  style="background: {{ $visit->houseType->color }}">
                                {{ $visit->houseType->name }}
                            </span>
                        @endif
                        @if($visit->visit_date)
                            <span class="text-xs text-gray-500 font-medium">{{ $visit->visit_date->format('Y/m/d') }}</span>
                        @endif
                        @if($visit->visitor)
                            <span class="text-xs text-gray-500">{{ $visit->visitor }}</span>
                        @endif
                        @if($visit->estimated_amount !== null)
                            <span class="text-sm font-black {{ $visit->estimated_amount < 0 ? 'text-red-600' : 'text-emerald-700' }}">
                                {{ $visit->estimated_amount >= 0 ? '+' : '' }}{{ number_format($visit->estimated_amount) }} ل.س
                            </span>
                        @endif
                        @if($visit->has_video)
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">فيديو</span>
                        @endif
                        @if($visit->has_special_case)
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-orange-50 text-orange-700 border border-orange-200">حالة خاصة</span>
                        @endif
                    </div>
                    @if($visit->notes)
                        <p class="text-xs text-gray-500 bg-white rounded-lg px-3 py-1.5 border border-gray-100">{{ $visit->notes }}</p>
                    @endif
                </div>
                <div class="flex gap-1 shrink-0">
                    <button type="button" onclick="evToggleEdit({{ $visit->id }})"
                            class="p-1.5 rounded-lg text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('field-visits.destroy', [$member, $visit]) }}"
                          onsubmit="return confirm('حذف هذه الجولة؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Edit form --}}
            <div id="ev-edit-{{ $visit->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                <form method="POST" action="{{ route('field-visits.update', [$member, $visit]) }}">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">الحالة</label>
                            <select name="field_visit_status_id" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">— غير محدد —</option>
                                @foreach($fieldVisitStatuses as $s)
                                    <option value="{{ $s->id }}" {{ $visit->field_visit_status_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">نوع البيت</label>
                            <select name="house_type_id" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">— غير محدد —</option>
                                @foreach($houseTypes as $ht)
                                    <option value="{{ $ht->id }}" {{ $visit->house_type_id == $ht->id ? 'selected' : '' }}>{{ $ht->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">تاريخ الزيارة</label>
                            <input type="date" name="visit_date" value="{{ $visit->visit_date?->format('Y-m-d') }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">اسم الزائر</label>
                            <input type="text" name="visitor" value="{{ $visit->visitor }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">المبلغ المقدر (ل.س)</label>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="evAdjust('ev-amt-{{ $visit->id }}', -1000)"
                                        class="shrink-0 w-8 h-9 flex items-center justify-center rounded-xl border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 font-bold text-base transition-colors">−</button>
                                <input type="number" name="estimated_amount" id="ev-amt-{{ $visit->id }}"
                                       value="{{ $visit->estimated_amount }}" step="1000"
                                       class="flex-1 min-w-0 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 font-mono text-center">
                                <button type="button" onclick="evAdjust('ev-amt-{{ $visit->id }}', 1000)"
                                        class="shrink-0 w-8 h-9 flex items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold text-base transition-colors">+</button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">سبب المبلغ</label>
                            <input type="text" name="amount_reason" value="{{ $visit->amount_reason }}"
                                   placeholder="وصف موجز..."
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2"
                                      class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ $visit->notes }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">حالة البيت</label>
                            <select name="house_condition_id"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">— بدون —</option>
                                @foreach($houseConditions as $hc)
                                    <option value="{{ $hc->id }}" {{ $visit->house_condition_id == $hc->id ? 'selected' : '' }}>{{ $hc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer mt-4">
                                <input type="hidden" name="has_video" value="0">
                                <input type="checkbox" name="has_video" value="1" {{ $visit->has_video ? 'checked' : '' }}
                                       class="w-4 h-4 text-rose-600 border-gray-300 rounded focus:ring-rose-400">
                                <span class="text-xs font-bold text-gray-600">يوجد فيديو</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer mt-4">
                                <input type="hidden" name="has_special_case" value="0">
                                <input type="checkbox" name="has_special_case" value="1" {{ $visit->has_special_case ? 'checked' : '' }}
                                       class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-400">
                                <span class="text-xs font-bold text-gray-600">حالة خاصة</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition-colors">حفظ التعديلات</button>
                        <button type="button" onclick="evToggleEdit({{ $visit->id }})"
                                class="text-xs text-gray-500 hover:text-gray-700 px-3 py-2 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-4">لا توجد جولات ميدانية مسجّلة بعد.</p>
        @endforelse

        {{-- Add new visit --}}
        <div class="border-t border-dashed border-gray-200 pt-4">
            <button type="button" onclick="evToggleAdd()" id="ev-add-btn"
                    class="flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                إضافة جولة ميدانية
            </button>
            <div id="ev-add-form" class="hidden mt-4">
                <form method="POST" action="{{ route('field-visits.store', $member) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">الحالة</label>
                            <select name="field_visit_status_id" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">— غير محدد —</option>
                                @foreach($fieldVisitStatuses as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">نوع البيت</label>
                            <select name="house_type_id" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">— غير محدد —</option>
                                @foreach($houseTypes as $ht)
                                    <option value="{{ $ht->id }}">{{ $ht->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">تاريخ الزيارة</label>
                            <input type="date" name="visit_date"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">اسم الزائر</label>
                            <input type="text" name="visitor" placeholder="الاسم الكامل للزائر"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">المبلغ (ل.س)</label>
                            <div class="flex gap-1.5">
                                <label class="flex items-center justify-center gap-1 px-3 py-2 rounded-xl border cursor-pointer text-xs font-bold transition-all has-[:checked]:bg-emerald-500 has-[:checked]:text-white has-[:checked]:border-emerald-500 bg-white text-gray-600 border-gray-200 shrink-0">
                                    <input type="radio" name="amount_operation" value="add" checked class="hidden"> + إضافة
                                </label>
                                <label class="flex items-center justify-center gap-1 px-3 py-2 rounded-xl border cursor-pointer text-xs font-bold transition-all has-[:checked]:bg-red-500 has-[:checked]:text-white has-[:checked]:border-red-500 bg-white text-gray-600 border-gray-200 shrink-0">
                                    <input type="radio" name="amount_operation" value="subtract" class="hidden"> − إنقاص
                                </label>
                                <input type="number" name="estimated_amount" min="0" step="0.01" placeholder="0"
                                       class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 font-mono">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">سبب المبلغ</label>
                            <input type="text" name="amount_reason" placeholder="وصف موجز..."
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2" placeholder="ملاحظات الجولة..."
                                      class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">حالة البيت</label>
                            <select name="house_condition_id"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">— بدون —</option>
                                @foreach($houseConditions as $hc)
                                    <option value="{{ $hc->id }}">{{ $hc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer mt-4">
                                <input type="hidden" name="has_video" value="0">
                                <input type="checkbox" name="has_video" value="1"
                                       class="w-4 h-4 text-rose-600 border-gray-300 rounded focus:ring-rose-400">
                                <span class="text-xs font-bold text-gray-600">يوجد فيديو</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer mt-4">
                                <input type="hidden" name="has_special_case" value="0">
                                <input type="checkbox" name="has_special_case" value="1"
                                       class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-400">
                                <span class="text-xs font-bold text-gray-600">حالة خاصة</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors">إضافة الجولة</button>
                        <button type="button" onclick="evToggleAdd()"
                                class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function evToggleEdit(id) {
    document.getElementById('ev-edit-' + id).classList.toggle('hidden');
}
function evToggleAdd() {
    const form = document.getElementById('ev-add-form');
    const btn  = document.getElementById('ev-add-btn');
    form.classList.toggle('hidden');
    btn.classList.toggle('hidden');
}
function evAdjust(inputId, delta) {
    const input = document.getElementById(inputId);
    input.value = (parseFloat(input.value) || 0) + delta;
}
</script>

@endsection
