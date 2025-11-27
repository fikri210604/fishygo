<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="nama" :value="__('Nama')" />
                <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full text-gray-900"
                    :value="old('nama', $user->nama)" required autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('nama')" />
            </div>
            <div>
                <x-input-label for="username" :value="__('Username')" />
                <x-text-input id="username" name="username" type="text" class="mt-1 block w-full text-gray-900"
                    :value="old('username', $user->username)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('username')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full text-gray-900"
                :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="address" :value="__('Alamat Lengkap (Jalan, RT/RW, No Rumah)')" />
            <textarea id="address" name="address" rows="3"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-gray-900">{{ old('address', optional($primaryAddress ?? null)->alamat_lengkap) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="phone" :value="__('Nomor HP')" />
                <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full text-gray-900"
                    :value="old('phone', $user->nomor_telepon)" autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
            <div>
                <x-input-label for="kode_pos" :value="__('Kode Pos')" />
                <x-text-input id="kode_pos" name="kode_pos" type="text" class="mt-1 block w-full text-gray-900"
                    :value="old('kode_pos', optional($primaryAddress ?? null)->kode_pos)" />
                <x-input-error class="mt-2" :messages="$errors->get('kode_pos')" />
            </div>
        </div>

        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />
            @php
                $avatar = $user->avatar ?? null;
                if ($avatar) {
                    $avatarUrl = \Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://'])
                        ? $avatar
                        : asset('storage/' . $avatar);
                }
            @endphp
            @if (!empty($avatar ?? null))
                <div class="mb-2">
                    <img src="{{ $avatarUrl }}" alt="avatar" class="h-16 w-16 rounded-full object-cover" loading="lazy" decoding="async" width="64" height="64">
                </div>
            @endif
            <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <!-- Wilayah: Provinsi, Kabupaten/Kota, Kecamatan, Kelurahan/Desa -->
        <div class="grid grid-cols-1 gap-4 mt-4">
            <div>
                <x-input-label for="province_select" :value="__('Provinsi')" />
                <div class="mt-1 flex items-center gap-2">
                    <select id="province" name="province_id" class="w-full bg-gray-200 border-gray-300 rounded-md" data-selected="{{ old('province_id', optional($primaryAddress ?? null)->province_id) }}">
                        <option value="">-- Pilih Provinsi --</option>
                    </select>
                    <span id="province_spinner" class="hidden inline-flex">
                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                </div>
                <input type="hidden" id="province_id" name="province_id"
                    value="{{ old('province_id', optional($primaryAddress ?? null)->province_id) }}">
                <input type="hidden" id="province_name" name="province_name"
                    value="{{ old('province_name', optional($primaryAddress ?? null)->province_name) }}">
                <x-input-error class="mt-2" :messages="$errors->get('province_id')" />
            </div>

            <div>
                <x-input-label for="regency_select" :value="__('Kabupaten/Kota')" />
                <div class="mt-1 flex items-center gap-2">
                    <select id="city" name="city_id" class="w-full bg-gray-200 border-gray-300 rounded-md" disabled data-selected="{{ old('regency_id', optional($primaryAddress ?? null)->regency_id) }}">
                        <option value="">-- Pilih Kota/Kabupaten --</option>
                    </select>
                    <span id="regency_spinner" class="hidden inline-flex">
                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                </div>
                <input type="hidden" id="regency_id" name="regency_id"
                    value="{{ old('regency_id', optional($primaryAddress ?? null)->regency_id) }}">
                <input type="hidden" id="regency_name" name="regency_name"
                    value="{{ old('regency_name', optional($primaryAddress ?? null)->regency_name) }}">
                <x-input-error class="mt-2" :messages="$errors->get('regency_id')" />
            </div>

            <div>
                <x-input-label for="district_select" :value="__('Kecamatan')" />
                <div class="mt-1 flex items-center gap-2">
                    <select id="district" name="district_id" class="w-full bg-gray-200 border-gray-300 rounded-md" disabled data-selected="{{ old('district_id', optional($primaryAddress ?? null)->district_id) }}">
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                    <span id="district_spinner" class="hidden inline-flex">
                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                </div>
                <input type="hidden" id="district_id" name="district_id"
                    value="{{ old('district_id', optional($primaryAddress ?? null)->district_id) }}">
                <input type="hidden" id="district_name" name="district_name"
                    value="{{ old('district_name', optional($primaryAddress ?? null)->district_name) }}">
                <x-input-error class="mt-2" :messages="$errors->get('district_id')" />
            </div>

            <div>
                <x-input-label for="subdistrict" :value="__('Kelurahan / Desa')" />
                <div class="mt-1 flex items-center gap-2">
                    <select id="subdistrict" name="subdistrict_id" class="w-full bg-gray-200 border-gray-300 rounded-md" disabled data-selected="{{ old('subdistrict_id', optional($primaryAddress ?? null)->village_id) }}">
                        <option value="">-- Pilih Kelurahan / Desa --</option>
                    </select>

                    <span id="subdistrict_spinner" class="hidden inline-flex">
                        <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </span>
                </div>
                <input type="hidden" id="subdistrict_id" name="subdistrict_id"
                    value="{{ old('subdistrict_id', optional($primaryAddress ?? null)->village_id) }}">
                <input type="hidden" id="subdistrict_name" name="subdistrict_name"
                    value="{{ old('subdistrict_name', optional($primaryAddress ?? null)->village_name) }}">

                <x-input-error class="mt-2" :messages="$errors->get('subdistrict_id')" />
            </div>


            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rt" :value="__('RT')" />
                    <x-text-input id="rt" name="rt" type="text" class="mt-1 block w-full text-gray-900"
                        :value="old('rt', optional($primaryAddress ?? null)->rt)" />
                    <x-input-error class="mt-2" :messages="$errors->get('rt')" />
                </div>
                <div>
                    <x-input-label for="rw" :value="__('RW')" />
                    <x-text-input id="rw" name="rw" type="text" class="mt-1 block w-full text-gray-900"
                        :value="old('rw', optional($primaryAddress ?? null)->rw)" />
                    <x-input-error class="mt-2" :messages="$errors->get('rw')" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    <script>
        (function(){
            // Sinkronkan nilai select -> hidden agar terkirim meski select disabled
            const selProvince = document.getElementById('province');
            const hidProvinceId = document.getElementById('province_id');
            const hidProvinceName = document.getElementById('province_name');

            const selRegency = document.getElementById('regency') || document.getElementById('city');
            const hidRegencyId = document.getElementById('regency_id');
            const hidRegencyName = document.getElementById('regency_name');

            const selDistrict = document.getElementById('district');
            const hidDistrictId = document.getElementById('district_id');
            const hidDistrictName = document.getElementById('district_name');

            const selSubdistrict = document.getElementById('subdistrict');
            const hidSubId = document.getElementById('subdistrict_id');
            const hidSubName = document.getElementById('subdistrict_name');

            function sync(sel, hidId, hidName){
                if(!sel || !hidId || !hidName) return;
                const opt = sel.options[sel.selectedIndex];
                if(!opt) return;
                hidId.value = opt.value || '';
                hidName.value = (opt.dataset && opt.dataset.label) ? opt.dataset.label : (opt.textContent || '');
            }

            selProvince && selProvince.addEventListener('change', function(){ sync(selProvince, hidProvinceId, hidProvinceName); });
            selRegency && selRegency.addEventListener('change', function(){ sync(selRegency, hidRegencyId, hidRegencyName); });
            selDistrict && selDistrict.addEventListener('change', function(){ sync(selDistrict, hidDistrictId, hidDistrictName); });
            selSubdistrict && selSubdistrict.addEventListener('change', function(){ sync(selSubdistrict, hidSubId, hidSubName); });

            // Sinkron awal jika select sudah berisi
            sync(selProvince, hidProvinceId, hidProvinceName);
            sync(selRegency, hidRegencyId, hidRegencyName);
            sync(selDistrict, hidDistrictId, hidDistrictName);
            sync(selSubdistrict, hidSubId, hidSubName);
        })();
    </script>

    <script>
        (function(){
            const apiBase = '/api/wilayah';

            const selProvince = document.getElementById('province');
            const selCity = document.getElementById('city');
            const selDistrict = document.getElementById('district');
            const selSubdistrict = document.getElementById('subdistrict');

            const hidProvinceId = document.getElementById('province_id');
            const hidProvinceName = document.getElementById('province_name');
            const hidRegencyId = document.getElementById('regency_id');
            const hidRegencyName = document.getElementById('regency_name');
            const hidDistrictId = document.getElementById('district_id');
            const hidDistrictName = document.getElementById('district_name');
            const hidSubId = document.getElementById('subdistrict_id');
            const hidSubName = document.getElementById('subdistrict_name');

            const spProv = document.getElementById('province_spinner');
            const spReg = document.getElementById('regency_spinner');
            const spDis = document.getElementById('district_spinner');
            const spSub = document.getElementById('subdistrict_spinner');

            function setHidden(sel, hidId, hidName){
                if(!sel || !hidId || !hidName) return;
                const opt = sel.options[sel.selectedIndex];
                if(!opt) return;
                hidId.value = opt.value || '';
                hidName.value = opt.dataset.label || opt.textContent || '';
            }

            function makeOption(val, label){
                const o = document.createElement('option');
                o.value = val ?? '';
                o.textContent = label ?? '';
                o.dataset.label = label ?? '';
                return o;
            }

            async function get(url){
                const r = await fetch(url);
                if(!r.ok) throw new Error('Failed '+url);
                const j = await r.json();
                return j.data || [];
            }

            async function loadProvinces(){
                try{
                    spProv && spProv.classList.remove('hidden');
                    const data = await get(`${apiBase}/provinces`);
                    selProvince.innerHTML = '';
                    selProvince.appendChild(makeOption('', '-- Pilih Provinsi --'));
                    data.forEach(p=> selProvince.appendChild(makeOption(p.id || p.province_id || p.value, p.name || p.province)) );
                    selProvince.disabled = false;
                    const pre = selProvince.dataset.selected || hidProvinceId?.value;
                    if(pre){ selProvince.value = pre; setHidden(selProvince, hidProvinceId, hidProvinceName); await onProvinceChange(false); }
                }finally{ spProv && spProv.classList.add('hidden'); }
            }

            async function onProvinceChange(clearDown=true){
                const pid = selProvince.value;
                setHidden(selProvince, hidProvinceId, hidProvinceName);
                selCity.innerHTML=''; selCity.appendChild(makeOption('', '-- Pilih Kota/Kabupaten --'));
                selDistrict.innerHTML=''; selDistrict.appendChild(makeOption('', '-- Pilih Kecamatan --'));
                selSubdistrict.innerHTML=''; selSubdistrict.appendChild(makeOption('', '-- Pilih Kelurahan / Desa --'));
                selCity.disabled = selDistrict.disabled = selSubdistrict.disabled = true;
                if(!pid) return;
                try{
                    spReg && spReg.classList.remove('hidden');
                    const data = await get(`${apiBase}/cities/${encodeURIComponent(pid)}`);
                    data.forEach(c=> selCity.appendChild(makeOption(c.id || c.city_id, (c.type? c.type+' ' : '') + (c.city_name || c.name || ''))));
                    selCity.disabled = false;
                    const pre = selCity.dataset.selected || hidRegencyId?.value;
                    if(pre){ selCity.value = pre; setHidden(selCity, hidRegencyId, hidRegencyName); await onCityChange(false); }
                }finally{ spReg && spReg.classList.add('hidden'); }
            }

            async function onCityChange(clearDown=true){
                const cid = selCity.value;
                setHidden(selCity, hidRegencyId, hidRegencyName);
                selDistrict.innerHTML=''; selDistrict.appendChild(makeOption('', '-- Pilih Kecamatan --'));
                selSubdistrict.innerHTML=''; selSubdistrict.appendChild(makeOption('', '-- Pilih Kelurahan / Desa --'));
                selDistrict.disabled = selSubdistrict.disabled = true;
                if(!cid) return;
                try{
                    spDis && spDis.classList.remove('hidden');
                    const data = await get(`${apiBase}/districts/${encodeURIComponent(cid)}`);
                    data.forEach(d=> selDistrict.appendChild(makeOption(d.id || d.district_id, d.district_name || d.name)));
                    selDistrict.disabled = false;
                    const pre = selDistrict.dataset.selected || hidDistrictId?.value;
                    if(pre){ selDistrict.value = pre; setHidden(selDistrict, hidDistrictId, hidDistrictName); await onDistrictChange(false); }
                }finally{ spDis && spDis.classList.add('hidden'); }
            }

            async function onDistrictChange(clearDown=true){
                const did = selDistrict.value;
                setHidden(selDistrict, hidDistrictId, hidDistrictName);
                selSubdistrict.innerHTML=''; selSubdistrict.appendChild(makeOption('', '-- Pilih Kelurahan / Desa --'));
                selSubdistrict.disabled = true;
                if(!did) return;
                try{
                    spSub && spSub.classList.remove('hidden');
                    const data = await get(`${apiBase}/sub-district/${encodeURIComponent(did)}`);
                    data.forEach(s=> selSubdistrict.appendChild(makeOption(s.id || s.subdistrict_id, s.subdistrict_name || s.name)));
                    selSubdistrict.disabled = false;
                    const pre = selSubdistrict.dataset.selected || hidSubId?.value;
                    if(pre){ selSubdistrict.value = pre; setHidden(selSubdistrict, hidSubId, hidSubName); }
                }finally{ spSub && spSub.classList.add('hidden'); }
            }

            selProvince && selProvince.addEventListener('change', ()=> onProvinceChange());
            selCity && selCity.addEventListener('change', ()=> onCityChange());
            selDistrict && selDistrict.addEventListener('change', ()=> onDistrictChange());
            selSubdistrict && selSubdistrict.addEventListener('change', ()=> setHidden(selSubdistrict, hidSubId, hidSubName));

            document.addEventListener('DOMContentLoaded', loadProvinces);
        })();
    </script>
</section>
