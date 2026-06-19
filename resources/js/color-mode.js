import { onPageLoad } from './page-load';

const MODES = ['light', 'dark'];

function readCookieMode() {
    const match = document.cookie.match(/(?:^|;\s*)color_mode=([^;]+)/);

    if (!match) {
        return null;
    }

    const mode = decodeURIComponent(match[1]);

    return MODES.includes(mode) ? mode : null;
}

export function applyColorMode(mode) {
    const body = document.body;

    if (!body || !MODES.includes(mode)) {
        return;
    }

    body.classList.remove('theme-mode-light', 'theme-mode-dark');
    body.classList.add(`theme-mode-${mode}`);
    body.dataset.colorMode = mode;
}

function resolveColorMode() {
    return readCookieMode() || document.body?.dataset.colorMode || 'light';
}

function syncColorMode() {
    applyColorMode(resolveColorMode());
}

async function toggleColorMode(targetMode) {
    applyColorMode(targetMode);

    try {
        await fetch(`/preferences/color-mode/${targetMode}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: { Accept: 'text/html' },
        });
    } catch {
        // Keep local mode even if cookie sync fails.
    }
}

function bindColorModeToggles(root = document) {
    root.querySelectorAll('[data-color-mode-toggle]').forEach((link) => {
        if (link.dataset.colorModeBound === '1') {
            return;
        }

        link.dataset.colorModeBound = '1';

        link.addEventListener('click', (event) => {
            event.preventDefault();

            const target = link.dataset.colorModeTarget
                || link.getAttribute('href')?.match(/color-mode\/(light|dark)/)?.[1];

            if (target) {
                toggleColorMode(target);
            }
        });
    });
}

onPageLoad(() => {
    syncColorMode();
    bindColorModeToggles();
});

document.addEventListener('turbo:before-cache', () => {
    syncColorMode();
    document.getElementById('turbo-progress')?.remove();
});
