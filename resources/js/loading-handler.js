// Decide if a button should use a compact loader (no text)
function isSmallButton(btn) {
    const cls = (btn.getAttribute('class') || '');
    return /\b(btn-xs|btn-sm|text-xs)\b/.test(cls) || (/\bbtn-circle\b/.test(cls) && /\bbtn-sm\b/.test(cls));
}

// Create loader markup depending on button size (using app.css/DaisyUI-like classes)
function createLoaderHTML(small) {
    if (small) {
        return '<span class="loader inline-flex items-center"><span class="loading loading-sm"></span></span>';
    }
    return '<span class="loader hidden items-center gap-2 text-sm"><span class="loading loading-md"></span><span>Memuat...</span></span>';
}

// Ensure button has standardized structure: .btn-text + .loader
function ensureButtonStructure(btn) {
    let btnText = btn.querySelector('.btn-text');
    let loader = btn.querySelector('.loader');

    if (!btnText) {
        const original = btn.innerHTML;
        btn.innerHTML = `<span class=\"btn-text\">${original}</span>` + createLoaderHTML(isSmallButton(btn));
        btnText = btn.querySelector('.btn-text');
        loader = btn.querySelector('.loader');
    } else if (!loader) {
        btn.insertAdjacentHTML('beforeend', createLoaderHTML(isSmallButton(btn)));
        loader = btn.querySelector('.loader');
    }

    return { btnText, loader };
}

window.globalButtonLoading = function (event) {
    if (!event) return;
    let btn = event.submitter;

    // Fallback: if event.submitter is not available, find first submit button in the form
    if (!btn && event.target && event.target.querySelector) {
        btn = event.target.querySelector('button[type="submit"]');
    }
    if (!btn) return;

    // Avoid re-applying
    if (btn.dataset.loadingApplied === '1') return;

    const { btnText, loader } = ensureButtonStructure(btn);

    if (btnText && loader) {
        btn.disabled = true;
        btn.setAttribute('aria-busy', 'true');
        btnText.classList.add('hidden');
        loader.classList.remove('hidden');
        btn.dataset.loadingApplied = '1';
    }
};

// Global handler: apply to all form submissions
document.addEventListener('submit', (e) => {
    try {
        const form = e.target;
        // Ignore dialog-close forms and any form explicitly opted-out
        const isDialog = form && (form.getAttribute('method') || '').toLowerCase() === 'dialog';
        const optedOut = form && form.matches('[data-no-loading]');
        if (isDialog || optedOut) return;
        window.globalButtonLoading(e);
    } catch {}
}, true);

// On reload after validation errors, restore buttons on pages that set window.hasFormError
window.addEventListener('DOMContentLoaded', () => {
    if (window.hasFormError) {
        document.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
            btn.querySelector('.btn-text')?.classList.remove('hidden');
            btn.querySelector('.loader')?.classList.add('hidden');
            delete btn.dataset.loadingApplied;
        });
    }
});
