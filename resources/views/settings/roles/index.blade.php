@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <h1 class="text-xl font-semibold">Roles</h1>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn btn-primary btn-sm" onclick="document.getElementById('modal-create-role').showModal()">Tambah</button>
        </div>
    </div>

    <div class="overflow-x-auto bg-white rounded border">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Aktif</th>
                    <th>#Perm</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $r)
                    <tr>
                        <td class="font-medium">{{ $r->nama }}</td>
                        <td class="text-gray-600">{{ $r->slug }}</td>
                        <td>
                            <span class="text-sm {{ $r->aktif === '1' ? 'text-green-600' : 'text-gray-400' }}">{{ $r->aktif === '1' ? 'Ya' : 'Tidak' }}</span>
                        </td>
                        <td>{{ $r->permissions_count }}</td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <button class="btn btn-xs" onclick="document.getElementById('modal-perms-{{ $r->id }}').showModal()">Permissions</button>
                                <button class="btn btn-xs" onclick="document.getElementById('modal-edit-{{ $r->id }}').showModal()">Edit</button>
                                <form action="{{ route('admin.settings.roles.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus role ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-xs btn-error text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <dialog id="modal-edit-{{ $r->id }}" class="modal">
                        <div class="modal-box max-w-md">
                            <h3 class="font-semibold text-lg mb-3">Edit Role</h3>
                            @php($mid = 'modal-edit-' . $r->id)
                            <form method="POST" action="{{ route('admin.settings.roles.update', $r) }}" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="modal" value="{{ $mid }}" />
                                <div>
                                    <label class="label"><span class="label-text">Nama</span></label>
                                    <input type="text" name="nama" class="input input-bordered w-full" value="{{ old('modal') === $mid ? old('nama', $r->nama) : $r->nama }}" required />
                                    @if(old('modal') === $mid)
                                        @error('nama')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                                    @endif
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Slug</span></label>
                                    <input type="text" name="slug" class="input input-bordered w-full" value="{{ old('modal') === $mid ? old('slug', $r->slug) : $r->slug }}" required />
                                    @if(old('modal') === $mid)
                                        @error('slug')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="aktif" value="1" class="checkbox" {{ (old('modal') === $mid ? (old('aktif') ? true : false) : ($r->aktif === '1')) ? 'checked' : '' }}>
                                    <span class="text-sm">Aktif</span>
                                </div>
                                <div class="modal-action">
                                    <button class="btn btn-primary">Simpan</button>
                                    <form method="dialog"><button class="btn">Batal</button></form>
                                </div>
                            </form>
                        </div>
                        <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
                    </dialog>

                    <dialog id="modal-perms-{{ $r->id }}" class="modal">
                        <div class="modal-box max-w-3xl">
                            <h3 class="font-semibold text-lg mb-3">Permissions untuk: {{ $r->nama }}</h3>
                            <form method="POST" action="{{ route('admin.settings.roles.permissions.update', $r) }}" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <div class="grid md:grid-cols-2 gap-3">
                                    @php($owned = $r->permissions()->pluck('permissions.id')->all())
                                    @foreach($permissions as $p)
                                        <label class="flex items-start gap-3 p-2 rounded hover:bg-gray-50">
                                            <input type="checkbox" name="permission_ids[]" value="{{ $p->id }}" class="mt-1" {{ in_array($p->id, $owned) ? 'checked' : '' }}>
                                            <span>
                                                <div class="font-medium">{{ $p->nama }} <span class="text-xs text-gray-500">({{ $p->slug }})</span></div>
                                                <div class="text-sm text-gray-600">{{ $p->modul ?? '-' }}</div>
                                            </span>
                                        </label>
                                    @endforeach
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
                        <td colspan="5" class="text-center text-gray-500">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <dialog id="modal-create-role" class="modal">
        <div class="modal-box max-w-md">
            <h3 class="font-semibold text-lg mb-3">Tambah Role</h3>
            <form method="POST" action="{{ route('admin.settings.roles.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="modal" value="modal-create-role" />
                <div>
                    <label class="label"><span class="label-text">Nama</span></label>
                    <input type="text" name="nama" class="input input-bordered w-full" value="{{ old('nama') }}" required />
                    @if(old('modal') === 'modal-create-role')
                        @error('nama')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    @endif
                </div>
                <div>
                    <label class="label"><span class="label-text">Slug</span></label>
                    <input type="text" name="slug" class="input input-bordered w-full" value="{{ old('slug') }}" required />
                    @if(old('modal') === 'modal-create-role')
                        @error('slug')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="aktif" value="1" class="checkbox" {{ old('aktif', '1') ? 'checked' : '' }}>
                    <span class="text-sm">Aktif</span>
                </div>
                <div class="modal-action">
                    <button class="btn btn-primary">Simpan</button>
                    <form method="dialog"><button class="btn">Batal</button></form>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop"><button>Tutup</button></form>
    </dialog>
    @if($errors->any() && old('modal'))
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const id = @json(old('modal'));
            const dlg = document.getElementById(id);
            if (dlg && typeof dlg.showModal === 'function') { dlg.showModal(); }
        });
    </script>
    @endif
@endsection





