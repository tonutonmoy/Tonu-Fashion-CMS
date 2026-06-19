import { onPageLoad } from './page-load';

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content;

const cartApi = {
    async get() {
        const res = await fetch('/api/cart', { headers: { Accept: 'application/json' } });
        return res.json();
    },
    async add(formData) {
        const res = await fetch('/api/cart', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: formData,
        });
        return res.json();
    },
    async update(id, quantity) {
        const res = await fetch(`/api/cart/${id}`, {
            method: 'PATCH',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({ quantity }),
        });
        return res.json();
    },
    async remove(id) {
        const res = await fetch(`/api/cart/${id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
        });
        return res.json();
    },
};

const renderCartSidebar = (data) => {
    const sidebar = document.getElementById('cart-sidebar');
    const overlay = document.getElementById('cart-sidebar-overlay');
    const itemsEl = document.getElementById('cart-sidebar-items');
    const subtotalEl = document.getElementById('cart-sidebar-subtotal');
    const countEls = document.querySelectorAll('[data-cart-count]');

    if (!sidebar || !itemsEl) {
        return;
    }

    countEls.forEach((el) => {
        el.textContent = data.count;
        el.classList.toggle('hidden', data.count === 0);
    });

    if (data.items.length === 0) {
        itemsEl.innerHTML = '<p class="text-sm text-gray-500 py-8 text-center">Your cart is empty.</p>';
    } else {
        itemsEl.innerHTML = data.items.map((item) => `
            <div class="flex gap-3 py-3 border-b border-gray-100" data-cart-item="${item.id}">
                <img src="${item.image || ''}" alt="" class="w-16 h-20 object-cover rounded-lg bg-gray-100">
                <div class="flex-1 min-w-0">
                    <a href="/products/${item.slug}" class="font-medium text-sm line-clamp-2 hover:text-brand-600">${item.name}</a>
                    ${item.variant ? `<p class="text-xs text-gray-500">${item.variant}</p>` : ''}
                    <p class="text-sm font-semibold mt-1">${item.price_label}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <button type="button" class="cart-qty-btn text-gray-500 hover:text-gray-900" data-action="dec" data-id="${item.id}">−</button>
                        <span class="text-sm w-6 text-center">${item.quantity}</span>
                        <button type="button" class="cart-qty-btn text-gray-500 hover:text-gray-900" data-action="inc" data-id="${item.id}">+</button>
                        <button type="button" class="ml-auto text-xs text-red-600 cart-remove-btn" data-id="${item.id}">Remove</button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    if (subtotalEl) {
        subtotalEl.textContent = data.subtotal_label;
    }

    sidebar.classList.remove('translate-x-full');
    overlay?.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
};

const closeCartSidebar = () => {
    document.getElementById('cart-sidebar')?.classList.add('translate-x-full');
    document.getElementById('cart-sidebar-overlay')?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
};

const bindCartSidebarEvents = () => {
    if (document.documentElement.dataset.cartDelegated === '1') {
        refreshCartCount();
        return;
    }
    document.documentElement.dataset.cartDelegated = '1';

    document.addEventListener('click', async (e) => {
        const buyBtn = e.target.closest('[data-buy-now]');
        if (buyBtn) {
            e.preventDefault();
            await buyNow(buyBtn);
            return;
        }

        const openBtn = e.target.closest('[data-open-cart]');
        if (openBtn) {
            e.preventDefault();
            const data = await cartApi.get();
            renderCartSidebar(data);
            return;
        }

        if (e.target.closest('#cart-sidebar-close') || e.target.closest('#cart-sidebar-overlay')) {
            closeCartSidebar();
            return;
        }

        const btn = e.target.closest('#cart-sidebar-items button');
        if (!btn) return;

        const id = btn.dataset.id;
        const row = btn.closest('[data-cart-item]');
        const qtyEl = row?.querySelector('span.text-sm.w-6');

        if (btn.classList.contains('cart-remove-btn')) {
            const data = await cartApi.remove(id);
            renderCartSidebar(data);
            return;
        }

        if (btn.classList.contains('cart-qty-btn')) {
            let qty = parseInt(qtyEl?.textContent || '1', 10);
            qty = btn.dataset.action === 'inc' ? qty + 1 : qty - 1;
            const data = await cartApi.update(id, qty);
            renderCartSidebar(data);
        }
    });

    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('form[data-add-to-cart]');
        if (!form) return;
        e.preventDefault();
        const formData = new FormData(form);
        const data = await cartApi.add(formData);
        renderCartSidebar(data);
        refreshCartCount(data);
    });
};

const refreshCartCount = (data) => {
    if (data && typeof data.count === 'number') {
        document.querySelectorAll('[data-cart-count]').forEach((el) => {
            el.textContent = data.count;
            el.classList.toggle('hidden', data.count === 0);
        });
        return;
    }

    cartApi.get().then((payload) => refreshCartCount(payload));
};

const redirectToCheckout = (url) => {
    const target = url || '/checkout';
    if (window.Turbo) {
        window.Turbo.visit(target);
    } else {
        window.location.href = target;
    }
};

const getProductForm = (trigger) => trigger?.closest('form') || document.getElementById('product-add-form');

const buyNow = async (trigger) => {
    const form = getProductForm(trigger);
    if (!form) return;

    const variantInput = form.querySelector('[data-variant-id]');
    if (variantInput && !variantInput.value) {
        alert('Please select size and color.');
        return;
    }

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const checkoutUrl = trigger?.dataset.checkoutUrl || '/checkout';
    const formData = new FormData(form);

    try {
        const res = await fetch('/api/cart', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: formData,
        });
        const data = await res.json();
        if (!res.ok) {
            throw new Error(data.message || 'Could not add to cart.');
        }
        refreshCartCount(data);
        redirectToCheckout(trigger?.dataset.checkoutUrl || data.checkout_url);
    } catch (err) {
        alert(err.message || 'Could not add to cart.');
    }
};

onPageLoad(() => {
    bindCartSidebarEvents();
});

window.FashionCart = { open: async () => renderCartSidebar(await cartApi.get()), close: closeCartSidebar };
