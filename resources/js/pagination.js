function initAjaxPagination() {
  const container = document.querySelector('[data-ajax-pagination="produk"]');
  if (!container) return;

  async function load(url) {
    try {
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) { location.href = url; return; }
      const html = await res.text();
      container.innerHTML = html;
      history.pushState({}, '', url);
      container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (e) {
      location.href = url;
    }
  }

  container.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    if (!a.closest('.pagination') && !a.closest('.join')) return;
    const href = a.getAttribute('href');
    if (!href || href === '#') return; 
    
    e.preventDefault();
    load(href);
  });

  window.addEventListener('popstate', () => load(location.href));
  
  // Intercept clicks that target listing URL (/produk?...) including filters & chips
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    const href = a.getAttribute('href') || '';
    if (!href || href === '#') return;
    const isListingUrl = /\/produk\?/i.test(href);
    const isPagination = a.closest('.pagination') || a.closest('.join');
    const isExplicit = a.matches('[data-load-products]');
    if (!(isListingUrl || isPagination || isExplicit)) return;
    e.preventDefault();
    load(href);
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAjaxPagination);
} else {
  initAjaxPagination();
}
