function initHomeLazySections() {
    const sections = document.querySelectorAll('[data-lazy-section]');
    if (!sections.length) {
        return;
    }

    const loadSection = async (el) => {
        if (el.dataset.loaded === '1') {
            return;
        }

        const url = el.dataset.lazyUrl;
        if (!url) {
            return;
        }

        el.dataset.loaded = '1';

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                el.remove();
                return;
            }

            const data = await response.json();
            if (data.html && data.html.trim()) {
                el.innerHTML = data.html;
                el.dispatchEvent(new CustomEvent('home:section-loaded', { bubbles: true }));
            } else {
                el.remove();
            }
        } catch {
            el.dataset.loaded = '0';
        }
    };

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        loadSection(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            },
            { rootMargin: '400px 0px' }
        );

        sections.forEach((el) => observer.observe(el));
        // Eager-load first lazy block so products appear without scrolling
        if (sections[0]) {
            loadSection(sections[0]);
        }
    } else {
        sections.forEach((el) => loadSection(el));
    }
}

export { initHomeLazySections };
