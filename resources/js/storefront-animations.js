import { onPageLoad } from './page-load';

const CARD_SELECTOR = '.theme-product-card, .theme-category-card, .theme-review-card, .theme-blog-card';
const SECTION_SELECTOR = '.theme-section:not(.theme-hero)';

const markVisible = (elements) => {
    elements.forEach((el) => el.classList.add('is-visible'));
};

const revealInViewport = (elements) => {
    const viewportBottom = window.innerHeight + 48;

    elements.forEach((el) => {
        const rect = el.getBoundingClientRect();

        if (rect.top < viewportBottom) {
            el.classList.add('is-visible');
        }
    });
};

const initStorefrontAnimations = () => {
    const cards = document.querySelectorAll(CARD_SELECTOR);
    const sections = document.querySelectorAll(SECTION_SELECTOR);

    if (!cards.length && !sections.length) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        markVisible(cards);
        markVisible(sections);
        return;
    }

    revealInViewport(cards);
    revealInViewport(sections);

    const pendingCards = [...cards].filter((el) => !el.classList.contains('is-visible'));
    const pendingSections = [...sections].filter((el) => !el.classList.contains('is-visible'));

    if (!pendingCards.length && !pendingSections.length) {
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.05, rootMargin: '0px 0px -16px 0px' });

    document.querySelectorAll('.theme-product-grid, .theme-blog-grid, .theme-category-grid').forEach((grid) => {
        grid.querySelectorAll(CARD_SELECTOR).forEach((card, index) => {
            if (card.classList.contains('is-visible')) {
                return;
            }

            card.style.setProperty('--reveal-delay', `${(index % 4) * 60}ms`);
            observer.observe(card);
        });
    });

    pendingSections.forEach((section, index) => {
        section.style.setProperty('--reveal-delay', `${Math.min(index, 3) * 40}ms`);
        observer.observe(section);
    });

    pendingCards.forEach((card) => {
        if (card.closest('.theme-product-grid, .theme-blog-grid, .theme-category-grid')) {
            return;
        }

        observer.observe(card);
    });
};

onPageLoad(initStorefrontAnimations);
