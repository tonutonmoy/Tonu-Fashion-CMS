/**
 * Storefront support chat — lazy-loaded, polls only when panel is open.
 */
import { onPageLoad } from './page-load';

const STORAGE_KEY = 'support_chat_state';
const POLL_OPEN_MS = 3000;

let audioCtx = null;
let activeChat = null;

function readState() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    } catch {
        return {};
    }
}

function writeState(patch) {
    const next = { ...readState(), ...patch };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(next));
    return next;
}

function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function escapeHtml(text) {
    const el = document.createElement('div');
    el.textContent = text ?? '';
    return el.innerHTML;
}

function playNotifySound() {
    try {
        audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.value = 880;
        gain.gain.value = 0.08;
        osc.start();
        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.25);
        osc.stop(audioCtx.currentTime + 0.25);
    } catch {
        /* ignore */
    }
}

function messageBubbleHtml(msg) {
    let inner = '';
    if (msg.attachment_url) {
        inner += `<a href="${escapeHtml(msg.attachment_url)}" target="_blank" rel="noopener"><img src="${escapeHtml(msg.attachment_url)}" alt="" class="support-widget-msg-image"></a>`;
    }
    if (msg.body) {
        inner += escapeHtml(msg.body);
    }
    return inner;
}

function renderMessage(msg, isCustomer, logoUrl) {
    if (!isCustomer && logoUrl) {
        return `<div class="support-widget-msg support-widget-msg--them" data-id="${msg.id}">
            <img src="${escapeHtml(logoUrl)}" alt="" class="support-widget-msg-avatar">
            <div class="support-widget-msg-col">
                <div class="support-widget-msg-bubble">${messageBubbleHtml(msg)}</div>
                <span class="support-widget-msg-time">${escapeHtml(msg.time_label || '')}</span>
            </div>
        </div>`;
    }
    return `<div class="support-widget-msg ${isCustomer ? 'support-widget-msg--me' : 'support-widget-msg--them'}" data-id="${msg.id}">
        <div class="support-widget-msg-bubble">${messageBubbleHtml(msg)}</div>
        <span class="support-widget-msg-time">${escapeHtml(msg.time_label || '')}</span>
    </div>`;
}

function scrollToBottom(el) {
    if (el) el.scrollTop = el.scrollHeight;
}

function mountBackdrop(backdrop, open) {
    if (!backdrop) return;
    if (open) {
        backdrop.classList.remove('hidden');
        backdrop.hidden = false;
        backdrop.setAttribute('data-turbo-temporary', '');
        if (backdrop.parentElement !== document.body) {
            document.body.appendChild(backdrop);
        }
    } else {
        backdrop.classList.add('hidden');
        backdrop.hidden = true;
        backdrop.removeAttribute('data-turbo-temporary');
    }
}

function createSupportChat(root) {
    const sessionUrl = root.dataset.sessionUrl;
    const resumeUrl = root.dataset.resumeUrl;
    const storeName = root.dataset.storeName || 'Support';
    const logoUrl = root.dataset.storeLogo || '';
    const toggle = document.getElementById('support-widget-toggle');
    const panel = document.getElementById('support-widget-panel');
    const backdrop = document.getElementById('support-widget-backdrop');
    const intro = document.getElementById('support-widget-intro');
    const chat = document.getElementById('support-widget-chat');
    const introForm = document.getElementById('support-intro-form');
    const messagesEl = document.getElementById('support-widget-messages');
    const form = document.getElementById('support-widget-form');
    const input = document.getElementById('support-widget-input');
    const imageInput = document.getElementById('support-widget-image');
    const imageBtn = document.getElementById('support-widget-image-btn');
    const badge = document.getElementById('support-widget-badge');

    let state = readState();
    let pollTimer = null;
    let lastMessageId = state.lastMessageId || 0;
    let panelOpen = false;
    let hasActiveChat = Boolean(state.conversationUuid && state.guestToken);

    const onRootClick = (e) => {
        if (e.target.closest('#support-widget-close')) {
            e.preventDefault();
            e.stopPropagation();
            setPanelOpen(false);
        }
    };

    const onKeydown = (e) => {
        if (e.key === 'Escape' && panelOpen) {
            e.preventDefault();
            setPanelOpen(false);
        }
    };

    const onBackdropClick = () => setPanelOpen(false);

    function setBadge(count) {
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : String(count);
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function setPanelOpen(open) {
        panelOpen = Boolean(open);
        if (panel) {
            panel.hidden = !panelOpen;
            panel.classList.toggle('hidden', !panelOpen);
            panel.classList.toggle('is-open', panelOpen);
        }
        mountBackdrop(backdrop, panelOpen);
        document.body.classList.toggle('overflow-hidden', panelOpen);
        document.body.classList.toggle('support-chat-open', panelOpen);
        toggle?.setAttribute('aria-expanded', panelOpen ? 'true' : 'false');
        toggle?.querySelector('.support-widget-icon--chat')?.classList.toggle('hidden', panelOpen);
        toggle?.querySelector('.support-widget-icon--close')?.classList.toggle('hidden', !panelOpen);
        writeState({ panelOpen });

        if (panelOpen) {
            setBadge(0);
            if (state.conversationUuid) {
                if (!messagesEl?.querySelector('[data-id]')) {
                    loadHistory().then(() => pollMessages(true));
                } else {
                    pollMessages(true);
                }
            } else {
                showIntro();
            }
            startPolling();
        } else {
            stopPolling();
            input?.blur();
        }
    }

    function showIntro() {
        intro?.classList.remove('hidden');
        chat?.classList.add('hidden');
        if (state.guestName && introForm?.guest_name) introForm.guest_name.value = state.guestName;
        if (state.guestPhone && introForm?.guest_phone) introForm.guest_phone.value = state.guestPhone;
    }

    function showChat() {
        intro?.classList.add('hidden');
        chat?.classList.remove('hidden');
    }

    function appendMessages(messages, scroll = true, silent = false) {
        if (!messages?.length || !messagesEl) return;
        const existing = new Set([...messagesEl.querySelectorAll('[data-id]')].map((n) => Number(n.dataset.id)));
        let added = false;
        messages.forEach((msg) => {
            if (existing.has(msg.id)) return;
            const isCustomer = msg.sender_type === 'customer';
            messagesEl.insertAdjacentHTML('beforeend', renderMessage(msg, isCustomer, logoUrl));
            lastMessageId = Math.max(lastMessageId, msg.id);
            added = true;
            if (!isCustomer && !silent && !panelOpen) {
                setBadge((Number(badge?.textContent) || 0) + 1);
            }
            if (!isCustomer && !silent) {
                playNotifySound();
                if (!panelOpen && 'Notification' in window && Notification.permission === 'granted') {
                    new Notification(storeName, { body: msg.body || 'Sent an image', icon: logoUrl || '/favicon.ico' });
                }
            }
        });
        writeState({ lastMessageId });
        if (added && scroll) scrollToBottom(messagesEl);
    }

    function renderHistory(messages) {
        if (!messagesEl) return;
        messagesEl.innerHTML = '';
        lastMessageId = 0;
        appendMessages(messages, false, true);
    }

    async function api(url, options = {}) {
        const isForm = options.body instanceof FormData;
        const res = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest',
                ...(isForm ? {} : { 'Content-Type': 'application/json' }),
                ...(options.headers || {}),
            },
            ...options,
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'Request failed');
        return data;
    }

    async function startSession(name, phone) {
        const data = await api(sessionUrl, {
            method: 'POST',
            body: JSON.stringify({
                guest_name: name,
                guest_phone: phone,
                guest_token: state.guestToken || null,
            }),
        });
        state = writeState({
            guestToken: data.guest_token,
            conversationUuid: data.conversation.uuid,
            guestName: name,
            guestPhone: phone,
        });
        hasActiveChat = true;
        if (messagesEl) messagesEl.innerHTML = '';
        appendMessages(data.messages, false);
        showChat();
    }

    async function loadHistory() {
        if (!state.conversationUuid || !state.guestToken) return false;
        const params = new URLSearchParams({
            guest_token: state.guestToken,
            guest_phone: state.guestPhone || '',
        });
        const url = `/api/support/conversations/${state.conversationUuid}?${params}`;
        const data = await api(url);
        renderHistory(data.messages || []);
        return true;
    }

    async function resumeChat() {
        const params = new URLSearchParams();
        if (state.guestToken) params.set('guest_token', state.guestToken);
        if (state.guestPhone) params.set('guest_phone', state.guestPhone);

        const data = await api(`${resumeUrl}?${params}`);
        if (!data.conversation) return false;

        state = writeState({
            guestToken: data.guest_token,
            conversationUuid: data.conversation.uuid,
            guestName: data.conversation.guest_name,
            guestPhone: data.conversation.guest_phone,
        });
        hasActiveChat = true;
        renderHistory(data.messages || []);
        showChat();
        return true;
    }

    async function pollMessages(markRead = false) {
        if (!state.conversationUuid || !panelOpen) return;
        const params = new URLSearchParams({
            guest_token: state.guestToken || '',
            guest_phone: state.guestPhone || '',
        });
        if (lastMessageId) params.set('since', String(lastMessageId));
        const url = `/api/support/conversations/${state.conversationUuid}?${params}`;
        const data = await api(url);
        appendMessages(data.messages, panelOpen);
        if (markRead && panelOpen && data.messages?.some((m) => m.sender_type === 'admin')) {
            await api(`/api/support/conversations/${state.conversationUuid}/read`, {
                method: 'POST',
                body: JSON.stringify({
                    guest_token: state.guestToken,
                    guest_phone: state.guestPhone || '',
                }),
            });
        }
    }

    function startPolling() {
        stopPolling();
        if (!panelOpen || !state.conversationUuid) return;
        pollTimer = setInterval(() => {
            if (!panelOpen) {
                stopPolling();
                return;
            }
            pollMessages(panelOpen);
        }, POLL_OPEN_MS);
    }

    function stopPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = null;
    }

    const onIntroSubmit = async (e) => {
        e.preventDefault();
        const name = introForm.guest_name.value.trim();
        const phone = introForm.guest_phone.value.trim();
        if (!name || !phone) return;
        try {
            await startSession(name, phone);
        } catch (err) {
            alert(err.message);
        }
    };

    const onImageBtnClick = () => imageInput?.click();

    const sendMessage = async () => {
        const body = input?.value.trim() || '';
        const file = imageInput?.files?.[0] || null;
        if ((!body && !file) || !state.conversationUuid) return;

        if (input) input.value = '';
        if (imageInput) imageInput.value = '';

        const formData = new FormData();
        if (body) formData.append('body', body);
        if (file) formData.append('attachment', file);
        formData.append('guest_token', state.guestToken || '');
        formData.append('guest_phone', state.guestPhone || '');

        try {
            const data = await api(`/api/support/conversations/${state.conversationUuid}/messages`, {
                method: 'POST',
                body: formData,
            });
            appendMessages([data.message], true);
        } catch (err) {
            if (input) input.value = body;
            alert(err.message);
        }
    };

    const onFormSubmit = (e) => {
        e.preventDefault();
        sendMessage();
    };

    const onInputKeydown = (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    };

    const onToggleClick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
        if (audioCtx?.state === 'suspended') audioCtx.resume();
        setPanelOpen(!panelOpen);
    };

    root.addEventListener('click', onRootClick, true);
    backdrop?.addEventListener('click', onBackdropClick);
    document.addEventListener('keydown', onKeydown);
    introForm?.addEventListener('submit', onIntroSubmit);
    imageBtn?.addEventListener('click', onImageBtnClick);
    form?.addEventListener('submit', onFormSubmit);
    input?.addEventListener('keydown', onInputKeydown);
    toggle?.addEventListener('click', onToggleClick);

    (async function bootstrap() {
        try {
            if (state.guestToken || state.guestPhone) {
                const resumed = await resumeChat();
                if (resumed) return;
            }
        } catch {
            /* fall through */
        }

        if (state.conversationUuid && state.guestToken) {
            try {
                showChat();
                hasActiveChat = true;
                await loadHistory();
            } catch {
                showIntro();
            }
        } else {
            showIntro();
        }
    })();

    return {
        destroy() {
            stopPolling();
            mountBackdrop(backdrop, false);
            root.removeEventListener('click', onRootClick, true);
            backdrop?.removeEventListener('click', onBackdropClick);
            document.removeEventListener('keydown', onKeydown);
            introForm?.removeEventListener('submit', onIntroSubmit);
            imageBtn?.removeEventListener('click', onImageBtnClick);
            form?.removeEventListener('submit', onFormSubmit);
            input?.removeEventListener('keydown', onInputKeydown);
            toggle?.removeEventListener('click', onToggleClick);
            document.body.classList.remove('support-chat-open');
        },
    };
}

export function initSupportChat() {
    const root = document.getElementById('support-chat-widget');
    if (!root) {
        return;
    }

    if (activeChat) {
        activeChat.destroy();
        activeChat = null;
    }

    root.removeAttribute('data-chat-ready');
    activeChat = createSupportChat(root);
    root.dataset.chatReady = '1';
}

onPageLoad(() => {
    if (document.getElementById('support-chat-widget')) {
        initSupportChat();
    }
});
