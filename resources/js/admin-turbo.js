import * as Turbo from '@hotwired/turbo';
import { markInternalLinks } from './turbo-instant';

window.Turbo = Turbo;

if (Turbo.session?.drive) {
    Turbo.session.drive.preloadOnHover = true;
}

function ensureProgressBar() {
    if (document.getElementById('admin-turbo-progress')) {
        return;
    }

    const bar = document.createElement('div');
    bar.id = 'admin-turbo-progress';
    bar.className = 'admin-turbo-progress';
    bar.setAttribute('aria-hidden', 'true');
    document.body.appendChild(bar);
}

function activateProgress() {
    ensureProgressBar();
    document.getElementById('admin-turbo-progress')?.classList.add('is-active');
}

function deactivateProgress() {
    document.getElementById('admin-turbo-progress')?.classList.remove('is-active');
}

function bootAdminTurbo() {
    if (!document.body.classList.contains('admin-body')) {
        return;
    }

    if (document.body.closest('[data-turbo="false"]') || document.body.dataset.turbo === 'false') {
        return;
    }

    markInternalLinks();
}

document.addEventListener('turbo:visit', activateProgress);
document.addEventListener('turbo:submit-start', activateProgress);
document.addEventListener('turbo:load', () => {
    deactivateProgress();
    bootAdminTurbo();
});
document.addEventListener('turbo:submit-end', deactivateProgress);

document.addEventListener('turbo:before-cache', () => {
    document.getElementById('admin-loading')?.classList.add('hidden');
    document.getElementById('admin-confirm-modal')?.classList.add('hidden');
    deactivateProgress();
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootAdminTurbo, { once: true });
} else {
    bootAdminTurbo();
}
