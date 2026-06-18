/**
 * Load GTM / GA / Facebook / TikTok after the page is interactive (not blocking paint).
 */

let loaded = false;

function injectScript(src, onload) {
    const script = document.createElement('script');
    script.async = true;
    script.src = src;
    if (onload) {
        script.onload = onload;
    }
    document.head.appendChild(script);
}

function loadGtm(id) {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
    injectScript(`https://www.googletagmanager.com/gtm.js?id=${encodeURIComponent(id)}`);
}

function loadGa(id) {
    injectScript(`https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(id)}`, () => {
        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function gtag() {
            window.dataLayer.push(arguments);
        };
        window.gtag('js', new Date());
        window.gtag('config', id);
    });
}

function loadFacebook(id) {
    if (window.fbq) {
        window.fbq('init', id);

        return;
    }

    const n = window.fbq = function fbq() {
        n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
    };

    if (!window._fbq) {
        window._fbq = n;
    }

    n.push = n;
    n.loaded = true;
    n.version = '2.0';
    n.queue = [];
    injectScript('https://connect.facebook.net/en_US/fbevents.js');
    window.fbq('init', id);
}

function loadTiktok(id) {
    const ttq = window.ttq = window.ttq || [];
    ttq.methods = ['page', 'track', 'identify', 'instances', 'debug', 'on', 'off', 'once', 'ready', 'alias', 'group', 'enableCookie', 'disableCookie'];
    ttq.setAndDefer = function setAndDefer(target, method) {
        target[method] = function deferred() {
            target.push([method].concat(Array.prototype.slice.call(arguments, 0)));
        };
    };

    for (let i = 0; i < ttq.methods.length; i++) {
        ttq.setAndDefer(ttq, ttq.methods[i]);
    }

    ttq.load = function load(pixelId) {
        const url = 'https://analytics.tiktok.com/i18n/pixel/events.js';
        ttq._i = ttq._i || {};
        ttq._i[pixelId] = [];
        ttq._i[pixelId]._u = url;
        ttq._t = ttq._t || {};
        ttq._t[pixelId] = +new Date();
        ttq._o = ttq._o || {};
        ttq._o[pixelId] = {};
        injectScript(`${url}?sdkid=${encodeURIComponent(pixelId)}&lib=ttq`);
    };

    ttq.load(id);
}

export function loadMarketingPixels() {
    if (loaded) {
        return;
    }

    loaded = true;

    const cfg = window.__MARKETING__ || {};

    if (cfg.gtm_id) {
        loadGtm(cfg.gtm_id);
    }

    if (cfg.ga_id) {
        loadGa(cfg.ga_id);
    }

    if (cfg.fb_pixel) {
        loadFacebook(cfg.fb_pixel);
    }

    if (cfg.tiktok_id) {
        loadTiktok(cfg.tiktok_id);
    }
}
