const POLL_ACTIVE_MS = 3000;
const POLL_IDLE_MS = 8000;

function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function escapeHtml(text) {
    const el = document.createElement('div');
    el.textContent = text ?? '';
    return el.innerHTML;
}

function pollInterval() {
    return document.hidden ? POLL_IDLE_MS : POLL_ACTIVE_MS;
}

function messageContentHtml(msg) {
    let html = '';
    if (msg.attachment_url) {
        html += `<a href="${escapeHtml(msg.attachment_url)}" target="_blank" rel="noopener"><img src="${escapeHtml(msg.attachment_url)}" alt="" class="support-msg-image max-w-[200px] rounded-lg mb-1"></a>`;
    }
    if (msg.body) {
        html += `<p class="support-msg-text">${escapeHtml(msg.body)}</p>`;
    }
    html += `<span class="support-msg-time">${escapeHtml(msg.time_label || '')}</span>`;
    return html;
}

function updateUnreadBadges(count) {
    document.querySelectorAll('#support-admin-unread, [data-support-unread]').forEach((el) => {
        if (count > 0) {
            el.textContent = `${count} unread`;
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });
    const navBadge = document.getElementById('admin-support-nav-badge');
    if (navBadge) {
        navBadge.textContent = count > 9 ? '9+' : String(count);
        navBadge.classList.toggle('hidden', count <= 0);
    }
}

function showToast(message) {
    if (window.AdminUI?.showToast) {
        const preview = message.body || (message.attachment_url ? '[Image]' : '');
        const label = message.guest_name ? `${message.guest_name}: ${preview}` : preview;
        window.AdminUI.showToast(label, 'info');
    }
}

function notifyAdmin(message) {
    if (!('Notification' in window) || Notification.permission !== 'granted' || !document.hidden) return;
    const preview = message.body || '[Image]';
    new Notification('New support message', {
        body: `${message.guest_name || 'Customer'}: ${preview}`,
        icon: '/favicon.ico',
    });
}

function playAlertSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.value = 880;
        gain.gain.value = 0.08;
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start();
        setTimeout(() => { osc.stop(); ctx.close(); }, 180);
    } catch {
        // ignore
    }
}

function renderAdminMessage(msg) {
    const isAdmin = msg.sender_type === 'admin';
    return `<div class="support-msg ${isAdmin ? 'support-msg--admin' : 'support-msg--customer'}" data-id="${msg.id}">
        <div class="support-msg-bubble">${messageContentHtml(msg)}</div>
    </div>`;
}

function scrollChatBottom() {
    const el = document.getElementById('support-chat-messages');
    if (el) el.scrollTop = el.scrollHeight;
}

function appendAdminMessages(messages) {
    const box = document.getElementById('support-chat-messages');
    if (!box || !messages?.length) return 0;
    const existing = new Set([...box.querySelectorAll('[data-id]')].map((n) => Number(n.dataset.id)));
    let added = 0;
    messages.forEach((msg) => {
        if (existing.has(msg.id)) return;
        box.insertAdjacentHTML('beforeend', renderAdminMessage(msg));
        added++;
    });
    if (added) scrollChatBottom();
    return added;
}

async function pollNotifications(sinceMessageId) {
    const params = new URLSearchParams();
    if (sinceMessageId) params.set('since_message_id', String(sinceMessageId));
    const res = await fetch(`/admin/api/support/notifications?${params}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (!res.ok) return { sinceMessageId, unread: 0 };
    const data = await res.json();
    updateUnreadBadges(data.unread_count || 0);
    let lastId = sinceMessageId;
    (data.messages || []).forEach((msg) => {
        lastId = Math.max(lastId, msg.id);
        if (sinceMessageId && msg.id > sinceMessageId) {
            showToast(msg);
            notifyAdmin(msg);
            playAlertSound();
        }
    });
    return { sinceMessageId: lastId, unread: data.unread_count || 0 };
}

async function sendAdminMessage(sendUrl, body, file) {
    const formData = new FormData();
    if (body) formData.append('body', body);
    if (file) formData.append('attachment', file);

    const res = await fetch(sendUrl, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrf(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Failed to send');
    return data.message;
}

function initPage() {
    const root = document.getElementById('admin-support-chat');
    if (!root) return;

    const pollUrl = root.dataset.pollUrl;
    const sendUrl = root.dataset.sendUrl;
    const form = document.getElementById('support-chat-form');
    const input = document.getElementById('support-chat-input');
    const imageInput = document.getElementById('support-chat-image');
    const imageBtn = document.getElementById('support-chat-image-btn');
    const box = document.getElementById('support-chat-messages');
    let lastId = [...box?.querySelectorAll('[data-id]') || []].map((n) => Number(n.dataset.id)).sort((a, b) => b - a)[0] || 0;
    let pollTimer = null;

    scrollChatBottom();

    imageBtn?.addEventListener('click', () => imageInput?.click());

    input?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form?.requestSubmit();
        }
    });

    const doSend = async () => {
        const body = input?.value.trim() || '';
        const file = imageInput?.files?.[0] || null;
        if (!body && !file) return;

        if (input) input.value = '';
        if (imageInput) imageInput.value = '';

        try {
            const message = await sendAdminMessage(sendUrl, body, file);
            appendAdminMessages([message]);
            lastId = Math.max(lastId, message.id);
        } catch (err) {
            if (input) input.value = body;
            alert(err.message);
        }
    };

    form?.addEventListener('submit', (e) => {
        e.preventDefault();
        doSend();
    });

    const schedulePoll = () => {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(async () => {
            if (document.hidden) return;
            try {
                const params = new URLSearchParams({ since: String(lastId), mark_read: '1' });
                const res = await fetch(`${pollUrl}?${params}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await res.json();
                const added = appendAdminMessages(data.messages || []);
                if (added) {
                    lastId = Math.max(lastId, ...data.messages.map((m) => m.id));
                }
            } catch {
                /* ignore */
            }
        }, pollInterval());
    };

    schedulePoll();
    document.addEventListener('visibilitychange', schedulePoll);
}

let sinceMessageId = 0;
let notificationsReady = false;
let notifyTimer = null;

function initNotifications() {
    if (!document.querySelector('[data-admin-support-notify]')) return;

    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    const tick = async () => {
        if (document.hidden) return;
        try {
            const result = await pollNotifications(sinceMessageId);
            if (!notificationsReady) {
                notificationsReady = true;
                sinceMessageId = result.sinceMessageId;
                updateUnreadBadges(result.unread);
                return;
            }
            sinceMessageId = result.sinceMessageId;
        } catch {
            /* ignore */
        }
    };

    const schedule = () => {
        if (notifyTimer) clearInterval(notifyTimer);
        notifyTimer = setInterval(tick, pollInterval());
    };

    tick();
    schedule();
    document.addEventListener('visibilitychange', schedule);
}

window.AdminSupportChat = { initPage, initNotifications };

document.addEventListener('DOMContentLoaded', () => {
    initNotifications();
    if (document.getElementById('admin-support-chat')) {
        initPage();
    }
});
