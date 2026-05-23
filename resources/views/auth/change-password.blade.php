@extends('layouts.app')

@section('title', 'تغيير كلمة المرور')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Page hero --}}
    <div class="relative mb-8 rounded-2xl overflow-hidden" style="background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 100%);">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="relative flex items-center gap-5 px-7 py-6">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <svg id="lock-icon" class="w-7 h-7 text-white transition-all duration-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-white leading-snug">تغيير كلمة المرور</h1>
                <p class="text-sm text-emerald-200 mt-0.5">أمان حسابك يبدأ بكلمة مرور قوية</p>
            </div>
            <div class="hidden sm:flex flex-col items-end gap-1 shrink-0">
                <span class="text-xs text-emerald-200 font-medium">{{ Auth::user()->name }}</span>
                <span class="text-xs text-emerald-300/70">{{ Auth::user()->email }}</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('password.update') }}" id="pwd-form">
        @csrf

        <div class="grid gap-5">

            {{-- ── Step 1: Verify identity ─────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white shrink-0"
                          style="background: linear-gradient(135deg,#059669,#047857)">١</span>
                    <div>
                        <p class="text-sm font-bold text-gray-800">التحقق من هويتك</p>
                        <p class="text-xs text-gray-400 mt-px">أدخل كلمة مرورك الحالية للتأكد</p>
                    </div>
                </div>
                <div class="px-6 py-5">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">كلمة المرور الحالية <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password"
                               name="current_password"
                               id="current_password"
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full rounded-xl border pl-11 pr-10 py-3 text-sm transition-all duration-200 focus:outline-none focus:ring-2
                                      {{ $errors->has('current_password')
                                          ? 'border-red-300 bg-red-50/60 focus:ring-red-300 text-red-800'
                                          : 'border-gray-200 bg-gray-50/80 focus:ring-emerald-400/60 focus:border-emerald-400 focus:bg-white text-gray-800' }}">
                        <button type="button" onclick="togglePwd('current_password','eye-current')"
                                class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-300 hover:text-gray-600 transition-colors">
                            <svg id="eye-current" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="mt-2.5 flex items-center gap-2 px-3 py-2 rounded-lg bg-red-50 border border-red-100">
                            <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span class="text-xs text-red-600 font-medium">{{ $message }}</span>
                        </div>
                    @enderror
                </div>
            </div>

            {{-- ── Step 2: New password ─────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white shrink-0"
                          style="background: linear-gradient(135deg,#059669,#047857)">٢</span>
                    <div>
                        <p class="text-sm font-bold text-gray-800">كلمة المرور الجديدة</p>
                        <p class="text-xs text-gray-400 mt-px">اختر كلمة مرور قوية وفريدة</p>
                    </div>
                </div>

                <div class="px-6 py-5 space-y-5">

                    {{-- New password field --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">كلمة المرور الجديدة <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   oninput="onNewPwd(this.value)"
                                   class="w-full rounded-xl border pl-11 pr-10 py-3 text-sm transition-all duration-200 focus:outline-none focus:ring-2
                                          {{ $errors->has('password')
                                              ? 'border-red-300 bg-red-50/60 focus:ring-red-300 text-red-800'
                                              : 'border-gray-200 bg-gray-50/80 focus:ring-emerald-400/60 focus:border-emerald-400 focus:bg-white text-gray-800' }}">
                            <button type="button" onclick="togglePwd('password','eye-new')"
                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-300 hover:text-gray-600 transition-colors">
                                <svg id="eye-new" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Strength meter --}}
                        <div class="mt-3">
                            <div class="flex gap-1.5 mb-1.5">
                                <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden"><div id="bar-1" class="h-full rounded-full transition-all duration-300 w-0"></div></div>
                                <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden"><div id="bar-2" class="h-full rounded-full transition-all duration-300 w-0"></div></div>
                                <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden"><div id="bar-3" class="h-full rounded-full transition-all duration-300 w-0"></div></div>
                                <div class="h-1.5 flex-1 rounded-full bg-gray-100 overflow-hidden"><div id="bar-4" class="h-full rounded-full transition-all duration-300 w-0"></div></div>
                            </div>
                            <p id="strength-label" class="text-[11px] text-gray-400 h-4 transition-all duration-200"></p>
                        </div>

                        @error('password')
                            <div class="mt-2.5 flex items-center gap-2 px-3 py-2 rounded-lg bg-red-50 border border-red-100">
                                <svg class="w-3.5 h-3.5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span class="text-xs text-red-600 font-medium">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    {{-- Requirements checklist --}}
                    <div class="grid grid-cols-2 gap-2" id="requirements">
                        <div class="req-item flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-100 bg-gray-50/50 transition-all duration-200" data-rule="length">
                            <div class="req-dot w-4 h-4 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 transition-all duration-200">
                                <svg class="req-check w-2.5 h-2.5 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="req-text text-xs text-gray-500 font-medium">٨ أحرف على الأقل</span>
                        </div>
                        <div class="req-item flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-100 bg-gray-50/50 transition-all duration-200" data-rule="upper">
                            <div class="req-dot w-4 h-4 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 transition-all duration-200">
                                <svg class="req-check w-2.5 h-2.5 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="req-text text-xs text-gray-500 font-medium">حرف كبير (A-Z)</span>
                        </div>
                        <div class="req-item flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-100 bg-gray-50/50 transition-all duration-200" data-rule="number">
                            <div class="req-dot w-4 h-4 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 transition-all duration-200">
                                <svg class="req-check w-2.5 h-2.5 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="req-text text-xs text-gray-500 font-medium">رقم (0-9)</span>
                        </div>
                        <div class="req-item flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-100 bg-gray-50/50 transition-all duration-200" data-rule="special">
                            <div class="req-dot w-4 h-4 rounded-full border-2 border-gray-200 flex items-center justify-center shrink-0 transition-all duration-200">
                                <svg class="req-check w-2.5 h-2.5 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="req-text text-xs text-gray-500 font-medium">رمز خاص (!@#$)</span>
                        </div>
                    </div>

                    {{-- Confirm password --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">تأكيد كلمة المرور <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                <svg id="confirm-icon" class="w-4 h-4 text-gray-300 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   autocomplete="new-password"
                                   placeholder="••••••••"
                                   oninput="checkMatch()"
                                   class="w-full rounded-xl border pl-11 pr-10 py-3 text-sm transition-all duration-200 focus:outline-none focus:ring-2 border-gray-200 bg-gray-50/80 focus:ring-emerald-400/60 focus:border-emerald-400 focus:bg-white text-gray-800">
                            <button type="button" onclick="togglePwd('password_confirmation','eye-confirm')"
                                    class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-300 hover:text-gray-600 transition-colors">
                                <svg id="eye-confirm" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p id="match-msg" class="mt-1.5 text-xs h-4 transition-all duration-200"></p>
                    </div>

                </div>
            </div>

            {{-- ── Actions ──────────────────────────────────────────────────── --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors group">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    رجوع
                </a>
                <button type="submit" id="submit-btn"
                        class="inline-flex items-center gap-2.5 text-white text-sm font-bold px-7 py-3 rounded-xl shadow-lg transition-all duration-200 active:scale-95"
                        style="background: linear-gradient(135deg, #059669 0%, #047857 100%); box-shadow: 0 4px 15px rgba(5,150,105,0.35);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    حفظ كلمة المرور الجديدة
                </button>
            </div>

        </div>
    </form>

</div>

@push('scripts')
<script>
/* ── toggle show/hide ─────────────────────────────────────────────── */
function togglePwd(fieldId, iconId) {
    var input = document.getElementById(fieldId);
    var icon  = document.getElementById(iconId);
    var show  = input.type === 'password';
    input.type = show ? 'text' : 'password';
    icon.innerHTML = show
        ? '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>'
        : '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
}

/* ── strength + requirements ──────────────────────────────────────── */
var rules = {
    length:  function(v) { return v.length >= 8; },
    upper:   function(v) { return /[A-Z]/.test(v); },
    number:  function(v) { return /[0-9]/.test(v); },
    special: function(v) { return /[^A-Za-z0-9]/.test(v); }
};

var strengthMeta = [
    null,
    { label: 'ضعيفة جداً',  color: '#ef4444', bg: '#fef2f2' },
    { label: 'ضعيفة',       color: '#f97316', bg: '#fff7ed' },
    { label: 'متوسطة',      color: '#eab308', bg: '#fefce8' },
    { label: 'قوية',         color: '#10b981', bg: '#ecfdf5' }
];

function onNewPwd(val) {
    var score = Object.values(rules).filter(function(fn) { return fn(val); }).length;

    /* bars */
    for (var i = 1; i <= 4; i++) {
        var bar = document.getElementById('bar-' + i);
        if (i <= score && score > 0) {
            bar.style.width = '100%';
            bar.style.background = strengthMeta[score].color;
        } else {
            bar.style.width = '0';
        }
    }

    /* label */
    var lbl = document.getElementById('strength-label');
    if (val.length === 0) {
        lbl.textContent = '';
    } else {
        lbl.textContent = 'قوة كلمة المرور: ' + strengthMeta[score].label;
        lbl.style.color = strengthMeta[score].color;
    }

    /* requirement items */
    document.querySelectorAll('.req-item').forEach(function(item) {
        var rule   = item.getAttribute('data-rule');
        var passed = rules[rule](val);
        var dot    = item.querySelector('.req-dot');
        var check  = item.querySelector('.req-check');
        var text   = item.querySelector('.req-text');

        if (passed) {
            dot.style.background   = '#10b981';
            dot.style.borderColor  = '#10b981';
            check.style.opacity    = '1';
            item.style.background  = '#f0fdf4';
            item.style.borderColor = '#bbf7d0';
            text.style.color       = '#059669';
        } else {
            dot.style.background   = '';
            dot.style.borderColor  = '#e5e7eb';
            check.style.opacity    = '0';
            item.style.background  = '';
            item.style.borderColor = '';
            text.style.color       = '';
        }
    });

    /* lock icon in hero */
    var lock = document.getElementById('lock-icon');
    if (score >= 3) {
        lock.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>';
    } else {
        lock.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>';
    }

    checkMatch();
}

/* ── confirm match ────────────────────────────────────────────────── */
function checkMatch() {
    var pwd     = document.getElementById('password').value;
    var confirm = document.getElementById('password_confirmation').value;
    var msg     = document.getElementById('match-msg');
    var icon    = document.getElementById('confirm-icon');

    if (confirm.length === 0) { msg.textContent = ''; return; }

    if (pwd === confirm) {
        msg.textContent = '✓ كلمتا المرور متطابقتان';
        msg.style.color = '#10b981';
        icon.style.color = '#10b981';
    } else {
        msg.textContent = '✗ كلمتا المرور غير متطابقتين';
        msg.style.color = '#ef4444';
        icon.style.color = '#ef4444';
    }
}
</script>
@endpush
@endsection
