@extends('layouts.admin')
@section('content')
    <h3 class="text-lg font-medium text-gray-900 mb-4">Create Admin</h3>

    <form id="admin-create-form" method="POST" action="{{ route('admin.admins.store') }}" class="space-y-4 max-w-lg">
        @csrf
        <div>
            <x-input-label for="nama" :value="__('Nama')" />
            <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama')" required />
            <x-input-error class="mt-2" :messages="$errors->get('nama')" />
        </div>
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username')" required />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <div class="flex items-center gap-3">
            <button type="button" class="btn btn-primary" onclick="document.getElementById('confirm-create-admin').showModal()">Create</button>
            <a href="{{ route('admin.admins.index') }}" class="text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>

    <dialog id="confirm-create-admin" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Konfirmasi</h3>
            <p class="py-4">Simpan admin baru?</p>
            <div class="modal-action">
                <button class="btn" type="submit" form="admin-create-form">Ya, Simpan</button>
                <form method="dialog"><button class="btn btn-ghost">Batal</button></form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>
@endsection
