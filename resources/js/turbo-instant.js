/**
 * Next.js-like navigation: progress bar, instant feedback, aggressive prefetch.
 */

const PROGRESS_ID = 'turbo-progress';

function ensureProgressBar() {
    if (document.getElementById(PROGRESS_ID)) {
        return document.getElementById(PROGRESS_ID);
    }

    const bar = document.createElement('div');
    bar.id = PROGRESS_ID;
    bar.className = 'turbo-progress';
    bar.setAttribute('aria-hidden', 'true');
    document.body.appendChild(bar);

    return bar;
}

function setProgress(percent) {
    const bar = ensureProgressBar();
    bar.style.width = `${percent}%`;
    bar.style.opacity = percent > 0 ? '1' : '0';
}

function markInternalLinks(root = document) {
    root.querySelectorAll('a[href]').forEach((link) => {
        if (link.dataset.turboPrefetch === 'false') {
            return;
        }

        if (link.target === '_blank' || link.hasAttribute('download')) {
            return;
        }

        try {
            const url = new URL(link.href, window.location.origin);

            if (url.origin !== window.location.origin) {
                return;
            }

            if (/\.(pdf|zip|rar)$/i.test(url.pathname)) {
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

    document.addEventListener('turbo:click', () => {
        document.documentElement.classList.add('is-turbo-navigating');
        setProgress(35);
    });

    document.addEventListener('turbo:before-fetch-request', () => {
        setProgress(55);
    });

    document.addEventListener('turbo:before-render', () => {
        setProgress(85);
    });

    document.addEventListener('turbo:load', () => {
        document.documentElement.classList.remove('is-turbo-navigating');
        setProgress(100);
        window.setTimeout(() => setProgress(0), 180);
        markInternalLinks();
    });

    document.addEventListener('turbo:frame-load', () => markInternalLinks());
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTurboInstant, { once: true });
} else {
    initTurboInstant();
}

export { markInternalLinks };
