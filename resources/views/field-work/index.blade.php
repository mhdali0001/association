<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>العمل الميداني — مسالك النور</title>
    @include('partials.pwa')
    {{-- صفحة مستقلة بلا ملفات Vite: يحفظها التطبيق (sw.js) لتفتح بدون إنترنت. لا تحوي بيانات مستفيدين؛
         قائمة الزيارة تُنزَّل منها وتُخزَّن على الجهاز (localStorage) يوماً واحداً --}}
    <style>
        @font-face{font-family:'Tajawal';font-weight:400;font-display:swap;src:url('/fonts/tajawal-400.woff2') format('woff2')}
        @font-face{font-family:'Tajawal';font-weight:700;font-display:swap;src:url('/fonts/tajawal-700.woff2') format('woff2')}
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: #f1f5f9; color: #1e293b; font-size: 15px; }
        [hidden] { display: none !important; }
        .top {
            position: sticky; top: 0; z-index: 5; display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; padding-top: max(12px, env(safe-area-inset-top));
            background: #166534; color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,.12);
        }
        .top img { width: 32px; height: 32px; background: #fff; border-radius: 8px; padding: 2px; }
        .top h1 { font-size: 17px; margin: 0; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        @media (max-width: 340px) { .top img { display: none; } }
        .top a { color: #dcfce7; font-size: 13px; text-decoration: none; padding: 12px 2px; }
        .net { font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 99px; background: #dcfce7; color: #166534; }
        .net.off { background: #e2e8f0; color: #475569; }
        main { max-width: 720px; margin: 0 auto; padding: 14px 14px 40px; }
        .card { background: #fff; border-radius: 16px; padding: 16px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(15,23,42,.06); }
        h2 { font-size: 16px; margin: 0 0 10px; color: #166534; }
        .muted { color: #64748b; font-size: 13px; }
        .hint { color: #64748b; font-size: 13px; line-height: 1.7; margin: 10px 0 0; }
        .banner { border-radius: 12px; padding: 10px 14px; margin-bottom: 14px; font-size: 14px; line-height: 1.6; }
        .banner.warn { background: #fef3c7; color: #92400e; }
        .banner.info { background: #e0f2fe; color: #075985; }
        .banner a { color: inherit; font-weight: 700; }
        label.field { display: block; font-size: 12px; font-weight: 700; color: #64748b; margin: 10px 0 4px; }
        input[type=text], input[type=search], input[type=number], input[type=date], select, textarea {
            /* 16px: أقل منه يجعل آيفون يكبّر الصفحة عند الضغط على الحقل */
            width: 100%; font-family: inherit; font-size: 16px; color: #1e293b; background: #f8fafc;
            border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; min-height: 46px;
        }
        textarea { resize: vertical; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        @media (max-width: 420px) { .row { grid-template-columns: 1fr; gap: 0; } }
        .check { display: flex; align-items: center; gap: 10px; min-height: 44px; margin-top: 6px; font-size: 15px; cursor: pointer; }
        .check input { width: 22px; height: 22px; accent-color: #166534; flex-shrink: 0; }
        bdi { direction: ltr; unicode-bidi: isolate; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-family: inherit;
            font-size: 15px; font-weight: 700; border: none; border-radius: 12px; padding: 11px 18px; cursor: pointer;
            background: #166534; color: #fff; white-space: nowrap;
        }
        .btn:disabled { opacity: .5; cursor: default; }
        .btn.light { background: #f1f5f9; color: #334155; }
        .btn.block { width: 100%; margin-top: 14px; }
        .btn.small { font-size: 14px; padding: 10px 14px; border-radius: 10px; min-height: 44px; }
        .link { background: none; border: none; font-family: inherit; font-size: 14px; color: #b91c1c; cursor: pointer; padding: 12px 0; }
        details.card { padding: 0; }
        details.card > summary { list-style: none; cursor: pointer; padding: 16px; display: flex; align-items: center; gap: 10px; }
        details.card > summary::-webkit-details-marker { display: none; }
        details.card > summary .chev { margin-inline-start: auto; font-size: 13px; font-weight: 700; color: #166534; white-space: nowrap; }
        details.card[open] > summary .chev .closed, details.card:not([open]) > summary .chev .opened { display: none; }
        /* بلا قائمة محفوظة النموذج ضروري، فلا يُطوى */
        details.card:not(.has-package) > summary { pointer-events: none; }
        details.card:not(.has-package) > summary .chev { display: none; }
        details.card > .card-body { padding: 0 16px 16px; }
        details.card h2 { margin-bottom: 4px; }
        .sync { border: 2px solid #f59e0b; }
        .sync-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .queue { list-style: none; margin: 10px 0 0; padding: 0; }
        .queue li { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 0; border-top: 1px solid #f1f5f9; font-size: 14px; }
        .queue .err { color: #b91c1c; font-size: 13px; display: block; }
        .queue .q-meta { display: block; font-size: 13px; }
        .member { background: #fff; border-radius: 14px; padding: 12px 14px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(15,23,42,.06); }
        .m-head { display: flex; align-items: baseline; justify-content: space-between; gap: 8px; }
        .m-head strong { font-size: 16px; }
        .dossier { font-size: 12px; color: #64748b; white-space: nowrap; }
        .m-meta { font-size: 14px; color: #475569; margin-top: 4px; line-height: 1.6; }
        .calls { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .call {
            display: inline-flex; align-items: center; gap: 6px; min-height: 42px; padding: 8px 14px; border-radius: 10px;
            border: 1.5px solid #bbf7d0; background: #f0fdf4; color: #166534; font-weight: 700; font-size: 15px; text-decoration: none;
        }
        .call svg { width: 16px; height: 16px; flex-shrink: 0; }
        .m-foot { margin-top: 10px; }
        .m-foot .btn { width: 100%; }
        .badge { display: inline-block; font-size: 13px; font-weight: 700; padding: 4px 10px; border-radius: 99px; background: #fef3c7; color: #92400e; margin-top: 8px; }
        .list-tools { position: sticky; top: var(--top-h, 56px); z-index: 4; background: #f1f5f9; padding: 6px 0 10px; }
        .seg { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
        .seg label { text-align: center; padding: 9px; border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: 13px; font-weight: 700; cursor: pointer; }
        .seg input { display: none; }
        .seg input:checked + span { color: #fff; }
        .seg label:has(input[value=add]:checked) { background: #059669; border-color: #059669; color: #fff; }
        .seg label:has(input[value=subtract]:checked) { background: #dc2626; border-color: #dc2626; color: #fff; }
        /* نموذج الجولة: لوح من أسفل الشاشة على الموبايل، وزر الحفظ ظاهر دائماً */
        dialog {
            width: 100%; max-width: 560px; max-height: 92dvh; margin: auto auto 0; border: none; padding: 0;
            border-radius: 20px 20px 0 0; display: none; flex-direction: column;
        }
        dialog[open] { display: flex; }
        @media (min-width: 600px) { dialog { margin: auto; border-radius: 18px; } }
        dialog::backdrop { background: rgba(15,23,42,.5); }
        #visit-form { display: flex; flex-direction: column; min-height: 0; flex: 1; }
        .dlg-head { background: #166534; color: #fff; padding: 14px 16px; border-radius: inherit; border-bottom-left-radius: 0; border-bottom-right-radius: 0; }
        .dlg-head strong { display: block; font-size: 16px; line-height: 1.5; }
        .dlg-body { padding: 4px 16px 16px; overflow-y: auto; flex: 1; -webkit-overflow-scrolling: touch; }
        .dlg-actions {
            display: flex; gap: 8px; padding: 12px 16px; padding-bottom: max(12px, env(safe-area-inset-bottom));
            border-top: 1px solid #e2e8f0; background: #fff;
        }
        .dlg-actions .btn { flex: 1; min-height: 48px; }
        .dlg-actions .btn.light { flex: 0 0 34%; }
        #toast {
            position: fixed; bottom: max(20px, env(safe-area-inset-bottom)); left: 50%; transform: translateX(-50%);
            background: #1e293b; color: #fff; padding: 10px 18px; border-radius: 12px; font-size: 14px; z-index: 20;
            max-width: calc(100% - 32px); text-align: center; box-shadow: 0 8px 24px rgba(0,0,0,.2);
        }
        .empty { text-align: center; color: #64748b; padding: 24px 8px; font-size: 14px; }
    </style>
</head>
<body>
<header class="top">
    <img src="/logo/logo-128.png" alt="">
    <h1>العمل الميداني</h1>
    <span id="net" class="net">متصل</span>
    <a href="{{ route('dashboard') }}">النظام ←</a>
</header>

<main>
    <div id="login-needed" class="banner warn" hidden>
        انتهت جلسة الدخول. <a href="{{ route('login') }}">سجّل الدخول</a> ثم عد إلى هذه الصفحة لإرسال الجولات أو تنزيل قائمة جديدة.
    </div>
    <div id="offline-unsupported" class="banner warn" hidden>
        العمل بدون إنترنت يحتاج فتح النظام عبر رابط آمن (https). يمكنك استخدام الصفحة الآن، لكنها لن تفتح بدون اتصال.
    </div>
    <div id="notice" class="banner info" hidden></div>

    {{-- الجولات المحفوظة على الجهاز بانتظار الإرسال --}}
    <section id="sync" class="card sync" hidden>
        <div class="sync-head">
            <div>
                <strong id="sync-count"></strong>
                <div id="sync-text" class="muted"></div>
            </div>
            <button type="button" id="sync-now" class="btn small">إرسال الآن</button>
        </div>
        <ul id="queue" class="queue"></ul>
    </section>

    {{-- تنزيل قائمة الزيارة — يُطوى حين توجد قائمة فتظهر الأسماء أول الشاشة --}}
    <details class="card" id="download" open>
        <summary>
            <div>
                <h2>قائمة الزيارة على هذا الجهاز</h2>
                <div id="package-info" class="muted">لا توجد قائمة محفوظة بعد.</div>
            </div>
            <span class="chev"><span class="closed">تنزيل أخرى ▾</span><span class="opened">إخفاء ▴</span></span>
        </summary>
        <div class="card-body">
        <form id="download-form">
            <div class="row">
                <div>
                    <label class="field" for="sector_id">القطاع</label>
                    <select name="sector_id" id="sector_id">
                        <option value="">كل القطاعات</option>
                        @foreach($sectors as $sector)
                            <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field" for="region_id">المنطقة</label>
                    <select name="region_id" id="region_id">
                        <option value="">كل المناطق</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" data-sector="{{ $region->sector_id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <label class="field" for="search">بحث (اختياري)</label>
            <input type="search" name="search" id="search" placeholder="اسم، رقم ملف، أو هاتف">
            <label class="check"><input type="checkbox" name="not_visited" value="1"> من لم تُسجَّل لهم جولة فقط</label>
            <button type="submit" id="download-btn" class="btn block">تنزيل القائمة إلى الجهاز</button>
        </form>
        <p class="hint">
            تحتوي القائمة الاسم ورقم الملف والهاتف والعنوان وآخر جولة فقط — بلا أرقام هوية أو آيبانات.
            تبقى على هذا الجهاز يوماً واحداً وتُحذف عند تسجيل الخروج. افتح هذه الصفحة مرة واحدة مع إنترنت لتعمل بعدها بدونه.
        </p>
        <button type="button" id="clear-package" class="link" hidden>حذف القائمة من الجهاز</button>
        </div>
    </details>

    <section id="list" hidden>
        <div class="list-tools">
            <input type="search" id="filter" placeholder="ابحث: اسم، ملف، هاتف…">
        </div>
        <div id="members"></div>
    </section>
</main>

<dialog id="visit-dialog">
    <form id="visit-form" method="dialog">
        <div class="dlg-head">
            <span class="muted" style="color:#bbf7d0">تسجيل جولة ميدانية</span>
            <strong id="visit-member"></strong>
        </div>
        <div class="dlg-body">
            <div class="row">
                <div>
                    <label class="field" for="field_visit_status_id">الحالة</label>
                    <select name="field_visit_status_id" id="field_visit_status_id"></select>
                </div>
                <div>
                    <label class="field" for="house_type_id">نوع البيت</label>
                    <select name="house_type_id" id="house_type_id"></select>
                </div>
            </div>
            <div class="row">
                <div>
                    <label class="field" for="visit_date">تاريخ الزيارة</label>
                    <input type="date" name="visit_date" id="visit_date" required>
                </div>
                <div>
                    <label class="field" for="visitor">اسم الزائر</label>
                    <input type="text" name="visitor" id="visitor" placeholder="الاسم الكامل للزائر">
                </div>
            </div>
            <label class="field">المبلغ (ل.س)</label>
            <div class="seg">
                <label><input type="radio" name="amount_operation" value="add" checked><span>+ إضافة</span></label>
                <label><input type="radio" name="amount_operation" value="subtract"><span>− إنقاص</span></label>
            </div>
            <input type="number" name="estimated_amount" min="0" step="0.01" placeholder="0" inputmode="decimal" style="margin-top:8px">
            <label class="field" for="amount_reason">سبب المبلغ</label>
            <input type="text" name="amount_reason" id="amount_reason" placeholder="وصف موجز لسبب تحديد هذا المبلغ">
            <label class="field" for="notes">ملاحظات</label>
            <textarea name="notes" id="notes" rows="2" placeholder="ملاحظات الجولة..."></textarea>
            <label class="field" for="house_condition_id">حالة البيت</label>
            <select name="house_condition_id" id="house_condition_id"></select>
            <div>
                <label class="check"><input type="checkbox" name="has_video" value="1"> يوجد فيديو</label>
            </div>
            <div>
                <label class="check"><input type="checkbox" name="has_special_case" value="1"> حالة خاصة</label>
            </div>
        </div>
        <div class="dlg-actions">
            <button type="submit" value="save" class="btn">حفظ الجولة</button>
            <button type="submit" value="cancel" formnovalidate class="btn light">إلغاء</button>
        </div>
    </form>
</dialog>

<div id="toast" hidden></div>

<script>
(function () {
    var PACKAGE_KEY = 'fieldwork.package';
    var QUEUE_KEY   = 'fieldwork.queue';
    var VISITOR_KEY = 'fieldwork.visitor';
    var URLS = {
        session: @json(route('field-work.session', [], false)),
        package: @json(route('field-work.package', [], false)),
        visits:  @json(route('field-work.visits.store', [], false))
    };

    var $ = function (id) { return document.getElementById(id); };
    var syncing = false;
    var currentMember = null;

    // ── التخزين على الجهاز ───────────────────────────────────────────────
    function read(key, fallback) {
        try { var v = localStorage.getItem(key); return v ? JSON.parse(v) : fallback; } catch (e) { return fallback; }
    }
    function write(key, value) {
        try { localStorage.setItem(key, JSON.stringify(value)); return true; } catch (e) { return false; }
    }
    function remove(key) { try { localStorage.removeItem(key); } catch (e) {} }

    function loadPackage() {
        var pkg = read(PACKAGE_KEY, null);
        if (pkg && Date.now() > pkg.expires_at) {
            remove(PACKAGE_KEY);
            notice('انتهت صلاحية قائمة الزيارة المحفوظة (يوم واحد) وحُذفت من الجهاز. نزّلها من جديد عند توفر الإنترنت.');
            return null;
        }
        return pkg;
    }
    function queue() { return read(QUEUE_KEY, []); }
    function updateQueue(fn) {
        var items = fn(queue());
        if (!write(QUEUE_KEY, items)) alert('تعذّر الحفظ على الجهاز — قد تكون مساحة التخزين ممتلئة.');
        return items;
    }

    // ── أدوات ────────────────────────────────────────────────────────────
    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function norm(s) {
        return String(s || '').toLowerCase().replace(/[أإآٱ]/g, 'ا').replace(/ة/g, 'ه').replace(/ى/g, 'ي').replace(/[ً-ْـ]/g, '');
    }
    function today() {
        var d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    function uuid() {
        if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
        var b = crypto.getRandomValues(new Uint8Array(16));
        b[6] = (b[6] & 0x0f) | 0x40;
        b[8] = (b[8] & 0x3f) | 0x80;
        var h = Array.prototype.map.call(b, function (x) { return x.toString(16).padStart(2, '0'); }).join('');
        return h.slice(0, 8) + '-' + h.slice(8, 12) + '-' + h.slice(12, 16) + '-' + h.slice(16, 20) + '-' + h.slice(20);
    }
    // أرقام لاتينية كباقي النظام
    function when(ms) {
        return new Date(ms).toLocaleString('ar-SY-u-nu-latn', { weekday: 'long', hour: 'numeric', minute: '2-digit' });
    }
    // التاريخ والأرقام داخل نص عربي تنقلب بدون عزل اتجاهها (2026-09-12 تظهر 12-09-2026)
    function ltr(s) { return '<bdi>' + esc(s) + '</bdi>'; }
    var PHONE_ICON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>';
    var toastTimer;
    function toast(text) {
        var el = $('toast');
        el.textContent = text;
        el.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { el.hidden = true; }, 3500);
    }
    function notice(text) {
        var el = $('notice');
        el.textContent = text;
        el.hidden = !text;
    }
    function request(url, options) {
        var ctrl = new AbortController();
        var timer = setTimeout(function () { ctrl.abort(); }, 20000);
        var opts = Object.assign({ credentials: 'same-origin', signal: ctrl.signal }, options || {});
        opts.headers = Object.assign({ 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, opts.headers || {});
        return fetch(url, opts).finally(function () { clearTimeout(timer); });
    }

    // ── الجلسة: المستخدم الحالي ورمز حماية جديد ──────────────────────────
    function getSession() {
        return request(URLS.session).then(function (res) {
            if (res.status === 401 || res.redirected) { $('login-needed').hidden = false; return null; }
            if (!res.ok) throw new Error(res.status);
            $('login-needed').hidden = true;
            return res.json();
        });
    }

    // ── العرض ────────────────────────────────────────────────────────────
    function renderNet() {
        var on = navigator.onLine;
        $('net').textContent = on ? 'متصل' : 'بدون إنترنت';
        $('net').classList.toggle('off', !on);
        $('download-btn').disabled = !on;
        $('download-btn').textContent = on ? 'تنزيل القائمة إلى الجهاز' : 'التنزيل يحتاج اتصالاً بالإنترنت';
    }

    function renderQueue() {
        var items = queue();
        $('sync').hidden = items.length === 0;
        if (!items.length) return;
        var failed = items.filter(function (i) { return i.error; }).length;
        $('sync-count').textContent = items.length + ' جولة محفوظة على الجهاز';
        $('sync-text').textContent = syncing ? 'جارٍ الإرسال…'
            : failed ? failed + ' منها تحتاج مراجعة'
            : (navigator.onLine ? 'تُرسل تلقائياً' : 'ستُرسل تلقائياً عند عودة الإنترنت');
        $('sync-now').disabled = syncing || !navigator.onLine;
        $('queue').innerHTML = items.map(function (i) {
            return '<li><div><strong>' + esc(i.member_name) + '</strong><span class="muted q-meta">ملف ' + ltr(i.dossier || '—') + ' · ' + ltr(i.data.visit_date) + '</span>'
                + (i.error ? '<span class="err">' + esc(i.error) + '</span>' : '') + '</div>'
                + '<button type="button" class="btn light small" data-delete="' + esc(i.client_id) + '">حذف</button></li>';
        }).join('');
    }

    var hadPackage = null;
    function renderPackage() {
        var pkg = loadPackage();
        $('list').hidden = !pkg;
        $('clear-package').hidden = !pkg;
        // نموذج التنزيل مفتوح حين لا توجد قائمة، ويُطوى بعد التنزيل فتظهر الأسماء أول الشاشة
        if (!!pkg !== hadPackage) { $('download').open = !pkg; hadPackage = !!pkg; }
        $('download').classList.toggle('has-package', !!pkg);
        if (!pkg) {
            $('package-info').textContent = 'لا توجد قائمة محفوظة بعد.';
            $('members').innerHTML = '';
            return;
        }
        $('package-info').innerHTML = '<strong>' + pkg.members.length + ' مستفيد</strong> · تُحذف ' + esc(when(pkg.expires_at))
            + (pkg.total > pkg.members.length ? '<br>من أصل ' + pkg.total + ' — ضيّق البحث لتنزيل الباقي' : '');
        renderMembers();
    }

    function renderMembers() {
        var pkg = loadPackage();
        if (!pkg) return;
        var q = norm($('filter').value.trim());
        var queued = {};
        queue().forEach(function (i) { queued[i.member_id] = true; });
        var list = pkg.members.filter(function (m) {
            return !q || norm([m.name, m.dossier, m.phone, m.phone2, m.region, m.address].join(' ')).indexOf(q) !== -1;
        });
        if (!list.length) { $('members').innerHTML = '<div class="empty">لا توجد نتائج.</div>'; return; }
        $('members').innerHTML = list.map(function (m) {
            var phones = [m.phone, m.phone2].filter(Boolean).map(function (p) {
                return '<a class="call" href="tel:' + esc(String(p).replace(/[^\d+]/g, '')) + '">' + PHONE_ICON + ltr(p) + '</a>';
            }).join('');
            var last = m.last_visit
                ? 'آخر جولة: ' + ltr(m.last_visit) + (m.last_status ? ' — ' + esc(m.last_status) : '') + ' (' + m.visits + ' جولات)'
                : 'لا توجد جولات سابقة';
            return '<div class="member">'
                + '<div class="m-head"><strong>' + esc(m.name) + '</strong><span class="dossier">ملف ' + ltr(m.dossier || '—') + '</span></div>'
                + '<div class="m-meta">' + esc([m.region, m.address].filter(Boolean).join(' · ') || '—') + '</div>'
                + '<div class="m-meta muted">' + last + '</div>'
                + (queued[m.id] ? '<span class="badge">جولة بانتظار الإرسال</span>' : '')
                + (phones ? '<div class="calls">' + phones + '</div>' : '')
                + '<div class="m-foot"><button type="button" class="btn" data-visit="' + m.id + '">تسجيل جولة</button></div>'
                + '</div>';
        }).join('');
    }

    function render() { renderNet(); renderQueue(); renderPackage(); }

    // ── تنزيل القائمة ────────────────────────────────────────────────────
    // آيفون لا يخفي <option> بـ hidden، فنعيد بناء قائمة المناطق حسب القطاع
    var allRegions = Array.prototype.slice.call($('region_id').options, 1).map(function (o) {
        return { id: o.value, name: o.textContent, sector: o.dataset.sector };
    });
    $('sector_id').addEventListener('change', function () {
        var sector = this.value, current = $('region_id').value;
        var regions = allRegions.filter(function (r) { return sector === '' || r.sector === sector; });
        $('region_id').innerHTML = '<option value="">كل المناطق</option>' + regions.map(function (r) {
            return '<option value="' + esc(r.id) + '">' + esc(r.name) + '</option>';
        }).join('');
        if (regions.some(function (r) { return r.id === current; })) $('region_id').value = current;
    });

    $('download-form').addEventListener('submit', function (e) {
        e.preventDefault();
        var params = new URLSearchParams();
        new FormData(this).forEach(function (v, k) { if (v !== '') params.append(k, v); });
        var btn = $('download-btn');
        btn.disabled = true;
        btn.textContent = 'جارٍ التنزيل…';
        request(URLS.package + '?' + params.toString()).then(function (res) {
            if (res.status === 401 || res.redirected) { $('login-needed').hidden = false; return null; }
            if (!res.ok) throw new Error(res.status);
            return res.json();
        }).then(function (pkg) {
            if (!pkg) return;
            if (!write(PACKAGE_KEY, pkg)) { alert('تعذّر حفظ القائمة على الجهاز — قد تكون مساحة التخزين ممتلئة.'); return; }
            notice('');
            $('filter').value = '';
            toast('نُزّلت ' + pkg.members.length + ' مستفيد إلى الجهاز');
            renderPackage();
        }).catch(function () {
            toast('تعذّر التنزيل — تحقق من الاتصال وأعد المحاولة');
        }).finally(renderNet);
    });

    $('clear-package').addEventListener('click', function () {
        if (!confirm('حذف قائمة الزيارة من هذا الجهاز؟ الجولات غير المرسلة لا تُحذف.')) return;
        remove(PACKAGE_KEY);
        renderPackage();
    });

    $('filter').addEventListener('input', renderMembers);

    // ── تسجيل جولة ───────────────────────────────────────────────────────
    function fillSelect(id, items, emptyLabel) {
        $(id).innerHTML = '<option value="">' + emptyLabel + '</option>' + (items || []).map(function (o) {
            return '<option value="' + esc(o.id) + '">' + esc(o.name) + '</option>';
        }).join('');
    }

    $('members').addEventListener('click', function (e) {
        var btn = e.target.closest('[data-visit]');
        if (!btn) return;
        var pkg = loadPackage();
        if (!pkg) return renderPackage();
        currentMember = pkg.members.find(function (m) { return String(m.id) === btn.dataset.visit; });
        if (!currentMember) return;
        var form = $('visit-form');
        form.reset();
        fillSelect('field_visit_status_id', pkg.options.statuses, '— غير محدد —');
        fillSelect('house_type_id', pkg.options.house_types, '— غير محدد —');
        fillSelect('house_condition_id', pkg.options.house_conditions, '— بدون —');
        form.elements.visit_date.value = today();
        form.elements.visitor.value = read(VISITOR_KEY, '') || '';
        $('visit-member').textContent = currentMember.name + ' — ملف ' + (currentMember.dossier || '—');
        // returnValue يبقى من الإغلاق السابق: بدونه يحفظ زر «رجوع» جولة ثانية بعد أول حفظ
        $('visit-dialog').returnValue = '';
        $('visit-dialog').showModal();
    });

    $('visit-dialog').addEventListener('close', function () {
        if (this.returnValue !== 'save' || !currentMember) return;
        var fields = $('visit-form').elements;
        var data = {
            visit_date: fields.visit_date.value || today(),
            amount_operation: fields.amount_operation.value,
            has_video: fields.has_video.checked,
            has_special_case: fields.has_special_case.checked
        };
        ['field_visit_status_id', 'house_type_id', 'house_condition_id', 'visitor', 'estimated_amount', 'amount_reason', 'notes']
            .forEach(function (name) { var v = fields[name].value.trim(); if (v !== '') data[name] = v; });
        if (data.visitor) write(VISITOR_KEY, data.visitor);

        var pkg = loadPackage();
        updateQueue(function (items) {
            items.push({
                client_id: uuid(),
                user_id: pkg ? pkg.user_id : null,
                member_id: currentMember.id,
                member_name: currentMember.name,
                dossier: currentMember.dossier,
                data: data,
                saved_at: Date.now(),
                error: null
            });
            return items;
        });
        currentMember = null;
        toast(navigator.onLine ? 'حُفظت الجولة — جارٍ إرسالها' : 'حُفظت الجولة على الجهاز — تُرسل عند عودة الإنترنت');
        render();
        sync();
    });

    $('queue').addEventListener('click', function (e) {
        var btn = e.target.closest('[data-delete]');
        if (!btn || !confirm('حذف هذه الجولة من الجهاز؟ لن تُرسل إلى النظام.')) return;
        updateQueue(function (items) { return items.filter(function (i) { return i.client_id !== btn.dataset.delete; }); });
        render();
    });

    // ── الإرسال ──────────────────────────────────────────────────────────
    function setError(clientId, message) {
        updateQueue(function (items) {
            items.forEach(function (i) { if (i.client_id === clientId) i.error = message; });
            return items;
        });
    }

    function postVisit(item, token) {
        return request(URLS.visits, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify(Object.assign({ client_id: item.client_id, member_id: item.member_id }, item.data))
        }).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (body) { return { status: res.status, body: body }; });
        });
    }

    function sync() {
        if (syncing || !navigator.onLine || !queue().length) return;
        syncing = true;
        renderQueue();
        var sent = 0, pending = 0;

        getSession().then(function (session) {
            if (!session) return;
            var items = queue();
            // واحدة تلو الأخرى: انقطاع الشبكة يوقف الإرسال وتبقى البقية محفوظة
            return items.reduce(function (chain, item) {
                return chain.then(function (stop) {
                    if (stop) return true;
                    if (item.user_id !== null && item.user_id !== session.user_id) {
                        setError(item.client_id, 'سُجّلت بحساب آخر — ادخل بذلك الحساب لإرسالها');
                        return false;
                    }
                    return postVisit(item, session.token).then(function (r) {
                        if (r.status === 200) {
                            updateQueue(function (q) { return q.filter(function (i) { return i.client_id !== item.client_id; }); });
                            sent++;
                            if (r.body.status === 'pending') pending++;
                            return false;
                        }
                        if (r.status === 422) {
                            var errors = r.body.errors ? Object.values(r.body.errors)[0] : null;
                            setError(item.client_id, (errors && errors[0]) || r.body.message || 'بيانات غير صالحة');
                            return false;
                        }
                        if (r.status === 401) { $('login-needed').hidden = false; }
                        return true; // 419 أو خطأ في السيرفر: نعيد المحاولة لاحقاً برمز جديد
                    });
                });
            }, Promise.resolve(false));
        }).catch(function () {
            // انقطاع أثناء الإرسال — تبقى الجولات وتُعاد المحاولة لاحقاً
        }).finally(function () {
            syncing = false;
            if (sent) toast('أُرسلت ' + sent + ' جولة' + (pending ? ' — بانتظار موافقة المسؤول' : ''));
            render();
        });
    }

    $('sync-now').addEventListener('click', sync);

    // ── البدء ────────────────────────────────────────────────────────────
    window.addEventListener('online', function () { render(); sync(); });
    window.addEventListener('offline', render);
    document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'visible') { render(); sync(); } });
    setInterval(function () { if (queue().length) sync(); }, 60000);

    if (!('serviceWorker' in navigator) || !window.isSecureContext) $('offline-unsupported').hidden = false;

    // بحث القائمة يبقى ظاهراً تحت الشريط العلوي أثناء التمرير (ارتفاعه يتغير مع حافة الشاشة في آيفون)
    function measureTop() { document.documentElement.style.setProperty('--top-h', document.querySelector('.top').offsetHeight + 'px'); }
    measureTop();
    window.addEventListener('resize', measureTop);

    render();
    if (navigator.onLine) {
        getSession().then(function (session) {
            var pkg = read(PACKAGE_KEY, null);
            // قائمة نزّلها حساب آخر على نفس الجهاز لا تُعرض
            if (session && pkg && pkg.user_id !== session.user_id) { remove(PACKAGE_KEY); renderPackage(); }
            sync();
        }).catch(function () {});
    }
})();
</script>
</body>
</html>
