@extends('layouts.admin')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <h1 class="text-xl font-semibold">Produk</h1>
            <span class="text-sm text-gray-500">{{ $items->total() }} total</span>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari produk..." class="input input-bordered input-sm w-56" />
                <button class="btn btn-sm">Cari</button>
            </form>
            <a href="{{ route('admin.produk.create') }}" class="btn btn-primary btn-sm">Tambah</a>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded border">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $p)
                    <tr>
                        <th>{{ ($items->currentPage()-1)*$items->perPage() + $i + 1 }}</th>
                        <td class="align-middle">
                            <div class="h-10 w-10 relative flex items-center justify-center">
                                <img
                                    src="{{ $p->gambar_produk ? asset('storage/'.$p->gambar_produk) : '' }}"
                                    class="absolute inset-0 h-10 w-10 rounded object-cover {{ $p->gambar_produk ? '' : 'hidden' }}"
                                    loading="lazy"
                                    decoding="async"
                                    onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                    alt="Produk"
                                >
                        
                                <div class="skeleton h-10 w-10 rounded bg-gray-200 animate-pulse {{ $p->gambar_produk ? 'hidden' : '' }}"></div>
                            </div>
                        </td>
                        
                        
                        <td class="font-medium">{{ $p->nama_produk }}</td>
                        <td><code>{{ $p->kode_produk }}</code></td>
                        <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                        <td>{{ $p->stok }}</td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.produk.edit', $p->produk_id) }}" class="btn btn-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.produk.destroy', $p->produk_id) }}" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-error text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-center">
        <div class="join">
            <a href="{{ $items->appends(request()->query())->previousPageUrl() ?: '#' }}" class="join-item btn btn-sm {{ $items->onFirstPage() ? 'btn-disabled' : '' }}">«</a>
            <button class="join-item btn btn-sm">Page {{ $items->currentPage() }} / {{ $items->lastPage() }}</button>
            <a href="{{ $items->appends(request()->query())->nextPageUrl() ?: '#' }}" class="join-item btn btn-sm {{ $items->hasMorePages() ? '' : 'btn-disabled' }}">»</a>
        </div>
    </div>
@endsection
