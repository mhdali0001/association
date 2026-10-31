<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>العمل الميداني — مسالك النور</title>
    @include('partials.pwa')
    {{-- صفحة مستقلة بلا ملفات Vite: يحفظها التطبيق (sw.js) لتفتح بدون إنترنت. لا تحوي بيانات مستفيدين —
         الجولة تُحفظ على الجهاز (localStorage) وتُرسل عند عودة الاتصال. الحقول نفسها في نموذج صفحة العضو. --}}
    <style>
        @font-face{font-family:'Tajawal';font-weight:400;font-display:swap;src:url('/fonts/tajawal-400.woff2') format('woff2')}
        @font-face{font-family:'Tajawal';font-weight:700;font-display:swap;src:url('/fonts/tajawal-700.woff2') format('woff2')}
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Tajawal', sans-serif; background: #f1f5f9; color: #1e293b; font-size: 15px; }
        [hidden] { display: none !important; }
        bdi { direction: ltr; unicode-bidi: isolate; }
        .top {
            position: sticky; top: 0; z-index: 5; display: flex; align-items: center; gap: 10px;
            padding: 10px 16px; padding-top: max(10px, env(safe-area-inset-top));
            background: #166534; color: #fff; box-shadow: 0 2px 10px rgba(0,0,0,.12);
        }
        .top h1 { font-size: 17px; margin: 0; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .top a { color: #dcfce7; font-size: 13px; text-decoration: none; padding: 12px 2px; white-space: nowrap; }
        .net { font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 99px; background: #dcfce7; color: #166534; white-space: nowrap; }
        .net.off { background: #e2e8f0; color: #475569; }
        main { max-width: 720px; margin: 0 auto; padding: 14px 14px 40px; }
        .card { background: #fff; border-radius: 16px; padding: 16px; margin-bottom: 14px; box-shadow: 0 1px 3px rgba(15,23,42,.06); border: 1px solid #f1f5f9; }
        h2 { font-size: 16px; margin: 0 0 4px; color: #312e81; }
        .muted { color: #64748b; font-size: 13px; }
        .banner { border-radius: 12px; padding: 10px 14px; margin-bottom: 14px; font-size: 14px; line-height: 1.6; }
        .banner.warn { background: #fef3c7; color: #92400e; }
        .banner a { color: inherit; font-weight: 700; }

        /* نفس شكل نموذج الجولة في صفحة العضو */
        .grid { display: grid; grid-template-columns: 1fr; gap: 12px; margin-top: 12px; }
        @media (min-width: 600px) { .grid { grid-template-columns: 1fr 1fr; } .grid .wide { grid-column: span 2; } }
        label.field { display: block; font-size: 12px; font-weight: 700; color: #6b7280; letter-spacing: .02em; margin-bottom: 4px; }
        input[type=text], input[type=number], input[type=date], select, textarea {
            /* 16px: أقل منه يجعل آيفون يكبّر الصفحة عند الضغط على الحقل */
            width: 100%; font-family: inherit; font-size: 16px; color: #1e293b; background: #f9fafb;
            border: 1px solid #e5e7eb; border-radius: 12px; padding: 10px 12px; min-height: 46px;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #818cf8; box-shadow: 0 0 0 3px rgba(129,140,248,.35); }
        textarea { resize: none; }
        .dossier input { font-size: 20px; font-weight: 700; text-align: center; letter-spacing: .05em; min-height: 54px; }
        .dossier-check { min-height: 22px; margin-top: 6px; font-size: 14px; font-weight: 700; text-align: center; }
        .dossier-check.ok { color: #047857; }
        .dossier-check.bad { color: #b91c1c; }
        .dossier-check.wait { color: #64748b; font-weight: 400; }
        .amount { display: flex; gap: 6px; }
        .amount label { display: flex; align-items: center; justify-content: center; padding: 0 12px; min-height: 46px; border-radius: 12px;
            border: 1px solid #e5e7eb; background: #fff; color: #4b5563; font-size: 13px; font-weight: 700; cursor: pointer; white-space: nowrap; }
        .amount label input { display: none; }
        .amount label:has(input[value=add]:checked) { background: #10b981; border-color: #10b981; color: #fff; }
        .amount label:has(input[value=subtract]:checked) { background: #ef4444; border-color: #ef4444; color: #fff; }
        .amount input[type=number] { flex: 1; min-width: 0; font-family: ui-monospace, monospace; }
        .checks { display: flex; gap: 18px; flex-wrap: wrap; align-items: center; }
        .check { display: flex; align-items: center; gap: 8px; min-height: 44px; font-size: 14px; font-weight: 700; color: #4b5563; cursor: pointer; }
        .check input { width: 22px; height: 22px; flex-shrink: 0; }
        .check.video input { accent-color: #e11d48; }
        .check.special input { accent-color: #ea580c; }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; font-family: inherit; font-size: 16px; font-weight: 700;
            border: none; border-radius: 12px; padding: 12px 18px; min-height: 48px; cursor: pointer; background: #4f46e5; color: #fff; white-space: nowrap;
        }
        .btn:disabled { opacity: .5; cursor: default; }
        .btn.block { width: 100%; margin-top: 16px; }
        .btn.light { background: #f1f5f9; color: #334155; font-size: 14px; min-height: 44px; padding: 10px 14px; }

        .sync { border: 2px solid #f59e0b; }
        .sync-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .queue { list-style: none; margin: 10px 0 0; padding: 0; }
        .queue li { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 0; border-top: 1px solid #f1f5f9; font-size: 14px; }
        .queue .q-meta { display: block; font-size: 13px; color: #64748b; }
        .queue .err { display: block; color: #b91c1c; font-size: 13px; }
        #toast {
            position: fixed; bottom: max(20px, env(safe-area-inset-bottom)); left: 50%; transform: translateX(-50%);
            background: #1e293b; color: #fff; padding: 10px 18px; border-radius: 12px; font-size: 14px; z-index: 20;
            max-width: calc(100% - 32px); text-align: center; box-shadow: 0 8px 24px rgba(0,0,0,.2);
        }
    </style>
</head>
<body>
<header class="top">
    <h1>العمل الميداني</h1>
    <span id="net" class="net">متصل</span>
    <a href="{{ route('dashboard') }}">النظام ←</a>
</header>

<main>
    <div id="login-needed" class="banner warn" hidden>
        انتهت جلسة الدخول. <a href="{{ route('login') }}">سجّل الدخول</a> ثم عد إلى هذه الصفحة لإرسال الجولات المحفوظة.
    </div>
    <div id="offline-unsupported" class="banner warn" hidden>
        العمل بدون إنترنت يحتاج فتح النظام عبر رابط آمن (https). يمكنك استخدام الصفحة الآن، لكنها لن تفتح بدون اتصال.
    </div>

    {{-- الجولات المحفوظة على الجهاز بانتظار الإرسال --}}
    <section id="sync" class="card sync" hidden>
        <div class="sync-head">
            <div>
                <strong id="sync-count"></strong>
                <div id="sync-text" class="muted"></div>
            </div>
            <button type="button" id="sync-now" class="btn light">إرسال الآن</button>
        </div>
        <ul id="queue" class="queue"></ul>
    </section>

    <form id="visit-form" class="card" novalidate>
        <h2>تسجيل جولة ميدانية</h2>
        <div class="muted">تُحفظ على الجهاز إن لم يتوفر الإنترنت، وتُرسل تلقائياً عند عودته.</div>

        <div class="grid">
            <div class="wide dossier">
                <label class="field" for="dossier_number">رقم الإضبارة</label>
                <input type="text" name="dossier_number" id="dossier_number" inputmode="numeric" autocomplete="off" placeholder="رقم ملف المستفيد">
                <div id="dossier-check" class="dossier-check"></div>
            </div>
            <div>
                <label class="field" for="field_visit_status_id">الحالة</label>
                <select name="field_visit_status_id" id="field_visit_status_id">
                    <option value="">— غير محدد —</option>
                    @foreach($statuses as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field" for="house_type_id">نوع البيت</label>
                <select name="house_type_id" id="house_type_id">
                    <option value="">— غير محدد —</option>
                    @foreach($houseTypes as $ht)
                        <option value="{{ $ht->id }}">{{ $ht->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field" for="visit_date">تاريخ الزيارة</label>
                <input type="date" name="visit_date" id="visit_date">
            </div>
            <div>
                <label class="field" for="visitor">اسم الزائر</label>
                <input type="text" name="visitor" id="visitor" placeholder="الاسم الكامل للزائر">
            </div>
            <div>
                <label class="field" for="estimated_amount">المبلغ (ل.س)</label>
                <div class="amount">
                    <label><input type="radio" name="amount_operation" value="add" checked>+ إضافة</label>
                    <label><input type="radio" name="amount_operation" value="subtract">− إنقاص</label>
                    <input type="number" name="estimated_amount" id="estimated_amount" min="0" step="0.01" placeholder="0" inputmode="decimal">
                </div>
            </div>
            <div class="wide">
                <label class="field" for="amount_reason">سبب المبلغ</label>
                <input type="text" name="amount_reason" id="amount_reason" placeholder="وصف موجز لسبب تحديد هذا المبلغ">
            </div>
            <div class="wide">
                <label class="field" for="notes">ملاحظات</label>
                <textarea name="notes" id="notes" rows="2" placeholder="ملاحظات الجولة..."></textarea>
            </div>
            <div>
                <label class="field" for="house_condition_id">حالة البيت</label>
                <select name="house_condition_id" id="house_condition_id">
                    <option value="">— بدون —</option>
                    @foreach($houseConditions as $hc)
                        <option value="{{ $hc->id }}">{{ $hc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="checks">
                <label class="check video"><input type="checkbox" name="has_video" value="1"> يوجد فيديو</label>
                <label class="check special"><input type="checkbox" name="has_special_case" value="1"> حالة خاصة</label>
            </div>
        </div>

        <button type="submit" class="btn block">حفظ الجولة</button>
    </form>
</main>

<div id="toast" hidden></div>

<script>
(function () {
    var QUEUE_KEY   = 'fieldwork.queue';
    var VISITOR_KEY = 'fieldwork.visitor';
    var USER_KEY    = 'fieldwork.user';
    var URLS = {
        session: @json(route('field-work.session', [], false)),
        member:  @json(route('field-work.member', [], false)),
        visits:  @json(route('field-work.visits.store', [], false))
    };

    var $ = function (id) { return document.getElementById(id); };
    var form = $('visit-form');
    var fields = form.elements;
    var syncing = false;
    var lookup = { value: null, name: null, found: null };

    // ── التخزين على الجهاز ───────────────────────────────────────────────
    function read(key, fallback) {
        try { var v = localStorage.getItem(key); return v ? JSON.parse(v) : fallback; } catch (e) { return fallback; }
    }
    function write(key, value) {
        try { localStorage.setItem(key, JSON.stringify(value)); return true; } catch (e) { return false; }
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
    // التاريخ والأرقام داخل نص عربي تنقلب بدون عزل اتجاهها (2026-09-12 تظهر 12-09-2026)
    function ltr(s) { return '<bdi>' + esc(s) + '</bdi>'; }
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
    var toastTimer;
    function toast(text) {
        var el = $('toast');
        el.textContent = text;
        el.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () { el.hidden = true; }, 3500);
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
            return res.json().then(function (s) { write(USER_KEY, s.user_id); return s; });
        });
    }

    // ── رقم الإضبارة: اسم المستفيد للتأكد حين يتوفر الإنترنت ─────────────
    var lookupTimer;
    function checkDossier() {
        var value = fields.dossier_number.value.trim();
        var el = $('dossier-check');
        clearTimeout(lookupTimer);
        lookup = { value: value, name: null, found: null };
        if (!value) { el.textContent = ''; el.className = 'dossier-check'; return; }
        if (!navigator.onLine) {
            el.textContent = 'بدون إنترنت — يُتحقق من الرقم عند الإرسال';
            el.className = 'dossier-check wait';
            return;
        }
        el.textContent = '…';
        el.className = 'dossier-check wait';
        lookupTimer = setTimeout(function () {
            request(URLS.member + '?dossier=' + encodeURIComponent(value)).then(function (res) {
                if (fields.dossier_number.value.trim() !== value) return;
                if (res.status === 404) {
                    lookup.found = false;
                    el.textContent = 'لا يوجد مستفيد بهذا الرقم';
                    el.className = 'dossier-check bad';
                    return;
                }
                if (!res.ok) throw new Error(res.status);
                return res.json().then(function (m) {
                    lookup.found = true;
                    lookup.name = m.name;
                    el.textContent = '✓ ' + m.name;
                    el.className = 'dossier-check ok';
                });
            }).catch(function () {
                if (fields.dossier_number.value.trim() !== value) return;
                el.textContent = 'تعذّر التحقق الآن — يُتحقق من الرقم عند الإرسال';
                el.className = 'dossier-check wait';
            });
        }, 400);
    }
    fields.dossier_number.addEventListener('input', checkDossier);

    // ── الحفظ ────────────────────────────────────────────────────────────
    function resetForm() {
        var visitor = fields.visitor.value, date = fields.visit_date.value;
        form.reset();
        fields.visitor.value = visitor || read(VISITOR_KEY, '') || '';
        fields.visit_date.value = date || today();
        checkDossier();
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var dossier = fields.dossier_number.value.trim();
        if (!dossier) { toast('أدخل رقم الإضبارة'); fields.dossier_number.focus(); return; }
        if (lookup.value === dossier && lookup.found === false) { toast('لا يوجد مستفيد بهذا الرقم — تحقق منه'); fields.dossier_number.focus(); return; }

        var data = {
            visit_date: fields.visit_date.value || today(),
            amount_operation: fields.amount_operation.value,
            has_video: fields.has_video.checked,
            has_special_case: fields.has_special_case.checked
        };
        ['field_visit_status_id', 'house_type_id', 'house_condition_id', 'visitor', 'estimated_amount', 'amount_reason', 'notes']
            .forEach(function (name) { var v = fields[name].value.trim(); if (v !== '') data[name] = v; });
        if (data.visitor) write(VISITOR_KEY, data.visitor);

        updateQueue(function (items) {
            items.push({
                client_id: uuid(),
                user_id: read(USER_KEY, null),
                dossier: dossier,
                member_name: lookup.value === dossier ? lookup.name : null,
                data: data,
                saved_at: Date.now(),
                error: null
            });
            return items;
        });
        toast(navigator.onLine ? 'حُفظت الجولة — جارٍ إرسالها' : 'حُفظت الجولة على الجهاز — تُرسل عند عودة الإنترنت');
        resetForm();
        render();
        sync();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ── العرض ────────────────────────────────────────────────────────────
    function renderNet() {
        var on = navigator.onLine;
        $('net').textContent = on ? 'متصل' : 'بدون إنترنت';
        $('net').classList.toggle('off', !on);
    }

    function renderQueue() {
        var items = queue();
        $('sync').hidden = items.length === 0;
        if (!items.length) return;
        var failed = items.filter(function (i) { return i.error; }).length;
        $('sync-count').textContent = items.length + ' جولة بانتظار الإرسال';
        $('sync-text').textContent = syncing ? 'جارٍ الإرسال…'
            : failed ? failed + ' منها تحتاج مراجعة'
            : (navigator.onLine ? 'تُرسل تلقائياً' : 'تُرسل تلقائياً عند عودة الإنترنت');
        $('sync-now').disabled = syncing || !navigator.onLine;
        $('queue').innerHTML = items.map(function (i) {
            var dossier = 'ملف ' + ltr(i.dossier || '—');
            return '<li><div><strong>' + (i.member_name ? esc(i.member_name) : dossier) + '</strong>'
                + '<span class="q-meta">' + (i.member_name ? dossier + ' · ' : '') + ltr(i.data.visit_date) + '</span>'
                + (i.error ? '<span class="err">' + esc(i.error) + '</span>' : '') + '</div>'
                + '<button type="button" class="btn light" data-delete="' + esc(i.client_id) + '">حذف</button></li>';
        }).join('');
    }

    function render() { renderNet(); renderQueue(); }

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
        // جولات النسخة السابقة من الصفحة تحمل رقم العضو بدل رقم الإضبارة
        var target = item.dossier ? { dossier_number: item.dossier } : { member_id: item.member_id };
        return request(URLS.visits, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify(Object.assign({ client_id: item.client_id }, target, item.data))
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
            // واحدة تلو الأخرى: انقطاع الشبكة يوقف الإرسال وتبقى البقية محفوظة
            return queue().reduce(function (chain, item) {
                return chain.then(function (stop) {
                    if (stop) return true;
                    if (item.user_id !== null && item.user_id !== undefined && item.user_id !== session.user_id) {
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
    window.addEventListener('online', function () { render(); checkDossier(); sync(); });
    window.addEventListener('offline', function () { render(); checkDossier(); });
    document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'visible') { render(); sync(); } });
    setInterval(function () { if (queue().length) sync(); }, 60000);

    if (!('serviceWorker' in navigator) || !window.isSecureContext) $('offline-unsupported').hidden = false;

    resetForm();
    render();
    if (navigator.onLine) getSession().then(sync).catch(function () {});
})();
</script>
</body>
</html>
