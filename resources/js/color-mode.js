/**
 * Keeps color-mode class on <body> in sync when toggling without full reload quirks.
 */
function applyColorMode(mode) {
    const body = document.body;
    if (!body) {
        return;
    }

    body.classList.remove('theme-mode-light', 'theme-mode-dark');
    body.classList.add(`theme-mode-${mode}`);
    body.dataset.colorMode = mode;
}

document.addEventListener('DOMContentLoaded', () => {
    const mode = document.body?.dataset.colorMode || 'light';
    applyColorMode(mode);

    document.querySelectorAll('[data-color-mode-toggle]').forEach((link) => {
        link.addEventListener('click', () => {
            const href = link.getAttribute('href') || '';
            const match = href.match(/color-mode\/(light|dark)/);
            if (match) {
                applyColorMode(match[1]);
            }
        });
    });
});
