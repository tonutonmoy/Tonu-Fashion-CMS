import { onPageLoad } from './page-load';

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content;

function formatBdt(n, freeLabel) {
    if (n === 0) return freeLabel;
    return '৳' + Number(n).toLocaleString('en-BD', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

function initCheckout() {
    const form = document.getElementById('checkout-form');
    const lineItems = document.getElementById('checkout-line-items');
    if (!form || !lineItems || form.dataset.checkoutBound) return;
    form.dataset.checkoutBound = '1';

    const freeLabel = form.dataset.freeLabel || 'Free';
    const cartUrl = form.dataset.cartUrl || '/cart';
    const shippingQuoteUrl = form.dataset.shippingQuoteUrl || '/checkout/shipping-quote';
    const requiresDelivery = form.dataset.requiresDelivery === '1';
    const checkoutEventId = form.dataset.checkoutEventId || '';
    const initialSubtotal = Number(form.dataset.subtotal || 0);
    const initialQty = Number(form.dataset.itemCount || 0);

    function cookie(name) {
        const m = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return m ? decodeURIComponent(m[2]) : '';
    }

    const fbp = document.getElementById('fbp');
    const fbc = document.getElementById('fbc');
    if (fbp) fbp.value = cookie('_fbp');
    if (fbc) fbc.value = cookie('_fbc');

    if (window.FashionMarketing && checkoutEventId) {
        FashionMarketing.initiateCheckout(initialSubtotal, initialQty, checkoutEventId);
    }

    form.addEventListener('submit', () => {
        if (window.FashionMarketing) {
            const purchaseId = document.getElementById('purchase_event_id');
            if (purchaseId) purchaseId.value = FashionMarketing.eventId();
        }
        if (fbp) fbp.value = cookie('_fbp');
        if (fbc) fbc.value = cookie('_fbc');
    });

    function selectedZone() {
        return form.querySelector('input[name="delivery_zone"]:checked')?.value || '';
    }

    function updateTotals(data) {
        const subtotalEl = document.getElementById('summary-subtotal');
        const shippingEl = document.getElementById('summary-shipping');
        const totalEl = document.getElementById('summary-total');
        const mobileTotal = document.getElementById('summary-total-mobile');
        const label = document.getElementById('shipping-label');

        if (subtotalEl) subtotalEl.textContent = data.subtotal_label || formatBdt(data.subtotal, freeLabel);
        if (shippingEl) shippingEl.textContent = formatBdt(data.shipping, freeLabel);
        if (totalEl) totalEl.textContent = formatBdt(data.total, freeLabel);
        if (mobileTotal) mobileTotal.textContent = formatBdt(data.total, freeLabel);

        if (label) {
            label.textContent = data.shipping_label && data.shipping > 0
                ? `(${data.shipping_label})`
                : data.free_delivery_reason ? `(${freeLabel})` : '';
        }

        document.querySelectorAll('[data-cart-count]').forEach((el) => {
            el.textContent = data.count ?? 0;
            el.classList.toggle('hidden', !data.count);
        });
    }

    function renderLineItems(items) {
        lineItems.innerHTML = items.map((item) => `
            <li class="checkout-line-item" data-checkout-item="${item.id}">
                <div class="checkout-line-thumb-wrap">
                    ${item.image
                        ? `<img src="${item.image}" alt="" class="checkout-line-thumb">`
                        : '<div class="checkout-line-thumb checkout-line-thumb--empty"></div>'}
                    <span class="checkout-line-qty" data-checkout-qty-badge>${item.quantity}</span>
                </div>
                <div class="checkout-line-info">
                    <p class="checkout-line-name">${item.name}</p>
                    ${item.variant ? `<p class="checkout-line-variant text-xs text-gray-500">${item.variant}</p>` : ''}
                    ${item.free_delivery ? `<span class="checkout-line-badge">${form.dataset.freeDeliveryLabel || 'Free delivery'}</span>` : ''}
                    <div class="checkout-line-qty-controls" aria-label="${form.dataset.quantityLabel || 'Quantity'}">
                        <button type="button" class="checkout-qty-btn" data-checkout-qty="dec" data-id="${item.id}" aria-label="-">−</button>
                        <span class="checkout-qty-value" data-checkout-qty-value>${item.quantity}</span>
                        <button type="button" class="checkout-qty-btn" data-checkout-qty="inc" data-id="${item.id}" aria-label="+">+</button>
                    </div>
                </div>
                <span class="checkout-line-price" data-checkout-line-total>${item.line_total_label}</span>
            </li>
        `).join('');
    }

    async function refreshSummary() {
        const params = new URLSearchParams();
        const zone = selectedZone();
        if (zone) params.set('delivery_zone', zone);

        const res = await fetch(`${shippingQuoteUrl}?${params}`, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();

        if (data.empty) {
            if (window.Turbo) {
                window.Turbo.visit(cartUrl);
            } else {
                window.location.href = cartUrl;
            }
            return;
        }

        renderLineItems(data.items || []);
        updateTotals(data);
    }

    async function updateItemQuantity(id, quantity) {
        const res = await fetch(`/api/cart/${id}`, {
            method: 'PATCH',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({ quantity }),
        });

        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            throw new Error(data.message || 'Could not update quantity.');
        }

        await refreshSummary();
    }

    lineItems.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-checkout-qty]');
        if (!btn) return;

        e.preventDefault();
        const row = btn.closest('[data-checkout-item]');
        const qtyEl = row?.querySelector('[data-checkout-qty-value]');
        let qty = parseInt(qtyEl?.textContent || '1', 10);
        qty = btn.dataset.checkoutQty === 'inc' ? qty + 1 : qty - 1;

        if (qty > 99) return;

        btn.disabled = true;
        try {
            await updateItemQuantity(btn.dataset.id, qty);
        } catch (err) {
            alert(err.message || 'Could not update quantity.');
        } finally {
            btn.disabled = false;
        }
    });

    form.querySelectorAll('input[name="delivery_zone"]').forEach((radio) => {
        radio.addEventListener('change', refreshSummary);
    });

    if (requiresDelivery) {
        refreshSummary();
    }
}

onPageLoad(initCheckout);
