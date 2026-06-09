@extends('layouts.app')

@section('title', 'التراجع الجماعي — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">التراجع الجماعي</span>
@endsection

@section('content')

<div class="relative bg-gradient-to-l from-rose-600 via-rose-500 to-pink-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-8 left-16 w-48 h-48 bg-white rounded-full"></div>
        <div class="absolute top-4 right-12 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white">التراجع عن العمليات الجماعية</h1>
            <p class="text-rose-200 text-sm mt-0.5">
                إجمالي الجلسات:
                <span class="font-black text-white">{{ number_format($sessions->total()) }}</span>
            </p>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-semibold flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-5 py-3.5 rounded-2xl text-sm font-semibold flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100 bg-gradient-to-l from-gray-50 to-white">
        <div class="w-7 h-7 rounded-lg bg-rose-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-gray-700">جلسات العمليات الجماعية</span>
    </div>

    @if($sessions->isEmpty())
    <div class="text-center py-20">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
            </svg>
        </div>
        <p class="text-gray-400 text-sm font-medium">لا توجد عمليات جماعية مسجّلة بعد</p>
        <p class="text-gray-300 text-xs mt-1">ستظهر هنا العمليات القابلة للتراجع عند تنفيذها</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/70 border-b border-gray-100">
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">#</th>
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">العملية</th>
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">المنفّذ</th>
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">الأعضاء المتأثرون</th>
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">التاريخ</th>
                    <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">الحالة</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($sessions as $session)
                <tr class="hover:bg-gray-50/50 transition-colors {{ $session->isReverted() ? 'opacity-60' : '' }}" id="session-row-{{ $session->id }}">
                    <td class="px-5 py-4 text-xs text-gray-400 font-mono">{{ $session->id }}</td>
                    <td class="px-5 py-4 max-w-sm">
                        <p class="text-sm font-semibold text-gray-800 leading-snug">{{ $session->description }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $session->operation }}</p>
                    </td>
                    <td class="px-5 py-4">
                        @if($session->user)
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center shrink-0">
                                <span class="text-white font-black text-xs">{{ mb_substr($session->user->name, 0, 1) }}</span>
                            </div>
                            <span class="text-xs font-semibold text-gray-700">{{ $session->user->name }}</span>
                        </div>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1 text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 px-2.5 py-1 rounded-full">
                            {{ number_format($session->affected_count) }} عضو
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-xs font-semibold text-gray-700">{{ $session->created_at->format('Y/m/d') }}</p>
                        <p class="text-xs text-gray-400">{{ $session->created_at->format('H:i') }}</p>
                    </td>
                    <td class="px-5 py-4">
                        @if($session->isReverted())
                        <div>
                            <span class="inline-flex items-center gap-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                تم التراجع
                            </span>
                            @if($session->revertedByUser)
                            <p class="text-xs text-gray-400 mt-1">{{ $session->revertedByUser->name }}</p>
                            <p class="text-xs text-gray-300">{{ $session->reverted_at->format('Y/m/d H:i') }}</p>
                            @endif
                        </div>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                            نشطة
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if(!$session->isReverted())
                        <button type="button"
                                onclick="revertSession({{ $session->id }}, '{{ route('bulk-revert.revert', $session->id) }}', this)"
                                class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl border border-rose-200 text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            تراجع عن العملية
                        </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($sessions->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $sessions->links() }}
    </div>
    @endif
    @endif
</div>

@push('scripts')
<script>
function revertSession(sessionId, url, btn) {
    if (!confirm('هل أنت متأكد من التراجع عن هذه العملية الجماعية؟\nسيتم استعادة بيانات جميع الأعضاء المتأثرين إلى حالتهم السابقة.')) return;

    const spinner = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>';

    btn.disabled = true;
    btn.innerHTML = spinner + ' جارٍ التراجع...';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json().catch(() => ({ success: r.ok })))
    .then(data => {
        if (data.success !== false) {
            const row = document.getElementById('session-row-' + sessionId);
            row.style.opacity = '0.5';
            btn.outerHTML = '<span class="text-xs text-emerald-600 font-bold flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>تم التراجع (' + (data.count || '') + ' عضو)</span>';

            // Update status badge
            const statusCell = row.querySelector('td:nth-child(6)');
            if (statusCell) {
                statusCell.innerHTML = '<span class="inline-flex items-center gap-1 text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full"><svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>تم التراجع</span>';
            }
        } else {
            alert(data.message || 'حدث خطأ أثناء التراجع.');
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg> تراجع عن العملية';
        }
    })
    .catch(() => {
        alert('حدث خطأ في الاتصال.');
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg> تراجع عن العملية';
    });
}
</script>
@endpush

@endsection
