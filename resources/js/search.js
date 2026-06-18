import { onPageLoad } from './page-load';

const initHeaderSearch = () => {
    document.querySelectorAll('[data-header-search]').forEach((form) => {
        const input = form.querySelector('input[name="q"]');
        if (!input || input.dataset.searchBound) {
            return;
        }
        input.dataset.searchBound = '1';

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = input.value.trim();
                const base = form.getAttribute('action') || '/shop';
                const url = query ? `${base}?q=${encodeURIComponent(query)}` : base;
                if (window.Turbo) {
                    window.Turbo.visit(url);
                } else {
                    window.location.href = url;
                }
            }
        });
    });
};

onPageLoad(initHeaderSearch);
