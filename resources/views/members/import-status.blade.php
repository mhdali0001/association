@extends('layouts.app')

@section('title', 'حالة الاستيراد — مسالك النور')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <a href="{{ route('members.import.show') }}" class="hover:text-emerald-700 transition-colors">استيراد من Excel</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">حالة الاستيراد</span>
@endsection

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
            حالة الاستيراد
        </h1>
        <a href="{{ route('members.import.show') }}"
           class="text-sm text-gray-500 hover:text-gray-700 border border-gray-200 px-4 py-2 rounded-lg transition-colors shrink-0">
            ← استيراد جديد
        </a>
    </div>

    {{-- Processing state (AJAX) --}}
    @if($importResult->status === 'pending')
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8" id="processing-block">

        {{-- Progress header --}}
        <div class="flex items-center gap-3 mb-6">
            <svg id="spinner" class="w-7 h-7 text-emerald-500 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <div>
                <p class="text-base font-semibold text-gray-800" id="status-text">جارٍ تحليل الملف…</p>
                <p class="text-xs text-gray-400" id="status-sub">الرجاء الانتظار، لا تغلق هذه الصفحة</p>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mb-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                <span id="progress-label">0 / {{ $importResult->total_rows }} صف</span>
                <span id="progress-pct">0%</span>
            </div>
            <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                <div id="progress-bar"
                     class="h-full bg-emerald-500 rounded-full transition-all duration-500"
                     style="width: 0%"></div>
            </div>
        </div>

        {{-- Live counts --}}
        <div class="grid grid-cols-2 gap-3 mt-5">
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 text-center">
                <p class="text-xl font-black text-emerald-700" id="live-imported">0</p>
                <p class="text-xs text-emerald-600 mt-0.5">تم الاستيراد</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-xl p-3 text-center">
                <p class="text-xl font-black text-red-700" id="live-errors">0</p>
                <p class="text-xs text-red-600 mt-0.5">أخطاء</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Done state --}}
    @if($importResult->status === 'done')
    <div class="space-y-4" id="done-block">

        <div class="grid grid-cols-2 gap-3">
            <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-center">
                <p class="text-2xl font-black text-emerald-700">{{ count($importResult->imported ?? []) }}</p>
                <p class="text-xs text-emerald-600 mt-1">تم الاستيراد</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-2xl p-4 text-center">
                <p class="text-2xl font-black text-red-700">{{ count($importResult->errors ?? []) }}</p>
                <p class="text-xs text-red-600 mt-1">أخطاء</p>
            </div>
        </div>

        @if(count($importResult->imported ?? []))
        <div class="bg-white border border-emerald-100 rounded-2xl overflow-hidden">
            <div class="bg-emerald-50 border-b border-emerald-100 px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm font-semibold text-emerald-700">الأعضاء الذين تم استيرادهم</span>
            </div>
            <div class="p-4 flex flex-wrap gap-2">
                @foreach($importResult->imported as $name)
                    <span class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-lg px-2.5 py-1">{{ $name }}</span>
                @endforeach
            </div>
        </div>
        @endif

        @if(count($importResult->errors ?? []))
        <div class="bg-white border border-red-100 rounded-2xl overflow-hidden">
            <div class="bg-red-50 border-b border-red-100 px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span class="text-sm font-semibold text-red-700">أخطاء</span>
            </div>
            <ul class="divide-y divide-red-50">
                @foreach($importResult->errors as $err)
                    <li class="px-5 py-2.5 text-xs text-red-800">{{ is_array($err) ? ($err['message'] ?? $err) : $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>
    @endif

    {{-- Failed state --}}
    @if($importResult->status === 'failed')
    <div class="bg-red-50 border border-red-100 rounded-2xl p-6 text-center">
        <svg class="w-10 h-10 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <p class="text-lg font-semibold text-red-700">فشل الاستيراد</p>
        @if($importResult->errors)
            <p class="text-sm text-red-600 mt-2">{{ $importResult->errors[0]['message'] ?? '' }}</p>
        @endif
    </div>
    @endif

</div>

@if($importResult->status === 'pending')
<script>
(function () {
    const chunkUrl  = '{{ route('members.import.chunk', $importResult->id) }}';
    const csrfToken = '{{ csrf_token() }}';
    const totalRows = {{ $importResult->total_rows ?: 1 }};

    let offset        = 0;
    let totalImported = 0;
    let totalErrors   = 0;
    let running       = false;

    const bar         = document.getElementById('progress-bar');
    const pct         = document.getElementById('progress-pct');
    const label       = document.getElementById('progress-label');
    const liveImported = document.getElementById('live-imported');
    const liveErrors   = document.getElementById('live-errors');
    const statusText   = document.getElementById('status-text');
    const statusSub    = document.getElementById('status-sub');

    function updateProgress(processed) {
        const p = Math.min(100, Math.round((processed / totalRows) * 100));
        bar.style.width   = p + '%';
        pct.textContent   = p + '%';
        label.textContent = processed + ' / ' + totalRows + ' صف';
        liveImported.textContent = totalImported;
        liveErrors.textContent   = totalErrors;
    }

    async function processChunk() {
        if (running) return;
        running = true;

        statusText.textContent = 'جارٍ معالجة الصفوف…';

        try {
            const res  = await fetch(chunkUrl, {
                method:  'POST',
                headers: {
                    'Content-Type':     'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN':     csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: 'offset=' + offset,
            });

            if (!res.ok) throw new Error('HTTP ' + res.status);

            const data = await res.json();

            totalImported += (data.imported || []).length;
            totalErrors   += (data.errors   || []).length;
            offset         = data.next_offset;

            updateProgress(data.processed_rows);

            if (data.done) {
                statusText.textContent = 'اكتمل الاستيراد!';
                statusSub.textContent  = 'جارٍ تحميل النتائج…';
                bar.classList.replace('bg-emerald-500', 'bg-emerald-600');
                setTimeout(() => window.location.reload(), 800);
            } else {
                running = false;
                processChunk();
            }
        } catch (err) {
            statusText.textContent = 'حدث خطأ: ' + err.message;
            statusSub.textContent  = 'انقر هنا لإعادة المحاولة';
            statusSub.classList.add('cursor-pointer', 'text-emerald-600', 'underline');
            statusSub.onclick = () => { running = false; processChunk(); };
        }
    }

    // Start immediately
    processChunk();
})();
</script>
@endif

@endsection
