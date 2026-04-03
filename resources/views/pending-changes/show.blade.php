@extends('layouts.app')

@section('title', 'تفاصيل الطلب — مسالك النور')
@section('max-width', 'max-w-4xl')

@section('breadcrumb')
    <a href="{{ route('pending-changes.index') }}" class="hover:text-amber-700 transition-colors">طلبات التعديل</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">طلب #{{ $pendingChange->id }}</span>
@endsection

@section('content')

@php
    $payload   = $pendingChange->payload  ?? [];
    $original  = $pendingChange->original ?? [];
    $action    = $pendingChange->action;
    $modelType = $pendingChange->model_type;

    $actionColors = ['create' => 'emerald', 'update' => 'blue', 'delete' => 'red'];
    $ac = $actionColors[$action] ?? 'gray';

    // Dynamic field list & labels based on model type
    if ($modelType === 'donation') {
        $labels      = \App\Models\PendingChange::donationFieldLabels();
        $topFields   = array_keys($labels);
        $scoreFields = [];
        $paymentFields = [];
    } elseif ($modelType === 'member_image') {
        $labels      = \App\Models\PendingChange::memberImageFieldLabels();
        $topFields   = array_keys($labels);
        $scoreFields = [];
        $paymentFields = [];
    } elseif ($modelType === 'marital_status') {
        $labels      = \App\Models\PendingChange::maritalStatusFieldLabels();
        $topFields   = array_keys($labels);
        $scoreFields = [];
        $paymentFields = [];
    } elseif ($modelType === 'association') {
        $labels      = \App\Models\PendingChange::associationFieldLabels();
        $topFields   = array_keys($labels);
        $scoreFields = [];
        $paymentFields = [];
    } elseif ($modelType === 'verification_status') {
        $labels      = \App\Models\PendingChange::verificationStatusFieldLabels();
        $topFields   = array_keys($labels);
        $scoreFields = [];
        $paymentFields = [];
    } else {
        $labels = \App\Models\PendingChange::memberFieldLabels();
        $topFields = [
            'full_name','age','gender','mother_name','national_id','verification_status_id',
            'dossier_number','current_address','marital_status','disease_type','phone',
            'network','provider_status','job','housing_status','dependents_count',
            'illness_details','special_cases','special_cases_description','sham_cash_account',
            'other_association','representative_id','delegate','association_id','score','estimated_amount',
        ];
        $scoreFields   = ['work_score','housing_score','dependents_score','dependent_status_score','illness_score','special_cases_score'];
        $paymentFields = ['iban','barcode'];
    }
@endphp

{{-- Header card --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="bg-gradient-to-l from-amber-50 to-orange-50 border-b border-amber-100 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <h1 class="text-base font-bold text-amber-900">
                    طلب {{ $pendingChange->actionLabel() }} {{ $pendingChange->modelLabel() }}
                </h1>
                <p class="text-xs text-amber-500 mt-0.5">
                    مقدّم من: <span class="font-semibold">{{ $pendingChange->requester?->name }}</span>
                    — {{ $pendingChange->created_at->format('d/m/Y H:i') }}
                    ({{ $pendingChange->created_at->diffForHumans() }})
                </p>
            </div>
        </div>
        @if($pendingChange->isPending())
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold bg-amber-100 text-amber-700 border border-amber-200">
                <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                بانتظار المراجعة
            </span>
        @elseif($pendingChange->isApproved())
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                موافق عليه — {{ $pendingChange->reviewer?->name }}
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-600 border border-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                مرفوض — {{ $pendingChange->reviewer?->name }}
            </span>
        @endif
    </div>

    {{-- Reviewer notes --}}
    @if($pendingChange->reviewer_notes)
        <div class="px-6 py-3 bg-red-50 border-b border-red-100 flex items-start gap-2">
            <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
            <p class="text-sm text-red-700"><span class="font-semibold">ملاحظة المراجع:</span> {{ $pendingChange->reviewer_notes }}</p>
        </div>
    @endif
</div>

{{-- BULK AMOUNT case --}}
@if($action === 'bulk_amount')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="flex items-center gap-2.5 bg-gradient-to-l from-violet-50 to-purple-50 border-b border-violet-100 px-6 py-3.5">
            <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-violet-800">تعديل جماعي للمبلغ المقدر</h2>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-violet-50 rounded-xl px-3 py-2.5 border border-violet-100">
                <p class="text-xs text-gray-400 mb-0.5">العملية</p>
                <p class="text-sm font-bold text-violet-700">
                    @php $opLabels = ['add' => 'إضافة', 'subtract' => 'طرح', 'set' => 'تعيين']; @endphp
                    {{ $opLabels[$payload['operation']] ?? $payload['operation'] }}
                </p>
            </div>
            <div class="bg-violet-50 rounded-xl px-3 py-2.5 border border-violet-100">
                <p class="text-xs text-gray-400 mb-0.5">المبلغ</p>
                <p class="text-sm font-bold text-violet-700">{{ number_format($payload['amount']) }} ل.س</p>
            </div>
            <div class="bg-violet-50 rounded-xl px-3 py-2.5 border border-violet-100">
                <p class="text-xs text-gray-400 mb-0.5">عدد الأعضاء</p>
                <p class="text-sm font-bold text-violet-700">{{ $payload['count'] ?? count($payload['member_ids'] ?? []) }}</p>
            </div>
        </div>
    </div>

{{-- DELETE case --}}
@elseif($action === 'delete')
    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h2 class="text-base font-bold text-red-800">طلب حذف {{ $pendingChange->modelLabel() }}</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($topFields as $field)
                @if(isset($original[$field]))
                    <div class="bg-white rounded-xl px-3 py-2.5 border border-red-100">
                        <p class="text-xs text-gray-400 mb-0.5">{{ $labels[$field] ?? $field }}</p>
                        <p class="text-sm font-semibold text-gray-800">
                            @if(is_bool($original[$field]))
                                {{ $original[$field] ? 'نعم' : 'لا' }}
                            @else
                                {{ $original[$field] ?: '—' }}
                            @endif
                        </p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

{{-- CREATE case --}}
@elseif($action === 'create')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="flex items-center gap-2.5 bg-gradient-to-l from-emerald-50 to-teal-50 border-b border-emerald-100 px-6 py-3.5">
            <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-emerald-800">بيانات {{ $pendingChange->modelLabel() }} الجديد</h2>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($topFields as $field)
                @if(isset($payload[$field]))
                    <div class="bg-emerald-50/50 rounded-xl px-3 py-2.5 border border-emerald-100">
                        <p class="text-xs text-gray-400 mb-0.5">{{ $labels[$field] ?? $field }}</p>
                        <p class="text-sm font-semibold text-gray-800">
                            @if(is_bool($payload[$field]))
                                {{ $payload[$field] ? 'نعم' : 'لا' }}
                            @else
                                {{ $payload[$field] ?: '—' }}
                            @endif
                        </p>
                    </div>
                @endif
            @endforeach
        </div>
        @if($modelType === 'member_image' && !empty($payload['file_path']))
            <div class="border-t border-gray-100 p-5">
                <p class="text-xs font-bold text-gray-500 mb-3">معاينة الملف</p>
                @php $fileUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($payload['file_path']); @endphp
                @if(str_starts_with($payload['mime_type'] ?? '', 'image/'))
                    <img src="{{ $fileUrl }}" alt="{{ $payload['file_name'] }}"
                         class="max-h-64 rounded-xl border border-gray-200 object-contain">
                @else
                    <a href="{{ $fileUrl }}" target="_blank"
                       class="inline-flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        {{ $payload['file_name'] }} — فتح الملف
                    </a>
                @endif
            </div>
        @endif
        @if(!empty($payload['scores']))
            <div class="border-t border-gray-100 p-5">
                <p class="text-xs font-bold text-gray-500 mb-3">النقاط</p>
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                    @foreach($scoreFields as $sf)
                        <div class="bg-violet-50 rounded-xl px-3 py-2.5 text-center border border-violet-100">
                            <p class="text-xs text-gray-400 mb-1">{{ $labels['scores.'.$sf] ?? $sf }}</p>
                            <p class="text-lg font-black text-violet-700">{{ $payload['scores'][$sf] ?? 0 }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

{{-- UPDATE case — diff table --}}
@elseif($action === 'update')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="flex items-center gap-2.5 bg-gradient-to-l from-blue-50 to-indigo-50 border-b border-blue-100 px-6 py-3.5">
            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-blue-800">التغييرات المقترحة</h2>
            <span class="text-xs text-blue-400">— الصفوف المظلّلة تحتوي تغييرات</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-right font-semibold text-gray-500 px-5 py-3">الحقل</th>
                        <th class="text-right font-semibold text-gray-500 px-5 py-3">قبل التعديل</th>
                        <th class="text-right font-semibold text-gray-500 px-5 py-3">بعد التعديل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($topFields as $field)
                        @php
                            $oldVal = $original[$field] ?? null;
                            $newVal = $payload[$field]  ?? null;
                            $changed = $oldVal != $newVal;
                        @endphp
                        @if($changed || isset($original[$field]) || isset($payload[$field]))
                            <tr class="{{ $changed ? 'bg-blue-50/60' : '' }}">
                                <td class="px-5 py-2.5 text-gray-500 text-xs font-medium whitespace-nowrap">
                                    {{ $labels[$field] ?? $field }}
                                    @if($changed)
                                        <span class="mr-1 w-1.5 h-1.5 bg-blue-500 rounded-full inline-block"></span>
                                    @endif
                                </td>
                                <td class="px-5 py-2.5 {{ $changed ? 'text-red-600 line-through' : 'text-gray-500' }} text-sm">
                                    @if(is_bool($oldVal)) {{ $oldVal ? 'نعم' : 'لا' }}
                                    @else {{ $oldVal ?: '—' }} @endif
                                </td>
                                <td class="px-5 py-2.5 {{ $changed ? 'text-emerald-700 font-semibold' : 'text-gray-600' }} text-sm">
                                    @if(is_bool($newVal)) {{ $newVal ? 'نعم' : 'لا' }}
                                    @else {{ $newVal ?: '—' }} @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    {{-- Scores diff --}}
                    @foreach($scoreFields as $sf)
                        @php
                            $oldVal = $original['scores'][$sf] ?? null;
                            $newVal = $payload['scores'][$sf]  ?? null;
                            $changed = $oldVal != $newVal;
                        @endphp
                        @if($changed)
                            <tr class="bg-violet-50/60">
                                <td class="px-5 py-2.5 text-gray-500 text-xs font-medium">
                                    {{ $labels['scores.'.$sf] ?? $sf }}
                                    <span class="mr-1 w-1.5 h-1.5 bg-violet-500 rounded-full inline-block"></span>
                                </td>
                                <td class="px-5 py-2.5 text-red-600 line-through text-sm">{{ $oldVal ?? '—' }}</td>
                                <td class="px-5 py-2.5 text-emerald-700 font-semibold text-sm">{{ $newVal ?? '—' }}</td>
                            </tr>
                        @endif
                    @endforeach

                    {{-- Payment diff --}}
                    @foreach($paymentFields as $pf)
                        @php
                            $oldVal = $original['payment'][$pf] ?? null;
                            $newVal = $payload['payment'][$pf]  ?? null;
                            $changed = $oldVal != $newVal;
                        @endphp
                        @if($changed)
                            <tr class="bg-amber-50/60">
                                <td class="px-5 py-2.5 text-gray-500 text-xs font-medium">
                                    {{ $labels['payment.'.$pf] ?? $pf }}
                                    <span class="mr-1 w-1.5 h-1.5 bg-amber-500 rounded-full inline-block"></span>
                                </td>
                                <td class="px-5 py-2.5 text-red-600 line-through text-sm">{{ $oldVal ?: '—' }}</td>
                                <td class="px-5 py-2.5 text-emerald-700 font-semibold text-sm">{{ $newVal ?: '—' }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Actions (admin only, pending only) --}}
@if($pendingChange->isPending())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-sm font-bold text-gray-700 mb-4">قرار المراجعة</h2>
        <div class="flex flex-col sm:flex-row gap-4">

            {{-- Approve --}}
            <form action="{{ route('pending-changes.approve', $pendingChange) }}" method="POST"
                  onsubmit="return confirm('هل أنت متأكد من الموافقة وتطبيق هذا التعديل؟')"
                  class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-3 rounded-xl transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    موافقة وتطبيق التعديل
                </button>
            </form>

            {{-- Reject --}}
            <form action="{{ route('pending-changes.reject', $pendingChange) }}" method="POST" class="flex-1">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="reviewer_notes" placeholder="سبب الرفض (اختياري)..."
                           class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-3 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                    <button type="submit"
                            class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-bold px-5 py-3 rounded-xl transition-colors shadow-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        رفض
                    </button>
                </div>
            </form>

        </div>
    </div>
@endif

@endsection
