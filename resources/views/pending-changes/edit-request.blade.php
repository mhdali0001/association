@extends('layouts.app')

@section('title', 'تعديل طلبي — مسالك النور')
@section('max-width', 'max-w-4xl')

@section('breadcrumb')
    <a href="{{ route('pending-changes.my') }}" class="hover:text-amber-700 transition-colors">طلباتي</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">تعديل طلب #{{ $pendingChange->id }}</span>
@endsection

@section('content')

@php
    $payload   = $pendingChange->payload  ?? [];
    $original  = $pendingChange->original ?? [];
    $action    = $pendingChange->action;
    $modelType = $pendingChange->model_type;

    if ($modelType === 'donation') {
        $labels = \App\Models\PendingChange::donationFieldLabels();
        $topFields = array_keys($labels);
        $scoreFields = []; $paymentFields = [];
    } elseif ($modelType === 'member_image') {
        $labels = \App\Models\PendingChange::memberImageFieldLabels();
        $topFields = array_keys($labels);
        $scoreFields = []; $paymentFields = [];
    } elseif ($modelType === 'field_visit') {
        $labels = \App\Models\PendingChange::fieldVisitFieldLabels();
        $topFields = array_keys($labels);
        $scoreFields = []; $paymentFields = [];
    } else {
        $labels = \App\Models\PendingChange::memberFieldLabels();
        $topFields = [
            'full_name','age','gender','mother_name','national_id','verification_status_id',
            'final_status_id','dossier_number','current_address','region_id','sector_id',
            'marital_status','disease_type','phone','phone2','network','provider_status','job',
            'housing_status_id','dependents_count','payments_count','notes',
            'illness_details','special_cases','special_cases_description','sham_cash_account',
            'other_association','representative_id','delegate','second_person',
            'data_entry_name','association_id','score','estimated_amount',
        ];
        $scoreFields = ['work_score','housing_score','dependents_score','dependent_status_score','illness_score','special_cases_score'];
        $paymentFields = ['iban','barcode','data_entry_name'];
    }

    $memberId = match($modelType) {
        'field_visit','donation','member_image' => $payload['member_id'] ?? ($original['member_id'] ?? null),
        default => $pendingChange->model_id,
    };
    $memberLink = ($memberId && in_array($modelType, ['member','field_visit','donation','member_image']))
        ? route('members.show', $memberId) : null;

    $fvsMap         = \App\Models\FieldVisitStatus::pluck('name','id')->toArray();
    $vsMap          = \App\Models\VerificationStatus::pluck('name','id')->toArray();
    $houseTypeMap   = \App\Models\HouseType::pluck('name','id')->toArray();
    $houseCondMap   = \App\Models\HouseCondition::pluck('name','id')->toArray();
    $regionMap      = \App\Models\Region::pluck('name','id')->toArray();
    $sectorMap      = \App\Models\Sector::pluck('name','id')->toArray();
    $finalStatusMap = \App\Models\FinalStatus::pluck('name','id')->toArray();
    $housingStatMap = \App\Models\HousingStatus::pluck('name','id')->toArray();

    $resolveId = function($field, $value) use ($fvsMap,$vsMap,$houseTypeMap,$houseCondMap,$regionMap,$sectorMap,$finalStatusMap,$housingStatMap) {
        if ($field === 'field_visit_status_id')  return $fvsMap[$value]         ?? $value;
        if ($field === 'verification_status_id') return $vsMap[$value]          ?? $value;
        if ($field === 'house_type_id')          return $houseTypeMap[$value]   ?? $value;
        if ($field === 'house_condition_id')     return $houseCondMap[$value]   ?? $value;
        if ($field === 'region_id')              return $regionMap[$value]      ?? $value;
        if ($field === 'sector_id')              return $sectorMap[$value]      ?? $value;
        if ($field === 'final_status_id')        return $finalStatusMap[$value] ?? $value;
        if ($field === 'housing_status_id')      return $housingStatMap[$value] ?? $value;
        return $value;
    };

    $inp = 'w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 bg-white';
@endphp

{{-- ══════════════════════════════════════════════════════
     HEADER
     ══════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
    <div class="bg-gradient-to-l from-amber-50 to-orange-50 border-b border-amber-100 px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-base font-bold text-amber-900">
                    تعديل طلب {{ $pendingChange->actionLabel() }} {{ $pendingChange->modelLabel() }}
                    <span class="text-amber-400 font-normal text-sm mr-1">#{{ $pendingChange->id }}</span>
                </h1>
                <p class="text-xs text-amber-500 mt-0.5">
                    أُرسل في {{ $pendingChange->created_at->format('d/m/Y H:i') }}
                    — {{ $pendingChange->created_at->diffForHumans() }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($memberLink)
                <a href="{{ $memberLink }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    ملف العضو
                </a>
            @endif
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                بانتظار المراجعة
            </span>
        </div>
    </div>
    @if(!empty($payload['_requester_notes']))
        <div class="px-6 py-3 bg-amber-50 border-b border-amber-100 flex items-start gap-2">
            <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
            <p class="text-sm text-amber-800"><span class="font-semibold">ملاحظتك للمسؤول:</span> {{ $payload['_requester_notes'] }}</p>
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════
     DIFF — ما الذي سيتغير
     ══════════════════════════════════════════════════════ --}}
@if($action === 'update')
@php
    $skipTopFields   = ['score','estimated_amount'];
    $changedFields   = []; $unchangedFields = [];
    foreach ($topFields as $field) {
        if (in_array($field, $skipTopFields)) continue;
        $o = $original[$field] ?? null; $n = $payload[$field] ?? null;
        if ($o != $n || ($o === null) !== ($n === null)) { $changedFields[] = $field; }
        elseif (isset($original[$field]) || isset($payload[$field])) { $unchangedFields[] = $field; }
    }
    $changedScores = []; $unchangedScores = [];
    $hasScores = !empty($original['scores']) || !empty($payload['scores']);
    foreach ($scoreFields as $sf) {
        $o = $original['scores'][$sf] ?? null; $n = $payload['scores'][$sf] ?? null;
        if ($o != $n || ($o === null) !== ($n === null)) { $changedScores[] = $sf; }
        elseif ($o !== null || $n !== null) { $unchangedScores[] = $sf; }
    }
    $changedPayments = []; $unchangedPayments = [];
    $hasPayment = !empty($original['payment']) || !empty($payload['payment']);
    foreach ($paymentFields as $pf) {
        $o = $original['payment'][$pf] ?? null; $n = $payload['payment'][$pf] ?? null;
        if ($o != $n || ($o === null) !== ($n === null)) { $changedPayments[] = $pf; }
        elseif ($o !== null || $n !== null) { $unchangedPayments[] = $pf; }
    }
    $totalChanged   = count($changedFields) + count($changedScores) + count($changedPayments);
    $totalUnchanged = count($unchangedFields) + count($unchangedScores) + count($unchangedPayments);
@endphp

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
    <div class="flex items-center justify-between bg-gradient-to-l from-blue-50 to-indigo-50 border-b border-blue-100 px-6 py-3.5">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-blue-800">ما الذي سيتغير</h2>
        </div>
        <div class="flex items-center gap-2">
            @if($totalChanged > 0)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-600 text-white">
                    {{ $totalChanged }} {{ $totalChanged === 1 ? 'حقل يتغير' : 'حقول تتغير' }}
                </span>
            @endif
            @if($totalUnchanged > 0)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                    {{ $totalUnchanged }} بدون تغيير
                </span>
            @endif
        </div>
    </div>

    @if($totalChanged === 0)
        <div class="p-8 text-center text-sm text-gray-400">لا توجد تغييرات مسجّلة في هذا الطلب</div>
    @else

    @if(!empty($changedFields))
    <div class="p-5 space-y-2.5">
        @foreach($changedFields as $field)
            @php
                $oldVal = $original[$field] ?? null; $newVal = $payload[$field] ?? null;
                $dispOld = $resolveId($field, $oldVal); $dispNew = $resolveId($field, $newVal);
            @endphp
            <div class="flex items-start gap-3 bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <span class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-blue-700 mb-1.5">{{ $labels[$field] ?? $field }}</p>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-block px-2.5 py-1 rounded-lg bg-red-100 text-red-700 text-sm line-through break-all">
                            @if(is_bool($oldVal)){{ $oldVal?'نعم':'لا' }}@elseif($dispOld!==null&&$dispOld!==''){{ $dispOld }}@else—@endif
                        </span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                        <span class="inline-block px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-sm font-bold break-all">
                            @if(is_bool($newVal)){{ $newVal?'نعم':'لا' }}@elseif($dispNew!==null&&$dispNew!==''){{ $dispNew }}@else—@endif
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    @if($hasScores)
    <div class="px-5 pb-5 {{ !empty($changedFields)?'':'pt-5' }}">
        <div class="bg-violet-50 rounded-xl border border-violet-200 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-2.5 border-b border-violet-200 bg-violet-100/60">
                <p class="text-xs font-bold text-violet-800">النقاط</p>
                @if(!empty($changedScores))<span class="text-xs font-bold text-violet-600">{{ count($changedScores) }} قيمة تغيّرت</span>@endif
            </div>
            <div class="p-3 grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach($scoreFields as $sf)
                    @php $oldScore=$original['scores'][$sf]??null; $newScore=$payload['scores'][$sf]??null; $scoreChg=$oldScore!=$newScore; if($oldScore===null&&$newScore===null)continue; @endphp
                    <div class="rounded-xl px-3 py-2.5 {{ $scoreChg?'bg-white border-2 border-violet-400 shadow-sm':'bg-violet-50/70 border border-violet-100' }}">
                        <p class="text-xs text-gray-500 mb-1.5">{{ $labels['scores.'.$sf]??$sf }}</p>
                        @if($scoreChg)
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-sm font-bold text-red-500 line-through">{{ $oldScore??'—' }}</span>
                                <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                <span class="text-base font-black text-emerald-600">{{ $newScore??'—' }}</span>
                            </div>
                        @else
                            <p class="text-sm font-semibold text-gray-600">{{ $oldScore??'—' }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            @php
                $oldTotal=$original['scores']['total_score']??$original['score']??null;
                $newTotal=$payload['scores']['total_score']??null; $totalChg=$oldTotal!=$newTotal;
                $oldEst=$original['estimated_amount']??($oldTotal!==null?$oldTotal*500:null);
                $newEst=$newTotal!==null?$newTotal*500:null;
            @endphp
            @if($oldTotal!==null||$newTotal!==null)
            <div class="px-4 py-3 border-t border-violet-200 bg-violet-100/40 flex items-center justify-between">
                <p class="text-xs font-bold text-violet-800">مجموع النقاط</p>
                @if($totalChg)
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-red-500 line-through">{{ $oldTotal??'—' }}</span>
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-xl font-black text-emerald-600">{{ $newTotal??'—' }}</span>
                        @if($oldTotal!==null&&$newTotal!==null)
                            @php $diff=$newTotal-$oldTotal; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $diff>0?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-600' }}">{{ $diff>0?'+':'' }}{{ $diff }}</span>
                        @endif
                    </div>
                @else
                    <span class="text-base font-black text-gray-700">{{ $oldTotal??'—' }}</span>
                @endif
            </div>
            @if($oldEst!==null||$newEst!==null)
            <div class="px-4 py-2.5 border-t border-violet-100 bg-violet-50/50 flex items-center justify-between">
                <p class="text-xs font-semibold text-violet-600">المبلغ المقدر</p>
                @if($oldEst!=$newEst)
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-red-400 line-through">{{ $oldEst!==null?number_format($oldEst).' ل.س':'—' }}</span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-sm font-bold text-emerald-600">{{ $newEst!==null?number_format($newEst).' ل.س':'—' }}</span>
                    </div>
                @else
                    <span class="text-sm font-semibold text-gray-600">{{ $oldEst!==null?number_format($oldEst).' ل.س':'—' }}</span>
                @endif
            </div>
            @endif
            @endif
        </div>
    </div>
    @endif

    @if($hasPayment && !empty($changedPayments))
    <div class="px-5 pb-5">
        <div class="bg-amber-50 rounded-xl border border-amber-200 overflow-hidden">
            <div class="px-4 py-2.5 border-b border-amber-200 bg-amber-100/60"><p class="text-xs font-bold text-amber-800">بيانات الدفع</p></div>
            <div class="p-3 space-y-2">
                @foreach($changedPayments as $pf)
                    @php $oldVal=$original['payment'][$pf]??null; $newVal=$payload['payment'][$pf]??null; @endphp
                    <div class="bg-white rounded-xl px-3 py-2.5 border border-amber-100">
                        <p class="text-xs font-bold text-amber-700 mb-1.5">{{ $labels['payment.'.$pf]??$pf }}</p>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-1 rounded bg-red-100 text-red-700 text-xs font-mono line-through break-all">{{ $oldVal?:'—' }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700 text-xs font-bold font-mono break-all">{{ $newVal?:'—' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @endif

    @if($totalUnchanged > 0)
    <div class="border-t border-gray-100">
        <button type="button"
                onclick="this.nextElementSibling.classList.toggle('hidden');this.querySelector('.chev').classList.toggle('rotate-180')"
                class="w-full flex items-center justify-between px-5 py-3 text-xs font-semibold text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors">
            <span>الحقول غير المُعدَّلة ({{ $totalUnchanged }})</span>
            <svg class="chev w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div class="hidden px-5 pb-5 grid grid-cols-2 sm:grid-cols-3 gap-2">
            @foreach($unchangedFields as $field)
                @php $val=$original[$field]??$payload[$field]??null; $dispVal=$resolveId($field,$val); @endphp
                <div class="rounded-xl px-3 py-2.5 bg-gray-50 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-0.5">{{ $labels[$field]??$field }}</p>
                    <p class="text-sm text-gray-600">@if(is_bool($val)){{ $val?'نعم':'لا' }}@elseif($dispVal!==null&&$dispVal!==''){{ $dispVal }}@else—@endif</p>
                </div>
            @endforeach
            @foreach($unchangedScores as $sf)
                @php $val=$original['scores'][$sf]??$payload['scores'][$sf]??null; @endphp
                <div class="rounded-xl px-3 py-2.5 bg-gray-50 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-0.5">{{ $labels['scores.'.$sf]??$sf }}</p>
                    <p class="text-sm text-gray-600">{{ $val??'—' }}</p>
                </div>
            @endforeach
            @foreach($unchangedPayments as $pf)
                @php $val=$original['payment'][$pf]??$payload['payment'][$pf]??null; @endphp
                <div class="rounded-xl px-3 py-2.5 bg-gray-50 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-0.5">{{ $labels['payment.'.$pf]??$pf }}</p>
                    <p class="text-sm text-gray-600 font-mono">{{ $val?:'—' }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@elseif($action === 'create')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
    <div class="flex items-center gap-2.5 bg-gradient-to-l from-emerald-50 to-teal-50 border-b border-emerald-100 px-6 py-3.5">
        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </div>
        <h2 class="text-sm font-bold text-emerald-800">بيانات {{ $pendingChange->modelLabel() }} المراد إضافته</h2>
    </div>
    <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach($topFields as $field)
            @if(isset($payload[$field]))
                @php $dispVal=$resolveId($field,$payload[$field]); @endphp
                <div class="bg-emerald-50/50 rounded-xl px-3 py-2.5 border border-emerald-100">
                    <p class="text-xs text-gray-400 mb-0.5">{{ $labels[$field]??$field }}</p>
                    <p class="text-sm font-semibold text-gray-800">@if(is_bool($payload[$field])){{ $payload[$field]?'نعم':'لا' }}@elseif($dispVal!==null&&$dispVal!==''){{ $dispVal }}@else—@endif</p>
                </div>
            @endif
        @endforeach
    </div>
</div>

@elseif($action === 'delete')
<div class="bg-red-50 border border-red-200 rounded-2xl p-5 mb-5">
    <div class="flex items-center gap-2.5 mb-4">
        <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h2 class="text-sm font-bold text-red-800">سيتم حذف هذا {{ $pendingChange->modelLabel() }}</h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach($topFields as $field)
            @if(isset($original[$field]))
                @php $dispOrig=$resolveId($field,$original[$field]); @endphp
                <div class="bg-white rounded-xl px-3 py-2.5 border border-red-100">
                    <p class="text-xs text-gray-400 mb-0.5">{{ $labels[$field]??$field }}</p>
                    <p class="text-sm font-semibold text-gray-800">@if(is_bool($original[$field])){{ $original[$field]?'نعم':'لا' }}@elseif($dispOrig!==null&&$dispOrig!==''){{ $dispOrig }}@else—@endif</p>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════════
     EDIT FORM
     ══════════════════════════════════════════════════════ --}}
<form method="POST" action="{{ route('pending-changes.update-request', $pendingChange) }}">
    @csrf
    @method('PATCH')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">

        {{-- Form header --}}
        <div class="flex items-center gap-2.5 bg-gradient-to-l from-slate-50 to-gray-50 border-b border-gray-100 px-6 py-3.5">
            <div class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-gray-800">تعديل بيانات الطلب</h2>
            <span class="text-xs text-gray-400">— اترك الحقل على قيمته الحالية إذا لا تريد تغييره</span>
        </div>

        <div class="p-6 space-y-6">

        {{-- ══════════════════════════════════════════
             MEMBER UPDATE / CREATE
             ══════════════════════════════════════════ --}}
        @if($modelType === 'member' && in_array($action, ['update','create']))
        @php
            $p = $payload;
            $verificationStatuses = \App\Models\VerificationStatus::active()->orderBy('name')->get();
            $finalStatusList      = \App\Models\FinalStatus::active()->orderBy('name')->get();
            $maritalStatusList    = \App\Models\MaritalStatus::active()->orderBy('id')->get();
            $associationList      = \App\Models\Association::active()->orderBy('name')->get();
            $representativeList   = \App\Models\User::orderBy('name')->get();
            $housingStatusListFrm = \App\Models\HousingStatus::active()->orderBy('name')->get();
            $regionListFrm        = \App\Models\Region::active()->orderBy('name')->get();
            $sectorListFrm        = \App\Models\Sector::active()->orderBy('name')->get();
        @endphp

        {{-- ① البيانات الشخصية --}}
        <div>
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-5 h-5 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 font-black text-xs">١</span>
                البيانات الشخصية
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        الاسم الكامل
                        @if(in_array('full_name', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[full_name]" value="{{ old('payload.full_name', $p['full_name']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        اسم الأم
                        @if(in_array('mother_name', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[mother_name]" value="{{ old('payload.mother_name', $p['mother_name']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        رقم الهوية
                        @if(in_array('national_id', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[national_id]" value="{{ old('payload.national_id', $p['national_id']??'') }}" class="{{ $inp }} font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        العمر
                        @if(in_array('age', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="number" name="payload[age]" value="{{ old('payload.age', $p['age']??'') }}" min="0" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        الجنس
                        @if(in_array('gender', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <select name="payload[gender]" class="{{ $inp }}">
                        <option value="">— اختر —</option>
                        <option value="ذكر" {{ ($p['gender']??'')==='ذكر'?'selected':'' }}>ذكر</option>
                        <option value="أنثى" {{ ($p['gender']??'')==='أنثى'?'selected':'' }}>أنثى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        الحالة الاجتماعية
                        @if(in_array('marital_status', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <select name="payload[marital_status]" class="{{ $inp }}">
                        <option value="">— اختر —</option>
                        @foreach($maritalStatusList as $ms)
                            <option value="{{ $ms->name }}" {{ ($p['marital_status']??'')===$ms->name?'selected':'' }}>{{ $ms->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        رقم الاضبارة
                        @if(in_array('dossier_number', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[dossier_number]" value="{{ old('payload.dossier_number', $p['dossier_number']??'') }}" class="{{ $inp }} font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        حالة التحقق
                        @if(in_array('verification_status_id', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <select name="payload[verification_status_id]" class="{{ $inp }}">
                        <option value="">— اختر —</option>
                        @foreach($verificationStatuses as $vs)
                            <option value="{{ $vs->id }}" {{ ($p['verification_status_id']??'')==$vs->id?'selected':'' }}>{{ $vs->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        الحالة النهائية
                        @if(in_array('final_status_id', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <select name="payload[final_status_id]" class="{{ $inp }}">
                        <option value="">— اختر —</option>
                        @foreach($finalStatusList as $fs)
                            <option value="{{ $fs->id }}" {{ ($p['final_status_id']??'')==$fs->id?'selected':'' }}>{{ $fs->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- ② الاتصال والعنوان --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-5 h-5 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 font-black text-xs">٢</span>
                الاتصال والعنوان
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        الهاتف
                        @if(in_array('phone', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[phone]" value="{{ old('payload.phone', $p['phone']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        الهاتف الثاني
                        @if(in_array('phone2', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[phone2]" value="{{ old('payload.phone2', $p['phone2']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        نوع الشبكة
                        @if(in_array('network', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[network]" value="{{ old('payload.network', $p['network']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        المنطقة
                        @if(in_array('region_id', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <select name="payload[region_id]" class="{{ $inp }}">
                        <option value="">— اختر —</option>
                        @foreach($regionListFrm as $rg)
                            <option value="{{ $rg->id }}" {{ ($p['region_id']??'')==$rg->id?'selected':'' }}>{{ $rg->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        القطاع
                        @if(in_array('sector_id', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <select name="payload[sector_id]" class="{{ $inp }}">
                        <option value="">— اختر —</option>
                        @foreach($sectorListFrm as $sc)
                            <option value="{{ $sc->id }}" {{ ($p['sector_id']??'')==$sc->id?'selected':'' }}>{{ $sc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        العنوان التفصيلي
                        @if(in_array('current_address', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[current_address]" value="{{ old('payload.current_address', $p['current_address']??'') }}" class="{{ $inp }}">
                </div>
            </div>
        </div>

        {{-- ③ الوضع المعيشي --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-5 h-5 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 font-black text-xs">٣</span>
                الوضع المعيشي
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        العمل
                        @if(in_array('job', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[job]" value="{{ old('payload.job', $p['job']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        وضع السكن
                        @if(in_array('housing_status_id', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <select name="payload[housing_status_id]" class="{{ $inp }}">
                        <option value="">— اختر —</option>
                        @foreach($housingStatusListFrm as $hs)
                            <option value="{{ $hs->id }}" {{ ($p['housing_status_id']??'')==$hs->id?'selected':'' }}>{{ $hs->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        حالة المعيل
                        @if(in_array('provider_status', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[provider_status]" value="{{ old('payload.provider_status', $p['provider_status']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        عدد المعالين
                        @if(in_array('dependents_count', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="number" name="payload[dependents_count]" value="{{ old('payload.dependents_count', $p['dependents_count']??'') }}" min="0" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        الجمعية
                        @if(in_array('association_id', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <select name="payload[association_id]" class="{{ $inp }}">
                        <option value="">— اختر —</option>
                        @foreach($associationList as $assoc)
                            <option value="{{ $assoc->id }}" {{ ($p['association_id']??'')==$assoc->id?'selected':'' }}>{{ $assoc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        المندوب
                        @if(in_array('delegate', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[delegate]" value="{{ old('payload.delegate', $p['delegate']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        الممثل المسؤول
                        @if(in_array('representative_id', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <select name="payload[representative_id]" class="{{ $inp }}">
                        <option value="">— اختر —</option>
                        @foreach($representativeList as $rep)
                            <option value="{{ $rep->id }}" {{ ($p['representative_id']??'')==$rep->id?'selected':'' }}>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-4 pt-5">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="payload[other_association]" value="0">
                        <input type="checkbox" name="payload[other_association]" value="1"
                               {{ !empty($p['other_association'])?'checked':'' }}
                               class="w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-400">
                        <span class="text-xs font-semibold text-gray-600">جمعية أخرى</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ④ الحالة الصحية --}}
        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-5 h-5 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 font-black text-xs">٤</span>
                الحالة الصحية والملاحظات
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        نوع المرض
                        @if(in_array('disease_type', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[disease_type]" value="{{ old('payload.disease_type', $p['disease_type']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        وصف الحالة الخاصة
                        @if(in_array('special_cases_description', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[special_cases_description]" value="{{ old('payload.special_cases_description', $p['special_cases_description']??'') }}" class="{{ $inp }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        تفاصيل المرض
                        @if(in_array('illness_details', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <textarea name="payload[illness_details]" rows="2" class="{{ $inp }} resize-none">{{ old('payload.illness_details', $p['illness_details']??'') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        ملاحظات
                        @if(in_array('notes', $changedFields??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <textarea name="payload[notes]" rows="3" class="{{ $inp }} resize-none">{{ old('payload.notes', $p['notes']??'') }}</textarea>
                </div>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="payload[special_cases]" value="0">
                        <input type="checkbox" name="payload[special_cases]" value="1"
                               {{ !empty($p['special_cases'])?'checked':'' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-400">
                        <span class="text-xs font-semibold text-gray-600">حالة خاصة</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ⑤ النقاط --}}
        @if(!empty($p['scores']))
        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-5 h-5 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 font-black text-xs">٥</span>
                النقاط
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-3">
                @foreach(['work_score'=>'نقاط العمل','housing_score'=>'نقاط السكن','dependents_score'=>'نقاط الأفراد','dependent_status_score'=>'نقاط المعيل','illness_score'=>'نقاط المرض','special_cases_score'=>'نقاط الخاصة'] as $sf=>$slbl)
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        {{ $slbl }}
                        @if(in_array($sf, $changedScores??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="number" name="payload[scores][{{ $sf }}]" value="{{ old('payload.scores.'.$sf, $p['scores'][$sf]??0) }}" min="0" step="0.5" class="{{ $inp }} text-center">
                </div>
                @endforeach
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                <div>
                    <label class="block text-xs font-bold text-emerald-700 mb-1">إضافة النقاط</label>
                    <input type="number" name="payload[scores][score_addition]" value="{{ old('payload.scores.score_addition', $p['scores']['score_addition']??0) }}" min="0"
                           class="w-full border border-emerald-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-400 bg-white text-emerald-700 font-bold text-center">
                </div>
                <div>
                    <label class="block text-xs font-bold text-emerald-700 mb-1">سبب الإضافة</label>
                    <input type="text" name="payload[scores][score_addition_reason]" value="{{ old('payload.scores.score_addition_reason', $p['scores']['score_addition_reason']??'') }}"
                           class="w-full border border-emerald-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-400 bg-white">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-red-50 border border-red-200 rounded-xl">
                <div>
                    <label class="block text-xs font-bold text-red-600 mb-1">انقاص النقاط</label>
                    <input type="number" name="payload[scores][score_deduction]" value="{{ old('payload.scores.score_deduction', $p['scores']['score_deduction']??0) }}" min="0"
                           class="w-full border border-red-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-400 bg-white text-red-700 font-bold text-center">
                </div>
                <div>
                    <label class="block text-xs font-bold text-red-600 mb-1">سبب الانقاص</label>
                    <input type="text" name="payload[scores][score_deduction_reason]" value="{{ old('payload.scores.score_deduction_reason', $p['scores']['score_deduction_reason']??'') }}"
                           class="w-full border border-red-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-400 bg-white">
                </div>
            </div>
        </div>
        @endif

        {{-- ⑥ بيانات الدفع --}}
        @if(!empty($p['payment']))
        <div class="border-t border-gray-100 pt-5">
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-5 h-5 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 font-black text-xs">٦</span>
                بيانات الدفع
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        اسم المستلم
                        @if(in_array('recipient_name', $changedPayments??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[payment][recipient_name]" value="{{ old('payload.payment.recipient_name', $p['payment']['recipient_name']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        اسم مدخل البيانات
                        @if(in_array('data_entry_name', $changedPayments??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[payment][data_entry_name]" value="{{ old('payload.payment.data_entry_name', $p['payment']['data_entry_name']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        رقم الآيبان
                        @if(in_array('iban', $changedPayments??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[payment][iban]" value="{{ old('payload.payment.iban', $p['payment']['iban']??'') }}" class="{{ $inp }} font-mono" dir="ltr">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        الباركود
                        @if(in_array('barcode', $changedPayments??[]))<span class="text-amber-500 text-xs">←</span>@endif
                    </label>
                    <input type="text" name="payload[payment][barcode]" value="{{ old('payload.payment.barcode', $p['payment']['barcode']??'') }}" class="{{ $inp }} font-mono" dir="ltr">
                </div>
            </div>
        </div>
        @endif

        {{-- ══════════════════════════════════════════
             FIELD VISIT UPDATE / CREATE
             ══════════════════════════════════════════ --}}
        @elseif($modelType === 'field_visit')
        @php $p = $payload; @endphp

        <div>
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3">بيانات الجولة الميدانية</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">حالة الجولة</label>
                    <select name="payload[field_visit_status_id]" class="{{ $inp }}">
                        <option value="">— اختر الحالة —</option>
                        @foreach(\App\Models\FieldVisitStatus::orderBy('name')->get() as $fvs)
                            <option value="{{ $fvs->id }}" {{ ($p['field_visit_status_id']??'')==$fvs->id?'selected':'' }}>{{ $fvs->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">نوع البيت</label>
                    <select name="payload[house_type_id]" class="{{ $inp }}">
                        <option value="">— اختر النوع —</option>
                        @foreach(\App\Models\HouseType::active()->orderBy('name')->get() as $ht)
                            <option value="{{ $ht->id }}" {{ ($p['house_type_id']??'')==$ht->id?'selected':'' }}>{{ $ht->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">حالة البيت</label>
                    <select name="payload[house_condition_id]" class="{{ $inp }}">
                        <option value="">— اختر الحالة —</option>
                        @foreach(\App\Models\HouseCondition::active()->orderBy('name')->get() as $hc)
                            <option value="{{ $hc->id }}" {{ ($p['house_condition_id']??'')==$hc->id?'selected':'' }}>{{ $hc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">تاريخ الزيارة</label>
                    <input type="date" name="payload[visit_date]" value="{{ old('payload.visit_date', $p['visit_date']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">اسم الزائر</label>
                    <input type="text" name="payload[visitor]" value="{{ old('payload.visitor', $p['visitor']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">المبلغ المقدر (ل.س)</label>
                    <input type="number" name="payload[estimated_amount]" value="{{ old('payload.estimated_amount', $p['estimated_amount']??'') }}" min="0" step="0.01" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">سبب المبلغ</label>
                    <input type="text" name="payload[amount_reason]" value="{{ old('payload.amount_reason', $p['amount_reason']??'') }}" class="{{ $inp }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظات</label>
                    <textarea name="payload[notes]" rows="3" class="{{ $inp }} resize-none">{{ old('payload.notes', $p['notes']??'') }}</textarea>
                </div>
                <div class="flex items-center gap-6 pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="payload[has_video]" value="0">
                        <input type="checkbox" name="payload[has_video]" value="1" {{ !empty($p['has_video'])?'checked':'' }}
                               class="w-4 h-4 text-rose-600 border-gray-300 rounded focus:ring-rose-400">
                        <span class="text-xs font-semibold text-gray-600">يوجد فيديو</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="payload[has_special_case]" value="0">
                        <input type="checkbox" name="payload[has_special_case]" value="1" {{ !empty($p['has_special_case'])?'checked':'' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-400">
                        <span class="text-xs font-semibold text-gray-600">حالة خاصة</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════
             DONATION UPDATE / CREATE
             ══════════════════════════════════════════ --}}
        @elseif($modelType === 'donation')
        @php $p = $payload; @endphp

        <div>
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3">بيانات التبرع</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">المبلغ (ل.س)</label>
                    <input type="number" name="payload[amount]" value="{{ old('payload.amount', $p['amount']??'') }}" min="0" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">شهر التبرع</label>
                    <input type="month" name="payload[donation_month]" value="{{ old('payload.donation_month', $p['donation_month']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">رقم المرجع</label>
                    <input type="text" name="payload[reference_number]" value="{{ old('payload.reference_number', $p['reference_number']??'') }}" class="{{ $inp }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">النوع</label>
                    <select name="payload[type]" class="{{ $inp }}">
                        <option value="cash" {{ ($p['type']??'')==='cash'?'selected':'' }}>نقدي</option>
                        <option value="transfer" {{ ($p['type']??'')==='transfer'?'selected':'' }}>تحويل</option>
                        <option value="other" {{ ($p['type']??'')==='other'?'selected':'' }}>أخرى</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظات</label>
                    <textarea name="payload[notes]" rows="3" class="{{ $inp }} resize-none">{{ old('payload.notes', $p['notes']??'') }}</textarea>
                </div>
            </div>
        </div>

        @endif

        {{-- ══════════════════════════════════════════
             REQUESTER NOTE (all types)
             ══════════════════════════════════════════ --}}
        <div class="border-t border-gray-100 pt-5">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                ملاحظة للمسؤول
                <span class="font-normal text-gray-400">(اختياري — تظهر للمسؤول عند المراجعة)</span>
            </label>
            <textarea name="_requester_notes" rows="2"
                      placeholder="أضف أي توضيح أو سبب..."
                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none @error('_requester_notes') border-red-300 @enderror">{{ old('_requester_notes', $payload['_requester_notes']??'') }}</textarea>
            @error('_requester_notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        </div>{{-- /p-6 --}}

        {{-- Form footer --}}
        <div class="bg-gray-50 border-t border-gray-100 px-6 py-4 flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                حفظ التعديلات
            </button>
            <a href="{{ route('pending-changes.my', ['status'=>'pending']) }}"
               class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 font-semibold text-sm px-4 py-2.5 rounded-xl border border-gray-200 hover:bg-white transition-colors">
                إلغاء
            </a>
            <p class="text-xs text-gray-400 mr-auto hidden sm:block">الطلب لا يزال معلّقاً — لن يُطبَّق إلا بعد موافقة المسؤول</p>
        </div>
    </div>
</form>

{{-- ══════════════════════════════════════════════════════
     WITHDRAW
     ══════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
    <div class="flex items-start justify-between gap-4 px-6 py-5">
        <div>
            <p class="text-sm font-bold text-red-700 mb-1">سحب الطلب نهائياً</p>
            <p class="text-xs text-gray-400">سيُحذف الطلب بالكامل ولن يتمكن المسؤول من رؤيته.</p>
        </div>
        <form method="POST" action="{{ route('pending-changes.withdraw', $pendingChange) }}"
              onsubmit="return confirm('هل أنت متأكد من سحب هذا الطلب وحذفه نهائياً؟')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-sm px-5 py-2.5 rounded-xl border border-red-200 hover:border-red-600 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                سحب الطلب
            </button>
        </form>
    </div>
</div>

@endsection
