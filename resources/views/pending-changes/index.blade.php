@extends('layouts.app')

@section('title', 'طلبات التعديل — مسالك النور')
@section('max-width', 'max-w-6xl')

@section('breadcrumb')
    <span class="text-gray-700">طلبات التعديل</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-amber-500 via-orange-500 to-amber-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-40 h-40 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-56 h-56 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white mb-1">طلبات التعديل والموافقة</h1>
            <p class="text-amber-100 text-sm">
                الطلبات المعلّقة:
                <span class="font-black text-white text-lg mx-1">{{ $pendingCount }}</span>
                طلب بانتظار المراجعة
            </p>
        </div>
        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </div>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="mb-5 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl px-4 py-3">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- Status tabs --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6">
    <div class="flex border-b border-gray-100">
        @foreach(['pending' => ['label' => 'معلّقة', 'color' => 'amber'], 'approved' => ['label' => 'موافق عليها', 'color' => 'emerald'], 'rejected' => ['label' => 'مرفوضة', 'color' => 'red']] as $s => $info)
            <a href="{{ route('pending-changes.index', ['status' => $s]) }}"
               class="flex-1 text-center py-3.5 text-sm font-semibold transition-colors border-b-2 {{ $status === $s ? 'border-'.$info['color'].'-500 text-'.$info['color'].'-600 bg-'.$info['color'].'-50/50' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                {{ $info['label'] }}
                @if($s === 'pending' && $pendingCount > 0)
                    <span class="mr-1 bg-amber-500 text-white text-xs font-black rounded-full px-1.5 py-0.5">{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    @if($changes->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-gray-400 font-medium">لا توجد طلبات في هذه الفئة</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100">
                        <th class="text-right font-semibold text-gray-500 px-5 py-3.5">#</th>
                        <th class="text-right font-semibold text-gray-500 px-5 py-3.5">نوع الطلب</th>
                        <th class="text-right font-semibold text-gray-500 px-5 py-3.5">العنصر</th>
                        <th class="text-right font-semibold text-gray-500 px-5 py-3.5">مقدّم الطلب</th>
                        <th class="text-right font-semibold text-gray-500 px-5 py-3.5">التاريخ</th>
                        <th class="text-right font-semibold text-gray-500 px-5 py-3.5">الحالة</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($changes as $change)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4 text-gray-400 font-mono text-xs">{{ $change->id }}</td>
                            <td class="px-5 py-4">
                                @php
                                    $actionColors = ['create' => 'emerald', 'update' => 'blue', 'delete' => 'red', 'bulk_amount' => 'violet', 'bulk_delete' => 'rose', 'bulk_update' => 'indigo'];
                                    $ac = $actionColors[$change->action] ?? 'gray';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $ac }}-50 text-{{ $ac }}-700 border border-{{ $ac }}-100">
                                    {{ $change->actionLabel() }} {{ $change->modelLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-gray-800">
                                {{ $change->payload['full_name'] ?? $change->payload['member_name'] ?? $change->getAttribute('original')['full_name'] ?? $change->getAttribute('original')['member_name'] ?? '—' }}
                                @php
                                    $memberId = $change->model_type === 'member'
                                        ? $change->model_id
                                        : ($change->payload['member_id'] ?? $change->getAttribute('original')['member_id'] ?? null);
                                    $dossier = $dossierMap[$memberId]
                                        ?? $visitDossierMap[$change->model_id]
                                        ?? ($change->payload['dossier_number'] ?? $change->getAttribute('original')['dossier_number'] ?? null);
                                @endphp
                                @if($dossier)
                                    <span class="text-xs text-emerald-600 font-bold bg-emerald-50 border border-emerald-100 rounded-full px-2 py-0.5 mr-1">ملف {{ $dossier }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-gray-600">{{ $change->requester?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-gray-500 text-xs">{{ $change->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-4">
                                @if($change->isPending())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                        معلّق
                                    </span>
                                @elseif($change->isApproved())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        موافق عليه
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        مرفوض
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('pending-changes.show', $change) }}"
                                       class="flex items-center gap-1 text-xs font-semibold text-amber-600 hover:text-amber-800 transition-colors">
                                        عرض التفاصيل
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </a>
                                    @if($change->isApproved())
                                        <form action="{{ route('pending-changes.revoke', $change) }}" method="POST"
                                              onsubmit="return confirm('إعادة رفض هذا الطلب؟')">
                                            @csrf
                                            <button type="submit"
                                                    class="flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                إعادة رفض
                                            </button>
                                        </form>
                                    @endif
                                    @if($change->isRejected())
                                        <form action="{{ route('pending-changes.reopen', $change) }}" method="POST"
                                              onsubmit="return confirm('إعادة فتح هذا الطلب؟')">
                                            @csrf
                                            <button type="submit"
                                                    class="flex items-center gap-1 text-xs font-semibold text-amber-500 hover:text-amber-700 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                إعادة فتح
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($changes->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $changes->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
