<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إضافة مستخدم — مسالك النور</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { font-family: 'Tajawal', sans-serif; box-sizing: border-box; }

        body {
            margin: 0; min-height: 100vh;
            display: flex;
            background: #f1f5f9;
        }

        /* ── LEFT PANEL ── */
        .panel-left {
            display: none;
            width: 46%;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            position: relative;
            overflow: hidden;
            background: #052e16;
        }
        @media (min-width: 1024px) { .panel-left { display: flex; } }

        .panel-left::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 70% 55% at 110% 30%, rgba(34,197,94,0.20) 0%, transparent 60%),
                radial-gradient(ellipse 50% 70% at -10% 80%, rgba(5,150,105,0.18) 0%, transparent 55%);
        }
        .panel-left::after {
            content: '';
            position: absolute;
            top: -120px; left: -120px;
            width: 480px; height: 480px;
            border-radius: 50%;
            border: 70px solid rgba(255,255,255,0.04);
        }
        .panel-ring-2 {
            position: absolute;
            bottom: -80px; right: -80px;
            width: 300px; height: 300px;
            border-radius: 50%;
            border: 50px solid rgba(255,255,255,0.04);
        }

        .panel-content { position: relative; z-index: 10; }

        .brand-mark {
            width: 56px; height: 56px; border-radius: 16px;
            background: rgba(255,255,255,0.10);
            border: 1.5px solid rgba(255,255,255,0.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 900; color: #fff;
            backdrop-filter: blur(8px);
            margin-bottom: 20px;
        }
        .brand-name { font-size: 32px; font-weight: 900; color: #fff; line-height: 1.2; margin: 0 0 8px; }
        .brand-sub  { font-size: 14px; color: rgba(255,255,255,0.45); font-weight: 400; }

        .panel-divider {
            width: 40px; height: 3px; border-radius: 2px;
            background: linear-gradient(90deg, #4ade80, #86efac);
            margin: 28px 0;
        }
        .panel-tagline {
            font-size: 15px; color: rgba(255,255,255,0.60);
            line-height: 1.9; max-width: 280px;
        }

        .features-list { list-style: none; padding: 0; margin: 36px 0 0; }
        .features-list li {
            display: flex; align-items: center; gap: 12px;
            font-size: 13px; color: rgba(255,255,255,0.55);
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .features-list li:last-child { border-bottom: none; }
        .feat-dot {
            width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
            background: rgba(74,222,128,0.12);
            border: 1px solid rgba(74,222,128,0.20);
            display: flex; align-items: center; justify-content: center;
        }
        .feat-dot svg { width: 14px; height: 14px; color: #4ade80; }

        .panel-footer-text { font-size: 12px; color: rgba(255,255,255,0.25); }

        /* ── RIGHT PANEL ── */
        .panel-right {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            padding: 32px 24px;
        }

        .register-card {
            width: 100%; max-width: 460px;
            background: #fff;
            border-radius: 20px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 32px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-top-bar {
            height: 4px;
            background: linear-gradient(90deg, #166534, #16a34a, #4ade80);
        }

        .card-body { padding: 36px 36px 32px; }
        @media (max-width: 480px) { .card-body { padding: 28px 24px; } }

        .mobile-brand {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 28px;
        }
        @media (min-width: 1024px) { .mobile-brand { display: none; } }
        .mobile-mark {
            width: 40px; height: 40px; border-radius: 11px;
            background: #166534;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 900; color: #fff;
        }
        .mobile-name { font-size: 16px; font-weight: 800; color: #0f172a; }
        .mobile-sub  { font-size: 11px; color: #94a3b8; }

        .card-heading { margin: 0 0 24px; }
        .card-heading h1 { font-size: 22px; font-weight: 900; color: #0f172a; margin: 0 0 4px; }
        .card-heading p  { font-size: 13px; color: #94a3b8; margin: 0; }

        .alert-error {
            display: flex; align-items: flex-start; gap: 10px;
            background: #fef2f2; border: 1px solid #fecaca;
            border-radius: 12px; padding: 12px 14px;
            margin-bottom: 20px;
        }
        .alert-error svg { width: 16px; height: 16px; color: #dc2626; flex-shrink: 0; margin-top: 1px; }
        .alert-error ul  { margin: 0; padding: 0 16px 0 0; }
        .alert-error li  { font-size: 13px; color: #dc2626; font-weight: 500; }

        .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block; font-size: 13px; font-weight: 600;
            color: #374151; margin-bottom: 7px;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; top: 50%; transform: translateY(-50%);
            right: 13px; pointer-events: none;
            display: flex; align-items: center;
        }
        .input-icon svg { width: 16px; height: 16px; color: #94a3b8; }
        .input-toggle {
            position: absolute; top: 50%; transform: translateY(-50%);
            left: 13px; background: none; border: none; cursor: pointer;
            display: flex; align-items: center; padding: 0; color: #94a3b8;
        }
        .input-toggle:hover { color: #475569; }
        .input-toggle svg { width: 16px; height: 16px; }

        .form-input {
            width: 100%; padding: 11px 40px 11px 14px;
            font-size: 14px; font-family: 'Tajawal', sans-serif;
            color: #0f172a; background: #f8fafc;
            border: 1.5px solid #e2e8f0; border-radius: 11px;
            outline: none; transition: all .2s;
        }
        .form-input::placeholder { color: #cbd5e1; }
        .form-input:focus {
            background: #fff;
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
        }
        .form-input.has-toggle { padding-left: 40px; }
        .form-input.is-error   { border-color: #fca5a5; background: #fef2f2; }

        .form-select {
            width: 100%; padding: 11px 40px 11px 14px;
            font-size: 14px; font-family: 'Tajawal', sans-serif;
            color: #0f172a; background: #f8fafc;
            border: 1.5px solid #e2e8f0; border-radius: 11px;
            outline: none; transition: all .2s; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 12px center;
            background-size: 16px;
        }
        .form-select:focus {
            background-color: #fff;
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22,163,74,0.12);
        }
        .form-select.is-error { border-color: #fca5a5; background-color: #fef2f2; }

        .field-error { font-size: 12px; color: #dc2626; margin-top: 5px; display: block; }

        .btn-submit {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: linear-gradient(135deg, #15803d, #166534);
            color: #fff; font-size: 15px; font-weight: 700;
            font-family: 'Tajawal', sans-serif;
            padding: 13px 24px; border-radius: 12px;
            border: none; cursor: pointer;
            box-shadow: 0 4px 16px rgba(21,128,61,0.35);
            transition: all .25s;
            margin-top: 6px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #16a34a, #15803d);
            box-shadow: 0 6px 22px rgba(21,128,61,0.45);
            transform: translateY(-1px);
        }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit svg { width: 17px; height: 17px; }

        .card-footer {
            margin-top: 22px; padding-top: 18px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }
        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 13px; color: #94a3b8; text-decoration: none;
            transition: color .2s;
        }
        .back-link:hover { color: #16a34a; }
        .back-link svg { width: 14px; height: 14px; }
    </style>
</head>
<body>

    {{-- ══════════════════ LEFT: Branding ══════════════════ --}}
    <div class="panel-left">
        <div class="panel-ring-2"></div>

        <div class="panel-content">
            <div class="brand-mark">م</div>
            <h1 class="brand-name">مسالك النور</h1>
            <p class="brand-sub">جمعية خيرية معتمدة</p>
            <div class="panel-divider"></div>
            <p class="panel-tagline">
                إضافة مستخدم جديد للوحة التحكم،
                وتحديد صلاحياته للوصول إلى
                بيانات الجمعية وإدارتها.
            </p>

            <ul class="features-list">
                <li>
                    <div class="feat-dot">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    تحديد اسم المستخدم والبريد
                </li>
                <li>
                    <div class="feat-dot">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    ضبط صلاحيات الوصول
                </li>
                <li>
                    <div class="feat-dot">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    كلمة مرور آمنة ومشفرة
                </li>
            </ul>
        </div>

        <p class="panel-footer-text panel-content">
            &copy; {{ date('Y') }} جمعية مسالك النور — جميع الحقوق محفوظة
        </p>
    </div>

    {{-- ══════════════════ RIGHT: Form ══════════════════ --}}
    <div class="panel-right">
        <div class="register-card">
            <div class="card-top-bar"></div>
            <div class="card-body">

                {{-- Mobile brand --}}
                <div class="mobile-brand">
                    <div class="mobile-mark">م</div>
                    <div>
                        <p class="mobile-name">مسالك النور</p>
                        <p class="mobile-sub">جمعية خيرية</p>
                    </div>
                </div>

                <div class="card-heading">
                    <h1>إضافة مستخدم جديد</h1>
                    <p>أدخل بيانات المستخدم وحدّد صلاحياته</p>
                </div>

                @if ($errors->any())
                    <div class="alert-error">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="form-group">
                        <label class="form-label" for="name">الاسم الكامل</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <input
                                id="name" type="text" name="name"
                                value="{{ old('name') }}"
                                required autofocus autocomplete="name"
                                placeholder="محمد أحمد"
                                class="form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                            >
                        </div>
                        @error('name') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email + Phone --}}
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label" for="email">البريد الإلكتروني</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <input
                                    id="email" type="email" name="email"
                                    value="{{ old('email') }}"
                                    required autocomplete="email"
                                    placeholder="example@domain.com"
                                    class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                                >
                            </div>
                            @error('email') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">رقم الهاتف</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </span>
                                <input
                                    id="phone" type="text" name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="09xxxxxxxx"
                                    class="form-input {{ $errors->has('phone') ? 'is-error' : '' }}"
                                >
                            </div>
                            @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Role --}}
                    <div class="form-group">
                        <label class="form-label" for="role">الصلاحية</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </span>
                            <select id="role" name="role" class="form-select {{ $errors->has('role') ? 'is-error' : '' }}">
                                <option value="">— اختر الصلاحية —</option>
                                <option value="admin"  {{ old('role') === 'admin'  ? 'selected' : '' }}>مدير (Admin)</option>
                                <option value="user"   {{ old('role') === 'user'   ? 'selected' : '' }}>مستخدم (User)</option>
                            </select>
                        </div>
                        @error('role') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label class="form-label" for="password">كلمة المرور</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input
                                id="password" type="password" name="password"
                                required autocomplete="new-password"
                                placeholder="••••••••"
                                class="form-input has-toggle {{ $errors->has('password') ? 'is-error' : '' }}"
                            >
                            <button type="button" class="input-toggle" onclick="togglePassword('password','eye-open','eye-closed')">
                                <svg id="eye-open" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-closed" class="hidden" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">تأكيد كلمة المرور</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input
                                id="password_confirmation" type="password" name="password_confirmation"
                                required autocomplete="new-password"
                                placeholder="••••••••"
                                class="form-input has-toggle"
                            >
                            <button type="button" class="input-toggle" onclick="togglePassword('password_confirmation','eye-open-2','eye-closed-2')">
                                <svg id="eye-open-2" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eye-closed-2" class="hidden" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        إنشاء المستخدم
                    </button>
                </form>

                <div class="card-footer">
                    <a href="{{ route('users.index') }}" class="back-link">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        العودة إلى قائمة المستخدمين
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, eyeOpenId, eyeClosedId) {
            const input    = document.getElementById(inputId);
            const eyeOpen  = document.getElementById(eyeOpenId);
            const eyeClosed = document.getElementById(eyeClosedId);
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
