@extends('layouts.admin')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <h1 class="text-xl font-semibold">Kategori Produk</h1>
            {{-- <span class="text-sm text-gray-500">{{ $items->total() }} total</span> --}}
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari kategori..." class="input input-bordered input-sm w-56" />
                <button class="btn btn-sm">Cari</button>
            </form>
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-create-kategori').showModal()">Tambah</button>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded border">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gambar</th>
                    <th>Nama Kategori</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $row)
                    <tr>
                        <th>{{ ($items->currentPage()-1)*$items->perPage() + $i + 1 }}</th>
                        <td>
                            @if($row->gambar_kategori)
    <img src="{{ asset('storage/'.$row->gambar_kategori) }}"
         class="h-10 w-10 rounded object-cover"
         loading="lazy" decoding="async" width="40" height="40"
         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
    <div class="skeleton h-10 w-10 rounded hidden"></div>
@else
    <div class="skeleton h-10 w-10 rounded"></div>
@endif
                        </td>
                        <td class="font-medium">{{ $row->nama_kategori }}</td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <button class="btn btn-xs" onclick="document.getElementById('modal-edit-{{ $row->kategori_produk_id }}').showModal()">Edit</button>
                                <form method="POST" action="{{ route('admin.kategori.destroy', $row->kategori_produk_id) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-error text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <dialog id="modal-edit-{{ $row->kategori_produk_id }}" class="modal">
                        <div class="modal-box">
                            <h3 class="font-semibold text-lg mb-3">Edit Kategori</h3>
                            <form method="POST" action="{{ route('admin.kategori.update', $row->kategori_produk_id) }}" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="label"><span class="label-text">Nama Kategori</span></label>
                                    <input type="text" name="nama_kategori" class="input input-bordered w-full" value="{{ $row->nama_kategori }}" required />
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Gambar (opsional)</span></label>
                                    <input type="file" name="gambar_kategori" accept="image/*" class="file-input file-input-bordered w-full" />
                                </div>
                                <div class="modal-action">
                                    <button class="btn btn-primary">Simpan</button>
                                    <form method="dialog"><button class="btn">Batal</button></form>
                                </div>
                            </form>
                        </div>
                        <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
                    </dialog>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500">Belum ada data.</td>
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

    <dialog id="modal-create-kategori" class="modal">
        <div class="modal-box">
            <h3 class="font-semibold text-lg mb-3">Tambah Kategori</h3>
            <form method="POST" action="{{ route('admin.kategori.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="label"><span class="label-text">Nama Kategori</span></label>
                    <input type="text" name="nama_kategori" class="input input-bordered w-full" required />
                </div>
                <div>
                    <label class="label"><span class="label-text">Gambar (opsional)</span></label>
                    <input type="file" name="gambar_kategori" accept="image/*" class="file-input file-input-bordered w-full" />
                </div>
                <div class="modal-action">
                    <button class="btn btn-primary">Simpan</button>
                    <form method="dialog"><button class="btn">Batal</button></form>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
    </dialog>
@endsection
