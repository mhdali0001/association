@extends('layouts.app')

@section('title', 'المستخدمون — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">المستخدمون</span>
@endsection

@section('content')

{{-- Header --}}
<div class="relative bg-gradient-to-l from-indigo-600 via-violet-500 to-purple-600 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-8 left-16 w-48 h-48 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-white">المستخدمون</h1>
            <p class="text-indigo-200 text-sm mt-0.5">إدارة مستخدمي لوحة التحكم</p>
        </div>
        <a href="{{ route('users.create') }}"
           class="flex items-center gap-2 bg-white text-indigo-700 hover:bg-indigo-50 text-sm font-bold px-5 py-2.5 rounded-xl transition-colors shadow-md">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            إضافة مستخدم
        </a>
    </div>
</div>

@if (session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5 mb-5">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    {{-- Table header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-l from-gray-50 to-white">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">قائمة المستخدمين</span>
        </div>
        <span class="text-xs text-gray-400 bg-gray-100 rounded-full px-3 py-1">{{ $users->count() }} مستخدم</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/70 border-b border-gray-100">
                    <th class="text-right font-semibold text-gray-400 text-xs px-5 py-3.5">#</th>
                    <th class="text-right font-semibold text-gray-400 text-xs px-5 py-3.5">المستخدم</th>
                    <th class="text-right font-semibold text-gray-400 text-xs px-5 py-3.5">البريد الإلكتروني</th>
                    <th class="text-right font-semibold text-gray-400 text-xs px-5 py-3.5">الهاتف</th>
                    <th class="text-right font-semibold text-gray-400 text-xs px-5 py-3.5">الصلاحية</th>
                    <th class="text-right font-semibold text-gray-400 text-xs px-5 py-3.5">تاريخ الإضافة</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($users as $user)
                <tr class="hover:bg-indigo-50/20 transition-colors group">
                    <td class="px-5 py-4 text-gray-300 font-mono text-xs">{{ $loop->iteration }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-400 to-violet-500 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-sm">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $user->name }}</p>
                                @if ($user->id === auth()->id())
                                    <span class="text-xs bg-emerald-100 text-emerald-600 px-1.5 py-0.5 rounded-full font-semibold">أنت</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-sm">{{ $user->email }}</td>
                    <td class="px-5 py-4 text-gray-500 text-sm">{{ $user->phone ?? '—' }}</td>
                    <td class="px-5 py-4">
                        @if ($user->role === 'admin')
                            <span class="inline-flex items-center gap-1.5 text-xs bg-gradient-to-l from-amber-50 to-yellow-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full font-bold shadow-sm">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.504 1.132a1 1 0 01.992 0l1.75 1a1 1 0 11-.992 1.736L10 3.152l-1.254.716a1 1 0 11-.992-1.736l1.75-1zM5.618 4.504a1 1 0 01-.372 1.364L5.016 6l.23.132a1 1 0 11-.992 1.736L4 7.723V8a1 1 0 01-2 0V6a.996.996 0 01.52-.878l1.734-.99a1 1 0 011.364.372zm8.764 0a1 1 0 011.364-.372l1.733.99A1.002 1.002 0 0118 6v2a1 1 0 11-2 0v-.277l-.254.145a1 1 0 11-.992-1.736l.23-.132-.23-.132a1 1 0 01-.372-1.364zm-7 4a1 1 0 011.364-.372L10 8.848l1.254-.716a1 1 0 11.992 1.736L11 10.58V12a1 1 0 11-2 0v-1.42l-1.246-.712a1 1 0 01-.372-1.364zM3 11a1 1 0 011 1v1.42l1.246.712a1 1 0 11-.992 1.736l-1.75-1A1 1 0 012 14v-2a1 1 0 011-1zm14 0a1 1 0 011 1v2a1 1 0 01-.504.868l-1.75 1a1 1 0 11-.992-1.736L16 13.42V12a1 1 0 011-1zm-9.618 5.504a1 1 0 011.364-.372l.254.145V16a1 1 0 112 0v.277l.254-.145a1 1 0 11.992 1.736l-1.735.992a.995.995 0 01-1.022 0l-1.735-.992a1 1 0 01-.372-1.364z" clip-rule="evenodd"/>
                                </svg>
                                مدير النظام
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full font-semibold">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                مستخدم
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-xs font-medium text-gray-600">{{ $user->created_at->format('Y/m/d') }}</p>
                        <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-5 py-4">
                        @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                              onsubmit="return confirm('هل أنت متأكد من حذف المستخدم {{ addslashes($user->name) }}؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-gray-400 hover:text-red-600 hover:bg-red-50 font-medium transition-colors px-2.5 py-1.5 rounded-lg">
                                حذف
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-400 text-sm font-medium">لا يوجد مستخدمون بعد</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
