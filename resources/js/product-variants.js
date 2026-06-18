import { onPageLoad } from './page-load';

const initProductVariants = () => {
    const root = document.querySelector('[data-product-variants]');
    if (!root) {
        return;
    }

    const variants = JSON.parse(root.dataset.variants || '[]');
    const hiddenInput = root.querySelector('[data-variant-id]');
    const priceEl = root.querySelector('[data-variant-price]');
    const sizeGroup = root.querySelector('[data-size-group]');
    const colorGroup = root.querySelector('[data-color-group]');

    let selectedSize = null;
    let selectedColor = null;

    const sizes = [...new Set(variants.map((v) => v.size).filter(Boolean))];
    const colors = [...new Set(variants.map((v) => v.color).filter(Boolean))];

    const renderButtons = (container, values, type) => {
        if (!container || values.length === 0) {
            return;
        }

        container.innerHTML = values.map((value) => `
            <button type="button" class="variant-pick-btn" data-pick-type="${type}" data-pick-value="${value}">${value}</button>
        `).join('');
    };

    renderButtons(sizeGroup, sizes, 'size');
    renderButtons(colorGroup, colors, 'color');

    const findVariant = () => variants.find((v) => {
        const sizeOk = !selectedSize || v.size === selectedSize;
        const colorOk = !selectedColor || v.color === selectedColor;

        return sizeOk && colorOk;
    });

    const updateSelection = () => {
        const match = findVariant();

        if (match && hiddenInput) {
            hiddenInput.value = match.id;
        }

        if (priceEl && match) {
            priceEl.textContent = match.price_label;
        }

        if (match?.image) {
            const main = document.querySelector('[data-gallery-main]');
            if (main) {
                main.src = match.image;
            }
        }

        root.querySelectorAll('.variant-pick-btn').forEach((btn) => {
            const type = btn.dataset.pickType;
            const value = btn.dataset.pickValue;
            const active = (type === 'size' && value === selectedSize) || (type === 'color' && value === selectedColor);
            btn.classList.toggle('is-active', active);
            btn.disabled = !variants.some((v) => {
                if (type === 'size') {
                    return v.size === value && (!selectedColor || v.color === selectedColor);
                }

                return v.color === value && (!selectedSize || v.size === selectedSize);
            });
        });
    };

    root.addEventListener('click', (e) => {
        const btn = e.target.closest('.variant-pick-btn');
        if (!btn || btn.disabled) {
            return;
        }

        if (btn.dataset.pickType === 'size') {
            selectedSize = btn.dataset.pickValue;
        } else {
            selectedColor = btn.dataset.pickValue;
        }

        updateSelection();
    });

    if (sizes.length === 1) {
        selectedSize = sizes[0];
    }
    if (colors.length === 1) {
        selectedColor = colors[0];
    }

    updateSelection();
};

onPageLoad(initProductVariants);
