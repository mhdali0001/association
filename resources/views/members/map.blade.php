@extends('layouts.app')

@section('title', 'خريطة الأعضاء — مسالك النور')
@section('max-width', 'max-w-full')

@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:text-emerald-700 transition-colors">الأعضاء</a>
    <span class="text-gray-300 mx-1">/</span>
    <span class="text-gray-700">الخريطة</span>
@endsection

@section('content')

{{-- ── Header ── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center shadow-md">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-lg font-black text-gray-900">خريطة الأعضاء</h1>
            <p class="text-xs text-gray-400">
                {{ $totalCount }} عضو
                @if($totalCount > 0)
                    <span class="text-gray-300 mx-1">·</span>
                    <span class="text-teal-600 font-medium">يظهرون على الخريطة</span>
                @else
                    <span class="text-gray-300 mx-1">·</span>
                    <span class="text-amber-500 font-medium">لا يوجد أعضاء بإحداثيات مسجّلة بعد</span>
                @endif
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2">
        {{-- Search box --}}
        <div class="relative">
            <input type="text" id="map-search" placeholder="بحث باسم أو ملف…"
                   class="border border-gray-200 rounded-xl px-4 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-400 w-52 pr-9">
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
            </svg>
        </div>
        {{-- Reset view --}}
        <button onclick="resetView()" title="إعادة تعيين الشاشة"
                class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-3 py-2 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            إعادة ضبط
        </button>
        <a href="{{ route('members.index') }}"
           class="flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-medium px-3 py-2 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            قائمة الأعضاء
        </a>
    </div>
</div>

{{-- ── Stats chips ── --}}
@if($totalCount > 0)
@php
    $byFinal = $members->groupBy(fn($m) => $m->finalStatus?->name ?? 'بدون حالة');
@endphp
<div class="flex flex-wrap gap-2 mb-4" id="legend">
    <button data-filter="all" onclick="filterByStatus('all')"
            class="legend-btn active-legend flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border-2 border-teal-500 bg-teal-50 text-teal-700 transition-all">
        <span class="w-2.5 h-2.5 rounded-full bg-teal-500 inline-block"></span>
        الكل ({{ $totalCount }})
    </button>
    @foreach($byFinal as $statusName => $group)
        @php $color = $group->first()->finalStatus?->color ?? '#6b7280'; @endphp
        <button data-filter="{{ $statusName }}" onclick="filterByStatus('{{ $statusName }}')"
                class="legend-btn flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full border-2 border-gray-200 bg-white text-gray-600 hover:border-gray-400 transition-all">
            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:{{ $color }}"></span>
            {{ $statusName }} ({{ $group->count() }})
        </button>
    @endforeach
</div>
@endif

{{-- ── Map ── --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<div class="rounded-2xl border border-gray-200 overflow-hidden shadow-sm" style="height: calc(100vh - 220px); min-height: 400px;">
    <div id="members-map" style="height:100%;width:100%;"></div>
</div>

@if($totalCount === 0)
<div class="mt-6 text-center py-12 bg-white rounded-2xl border border-dashed border-gray-200">
    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
    <p class="text-gray-400 text-sm font-medium">لا يوجد أعضاء بإحداثيات مسجّلة</p>
    <p class="text-gray-300 text-xs mt-1">افتح صفحة تعديل أي عضو لإضافة موقعه على الخريطة.</p>
    <a href="{{ route('members.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm text-teal-600 hover:text-teal-700 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        الذهاب لقائمة الأعضاء
    </a>
</div>
@endif

@php
$mapData = $members->map(function ($m) {
    return [
        'id'      => $m->id,
        'name'    => $m->full_name,
        'dossier' => $m->dossier_number,
        'gender'  => $m->gender,
        'lat'     => (float) $m->latitude,
        'lng'     => (float) $m->longitude,
        'vStatus' => $m->verificationStatus?->name,
        'fStatus' => $m->finalStatus?->name,
        'fColor'  => $m->finalStatus?->color ?? '#10b981',
        'url'     => route('members.show', $m->id),
        'editUrl' => route('members.edit', $m->id),
    ];
})->values();
@endphp

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const membersData = @json($mapData);

const defaultCenter = [33.5138, 36.2765];
const map = L.map('members-map').setView(defaultCenter, 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
}).addTo(map);

// Custom colored marker using divIcon
function makeIcon(color) {
    return L.divIcon({
        className: '',
        html: `<div style="
            width:14px;height:14px;border-radius:50%;
            background:${color};border:2.5px solid white;
            box-shadow:0 1px 4px rgba(0,0,0,0.35);
        "></div>`,
        iconSize: [14, 14],
        iconAnchor: [7, 7],
        popupAnchor: [0, -10],
    });
}

const markersLayer = L.layerGroup().addTo(map);
let allMarkers = [];

function buildMarkers(data) {
    markersLayer.clearLayers();
    allMarkers = [];

    data.forEach(m => {
        const marker = L.marker([m.lat, m.lng], { icon: makeIcon(m.fColor) });

        const popup = `
            <div style="min-width:180px;font-family:'Tajawal',sans-serif;direction:rtl;text-align:right;">
                <p style="font-size:14px;font-weight:900;color:#111827;margin:0 0 2px;">${m.name}</p>
                ${m.dossier ? `<p style="font-size:11px;color:#6b7280;margin:0 0 6px;">ملف: ${m.dossier}</p>` : ''}
                <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:8px;">
                    ${m.vStatus ? `<span style="font-size:10px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;border-radius:99px;padding:2px 8px;">${m.vStatus}</span>` : ''}
                    ${m.fStatus ? `<span style="font-size:10px;background:${m.fColor}20;color:${m.fColor};border:1px solid ${m.fColor}40;border-radius:99px;padding:2px 8px;">${m.fStatus}</span>` : ''}
                    ${m.gender  ? `<span style="font-size:10px;background:#f8fafc;color:#475569;border:1px solid #e2e8f0;border-radius:99px;padding:2px 8px;">${m.gender}</span>` : ''}
                </div>
                <div style="display:flex;gap:6px;">
                    <a href="${m.url}"     style="flex:1;text-align:center;font-size:11px;font-weight:700;background:#10b981;color:white;border-radius:8px;padding:5px 8px;text-decoration:none;">عرض</a>
                    <a href="${m.editUrl}" style="flex:1;text-align:center;font-size:11px;font-weight:700;background:#f0fdf4;color:#065f46;border:1px solid #bbf7d0;border-radius:8px;padding:5px 8px;text-decoration:none;">تعديل</a>
                </div>
            </div>`;

        marker.bindPopup(popup, { maxWidth: 240 });
        marker._memberData = m;
        markersLayer.addLayer(marker);
        allMarkers.push(marker);
    });

    if (allMarkers.length > 0) {
        const group = L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.15));
    }
}

buildMarkers(membersData);

// Reset view
window.resetView = function () {
    if (allMarkers.length > 0) {
        const group = L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.15));
    } else {
        map.setView(defaultCenter, 8);
    }
};

// Filter by final status
let currentFilter = 'all';
window.filterByStatus = function (status) {
    currentFilter = status;

    document.querySelectorAll('.legend-btn').forEach(btn => {
        const isActive = btn.dataset.filter === status;
        btn.classList.toggle('active-legend', isActive);
        btn.classList.toggle('border-teal-500', isActive);
        btn.classList.toggle('bg-teal-50', isActive);
        btn.classList.toggle('text-teal-700', isActive);
        btn.classList.toggle('border-gray-200', !isActive);
        btn.classList.toggle('bg-white', !isActive);
        btn.classList.toggle('text-gray-600', !isActive);
    });

    const filtered = status === 'all'
        ? membersData
        : membersData.filter(m => (m.fStatus ?? 'بدون حالة') === status);

    buildMarkers(filtered);
};

// Search
const searchInput = document.getElementById('map-search');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        const base = currentFilter === 'all'
            ? membersData
            : membersData.filter(m => (m.fStatus ?? 'بدون حالة') === currentFilter);

        const filtered = q
            ? base.filter(m =>
                m.name.toLowerCase().includes(q) ||
                (m.dossier && m.dossier.toLowerCase().includes(q))
              )
            : base;

        buildMarkers(filtered);
    });
}
</script>
@endpush

@endsection
