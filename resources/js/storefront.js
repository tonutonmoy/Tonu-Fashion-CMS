import * as Turbo from '@hotwired/turbo';

window.Turbo = Turbo;

import './bootstrap';
import './marketing';
import './color-mode';
import { onPageLoad } from './page-load';

function bootStorefront() {
    import('./mobile');
    import('./cart');
    import('./search');
    import('./support-chat');

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
    if (document.querySelector('.theme-product-card, .theme-section')) {
        import('./storefront-animations');
    }
}

onPageLoad(bootStorefront);

document.addEventListener('turbo:before-cache', () => {
    document.querySelectorAll('[data-turbo-temporary]').forEach((el) => el.remove());
});
