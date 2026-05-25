@extends('layouts.app')

@section('title', 'الأرشيف — ' . $date . ' — مسالك النور')
@section('max-width', 'max-w-6xl')

@section('breadcrumb')
    <span class="text-gray-700">الأرشيف</span>
@endsection

@section('content')

@php
$actionMeta = [
    'created'  => ['label' => 'إضافة',           'color' => 'blue',    'bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'border' => 'border-blue-100',  'dot' => 'bg-blue-500'],
    'updated'  => ['label' => 'تعديل',            'color' => 'amber',   'bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'border' => 'border-amber-100', 'dot' => 'bg-amber-500'],
    'deleted'  => ['label' => 'حذف',              'color' => 'red',     'bg' => 'bg-red-50',     'text' => 'text-red-700',     'border' => 'border-red-100',   'dot' => 'bg-red-500'],
    'approved' => ['label' => 'موافقة',           'color' => 'emerald', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100','dot' => 'bg-emerald-500'],
    'rejected' => ['label' => 'رفض',              'color' => 'rose',    'bg' => 'bg-rose-50',    'text' => 'text-rose-700',    'border' => 'border-rose-100',  'dot' => 'bg-rose-500'],
    'imported' => ['label' => 'استيراد',          'color' => 'violet',  'bg' => 'bg-violet-50',  'text' => 'text-violet-700',  'border' => 'border-violet-100','dot' => 'bg-violet-500'],
    'exported' => ['label' => 'تصدير',            'color' => 'indigo',  'bg' => 'bg-indigo-50',  'text' => 'text-indigo-700',  'border' => 'border-indigo-100','dot' => 'bg-indigo-500'],
    'login'    => ['label' => 'دخول',             'color' => 'teal',    'bg' => 'bg-teal-50',    'text' => 'text-teal-700',    'border' => 'border-teal-100',  'dot' => 'bg-teal-500'],
    'logout'   => ['label' => 'خروج',             'color' => 'gray',    'bg' => 'bg-gray-100',   'text' => 'text-gray-600',    'border' => 'border-gray-200',  'dot' => 'bg-gray-400'],
    'viewed'   => ['label' => 'عرض',              'color' => 'purple',  'bg' => 'bg-purple-50',  'text' => 'text-purple-700',  'border' => 'border-purple-100','dot' => 'bg-purple-400'],
];
$subjectLabels = [
    'Member' => 'مستفيد', 'Donation' => 'تبرع', 'FieldVisit' => 'جولة ميدانية',
    'PaymentInfo' => 'IBAN', 'PaymentInfoAI' => 'IBAN AI', 'PaymentReview' => 'مراجعة دفع',
    'Expense' => 'مصروف', 'Employee' => 'موظف', 'EmployeeTransaction' => 'معاملة موظف',
    'MemberImage' => 'صورة عضو', 'PendingChange' => 'طلب تعديل',
    'VerificationStatus' => 'حالة تحقق', 'FinalStatus' => 'حالة خاصة',
    'Association' => 'جمعية', 'Region' => 'منطقة', 'Sector' => 'قطاع',
    'HousingStatus' => 'حالة سكن',
];
@endphp

{{-- ── Hero ── --}}
<div class="relative bg-gradient-to-l from-slate-700 via-slate-600 to-slate-700 rounded-3xl p-5 sm:p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-40 h-40 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-56 h-56 bg-white rounded-full"></div>
    </div>
    <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-black text-white">الأرشيف</h1>
                <p class="text-slate-300 text-sm">{{ \Carbon\Carbon::parse($date)->translatedFormat('l، j F Y') }}</p>
            </div>
        </div>

        {{-- Date navigation --}}
        <div class="flex items-center gap-2">
            @if($prevDate)
            <a href="{{ route('archive.index', array_filter(['date' => $prevDate, 'user_id' => $userFilter, 'action' => $actionFilter, 'subject_type' => $subjectFilter])) }}"
               title="{{ $prevDate }}"
               class="flex items-center gap-1 bg-white/15 hover:bg-white/25 text-white text-xs font-medium px-3 py-2 rounded-xl transition-colors border border-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                السابق
            </a>
            @endif

            <form method="GET" action="{{ route('archive.index') }}" class="flex items-center gap-1">
                @if($userFilter)   <input type="hidden" name="user_id"      value="{{ $userFilter }}">    @endif
                @if($actionFilter) <input type="hidden" name="action"        value="{{ $actionFilter }}">  @endif
                @if($subjectFilter)<input type="hidden" name="subject_type"  value="{{ $subjectFilter }}"> @endif
                <input type="date" name="date" value="{{ $date }}"
                       onchange="this.form.submit()"
                       class="text-sm bg-white/90 text-gray-800 font-semibold border-0 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-white/50 cursor-pointer">
            </form>

            @if($nextDate)
            <a href="{{ route('archive.index', array_filter(['date' => $nextDate, 'user_id' => $userFilter, 'action' => $actionFilter, 'subject_type' => $subjectFilter])) }}"
               title="{{ $nextDate }}"
               class="flex items-center gap-1 bg-white/15 hover:bg-white/25 text-white text-xs font-medium px-3 py-2 rounded-xl transition-colors border border-white/20">
                التالي
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            @endif

            <a href="{{ route('archive.index', ['date' => now()->toDateString()]) }}"
               class="bg-white/15 hover:bg-white/25 text-white text-xs font-medium px-3 py-2 rounded-xl transition-colors border border-white/20">
                اليوم
            </a>
        </div>
    </div>
</div>

{{-- ── Stats by action ── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-3 mb-5">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">إجمالي النشاط</p>
            <p class="text-2xl font-black text-gray-800">{{ number_format($totalLogs) }}</p>
        </div>
    </div>
    @foreach($actionMeta as $action => $meta)
        @if(isset($statsByAction[$action]) && $statsByAction[$action] > 0)
        <a href="{{ route('archive.index', array_filter(['date' => $date, 'action' => $action, 'user_id' => $userFilter, 'subject_type' => $subjectFilter])) }}"
           class="bg-white rounded-2xl border {{ $actionFilter === $action ? 'border-'.$meta['color'].'-300 ring-2 ring-'.$meta['color'].'-200' : 'border-gray-100' }} shadow-sm px-4 py-3 flex items-center gap-3 hover:shadow-md transition-all">
            <div class="w-9 h-9 rounded-xl {{ $meta['bg'] }} flex items-center justify-center shrink-0">
                <span class="w-3 h-3 rounded-full {{ $meta['dot'] }}"></span>
            </div>
            <div>
                <p class="text-xs {{ $meta['text'] }} font-medium">{{ $meta['label'] }}</p>
                <p class="text-2xl font-black text-gray-800">{{ $statsByAction[$action] }}</p>
            </div>
        </a>
        @endif
    @endforeach
</div>

{{-- ── Table summary ── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5">
    <h2 class="text-xs font-black text-gray-400 uppercase tracking-wide mb-3">ملخص بالجداول</h2>
    <div class="flex flex-wrap gap-2">
        @foreach($tableSummary as $tbl)
            @php $tblTotal = ($tbl['created'] ?? 0) + ($tbl['updated'] ?? 0); @endphp
            @if($tblTotal > 0)
            <div class="flex items-center gap-2 bg-gray-50 border border-gray-100 rounded-xl px-3 py-2">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tbl['icon'] }}"/>
                </svg>
                <span class="text-xs font-semibold text-gray-700">{{ $tbl['label'] }}</span>
                @if(($tbl['created'] ?? 0) > 0)
                    <span class="text-xs bg-blue-100 text-blue-700 font-bold px-1.5 py-0.5 rounded-full">+{{ $tbl['created'] }}</span>
                @endif
                @if(($tbl['updated'] ?? 0) > 0)
                    <span class="text-xs bg-amber-100 text-amber-700 font-bold px-1.5 py-0.5 rounded-full">~{{ $tbl['updated'] }}</span>
                @endif
            </div>
            @endif
        @endforeach
        @if(collect($tableSummary)->sum(fn($t) => ($t['created'] ?? 0) + ($t['updated'] ?? 0)) === 0)
            <p class="text-xs text-gray-400">لا توجد سجلات مضافة أو معدّلة في هذا التاريخ.</p>
        @endif
    </div>
</div>

{{-- ── Filter bar ── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-5">
    <form method="GET" action="{{ route('archive.index') }}"
          class="flex flex-wrap items-center gap-3 px-4 py-3">
        <input type="hidden" name="date" value="{{ $date }}">

        {{-- User --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-gray-500">المستخدم:</span>
            <select name="user_id"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-slate-400 min-w-[130px]">
                <option value="">الكل</option>
                @foreach($usersList as $u)
                    <option value="{{ $u->id }}" {{ $userFilter == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Action --}}
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-gray-500">نوع العملية:</span>
            <select name="action"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-slate-400 min-w-[120px]">
                <option value="">الكل</option>
                @foreach($actionMeta as $a => $m)
                    <option value="{{ $a }}" {{ $actionFilter === $a ? 'selected' : '' }}>{{ $m['label'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Subject type --}}
        @if($subjectTypes->count() > 0)
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-gray-500">العنصر:</span>
            <select name="subject_type"
                    class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-slate-400 min-w-[130px]">
                <option value="">الكل</option>
                @foreach($subjectTypes as $st)
                    <option value="{{ $st }}" {{ $subjectFilter === $st ? 'selected' : '' }}>
                        {{ $subjectLabels[$st] ?? $st }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <button type="submit"
                class="bg-slate-700 hover:bg-slate-800 text-white text-sm font-semibold px-4 py-1.5 rounded-xl transition-colors">
            تطبيق
        </button>
        @if($userFilter || $actionFilter || $subjectFilter)
            <a href="{{ route('archive.index', ['date' => $date]) }}"
               class="text-sm text-gray-400 hover:text-gray-600 underline">مسح الفلاتر</a>
        @endif

        @if($logs->total() > 0 && ($userFilter || $actionFilter || $subjectFilter))
        <span class="mr-auto text-xs text-gray-500">
            نتائج: <span class="font-black text-gray-700">{{ number_format($logs->total()) }}</span> سجل
        </span>
        @endif
    </form>
</div>

{{-- ── Timeline ── --}}
@if($logs->isEmpty())
<div class="bg-white rounded-2xl border border-dashed border-gray-200 py-20 text-center">
    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
    </svg>
    <p class="text-gray-400 font-medium">لا يوجد نشاط مسجّل في هذا التاريخ</p>
    <p class="text-gray-300 text-xs mt-1">جرّب تاريخاً آخر أو أزل الفلاتر.</p>
</div>
@else
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50/50">
        <h2 class="text-sm font-black text-gray-700">سجل التعديلات</h2>
        <span class="text-xs text-gray-400">
            {{ number_format($logs->total()) }} سجل — الصفحة {{ $logs->currentPage() }} من {{ $logs->lastPage() }}
        </span>
    </div>

    {{-- Log entries --}}
    @php $lastHour = null; @endphp
    @foreach($logs as $log)
        @php
            $hour = $log->created_at->format('H:00');
            $meta = $actionMeta[$log->action] ?? ['label' => $log->action, 'bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'];
            $subjectLbl = $subjectLabels[$log->subject_type] ?? $log->subject_type;
            $memberId   = ($log->subject_type === 'Member') ? $log->subject_id : null;
            $dossier    = $memberId ? ($dossierMap[$memberId] ?? null) : null;
        @endphp

        {{-- Hour separator --}}
        @if($hour !== $lastHour)
            @php $lastHour = $hour; @endphp
            <div class="flex items-center gap-3 px-5 py-2 bg-slate-50/70 border-y border-slate-100">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs font-bold text-slate-500 font-mono">{{ $hour }}</span>
            </div>
        @endif

        <div class="flex items-start gap-4 px-5 py-3.5 border-b border-gray-50 hover:bg-gray-50/60 transition-colors group">

            {{-- Time --}}
            <span class="text-xs text-gray-400 font-mono shrink-0 pt-0.5 w-11 text-left">
                {{ $log->created_at->format('H:i') }}
            </span>

            {{-- Action badge --}}
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold shrink-0 {{ $meta['bg'] }} {{ $meta['text'] }} border {{ $meta['border'] }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $meta['dot'] }}"></span>
                {{ $meta['label'] }}
            </span>

            {{-- Main content --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-0.5">
                    {{-- Subject type --}}
                    @if($log->subject_type)
                    <span class="text-xs bg-slate-100 text-slate-600 font-semibold px-2 py-0.5 rounded-lg">
                        {{ $subjectLbl }}
                    </span>
                    @endif

                    {{-- Subject label (name) --}}
                    @if($log->subject_label)
                    <span class="text-sm font-bold text-gray-800 truncate max-w-[200px]">
                        {{ $log->subject_label }}
                    </span>
                    @endif

                    {{-- Dossier number --}}
                    @if($dossier)
                    <a href="{{ route('members.show', $memberId) }}"
                       class="text-xs text-emerald-600 font-bold bg-emerald-50 border border-emerald-100 rounded-full px-2 py-0.5 hover:bg-emerald-100 transition-colors">
                        ملف {{ $dossier }}
                    </a>
                    @elseif($memberId)
                    <a href="{{ route('members.show', $memberId) }}"
                       class="text-xs text-blue-500 font-medium hover:underline">
                        عرض العضو
                    </a>
                    @endif
                </div>

                {{-- Description --}}
                <p class="text-xs text-gray-500 leading-relaxed">{{ $log->description }}</p>

                {{-- Properties diff --}}
                @if(!empty($log->properties) && is_array($log->properties))
                <div class="mt-1.5 flex flex-wrap gap-1.5">
                    @foreach($log->properties as $field => $change)
                        @if(is_array($change) && isset($change['old'], $change['new']))
                        <span class="inline-flex items-center gap-1 text-[11px] bg-gray-50 border border-gray-100 rounded-lg px-2 py-0.5 font-mono">
                            <span class="text-gray-400">{{ $field }}:</span>
                            <span class="text-red-500 line-through">{{ is_array($change['old']) ? '...' : ($change['old'] ?? '—') }}</span>
                            <span class="text-gray-300">→</span>
                            <span class="text-emerald-600 font-bold">{{ is_array($change['new']) ? '...' : ($change['new'] ?? '—') }}</span>
                        </span>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- User --}}
            <span class="text-xs text-gray-400 shrink-0 hidden sm:block">
                {{ $log->user?->name ?? 'النظام' }}
            </span>
        </div>
    @endforeach

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endif

{{-- ── Supplementary: Field Visits ── --}}
@if($fieldVisits->count() > 0)
<div class="bg-white rounded-2xl border border-teal-100 shadow-sm overflow-hidden mt-6">
    <div class="flex items-center gap-3 px-5 py-3.5 border-b border-teal-100 bg-teal-50/50">
        <div class="w-7 h-7 rounded-xl bg-teal-500 flex items-center justify-center">
            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
        </div>
        <h2 class="text-sm font-black text-teal-800">الجولات الميدانية المُضافة في هذا اليوم</h2>
        <span class="text-xs bg-teal-100 text-teal-700 font-bold px-2 py-0.5 rounded-full">{{ $fieldVisits->count() }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">الوقت</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">العضو</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">رقم الملف</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">تاريخ الزيارة</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">الزائر</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">المبلغ</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">الحالة</th>
                    <th class="text-right font-semibold text-gray-500 px-4 py-3">أُضيف بواسطة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($fieldVisits as $fv)
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-4 py-3 text-xs text-gray-400 font-mono">
                        {{ \Carbon\Carbon::parse($fv->created_at)->format('H:i') }}
                    </td>
                    <td class="px-4 py-3 font-semibold text-gray-800">
                        <a href="{{ route('members.show', $fv->member_id) }}"
                           class="hover:text-teal-600 transition-colors">
                            {{ $fv->full_name }}
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        @if($fv->dossier_number)
                        <a href="{{ route('members.show', $fv->member_id) }}"
                           class="text-xs text-emerald-600 font-bold bg-emerald-50 border border-emerald-100 rounded-full px-2 py-0.5 hover:bg-emerald-100 transition-colors">
                            {{ $fv->dossier_number }}
                        </a>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        {{ $fv->visit_date ? \Carbon\Carbon::parse($fv->visit_date)->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">{{ $fv->visitor ?: '—' }}</td>
                    <td class="px-4 py-3 text-xs font-semibold text-gray-700">
                        {{ $fv->estimated_amount ? number_format($fv->estimated_amount, 0) . ' ل.س' : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($fv->status_name)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                              style="background:{{ $fv->status_color }}20;color:{{ $fv->status_color }};border:1px solid {{ $fv->status_color }}40">
                            {{ $fv->status_name }}
                        </span>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $fv->creator_name ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
