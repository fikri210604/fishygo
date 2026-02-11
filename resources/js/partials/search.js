document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const productList = document.getElementById('product-list');

    if (!searchInput || !productList) return;

    let timeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const query = this.value;

        timeout = setTimeout(() => {
            const url = new URL(window.location.href);
            if (query) {
                url.searchParams.set('q', query);
            } else {
                url.searchParams.delete('q');
            }
            url.searchParams.delete('page_produk'); 

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                productList.innerHTML = html;
                window.history.pushState({}, '', url.toString());
            })
            .catch(error => console.error('Error fetching products:', error));
        }, 300);
    });
    
    window.addEventListener('popstate', function() {
       const url = new URL(window.location.href);
       const q = url.searchParams.get('q') || '';
       if (searchInput) searchInput.value = q;
    });
});
