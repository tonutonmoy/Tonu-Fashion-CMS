import { onPageLoad } from './page-load';

const lockScroll = (lock) => {
    document.body.classList.toggle('overflow-hidden', lock);
};

const resetMobileSubmenus = (menu) => {
    menu?.querySelectorAll('[data-mobile-submenu-toggle]').forEach((btn) => {
        btn.setAttribute('aria-expanded', 'false');
        btn.classList.remove('is-open');
    });
    menu?.querySelectorAll('[data-mobile-submenu]').forEach((panel) => {
        panel.classList.add('hidden');
    });
};

const initMobileSubmenus = () => {
    const menu = document.getElementById('mobile-menu');
    if (!menu || menu.dataset.submenuBound === '1') {
        return;
    }
    menu.dataset.submenuBound = '1';

    menu.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-mobile-submenu-toggle]');
        if (!toggle) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const group = toggle.closest('[data-mobile-nav-group]');
        const panel = group?.querySelector('[data-mobile-submenu]');
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';

        menu.querySelectorAll('[data-mobile-nav-group]').forEach((otherGroup) => {
            if (otherGroup === group) {
                return;
            }

            const otherToggle = otherGroup.querySelector('[data-mobile-submenu-toggle]');
            const otherPanel = otherGroup.querySelector('[data-mobile-submenu]');
            otherToggle?.setAttribute('aria-expanded', 'false');
            otherToggle?.classList.remove('is-open');
            otherPanel?.classList.add('hidden');
        });

        toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        toggle.classList.toggle('is-open', !isOpen);
        panel?.classList.toggle('hidden', isOpen);
    });
};

const initMobileNav = () => {
    const toggle = document.getElementById('mobile-menu-toggle');
    const menu = document.getElementById('mobile-menu');
    const overlay = document.getElementById('mobile-menu-overlay');
    const closeBtn = document.getElementById('mobile-menu-close');

    if (!toggle || !menu) {
        return;
    }

    initMobileSubmenus();

    if (toggle.dataset.bound === '1') {
        return;
    }
    toggle.dataset.bound = '1';

    const close = () => {
        menu.classList.add('translate-x-full');
        overlay?.classList.add('hidden');
        menu.setAttribute('aria-hidden', 'true');
        overlay?.setAttribute('aria-hidden', 'true');
        resetMobileSubmenus(menu);
        lockScroll(false);
        toggle.setAttribute('aria-expanded', 'false');
    };

    const open = () => {
        menu.classList.remove('translate-x-full');
        overlay?.classList.remove('hidden');
        menu.setAttribute('aria-hidden', 'false');
        overlay?.setAttribute('aria-hidden', 'false');
        lockScroll(true);
        toggle.setAttribute('aria-expanded', 'true');
    };

    toggle.addEventListener('click', () => {
        if (menu.classList.contains('translate-x-full')) {
            open();
        } else {
            close();
        }
    });

    closeBtn?.addEventListener('click', close);
    overlay?.addEventListener('click', close);
    menu.querySelectorAll('a.mobile-nav-link:not(.mobile-nav-toggle)').forEach((link) => {
        link.addEventListener('click', close);
    });
};

const initShopFilterDrawer = () => {
    const toggle = document.getElementById('shop-filter-toggle');
    const panel = document.getElementById('shop-filter-panel');
    const overlay = document.getElementById('shop-filter-overlay');
    const closeBtn = document.getElementById('shop-filter-close');

    if (!toggle || !panel || toggle.dataset.bound) {
        return;
    }
    toggle.dataset.bound = '1';

    const close = () => {
        panel.classList.remove('is-open');
        overlay?.classList.add('hidden');
        lockScroll(false);
    };

    const open = () => {
        panel.classList.add('is-open');
        overlay?.classList.remove('hidden');
        lockScroll(true);
    };

    toggle.addEventListener('click', open);
    closeBtn?.addEventListener('click', close);
    overlay?.addEventListener('click', close);
};

const initAdminSidebar = () => {
    const toggle = document.getElementById('admin-sidebar-toggle');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');
    const closeBtn = document.getElementById('admin-sidebar-close');

    if (!toggle || !sidebar) {
        return;
    }

    const close = () => {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('is-open');
        overlay?.classList.add('hidden');
        lockScroll(false);
    };

    const open = () => {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('is-open');
        overlay?.classList.remove('hidden');
        if (document.body.classList.contains('builder-mode')) {
            lockScroll(false);
        } else {
            lockScroll(true);
        }
    };

    toggle.addEventListener('click', () => {
        if (sidebar.classList.contains('-translate-x-full')) {
            open();
        } else {
            close();
        }
    });

    overlay?.addEventListener('click', close);
    closeBtn?.addEventListener('click', close);
    sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', close));
};

const initProductSwipe = () => {
    const gallery = document.querySelector('[data-product-gallery]');
    const main = gallery?.querySelector('[data-gallery-main]');
    const thumbs = [...(gallery?.querySelectorAll('[data-gallery-thumb]') ?? [])];

    if (!main || thumbs.length < 2) {
        return;
    }

    let startX = 0;
    let currentIndex = 0;

    const show = (index) => {
        currentIndex = (index + thumbs.length) % thumbs.length;
        const thumb = thumbs[currentIndex];
        main.src = thumb.dataset.galleryThumb;
        main.alt = thumb.dataset.galleryAlt || '';
        thumbs.forEach((t, i) => t.classList.toggle('is-active', i === currentIndex));
    };

    gallery.addEventListener('touchstart', (e) => {
        startX = e.changedTouches[0].screenX;
    }, { passive: true });

    gallery.addEventListener('touchend', (e) => {
        const diff = e.changedTouches[0].screenX - startX;
        if (Math.abs(diff) < 40) {
            return;
        }
        show(diff < 0 ? currentIndex + 1 : currentIndex - 1);
    }, { passive: true });
};

const initMobileAtc = () => {
    const bar = document.querySelector('[data-mobile-atc]');
    const form = document.getElementById('product-add-form');
    if (!bar || !form) {
        return;
    }

    const addBtn = bar.querySelector('[data-mobile-atc-add]');
    if (!addBtn || addBtn.dataset.bound) {
        return;
    }
    addBtn.dataset.bound = '1';
    addBtn.addEventListener('click', () => {
        if (form.requestSubmit) {
            form.requestSubmit();
        } else {
            form.submit();
        }
    });
};

onPageLoad(() => {
    initMobileNav();
    initShopFilterDrawer();
    initAdminSidebar();
    initProductSwipe();
    initMobileAtc();
});
