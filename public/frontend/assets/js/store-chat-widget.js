(function () {
    const cfg = window.STORE_CHAT_CONFIG;
    if (!cfg || !cfg.enabled) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function detectProductSlug() {
        const m = window.location.pathname.match(/\/product\/([^/]+)/);
        return m ? decodeURIComponent(m[1]) : null;
    }

    function el(id) {
        return document.getElementById(id);
    }

    function appendBubble(role, text, products) {
        const box = el('store-chat-messages');
        if (!box) return;
        const div = document.createElement('div');
        div.className = 'store-chat-bubble ' + role;
        div.textContent = text;
        if (role === 'assistant' && products && products.length) {
            const links = document.createElement('div');
            links.className = 'store-chat-products';
            products.forEach(function (p) {
                const a = document.createElement('a');
                a.href = p.url;
                a.target = '_blank';
                a.rel = 'noopener';
                a.textContent = p.name + ' — ' + p.price_formatted;
                links.appendChild(a);
            });
            div.appendChild(links);
        }
        box.appendChild(div);
        box.scrollTop = box.scrollHeight;
    }

    function showWelcome() {
        const box = el('store-chat-messages');
        if (!box || box.dataset.welcomeShown === '1') return;
        box.innerHTML = '';
        appendBubble('assistant', cfg.welcomeMessage, []);
        box.dataset.welcomeShown = '1';
    }

    function renderHistory(history) {
        const box = el('store-chat-messages');
        if (!box) return;
        box.innerHTML = '';
        box.dataset.welcomeShown = '1';
        appendBubble('assistant', cfg.welcomeMessage, []);
        (history || []).forEach(function (m) {
            if (m.role === 'user' || m.role === 'assistant') {
                appendBubble(m.role, m.content, []);
            }
        });
    }

    async function parseJsonResponse(res) {
        const contentType = res.headers.get('content-type') || '';
        const text = await res.text();

        if (contentType.includes('application/json') || text.trim().startsWith('{')) {
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('رد الخادم غير صالح.');
            }
        }

        if (res.status === 419) {
            throw new Error('انتهت الجلسة. حدّث الصفحة وحاول مرة أخرى.');
        }
        if (res.status === 404) {
            throw new Error('مسار المحادثة غير موجود (404). تأكد من رفع التحديثات وتشغيل migrate على الخادم.');
        }
        if (res.status === 502 || res.status === 504) {
            throw new Error('انتهت مهلة الخادم. حاول بعد قليل.');
        }

        throw new Error('الخادم أعاد صفحة HTML بدل JSON. راجع السجلات أو شغّل php artisan migrate.');
    }

    async function api(url, options) {
        const res = await fetch(url, Object.assign({
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
        }, options || {}));
        const data = await parseJsonResponse(res);
        if (!res.ok || !data.success) {
            throw new Error(data.message || 'فشل الطلب');
        }
        return data.data;
    }

    let sessionToken = null;
    let busy = false;

    async function ensureSession() {
        if (sessionToken) return sessionToken;
        const data = await api(cfg.sessionUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: '{}',
        });
        sessionToken = data.session_token;
        if (data.history && data.history.length) {
            renderHistory(data.history);
        }
        return sessionToken;
    }

    async function sendMessage(text) {
        if (busy) return;
        busy = true;
        const typing = el('store-chat-typing');
        const sendBtn = el('store-chat-send');
        if (typing) typing.textContent = 'جاري الكتابة...';
        if (sendBtn) sendBtn.disabled = true;

        appendBubble('user', text, []);

        try {
            await ensureSession();
            const data = await api(cfg.messageUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    message: text,
                    product_slug: detectProductSlug(),
                    session_token: sessionToken,
                }),
            });
            appendBubble('assistant', data.reply, data.suggested_products || []);
        } catch (e) {
            appendBubble('assistant', e.message || 'حدث خطأ.', []);
        } finally {
            busy = false;
            if (typing) typing.textContent = '';
            if (sendBtn) sendBtn.disabled = false;
        }
    }

    function bindUi() {
        showWelcome();

        const launcher = el('store-chat-launcher');
        const panel = el('store-chat-panel');
        const closeBtn = el('store-chat-close');
        const form = el('store-chat-form');
        const input = el('store-chat-input');

        launcher?.addEventListener('click', function () {
            panel?.classList.toggle('is-open');
            if (panel?.classList.contains('is-open')) {
                showWelcome();
                ensureSession().catch(function () {});
                input?.focus();
            }
        });

        closeBtn?.addEventListener('click', function () {
            panel?.classList.remove('is-open');
        });

        form?.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = input?.value?.trim();
            if (!text) return;
            input.value = '';
            sendMessage(text);
        });
    }

    bindUi();
})();
