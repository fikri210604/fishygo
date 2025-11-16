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
        $data = $request->validated();
        // Ambil field wilayah untuk alamat (pisahkan dari kolom user)
        $alamatData = [
            'province_id' => $data['province_id'] ?? null,
            'province_name' => $data['province_name'] ?? null,
            'regency_id' => $data['regency_id'] ?? null,
            'regency_name' => $data['regency_name'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'district_name' => $data['district_name'] ?? null,
            'village_id' => $data['village_id'] ?? null,
            'village_name' => $data['village_name'] ?? null,
            'rt' => $data['rt'] ?? null,
            'rw' => $data['rw'] ?? null,
            'kode_pos' => $data['kode_pos'] ?? null,
        ];
        // Hapus field wilayah dari $data agar tidak masuk ke kolom user
        foreach (array_keys($alamatData) as $k) { unset($data[$k]); }

        $user = $request->user();

        // Build payload update ala: $model->update(['field' => $request->input('field')])
        $payload = [
            'nama' => $request->input('nama'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
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

        // Simpan/Update alamat utama jika field wilayah lengkap dan alamat_lengkap tersedia
        $hasWilayah = filled($alamatData['province_id']) && filled($alamatData['regency_id']) && filled($alamatData['district_id']) && filled($alamatData['village_id']);
        if ($hasWilayah && filled($request->input('address'))) {
            Alamat::updateOrCreate(
                ['pengguna_id' => $user->id],
                array_merge($alamatData, [
                    'pengguna_id' => $user->id,
                    'penerima' => $user->nama,
                    'alamat_lengkap' => $request->input('address'),
                ])
            );
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
