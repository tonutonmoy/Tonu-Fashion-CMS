import * as Turbo from '@hotwired/turbo';

window.Turbo = Turbo;

if (Turbo.session?.drive && typeof Turbo.session.drive === 'object') {
    Turbo.session.drive.preloadOnHover = true;
}

import './bootstrap';
import './turbo-instant';
import './marketing';
import './color-mode';
import './mobile';
import './cart';
import './hero-slider';
import { initSupportChat } from './support-chat';
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

    if (document.getElementById('support-chat-widget')) {
        initSupportChat();
    }

    if (document.getElementById('shop-filter-form') || document.getElementById('shop-results')) {
        import('./shop');
    }
    if (document.querySelector('[data-product-gallery]')) {
        import('./product-gallery');
    }
    if (document.getElementById('product-add-form')) {
        import('./product-variants');
    }
    if (document.querySelector('[data-flash-countdown]')) {
        import('./flash-sale');
    }

    whenIdle(() => {
        import('./marketing-load').then(({ loadMarketingPixels }) => loadMarketingPixels());
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
    document.querySelectorAll('[data-hero-slider]').forEach((el) => {
        delete el.dataset.heroReady;
    });
    document.querySelectorAll('[data-flash-countdown]').forEach((el) => {
        delete el.dataset.flashReady;
    });
    document.getElementById('support-chat-widget')?.removeAttribute('data-chat-ready');
});
