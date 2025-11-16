window.globalButtonLoading = function (event) {
    const btn = event.submitter;
    if (!btn) return;

    const btnText = btn.querySelector('.btn-text');
    const loader = btn.querySelector('.loader');

    if (btnText && loader) {
        btn.disabled = true;
        btnText.classList.add('hidden');
        loader.classList.remove('hidden');
    }
};

window.addEventListener('DOMContentLoaded', () => {
    if (window.hasFormError) {
        document.querySelectorAll('button[type="submit"]').forEach(btn => {
            btn.disabled = false;
            btn.querySelector('.btn-text')?.classList.remove('hidden');
            btn.querySelector('.loader')?.classList.add('hidden');
        });
    }
});
