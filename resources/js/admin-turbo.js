import * as Turbo from '@hotwired/turbo';

window.Turbo = Turbo;

if (Turbo.session?.drive) {
    Turbo.session.drive.preloadOnHover = false;
}

document.addEventListener('turbo:before-cache', () => {
    document.getElementById('admin-confirm-modal')?.classList.add('hidden');
});

document.addEventListener('turbo:load', () => {
    document.documentElement.classList.remove('is-turbo-navigating');
});

document.addEventListener('turbo:before-visit', () => {
    document.documentElement.classList.add('is-turbo-navigating');
});
