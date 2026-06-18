/**
 * Fashion BD Marketing Events — client-side tracking with event_id deduplication
 */
(function () {
    const cfg = window.__MARKETING__ || {};

    function uuid() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            const r = Math.random() * 16 | 0;
            return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });
    }

    function pushDataLayer(event, data) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event, ...data });
    }

    window.FashionMarketing = {
        eventId: uuid,

        pageView: function () {
            const eventId = uuid();
            if (cfg.fb_pixel && window.fbq) fbq('track', 'PageView', {}, { eventID: eventId });
            if (cfg.ga_id && window.gtag) gtag('event', 'page_view', { send_to: cfg.ga_id });
            if (cfg.tiktok_id && window.ttq) ttq.page();
            pushDataLayer('page_view', { event_id: eventId });
            return eventId;
        },

        viewContent: function (product) {
            const eventId = uuid();
            const data = {
                content_ids: [product.sku],
                content_name: product.name,
                content_type: 'product',
                value: product.price,
                currency: 'BDT',
            };
            if (cfg.fb_pixel && window.fbq) fbq('track', 'ViewContent', data, { eventID: eventId });
            if (cfg.ga_id && window.gtag) gtag('event', 'view_item', { currency: 'BDT', value: product.price, items: [{ item_id: product.sku, item_name: product.name }] });
            if (cfg.tiktok_id && window.ttq) ttq.track('ViewContent', data);
            pushDataLayer('view_content', { ...data, event_id: eventId });
            return eventId;
        },

        addToCart: function (product, quantity, value) {
            const eventId = uuid();
            const data = {
                content_ids: [product.sku],
                content_name: product.name,
                content_type: 'product',
                value: value,
                currency: 'BDT',
                num_items: quantity,
            };
            if (cfg.fb_pixel && window.fbq) fbq('track', 'AddToCart', data, { eventID: eventId });
            if (cfg.ga_id && window.gtag) gtag('event', 'add_to_cart', { currency: 'BDT', value, items: [{ item_id: product.sku, quantity }] });
            if (cfg.tiktok_id && window.ttq) ttq.track('AddToCart', data);
            pushDataLayer('add_to_cart', { ...data, event_id: eventId });
            return eventId;
        },

        initiateCheckout: function (value, numItems, eventId) {
            eventId = eventId || uuid();
            const data = { value, currency: 'BDT', num_items: numItems };
            if (cfg.fb_pixel && window.fbq) fbq('track', 'InitiateCheckout', data, { eventID: eventId });
            if (cfg.ga_id && window.gtag) gtag('event', 'begin_checkout', { currency: 'BDT', value });
            pushDataLayer('initiate_checkout', { ...data, event_id: eventId });
            return eventId;
        },

        purchase: function (order) {
            const eventId = order.event_id || uuid();
            const data = {
                value: order.total,
                currency: 'BDT',
                content_ids: order.content_ids || [],
                content_type: 'product',
                num_items: order.num_items || 1,
                order_id: order.order_number,
            };
            if (cfg.fb_pixel && window.fbq) fbq('track', 'Purchase', data, { eventID: eventId });
            if (cfg.ga_id && window.gtag) gtag('event', 'purchase', { transaction_id: order.order_number, value: order.total, currency: 'BDT' });
            if (cfg.tiktok_id && window.ttq) ttq.track('CompletePayment', data);
            pushDataLayer('purchase', { ...data, event_id: eventId });
            return eventId;
        },
    };

    document.addEventListener('DOMContentLoaded', function () {
        if (window.FashionMarketing) FashionMarketing.pageView();
    });
})();
