import { onPageLoad } from './page-load';

const initProductGallery = () => {
    const gallery = document.querySelector('[data-product-gallery]');
    if (!gallery) {
        return;
    }

    const main = gallery.querySelector('[data-gallery-main]');
    const zoomLens = gallery.querySelector('[data-gallery-zoom]');
    const thumbs = gallery.querySelectorAll('[data-gallery-thumb]');

    if (!main) {
        return;
    }

    const setMain = (src, alt) => {
        main.src = src;
        main.alt = alt || '';
        thumbs.forEach((thumb) => {
            thumb.classList.toggle('is-active', thumb.dataset.galleryThumb === src);
        });
    };

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            setMain(thumb.dataset.galleryThumb, thumb.dataset.galleryAlt);
        });
    });

    if (!zoomLens) {
        return;
    }

    const zoomInner = zoomLens.querySelector('[data-gallery-zoom-inner]');
    const scale = 2.2;

    const onMove = (e) => {
        const rect = main.getBoundingClientRect();
        const x = Math.max(0, Math.min(e.clientX - rect.left, rect.width));
        const y = Math.max(0, Math.min(e.clientY - rect.top, rect.height));
        const px = (x / rect.width) * 100;
        const py = (y / rect.height) * 100;

        if (zoomInner) {
            zoomInner.style.backgroundImage = `url('${main.src}')`;
            zoomInner.style.backgroundSize = `${rect.width * scale}px ${rect.height * scale}px`;
            zoomInner.style.backgroundPosition = `${px}% ${py}%`;
        }
    };

    gallery.addEventListener('mouseenter', () => zoomLens.classList.remove('hidden'));
    gallery.addEventListener('mouseleave', () => zoomLens.classList.add('hidden'));
    gallery.addEventListener('mousemove', onMove);
};

onPageLoad(initProductGallery);
