/**
 * Homepage hero carousel — auto-rotate, arrows, dots.
 */
import { onPageLoad } from './page-load';

const initHeroSlider = () => {
    document.querySelectorAll('[data-hero-slider]').forEach((slider) => {
        if (slider.dataset.heroReady === '1') {
            return;
        }
        slider.dataset.heroReady = '1';

        const slides = [...slider.querySelectorAll('[data-hero-slide]')];
        if (slides.length === 0) {
            return;
        }

        let current = slides.findIndex((s) => s.classList.contains('is-active'));
        if (current < 0) current = 0;

        const dots = [...slider.querySelectorAll('[data-hero-dot]')];
        const interval = parseInt(slider.dataset.autoplay || '5000', 10) || 5000;
        let timer = null;

        const show = (index) => {
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, i) => slide.classList.toggle('is-active', i === current));
            dots.forEach((dot, i) => dot.classList.toggle('is-active', i === current));
        };

        const next = () => show(current + 1);
        const prev = () => show(current - 1);

        const startAuto = () => {
            if (slides.length <= 1) {
                return;
            }
            stopAuto();
            timer = window.setInterval(next, interval);
        };

        const stopAuto = () => {
            if (timer) window.clearInterval(timer);
            timer = null;
        };

        slider.querySelector('[data-hero-next]')?.addEventListener('click', () => {
            next();
            startAuto();
        });

        slider.querySelector('[data-hero-prev]')?.addEventListener('click', () => {
            prev();
            startAuto();
        });

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                show(parseInt(dot.dataset.heroDot, 10));
                startAuto();
            });
        });

        slider.addEventListener('mouseenter', stopAuto);
        slider.addEventListener('mouseleave', startAuto);

        show(current);
        startAuto();
    });
};

onPageLoad(initHeroSlider);
