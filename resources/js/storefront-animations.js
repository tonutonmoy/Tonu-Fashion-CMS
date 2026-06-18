import { onPageLoad } from './page-load';

/**
 * Scroll-reveal animations for storefront cards and sections.
 */
const CARD_SELECTOR = '.theme-product-card, .theme-category-card, .theme-review-card, .theme-blog-card';
const SECTION_SELECTOR = '.theme-section:not(.theme-hero)';

const markVisible = (elements) => {
    elements.forEach((el) => el.classList.add('is-visible'));
};

const initStorefrontAnimations = () => {
    const cards = document.querySelectorAll(CARD_SELECTOR);
    const sections = document.querySelectorAll(SECTION_SELECTOR);

    if (!cards.length && !sections.length) return;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        markVisible(cards);
        markVisible(sections);
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -32px 0px' });

    document.querySelectorAll('.theme-product-grid, .theme-blog-grid, .theme-category-grid').forEach((grid) => {
        grid.querySelectorAll(CARD_SELECTOR).forEach((card, index) => {
            card.style.setProperty('--reveal-delay', `${(index % 4) * 90}ms`);
            observer.observe(card);
        });
    });

    document.querySelectorAll('.theme-review-card').forEach((card, index) => {
        if (card.closest('.theme-product-grid, .theme-blog-grid, .theme-category-grid')) return;
        card.style.setProperty('--reveal-delay', `${(index % 4) * 90}ms`);
        observer.observe(card);
    });

    document.querySelectorAll(`${CARD_SELECTOR}:not(.is-visible)`).forEach((card) => {
        if (card.closest('.theme-product-grid, .theme-blog-grid, .theme-category-grid')) return;
        observer.observe(card);
    });

    sections.forEach((section, index) => {
        section.style.setProperty('--reveal-delay', `${Math.min(index, 3) * 50}ms`);
        observer.observe(section);
    });
};

onPageLoad(initStorefrontAnimations);
