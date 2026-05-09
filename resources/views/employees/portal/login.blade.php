<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الموظفين — مسالك النور</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#0f172a 100%)">

    {{-- Background blobs --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full opacity-15" style="background:radial-gradient(circle,#6366f1,transparent)"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full opacity-10" style="background:radial-gradient(circle,#06b6d4,transparent)"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full opacity-5" style="background:radial-gradient(circle,#a78bfa,transparent)"></div>
    </div>

    <div class="relative w-full max-w-sm">

        {{-- Logo / Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4 shadow-2xl"
                 style="background:linear-gradient(135deg,#6366f1,#4f46e5)">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">بوابة الموظفين</h1>
            <p class="text-sm mt-1.5" style="color:#94a3b8">أدخل كلمة السر للاطلاع على مستحقاتك</p>
        </div>

        {{-- Card --}}
        <div class="rounded-3xl p-8 shadow-2xl" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);backdrop-filter:blur(20px)">

            @if($errors->any())
            <div class="mb-5 flex items-center gap-3 rounded-2xl px-4 py-3" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3)">
                <svg class="w-5 h-5 shrink-0" style="color:#f87171" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-medium" style="color:#fca5a5">{{ $errors->first() }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route('employee-portal.authenticate') }}">
                @csrf
                <div class="mb-6">
                    <label class="block text-xs font-bold mb-2 tracking-wider uppercase" style="color:#94a3b8">كلمة السر</label>
                    <div class="relative">
                        <input type="password" name="pin" id="pin-input"
                               autocomplete="off" autocorrect="off"
                               placeholder="● ● ● ● ● ●"
                               autofocus
                               class="w-full rounded-2xl px-5 py-4 text-center text-2xl font-black tracking-[0.5em] focus:outline-none transition-all"
                               style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);color:#fff;caret-color:#6366f1"
                               onfocus="this.style.borderColor='rgba(99,102,241,0.6)';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.15)'"
                               onblur="this.style.borderColor='rgba(255,255,255,0.15)';this.style.boxShadow='none'">
                        <button type="button" onclick="togglePin()" class="absolute left-4 top-1/2 -translate-y-1/2" style="color:#64748b">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-4 rounded-2xl text-white font-black text-sm tracking-wide transition-all duration-200 hover:opacity-90 active:scale-[0.98]"
                        style="background:linear-gradient(135deg,#6366f1,#4f46e5);box-shadow:0 4px 24px rgba(99,102,241,0.4)">
                    دخول
                </button>
            </form>
        </div>

        <p class="text-center text-xs mt-6" style="color:#334155">
            للحصول على كلمة السر تواصل مع الإدارة
        </p>
    </div>

    <script>
    function togglePin() {
        const input = document.getElementById('pin-input');
        const icon  = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    }
    </script>
</body>
</html>
