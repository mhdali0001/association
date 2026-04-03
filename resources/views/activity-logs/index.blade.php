@extends('layouts.app')

@section('title', 'سجل النشاط — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">سجل النشاط</span>
@endsection

@section('content')

{{-- Header --}}
<div class="relative bg-gradient-to-l from-indigo-600 via-indigo-500 to-violet-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-8 left-16 w-48 h-48 bg-white rounded-full"></div>
        <div class="absolute top-4 right-12 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white">سجل النشاط</h1>
            <p class="text-indigo-200 text-sm mt-0.5">
                إجمالي السجلات:
                <span class="font-black text-white">{{ number_format($logs->total()) }}</span>
            </p>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-white/30">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('activity-logs.index') }}"
      class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 mb-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
        <div class="lg:col-span-2 relative">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
            </span>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="بحث في الوصف أو اسم المستفيد..."
                   class="w-full pr-10 pl-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50
                          focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
        </div>
        <div>
            <select name="action"
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                           focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
                <option value="">— كل الإجراءات —</option>
                <option value="login"   {{ $action === 'login'   ? 'selected' : '' }}>تسجيل دخول</option>
                <option value="logout"  {{ $action === 'logout'  ? 'selected' : '' }}>تسجيل خروج</option>
                <option value="created" {{ $action === 'created' ? 'selected' : '' }}>إضافة</option>
                <option value="updated" {{ $action === 'updated' ? 'selected' : '' }}>تعديل</option>
                <option value="deleted" {{ $action === 'deleted' ? 'selected' : '' }}>حذف</option>
                <option value="viewed"  {{ $action === 'viewed'  ? 'selected' : '' }}>عرض</option>
            </select>
        </div>
        <div>
            <select name="user_id"
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                           focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
                <option value="">— كل المستخدمين —</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="date" name="date" value="{{ $date ?? '' }}"
               class="text-sm border border-gray-200 rounded-xl px-3 py-2.5 bg-gray-50
                      focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            تصفية
        </button>
        @if($action || $userId || $search || $date)
            <a href="{{ route('activity-logs.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-5 py-2.5 rounded-xl transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                مسح
            </a>
        @endif
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Section header --}}
    <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100 bg-gradient-to-l from-gray-50 to-white">
        <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
        </div>
        <span class="text-sm font-bold text-gray-700">السجلات</span>
    </div>

    @if($logs->isEmpty())
        <div class="text-center py-20">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-gray-400 text-sm font-medium">لا توجد سجلات مطابقة</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100">
                        <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">الإجراء</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">الوصف</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">المستخدم</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">العنصر المرتبط</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">IP</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-gray-400 text-xs">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($logs as $log)
                    @php
                        $color = $log->actionColor();
                        $badgeMap = [
                            'emerald' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                            'gray'    => 'bg-gray-100 text-gray-600 border border-gray-200',
                            'blue'    => 'bg-blue-50 text-blue-700 border border-blue-200',
                            'yellow'  => 'bg-amber-50 text-amber-700 border border-amber-200',
                            'red'     => 'bg-red-50 text-red-700 border border-red-200',
                            'purple'  => 'bg-purple-50 text-purple-700 border border-purple-200',
                        ];
                        $rowMap = [
                            'emerald' => 'hover:bg-emerald-50/30',
                            'gray'    => 'hover:bg-gray-50/50',
                            'blue'    => 'hover:bg-blue-50/30',
                            'yellow'  => 'hover:bg-amber-50/30',
                            'red'     => 'hover:bg-red-50/30',
                            'purple'  => 'hover:bg-purple-50/30',
                        ];
                        $badge = $badgeMap[$color] ?? 'bg-gray-100 text-gray-600';
                        $rowHover = $rowMap[$color] ?? 'hover:bg-gray-50/50';
                    @endphp
                    <tr class="transition-colors {{ $rowHover }}">
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs font-bold {{ $badge }}">
                                @if($log->action === 'login')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                                @elseif($log->action === 'logout')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                                @elseif($log->action === 'created')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                @elseif($log->action === 'updated')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                @elseif($log->action === 'deleted')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                @else
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                @endif
                                {{ $log->actionLabel() }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-700 max-w-xs">
                            <p class="truncate text-sm">{{ $log->description }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($log->user)
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center shrink-0 shadow-sm">
                                        <span class="text-white font-black text-xs">{{ mb_substr($log->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">{{ $log->user->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $log->user->email }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($log->subject_label)
                                <p class="text-xs font-semibold text-gray-700">{{ $log->subject_label }}</p>
                                @if($log->subject_type)
                                    <p class="text-xs text-gray-400">
                                        {{ $log->subject_type }}
                                        @if($log->subject_id) <span class="font-mono">#{{ $log->subject_id }}</span> @endif
                                    </p>
                                @endif
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-400 font-mono text-xs">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-xs font-semibold text-gray-700">{{ $log->created_at->format('Y/m/d') }}</p>
                            <p class="text-xs text-gray-400">{{ $log->created_at->format('H:i') }}</p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
