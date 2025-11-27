<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5 mb-6">
    @forelse($produk as $p)
        <div class="card bg-base-100 shadow hover:shadow-md transition">
            <figure class="h-40 overflow-hidden relative">
                <div class="skeleton absolute inset-0 bg-gray-200 animate-pulse"></div>
                <img src="{{ $p->gambar_produk ? asset('storage/' . $p->gambar_produk) : '' }}" alt="{{ $p->nama_produk }}"
                    class="absolute inset-0 w-full h-full object-cover opacity-0" loading="lazy" decoding="async"
                    sizes="(max-width: 640px) 50vw, (max-width: 1024px) 33vw, 25vw"
                    onload="this.classList.remove('opacity-0'); this.previousElementSibling.classList.add('hidden');">
            </figure>
            <div class="card-body p-3">
                <p class="font-semibold text-sm">{{ $p->nama_produk }}</p>
                <p class="text-sm text-primary font-bold">Rp {{ number_format($p->harga ?? 0, 0, ',', '.') }}</p>
                <div class="card-actions justify-between mt-2">
                    @if(!empty($p->slug))
                        <a href="{{ route('produk.show', ['produk' => $p->slug]) }}" class="btn btn-xs bg-gray-200">Detail</a>
                    @else
                        <span class="btn btn-xs bg-gray-200 btn-disabled" title="Slug tidak tersedia">Detail</span>
                    @endif
                    @auth
                        <form action="{{ route('cart.add', $p->produk_id) }}" method="POST" data-cart-add="true">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-primary flex items-center gap-1">
                                <span class="text-xs font-bold">+</span>
                                <i class="ri-shopping-cart-2-line text-sm"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-xs btn-primary flex items-center gap-1">
                            <span class="text-xs font-bold">+</span>
                            <i class="ri-shopping-cart-2-line text-sm"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    @empty
        <p class="col-span-full text-gray-500 text-center">Produk belum tersedia.</p>
    @endforelse
</div>

<div class="mb-10 flex justify-center">
    <div class="join pagination">
        <a href="{{ $produk->previousPageUrl() ?: '#' }}"
            class="join-item btn btn-sm {{ $produk->onFirstPage() ? 'btn-disabled' : '' }}">«</a>
        <button class="join-item btn btn-sm">Page {{ $produk->currentPage() }} / {{ $produk->lastPage() }}</button>
        <a href="{{ $produk->nextPageUrl() ?: '#' }}"
            class="join-item btn btn-sm {{ $produk->hasMorePages() ? '' : 'btn-disabled' }}">»</a>
    </div>
</div>