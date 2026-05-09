<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مستحقاتي — {{ $employee->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .tx-row:hover { background: rgba(255,255,255,0.04); }
    </style>
</head>
<body class="min-h-screen" style="background:#0f172a;color:#e2e8f0">

{{-- Top nav --}}
<header class="sticky top-0 z-30 px-4 sm:px-6 py-3 flex items-center justify-between" style="background:rgba(15,23,42,0.9);backdrop-filter:blur(12px);border-bottom:1px solid rgba(255,255,255,0.07)">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white font-black text-sm"
             style="background:linear-gradient(135deg,#6366f1,#4f46e5)">
            {{ mb_substr($employee->name, 0, 1) }}
        </div>
        <div>
            <p class="text-sm font-bold text-white leading-none">{{ $employee->name }}</p>
            @if($employee->job_title)
                <p class="text-[11px] leading-none mt-0.5" style="color:#64748b">{{ $employee->job_title }}{{ $employee->department ? ' · ' . $employee->department : '' }}</p>
            @endif
        </div>
    </div>
    <form method="POST" action="{{ route('employee-portal.logout') }}">
        @csrf
        <button type="submit"
                class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-xl transition-all"
                style="background:rgba(255,255,255,0.07);color:#94a3b8;border:1px solid rgba(255,255,255,0.1)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            خروج
        </button>
    </form>
</header>

<main class="max-w-2xl mx-auto px-4 sm:px-6 py-8">

    {{-- Balance hero --}}
    @php
        $sypNet = $totals['SYP']['net'];
        $usdNet = $totals['USD']['net'];
        $hasSYP = ($totals['SYP']['salary'] + $totals['SYP']['additions'] + $totals['SYP']['bonuses'] + $totals['SYP']['deductions'] + $totals['SYP']['advances']) > 0;
        $hasUSD = ($totals['USD']['salary'] + $totals['USD']['additions'] + $totals['USD']['bonuses'] + $totals['USD']['deductions'] + $totals['USD']['advances']) > 0;
        $netPositive = (!$hasSYP || $sypNet >= 0) && (!$hasUSD || $usdNet >= 0);
    @endphp

    <div class="rounded-3xl p-6 mb-6 text-center" style="background:linear-gradient(135deg,rgba(99,102,241,0.15),rgba(6,182,212,0.08));border:1px solid rgba(99,102,241,0.2)">
        <p class="text-[11px] font-black uppercase tracking-[0.25em] mb-4" style="color:#6366f1">الرصيد المتبقي</p>
        @if(!$hasSYP && !$hasUSD)
            <p class="text-5xl font-black text-white mb-2">—</p>
            <p class="text-sm" style="color:#475569">لا توجد معاملات مسجلة</p>
        @else
            @if($hasSYP)
            <div class="flex items-baseline justify-center gap-2 mb-2">
                <span class="text-xl font-bold {{ $sypNet >= 0 ? '' : '' }}" style="color:{{ $sypNet >= 0 ? '#34d399' : '#f87171' }}">{{ $sypNet >= 0 ? '+' : '−' }}</span>
                <span class="text-5xl font-black tracking-tight" style="color:{{ $sypNet >= 0 ? '#34d399' : '#f87171' }}">{{ number_format(abs($sypNet)) }}</span>
                <span class="text-lg font-bold" style="color:{{ $sypNet >= 0 ? '#10b981' : '#ef4444' }}">ل.س</span>
            </div>
            @endif
            @if($hasUSD)
            <div class="flex items-baseline justify-center gap-2 {{ $hasSYP ? 'mt-3' : 'mb-2' }}">
                <span class="text-xl font-bold" style="color:{{ $usdNet >= 0 ? '#34d399' : '#f87171' }}">{{ $usdNet >= 0 ? '+' : '−' }}</span>
                <span class="text-5xl font-black tracking-tight" style="color:{{ $usdNet >= 0 ? '#34d399' : '#f87171' }}">{{ number_format(abs($usdNet), 2) }}</span>
                <span class="text-lg font-bold" style="color:{{ $usdNet >= 0 ? '#10b981' : '#ef4444' }}">$</span>
            </div>
            @endif
            <p class="text-xs mt-3" style="color:#475569">راتب + إضافات + مكافآت − خصومات − سلف</p>
        @endif
    </div>

    {{-- Breakdown cards --}}
    @if($hasSYP || $hasUSD)
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        @php
            $breakdown = [
                ['الرواتب',   'salary',     '#3b82f6', '#1d4ed8', '+'],
                ['الإضافات',  'additions',  '#10b981', '#059669', '+'],
                ['الخصومات',  'deductions', '#ef4444', '#dc2626', '−'],
                ['السلف',     'advances',   '#f59e0b', '#d97706', '−'],
            ];
        @endphp
        @foreach($breakdown as [$label, $key, $clr, $dark, $sign])
        @php
            $valSYP = $totals['SYP'][$key];
            $valUSD = $totals['USD'][$key];
        @endphp
        @if($valSYP > 0 || $valUSD > 0)
        <div class="rounded-2xl p-4 text-center" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.07)">
            <p class="text-[10px] font-black uppercase tracking-wider mb-2" style="color:{{ $clr }}">{{ $label }}</p>
            @if($valSYP > 0)
                <p class="text-lg font-black leading-tight" style="color:#e2e8f0">{{ $sign }}{{ number_format($valSYP) }}</p>
                <p class="text-[10px] mt-0.5" style="color:#475569">ل.س</p>
            @endif
            @if($valUSD > 0)
                <p class="text-lg font-black leading-tight" style="color:#e2e8f0">{{ $sign }}{{ number_format($valUSD, 2) }}</p>
                <p class="text-[10px] mt-0.5" style="color:#475569">$</p>
            @endif
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- Transactions list --}}
    <div class="rounded-3xl overflow-hidden" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07)">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.06)">
            <h2 class="font-black text-sm text-white">سجل العمليات</h2>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg" style="background:rgba(255,255,255,0.07);color:#64748b">
                {{ $employee->transactions->count() }} عملية
            </span>
        </div>

        @if($employee->transactions->isEmpty())
            <div class="py-16 text-center">
                <p class="text-sm" style="color:#475569">لا توجد عمليات مسجلة</p>
            </div>
        @else
        @php
            $typeMeta = [
                'salary'    => ['label' => 'راتب',    'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,0.12)', 'sign' => '+'],
                'addition'  => ['label' => 'إضافة',   'color' => '#10b981', 'bg' => 'rgba(16,185,129,0.12)', 'sign' => '+'],
                'bonus'     => ['label' => 'مكافأة',  'color' => '#8b5cf6', 'bg' => 'rgba(139,92,246,0.12)', 'sign' => '+'],
                'deduction' => ['label' => 'خصم',     'color' => '#ef4444', 'bg' => 'rgba(239,68,68,0.12)',  'sign' => '−'],
                'advance'   => ['label' => 'سلفة',    'color' => '#f59e0b', 'bg' => 'rgba(245,158,11,0.12)', 'sign' => '−'],
            ];
        @endphp
        <div class="divide-y" style="border-color:rgba(255,255,255,0.05)">
            @foreach($employee->transactions as $tx)
            @php $meta = $typeMeta[$tx->type] ?? ['label' => $tx->type, 'color' => '#94a3b8', 'bg' => 'rgba(148,163,184,0.1)', 'sign' => '']; @endphp
            <div class="tx-row flex items-center gap-4 px-5 py-4 transition-colors">
                {{-- Type badge --}}
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-xs font-black"
                     style="background:{{ $meta['bg'] }};color:{{ $meta['color'] }}">
                    {{ mb_substr($meta['label'], 0, 1) }}
                </div>
                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-xs font-bold" style="color:{{ $meta['color'] }}">{{ $meta['label'] }}</span>
                        @if($tx->reason)
                            <span class="text-xs truncate" style="color:#64748b">· {{ $tx->reason }}</span>
                        @endif
                    </div>
                    <p class="text-[11px]" style="color:#475569">{{ $tx->transaction_date->format('Y/m/d') }}</p>
                </div>
                {{-- Amount --}}
                <div class="text-left shrink-0">
                    <p class="text-base font-black" style="color:{{ $meta['color'] }}">
                        {{ $meta['sign'] }}{{ $tx->currency === 'USD' ? number_format((float)$tx->amount, 2) : number_format((float)$tx->amount) }}
                    </p>
                    <p class="text-[11px] text-left" style="color:#475569">{{ $tx->currency === 'USD' ? '$' : 'ل.س' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <p class="text-center text-xs mt-8" style="color:#1e293b">مسالك النور · بوابة الموظفين</p>
</main>

</body>
</html>
