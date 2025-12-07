@php
    use Illuminate\Support\Str;
    $avatar = $user->avatar ?? null;
    if ($avatar) {
        $avatarUrl = Str::startsWith($avatar, ['http://','https://']) ? $avatar : asset('storage/'.$avatar);
    } else {
        $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($user->nama);
    }
@endphp

<h2 class="text-xl font-semibold mb-6">Informasi Akun</h2>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
    </form>

<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('patch')

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-4">
            <div>
                <x-input-label for="nama" :value="__('Nama')" />
                <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $user->nama)" required />
                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="username" :value="__('Username')" />
                <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required />
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <div class="text-sm mt-2 text-gray-600">
                        Email kamu belum terverifikasi.
                        <button form="send-verification" class="underline text-indigo-600">Kirim ulang tautan verifikasi</button>
                    </div>
                @endif
            </div>

            <div>
                <x-input-label for="phone" :value="__('Nomor HP')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->nomor_telepon)" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <div class="flex flex-col items-center gap-4">
            <img src="{{ $avatarUrl }}" class="h-28 w-28 rounded-full object-cover shadow">
            <label class="cursor-pointer bg-gray-100 border px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                Ganti Foto
                <input type="file" name="avatar" accept="image/*" class="hidden">
            </label>
            <p class="text-xs text-gray-500">Maks 2MB (JPG/PNG)</p>
        </div>
    </div>

    <div class="pt-4">
        <x-primary-button>Simpan Perubahan</x-primary-button>
        @if (session('status') === 'profile-updated')
            <span class="text-sm text-gray-600 ml-3">Perubahan tersimpan.</span>
        @endif
    </div>
</form>

