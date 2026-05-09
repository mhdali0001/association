@extends('layouts.app')

@section('title', $sector->name . ' — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('sectors.index') }}" class="hover:text-indigo-700 transition-colors">القطاعات</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">{{ $sector->name }}</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-indigo-600 via-violet-500 to-purple-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 border-2 border-white/40 flex items-center justify-center shadow-lg shrink-0">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-black text-white">{{ $sector->name }}</h1>
                <p class="text-indigo-100 text-sm mt-0.5">{{ $members->total() }} مستفيد في هذا القطاع</p>
            </div>
        </div>
        <a href="{{ route('sectors.index') }}"
           class="text-sm text-white/80 hover:text-white bg-white/10 hover:bg-white/20 border border-white/30 px-4 py-2 rounded-xl transition-colors">
            رجوع
        </a>
    </div>
</div>

{{-- Bulk reassign form --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5 overflow-hidden">
    <div class="flex items-center gap-2 bg-indigo-50/50 border-b border-indigo-100 px-5 py-3">
        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        <span class="text-sm font-bold text-indigo-700">تعديل جماعي للمستفيدين</span>
    </div>
    <div class="p-4">
        <form id="bulk-form" method="POST" action="{{ route('members.bulk-update') }}" onsubmit="return submitBulk()">
            @csrf @method('PATCH')
            <input type="hidden" name="apply_fields[]" value="sector_id">
            <div id="ids-container"></div>

            <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">نقل المحددين إلى قطاع</label>
                    <select name="fields[sector_id]"
                            class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                        <option value="">— بدون قطاع (إزالة) —</option>
                        @foreach($allSectors as $s)
                            <option value="{{ $s->id }}" {{ $s->id === $sector->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="selectAll()" id="select-all-btn"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 border border-indigo-200 hover:bg-indigo-50 px-3 py-2.5 rounded-xl transition-colors">
                        تحديد الكل
                    </button>
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors">
                        تطبيق على المحددين
                    </button>
                </div>
            </div>
            <p id="bulk-hint" class="mt-2 text-xs text-gray-400">حدد مستفيدين من القائمة أدناه ثم اختر القطاع المراد النقل إليه.</p>
        </form>
    </div>
</div>

{{-- Members table --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between gap-2 px-5 py-3.5 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">المستفيدون ({{ $members->total() }})</span>
        </div>
        <span id="selected-count" class="text-xs text-indigo-600 font-semibold hidden">0 محدد</span>
    </div>

    @if($members->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <p class="text-gray-400 font-semibold">لا يوجد مستفيدون في هذا القطاع</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox" id="check-all" onchange="toggleAll(this)"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400 cursor-pointer">
                        </th>
                        <th class="font-semibold text-gray-500 px-4 py-3">رقم الاضبارة</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">الاسم الكامل</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">المنطقة</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">حالة التحقق</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">الحالة النهائية</th>
                        <th class="font-semibold text-gray-500 px-4 py-3">المبلغ المقدر</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $member)
                    <tr class="hover:bg-indigo-50/30 transition-colors member-row" data-id="{{ $member->id }}">
                        <td class="px-4 py-3.5">
                            <input type="checkbox" value="{{ $member->id }}" class="member-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-400 cursor-pointer" onchange="updateCount()">
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 font-mono text-xs">{{ $member->dossier_number ?? '—' }}</td>
                        <td class="px-4 py-3.5 font-semibold text-gray-800">{{ $member->full_name }}</td>
                        <td class="px-4 py-3.5 text-gray-600">{{ $member->region?->name ?? '—' }}</td>
                        <td class="px-4 py-3.5">
                            @if($member->verificationStatus)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full border"
                                      style="color:{{ $member->verificationStatus->color }};border-color:{{ $member->verificationStatus->color }}40;background:{{ $member->verificationStatus->color }}15">
                                    {{ $member->verificationStatus->name }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($member->finalStatus)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full border"
                                      style="color:{{ $member->finalStatus->color }};border-color:{{ $member->finalStatus->color }}40;background:{{ $member->finalStatus->color }}15">
                                    {{ $member->finalStatus->name }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-gray-700">
                            {{ $member->estimated_amount ? number_format($member->estimated_amount, 0) . ' ل.س' : '—' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <a href="{{ route('members.show', $member) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg px-2.5 py-1 transition-colors">
                                عرض
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $members->links() }}
            </div>
        @endif
    @endif
</div>

<script>
function getChecked() {
    return [...document.querySelectorAll('.member-check:checked')].map(c => c.value);
}

function updateCount() {
    const ids = getChecked();
    const el = document.getElementById('selected-count');
    el.textContent = ids.length + ' محدد';
    el.classList.toggle('hidden', ids.length === 0);
    document.getElementById('check-all').indeterminate =
        ids.length > 0 && ids.length < document.querySelectorAll('.member-check').length;
    document.getElementById('check-all').checked =
        ids.length === document.querySelectorAll('.member-check').length;
}

function toggleAll(master) {
    document.querySelectorAll('.member-check').forEach(c => c.checked = master.checked);
    updateCount();
}

function selectAll() {
    document.querySelectorAll('.member-check').forEach(c => c.checked = true);
    document.getElementById('check-all').checked = true;
    updateCount();
}

function submitBulk() {
    const ids = getChecked();
    if (ids.length === 0) {
        alert('يرجى تحديد مستفيد واحد على الأقل.');
        return false;
    }
    const container = document.getElementById('ids-container');
    container.innerHTML = '';
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'ids[]';
        inp.value = id;
        container.appendChild(inp);
    });
    return confirm('تطبيق التعديل على ' + ids.length + ' مستفيد؟');
}
</script>

@endsection
