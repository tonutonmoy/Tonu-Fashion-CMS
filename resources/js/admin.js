const slugify = (value) => value
    .toLowerCase()
    .trim()
    .replace(/[^\w\s-]/g, '')
    .replace(/[\s_-]+/g, '-')
    .replace(/^-+|-+$/g, '');

const showToast = (message, type = 'success') => {
    const root = document.getElementById('admin-toast-root');
    if (!root || !message) {
        return;
    }

    const colors = {
        success: 'bg-green-600',
        error: 'bg-red-600',
        info: 'bg-blue-600',
    };

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto text-white px-4 py-3 rounded-xl shadow-lg text-sm font-medium transform transition ${colors[type] || colors.success}`;
    toast.textContent = message;
    root.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('translate-x-0', 'opacity-100');
    });

    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-x-4');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
};

const initToasts = () => {
    document.querySelectorAll('[data-admin-toast]').forEach((el) => {
        showToast(el.textContent.trim(), el.dataset.type || 'success');
    });
};

const showLoading = (message = 'Please wait…') => {
    const overlay = document.getElementById('admin-loading');
    const text = document.getElementById('admin-loading-text');
    if (!overlay) return;
    if (text) text.textContent = message;
    overlay.classList.remove('hidden');
};

const hideLoading = () => {
    document.getElementById('admin-loading')?.classList.add('hidden');
};

const initFormLoading = () => {
    document.querySelectorAll('form').forEach((form) => {
        if (form.dataset.noLoading === '1') return;

        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) return;
            if (form.dataset.confirm && form.dataset.confirmed !== 'true') return;

            showLoading(form.dataset.loadingMessage || 'Saving…');
        });
    });

    window.addEventListener('pageshow', () => hideLoading());
};

const initSlugFields = () => {
    document.querySelectorAll('[data-slug-source]').forEach((nameInput) => {
        const slugInput = document.querySelector(nameInput.dataset.slugTarget);
        if (!slugInput) {
            return;
        }

        let manual = slugInput.dataset.manual === 'true';

        slugInput.addEventListener('input', () => {
            manual = true;
            slugInput.dataset.manual = 'true';
        });

        nameInput.addEventListener('input', () => {
            if (!manual) {
                slugInput.value = slugify(nameInput.value);
            }
            const preview = document.getElementById('product-slug-preview');
            if (preview) {
                preview.textContent = slugInput.value || 'your-product';
            }
        });

        slugInput.addEventListener('input', () => {
            const preview = document.getElementById('product-slug-preview');
            if (preview) {
                preview.textContent = slugInput.value || 'your-product';
            }
        });
    });
};

const formatFileSize = (bytes) => {
    if (!bytes) return '0 B';
    const units = ['B', 'KB', 'MB', 'GB'];
    const i = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
    return `${Math.round((bytes / (1024 ** i)) * 10) / 10} ${units[i]}`;
};

const uploaderStatusText = (files, multiple) => {
    const count = files?.length || 0;
    if (!count) return 'No file selected';
    if (count === 1) return files[0].name;
    return `${count} files selected`;
};

const buildUploaderMarkup = (name, label, { multiple = false, compact = false } = {}) => `
    <div class="admin-uploader ${compact ? 'admin-uploader--compact' : ''}" data-uploader data-multiple="${multiple ? '1' : '0'}" data-preview-mode="image">
        <label class="label">${label}</label>
        <div class="admin-uploader-drop" data-uploader-drop tabindex="0" role="button">
            <input type="file" name="${name}" accept="image/*" class="sr-only" data-uploader-input${multiple ? ' multiple' : ''}>
            <div class="admin-uploader-drop-inner pointer-events-none">
                <div class="admin-uploader-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="admin-uploader-title">${compact ? 'Drop image or <span class="text-brand-600">browse</span>' : 'Drag & drop an image here'}</p>
                ${compact ? '' : '<p class="admin-uploader-subtitle">or click to choose from your device</p><span class="admin-uploader-btn">Choose file</span>'}
                <p class="admin-uploader-status" data-uploader-status>No file selected</p>
            </div>
        </div>
        <div class="admin-uploader-preview grid grid-cols-2 gap-3 mt-3" data-uploader-preview></div>
    </div>
`;

const initUploaders = () => {
    document.querySelectorAll('[data-uploader]').forEach((uploader) => {
        if (uploader.dataset.uploaderReady === '1') return;
        uploader.dataset.uploaderReady = '1';

        const input = uploader.querySelector('[data-uploader-input]');
        const drop = uploader.querySelector('[data-uploader-drop]');
        const preview = uploader.querySelector('[data-uploader-preview]');
        const status = uploader.querySelector('[data-uploader-status]');
        const multiple = uploader.dataset.multiple === '1';
        const previewMode = uploader.dataset.previewMode || 'image';

        if (!input || !drop || !preview) return;

        const updateStatus = (files) => {
            if (!status) return;
            status.textContent = uploaderStatusText(files, multiple);
            status.classList.toggle('has-files', (files?.length || 0) > 0);
        };

        const renderImagePreview = (files) => {
            preview.innerHTML = '';
            [...files].forEach((file, index) => {
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = (e) => {
                    const card = document.createElement('div');
                    card.className = 'relative rounded-xl border border-gray-200 overflow-hidden bg-gray-50 shadow-sm group';
                    card.innerHTML = `
                        <img src="${e.target.result}" alt="" class="w-full h-32 object-cover">
                        <button type="button" class="admin-uploader-remove opacity-100 sm:opacity-0 sm:group-hover:opacity-100" data-remove-preview="${index}" aria-label="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        ${multiple ? `<span class="absolute bottom-2 left-2 bg-gray-900/80 text-white text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full">New</span>` : ''}
                    `;
                    preview.appendChild(card);

                    card.querySelector('[data-remove-preview]')?.addEventListener('click', (event) => {
                        event.stopPropagation();
                        const dt = new DataTransfer();
                        [...input.files].forEach((f, i) => {
                            if (i !== index) dt.items.add(f);
                        });
                        input.files = dt.files;
                        renderPreview(input.files);
                    });
                };
                reader.readAsDataURL(file);
            });
        };

        const renderFilePreview = (files) => {
            preview.innerHTML = '';
            [...files].forEach((file, index) => {
                const ext = file.name.split('.').pop()?.slice(0, 4) || 'file';
                const row = document.createElement('div');
                row.className = 'admin-uploader-file-row';
                row.innerHTML = `
                    <div class="admin-uploader-file-icon">${ext}</div>
                    <div class="admin-uploader-file-meta">
                        <p class="admin-uploader-file-name">${file.name}</p>
                        <p class="admin-uploader-file-size">${formatFileSize(file.size)}</p>
                    </div>
                    <button type="button" class="admin-uploader-file-remove" data-remove-preview="${index}" aria-label="Remove">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                `;
                preview.appendChild(row);

                row.querySelector('[data-remove-preview]')?.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const dt = new DataTransfer();
                    [...input.files].forEach((f, i) => {
                        if (i !== index) dt.items.add(f);
                    });
                    input.files = dt.files;
                    renderPreview(input.files);
                });
            });
        };

        const renderPreview = (files) => {
            updateStatus(files);
            if (!files?.length) {
                preview.innerHTML = '';
                return;
            }
            if (previewMode === 'file') {
                renderFilePreview(files);
            } else {
                renderImagePreview(files);
            }
        };

        const assignFiles = (fileList) => {
            if (!fileList?.length) return;
            const dt = new DataTransfer();
            if (multiple) {
                [...(input.files || []), ...fileList].forEach((f) => dt.items.add(f));
            } else if (fileList[0]) {
                dt.items.add(fileList[0]);
            }
            if (!dt.files.length) return;
            input.files = dt.files;
            renderPreview(input.files);
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        drop.addEventListener('click', () => input.click());

        drop.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                input.click();
            }
        });

        drop.addEventListener('dragover', (e) => {
            e.preventDefault();
            drop.classList.add('is-dragover');
        });

        drop.addEventListener('dragleave', () => {
            drop.classList.remove('is-dragover');
        });

        drop.addEventListener('drop', (e) => {
            e.preventDefault();
            drop.classList.remove('is-dragover');
            if (e.dataTransfer.files?.length) {
                assignFiles(e.dataTransfer.files);
            }
        });

        input.addEventListener('change', () => renderPreview(input.files));

        uploader.querySelectorAll('[data-remove-existing]').forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.stopPropagation();
                const id = btn.dataset.removeExisting;
                const item = btn.closest('[data-existing-id]');
                const hidden = item?.querySelector('[data-remove-input]');
                if (hidden) {
                    hidden.disabled = false;
                    hidden.value = id;
                }
                item?.classList.add('opacity-40', 'grayscale');
                btn.remove();
            });
        });

        updateStatus(input.files);
    });
};

const initVariantBuilder = () => {
    const catalog = document.getElementById('variant-catalog');
    const container = document.getElementById('variants');
    const addBtn = document.getElementById('add-variant-row');
    if (!catalog || !container || !addBtn) {
        return;
    }

    let index = container.querySelectorAll('[data-variant-row]').length;

    const readCatalog = (type) => {
        const list = catalog.querySelector(`[data-option-list="${type}"]`);
        if (!list) return [];
        return [...list.querySelectorAll('[data-value]')].map((tag) => tag.dataset.value || '').filter(Boolean);
    };

    const syncDataset = () => {
        const sizes = readCatalog('sizes');
        const colors = readCatalog('colors');
        container.dataset.sizes = JSON.stringify(sizes);
        container.dataset.colors = JSON.stringify(colors);
        return { sizes, colors };
    };

    const chipHtml = (type, value, activeValue) => {
        const attr = type === 'sizes' ? 'data-variant-size' : 'data-variant-color';
        const active = activeValue === value ? ' is-active' : '';
        return `<button type="button" class="variant-chip${active}" ${attr}="${value}">${value}</button>`;
    };

    const renderRowOptions = (row, sizes, colors) => {
        const sizeValue = row.querySelector('[data-size-input]')?.value || '';
        const colorValue = row.querySelector('[data-color-input]')?.value || '';
        const sizeWrap = row.querySelector('[data-variant-size-group]');
        const colorWrap = row.querySelector('[data-variant-color-group]');
        if (sizeWrap) {
            sizeWrap.innerHTML = sizes.map((s) => chipHtml('sizes', s, sizeValue)).join('');
        }
        if (colorWrap) {
            colorWrap.innerHTML = colors.map((c) => chipHtml('colors', c, colorValue)).join('');
        }
    };

    const refreshAllRows = () => {
        const { sizes, colors } = syncDataset();
        container.querySelectorAll('[data-variant-row]').forEach((row) => {
            renderRowOptions(row, sizes, colors);
            bindRow(row);
        });
    };

    const createOptionTag = (type, value) => {
        const tag = document.createElement('span');
        tag.className = 'variant-option-tag';
        tag.dataset.value = value;

        const label = document.createElement('span');
        label.dataset.optionLabel = '';
        label.textContent = value;

        const editBtn = document.createElement('button');
        editBtn.type = 'button';
        editBtn.className = 'variant-option-btn';
        editBtn.dataset.optionEdit = '';
        editBtn.title = 'Edit';
        editBtn.textContent = '✎';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'variant-option-btn variant-option-btn--danger';
        removeBtn.dataset.optionRemove = '';
        removeBtn.title = 'Delete';
        removeBtn.textContent = '×';

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = `variant_catalog_${type}[]`;
        hidden.value = value;

        tag.append(label, editBtn, removeBtn, hidden);

        return tag;
    };

    const addOption = (type, rawValue) => {
        const value = String(rawValue || '').trim();
        if (!value) return false;

        const list = catalog.querySelector(`[data-option-list="${type}"]`);
        if (!list) return false;

        const exists = [...list.querySelectorAll('[data-value]')].some((tag) => tag.dataset.value === value);
        if (exists) {
            alert(`"${value}" already exists.`);
            return false;
        }

        list.appendChild(createOptionTag(type, value));
        refreshAllRows();
        return true;
    };

    const removeOption = (type, value) => {
        const list = catalog.querySelector(`[data-option-list="${type}"]`);
        const tag = list?.querySelector(`[data-value="${CSS.escape(value)}"]`);
        if (!tag) return;

        if (!confirm(`Remove "${value}" from ${type}?`)) return;

        tag.remove();

        container.querySelectorAll('[data-variant-row]').forEach((row) => {
            const input = row.querySelector(type === 'sizes' ? '[data-size-input]' : '[data-color-input]');
            if (input?.value === value) {
                input.value = '';
            }
        });

        refreshAllRows();
    };

    const editOption = (type, oldValue) => {
        const newValue = prompt(`Edit ${type.slice(0, -1)}`, oldValue);
        if (newValue === null) return;

        const trimmed = newValue.trim();
        if (!trimmed || trimmed === oldValue) return;

        const list = catalog.querySelector(`[data-option-list="${type}"]`);
        const exists = [...list.querySelectorAll('[data-value]')].some((tag) => tag.dataset.value === trimmed);
        if (exists) {
            alert(`"${trimmed}" already exists.`);
            return;
        }

        const tag = list?.querySelector(`[data-value="${CSS.escape(oldValue)}"]`);
        if (!tag) return;

        tag.dataset.value = trimmed;
        tag.querySelector('[data-option-label]').textContent = trimmed;
        tag.querySelector('input[type="hidden"]').value = trimmed;

        container.querySelectorAll('[data-variant-row]').forEach((row) => {
            const input = row.querySelector(type === 'sizes' ? '[data-size-input]' : '[data-color-input]');
            if (input?.value === oldValue) {
                input.value = trimmed;
            }
        });

        refreshAllRows();
    };

    const bindRow = (row) => {
        row.querySelectorAll('[data-variant-size]').forEach((btn) => {
            btn.onclick = () => {
                row.querySelector('[data-size-input]').value = btn.dataset.variantSize;
                row.querySelectorAll('[data-variant-size]').forEach((b) => b.classList.toggle('is-active', b === btn));
            };
        });

        row.querySelectorAll('[data-variant-color]').forEach((btn) => {
            btn.onclick = () => {
                row.querySelector('[data-color-input]').value = btn.dataset.variantColor;
                row.querySelectorAll('[data-variant-color]').forEach((b) => b.classList.toggle('is-active', b === btn));
            };
        });

        const removeBtn = row.querySelector('[data-remove-variant]');
        if (removeBtn) {
            removeBtn.onclick = () => row.remove();
        }
    };

    catalog.querySelectorAll('[data-option-list]').forEach((list) => {
        const type = list.dataset.optionList;
        list.addEventListener('click', (e) => {
            const tag = e.target.closest('[data-value]');
            if (!tag) return;

            if (e.target.closest('[data-option-remove]')) {
                e.preventDefault();
                removeOption(type, tag.dataset.value);
            } else if (e.target.closest('[data-option-edit]')) {
                e.preventDefault();
                editOption(type, tag.dataset.value);
            }
        });
    });

    catalog.querySelectorAll('[data-option-add]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.optionAdd;
            const input = catalog.querySelector(`[data-option-add-input="${type}"]`);
            if (!input) return;
            if (addOption(type, input.value)) {
                input.value = '';
                input.focus();
            }
        });
    });

    catalog.querySelectorAll('[data-option-add-input]').forEach((input) => {
        input.addEventListener('keydown', (e) => {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            const type = input.dataset.optionAddInput;
            const btn = catalog.querySelector(`[data-option-add="${type}"]`);
            btn?.click();
        });
    });

    container.querySelectorAll('[data-variant-row]').forEach((row) => {
        if (!row.querySelector('[data-variant-size-group]')) {
            const sizeDiv = row.querySelector('[data-variant-size]')?.parentElement;
            if (sizeDiv) sizeDiv.setAttribute('data-variant-size-group', '');
        }
        if (!row.querySelector('[data-variant-color-group]')) {
            const colorDiv = row.querySelector('[data-variant-color]')?.parentElement;
            if (colorDiv) colorDiv.setAttribute('data-variant-color-group', '');
        }
        bindRow(row);
    });

    addBtn.addEventListener('click', () => {
        const { sizes, colors } = syncDataset();
        const row = document.createElement('div');
        row.className = 'border border-gray-200 rounded-xl p-4 space-y-3';
        row.dataset.variantRow = '1';
        row.innerHTML = `
            <div class="flex justify-between items-center">
                <p class="text-sm font-medium">Variant #${index + 1}</p>
                <button type="button" class="text-red-600 text-sm" data-remove-variant>Remove</button>
            </div>
            <input type="hidden" name="variants[${index}][size]" data-size-input>
            <input type="hidden" name="variants[${index}][color]" data-color-input>
            <div><p class="text-xs text-gray-500 mb-2">Size</p><div class="flex flex-wrap gap-2" data-variant-size-group></div></div>
            <div><p class="text-xs text-gray-500 mb-2">Color</p><div class="flex flex-wrap gap-2" data-variant-color-group></div></div>
            <div class="grid grid-cols-2 gap-2">
                <input type="number" name="variants[${index}][stock]" value="10" placeholder="Stock" class="input">
                <input type="number" name="variants[${index}][price_adjustment]" value="0" step="0.01" placeholder="Price +/-" class="input">
            </div>
            ${buildUploaderMarkup(`variants[${index}][image]`, 'Variant Image')}
        `;
        container.appendChild(row);
        renderRowOptions(row, sizes, colors);
        bindRow(row);
        initUploaders();
        index += 1;
    });

    syncDataset();
};

const initConfirmModal = () => {
    const modal = document.getElementById('admin-confirm-modal');
    if (!modal) {
        return;
    }

    let pendingForm = null;
    const title = modal.querySelector('[data-confirm-title]');
    const message = modal.querySelector('[data-confirm-message]');
    const okBtn = modal.querySelector('[data-confirm-ok]');

    const close = () => {
        modal.classList.add('hidden');
        pendingForm = null;
    };

    modal.querySelectorAll('[data-confirm-cancel]').forEach((el) => el.addEventListener('click', close));

    okBtn?.addEventListener('click', () => {
        if (pendingForm) {
            pendingForm.dataset.confirmed = 'true';
            pendingForm.submit();
        }
        close();
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }

            e.preventDefault();
            pendingForm = form;
            title.textContent = form.dataset.confirmTitle || 'Are you sure?';
            message.textContent = form.dataset.confirmMessage || 'This action cannot be undone.';
            okBtn.textContent = form.dataset.confirmOk || 'Confirm';
            modal.classList.remove('hidden');
        });
    });
};

const refreshCsrfToken = async () => {
    const response = await fetch('/csrf-token', {
        credentials: 'same-origin',
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
    });

    if (!response.ok) {
        return null;
    }

    const data = await response.json();
    const token = data.token;

    if (!token) {
        return null;
    }

    document.querySelector('meta[name="csrf-token"]')?.setAttribute('content', token);
    document.querySelectorAll('input[name="_token"]').forEach((input) => {
        input.value = token;
    });

    if (window.axios) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }

    return token;
};

const initAdminCsrfGuard = () => {
    refreshCsrfToken().catch(() => {});

    window.setInterval(() => {
        refreshCsrfToken().catch(() => {});
    }, 10 * 60 * 1000);

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            refreshCsrfToken().catch(() => {});
        }
    });
};

const initAdminNavGroups = () => {
    document.querySelectorAll('[data-admin-nav-group]').forEach((group) => {
        const button = group.querySelector('button');
        const children = group.querySelector('[data-admin-nav-children]');
        const chevron = group.querySelector('.admin-nav-chevron');
        if (!button || !children) return;

        button.addEventListener('click', () => {
            const expanded = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            children.classList.toggle('hidden', expanded);
            chevron?.classList.toggle('rotate-180', !expanded);
        });
    });
};

const initLowStockBell = () => {
    const root = document.querySelector('[data-admin-low-stock]');
    if (!root) return;

    const toggle = root.querySelector('[data-admin-low-stock-toggle]');
    const panel = root.querySelector('[data-admin-low-stock-panel]');
    if (!toggle || !panel) return;

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        panel.classList.toggle('hidden');
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) {
            panel.classList.add('hidden');
        }
    });
};

const initAdmin = () => {
    if (!document.body.classList.contains('admin-body')) {
        return;
    }

    initToasts();
    initAdminCsrfGuard();
    initFormLoading();
    initSlugFields();
    initUploaders();
    initVariantBuilder();
    initConfirmModal();
    initAdminNavGroups();
    initLowStockBell();
};

document.addEventListener('DOMContentLoaded', initAdmin);

window.AdminUI = { showToast, showLoading, hideLoading, refreshCsrfToken };
