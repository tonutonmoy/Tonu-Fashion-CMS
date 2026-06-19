import * as Turbo from '@hotwired/turbo';

window.Turbo = Turbo;

if (Turbo.session?.drive) {
    Turbo.session.drive.preloadOnHover = false;
}

import './bootstrap';
import './turbo-instant';
import './marketing';
import './color-mode';
import './mobile';
import './cart';
import { onPageLoad } from './page-load';

function whenIdle(fn) {
    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(fn, { timeout: 2500 });
    } else {
        window.setTimeout(fn, 1200);
    }
}

function bootStorefront() {
    import('./search');

    if (document.getElementById('shop-filter-form') || document.getElementById('shop-results')) {
        import('./shop');
    }
    if (document.querySelector('[data-product-gallery]')) {
        import('./product-gallery');
    }
    if (document.getElementById('product-add-form')) {
        import('./product-variants');
    }
    if (document.querySelector('[data-hero-slider]')) {
        import('./hero-slider');
    }

    whenIdle(() => {
        import('./marketing-load').then(({ loadMarketingPixels }) => loadMarketingPixels());

        if (document.getElementById('support-chat-widget')) {
            import('./support-chat');
        }
    });

    if (document.querySelector('[data-lazy-section]')) {
        import('./home-lazy').then(({ initHomeLazySections }) => initHomeLazySections());
    }
}

onPageLoad(bootStorefront);

document.addEventListener('turbo:before-cache', () => {
    document.querySelectorAll('[data-turbo-temporary]').forEach((el) => el.remove());
    document.documentElement.classList.remove('is-turbo-navigating');
    document.body.classList.remove('overflow-hidden');
    document.getElementById('mobile-menu')?.classList.add('translate-x-full');
    document.getElementById('mobile-menu-overlay')?.classList.add('hidden');
    document.getElementById('shop-filter-panel')?.classList.remove('is-open');
    document.getElementById('shop-filter-overlay')?.classList.add('hidden');
    document.getElementById('cart-sidebar')?.classList.add('translate-x-full');
    document.getElementById('cart-sidebar-overlay')?.classList.add('hidden');
    document.querySelectorAll('[data-product-gallery]').forEach((gallery) => {
        delete gallery.dataset.swipeBound;
    });
});
