// =======================================================
// 🌟 Global Form Loading Handler for DaisyUI + Laravel
// =======================================================
//
// ✅ Fitur:
// - Menambahkan animasi loading ke tombol submit (btn.loading dari DaisyUI)
// - Mencegah klik ganda (disable form dan tombol lain)
// - Aman untuk Laravel CSRF + _method
// - Abaikan form modal <dialog> dan tombol non-submit
// - Dapat digunakan juga untuk tombol custom (data-loading-on-click)
//
// =======================================================

function setButtonLoading(btn, loading = true) {
  if (!btn) return;

  try {
    // Jika tombol menggunakan class DaisyUI .btn
    if (btn.classList.contains('btn')) {
      btn.classList.toggle('loading', loading);

      if (loading) {
        btn.setAttribute('disabled', 'disabled');
      } else {
        btn.removeAttribute('disabled');
      }
    } else {
      // Tombol custom tanpa class .btn
      if (loading) {
        if (!btn.dataset.originalHtml) btn.dataset.originalHtml = btn.innerHTML;
        const txt = btn.getAttribute('data-loading-text') || 'Processing...';

        btn.innerHTML = `
          <svg class="animate-spin h-4 w-4 mr-2 inline-block" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
              stroke="currentColor" stroke-width="4" fill="none"></circle>
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
          </svg>
          <span>${txt}</span>
        `;

        btn.classList.add('opacity-60', 'pointer-events-none');
        btn.setAttribute('disabled', 'disabled');
      } else {
        if (btn.dataset.originalHtml) btn.innerHTML = btn.dataset.originalHtml;
        btn.classList.remove('opacity-60', 'pointer-events-none');
        btn.removeAttribute('disabled');
      }
    }
  } catch (err) {
    console.error('setButtonLoading error:', err);
  }
}

function disableForm(form, disabled = true, exceptEl = null) {
  if (!form) return;

  const selector = 'button, input, select, textarea';
  form.querySelectorAll(selector).forEach((el) => {
    if (exceptEl && el === exceptEl) return;

    // Jangan disable hidden input penting milik Laravel
    const isHiddenInput =
      el.tagName === 'INPUT' &&
      (el.type === 'hidden' || el.name === '_method' || el.name === '_token');
    if (isHiddenInput) return;

    try {
      if (disabled) el.setAttribute('disabled', 'disabled');
      else el.removeAttribute('disabled');
    } catch (_) {}
  });
}

function installGlobalFormLoading() {
  // Handle semua event submit
  document.addEventListener(
    'submit',
    function (e) {
      const form = e.target;

      // 🚫 Abaikan form dengan method="dialog" (misal tombol Batal modal DaisyUI)
      if (form.getAttribute('method') === 'dialog') return;

      // 🚫 Abaikan kalau tombol submit tidak ada
      const btn =
        e.submitter ||
        form.querySelector('button[type="submit"], input[type="submit"]');
      if (!btn) return;

      // 🚫 Abaikan tombol non-submit (misal tombol batal atau tutup)
      if (btn.getAttribute('type') !== 'submit') return;

      // 🚫 Abaikan tombol yang diberi atribut data-skip-loading
      if (btn.hasAttribute('data-skip-loading')) return;

      // ✅ Aktifkan loading
      setButtonLoading(btn, true);
      form.setAttribute('aria-busy', 'true');

      // Nonaktifkan field setelah form data terserialisasi
      setTimeout(() => disableForm(form, true, btn), 0);
    },
    true
  );

  // Tombol manual dengan atribut data-loading-on-click
  document.addEventListener('click', function (e) {
    const el = e.target.closest('button[data-loading-on-click]');
    if (!el) return;
    setButtonLoading(el, true);
  });

  // Tombol dengan data-submit-form (programmatically submit form lain)
  document.addEventListener('click', function (e) {
    const el = e.target.closest('[data-submit-form]');
    if (!el) return;

    const selector = el.getAttribute('data-submit-form');
    const form = selector ? document.querySelector(selector) : null;
    if (!form) return;

    e.preventDefault();
    setButtonLoading(el, true);

    try {
      form.requestSubmit ? form.requestSubmit() : form.submit();
    } catch (_) {
      form.submit();
    }

    setTimeout(() => disableForm(form, true, el), 0);
  });
}

// Jalankan setelah DOM siap
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', installGlobalFormLoading);
} else {
  installGlobalFormLoading();
}

export { setButtonLoading };
