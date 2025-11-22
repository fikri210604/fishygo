// Simple AJAX handler for cart related forms:
// - Add to cart:  form[data-cart-add="true"]
// - Update qty:   form[data-cart-update="true"]
// - Remove item:  form[data-cart-remove="true"]
// - Clear cart:   form[data-cart-clear="true"]

(function () {
  let lastSubmitter = null;

  function init() {
    document.addEventListener('click', onClick, true);
    document.addEventListener('submit', onSubmit, true);
  }

  function onClick(event) {
    const btn = event.target.closest('button[type="submit"], input[type="submit"]');
    if (!btn) return;
    const form = btn.form;
    if (!form) return;
    lastSubmitter = btn;
  }

  async function onSubmit(event) {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    const isAdd = form.matches('form[data-cart-add="true"]');
    const isUpdate = form.matches('form[data-cart-update="true"]');
    const isRemove = form.matches('form[data-cart-remove="true"]');
    const isClear = form.matches('form[data-cart-clear="true"]');

    if (!isAdd && !isUpdate && !isRemove && !isClear) return;

    event.preventDefault();

    const action = form.action;
    const method = (form.method || 'post').toLowerCase();

    if (!action) return;
    if (!window.axios) {
      form.submit();
      return;
    }

    const submitButton =
      event.submitter ||
      (document.activeElement && document.activeElement.form === form ? document.activeElement : null) ||
      lastSubmitter ||
      form.querySelector('button[type="submit"], input[type="submit"]');

    if (submitButton) submitButton.disabled = true;

    try {
      const formData = new FormData(form);
      if (submitButton && submitButton.name) {
        formData.append(submitButton.name, submitButton.value || '');
      }

      const response = await window.axios({
        method,
        url: action,
        data: formData,
        headers: {
          Accept: 'application/json',
        },
      });

      const data = response?.data || {};
      if (isAdd) {
        handleAddResponse(data);
      } else if (isUpdate || isRemove || isClear) {
        handleCartPageResponse(form, data, { isUpdate, isRemove, isClear });
      }
    } catch (error) {
      handleRequestError(error, isAdd ? 'Gagal menambahkan ke keranjang.' : 'Gagal memperbarui keranjang.');
    } finally {
      if (submitButton) submitButton.disabled = false;
    }
  }

  function ensureToastContainer() {
    let container = document.querySelector('.js-toast-container');
    if (container) return container;

    container = document.createElement('div');
    container.className = 'toast toast-top toast-end z-[60] js-toast-container';
    document.body.appendChild(container);
    return container;
  }

  function showToast(type, text) {
    if (!text) return;
    const container = ensureToastContainer();

    const alert = document.createElement('div');
    let alertClass = 'alert-info';
    if (type === 'success') alertClass = 'alert-success';
    else if (type === 'error') alertClass = 'alert-error';
    else if (type === 'warning') alertClass = 'alert-warning';

    alert.className = `alert ${alertClass} shadow-lg w-80 sm:w-96 mb-2`;

    const span = document.createElement('span');
    span.textContent = text;
    alert.appendChild(span);

    container.appendChild(alert);

    setTimeout(() => {
      alert.remove();
      if (!container.children.length) {
        container.remove();
      }
    }, 3500);
  }

  function updateCartCount(count) {
    if (typeof count !== 'number' || Number.isNaN(count)) return;

    const badge = document.getElementById('navbar-cart-count');
    if (badge) {
      badge.textContent = String(count);
      if (count > 0) {
        badge.classList.remove('hidden');
      } else if (!badge.classList.contains('hidden')) {
        badge.classList.add('hidden');
      }
    }

    const mobileCount = document.getElementById('navbar-cart-count-mobile');
    if (mobileCount) {
      mobileCount.textContent = String(count);
    }
  }

  function handleRequestError(error, fallbackMessage) {
    const res = error?.response;
    const msg =
      res?.data?.message ||
      res?.data?.error ||
      fallbackMessage;
    showToast('error', msg);
  }

  function handleAddResponse(data) {
    const message = typeof data.message === 'string'
      ? data.message
      : 'Produk ditambahkan ke keranjang.';

    showToast('success', message);
    const count = typeof data.cart_count === 'number' ? data.cart_count : data.cartCount;
    updateCartCount(count);
  }

  function formatRupiah(number) {
    if (typeof number !== 'number' || Number.isNaN(number)) return null;
    return 'Rp ' + number.toLocaleString('id-ID');
  }

  function handleCartPageResponse(form, data, flags) {
    const { isUpdate, isRemove, isClear } = flags || {};

    if (data && typeof data.message === 'string') {
      showToast('success', data.message);
    }

    if (typeof data.cart_count === 'number') {
      updateCartCount(data.cart_count);
    } else if (typeof data.cartCount === 'number') {
      updateCartCount(data.cartCount);
    }

    // Update jumlah jenis produk di keranjang (bukan total qty)
    if (typeof data.items_count === 'number') {
      document.querySelectorAll('[data-cart-items-count]').forEach((el) => {
        el.textContent = data.items_count + ' produk di keranjang';
      });
    }

    // Update jumlah item pada tombol checkout (harus sama seperti badge navbar)
    const checkoutBtn = document.querySelector('[data-cart-checkout-count]');
    if (checkoutBtn) {
      let checkoutCount = null;
      if (typeof data.cart_count === 'number') {
        checkoutCount = data.cart_count;
      } else if (typeof data.cartCount === 'number') {
        checkoutCount = data.cartCount;
      } else if (typeof data.items_count === 'number') {
        // fallback kalau backend belum kirim cart_count
        checkoutCount = data.items_count;
      }

      if (checkoutCount !== null) {
        checkoutBtn.textContent = 'Checkout (' + checkoutCount + ')';
      }
    }

    // Update total di bagian bawah keranjang
    if (typeof data.total === 'number') {
      const totalEl = document.getElementById('cart-total');
      const formatted = formatRupiah(data.total);
      if (totalEl && formatted) {
        totalEl.textContent = formatted;
      }
    }

    // Handle update qty di baris item
    if (isUpdate && data.item) {
      const container = form.closest('[data-cart-item]');
      if (!container) return;

      const item = data.item;
      const produkId = String(item.produk_id || '');
      const currentProdukId = container.getAttribute('data-produk-id') || '';
      if (produkId && currentProdukId && produkId !== currentProdukId) {
        // Item tidak cocok, jangan update
        return;
      }

      if (typeof item.qty === 'number') {
        const qtySpan = container.querySelector('[data-cart-qty]');
        if (qtySpan) {
          qtySpan.textContent = String(item.qty);
        }
      }
    }

    // Hapus satu item dari DOM (bisa dari aksi remove maupun update yang membuat qty jadi 0)
    if (data.removed_produk_id) {
      const selector = '[data-cart-item][data-produk-id="' + data.removed_produk_id + '"]';
      const row = document.querySelector(selector);
      if (row) {
        row.remove();
      }
    }

    // Bersihkan semua item dari DOM
    if (isClear) {
      document.querySelectorAll('[data-cart-item]').forEach((el) => el.remove());
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
