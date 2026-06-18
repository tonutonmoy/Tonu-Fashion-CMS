document.addEventListener('DOMContentLoaded', function () {
    const countdown = document.getElementById('flash-countdown');
    if (countdown) {
        const end = countdown.closest('.theme-countdown')?.dataset.end;
        if (end) {
            const tick = () => {
                const diff = new Date(end) - new Date();
                if (diff <= 0) { countdown.textContent = 'Ended'; return; }
                const d = Math.floor(diff / 86400000);
                const h = Math.floor((diff % 86400000) / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                countdown.textContent = `${d}d ${h}h ${m}m ${s}s`;
            };
            tick();
            setInterval(tick, 1000);
        }
    }

    const transparentHeader = document.querySelector('.theme-header-transparent');
    if (transparentHeader) {
        const hasHero = document.body.classList.contains('has-home-hero');
        const onScroll = () => {
            transparentHeader.classList.toggle('is-scrolled', !hasHero || window.scrollY > 24);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    }
});
