@extends('layouts.app')

@section('title', 'تعديلات مبالغ الجولات الميدانية — مسالك النور')
@section('max-width', 'max-w-7xl')

@section('breadcrumb')
    <span class="text-gray-700">تعديلات مبالغ الجولات الميدانية</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-slate-700 via-slate-600 to-slate-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">تعديلات مبالغ الجولات الميدانية</h1>
            <p class="text-slate-300 text-sm mt-0.5">جميع الجولات التي تحمل مبلغاً مُعدَّلاً (زيادة أو نقصان)</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <div class="bg-white/10 border border-white/20 rounded-xl px-4 py-2.5 text-center min-w-[120px]">
                <p class="text-white font-black text-xl leading-none">{{ number_format($totalMembers) }}</p>
                <p class="text-slate-300 text-xs mt-0.5">عدد الأعضاء</p>
            </div>
            <div class="bg-emerald-500/20 border border-emerald-400/30 rounded-xl px-4 py-2.5 text-center min-w-[140px]">
                <p class="text-emerald-300 font-black text-xl leading-none">+{{ number_format($positiveTotal, 0) }}</p>
                <p class="text-emerald-300/70 text-xs mt-0.5">مجموع الزيادات (ل.س) — {{ number_format($positiveCount) }} جولة</p>
            </div>
            <div class="bg-red-500/20 border border-red-400/30 rounded-xl px-4 py-2.5 text-center min-w-[140px]">
                <p class="text-red-300 font-black text-xl leading-none">{{ number_format($negativeTotal, 0) }}</p>
                <p class="text-red-300/70 text-xs mt-0.5">مجموع النقصانات (ل.س) — {{ number_format($negativeCount) }} جولة</p>
            </div>
        </div>
    </div>
</div>

{{-- Type tabs --}}
<div class="flex gap-2 mb-5 flex-wrap">
    @foreach([
        ['all',      'الكل',     'bg-slate-600 text-white',   'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50'],
        ['positive', 'زيادة ↑',  'bg-emerald-600 text-white', 'bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50'],
        ['negative', 'نقصان ↓',  'bg-red-600 text-white',     'bg-white border border-red-200 text-red-600 hover:bg-red-50'],
    ] as [$val, $lbl, $activeClass, $inactiveClass])
    <a href="{{ route('field-visits.with-amounts', array_merge(request()->except('type','page'), ['type' => $val])) }}"
       class="px-5 py-2 rounded-xl text-sm font-bold transition-colors {{ $typeFilter === $val ? $activeClass : $inactiveClass }}">
        {{ $lbl }}
        @if($val === 'all')
            <span class="mr-1 text-xs opacity-70">({{ number_format($totalCount) }})</span>
        @elseif($val === 'positive')
            <span class="mr-1 text-xs opacity-70">({{ number_format($positiveCount) }})</span>
        @else
            <span class="mr-1 text-xs opacity-70">({{ number_format($negativeCount) }})</span>
        @endif
    </a>
    @endforeach
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-5">
    <form method="GET" action="{{ route('field-visits.with-amounts') }}">
        <input type="hidden" name="type" value="{{ $typeFilter }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">بحث (اسم / رقم اضبارة / هوية)</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="ابحث..."
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">الزائر</label>
                <div class="relative" id="visitor-dropdown">
                    <button type="button" id="visitor-btn"
                            onclick="toggleVisitorDropdown(event)"
                            class="w-full flex items-center justify-between gap-2 border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 hover:border-slate-300 focus:outline-none transition-colors text-right">
                        <span id="visitor-display" class="{{ !empty($visitorFilter) ? 'text-gray-800 font-medium' : 'text-gray-400' }} truncate">
                            @if(!empty($visitorFilter))
                                {{ count($visitorFilter) === 1 ? $visitorFilter[0] : count($visitorFilter) . ' زوار مختارون' }}
                            @else
                                اختر زائراً...
                            @endif
                        </span>
                        <span class="flex items-center gap-1 shrink-0">
                            @if(!empty($visitorFilter))
                                <span id="visitor-clear-x" onclick="clearVisitors(event)"
                                      class="text-gray-400 hover:text-red-500 transition-colors p-0.5 rounded cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </span>
                            @endif
                            <svg class="w-4 h-4 text-gray-400" id="visitor-chevron" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>
                    <div id="visitor-panel"
                         class="hidden absolute z-40 top-full mt-1 w-full min-w-[240px] bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">
                        <div class="p-2 border-b border-gray-100">
                            <input type="text" id="visitor-search" placeholder="ابحث في الزوار..."
                                   class="w-full px-3 py-1.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-slate-300 focus:outline-none"
                                   oninput="filterVisitorItems(this.value)" autocomplete="off">
                        </div>
                        <ul class="max-h-60 overflow-y-auto py-1" id="visitor-options">
                            @forelse($visitorList as $v)
                            <li class="visitor-item">
                                <label class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-slate-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="visitor[]" value="{{ $v }}"
                                           {{ in_array($v, $visitorFilter) ? 'checked' : '' }}
                                           class="visitor-cb rounded border-gray-300 text-slate-600 focus:ring-slate-400"
                                           onchange="updateVisitorDisplay()">
                                    <span class="text-sm text-gray-700 truncate visitor-label">{{ $v }}</span>
                                </label>
                            </li>
                            @empty
                            <li class="px-4 py-3 text-xs text-gray-400 text-center">لا يوجد زوار مسجلون</li>
                            @endforelse
                        </ul>
                        <div id="visitor-no-results" class="hidden px-4 py-3 text-xs text-gray-400 text-center">لا توجد نتائج</div>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">سبب المبلغ</label>
                <input type="text" name="reason" value="{{ $reasonFilter }}" placeholder="السبب..."
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50"
                       list="reason-list">
                <datalist id="reason-list">
                    @foreach($reasonList as $r)<option value="{{ $r }}">@endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">الترتيب</label>
                <select name="sort" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50">
                    <option value="amount_desc" {{ $sortBy === 'amount_desc' ? 'selected' : '' }}>المبلغ (الأعلى أولاً)</option>
                    <option value="amount_asc"  {{ $sortBy === 'amount_asc'  ? 'selected' : '' }}>المبلغ (الأقل أولاً)</option>
                    <option value="date_desc"   {{ $sortBy === 'date_desc'   ? 'selected' : '' }}>تاريخ الجولة (الأحدث)</option>
                    <option value="date_asc"    {{ $sortBy === 'date_asc'    ? 'selected' : '' }}>تاريخ الجولة (الأقدم)</option>
                    <option value="name"          {{ $sortBy === 'name'          ? 'selected' : '' }}>الاسم</option>
                    <option value="dossier"       {{ $sortBy === 'dossier'       ? 'selected' : '' }}>رقم الاضبارة</option>
                    <option value="created_desc"  {{ $sortBy === 'created_desc'  ? 'selected' : '' }}>تاريخ الإضافة (الأحدث أولاً)</option>
                    <option value="created_asc"   {{ $sortBy === 'created_asc'   ? 'selected' : '' }}>تاريخ الإضافة (الأقدم أولاً)</option>
                </select>
            </div>
        </div>

        {{-- Created by filter --}}
        @if($createdByList->isNotEmpty())
        <div class="mb-3">
            <label class="block text-xs font-bold text-gray-500 mb-2">الحساب الذي أضاف الجولة</label>
            <div class="flex flex-wrap gap-2">
                @foreach($createdByList as $user)
                <label class="inline-flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-xl border text-sm font-medium transition-colors
                              {{ in_array($user->id, $createdByFilter) ? 'bg-slate-700 border-slate-700 text-white' : 'bg-gray-50 border-gray-200 text-gray-600 hover:border-slate-400 hover:text-slate-700' }}">
                    <input type="checkbox" name="created_by[]" value="{{ $user->id }}"
                           {{ in_array($user->id, $createdByFilter) ? 'checked' : '' }}
                           class="hidden">
                    {{ $user->name }}
                </label>
                @endforeach
            </div>
        </div>
        @endif
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">تاريخ الجولة من</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">تاريخ الجولة إلى</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">المبلغ من (ل.س)</label>
                <input type="number" name="amount_from" value="{{ $amountFrom }}" placeholder="0"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">المبلغ إلى (ل.س)</label>
                <input type="number" name="amount_to" value="{{ $amountTo }}" placeholder="∞"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none bg-gray-50">
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="flex items-center gap-1.5 bg-slate-700 hover:bg-slate-800 text-white text-sm font-bold px-5 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                بحث
            </button>
            @if($search || $reasonFilter || !empty($visitorFilter) || !empty($createdByFilter) || $dateFrom || $dateTo || $amountFrom || $amountTo)
            <a href="{{ route('field-visits.with-amounts', $typeFilter !== 'all' ? ['type' => $typeFilter] : []) }}"
               class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold px-4 py-2 rounded-xl transition-colors">
                مسح الفلاتر
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Table --}}
@if($visits->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-gray-400 text-sm font-medium">لا توجد جولات مطابقة</p>
    </div>
@else
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
        <p class="text-sm font-bold text-gray-600">
            {{ number_format($visits->total()) }} جولة
            @if($typeFilter === 'positive')
                <span class="mr-2 text-xs text-emerald-600 font-semibold">↑ زيادات فقط</span>
            @elseif($typeFilter === 'negative')
                <span class="mr-2 text-xs text-red-600 font-semibold">↓ نقصانات فقط</span>
            @endif
        </p>
        <p class="text-xs text-gray-400">صفحة {{ $visits->currentPage() }} من {{ $visits->lastPage() }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/30">
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">العضو</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">رقم الاضبارة</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">تاريخ الجولة</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">المبلغ</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">سبب المبلغ</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">الزائر</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">الحالة</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3 whitespace-nowrap">أُضيف بواسطة</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($visits as $visit)
                @php $isPositive = $visit->estimated_amount > 0; @endphp
                <tr class="hover:bg-gray-50/60 transition-colors {{ $isPositive ? '' : 'bg-red-50/20' }}">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800 whitespace-nowrap">{{ $visit->member?->full_name ?? '—' }}</p>
                        @if($visit->member?->verificationStatus)
                            <span class="text-xs px-1.5 py-0.5 rounded-md font-medium"
                                  style="background: {{ $visit->member->verificationStatus->color }}22; color: {{ $visit->member->verificationStatus->color }};">
                                {{ $visit->member->verificationStatus->name }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs whitespace-nowrap">
                        {{ $visit->member?->dossier_number ?: '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                        {{ $visit->visit_date ? $visit->visit_date->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($isPositive)
                            <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 font-black text-sm px-2.5 py-1 rounded-lg whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                +{{ number_format($visit->estimated_amount, 0) }} ل.س
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 font-black text-sm px-2.5 py-1 rounded-lg whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                </svg>
                                {{ number_format($visit->estimated_amount, 0) }} ل.س
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs max-w-[180px]">
                        {{ $visit->amount_reason ?: '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                        {{ $visit->visitor ?: '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($visit->status)
                            <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-lg font-medium whitespace-nowrap">
                                {{ $visit->status->name }}
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                        {{ $visit->createdBy?->name ?: '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($visit->member_id)
                        <a href="{{ route('members.show', $visit->member_id) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 hover:text-slate-700 bg-gray-100 hover:bg-slate-100 border border-gray-200 px-2.5 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            عرض
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($visits->hasPages())
    <div class="border-t border-gray-100 px-5 py-3 bg-gray-50/30">
        {{ $visits->links() }}
    </div>
    @endif
</div>
@endif

@push('scripts')
<script>
// Checkbox pill toggle
document.querySelectorAll('label:has(input[type="checkbox"].hidden)').forEach(label => {
    label.addEventListener('click', () => {
        const cb = label.querySelector('input[type="checkbox"]');
        cb.checked = !cb.checked;
        label.classList.toggle('bg-slate-700', cb.checked);
        label.classList.toggle('border-slate-700', cb.checked);
        label.classList.toggle('text-white', cb.checked);
        label.classList.toggle('bg-gray-50', !cb.checked);
        label.classList.toggle('border-gray-200', !cb.checked);
        label.classList.toggle('text-gray-600', !cb.checked);
    });
});

// Visitor multi-select dropdown
function toggleVisitorDropdown(e) {
    e.stopPropagation();
    const panel = document.getElementById('visitor-panel');
    const isHidden = panel.classList.contains('hidden');
    panel.classList.toggle('hidden', !isHidden);
    document.getElementById('visitor-chevron').style.transform = isHidden ? 'rotate(180deg)' : '';
    if (isHidden) {
        const s = document.getElementById('visitor-search');
        s.value = '';
        filterVisitorItems('');
        setTimeout(() => s.focus(), 50);
    }
}

function updateVisitorDisplay() {
    const checked = Array.from(document.querySelectorAll('.visitor-cb:checked'));
    const disp = document.getElementById('visitor-display');
    if (checked.length === 0) {
        disp.textContent = 'اختر زائراً...';
        disp.className = 'text-gray-400 truncate';
    } else if (checked.length === 1) {
        disp.textContent = checked[0].value;
        disp.className = 'text-gray-800 font-medium truncate';
    } else {
        disp.textContent = checked.length + ' زوار مختارون';
        disp.className = 'text-gray-800 font-medium truncate';
    }
}

function clearVisitors(e) {
    e.stopPropagation();
    document.querySelectorAll('.visitor-cb').forEach(cb => cb.checked = false);
    updateVisitorDisplay();
}

function filterVisitorItems(q) {
    q = (q || '').toLowerCase().trim();
    let visible = 0;
    document.querySelectorAll('.visitor-item').forEach(li => {
        const lbl = li.querySelector('.visitor-label');
        const match = !q || (lbl?.textContent || '').toLowerCase().includes(q);
        li.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('visitor-no-results').classList.toggle('hidden', visible > 0);
}

document.addEventListener('click', function(e) {
    const dd = document.getElementById('visitor-dropdown');
    if (dd && !dd.contains(e.target)) {
        document.getElementById('visitor-panel').classList.add('hidden');
        document.getElementById('visitor-chevron').style.transform = '';
    }
});
</script>
@endpush

@endsection
