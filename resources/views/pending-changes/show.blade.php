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
    } elseif ($modelType === 'field_visit') {
        $labels      = \App\Models\PendingChange::fieldVisitFieldLabels();
        $topFields   = array_keys($labels);
        $scoreFields = [];
        $paymentFields = [];
    } else {
        $labels = \App\Models\PendingChange::memberFieldLabels();
        $topFields = [
            'full_name','age','gender','mother_name','national_id','verification_status_id',
            'dossier_number','current_address','marital_status','disease_type','phone','phone2',
            'network','provider_status','job','housing_status','dependents_count',
            'illness_details','special_cases','special_cases_description','sham_cash_account',
            'other_association','representative_id','delegate','association_id','score','estimated_amount',
        ];
        $scoreFields   = ['work_score','housing_score','dependents_score','dependent_status_score','illness_score','special_cases_score'];
        $paymentFields = ['iban','barcode'];
    }

    // Resolve member link
    $memberId = match($modelType) {
        'field_visit', 'donation', 'member_image' => $payload['member_id'] ?? ($original['member_id'] ?? null),
        default => $pendingChange->model_id,
    };
    $memberLink = ($memberId && in_array($modelType, ['member','field_visit','donation','member_image']))
        ? route('members.show', $memberId)
        : null;

    // Maps for ID → name resolution
    $fvsMap       = \App\Models\FieldVisitStatus::pluck('name', 'id')->toArray();
    $vsMap        = \App\Models\VerificationStatus::pluck('name', 'id')->toArray();
    $houseTypeMap = \App\Models\HouseType::pluck('name', 'id')->toArray();
    $houseCondMap = \App\Models\HouseCondition::pluck('name', 'id')->toArray();

    // Helper: resolve any known ID field to its display name
    $resolveId = function($field, $value) use ($fvsMap, $vsMap, $houseTypeMap, $houseCondMap) {
        if ($field === 'field_visit_status_id')  return $fvsMap[$value]       ?? $value;
        if ($field === 'verification_status_id') return $vsMap[$value]        ?? $value;
        if ($field === 'house_type_id')          return $houseTypeMap[$value] ?? $value;
        if ($field === 'house_condition_id')     return $houseCondMap[$value] ?? $value;
        return $value;
    };
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
        <div class="flex items-center gap-2 flex-wrap justify-end">
            @if($memberLink)
                <a href="{{ $memberLink }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-bold bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    ملف العضو
                </a>
            @endif
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
                    @php $dispOrig = $resolveId($field, $original[$field]); @endphp
                    <div class="bg-white rounded-xl px-3 py-2.5 border border-red-100">
                        <p class="text-xs text-gray-400 mb-0.5">{{ $labels[$field] ?? $field }}</p>
                        <p class="text-sm font-semibold text-gray-800">
                            @if(is_bool($original[$field]))
                                {{ $original[$field] ? 'نعم' : 'لا' }}
                            @else
                                {{ $dispOrig ?: '—' }}
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
                    @php $dispVal = $resolveId($field, $payload[$field]); @endphp
                    <div class="bg-emerald-50/50 rounded-xl px-3 py-2.5 border border-emerald-100">
                        <p class="text-xs text-gray-400 mb-0.5">{{ $labels[$field] ?? $field }}</p>
                        <p class="text-sm font-semibold text-gray-800">
                            @if(is_bool($payload[$field]))
                                {{ $payload[$field] ? 'نعم' : 'لا' }}
                            @else
                                {{ $dispVal ?: '—' }}
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
                            $displayOld = $resolveId($field, $oldVal);
                            $displayNew = $resolveId($field, $newVal);
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
                                    @else {{ $displayOld ?: '—' }} @endif
                                </td>
                                <td class="px-5 py-2.5 {{ $changed ? 'text-emerald-700 font-semibold' : 'text-gray-600' }} text-sm">
                                    @if(is_bool($newVal)) {{ $newVal ? 'نعم' : 'لا' }}
                                    @else {{ $displayNew ?: '—' }} @endif
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
        <div class="flex flex-col sm:flex-row gap-3 mb-4">

            {{-- Approve as-is --}}
            <form action="{{ route('pending-changes.approve', $pendingChange) }}" method="POST"
                  onsubmit="return confirm('الموافقة وتطبيق التعديل كما هو؟')"
                  class="flex-1">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-3 rounded-xl transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    موافقة وتطبيق
                </button>
            </form>

            {{-- Approve with edit toggle --}}
            @if($action !== 'delete' && !in_array($action, ['bulk_amount','bulk_delete','bulk_update']))
            <button type="button" onclick="toggleEditPanel()"
                    class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-3 rounded-xl transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل والموافقة
            </button>
            @endif

            {{-- Reject --}}
            <form action="{{ route('pending-changes.reject', $pendingChange) }}" method="POST" class="flex-1">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="reviewer_notes" placeholder="سبب الرفض..."
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

        {{-- Edit & Approve panel --}}
        @if($action !== 'delete' && !in_array($action, ['bulk_amount','bulk_delete','bulk_update']))
        <div id="edit-panel" class="hidden border-t border-blue-100 pt-5 mt-2">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-blue-800">تعديل البيانات قبل الموافقة</h3>
                <span class="text-xs text-blue-400">— اترك الحقل فارغاً للإبقاء على القيمة الأصلية</span>
            </div>

            <form action="{{ route('pending-changes.approve-edit', $pendingChange) }}" method="POST">
                @csrf

                @php
                    $editPayload = $pendingChange->payload ?? [];
                @endphp

                {{-- Field visit fields --}}
                @if($modelType === 'field_visit')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">حالة الجولة</label>
                            <select name="payload[field_visit_status_id]"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                                <option value="">— اختر الحالة —</option>
                                @foreach(\App\Models\FieldVisitStatus::orderBy('name')->get() as $fvs)
                                    <option value="{{ $fvs->id }}" {{ ($editPayload['field_visit_status_id'] ?? '') == $fvs->id ? 'selected' : '' }}>
                                        {{ $fvs->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">نوع البيت</label>
                            <select name="payload[house_type_id]"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                                <option value="">— اختر النوع —</option>
                                @foreach(\App\Models\HouseType::active()->orderBy('name')->get() as $ht)
                                    <option value="{{ $ht->id }}" {{ ($editPayload['house_type_id'] ?? '') == $ht->id ? 'selected' : '' }}>
                                        {{ $ht->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">حالة البيت</label>
                            <select name="payload[house_condition_id]"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                                <option value="">— اختر الحالة —</option>
                                @foreach(\App\Models\HouseCondition::active()->orderBy('name')->get() as $hc)
                                    <option value="{{ $hc->id }}" {{ ($editPayload['house_condition_id'] ?? '') == $hc->id ? 'selected' : '' }}>
                                        {{ $hc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">تاريخ الزيارة</label>
                            <input type="date" name="payload[visit_date]" value="{{ $editPayload['visit_date'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">اسم الزائر</label>
                            <input type="text" name="payload[visitor]" value="{{ $editPayload['visitor'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">المبلغ المقدر (ل.س)</label>
                            <input type="number" name="payload[estimated_amount]" value="{{ $editPayload['estimated_amount'] ?? '' }}" min="0" step="0.01"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">سبب المبلغ</label>
                            <input type="text" name="payload[amount_reason]" value="{{ $editPayload['amount_reason'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">ملاحظات</label>
                            <textarea name="payload[notes]" rows="2"
                                      class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50 resize-none">{{ $editPayload['notes'] ?? '' }}</textarea>
                        </div>
                    </div>

                {{-- Donation fields --}}
                @elseif($modelType === 'donation')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">المبلغ (ل.س)</label>
                            <input type="number" name="payload[amount]" value="{{ $editPayload['amount'] ?? '' }}" min="0"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">شهر التبرع</label>
                            <input type="month" name="payload[donation_month]" value="{{ $editPayload['donation_month'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">ملاحظات</label>
                            <textarea name="payload[notes]" rows="2"
                                      class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50 resize-none">{{ $editPayload['notes'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">رقم المرجع</label>
                            <input type="text" name="payload[reference_number]" value="{{ $editPayload['reference_number'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                    </div>

                {{-- Marital status / Association / Verification status --}}
                @elseif(in_array($modelType, ['marital_status','association','verification_status']))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">الاسم</label>
                            <input type="text" name="payload[name]" value="{{ $editPayload['name'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        @if($modelType === 'verification_status')
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">اللون</label>
                            <input type="color" name="payload[color]" value="{{ $editPayload['color'] ?? '#6366f1' }}"
                                   class="h-11 w-24 border border-gray-200 rounded-xl cursor-pointer p-1">
                        </div>
                        @endif
                    </div>

                {{-- Member fields (key fields only) --}}
                @elseif($modelType === 'member')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">الاسم الكامل</label>
                            <input type="text" name="payload[full_name]" value="{{ $editPayload['full_name'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">رقم الهوية</label>
                            <input type="text" name="payload[national_id]" value="{{ $editPayload['national_id'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">الهاتف</label>
                            <input type="text" name="payload[phone]" value="{{ $editPayload['phone'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">العنوان الحالي</label>
                            <input type="text" name="payload[current_address]" value="{{ $editPayload['current_address'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">نوع المرض</label>
                            <input type="text" name="payload[disease_type]" value="{{ $editPayload['disease_type'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">وصف الحالة الخاصة</label>
                            <input type="text" name="payload[special_cases_description]" value="{{ $editPayload['special_cases_description'] ?? '' }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                        </div>
                    </div>
                @endif

                <div class="flex gap-3 items-end">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">ملاحظة المراجع (اختياري)</label>
                        <input type="text" name="reviewer_notes" placeholder="ملاحظة على التعديلات..."
                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-400 bg-gray-50">
                    </div>
                    <button type="submit"
                            onclick="return confirm('تطبيق الطلب مع التعديلات؟')"
                            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition-colors shadow-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        تأكيد الموافقة مع التعديل
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>
@endif

{{-- Revoke action (admin only, approved only) --}}
@if($pendingChange->isApproved())
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6">
        <h2 class="text-sm font-bold text-gray-700 mb-3">إعادة رفض الطلب</h2>
        <p class="text-xs text-gray-400 mb-4">سيُعاد تصنيف الطلب إلى "مرفوض" — لن يتم التراجع عن التغييرات المُطبَّقة تلقائياً.</p>
        <form action="{{ route('pending-changes.revoke', $pendingChange) }}" method="POST"
              onsubmit="return confirm('إعادة رفض هذا الطلب الموافق عليه؟')">
            @csrf
            <div class="flex gap-2">
                <input type="text" name="reviewer_notes" placeholder="سبب إعادة الرفض..."
                       class="flex-1 text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-300 transition">
                <button type="submit"
                        class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-colors shadow-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    إعادة رفض
                </button>
            </div>
        </form>
    </div>
@endif

{{-- Reopen action (admin only, rejected only, and not previously applied) --}}
@if($pendingChange->isRejected())
    <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-6">
        <h2 class="text-sm font-bold text-gray-700 mb-3">إعادة فتح الطلب</h2>
        <p class="text-xs text-gray-400 mb-4">سيُعاد الطلب إلى حالة "بانتظار المراجعة" ويمكن الموافقة عليه أو رفضه مجدداً.</p>
        <form action="{{ route('pending-changes.reopen', $pendingChange) }}" method="POST"
              onsubmit="return confirm('إعادة فتح هذا الطلب المرفوض؟')">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold px-6 py-3 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                إعادة فتح الطلب
            </button>
        </form>
    </div>
@endif

<script>
function toggleEditPanel() {
    const panel = document.getElementById('edit-panel');
    panel.classList.toggle('hidden');
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>

@endsection
