<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Alamat;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $primaryAddress = $user->alamats()->orderBy('created_at')->first();
        return view('profile.edit', [
            'user' => $user,
            'primaryAddress' => $primaryAddress,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            // 1) Update profil pengguna
            $payload = [
                'nama' => (string) $request->input('nama'),
                'username' => (string) $request->input('username'),
                'email' => (string) $request->input('email'),
                'nomor_telepon' => $request->input('phone'),
            ];

            // Tangani avatar: hapus lama dan simpan baru (jika ada)
            if ($request->hasFile('avatar')) {
                if (!empty($user->avatar)) {
                    try { Storage::disk('public')->delete($user->avatar); } catch (\Throwable $e) {}
                }
                $path = $request->file('avatar')->store('avatars', 'public');
                $payload['avatar'] = $path;
            }

            // Jika email berubah, reset verifikasi email
            if (!empty($payload['email']) && $payload['email'] !== $user->email) {
                $payload['email_verified_at'] = null;
            }

            // Update user dengan pola $model->update([...])
            $user->update($payload);

            // 2) Update alamat utama sesuai model + migrasi (menyimpan snapshot wilayah)
            $existingAlamat = Alamat::where('pengguna_id', $user->id)->first();

            // Aliases & fallback ke nilai existing agar update tetap tersimpan
            $provinceId   = $request->input('province_id') ?: ($existingAlamat->province_id ?? null);
            $provinceName = $request->input('province_name') ?: ($existingAlamat->province_name ?? null);
            $regencyId    = $request->input('regency_id') ?: $request->input('city_id') ?: ($existingAlamat->regency_id ?? null);
            $regencyName  = $request->input('regency_name') ?: $request->input('city_name') ?: ($existingAlamat->regency_name ?? null);
            $districtId   = $request->input('district_id') ?: ($existingAlamat->district_id ?? null);
            $districtName = $request->input('district_name') ?: ($existingAlamat->district_name ?? null);
            $villageId    = $request->input('village_id') ?: $request->input('subdistrict_id') ?: ($existingAlamat->village_id ?? null);
            $villageName  = $request->input('village_name') ?: $request->input('subdistrict_name') ?: ($existingAlamat->village_name ?? null);
            $rt           = $request->input('rt') ?: ($existingAlamat->rt ?? null);
            $rw           = $request->input('rw') ?: ($existingAlamat->rw ?? null);
            $kodePos      = $request->input('kode_pos') ?: ($existingAlamat->kode_pos ?? null);

            $alamatLengkap = trim((string) $request->input('address'));
            if ($alamatLengkap === '' && $existingAlamat) {
                $alamatLengkap = (string) $existingAlamat->alamat_lengkap;
            }

            // Simpan alamat jika ada salah satu data wilayah atau alamat lengkap tersedia
            $hasAnyWilayah = filled($provinceId) || filled($regencyId) || filled($districtId) || filled($villageId)
                || filled($provinceName) || filled($regencyName) || filled($districtName) || filled($villageName);

            if ($hasAnyWilayah || $alamatLengkap !== '') {
                Alamat::updateOrCreate(
                    ['pengguna_id' => $user->id],
                    [
                        'pengguna_id' => $user->id,
                        'penerima' => $payload['nama'] ?: $user->nama,
                        'alamat_lengkap' => $alamatLengkap,
                        'province_id' => $provinceId,
                        'province_name' => $provinceName,
                        'regency_id' => $regencyId,
                        'regency_name' => $regencyName,
                        'district_id' => $districtId,
                        'district_name' => $districtName,
                        'village_id' => $villageId,
                        'village_name' => $villageName,
                        'rt' => $rt,
                        'rw' => $rw,
                        'kode_pos' => $kodePos,
                    ]
                );
            }

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'ProfileController@update']); }
            return Redirect::back()->withInput()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal memperbarui profil.') : 'Terjadi kesalahan.');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            $request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);

            $user = $request->user();

            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'ProfileController@destroy']); }
            return Redirect::back()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal menghapus akun.') : 'Terjadi kesalahan.');
        }
    }
}
