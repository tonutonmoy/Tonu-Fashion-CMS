import { onPageLoad } from './page-load';

const debounce = (fn, ms = 350) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), ms);
    };
};

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

    if (window.Turbo?.visit) {
        window.Turbo.visit(target, { action: 'replace' });
        return;
    }

    window.location.href = target;
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
            if (window.Turbo?.visit) {
                window.Turbo.visit(item.url);
            } else {
                window.location.href = item.url;
            }
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

export const initAdminAutoFilters = () => {
    if (!document.body.classList.contains('admin-body')) {
        return;
    }

    document.querySelectorAll('form[data-admin-auto-filter]').forEach((form) => {
        if (form.dataset.autoFilterBound) {
            return;
        }
        form.dataset.autoFilterBound = '1';

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            submitFilterForm(form);
        });

        const debouncedSubmit = debounce(() => submitFilterForm(form), 400);

        form.querySelectorAll('input, select').forEach((el) => {
            if (el.type === 'hidden' || el.type === 'submit' || el.type === 'button') {
                return;
            }

            if (el.dataset.searchSuggest) {
                initSearchSuggest(el);
                const suggestSubmit = debounce(() => submitFilterForm(form), 700);
                el.addEventListener('input', suggestSubmit);
                el.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && form) {
                        e.preventDefault();
                        submitFilterForm(form);
                    }
                });
            } else if (el.type === 'search' || el.type === 'text' || el.name === 'search' || el.name === 'q') {
                el.addEventListener('input', debouncedSubmit);
            } else {
                el.addEventListener('change', () => submitFilterForm(form));
            }
        });
    });
};

onPageLoad(initAdminAutoFilters);
