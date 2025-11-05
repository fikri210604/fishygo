<x-admin-layout>
    <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Admin</h3>

    <form id="admin-edit-form" method="POST" action="{{ route('admin.admins.update', $admin) }}" class="space-y-4 max-w-lg">
        @csrf
        @method('PATCH')
        <div>
            <x-input-label for="nama" :value="__('Nama')" />
            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $admin->nama)" required />
            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
        </div>
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $admin->username)" required />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $admin->email)" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>
        <div>
            <x-input-label for="password" :value="__('Password (optional)')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <div class="flex items-center gap-3">
            <button type="button" class="btn btn-primary" onclick="document.getElementById('confirm-update-admin').showModal()">Update</button>
            <a href="{{ route('admin.admins.index') }}" class="text-gray-600 hover:underline">Cancel</a>
            <button type="button" class="text-red-600 hover:underline ml-auto" onclick="document.getElementById('confirm-delete-admin').showModal()">Delete</button>
        </div>
    </form>

    <!-- Confirm Update Modal -->
    <dialog id="confirm-update-admin" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Konfirmasi</h3>
            <p class="py-4">Simpan perubahan admin?</p>
            <div class="modal-action">
                <button class="btn" type="submit" form="admin-edit-form">Ya, Simpan</button>
                <form method="dialog"><button class="btn btn-ghost">Batal</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <!-- Confirm Delete Modal -->
    <dialog id="confirm-delete-admin" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Hapus Admin</h3>
            <p class="py-4">Yakin hapus admin ini? (soft delete)</p>
            <div class="modal-action">
                <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-error">Ya, Hapus</button>
                </form>
                <form method="dialog"><button class="btn btn-ghost">Batal</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
</x-admin-layout>
