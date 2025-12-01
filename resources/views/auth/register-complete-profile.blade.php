@section('title', 'Lengkapi Profil - FishyGo')


<x-guest-layout>
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- BAGIAN KIRI GAMBAR -->
        <div class="hidden lg:flex w-1/2 bg-cover bg-center" style="background-image: url('{{ asset('assets/images/background.png') }}');"></div>

        <!-- BAGIAN KANAN FORM -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 md:px-12 py-10">
            <div class="w-full max-w-md">
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-primary mb-2">Lengkapi Profil</h1>
                    <p class="text-gray-600">Untuk akun: <span class="font-semibold">{{ $email }}</span></p>
                </div>

                <form method="POST" action="{{ route('register.complete.profile.store') }}" enctype="multipart/form-data" class="card bg-white shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    @csrf

                {{-- Info Akun --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label value="Nama Lengkap" />
                        <input type="text" name="nama" value="{{ old('nama', $data['nama'] ?? '') }}" class="input input-bordered w-full" required />
                        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Username" />
                        <input type="text" name="username" value="{{ old('username', $data['username'] ?? '') }}" class="input input-bordered w-full" required />
                        <x-input-error :messages="$errors->get('username')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="Nomor Telepon" />
                        <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $data['nomor_telepon'] ?? '') }}" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('nomor_telepon')" class="mt-1" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label value="Avatar (opsional)" />
                        <input type="file" name="avatar" accept="image/*" class="file-input file-input-bordered w-full" />
                        <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
                    </div>
                </div>

                <div class="divider my-6">Alamat</div>

                {{-- Alamat --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <x-input-label value="Alamat Lengkap" />
                        <textarea name="alamat_lengkap" class="textarea textarea-bordered w-full" rows="3" required>{{ old('alamat_lengkap', $data['alamat_lengkap'] ?? '') }}</textarea>
                        <x-input-error :messages="$errors->get('alamat_lengkap')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Provinsi" />
                        <select id="province" name="province_id" class="select select-bordered w-full" required data-selected="{{ old('province_id', $data['province_id'] ?? '') }}">
                            <option value="">Pilih Provinsi</option>
                        </select>
                        <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name', $data['province_name'] ?? '') }}" />
                        <x-input-error :messages="$errors->get('province_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Kabupaten / Kota" />
                        <select id="regency" name="regency_id" class="select select-bordered w-full" required data-selected="{{ old('regency_id', $data['regency_id'] ?? '') }}" disabled>
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                        <input type="hidden" name="regency_name" id="regency_name" value="{{ old('regency_name', $data['regency_name'] ?? '') }}" />
                        <x-input-error :messages="$errors->get('regency_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Kecamatan" />
                        <select id="district" name="district_id" class="select select-bordered w-full" required data-selected="{{ old('district_id', $data['district_id'] ?? '') }}" disabled>
                            <option value="">Pilih Kecamatan</option>
                        </select>
                        <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name', $data['district_name'] ?? '') }}" />
                        <x-input-error :messages="$errors->get('district_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Kelurahan / Desa" />
                        <select id="subdistrict" name="subdistrict_id" class="select select-bordered w-full" required data-selected="{{ old('subdistrict_id', $data['subdistrict_id'] ?? '') }}" disabled>
                            <option value="">Pilih Kelurahan/Desa</option>
                        </select>
                        <input type="hidden" name="subdistrict_name" id="subdistrict_name" value="{{ old('subdistrict_name', $data['subdistrict_name'] ?? '') }}" />
                        <x-input-error :messages="$errors->get('subdistrict_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="RT" />
                        <input type="text" name="rt" value="{{ old('rt', $data['rt'] ?? '') }}" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('rt')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label value="RW" />
                        <input type="text" name="rw" value="{{ old('rw', $data['rw'] ?? '') }}" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('rw')" class="mt-1" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label value="Kode Pos" />
                        <input type="text" name="kode_pos" value="{{ old('kode_pos', $data['kode_pos'] ?? '') }}" class="input input-bordered w-full" />
                        <x-input-error :messages="$errors->get('kode_pos')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn btn-primary w-full">Lanjutkan</button>
                </div>
                </form>

                <div class="text-center text-sm mt-4">
                    <a href="{{ route('register.notice') }}" class="text-gray-600 hover:underline">Kembali ke Verifikasi</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Wilayah dropdowns are handled by resources/js/wilayah.js via app.js --}}
</x-guest-layout>

