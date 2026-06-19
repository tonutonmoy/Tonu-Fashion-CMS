import { onPageLoad } from './page-load';

const lockScroll = (lock) => {
    document.body.classList.toggle('overflow-hidden', lock);
};

const getMobileMenu = () => document.getElementById('mobile-menu');

const isMobileMenuOpen = () => {
    const menu = getMobileMenu();

    return menu ? !menu.classList.contains('translate-x-full') : false;
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

const closeMobileMenu = () => {
    const menu = getMobileMenu();
    const overlay = document.getElementById('mobile-menu-overlay');
    const toggle = document.getElementById('mobile-menu-toggle');

    menu?.classList.add('translate-x-full');
    overlay?.classList.add('hidden');
    menu?.setAttribute('aria-hidden', 'true');
    overlay?.setAttribute('aria-hidden', 'true');
    resetMobileSubmenus(menu);
    lockScroll(false);
    toggle?.setAttribute('aria-expanded', 'false');
};

const openMobileMenu = () => {
    const menu = getMobileMenu();
    const overlay = document.getElementById('mobile-menu-overlay');
    const toggle = document.getElementById('mobile-menu-toggle');

    menu?.classList.remove('translate-x-full');
    overlay?.classList.remove('hidden');
    menu?.setAttribute('aria-hidden', 'false');
    overlay?.setAttribute('aria-hidden', 'false');
    lockScroll(true);
    toggle?.setAttribute('aria-expanded', 'true');
};

const bindMobileInteractions = () => {
    if (document.documentElement.dataset.mobileDelegated === '1') {
        return;
    }

    document.documentElement.dataset.mobileDelegated = '1';

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('#mobile-menu-toggle');
        if (toggle) {
            event.preventDefault();
            if (isMobileMenuOpen()) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
            return;
        }

        if (event.target.closest('#mobile-menu-close') || event.target.closest('#mobile-menu-overlay')) {
            event.preventDefault();
            closeMobileMenu();
            return;
        }

        const submenuToggle = event.target.closest('[data-mobile-submenu-toggle]');
        if (submenuToggle) {
            event.preventDefault();
            event.stopPropagation();

            const menu = getMobileMenu();
            const group = submenuToggle.closest('[data-mobile-nav-group]');
            const panel = group?.querySelector('[data-mobile-submenu]');
            const isOpen = submenuToggle.getAttribute('aria-expanded') === 'true';

            menu?.querySelectorAll('[data-mobile-nav-group]').forEach((otherGroup) => {
                if (otherGroup === group) {
                    return;
                }

                const otherToggle = otherGroup.querySelector('[data-mobile-submenu-toggle]');
                const otherPanel = otherGroup.querySelector('[data-mobile-submenu]');
                otherToggle?.setAttribute('aria-expanded', 'false');
                otherToggle?.classList.remove('is-open');
                otherPanel?.classList.add('hidden');
            });

            submenuToggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            submenuToggle.classList.toggle('is-open', !isOpen);
            panel?.classList.toggle('hidden', isOpen);
            return;
        }

        const mobileLink = event.target.closest('#mobile-menu a.mobile-nav-link:not(.mobile-nav-toggle)');
        if (mobileLink) {
            closeMobileMenu();
        }

        const filterToggle = event.target.closest('#shop-filter-toggle');
        if (filterToggle) {
            event.preventDefault();
            document.getElementById('shop-filter-panel')?.classList.add('is-open');
            document.getElementById('shop-filter-overlay')?.classList.remove('hidden');
            lockScroll(true);
            return;
        }

        if (event.target.closest('#shop-filter-close') || event.target.closest('#shop-filter-overlay')) {
            document.getElementById('shop-filter-panel')?.classList.remove('is-open');
            document.getElementById('shop-filter-overlay')?.classList.add('hidden');
            lockScroll(false);
        }
    });

    document.addEventListener('click', (event) => {
        const addBtn = event.target.closest('[data-mobile-atc-add]');
        if (!addBtn) {
            return;
        }

        event.preventDefault();
        const form = document.getElementById('product-add-form');
        if (!form) {
            return;
        }

        if (form.requestSubmit) {
            form.requestSubmit();
        } else {
            form.submit();
        }
    });
};

const bindAdminSidebar = () => {
    if (document.documentElement.dataset.adminSidebarDelegated === '1') {
        return;
    }

    document.documentElement.dataset.adminSidebarDelegated = '1';

    const close = () => {
        const sidebar = document.getElementById('admin-sidebar');
        sidebar?.classList.add('-translate-x-full');
        sidebar?.classList.remove('is-open');
        document.getElementById('admin-sidebar-overlay')?.classList.add('hidden');
        lockScroll(false);
    };

    const open = () => {
        const sidebar = document.getElementById('admin-sidebar');
        sidebar?.classList.remove('-translate-x-full');
        sidebar?.classList.add('is-open');
        document.getElementById('admin-sidebar-overlay')?.classList.remove('hidden');
        if (document.body.classList.contains('builder-mode')) {
            lockScroll(false);
        } else {
            lockScroll(true);
        }
    };

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('#admin-sidebar-toggle');
        if (toggle) {
            event.preventDefault();
            const sidebar = document.getElementById('admin-sidebar');
            if (sidebar?.classList.contains('-translate-x-full')) {
                open();
            } else {
                close();
            }
            return;
        }

        if (event.target.closest('#admin-sidebar-overlay') || event.target.closest('#admin-sidebar-close')) {
            close();
            return;
        }

        if (event.target.closest('#admin-sidebar a')) {
            close();
        }
    });
};

const initProductSwipe = () => {
    const gallery = document.querySelector('[data-product-gallery]');
    const main = gallery?.querySelector('[data-gallery-main]');
    const thumbs = [...(gallery?.querySelectorAll('[data-gallery-thumb]') ?? [])];

    if (!main || thumbs.length < 2 || gallery.dataset.swipeBound === '1') {
        return;
    }

    gallery.dataset.swipeBound = '1';

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

onPageLoad(() => {
    bindMobileInteractions();
    bindAdminSidebar();
    initProductSwipe();
});

export { closeMobileMenu, openMobileMenu };
