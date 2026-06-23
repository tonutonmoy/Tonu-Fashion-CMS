/**
 * Lightweight Turbo helpers — no top progress bar or page fade.
 */

function markInternalLinks(root = document) {
    if (document.body?.classList.contains('admin-body')) {
        return;
    }

    root.querySelectorAll('a[href]').forEach((link) => {
        if (link.target === '_blank' || link.hasAttribute('download')) {
            return;
        }

        try {
            const url = new URL(link.href, window.location.origin);

            if (url.origin !== window.location.origin) {
                return;
            }

            if (/\.(pdf|zip|rar|csv|xlsx)$/i.test(url.pathname)) {
                return;
            }

            if (link.dataset.turbo === 'false') {
                return;
            }

            link.setAttribute('data-turbo-preload', '');
        } catch {
            // ignore invalid URLs
        }
    });
}

function initTurboInstant() {
    markInternalLinks();
    document.addEventListener('turbo:load', () => markInternalLinks());
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTurboInstant, { once: true });
} else {
    initTurboInstant();
}

export { markInternalLinks };
