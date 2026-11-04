@extends('layouts.app')

@section('title', 'النسخ الاحتياطي — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">النسخ الاحتياطي</span>
@endsection

@section('content')

@php
    $size = fn (float $bytes) => $bytes >= 1048576 ? number_format($bytes / 1048576, 1) . ' MB' : number_format($bytes / 1024) . ' KB';
@endphp

{{-- Header --}}
<div class="mb-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 flex items-center gap-2.5">
        <span class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
        </span>
        النسخ الاحتياطي
    </h1>
    <p class="text-sm text-gray-400 mt-1 mr-10 sm:mr-12">
        نسخة من قاعدة البيانات تُرسل إلى Google Drive تلقائياً كل أسبوع (الجمعة الساعة 2 صباحاً)، ويمكنك إرسال نسخة الآن.
    </p>
</div>

{{-- Send now --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
    <form method="POST" action="{{ route('backups.store') }}" id="backup-form">
        @csrf
        <button type="submit" id="backup-btn"
                class="w-full sm:w-auto flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-6 py-3 rounded-xl transition-colors">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span id="backup-btn-text">إرسال نسخة احتياطية الآن إلى Google Drive</span>
        </button>
    </form>
    <p class="text-xs text-gray-400 mt-3">
        قاعدة البيانات فقط، في أرشيف محمي بكلمة السر نفسها للنسخة التلقائية. قد يستغرق الإرسال دقيقة — لا تضغط الزر مرتين.
    </p>
</div>

{{-- Latest backups on Drive --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-800">آخر النسخ على Google Drive</h2>
    </div>
    @if($error)
        <div class="p-5 text-sm text-red-600">تعذّر الاتصال بـ Google Drive: {{ $error }}</div>
    @elseif($backups->isEmpty())
        <div class="p-5 text-sm text-gray-400">لا توجد نسخ على Google Drive بعد.</div>
    @else
        <div class="divide-y divide-gray-50">
            @foreach($backups as $backup)
                <div class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-800">{{ $backup->date()->timezone(config('app.timezone'))->format('Y/m/d — H:i') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $backup->date()->diffForHumans() }}</p>
                    </div>
                    <span class="text-xs text-gray-500 font-mono shrink-0">{{ $size($backup->sizeInBytes()) }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.getElementById('backup-form').addEventListener('submit', function () {
    var btn = document.getElementById('backup-btn');
    btn.disabled = true;
    btn.classList.add('opacity-60');
    document.getElementById('backup-btn-text').textContent = 'جارٍ إرسال النسخة… لا تغلق الصفحة';
});
</script>
@endpush
