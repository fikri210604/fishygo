<x-admin-layout>
    <div class="p-6 space-y-6">
        <!-- =================== HEADER =================== -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">👥 User Management</h3>
                <p class="text-sm text-gray-500">Kelola akun pengguna sistem dengan mudah.</p>
            </div>
            <button class="btn btn-primary btn-md shadow-md hover:shadow-lg transition-all duration-200 bg-cyan-300 hover:bg-cyan-400 p-6"
                onclick="user_create_modal.showModal()">
                <i class="fa-solid fa-plus mr-2"></i> Tambah User
            </button>
        </div>

        <!-- =================== TABLE =================== -->
        <div class="bg-base-100 rounded-xl shadow-lg border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead class="bg-base-200 text-base-content">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-medium">{{ $user->nama }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="flex justify-center gap-2">
                                    <button 
                                        class="btn btn-sm btn-outline btn-info"
                                        onclick="document.getElementById('edit_user_modal_{{ $user->id }}').showModal()"
                                    >
                                        <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                    </button>

                                    <button 
                                        class="btn btn-sm btn-outline btn-error"
                                        onclick="document.getElementById('delete_user_modal_{{ $user->id }}').showModal()"
                                    >
                                        <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                                    </button>
                                </td>
                            </tr>

                            <!-- =================== MODAL EDIT =================== -->
                            <dialog id="edit_user_modal_{{ $user->id }}" class="modal">
                                <div class="modal-box max-w-md">
                                    <h3 class="font-bold text-lg mb-3 text-primary">Edit Data User</h3>
                                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-4">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label class="label"><span class="label-text">Nama</span></label>
                                            <input type="text" name="nama" value="{{ $user->nama }}" class="input input-bordered w-full" required />
                                        </div>

                                        <div>
                                            <label class="label"><span class="label-text">Username</span></label>
                                            <input type="text" name="username" value="{{ $user->username }}" class="input input-bordered w-full" required />
                                        </div>

                                        <div>
                                            <label class="label"><span class="label-text">Email</span></label>
                                            <input type="email" name="email" value="{{ $user->email }}" class="input input-bordered w-full" required />
                                        </div>

                                        <div>
                                            <label class="label"><span class="label-text">Password</span></label>
                                            <input type="password" name="password" placeholder="Kosongkan jika tidak diganti" class="input input-bordered w-full" />
                                        </div>

                                        <div class="modal-action">
                                            <button class="btn btn-success">
                                                <i class="fa-solid fa-check mr-2"></i> Update
                                            </button>
                                            <form method="dialog">
                                                <button class="btn btn-ghost">Batal</button>
                                            </form>
                                        </div>
                                    </form>
                                </div>
                            </dialog>

                            <!-- =================== MODAL DELETE =================== -->
                            <dialog id="delete_user_modal_{{ $user->id }}" class="modal">
                                <div class="modal-box text-center">
                                    <h3 class="font-bold text-lg text-error mb-2">Hapus User</h3>
                                    <p class="py-2 text-gray-600">
                                        Apakah kamu yakin ingin menghapus user 
                                        <b>{{ $user->username }}</b>?
                                    </p>

                                    <div class="modal-action justify-center">
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-error">
                                                <i class="fa-solid fa-trash mr-2"></i> Hapus
                                            </button>
                                        </form>
                                        <form method="dialog">
                                            <button class="btn btn-ghost">Batal</button>
                                        </form>
                                    </div>
                                </div>
                            </dialog>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 py-6">Belum ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-base-300 bg-base-200">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- =================== MODAL CREATE =================== -->
    <dialog id="user_create_modal" class="modal">
        <div class="modal-box max-w-md">
            <h3 class="font-bold text-lg mb-3 text-primary">Tambah User Baru</h3>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="label"><span class="label-text">Nama</span></label>
                    <input type="text" name="nama" class="input input-bordered w-full" required />
                </div>

                <div>
                    <label class="label"><span class="label-text">Username</span></label>
                    <input type="text" name="username" class="input input-bordered w-full" required />
                </div>

                <div>
                    <label class="label"><span class="label-text">Email</span></label>
                    <input type="email" name="email" class="input input-bordered w-full" required />
                </div>

                <div>
                    <label class="label"><span class="label-text">Password</span></label>
                    <input type="password" name="password" class="input input-bordered w-full" required />
                </div>

                <div class="modal-action">
                    <button class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan
                    </button>
                    <form method="dialog">
                        <button class="btn btn-ghost">Batal</button>
                    </form>
                </div>
            </form>
        </div>
    </dialog>
</x-admin-layout>
