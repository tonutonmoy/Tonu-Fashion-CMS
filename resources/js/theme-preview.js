/**
 * Website Builder — live theme customizer preview (WordPress-style).
 */
import { onPageLoad } from './page-load';

const PREVIEW_DESKTOP_WIDTH = 1280;

const slugify = (value) => value
    .toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '');

const getPreviewDevice = () => document.querySelector('[data-preview-device].is-active')?.dataset.previewDevice || 'desktop';

const getThemeDefaults = () => {
    try {
        return JSON.parse(document.getElementById('theme-defaults-data')?.textContent || '{}');
    } catch (_) {
        return {};
    }
};

const getFontSlugs = () => {
    try {
        return JSON.parse(document.getElementById('theme-font-slugs')?.textContent || '{}');
    } catch (_) {
        return {};
    }
};

const buildPreviewUrl = (pathOrUrl = '/', section = null, device = null, theme = null) => {
    const url = new URL(
        String(pathOrUrl).startsWith('http') ? pathOrUrl : `${window.location.origin}${String(pathOrUrl).startsWith('/') ? pathOrUrl : `/${pathOrUrl}`}`,
    );
    url.searchParams.set('preview', '1');
    url.searchParams.set('device', device || getPreviewDevice());
    if (theme) url.searchParams.set('theme', theme);
    if (section) url.hash = section.startsWith('#') ? section : `#${section}`;
    return url.toString();
};

const setPreviewUrl = (url, label = null) => {
    document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
        iframe.src = url;
        iframe.dataset.previewSrc = url.split('#')[0];
        const hash = url.includes('#') ? `#${url.split('#')[1]}` : (iframe.dataset.previewHash || '');
        if (hash) {
            iframe.dataset.previewHash = hash;
        }
    });
    document.querySelectorAll('[data-preview-open]').forEach((a) => { a.href = url.split('#')[0]; });
    if (label) {
        document.querySelectorAll('[data-preview-label]').forEach((el) => {
            el.textContent = label;
            el.title = label;
        });
    }
};

const fitDesktopPreview = () => {
    document.querySelectorAll('.builder-preview-frame.is-desktop [data-preview-scale-wrap]').forEach((wrap) => {
        const frame = wrap.closest('[data-preview-frame]');
        const iframe = wrap.querySelector('[data-theme-preview-iframe]');
        if (!frame || !iframe) return;

        const frameHeight = Math.max(frame.clientHeight, 480);
        const wrapWidth = wrap.clientWidth || frame.clientWidth || PREVIEW_DESKTOP_WIDTH;
        const scale = Math.max(0.2, Math.min(1, wrapWidth / PREVIEW_DESKTOP_WIDTH));

        iframe.style.width = `${PREVIEW_DESKTOP_WIDTH}px`;
        iframe.style.height = `${Math.round(frameHeight / scale)}px`;
        iframe.style.transform = `scale(${scale})`;
        wrap.style.height = `${frameHeight}px`;
    });
};

const scheduleFitDesktopPreview = () => {
    fitDesktopPreview();
    requestAnimationFrame(fitDesktopPreview);
    [100, 350, 800].forEach((ms) => setTimeout(fitDesktopPreview, ms));
};

const applyGoogleFont = (doc, fontFamily) => {
    if (!doc || !fontFamily) return;

    const slug = getFontSlugs()[fontFamily] || fontFamily.replace(/ /g, '+');
    const href = `https://fonts.googleapis.com/css2?family=${slug}:wght@400;500;600;700&display=swap`;
    let link = doc.getElementById('builder-live-font');

    if (!link) {
        link = doc.createElement('link');
        link.id = 'builder-live-font';
        link.rel = 'stylesheet';
        doc.head.appendChild(link);
    }

    if (link.getAttribute('href') !== href) {
        link.setAttribute('href', href);
    }
};

const applyLogoToDoc = (doc, dataUrl) => {
    if (!doc || !dataUrl) return;

    const logoLink = doc.querySelector('.theme-logo');
    if (!logoLink) return;

    let img = logoLink.querySelector('img');
    if (!img) {
        logoLink.querySelector('.theme-logo-text')?.remove();
        img = doc.createElement('img');
        img.className = 'theme-logo-img';
        img.alt = 'Store logo';
        img.width = 160;
        img.height = 48;
        logoLink.appendChild(img);
    }

    img.src = dataUrl;
};

const applyFaviconToDoc = (doc, dataUrl) => {
    if (!doc || !dataUrl) return;

    let link = doc.querySelector('link[rel="icon"]');
    if (!link) {
        link = doc.createElement('link');
        link.rel = 'icon';
        doc.head.appendChild(link);
    }

    link.href = dataUrl;
};

const readFileAsDataUrl = (file) => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => resolve(e.target.result);
    reader.onerror = reject;
    reader.readAsDataURL(file);
});

const rgbToHex = (r, g, b) => `#${[r, g, b].map((c) => Math.min(255, c).toString(16).padStart(2, '0')).join('')}`;

const extractDominantColors = async (file, maxColors = 6) => {
    if (!file?.type?.startsWith('image/') || file.type === 'image/svg+xml') {
        return [];
    }

    const bitmap = await createImageBitmap(file);
    const size = 48;
    const canvas = document.createElement('canvas');
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(bitmap, 0, 0, size, size);
    bitmap.close?.();

    const { data } = ctx.getImageData(0, 0, size, size);
    const buckets = new Map();

    for (let i = 0; i < data.length; i += 4) {
        if (data[i + 3] < 128) {
            continue;
        }

        const lum = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
        if (lum > 245 || lum < 15) {
            continue;
        }

        const r = Math.round(data[i] / 32) * 32;
        const g = Math.round(data[i + 1] / 32) * 32;
        const b = Math.round(data[i + 2] / 32) * 32;
        const key = `${r},${g},${b}`;
        buckets.set(key, (buckets.get(key) || 0) + 1);
    }

    const seen = new Set();
    const colors = [];

    [...buckets.entries()]
        .sort((a, b) => b[1] - a[1])
        .forEach(([key]) => {
            const [r, g, b] = key.split(',').map(Number);
            const hex = rgbToHex(r, g, b);
            if (!seen.has(hex)) {
                seen.add(hex);
                colors.push(hex);
            }
        });

    return colors.slice(0, maxColors);
};

const compressRasterImage = (file, maxWidth = 1200, quality = 0.82, targetMaxBytes = 1.8 * 1024 * 1024) => new Promise((resolve) => {
    if (!file?.type?.startsWith('image/') || ['image/svg+xml', 'image/gif', 'image/x-icon'].includes(file.type)) {
        resolve(file);
        return;
    }

    if (file.size <= targetMaxBytes) {
        resolve(file);
        return;
    }

    const img = new Image();
    const objectUrl = URL.createObjectURL(file);

    img.onload = () => {
        URL.revokeObjectURL(objectUrl);
        const scale = Math.min(1, maxWidth / Math.max(img.width, 1));
        const width = Math.max(1, Math.round(img.width * scale));
        const height = Math.max(1, Math.round(img.height * scale));
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            resolve(file);
            return;
        }
        ctx.drawImage(img, 0, 0, width, height);
        canvas.toBlob((blob) => {
            if (!blob) {
                resolve(file);
                return;
            }
            const ext = file.type === 'image/png' ? 'png' : 'jpg';
            const mime = ext === 'png' ? 'image/png' : 'image/jpeg';
            const compressed = new File([blob], file.name.replace(/\.[^.]+$/, `.${ext}`), {
                type: mime,
                lastModified: Date.now(),
            });
            resolve(compressed.size < file.size ? compressed : file);
        }, file.type === 'image/png' ? 'image/png' : 'image/jpeg', quality);
    };

    img.onerror = () => {
        URL.revokeObjectURL(objectUrl);
        resolve(file);
    };

    img.src = objectUrl;
});

const replaceFileInput = (input, file) => {
    if (!input || !file) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    input.dispatchEvent(new Event('change', { bubbles: true }));
};

const initBuilderMode = () => {
    const shell = document.querySelector('[data-builder-shell]');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');

    if (!shell) {
        document.body.classList.remove('builder-mode');
        sidebar?.classList.remove('is-open');
        return;
    }

    document.body.classList.add('builder-mode');

    if (sidebar) {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('lg:translate-x-0', 'is-open');
    }

    overlay?.classList.add('hidden');
    scheduleFitDesktopPreview();
};

const initThemeCustomizerLive = () => {
    const form = document.querySelector('[data-theme-customizer]');
    if (!form) return;

    const getVal = (name) => {
        const el = form.querySelector(`[name="${name}"]`);
        if (!el) return '';
        if (el.type === 'radio') return form.querySelector(`[name="${name}"]:checked`)?.value || '';
        return el.value;
    };

    const setVal = (name, value) => {
        if (value === undefined || value === null) return;
        const el = form.querySelector(`[name="${name}"]`);
        if (!el) return;
        if (el.type === 'radio') {
            const radio = form.querySelector(`[name="${name}"][value="${value}"]`);
            if (radio) radio.checked = true;
            return;
        }
        el.value = value;
    };

    const applyThemeDefaults = (slug) => {
        const defs = getThemeDefaults()[slug];
        if (!defs) return;

        setVal('primary_color', defs.primary_color);
        setVal('secondary_color', defs.secondary_color);
        setVal('accent_color', defs.accent_color);
        setVal('font_family', defs.font_family);
        setVal('header_style', defs.header_style);
        setVal('footer_style', defs.footer_style);
        setVal('button_radius', defs.button_radius);
        setVal('container_width', defs.container_width);
    };

    const getFormValues = () => ({
        primary: getVal('primary_color'),
        secondary: getVal('secondary_color'),
        accent: getVal('accent_color'),
        font: getVal('font_family'),
        radius: getVal('button_radius'),
        containerWidth: getVal('container_width'),
        headerStyle: getVal('header_style'),
        footerStyle: getVal('footer_style'),
        theme: getVal('active_theme'),
    });

    const buildLiveCss = (v) => `
        :root {
            --theme-primary: ${v.primary};
            --theme-secondary: ${v.secondary};
            --theme-accent: ${v.accent};
            --theme-font: '${v.font}', system-ui, sans-serif;
            --theme-btn-radius: ${v.radius};
            --theme-container-width: ${v.containerWidth};
        }
        html, body, .theme-body { font-family: var(--theme-font) !important; }
        .theme-container { max-width: var(--theme-container-width) !important; width: 100% !important; margin-left: auto !important; margin-right: auto !important; }
        .theme-btn, .theme-btn-primary, .btn-primary { border-radius: var(--theme-btn-radius) !important; }
        .theme-btn-primary, .btn-primary, [data-cart-count], .bg-brand-600 { background-color: var(--theme-primary) !important; }
        .theme-btn-primary, .theme-btn-primary:hover, .theme-btn.theme-hero-btn, .theme-btn.theme-hero-btn:hover { color: #fff !important; }
        .theme-logo-text, .theme-link, .theme-price-current { color: var(--theme-primary) !important; }
        .theme-nav a:hover { color: var(--theme-primary) !important; }
        .theme-badge-sale { background-color: var(--theme-primary) !important; color: #fff !important; }
        .theme-countdown-timer, .theme-review-stars, .theme-badge-sale.theme-accent { color: var(--theme-accent) !important; }
        .theme-hero-placeholder { background: linear-gradient(135deg, var(--theme-primary), var(--theme-secondary)) !important; }
        .theme-footer, .theme-newsletter { background: var(--theme-secondary) !important; }
        a:not(.theme-btn):hover { color: var(--theme-primary) !important; }
    `;

    const updateAdminSwatches = (v) => {
        document.querySelectorAll('[data-swatch-primary]').forEach((el) => { el.style.background = v.primary; });
        document.querySelectorAll('[data-swatch-secondary]').forEach((el) => { el.style.background = v.secondary; });
        document.querySelectorAll('[data-swatch-accent]').forEach((el) => { el.style.background = v.accent; });
    };

    const applyToDocument = (doc) => {
        if (!doc) return;

        const v = getFormValues();
        let style = doc.getElementById('builder-live-vars');
        if (!style) {
            style = doc.createElement('style');
            style.id = 'builder-live-vars';
            doc.head.appendChild(style);
        }
        style.textContent = buildLiveCss(v);
        applyGoogleFont(doc, v.font);

        const header = doc.querySelector('header.theme-header, .theme-header');
        if (header) {
            ['default', 'centered', 'transparent', 'sticky'].forEach((s) => header.classList.remove(`theme-header-${s}`));
            header.classList.add(`theme-header-${v.headerStyle}`);
            header.style.position = '';
            header.classList.toggle('is-scrolled', v.headerStyle !== 'transparent' && header.classList.contains('is-scrolled'));

            const body = doc.body;
            if (body) {
                ['default', 'centered', 'transparent', 'sticky'].forEach((s) => {
                    body.classList.remove(`theme-header-layout-${s}`);
                });
                body.classList.add(`theme-header-layout-${v.headerStyle}`);
            }
        }

        const footer = doc.querySelector('footer.theme-footer, .theme-footer');
        if (footer) {
            ['default', 'minimal', 'expanded'].forEach((s) => footer.classList.remove(`theme-footer-${s}`));
            footer.classList.add(`theme-footer-${v.footerStyle}`);
        }

        const body = doc.body;
        if (body) {
            ['fashion-modern', 'fashion-classic', 'fashion-luxury', 'fashion-minimal'].forEach((t) => {
                body.classList.remove(`theme-${t}`);
            });
            body.classList.add(`theme-${v.theme}`);
            body.setAttribute('data-theme', v.theme);
        }

        doc.querySelectorAll('[data-swatch-primary]').forEach((el) => { el.style.background = v.primary; });
        doc.querySelectorAll('[data-swatch-secondary]').forEach((el) => { el.style.background = v.secondary; });
        doc.querySelectorAll('[data-swatch-accent]').forEach((el) => { el.style.background = v.accent; });
    };

    const applyLivePreview = () => {
        const v = getFormValues();
        updateAdminSwatches(v);

        document.querySelectorAll('[data-theme-preview]').forEach((el) => applyToDocument(el.ownerDocument || document));
        document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
            try {
                applyToDocument(iframe.contentDocument);
            } catch (_) {}
        });

        document.querySelectorAll('.theme-pick-card').forEach((card) => {
            const active = card.querySelector('input')?.checked;
            card.classList.toggle('border-brand-600', active);
            card.classList.toggle('ring-2', active);
            card.classList.toggle('ring-brand-600', active);
            card.classList.toggle('bg-brand-50/30', active);
        });
    };

    const applyFileToPreviews = async (name, file) => {
        if (!file?.type?.startsWith('image/')) return;

        const dataUrl = await readFileAsDataUrl(file);
        document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
            try {
                const doc = iframe.contentDocument;
                if (name === 'logo') applyLogoToDoc(doc, dataUrl);
                if (name === 'favicon') applyFaviconToDoc(doc, dataUrl);
            } catch (_) {}
        });

        const colors = await extractDominantColors(file);
        renderImagePalette(form, colors);
    };

    const paletteRoot = form.querySelector('[data-image-palette-root]');
    const paletteSwatches = form.querySelector('[data-image-palette-swatches]');
    let paletteColors = [];

    const renderImagePalette = (paletteForm, colors) => {
        if (!paletteRoot || !paletteSwatches) {
            return;
        }

        paletteColors = colors;
        paletteSwatches.innerHTML = '';

        if (!colors.length) {
            paletteRoot.classList.add('hidden');
            return;
        }

        paletteRoot.classList.remove('hidden');

        colors.forEach((hex) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'w-10 h-10 rounded-lg border-2 border-white shadow ring-1 ring-gray-200 hover:scale-105 transition-transform';
            btn.style.backgroundColor = hex;
            btn.title = hex;
            btn.dataset.color = hex;
            btn.addEventListener('click', () => {
                setVal('primary_color', hex);
                applyLivePreview();
            });
            paletteSwatches.appendChild(btn);
        });
    };

    form.querySelector('[data-apply-palette-theme]')?.addEventListener('click', () => {
        if (!paletteColors.length) {
            return;
        }

        const [primary, secondary, accent] = [
            paletteColors[0],
            paletteColors[1] || paletteColors[0],
            paletteColors[2] || paletteColors[1] || paletteColors[0],
        ];

        setVal('primary_color', primary);
        setVal('secondary_color', secondary);
        setVal('accent_color', accent);
        applyLivePreview();
        window.AdminUI?.showToast?.('Theme colors applied from image palette.', 'success');
    });

    const reloadPreviewIframe = (themeSlug = null) => {
        const theme = themeSlug || getVal('active_theme');
        const url = buildPreviewUrl('/', null, getPreviewDevice(), theme);
        document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
            const loadHandler = () => {
                applyLivePreview();
                scheduleFitDesktopPreview();
                iframe.removeEventListener('load', loadHandler);
            };
            iframe.addEventListener('load', loadHandler);
            iframe.src = `${url}&t=${Date.now()}`;
            iframe.dataset.previewSrc = url;
        });
        setPreviewUrl(url, `Theme: ${theme}`);
    };

    form.querySelectorAll('input, select').forEach((el) => {
        el.addEventListener('input', applyLivePreview);
        el.addEventListener('change', applyLivePreview);
    });

    form.addEventListener('change', (e) => {
        const target = e.target;
        if (!target.matches('[name="logo"], [name="favicon"]')) return;
        const file = target.files?.[0];
        if (file) applyFileToPreviews(target.name, file);
    });

    form.addEventListener('submit', async (event) => {
        if (form.dataset.skipCompress === '1') {
            delete form.dataset.skipCompress;
            return;
        }

        const maxBytes = (parseFloat(form.dataset.maxFileMb) || 1.8) * 1024 * 1024;
        const inputs = ['logo', 'favicon']
            .map((name) => form.querySelector(`[name="${name}"]`))
            .filter(Boolean);

        let needsOptimize = false;

        for (const input of inputs) {
            const file = input.files?.[0];
            if (!file || file.size <= maxBytes) {
                continue;
            }

            if (!file.type.startsWith('image/') || file.type === 'image/svg+xml') {
                event.preventDefault();
                window.AdminUI?.showToast?.(
                    `"${file.name}" is too large. Server limit is ${form.dataset.maxFileMb || 1.8}MB per file.`,
                    'error',
                );
                return;
            }

            needsOptimize = true;
        }

        if (!needsOptimize) {
            return;
        }

        event.preventDefault();
        window.AdminUI?.showLoading?.('Optimizing image…');

        try {
            for (const input of inputs) {
                const file = input.files?.[0];
                if (!file || file.size <= maxBytes) {
                    continue;
                }

                const smaller = await compressRasterImage(
                    file,
                    input.name === 'favicon' ? 256 : 1200,
                    0.82,
                    maxBytes,
                );
                replaceFileInput(input, smaller);
            }

            form.dataset.skipCompress = '1';
            form.submit();
        } finally {
            window.AdminUI?.hideLoading?.();
        }
    });

    form.querySelectorAll('[name="active_theme"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            applyThemeDefaults(radio.value);
            applyLivePreview();
            reloadPreviewIframe(radio.value);
        });
    });

    document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
        iframe.addEventListener('load', () => {
            applyLivePreview();
            scheduleFitDesktopPreview();
        });
    });

    applyLivePreview();
    scheduleFitDesktopPreview();
    window.addEventListener('resize', scheduleFitDesktopPreview);

    if (typeof ResizeObserver !== 'undefined') {
        document.querySelectorAll('[data-preview-scale-wrap]').forEach((wrap) => {
            new ResizeObserver(() => scheduleFitDesktopPreview()).observe(wrap);
        });
    }
};

const initPreviewSlugSync = () => {
    const form = document.querySelector('[data-preview-slug-form]');
    if (!form) return;
    const slugInput = form.querySelector('[name="slug"]');
    const titleInput = form.querySelector('[name="title"]');
    const type = form.dataset.previewType || 'page';
    const basePath = type === 'blog' ? '/blog/' : '/pages/';
    let debounce;
    const update = () => {
        clearTimeout(debounce);
        debounce = setTimeout(() => {
            const slug = (slugInput?.value || '').trim() || slugify(titleInput?.value || '');
            if (!slug) return;
            const url = buildPreviewUrl(`${basePath}${slug}`);
            const label = type === 'blog' ? `Blog: /blog/${slug}` : `Page: /pages/${slug}`;
            setPreviewUrl(url, label);
        }, 400);
    };
    slugInput?.addEventListener('input', update);
    titleInput?.addEventListener('input', update);
};

const reloadPreviewIframes = (device = null) => {
    document.querySelectorAll('[data-theme-preview-iframe]').forEach((iframe) => {
        try {
            const base = iframe.dataset.previewSrc || iframe.src.split('#')[0];
            const url = new URL(base, window.location.origin);
            url.searchParams.set('preview', '1');
            url.searchParams.set('device', device || getPreviewDevice());
            url.searchParams.set('t', Date.now().toString());
            const hash = iframe.dataset.previewHash || '';
            if (hash) {
                url.hash = hash.startsWith('#') ? hash : `#${hash}`;
            }
            iframe.src = url.toString();
        } catch (_) {
            const hash = iframe.dataset.previewHash || '';
            iframe.src = `${iframe.src.split('?')[0]}?preview=1&t=${Date.now()}${hash}`;
        }
    });
    setTimeout(scheduleFitDesktopPreview, 300);
};

const initBuilderPreview = () => {
    document.querySelectorAll('[data-preview-refresh]').forEach((btn) => {
        btn.addEventListener('click', () => reloadPreviewIframes());
    });
    document.querySelectorAll('[data-preview-device]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const device = btn.dataset.previewDevice;
            const panel = btn.closest('[data-builder-preview], [data-builder-preview-mobile]');
            panel?.querySelectorAll('[data-preview-device]').forEach((b) => b.classList.toggle('is-active', b === btn));
            panel?.querySelectorAll('[data-preview-frame]').forEach((frame) => {
                frame.classList.toggle('is-mobile', device === 'mobile');
                frame.classList.toggle('is-desktop', device === 'desktop');
            });
            reloadPreviewIframes(device);
            scheduleFitDesktopPreview();
        });
    });

    scheduleFitDesktopPreview();
    window.addEventListener('resize', scheduleFitDesktopPreview);
};

const initBuilderNavToggle = () => {
    if (document.documentElement.dataset.builderNavToggle === '1') {
        return;
    }

    document.documentElement.dataset.builderNavToggle = '1';

    document.addEventListener('click', (event) => {
        const btn = event.target.closest('[data-builder-nav-toggle]');
        if (!btn) {
            return;
        }

        const body = document.querySelector('[data-builder-nav-body]');
        const chevron = btn.querySelector('[data-builder-nav-chevron]');
        const collapsed = body?.classList.toggle('is-collapsed');
        btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        chevron?.classList.toggle('rotate-180', !collapsed);
    });
};

const bootBuilderUi = () => {
    initBuilderMode();
    initBuilderNavToggle();
    initThemeCustomizerLive();
    initPreviewSlugSync();
    initBuilderPreview();
};

onPageLoad(bootBuilderUi);
