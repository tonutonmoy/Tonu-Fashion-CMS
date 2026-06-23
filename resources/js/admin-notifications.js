const POLL_MS = 30000;

function playNotificationSound() {
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
        setTimeout(() => {
            osc.stop();
            ctx.close();
        }, 180);
    } catch {
        // ignore
    }
}

function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function updateLowStockBadge(count) {
    const bell = document.querySelector('[data-admin-low-stock]');
    const badge = bell?.querySelector('[data-admin-low-stock-count]');
    if (!badge) return;

    if (count > 0) {
        badge.textContent = count > 9 ? '9+' : String(count);
        badge.classList.remove('hidden');
        bell.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
}

async function markLowStockRead() {
    await fetch('/admin/api/notifications/mark-read', {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ type: 'low_stock' }),
    });
}

async function pollNotifications(lastUnread) {
    const res = await fetch('/admin/api/notifications', {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });
    if (!res.ok) return lastUnread;

    const data = await res.json();
    const unread = data.low_stock?.unread_count || 0;
    updateLowStockBadge(unread);

    if (lastUnread !== null && unread > lastUnread) {
        playNotificationSound();
    }

    return unread;
}

function initAdminNotifications() {
    if (!document.body.classList.contains('admin-body')) return;
    if (!document.querySelector('[data-admin-low-stock]')) return;

    let lastUnread = null;

    const toggle = document.querySelector('[data-admin-low-stock-toggle]');
    const panel = document.querySelector('[data-admin-low-stock-panel]');
    const markReadBtn = document.querySelector('[data-admin-low-stock-mark-read]');

    toggle?.addEventListener('click', async (event) => {
        event.stopPropagation();
        panel?.classList.toggle('hidden');
    });

    markReadBtn?.addEventListener('click', async (event) => {
        event.preventDefault();
        await markLowStockRead();
        lastUnread = await pollNotifications(lastUnread);
        panel?.classList.add('hidden');
    });

    document.addEventListener('click', (event) => {
        const root = document.querySelector('[data-admin-low-stock]');
        if (root && !root.contains(event.target)) {
            panel?.classList.add('hidden');
        }
    });

    pollNotifications(lastUnread).then((count) => {
        lastUnread = count;
    });

    window.setInterval(async () => {
        lastUnread = await pollNotifications(lastUnread);
    }, POLL_MS);
}

document.addEventListener('DOMContentLoaded', initAdminNotifications);
