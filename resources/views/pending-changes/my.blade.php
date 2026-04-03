@extends('layouts.app')

@section('title', 'طلباتي — مسالك النور')
@section('max-width', 'max-w-5xl')

@section('breadcrumb')
    <span class="text-gray-700">طلباتي</span>
@endsection

@section('content')

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-blue-600 via-blue-500 to-indigo-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-40 h-40 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-56 h-56 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white mb-1">طلباتي</h1>
            <p class="text-blue-100 text-sm">سجل طلبات التعديل التي قدّمتها</p>
        </div>
        <div class="flex items-center gap-3">
            @if($pendingCount)
                <div class="bg-white/20 backdrop-blur rounded-2xl px-4 py-2.5 text-center border border-white/30">
                    <p class="text-white/70 text-xs mb-0.5">معلّقة</p>
                    <p class="text-white font-black text-xl">{{ $pendingCount }}</p>
                </div>
            @endif
            @if($rejectedCount)
                <div class="bg-red-500/30 backdrop-blur rounded-2xl px-4 py-2.5 text-center border border-red-300/40">
                    <p class="text-red-100 text-xs mb-0.5">مرفوضة</p>
                    <p class="text-white font-black text-xl">{{ $rejectedCount }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Status tabs --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex border-b border-gray-100">
        @foreach(['' => 'الكل', 'pending' => 'معلّقة', 'approved' => 'موافق عليها', 'rejected' => 'مرفوضة'] as $s => $label)
            <a href="{{ route('pending-changes.my', $s ? ['status' => $s] : []) }}"
               @class([
                   'flex-1 text-center py-3.5 text-sm font-semibold transition-colors border-b-2',
                   'border-blue-500 text-blue-600 bg-blue-50/50'   => $status === $s,
                   'border-transparent text-gray-400 hover:text-gray-600' => $status !== $s,
               ])>
                {{ $label }}
                @if($s === 'rejected' && $rejectedCount)
                    <span class="mr-1 bg-red-500 text-white text-xs font-black rounded-full px-1.5 py-0.5">{{ $rejectedCount }}</span>
                @endif
                @if($s === 'pending' && $pendingCount)
                    <span class="mr-1 bg-amber-500 text-white text-xs font-black rounded-full px-1.5 py-0.5">{{ $pendingCount }}</span>
                @endif
            </a>
        @endforeach
    </div>

    @if($changes->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-gray-400 font-semibold mb-1">لا توجد طلبات</p>
            <p class="text-sm text-gray-300">لم تقدّم أي طلبات تعديل بعد</p>
        </div>
    @else
        <div class="divide-y divide-gray-50">
            @foreach($changes as $change)
                @php
                    $actionColors = ['create' => 'emerald', 'update' => 'blue', 'delete' => 'red', 'bulk_amount' => 'violet'];
                    $ac = $actionColors[$change->action] ?? 'gray';
                @endphp
                <div @class([
                    'px-6 py-4 transition-colors',
                    'hover:bg-gray-50' => $change->isPending() || $change->isApproved(),
                    'bg-red-50/40 hover:bg-red-50' => $change->isRejected(),
                ])>
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">

                            {{-- Action badge --}}
                            <span class="mt-0.5 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-{{ $ac }}-50 text-{{ $ac }}-700 border border-{{ $ac }}-100 shrink-0">
                                {{ $change->actionLabel() }} {{ $change->modelLabel() }}
                            </span>

                            <div>
                                <p class="font-bold text-gray-800 text-sm">
                                    {{ $change->payload['full_name'] ?? $change->payload['member_name'] ?? $change->original['full_name'] ?? $change->original['member_name'] ?? '—' }}
                                    @if($change->model_id)
                                        <span class="text-xs text-gray-400 font-normal mr-1">#{{ $change->model_id }}</span>
                                    @endif
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $change->created_at->format('d/m/Y H:i') }}
                                    — {{ $change->created_at->diffForHumans() }}
                                </p>

                                {{-- Rejection reason --}}
                                @if($change->isRejected() && $change->reviewer_notes)
                                    <div class="mt-2 flex items-start gap-1.5 bg-red-100 text-red-700 text-xs rounded-lg px-3 py-2">
                                        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                        </svg>
                                        <span><span class="font-semibold">سبب الرفض:</span> {{ $change->reviewer_notes }}</span>
                                    </div>
                                @elseif($change->isRejected())
                                    <div class="mt-2 flex items-center gap-1.5 bg-red-100 text-red-600 text-xs rounded-lg px-3 py-2">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        تم رفض هذا الطلب بدون ذكر سبب
                                    </div>
                                @endif

                                {{-- Approval info --}}
                                @if($change->isApproved())
                                    <div class="mt-2 flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs rounded-lg px-3 py-2">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        تمت الموافقة وتطبيق التعديل
                                        @if($change->reviewer)
                                            بواسطة <span class="font-semibold mr-1">{{ $change->reviewer->name }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Status badge --}}
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
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600 border border-red-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    مرفوض
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($changes->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $changes->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
