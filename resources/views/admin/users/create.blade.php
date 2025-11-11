@extends('layouts.admin')
@section('content')
    <h3 class="text-lg font-medium text-gray-900 mb-4">Create User</h3>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4 max-w-lg">
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
            <x-primary-button>Create</x-primary-button>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
@endsection
