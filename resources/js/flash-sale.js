import { onPageLoad } from './page-load';

const tickCountdown = (root) => {
    const end = root.dataset.end;
    const display = root.querySelector('[data-flash-countdown-display]');
    if (!end || !display) return;

    const diff = new Date(end).getTime() - Date.now();
    if (diff <= 0) {
        display.textContent = 'Ended';
        return;
    }

    const d = Math.floor(diff / 86400000);
    const h = Math.floor((diff % 86400000) / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);
    display.textContent = `${d}d ${h}h ${m}m ${s}s`;
};

const initFlashCountdown = () => {
    document.querySelectorAll('[data-flash-countdown]').forEach((root) => {
        if (root.dataset.flashReady === '1') return;
        root.dataset.flashReady = '1';
        tickCountdown(root);
        window.setInterval(() => tickCountdown(root), 1000);
    });
};

onPageLoad(initFlashCountdown);
