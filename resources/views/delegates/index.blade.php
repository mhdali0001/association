@extends('layouts.app')

@section('title', 'المندوبون — مسالك النور')

@section('breadcrumb')
    <span class="text-gray-700">المندوبون</span>
@endsection

@section('content')

@if(session('success'))
    <div class="mb-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-2xl px-5 py-3.5">
        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm font-medium rounded-2xl px-5 py-3.5">
        <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        {{ $errors->first() }}
    </div>
@endif

{{-- Hero --}}
<div class="relative bg-gradient-to-l from-sky-600 via-blue-500 to-indigo-500 rounded-3xl p-6 mb-6 overflow-hidden shadow-lg">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-6 -left-6 w-36 h-36 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 left-20 w-52 h-52 bg-white rounded-full"></div>
        <div class="absolute top-4 right-10 w-20 h-20 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-black text-white">المندوبون</h1>
            <p class="text-sky-100 text-sm mt-0.5">قائمة المندوبين وعدد الأعضاء عند كل مندوب</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="bg-white/15 border border-white/30 rounded-2xl px-4 py-2 text-center">
                <p class="text-2xl font-black text-white">{{ number_format($totalDelegates) }}</p>
                <p class="text-sky-100 text-xs">مندوب</p>
            </div>
            <div class="bg-white/15 border border-white/30 rounded-2xl px-4 py-2 text-center">
                <p class="text-2xl font-black text-white">{{ number_format($totalMembers) }}</p>
                <p class="text-sky-100 text-xs">عضو مرتبط</p>
            </div>
            @if(auth()->user()?->role === 'admin')
            <button type="button" onclick="document.getElementById('modal-add').classList.remove('hidden')"
                    class="flex items-center gap-2 bg-white text-sky-700 hover:bg-sky-50 text-sm font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة مندوب
            </button>
            @endif
        </div>
    </div>
</div>

{{-- Search --}}
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm mb-5 p-4">
    <form method="GET" action="{{ route('delegates.index') }}">
        <div class="relative">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
            </span>
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="بحث باسم المندوب..."
                   class="w-full pr-10 pl-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white transition placeholder-gray-300">
        </div>
    </form>
</div>

{{-- List --}}
@if($delegates->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-gray-400 font-semibold mb-1">لا يوجد مندوبون</p>
        @if(auth()->user()?->role === 'admin')
            <button type="button" onclick="document.getElementById('modal-add').classList.remove('hidden')"
                    class="mt-3 text-sm text-sky-600 hover:text-sky-700 font-semibold underline">
                أضف أول مندوب
            </button>
        @endif
    </div>
@else
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 px-5 py-3.5 border-b border-gray-100">
            <div class="w-7 h-7 rounded-lg bg-sky-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4.13a4 4 0 11-8 0 4 4 0 018 0zm6-4a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-700">{{ $totalDelegates }} مندوب — {{ $totalMembers }} عضو</span>
        </div>

        @php $maxCount = $delegates->max('members_count') ?: 1; @endphp

        <div class="divide-y divide-gray-50">
            @foreach($delegates as $index => $delegate)
            <div class="flex items-center gap-3 sm:gap-4 px-4 sm:px-5 py-3.5 hover:bg-sky-50/40 transition-colors group">

                {{-- Rank --}}
                <span class="w-6 text-center text-xs font-bold text-gray-300 shrink-0">{{ $index + 1 }}</span>

                {{-- Avatar --}}
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center shadow-sm shrink-0">
                    <span class="text-white font-black text-sm sm:text-base">{{ mb_substr($delegate->name, 0, 1) }}</span>
                </div>

                {{-- Name + bar --}}
                <a href="{{ route('delegates.show', $delegate->name) }}" class="flex-1 min-w-0">
                    <p class="font-bold text-gray-800 text-sm group-hover:text-sky-700 transition-colors truncate">{{ $delegate->name }}</p>
                    <div class="mt-1.5 h-1.5 bg-gray-100 rounded-full overflow-hidden w-full max-w-xs">
                        @php $pct = round(($delegate->members_count / $maxCount) * 100); @endphp
                        <div class="h-full bg-gradient-to-r from-sky-400 to-blue-500 rounded-full transition-all duration-500"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </a>

                {{-- Count badge --}}
                <div class="text-right shrink-0">
                    @if($delegate->members_count > 0)
                        <span class="text-xl sm:text-2xl font-black text-sky-600">{{ number_format($delegate->members_count) }}</span>
                        <span class="text-xs text-gray-400 mr-0.5">عضو</span>
                    @else
                        <span class="text-xs text-gray-300 italic">بدون أعضاء</span>
                    @endif
                </div>

                {{-- Actions (admin only) --}}
                @if(auth()->user()?->role === 'admin')
                <div class="flex items-center gap-1 shrink-0">
                    <button type="button"
                            onclick="openRename({{ json_encode($delegate->name) }}, '{{ route('delegates.rename', $delegate->name) }}')"
                            class="w-8 h-8 rounded-lg text-gray-400 hover:text-sky-600 hover:bg-sky-50 flex items-center justify-center transition-colors"
                            title="تعديل الاسم">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <form method="POST" action="{{ route('delegates.destroy', $delegate->name) }}"
                          onsubmit="return confirm('هل أنت متأكد من حذف المندوب «{{ addslashes($delegate->name) }}»؟ سيتم إزالته من {{ $delegate->members_count }} عضو.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-8 h-8 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition-colors"
                                title="حذف المندوب">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
@endif

@if(auth()->user()?->role === 'admin')

{{-- Add Delegate Modal --}}
<div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)"
     onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">إضافة مندوب جديد</h2>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('delegates.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">اسم المندوب <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="add-name" required autofocus
                       value="{{ old('name') }}"
                       placeholder="أدخل اسم المندوب..."
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none bg-gray-50 focus:bg-white transition">
            </div>
            <div class="flex gap-3 pt-1 border-t border-gray-100">
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    إضافة
                </button>
                <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Rename Modal --}}
<div id="modal-rename" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.5)"
     onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">تعديل اسم المندوب</h2>
            <button onclick="document.getElementById('modal-rename').classList.add('hidden')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="rename-form" method="POST" class="p-6 space-y-4">
            @csrf @method('PATCH')
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">الاسم الحالي</label>
                <p id="rename-current" class="text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5"></p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">الاسم الجديد <span class="text-red-500">*</span></label>
                <input type="text" name="new_name" id="rename-new-name" required
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none bg-gray-50 focus:bg-white transition">
            </div>
            <div class="flex gap-3 pt-1 border-t border-gray-100">
                <button type="submit"
                        class="flex-1 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold py-2.5 rounded-xl transition-colors">
                    حفظ
                </button>
                <button type="button" onclick="document.getElementById('modal-rename').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRename(currentName, actionUrl) {
    document.getElementById('rename-current').textContent = currentName;
    document.getElementById('rename-new-name').value = currentName;
    document.getElementById('rename-form').action = actionUrl;
    document.getElementById('modal-rename').classList.remove('hidden');
    setTimeout(() => document.getElementById('rename-new-name').select(), 50);
}

@if($errors->any())
// Re-open add modal if there were validation errors
document.getElementById('modal-add').classList.remove('hidden');
@endif
</script>

@endif

@endsection
