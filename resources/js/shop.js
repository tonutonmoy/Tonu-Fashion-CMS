import { onPageLoad } from './page-load';

const debounce = (fn, ms = 400) => {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), ms);
    };
};

const initShopFilters = () => {
    const form = document.getElementById('shop-filter-form');
    const results = document.getElementById('shop-results');

    if (!form || !results) {
        return;
    }

    const minSlider = document.getElementById('price-min-slider');
    const maxSlider = document.getElementById('price-max-slider');
    const minInput = document.getElementById('min_price');
    const maxInput = document.getElementById('max_price');
    const minLabel = document.getElementById('price-min-label');
    const maxLabel = document.getElementById('price-max-label');

    const syncPriceSliders = () => {
        if (!minSlider || !maxSlider) {
            return;
        }

        let minVal = parseInt(minSlider.value, 10);
        let maxVal = parseInt(maxSlider.value, 10);

        if (minVal > maxVal) {
            [minVal, maxVal] = [maxVal, minVal];
        }

        minSlider.value = minVal;
        maxSlider.value = maxVal;
        minInput.value = minVal;
        maxInput.value = maxVal;

        if (minLabel) {
            minLabel.textContent = formatBdt(minVal);
        }
        if (maxLabel) {
            maxLabel.textContent = formatBdt(maxVal);
        }
    };

    const formatBdt = (amount) => {
        const symbol = '৳';
        return symbol + Number(amount).toLocaleString('en-BD', { maximumFractionDigits: 0 });
    };

    const buildUrls = (overrideDisplay = null) => {
        syncPriceSliders();
        const displayParams = new URLSearchParams(new FormData(form));
        const fetchParams = new URLSearchParams(displayParams);
        fetchParams.set('ajax', '1');

        const fetchUrl = `${form.action}?${fetchParams}`;
        const displayUrl = overrideDisplay ?? (
            displayParams.toString() ? `${form.action}?${displayParams}` : form.action
        );

        return { fetchUrl, displayUrl };
    };

    const fetchProducts = async (displayUrlOverride = null) => {
        const { fetchUrl, displayUrl } = buildUrls(displayUrlOverride);
        window.history.replaceState({}, '', displayUrl);

        results.classList.add('opacity-50');

        try {
            const response = await fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    Accept: 'application/json',
                },
            });

            const data = await response.json();
            results.innerHTML = data.html;

            const countEl = document.getElementById('shop-results-count');
            if (countEl && data.total !== undefined) {
                countEl.textContent = `${data.total} product${data.total === 1 ? '' : 's'} found`;
            }

            bindPagination();
        } catch (e) {
            console.error(e);
        } finally {
            results.classList.remove('opacity-50');
        }
    };

    const bindPagination = () => {
        results.querySelectorAll('#shop-pagination a').forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const pageUrl = new URL(link.href);
                pageUrl.searchParams.forEach((value, key) => {
                    let input = form.querySelector(`[name="${key}"]`);
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        form.appendChild(input);
                    }
                    input.value = value;
                });
                fetchProducts(link.href);
            });
        });
    };

    const debouncedFetch = debounce(fetchProducts, window.matchMedia('(max-width: 767px)').matches ? 500 : 350);

    form.querySelectorAll('.shop-filter-input').forEach((el) => {
        el.addEventListener('change', debouncedFetch);
        el.addEventListener('input', debouncedFetch);
    });

    if (minSlider && maxSlider) {
        minSlider.addEventListener('input', () => {
            syncPriceSliders();
            debouncedFetch();
        });
        maxSlider.addEventListener('input', () => {
            syncPriceSliders();
            debouncedFetch();
        });
    }

    bindPagination();
};

onPageLoad(initShopFilters);
