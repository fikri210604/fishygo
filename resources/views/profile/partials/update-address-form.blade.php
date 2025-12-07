@php($addr = $primaryAddress ?? null)

<h2 class="text-xl font-semibold mb-6">Alamat Pengiriman</h2>

<form method="post" action="{{ route('profile.update') }}" class="space-y-6">
    @csrf
    @method('patch')

    <div>
        <x-input-label for="address" :value="__('Alamat Lengkap (Jalan, RT/RW, No Rumah)')" />
        <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('address', optional($addr)->alamat_lengkap) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="kode_pos" :value="__('Kode Pos')" />
            <x-text-input id="kode_pos" name="kode_pos" type="text" class="mt-1 block w-full" :value="old('kode_pos', optional($addr)->kode_pos)" />
            <x-input-error class="mt-2" :messages="$errors->get('kode_pos')" />
        </div>
        <div>
            <x-input-label :value="__('RT / RW')" />
            <div class="flex gap-2">
                <x-text-input name="rt" type="text" class="mt-1 block w-full" :value="old('rt', optional($addr)->rt)" />
                <x-text-input name="rw" type="text" class="mt-1 block w-full" :value="old('rw', optional($addr)->rw)" />
            </div>
        </div>
    </div>

    <!-- Wilayah: Provinsi, Kab/Kota, Kecamatan, Kelurahan/Desa -->
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="province" :value="__('Provinsi')" />
            <div class="mt-1 flex items-center gap-2">
                <select id="province" name="province_id" class="w-full bg-gray-200 border-gray-300 rounded-md" data-selected="{{ old('province_id', optional($addr)->province_id) }}">
                    <option value="">-- Pilih Provinsi --</option>
                </select>
                <span id="province_spinner" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                </span>
            </div>
            <input type="hidden" id="province_id" name="province_id" value="{{ old('province_id', optional($addr)->province_id) }}">
            <input type="hidden" id="province_name" name="province_name" value="{{ old('province_name', optional($addr)->province_name) }}">
            <x-input-error class="mt-2" :messages="$errors->get('province_id')" />
        </div>

        <div>
            <x-input-label for="regency" :value="__('Kabupaten/Kota')" />
            <div class="mt-1 flex items-center gap-2">
                <select id="regency" name="regency_id" class="w-full bg-gray-200 border-gray-300 rounded-md" disabled data-selected="{{ old('regency_id', optional($addr)->regency_id) }}">
                    <option value="">-- Pilih Kota/Kabupaten --</option>
                </select>
                <span id="regency_spinner" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                </span>
            </div>
            <input type="hidden" id="regency_id" name="regency_id" value="{{ old('regency_id', optional($addr)->regency_id) }}">
            <input type="hidden" id="regency_name" name="regency_name" value="{{ old('regency_name', optional($addr)->regency_name) }}">
            <x-input-error class="mt-2" :messages="$errors->get('regency_id')" />
        </div>

        <div>
            <x-input-label for="district" :value="__('Kecamatan')" />
            <div class="mt-1 flex items-center gap-2">
                <select id="district" name="district_id" class="w-full bg-gray-200 border-gray-300 rounded-md" disabled data-selected="{{ old('district_id', optional($addr)->district_id) }}">
                    <option value="">-- Pilih Kecamatan --</option>
                </select>
                <span id="district_spinner" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                </span>
            </div>
            <input type="hidden" id="district_id" name="district_id" value="{{ old('district_id', optional($addr)->district_id) }}">
            <input type="hidden" id="district_name" name="district_name" value="{{ old('district_name', optional($addr)->district_name) }}">
            <x-input-error class="mt-2" :messages="$errors->get('district_id')" />
        </div>

        <div>
            <x-input-label for="subdistrict" :value="__('Kelurahan / Desa')" />
            <div class="mt-1 flex items-center gap-2">
                <select id="subdistrict" name="subdistrict_id" class="w-full bg-gray-200 border-gray-300 rounded-md" disabled data-selected="{{ old('subdistrict_id', optional($addr)->village_id) }}">
                    <option value="">-- Pilih Kelurahan / Desa --</option>
                </select>
                <span id="subdistrict_spinner" class="hidden">
                    <svg class="animate-spin h-5 w-5 text-indigo-600" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                </span>
            </div>
            <input type="hidden" id="subdistrict_id" name="subdistrict_id" value="{{ old('subdistrict_id', optional($addr)->village_id) }}">
            <input type="hidden" id="subdistrict_name" name="subdistrict_name" value="{{ old('subdistrict_name', optional($addr)->village_name) }}">
            <x-input-error class="mt-2" :messages="$errors->get('subdistrict_id')" />
        </div>
    </div>

    <div class="pt-2">
        <x-primary-button>Simpan Alamat</x-primary-button>
        @if (session('status') === 'profile-updated')
            <span class="text-sm text-gray-600 ml-3">Perubahan tersimpan.</span>
        @endif
    </div>
</form>

