<?php if(request()->boolean('preview')): ?>
<style>
    html.is-builder-preview,
    body.is-builder-preview {
        min-width: 1280px;
    }
    body.is-builder-preview.is-preview-mobile {
        min-width: 390px;
    }
    body.is-builder-preview #mobile-menu,
    body.is-builder-preview #mobile-menu-overlay,
    body.is-builder-preview #mobile-menu-toggle {
        display: none !important;
    }
    body.is-builder-preview #cart-sidebar,
    body.is-builder-preview #cart-overlay {
        display: none !important;
    }
    body.is-builder-preview .theme-nav-dropdown + div {
        display: none !important;
    }
</style>
<script>
    document.documentElement.classList.add('is-builder-preview');

    function scrollToPreviewSection(id) {
        if (!id) return;
        var el = document.getElementById(id) || document.querySelector(id);
        if (!el) return;
        var top = el.getBoundingClientRect().top + window.scrollY;
        window.scrollTo({ top: Math.max(0, top - 8), behavior: 'auto' });
        el.scrollIntoView({ behavior: 'auto', block: 'start' });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.body.classList.add('is-builder-preview');
        if (new URLSearchParams(location.search).get('device') === 'mobile') {
            document.body.classList.add('is-preview-mobile');
        }
        var menu = document.getElementById('mobile-menu');
        var overlay = document.getElementById('mobile-menu-overlay');
        if (menu) menu.classList.add('translate-x-full');
        if (overlay) overlay.classList.add('hidden');
        var cart = document.getElementById('cart-sidebar');
        if (cart) cart.classList.add('translate-x-full');

        if (location.hash) {
            var sectionId = location.hash.replace(/^#/, '');
            scrollToPreviewSection(sectionId);
            setTimeout(function () { scrollToPreviewSection(sectionId); }, 250);
            setTimeout(function () { scrollToPreviewSection(sectionId); }, 900);
        }

        if (window.initHeroSlider) {
            setTimeout(function () { window.initHeroSlider(); }, 150);
            setTimeout(function () { window.initHeroSlider(); }, 800);
        }
    });
</script>
<?php endif; ?>
<?php /**PATH D:\PROGECTS\LARAVEL-PHP\E-mommarz\Fashion-Templete\resources\views/themes/shared/partials/builder-preview.blade.php ENDPATH**/ ?>