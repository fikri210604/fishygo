@extends('layouts.admin')
@section('content')
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900">Admins</h3>
        <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Create Admin</a>
    </div>
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($admins as $admin)
                <tr>
                    <td class="px-4 py-2">{{ $admin->username }}</td>
                    <td class="px-4 py-2">{{ $admin->email }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="{{ route('admin.admins.edit', $admin) }}" class="text-indigo-600 hover:underline">Edit</a>
                        <button type="button" class="text-red-600 hover:underline" onclick="document.getElementById('confirm-delete-admin-{{ $admin->id }}').showModal()">Delete</button>
                        <dialog id="confirm-delete-admin-{{ $admin->id }}" class="modal">
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
                    </td>
                </tr>
                @empty
                <tr><td class="px-4 py-4 text-gray-500" colspan="3">No admins found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $admins->links() }}</div>
@endsection
