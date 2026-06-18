/**
 * CMS builder: rich editor, media picker, menu builder, homepage reorder, tags.
 */
const initRichEditors = () => {
    document.querySelectorAll('[data-rich-editor]').forEach((root) => {
        const body = root.querySelector('[data-editor-body]');
        const input = root.querySelector('[data-editor-input]');
        if (!body || !input) return;

        const sync = () => { input.value = body.innerHTML; };
        body.addEventListener('input', sync);
        sync();

        root.querySelectorAll('[data-cmd]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                body.focus();
                const cmd = btn.dataset.cmd;
                const val = btn.dataset.value || null;
                if (cmd === 'createLink') {
                    const url = prompt('Enter URL');
                    if (url) document.execCommand(cmd, false, url);
                } else if (cmd === 'formatBlock' && val) {
                    document.execCommand(cmd, false, val);
                } else {
                    document.execCommand(cmd, false, null);
                }
                sync();
            });
        });

        root.closest('form')?.addEventListener('submit', sync);
    });
};

const initMediaPickers = () => {
    document.querySelectorAll('[data-media-picker]').forEach((root) => {
        const urlInput = root.querySelector('[data-media-url]');
        const preview = root.querySelector('[data-media-preview]');
        const modal = root.querySelector('[data-media-modal]');
        const grid = root.querySelector('[data-media-grid]');
        const searchInput = root.querySelector('[data-media-search]');
        const searchUrl = root.dataset.searchUrl;
        if (!urlInput || !modal || !grid) return;

        const showPreview = (url) => {
            if (!url || !preview) return;
            const img = preview.querySelector('img');
            if (img) {
                img.src = url;
                preview.classList.remove('hidden');
            }
        };

        if (urlInput.value) showPreview(urlInput.value);

        const openModal = () => {
            modal.classList.remove('hidden');
            searchInput?.focus();
            if (searchInput?.value) fetchMedia(searchInput.value);
        };

        const closeModal = () => modal.classList.add('hidden');

        root.querySelector('[data-media-open]')?.addEventListener('click', openModal);
        root.querySelectorAll('[data-media-close]').forEach((el) => el.addEventListener('click', closeModal));
        root.querySelector('[data-media-clear]')?.addEventListener('click', () => {
            urlInput.value = '';
            preview?.classList.add('hidden');
        });

        let debounce;
        const fetchMedia = (q) => {
            if (!searchUrl || !q) return;
            fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then((r) => r.json())
                .then((items) => {
                    grid.innerHTML = '';
                    if (!items.length) {
                        grid.innerHTML = '<p class="col-span-full text-sm text-gray-500 text-center py-8">No results.</p>';
                        return;
                    }
                    items.forEach((item) => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'border rounded-lg overflow-hidden hover:ring-2 hover:ring-brand-500 transition';
                        btn.innerHTML = item.url.match(/\.(pdf|svg)$/i)
                            ? `<div class="h-24 flex items-center justify-center bg-gray-100 text-xs">${item.filename}</div>`
                            : `<img src="${item.url}" alt="" class="w-full h-24 object-cover" loading="lazy">`;
                        btn.addEventListener('click', () => {
                            urlInput.value = item.url;
                            showPreview(item.url);
                            closeModal();
                        });
                        grid.appendChild(btn);
                    });
                })
                .catch(() => {});
        };

        searchInput?.addEventListener('input', () => {
            clearTimeout(debounce);
            debounce = setTimeout(() => fetchMedia(searchInput.value.trim()), 300);
        });
    });

    document.querySelectorAll('[data-copy-url]').forEach((btn) => {
        btn.addEventListener('click', () => {
            navigator.clipboard.writeText(btn.dataset.copyUrl);
            btn.textContent = 'Copied!';
            setTimeout(() => { btn.textContent = 'Copy URL'; }, 1500);
        });
    });
};

const initTagsInput = () => {
    document.querySelectorAll('[data-tags-input]').forEach((input) => {
        const form = input.closest('form');
        if (!form) return;
        form.addEventListener('submit', () => {
            form.querySelectorAll('[data-tag-field]').forEach((el) => el.remove());
            const tags = input.value.split(',').map((t) => t.trim()).filter(Boolean);
            tags.forEach((tag, i) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = `tags[${i}]`;
                hidden.value = tag;
                hidden.dataset.tagField = '1';
                form.appendChild(hidden);
            });
        });
    });
};

const initMenuBuilder = () => {
    const form = document.querySelector('[data-menu-builder]');
    if (!form) return;

    const list = form.querySelector('[data-menu-list]');
    const empty = form.querySelector('[data-menu-empty]');
    const jsonInput = form.querySelector('[data-menu-json]');
    const template = document.getElementById('menu-item-template');
    const initialEl = document.getElementById('menu-initial-data');
    if (!list || !template) return;

    let dragEl = null;

    const collectItem = (li) => {
        const item = {
            title: li.querySelector('[data-field="title"]')?.value || '',
            url: li.querySelector('[data-field="url"]')?.value || null,
            page_id: li.querySelector('[data-field="page_id"]')?.value || null,
            open_in_new_tab: li.querySelector('[data-field="open_in_new_tab"]')?.checked || false,
            children: [],
        };
        if (item.page_id) item.page_id = parseInt(item.page_id, 10);
        li.querySelector('[data-children]')?.querySelectorAll(':scope > [data-menu-item]').forEach((child) => {
            item.children.push(collectItem(child));
        });
        return item;
    };

    const serialize = () => {
        const items = [...list.querySelectorAll(':scope > [data-menu-item]')].map(collectItem);
        if (jsonInput) jsonInput.value = JSON.stringify(items);
        if (empty) empty.classList.toggle('hidden', items.length > 0);
    };

    const bindItem = (li, type = 'link') => {
        const urlField = li.querySelector('[data-field="url"]');
        const pageSelect = li.querySelector('[data-field="page_id"]');
        const childrenUl = li.querySelector('[data-children]');
        const addChildBtn = li.querySelector('[data-add-child]');

        if (type === 'page') {
            pageSelect?.classList.remove('hidden');
            urlField?.classList.add('hidden');
        } else if (type === 'dropdown') {
            urlField?.classList.add('hidden');
            pageSelect?.classList.add('hidden');
            childrenUl?.classList.remove('hidden');
            addChildBtn?.classList.remove('hidden');
        }

        li.querySelector('[data-remove]')?.addEventListener('click', () => {
            li.remove();
            serialize();
        });

        addChildBtn?.addEventListener('click', () => {
            const child = createItem('link', childrenUl);
            childrenUl?.appendChild(child);
            serialize();
        });

        li.querySelectorAll('input, select').forEach((el) => el.addEventListener('input', serialize));
        li.querySelectorAll('input[type="checkbox"]').forEach((el) => el.addEventListener('change', serialize));

        li.addEventListener('dragstart', () => { dragEl = li; li.classList.add('opacity-50'); });
        li.addEventListener('dragend', () => { dragEl = null; li.classList.remove('opacity-50'); serialize(); });
        li.addEventListener('dragover', (e) => {
            e.preventDefault();
            if (!dragEl || dragEl === li) return;
            const rect = li.getBoundingClientRect();
            const after = e.clientY > rect.top + rect.height / 2;
            li.parentElement?.insertBefore(dragEl, after ? li.nextSibling : li);
        });
    };

    const createItem = (type, parentList = list) => {
        const clone = template.content.cloneNode(true);
        const li = clone.querySelector('[data-menu-item]');
        bindItem(li, type);
        parentList?.appendChild(li);
        serialize();
        return li;
    };

    const populate = (items, parentList = list, type = 'link') => {
        items.forEach((item) => {
            const itemType = item.children?.length ? 'dropdown' : (item.page_id ? 'page' : 'link');
            const clone = template.content.cloneNode(true);
            const li = clone.querySelector('[data-menu-item]');
            li.querySelector('[data-field="title"]').value = item.title || '';
            if (item.url) li.querySelector('[data-field="url"]').value = item.url;
            if (item.page_id) li.querySelector('[data-field="page_id"]').value = item.page_id;
            if (item.open_in_new_tab) li.querySelector('[data-field="open_in_new_tab"]').checked = true;
            bindItem(li, itemType);
            parentList.appendChild(li);
            if (item.children?.length) {
                const childrenUl = li.querySelector('[data-children]');
                populate(item.children, childrenUl, 'link');
            }
        });
        serialize();
    };

    form.querySelector('[data-menu-add="link"]')?.addEventListener('click', () => createItem('link'));
    form.querySelector('[data-menu-add="page"]')?.addEventListener('click', () => createItem('page'));
    form.querySelector('[data-menu-add="dropdown"]')?.addEventListener('click', () => createItem('dropdown'));

    form.addEventListener('submit', (e) => {
        serialize();
        const items = JSON.parse(jsonInput?.value || '[]');
        form.querySelectorAll('[data-menu-field]').forEach((el) => el.remove());
        const appendFields = (arr, prefix) => {
            arr.forEach((item, i) => {
                const base = `${prefix}[${i}]`;
                ['title', 'url', 'page_id'].forEach((key) => {
                    if (item[key]) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `${base}[${key}]`;
                        input.value = item[key];
                        input.dataset.menuField = '1';
                        form.appendChild(input);
                    }
                });
                if (item.open_in_new_tab) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `${base}[open_in_new_tab]`;
                    input.value = '1';
                    input.dataset.menuField = '1';
                    form.appendChild(input);
                }
                if (item.children?.length) appendFields(item.children, `${base}[children]`);
            });
        };
        appendFields(items, 'items');
    });

    if (initialEl) {
        try {
            const initial = JSON.parse(initialEl.textContent || '[]');
            if (initial.length) populate(initial);
        } catch (_) {}
    } else {
        serialize();
    }
};

const initHomepageSortable = () => {
    const list = document.querySelector('[data-homepage-sort]');
    const form = document.querySelector('[data-homepage-reorder-form]');
    if (!list || !form) return;

    let dragEl = null;
    list.querySelectorAll('[data-section-id]').forEach((row) => {
        row.setAttribute('draggable', 'true');
        row.addEventListener('dragstart', () => { dragEl = row; row.classList.add('opacity-50'); });
        row.addEventListener('dragend', () => { dragEl = null; row.classList.remove('opacity-50'); updateOrder(); });
        row.addEventListener('dragover', (e) => {
            e.preventDefault();
            if (!dragEl || dragEl === row) return;
            const rect = row.getBoundingClientRect();
            list.insertBefore(dragEl, e.clientY > rect.top + rect.height / 2 ? row.nextSibling : row);
        });
    });

    const updateOrder = () => {
        form.querySelectorAll('[data-order-input]').forEach((el) => el.remove());
        [...list.querySelectorAll('[data-section-id]')].forEach((row, i) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `order[${i}]`;
            input.value = row.dataset.sectionId;
            input.dataset.orderInput = '1';
            form.appendChild(input);
        });
    };

    updateOrder();
    form.addEventListener('submit', updateOrder);
};

const HERO_TYPOGRAPHY_DEFAULTS = { title_size: 48, subtitle_size: 20, button_size: 14 };
const HERO_SIZE_DEFAULTS = { button_width: 140, button_height: 44 };

const updateHeroMediaOrder = (list) => {
    if (!list) return;
    [...list.querySelectorAll('[data-hero-media-id]')].forEach((row) => {
        const input = row.querySelector('[data-hero-media-order]');
        if (input) input.value = row.dataset.heroMediaId;
    });
};

const initHeroMediaActions = () => {
    const list = document.querySelector('[data-hero-media-sort]');
    const form = document.querySelector('[data-hero-builder]');
    if (!list || !form) return;

    const notify = (message, type = 'info') => {
        if (window.AdminUI?.showToast) window.AdminUI.showToast(message, type);
    };

    list.querySelectorAll('[data-hero-set-main]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const row = list.querySelector(`[data-hero-media-id="${btn.dataset.heroSetMain}"]`);
            if (!row) return;
            list.prepend(row);
            updateHeroMediaOrder(list);
            notify('Set as main slide — save draft to apply', 'info');
        });
    });

    list.querySelectorAll('[data-hero-remove]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!window.confirm('Remove this slide from the hero?')) return;
            const id = btn.dataset.heroRemove;
            const input = list.querySelector(`[data-hero-remove-input="${id}"]`);
            const row = list.querySelector(`[data-hero-media-id="${id}"]`);
            if (input) {
                input.disabled = false;
                input.value = id;
            }
            row?.remove();
            updateHeroMediaOrder(list);
            notify('Slide removed — save draft to apply', 'info');
        });
    });

    form.addEventListener('submit', () => updateHeroMediaOrder(list));
};

const initHeroMediaSortable = () => {
    const list = document.querySelector('[data-hero-media-sort]');
    if (!list) return;

    let dragEl = null;

    list.querySelectorAll('[data-hero-media-id]').forEach((row) => {
        row.setAttribute('draggable', 'true');
        row.addEventListener('dragstart', () => { dragEl = row; row.classList.add('opacity-50'); });
        row.addEventListener('dragend', () => { dragEl = null; row.classList.remove('opacity-50'); updateHeroMediaOrder(list); });
        row.addEventListener('dragover', (e) => {
            e.preventDefault();
            if (!dragEl || dragEl === row) return;
            const rect = row.getBoundingClientRect();
            list.insertBefore(dragEl, e.clientY > rect.top + rect.height / 2 ? row.nextSibling : row);
        });
    });

    updateHeroMediaOrder(list);
    initHeroMediaActions();
};

const initAdminScrollRestore = () => {
    const pageKey = `admin_scroll:${window.location.pathname}${window.location.search}`;
    const main = document.querySelector('.builder-shell-main');
    const mainKey = `${pageKey}:main`;

    const restore = () => {
        const saved = sessionStorage.getItem(pageKey);
        if (saved) {
            window.scrollTo(0, parseInt(saved, 10));
        }
        if (main) {
            const mainSaved = sessionStorage.getItem(mainKey);
            if (mainSaved) {
                main.scrollTop = parseInt(mainSaved, 10);
            }
        }
    };

    window.addEventListener('beforeunload', () => {
        sessionStorage.setItem(pageKey, String(window.scrollY));
        if (main) {
            sessionStorage.setItem(mainKey, String(main.scrollTop));
        }
    });

    if (main) {
        main.addEventListener('scroll', () => {
            sessionStorage.setItem(mainKey, String(main.scrollTop));
        }, { passive: true });
    }

    window.addEventListener('scroll', () => {
        sessionStorage.setItem(pageKey, String(window.scrollY));
    }, { passive: true });

    requestAnimationFrame(restore);
    setTimeout(restore, 100);
};

const scrollIframeToSection = (iframe, sectionId) => {
    if (!iframe || !sectionId) return;

    const scroll = () => {
        try {
            const doc = iframe.contentDocument;
            if (!doc) return;

            const el = doc.getElementById(sectionId);
            if (!el) return;

            const top = el.getBoundingClientRect().top + (doc.documentElement.scrollTop || doc.body.scrollTop || 0);
            doc.documentElement.scrollTop = Math.max(0, top - 4);
            doc.body.scrollTop = Math.max(0, top - 4);
            el.scrollIntoView({ block: 'start', behavior: 'auto' });
        } catch (_) {}
    };

    if (iframe.contentDocument?.readyState === 'complete') {
        scroll();
        setTimeout(scroll, 200);
        setTimeout(scroll, 800);
    } else {
        iframe.addEventListener('load', () => {
            scroll();
            setTimeout(scroll, 200);
            setTimeout(scroll, 800);
        }, { once: true });
    }
};

const refreshBuilderPreview = () => {
    document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
        try {
            const current = new URL(iframe.dataset.previewSrc || iframe.src, window.location.origin);
            current.searchParams.set('preview', '1');
            current.searchParams.set('t', Date.now().toString());
            const hash = iframe.dataset.previewHash || current.hash || '';
            if (hash) {
                current.hash = hash.startsWith('#') ? hash : `#${hash}`;
            }
            iframe.src = current.toString();
            iframe.dataset.previewSrc = `${current.origin}${current.pathname}${current.search}`;

            const sectionId = (hash || '').replace(/^#/, '') || iframe.closest('[data-preview-section]')?.dataset.previewSection;
            if (sectionId) {
                iframe.addEventListener('load', () => scrollIframeToSection(iframe, sectionId), { once: true });
                scrollIframeToSection(iframe, sectionId);
            }
        } catch (_) {
            const hash = iframe.dataset.previewHash || '';
            iframe.src = `${(iframe.dataset.previewSrc || iframe.src).split('?')[0]}?preview=1&t=${Date.now()}${hash}`;
        }
    });
};

const heroDimensionCss = (value) => {
    const num = parseInt(value, 10);
    return Number.isFinite(num) && num > 0 ? `${num}px` : 'auto';
};

const applyHeroContentVars = (content, values) => {
    if (!content) return;

    content.style.setProperty('--hero-title-size', `${values.titleSize}px`);
    content.style.setProperty('--hero-subtitle-size', `${values.subtitleSize}px`);
    content.style.setProperty('--hero-button-size', `${values.buttonSize}px`);
    content.style.setProperty('--hero-button-width', heroDimensionCss(values.buttonWidth));
    content.style.setProperty('--hero-button-height', heroDimensionCss(values.buttonHeight));
};

const applyHeroElementStyles = (title, subtitle, button, values) => {
    if (title) title.style.fontSize = `${values.titleSize}px`;
    if (subtitle) subtitle.style.fontSize = `${values.subtitleSize}px`;
    if (button) {
        button.style.fontSize = `${values.buttonSize}px`;
        button.style.width = values.buttonWidth > 0 ? `${values.buttonWidth}px` : '';
        button.style.minWidth = values.buttonWidth > 0 ? `${values.buttonWidth}px` : '';
        button.style.height = values.buttonHeight > 0 ? `${values.buttonHeight}px` : '';
        button.style.minHeight = values.buttonHeight > 0 ? `${values.buttonHeight}px` : '';
        button.style.boxSizing = 'border-box';
    }
};

const heroPxOrDefault = (value, fallback, allowZero = false) => {
    const num = parseInt(value, 10);
    if (!Number.isFinite(num)) return fallback;
    if (allowZero) return num >= 0 ? num : fallback;
    return num > 0 ? num : fallback;
};

const heroOverlayRgba = (hex, opacity = 0.45) => {
    let color = String(hex || '#000000').replace('#', '');
    if (color.length === 3) {
        color = color.split('').map((c) => c + c).join('');
    }
    const r = parseInt(color.slice(0, 2), 16) || 0;
    const g = parseInt(color.slice(2, 4), 16) || 0;
    const b = parseInt(color.slice(4, 6), 16) || 0;
    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
};

const formGetValue = (form, name) => {
    const checkbox = form.querySelector(`[name="${name}"][type="checkbox"]`);
    if (checkbox) {
        return checkbox.checked;
    }

    const fields = form.querySelectorAll(`[name="${name}"]`);
    if (!fields.length) return '';

    const first = fields[0];
    if (first.type === 'radio') {
        return form.querySelector(`[name="${name}"]:checked`)?.value ?? '';
    }

    return first.value ?? '';
};

const applyHeroLivePreview = (form) => {
    const get = (name) => formGetValue(form, name);

    const values = {
        title: get('title'),
        subtitle: get('subtitle'),
        buttonText: get('button_text'),
        buttonLink: get('button_link'),
        showTitle: !!get('show_title'),
        showSubtitle: !!get('show_subtitle'),
        showButton: !!get('show_button'),
        layout: get('content_layout') || 'centered',
        titleSize: heroPxOrDefault(get('title_size'), HERO_TYPOGRAPHY_DEFAULTS.title_size),
        subtitleSize: heroPxOrDefault(get('subtitle_size'), HERO_TYPOGRAPHY_DEFAULTS.subtitle_size),
        buttonSize: heroPxOrDefault(get('button_size'), HERO_TYPOGRAPHY_DEFAULTS.button_size),
        buttonWidth: heroPxOrDefault(get('button_width'), HERO_SIZE_DEFAULTS.button_width, true),
        buttonHeight: heroPxOrDefault(get('button_height'), HERO_SIZE_DEFAULTS.button_height, true),
        overlay: get('overlay_color') || '#000000',
        autoplay: heroPxOrDefault(get('autoplay_seconds'), 5),
    };

    document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
        try {
            const doc = iframe.contentDocument;
            if (!doc) return;

            const section = doc.getElementById('section-hero_slider');
            if (!section) return;

            const title = section.querySelector('[data-hero-live-title]');
            const subtitle = section.querySelector('[data-hero-live-subtitle]');
            const button = section.querySelector('[data-hero-live-button]');
            const overlay = section.querySelector('[data-hero-live-overlay]');
            const content = section.querySelector('[data-hero-live-content]');
            const slider = section.querySelector('[data-hero-slider]');

            if (title) {
                title.textContent = values.title;
                title.style.display = values.showTitle && values.title ? '' : 'none';
            }

            if (subtitle) {
                subtitle.textContent = values.subtitle;
                subtitle.style.display = values.showSubtitle && values.subtitle ? '' : 'none';
            }

            if (button) {
                if (values.showButton && values.buttonText && values.buttonLink) {
                    button.textContent = values.buttonText;
                    button.href = values.buttonLink;
                    button.style.display = '';
                } else {
                    button.style.display = 'none';
                }
            }

            if (overlay) {
                overlay.style.setProperty('--hero-overlay-tint', heroOverlayRgba(values.overlay));
            }

            if (content) {
                ['centered', 'left', 'right', 'bottom'].forEach((name) => {
                    content.classList.remove(`theme-hero-content--${name}`);
                });
                const layout = values.layout || 'centered';
                content.classList.add(`theme-hero-content--${layout}`);
                content.setAttribute('data-hero-layout', layout);
                applyHeroContentVars(content, values);
            }

            applyHeroElementStyles(title, subtitle, button, values);

            if (slider) {
                slider.dataset.autoplay = String(values.autoplay * 1000);
            }
        } catch (_) {
            // iframe not ready
        }
    });
};

const initHeroUploadValidation = () => {
    const form = document.querySelector('[data-hero-builder]');
    if (!form) return;

    const maxPerFile = (parseInt(form.dataset.maxFileMb, 10) || 16) * 1024 * 1024;
    const maxTotal = (parseInt(form.dataset.maxPostMb, 10) || 64) * 1024 * 1024;
    const maxFiles = parseInt(form.dataset.maxFiles, 10) || 20;

    const notify = (message) => {
        if (window.AdminUI?.showToast) {
            window.AdminUI.showToast(message, 'error');
        } else {
            window.alert(message);
        }
    };

    const validateFiles = (files) => {
        if (!files?.length) return true;

        if (files.length > maxFiles) {
            notify(`Too many images. Max ${maxFiles} files per save.`);
            return false;
        }

        let total = 0;
        for (const file of files) {
            if (!file.type?.startsWith('image/')) continue;
            if (file.size > maxPerFile) {
                const mb = Math.round(file.size / 1024 / 1024 * 10) / 10;
                notify(`"${file.name}" is ${mb}MB. Max ${form.dataset.maxFileMb || 16}MB per image.`);
                return false;
            }
            total += file.size;
        }

        if (total > maxTotal) {
            const mb = Math.round(total / 1024 / 1024 * 10) / 10;
            notify(`Total upload is ${mb}MB. Max ${form.dataset.maxPostMb || 64}MB combined — upload fewer or smaller images.`);
            return false;
        }

        return true;
    };

    const fileInput = form.querySelector('input[name="media_images[]"]');
    fileInput?.addEventListener('change', () => {
        if (!validateFiles(fileInput.files)) {
            fileInput.value = '';
        }
    });

    form.addEventListener('submit', (event) => {
        if (!validateFiles(fileInput?.files)) {
            event.preventDefault();
        }
    });
};

const previewHeroImageFiles = (form) => {
    const input = form.querySelector('input[name="media_images[]"]');
    const file = input?.files?.[0];
    if (!file?.type?.startsWith('image/')) return;

    const reader = new FileReader();
    reader.onload = () => {
        document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
            try {
                const doc = iframe.contentDocument;
                const img = doc?.querySelector('#section-hero_slider .theme-hero-slide.is-active img.theme-hero-bg')
                    || doc?.querySelector('#section-hero_slider img.theme-hero-bg');
                if (img) img.src = reader.result;
            } catch (_) {}
        });
    };
    reader.readAsDataURL(file);
};

const initHeroBuilderLivePreview = () => {
    const form = document.querySelector('[data-hero-builder]');
    if (!form) return;

    const apply = () => applyHeroLivePreview(form);

    form.querySelectorAll('input:not([type="file"]), select, textarea').forEach((field) => {
        field.addEventListener('input', apply);
        field.addEventListener('change', apply);
    });

    form.querySelector('input[name="media_images[]"]')?.addEventListener('change', () => {
        previewHeroImageFiles(form);
    });

    document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
        iframe.addEventListener('load', apply);
    });

    apply();
};

const initPreviewSectionFocus = () => {
    document.querySelectorAll('[data-preview-goto-section]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const sectionId = btn.dataset.previewGotoSection;
            document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
                scrollIframeToSection(iframe, sectionId);
            });
        });
    });

    document.querySelectorAll('[data-builder-preview][data-preview-section]').forEach((panel) => {
        const sectionId = panel.dataset.previewSection;
        if (!sectionId) return;

        document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
            scrollIframeToSection(iframe, sectionId);
            iframe.addEventListener('load', () => scrollIframeToSection(iframe, sectionId));
        });
    });
};

const initHeroSlidePreviewRefresh = () => {
    if (!document.querySelector('[data-hero-builder]')) {
        return;
    }

    initPreviewSectionFocus();
    initHeroUploadValidation();
    initHeroBuilderLivePreview();

    const triggerRefresh = () => window.setTimeout(refreshBuilderPreview, 200);

    if (document.querySelector('[data-admin-toast][data-type="success"]') || document.body.dataset.refreshPreview === '1') {
        triggerRefresh();
    }

    window.addEventListener('load', () => {
        if (document.body.dataset.refreshPreview === '1') {
            triggerRefresh();
        }
    });
};

const initCmsBuilder = () => {
    if (!document.body.classList.contains('admin-body')) return;
    initRichEditors();
    initMediaPickers();
    initTagsInput();
    initMenuBuilder();
    initHomepageSortable();
    initHeroMediaSortable();
    initAdminScrollRestore();
    initPreviewSectionFocus();
    initHeroSlidePreviewRefresh();
};

document.addEventListener('DOMContentLoaded', initCmsBuilder);

window.refreshBuilderPreview = refreshBuilderPreview;
