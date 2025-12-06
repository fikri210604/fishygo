@extends('layouts.admin')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <h1 class="text-xl font-semibold">Jenis Ikan</h1>
            <span class="text-sm text-gray-500">{{ $items->total() }} total</span>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari jenis ikan..." class="input input-bordered input-sm w-56" />
                <button class="btn btn-sm">Cari</button>
            </form>
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-create-jenis').showModal()">Tambah</button>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded border">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gambar</th>
                    <th>Jenis Ikan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $row)
                    <tr>
                        <th>{{ ($items->currentPage()-1)*$items->perPage() + $i + 1 }}</th>
                        <td class="align-middle">
                            <div class="relative h-10 w-10">
                                <img
                                    src="{{ $row->gambar_jenis_ikan ? asset('storage/'.$row->gambar_jenis_ikan) : '' }}"
                                    class="h-10 w-10 rounded object-cover {{ $row->gambar_jenis_ikan ? '' : 'hidden' }}"
                                    loading="lazy"
                                    decoding="async"
                                    onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                    alt="Jenis Ikan"
                                >

                                <div class="skeleton h-10 w-10 rounded bg-gray-200 animate-pulse {{ $row->gambar_jenis_ikan ? 'hidden' : '' }}"></div>
                            </div>
                        </td>


                        <td class="font-medium">{{ $row->jenis_ikan }}</td>
                        <td class="text-center">
                            <div class="dropdown dropdown-end">
                                <button type="button" tabindex="0" class="btn btn-xs btn-outline">
                                    Pilih aksi
                                </button>
                                <ul tabindex="0" class="dropdown-content z-[1] menu menu-sm bg-base-100 rounded-box shadow p-1 min-w-[112px]">
                                    <li>
                                        <button type="button"
                                            class="btn btn-ghost btn-xs w-full justify-center"
                                            onclick="document.getElementById('modal-edit-{{ $row->jenis_ikan_id }}').showModal()">
                                            Edit
                                        </button>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.jenis-ikan.destroy', $row->jenis_ikan_id) }}" onsubmit="return confirm('Hapus jenis ikan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-xs w-full justify-center text-red-600">
                                                Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    <dialog id="modal-edit-{{ $row->jenis_ikan_id }}" class="modal">
                        <div class="modal-box">
                            <h3 class="font-semibold text-lg mb-3">Edit Jenis Ikan</h3>
                            <form method="POST" action="{{ route('admin.jenis-ikan.update', $row->jenis_ikan_id) }}" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="label"><span class="label-text">Jenis Ikan</span></label>
                                    <input type="text" name="jenis_ikan" class="input input-bordered w-full" value="{{ $row->jenis_ikan }}" required />
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Gambar (opsional)</span></label>
                                    <input type="file" name="gambar_jenis_ikan" accept="image/*" class="file-input file-input-bordered w-full" />
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

    <dialog id="modal-create-jenis" class="modal">
        <div class="modal-box">
            <h3 class="font-semibold text-lg mb-3">Tambah Jenis Ikan</h3>
            <form method="POST" action="{{ route('admin.jenis-ikan.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="label"><span class="label-text">Jenis Ikan</span></label>
                    <input type="text" name="jenis_ikan" class="input input-bordered w-full" required />
                </div>
                <div>
                    <label class="label"><span class="label-text">Gambar (opsional)</span></label>
                    <input type="file" name="gambar_jenis_ikan" accept="image/*" class="file-input file-input-bordered w-full" />
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
