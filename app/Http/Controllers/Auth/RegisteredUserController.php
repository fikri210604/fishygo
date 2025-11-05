<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alamat;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'province_id' => ['required', 'string'],
            'province_name' => ['required', 'string'],
            'regency_id' => ['required', 'string'],
            'regency_name' => ['required', 'string'],
            'district_id' => ['required', 'string'],
            'district_name' => ['required', 'string'],
            'village_id' => ['required', 'string'],
            'village_name' => ['required', 'string'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'avatar.image' => 'Avatar harus berupa gambar.',
            'avatar.max' => 'Ukuran avatar maksimal 2MB.',
            'province_id.required' => 'Provinsi wajib dipilih.',
            'regency_id.required' => 'Kabupaten/Kota wajib dipilih.',
            'district_id.required' => 'Kecamatan wajib dipilih.',
            'village_id.required' => 'Kelurahan/Desa wajib dipilih.',
        ], [
            'province_id' => 'Provinsi',
            'regency_id' => 'Kabupaten/Kota',
            'district_id' => 'Kecamatan',
            'village_id' => 'Kelurahan/Desa',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_slug' => User::defaultRoleSlug(),
            'avatar' => $avatarPath,
        ]);

        // Simpan alamat utama ke tabel alamat
        Alamat::create([
            'pengguna_id' => $user->id,
            'label_alamat' => 'Alamat Utama',
            'penerima' => $request->nama,
            'no_telp_penerima' => $request->phone ?? '',
            'alamat_lengkap' => $request->address ?? '',
            'province_id' => $request->province_id,
            'province_name' => $request->province_name,
            'regency_id' => $request->regency_id,
            'regency_name' => $request->regency_name,
            'district_id' => $request->district_id,
            'district_name' => $request->district_name,
            'village_id' => $request->village_id,
            'village_name' => $request->village_name,
        ]);

        // Ensure default role exists in pivot
        if (method_exists($user, 'assignRole')) {
            $user->assignRole(User::defaultRoleSlug());
        }

        // dd($user);

        // Trigger email verification notification
        event(new Registered($user));

        // Jika request dari API, kembalikan JSON + token
        if ($request->expectsJson()) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
        }

        Auth::login($user);

        // Arahkan ke halaman verifikasi email
        return redirect()->route('verification.notice');
    }
}
