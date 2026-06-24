import { onPageLoad } from './page-load';

const debounce = (fn, ms = 350) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), ms);
    };
};

const formDebouncers = new WeakMap();

const buildFilterUrl = (form) => {
    const url = new URL(form.getAttribute('action') || window.location.pathname, window.location.origin);
    const params = new URLSearchParams();

    new FormData(form).forEach((value, key) => {
        if (value !== null && String(value).trim() !== '') {
            params.append(key, value);
        }
    });

    url.search = params.toString();

    return url.toString();
};

const submitFilterForm = (form) => {
    if (!form) {
        return;
    }

    if (form.dataset.confirm && form.dataset.confirmed !== 'true') {
        return;
    }

    const target = buildFilterUrl(form);
    const current = `${window.location.pathname}${window.location.search}`;

    if (`${new URL(target).pathname}${new URL(target).search}` === current) {
        return;
    }

    window.location.href = target;
};

const debouncedFilterSubmit = (form) => {
    if (!formDebouncers.has(form)) {
        formDebouncers.set(form, debounce(() => submitFilterForm(form), 400));
    }

    formDebouncers.get(form)();
};

const initSearchSuggest = (input) => {
    if (!input || input.dataset.suggestBound) {
        return;
    }

    const url = input.dataset.searchSuggest;
    if (!url) {
        return;
    }

    input.dataset.suggestBound = '1';
    const form = input.closest('form');
    const wrap = document.createElement('div');
    wrap.className = 'search-suggest-wrap relative';
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);

    const dropdown = document.createElement('div');
    dropdown.className = 'search-suggest-dropdown hidden';
    dropdown.setAttribute('role', 'listbox');
    wrap.appendChild(dropdown);

    let items = [];
    let activeIndex = -1;

    const close = () => {
        dropdown.classList.add('hidden');
        dropdown.innerHTML = '';
        items = [];
        activeIndex = -1;
    };

    const pick = (item) => {
        if (!item) {
            return;
        }
        if (item.url) {
            window.location.assign(item.url);
            return;
        }
        input.value = item.value ?? item.label ?? '';
        close();
        if (form) {
            submitFilterForm(form);
        }
    };

    const render = () => {
        dropdown.innerHTML = '';
        if (!items.length) {
            close();
            return;
        }
        dropdown.classList.remove('hidden');
        items.forEach((item, index) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `search-suggest-item${index === activeIndex ? ' is-active' : ''}`;
            btn.setAttribute('role', 'option');
            btn.innerHTML = `<span class="search-suggest-label">${item.label}</span>${item.meta ? `<span class="search-suggest-meta">${item.meta}</span>` : ''}`;
            btn.addEventListener('mousedown', (e) => {
                e.preventDefault();
                pick(item);
            });
            dropdown.appendChild(btn);
        });
    };

    const fetchSuggestions = debounce(async () => {
        const q = input.value.trim();
        if (q.length < 1) {
            close();
            return;
        }
        try {
            const response = await fetch(`${url}?q=${encodeURIComponent(q)}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!response.ok) {
                close();
                return;
            }
            items = await response.json();
            activeIndex = -1;
            render();
        } catch {
            close();
        }
    }, 280);

    input.addEventListener('input', fetchSuggestions);
    input.addEventListener('focus', fetchSuggestions);
    input.addEventListener('blur', () => setTimeout(close, 150));
    input.addEventListener('keydown', (e) => {
        if (!items.length) {
            return;
        }
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            activeIndex = (activeIndex + 1) % items.length;
            render();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            activeIndex = (activeIndex - 1 + items.length) % items.length;
            render();
        } else if (e.key === 'Enter' && activeIndex >= 0) {
            e.preventDefault();
            pick(items[activeIndex]);
        } else if (e.key === 'Escape') {
            close();
        }
    });
};

const initAdminFiltersShell = () => {
    if (document.documentElement.dataset.adminFiltersShell === '1') {
        return;
    }

    document.documentElement.dataset.adminFiltersShell = '1';

    document.addEventListener('input', (event) => {
        const input = event.target;
        if (!input.matches?.('form[data-admin-auto-filter] input')) {
            return;
        }

        const form = input.closest('form[data-admin-auto-filter]');
        if (!form || input.dataset.searchSuggest) {
            return;
        }

        if (input.type === 'hidden' || input.type === 'submit' || input.type === 'button') {
            return;
        }

        if (input.type === 'search' || input.type === 'text' || input.name === 'search' || input.name === 'q') {
            debouncedFilterSubmit(form);
        }
    });

    document.addEventListener('change', (event) => {
        const el = event.target;
        const form = el.closest?.('form[data-admin-auto-filter]');
        if (!form) {
            return;
        }

        if (el.matches('select, input[type="date"], input[type="checkbox"], input[type="radio"]')) {
            submitFilterForm(form);
        }
    });
};

export const initAdminAutoFilters = () => {
    if (!document.body.classList.contains('admin-body')) {
        return;
    }

    initAdminFiltersShell();

    document.querySelectorAll('form[data-admin-auto-filter] [data-search-suggest]').forEach((input) => {
        initSearchSuggest(input);
    });
};

onPageLoad(initAdminAutoFilters);
