@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-4">
  <div class="flex items-center gap-2">
    <h1 class="text-xl font-semibold">Permissions</h1>
  </div>
  <div class="flex items-center gap-2">
    <button class="btn btn-primary btn-sm"
      onclick="document.getElementById('modal-create-permission').showModal()">Tambah</button>
  </div>
</div>

<div class="overflow-x-auto bg-white rounded border">
  <table class="table table-zebra">
    <thead>
      <tr>
        <th>Nama</th>
        <th>Slug</th>
        <th>Modul</th>
        <th>Aktif</th>
        <th class="text-center">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($permissions as $p)
      <tr>
        <td class="font-medium">{{ $p->nama }}</td>
        <td class="text-gray-600">{{ $p->slug }}</td>
        <td class="text-gray-600">{{ $p->modul ?? '-' }}</td>
        <td>
          <span
            class="text-sm {{ $p->aktif === '1' ? 'text-green-600' : 'text-gray-400' }}">{{ $p->aktif === '1' ? 'Ya' : 'Tidak' }}</span>
        </td>
        <td class="text-center">
          <div class="flex justify-center gap-2">
            <button class="btn btn-xs"
              onclick="document.getElementById('modal-edit-{{ $p->id }}').showModal()">Edit</button>
            <form action="{{ route('admin.settings.permissions.destroy', $p) }}" method="POST"
              onsubmit="return confirm('Hapus permission ini?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-xs btn-error text-white">Hapus</button>
            </form>
          </div>
        </td>
      </tr>

      <dialog id="modal-edit-{{ $p->id }}" class="modal">
        <div class="modal-box max-w-2xl">
          <h3 class="font-semibold text-lg mb-3">Edit Permission</h3>
          @php($mid = 'modal-edit-' . $p->id)
          <form method="POST" action="{{ route('admin.settings.permissions.update', $p) }}" class="space-y-3">
            @csrf
            @method('PUT')
            <input type="hidden" name="modal" value="{{ $mid }}" />
            <div>
              <label class="label"><span class="label-text">Nama</span></label>
              <input type="text" name="nama" class="input input-bordered w-full"
                value="{{ old('modal') === $mid ? old('nama', $p->nama) : $p->nama }}" required />
              @if(old('modal') === $mid)
                @error('nama')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
              @endif
            </div>
            <div class="grid md:grid-cols-2 gap-3">
              <div>
                <label class="label"><span class="label-text">Slug</span></label>
                <input type="text" name="slug" class="input input-bordered w-full"
                  value="{{ old('modal') === $mid ? old('slug', $p->slug) : $p->slug }}" required />
                @if(old('modal') === $mid)
                  @error('slug')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                @endif
              </div>
              <div>
                <label class="label"><span class="label-text">Modul</span></label>
                <input type="text" name="modul" class="input input-bordered w-full"
                  value="{{ old('modal') === $mid ? old('modul', $p->modul) : $p->modul }}" />
              </div>
            </div>
            <div>
              <label class="label"><span class="label-text">Deskripsi</span></label>
              <textarea name="deskripsi" rows="3"
                class="textarea textarea-bordered w-full">{{ old('modal') === $mid ? old('deskripsi', $p->deskripsi) : $p->deskripsi }}</textarea>
            </div>
            <div class="flex items-center gap-2">
              <input type="checkbox" name="aktif" value="1" class="checkbox" {{ (old('modal') === $mid ? (old('aktif') ? true : false) : ($p->aktif === '1')) ? 'checked' : '' }}>
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
      @empty
      <tr>
        <td colspan="5" class="text-center text-gray-500">Belum ada data.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<dialog id="modal-create-permission" class="modal">
  <div class="modal-box max-w-2xl">
    <h3 class="font-semibold text-lg mb-3">Tambah Permission</h3>
    <form method="POST" action="{{ route('admin.settings.permissions.store') }}" class="space-y-3">
      @csrf
      <input type="hidden" name="modal" value="modal-create-permission" />
      <div>
        <label class="label"><span class="label-text">Nama</span></label>
        <input type="text" name="nama" class="input input-bordered w-full" value="{{ old('nama') }}" required />
        @if(old('modal') === 'modal-create-permission')
          @error('nama')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
        @endif
      </div>
      <div class="grid md:grid-cols-2 gap-3">
        <div>
          <label class="label"><span class="label-text">Slug</span></label>
          <input type="text" name="slug" class="input input-bordered w-full" value="{{ old('slug') }}" required />
          @if(old('modal') === 'modal-create-permission')
            @error('slug')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
          @endif
        </div>
        <div>
          <label class="label"><span class="label-text">Modul</span></label>
          <input type="text" name="modul" class="input input-bordered w-full" value="{{ old('modul') }}" />
        </div>
      </div>
      <div>
        <label class="label"><span class="label-text">Deskripsi</span></label>
        <textarea name="deskripsi" rows="3" class="textarea textarea-bordered w-full">{{ old('deskripsi') }}</textarea>
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
    window.addEventListener('DOMContentLoaded', function () {
      const id = @json(old('modal'));
      const dlg = document.getElementById(id);
      if (dlg && typeof dlg.showModal === 'function') { dlg.showModal(); }
    });
  </script>
@endif
@endsection