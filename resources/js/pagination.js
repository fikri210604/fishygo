document.addEventListener('DOMContentLoaded', () => {
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
  
  // Intercept clicks on category links to load products via AJAX
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[data-load-products]');
    if (!a) return;
    const href = a.getAttribute('href');
    if (!href || href === '#') return;
    e.preventDefault();
    load(href);
  });
});
