@extends('layouts.admin')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <h1 class="text-xl font-semibold">Artikel</h1>
            <span class="text-sm text-gray-500">{{ $articles->total() }} total</span>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Cari artikel..." class="input input-bordered input-sm w-56" />
                <button class="btn btn-sm">Cari</button>
            </form>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">Tambah</a>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded border">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Thumbnail</th>
                    <th>Judul</th>
                    <th>Slug</th>
                    <th>Terbit</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($articles as $i => $a)
                    <tr>
                        <th>{{ ($articles->currentPage()-1)*$articles->perPage() + $i + 1 }}</th>
                        <td>
                            @if($a->thumbnail)
                                <img src="{{ asset('storage/'.$a->thumbnail) }}" class="h-10 w-16 rounded object-cover" loading="lazy" decoding="async" width="64" height="40" />
                            @else
                                <div class="skeleton h-10 w-16 rounded bg-gray-200 animate-pulse"></div>
                            @endif
                        </td>
                        <td class="font-medium">{{ $a->judul }}</td>
                        <td>{{ $a->slug }}</td>
                        <td>{{ $a->diterbitkan_pada ? $a->diterbitkan_pada->format('Y-m-d H:i') : '-' }}</td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.articles.edit', $a) }}" class="btn btn-xs">Edit</a>
                                <button class="btn btn-xs btn-error text-white" onclick="document.getElementById('confirm-delete-article-{{ $a->id }}').showModal()">Hapus</button>
                            </div>

                            <dialog id="confirm-delete-article-{{ $a->id }}" class="modal">
                                <div class="modal-box">
                                    <h3 class="font-bold text-lg text-error">Hapus Artikel</h3>
                                    <p class="py-3 text-gray-700">Yakin ingin menghapus artikel ini?</p>
                                    <div class="modal-action">
                                        <form action="{{ route('admin.articles.destroy', $a) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-error">Ya, Hapus</button>
                                        </form>
                                        <form method="dialog">
                                            <button class="btn">Batal</button>
                                        </form>
                                    </div>
                                </div>
                                <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
                            </dialog>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500">Belum ada artikel.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-center">
        <div class="join">
            <a href="{{ $articles->appends(request()->query())->previousPageUrl() ?: '#' }}" class="join-item btn btn-sm {{ $articles->onFirstPage() ? 'btn-disabled' : '' }}">«</a>
            <button class="join-item btn btn-sm">Page {{ $articles->currentPage() }} / {{ $articles->lastPage() }}</button>
            <a href="{{ $articles->appends(request()->query())->nextPageUrl() ?: '#' }}" class="join-item btn btn-sm {{ $articles->hasMorePages() ? '' : 'btn-disabled' }}">»</a>
        </div>
    </div>
@endsection
