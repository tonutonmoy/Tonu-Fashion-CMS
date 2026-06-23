import { onPageLoad } from './page-load';

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

function renderLowStockItems(items, threshold) {
    const list = document.querySelector('[data-admin-low-stock-list]');
    if (!list) return;

    if (!items?.length) {
        list.innerHTML = '<p class="px-4 py-6 text-gray-500">No low-stock alerts.</p>';
        return;
    }

    list.innerHTML = items.map((item) => `
        <div class="px-4 py-3 flex justify-between gap-3">
            <div class="min-w-0">
                <p class="font-medium truncate">${item.product_name || ''}</p>
                <p class="text-xs text-gray-500 truncate">${item.variant_label || ''}</p>
            </div>
            <span class="font-semibold text-orange-600 shrink-0">${item.available_stock ?? 0} left</span>
        </div>
    `).join('');

    const heading = document.querySelector('[data-admin-low-stock-panel] .border-b span');
    if (heading && threshold) {
        heading.textContent = `Low Stock (< ${threshold})`;
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
    if (!res.ok) return { lastUnread, data: null };

    const data = await res.json();
    const unread = data.low_stock?.unread_count || 0;
    updateLowStockBadge(unread);

    if (lastUnread !== null && unread > lastUnread) {
        playNotificationSound();
    }

    return { lastUnread: unread, data };
}

async function loadLowStockPanel(force = false) {
    const list = document.querySelector('[data-admin-low-stock-list]');
    if (!list) return;

    if (list.dataset.loaded === '1' && !force) {
        return;
    }

    list.dataset.loaded = 'loading';
    const placeholder = list.querySelector('[data-admin-low-stock-placeholder]');
    if (placeholder) {
        placeholder.textContent = 'Loading…';
    }

    const { data } = await pollNotifications(null);
    if (!data?.low_stock) {
        list.innerHTML = '<p class="px-4 py-6 text-gray-500">Could not load alerts.</p>';
        return;
    }

    renderLowStockItems(data.low_stock.items, data.low_stock.threshold);
    list.dataset.loaded = '1';
}

function initAdminNotifications() {
    if (!document.body.classList.contains('admin-body')) return;
    if (!document.querySelector('[data-admin-low-stock]')) return;
    if (document.documentElement.dataset.adminNotifications === '1') return;

    document.documentElement.dataset.adminNotifications = '1';

    let lastUnread = null;

    const panel = document.querySelector('[data-admin-low-stock-panel]');
    const root = document.querySelector('[data-admin-low-stock]');

    document.addEventListener('click', async (event) => {
        const toggle = event.target.closest('[data-admin-low-stock-toggle]');
        if (toggle) {
            event.stopPropagation();
            const opening = panel?.classList.contains('hidden');
            panel?.classList.toggle('hidden');
            if (opening) {
                await loadLowStockPanel();
            }
            return;
        }

        const markReadBtn = event.target.closest('[data-admin-low-stock-mark-read]');
        if (markReadBtn) {
            event.preventDefault();
            await markLowStockRead();
            const result = await pollNotifications(lastUnread);
            lastUnread = result.lastUnread;
            await loadLowStockPanel(true);
            panel?.classList.add('hidden');
            return;
        }

        if (root && panel && !root.contains(event.target)) {
            panel.classList.add('hidden');
        }
    });

    pollNotifications(lastUnread).then((result) => {
        lastUnread = result.lastUnread;
    });

    window.setInterval(async () => {
        const result = await pollNotifications(lastUnread);
        lastUnread = result.lastUnread;
    }, POLL_MS);
}

onPageLoad(initAdminNotifications);
