<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\RegistrationVerificationMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use App\Models\Alamat;

class RegisteredUserController extends Controller
{
    // Step 1: Halaman register (email only)
    public function create(Request $request)
    {
        return view('auth.register');
    }

    // Step 1: Terima email, kirim tautan verifikasi
    public function start(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email:lowercase|max:255|unique:pengguna,email',
            ]);
            $email = $request->input('email');
            $url = URL::temporarySignedRoute(
                'register.verify',
                now()->addMinutes(60),
                ['email' => $email]
            );
            Mail::to($email)->send(new RegistrationVerificationMail($url));
            $request->session()->put('pending_registration_email', $email);
            return redirect()->route('register.notice')->with('status', 'verification-link-sent');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'RegisteredUserController@start']);
            }
            return back()->withInput()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal mengirim link verifikasi. Coba lagi.') : 'Terjadi kesalahan.');
        }
    }

    // Halaman pemberitahuan cek email
    public function notice(Request $request)
    {
        $email = $request->session()->get('pending_registration_email');
        if (!$email) {
            return redirect()->route('register');
        }
        return view('auth.register-verify-notice', ['email' => $email]);
    }

    // Resend tautan verifikasi
    public function resend(Request $request)
    {
        try {
            $email = $request->session()->get('pending_registration_email');
            if (!$email) {
                return redirect()->route('register')->with('error', 'Silakan isi email terlebih dahulu.');
            }
            if (User::query()->where('email', $email)->exists()) {
                return redirect()->route('login')->with('status', 'Email sudah terdaftar, silakan login.');
            }

            $url = URL::temporarySignedRoute(
                'register.verify',
                now()->addMinutes(60),
                ['email' => $email]
            );

            Mail::to($email)->send(new RegistrationVerificationMail($url));

            return back()->with('status', 'verification-link-sent');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'RegisteredUserController@resend']);
            }
            return back()->with('error', 'Gagal mengirim ulang tautan verifikasi.');
        }
    }

    // Step 2: Klik link verifikasi di email -> set email di sesi
    public function verify(Request $request)
    {
        try {
            $email = (string) $request->query('email');
            if (empty($email)) {
                return redirect()->route('register')->with('error', 'Tautan tidak valid.');
            }

            if (User::query()->where('email', $email)->exists()) {
                return redirect()->route('login')->with('status', 'Email sudah terdaftar, silakan login.');
            }

            $request->session()->put('verified_registration_email', $email);
            $request->session()->forget('pending_registration_email');

            return redirect()->route('register.complete.profile')->with('status', 'email-verified-for-registration');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'RegisteredUserController@verify']);
            }
            return redirect()->route('register')->with('error', 'Tautan verifikasi tidak valid atau sudah kedaluwarsa.');
        }
    }

    // Step 3 (GET): Tampilkan form profil (nama, username, nomor_telepon, avatar)
    public function completeProfile(Request $request)
    {
        try {
            $email = $request->session()->get('verified_registration_email');
            if (!$email) {
                return redirect()->route('register')->with('error', 'Silakan verifikasi email terlebih dahulu.');
            }
            $data = $request->session()->get('registration_profile_data', []);
            return view('auth.register-complete-profile', [
                'email' => $email,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'RegisteredUserController@completeProfile']);
            }
            return redirect()->route('register')->with('error', 'Terjadi kesalahan.');
        }
    }

    // Step 3 (POST): Simpan profil ke sesi dan lanjut ke password
    public function completeProfileStore(Request $request)
    {
        try {
            $email = $request->session()->get('verified_registration_email');
            if (!$email) {
                return redirect()->route('register')->with('error', 'Silakan verifikasi email terlebih dahulu.');
            }

            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:pengguna,username',
                'nomor_telepon' => 'nullable|string|max:32',
                'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'alamat_lengkap' => 'required|string|min:10',
                'province_id' => 'required|string',
                'province_name' => 'nullable|string',
                'regency_id' => 'required|string',
                'regency_name' => 'nullable|string',
                'district_id' => 'required|string',
                'district_name' => 'nullable|string',
                'subdistrict_id' => 'required|string',
                'subdistrict_name' => 'nullable|string',
                'rt' => 'nullable|string|max:10',
                'rw' => 'nullable|string|max:10',
                'kode_pos' => 'nullable|string|max:10',
            ]);

            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $validated['avatar'] = $path;
            }

            $request->session()->put('registration_profile_data', $validated);
            return redirect()->route('register.complete.password');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'RegisteredUserController@completeProfileStore']);
            }
            return redirect()->route('register')->with('error', 'Terjadi kesalahan.');
        }
    }

    // Step 4 (GET): Tampilkan form password
    public function completePassword(Request $request)
    {
        try {
            $email = $request->session()->get('verified_registration_email');
            $data = $request->session()->get('registration_profile_data');
            if (!$email || !$data) {
                return redirect()->route('register');
            }
            return view('auth.register-complete-password', [
                'email' => $email,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'RegisteredUserController@completePassword']);
            }
            return redirect()->route('register')->with('error', 'Terjadi kesalahan.');
        }
    }

    // Step 4 (POST): Submit akhir buat akun
    public function complete(Request $request)
    {
        try {
            $email = $request->session()->get('verified_registration_email');
            $profile = $request->session()->get('registration_profile_data');
            if (!$email || !$profile) {
                return redirect()->route('register');
            }

            $request->validate([
                'password' => 'required|confirmed|min:6',
            ]);

            $user = User::create([
                'nama' => $profile['nama'] ?? '',
                'username' => $profile['username'] ?? '',
                'email' => $email,
                'password' => Hash::make($request->input('password')),
                'email_verified_at' => now(),
                'role_slug' => User::defaultRoleSlug(),
                'nomor_telepon' => $profile['nomor_telepon'] ?? null,
                'avatar' => $profile['avatar'] ?? null,
            ]);
            $user->assignRole(User::defaultRoleSlug());

            Auth::login($user);

            // Simpan alamat utama (jika data alamat tersedia)
            try {
                $hasAddress = !empty($profile['alamat_lengkap'])
                    || !empty($profile['province_id']) || !empty($profile['regency_id'])
                    || !empty($profile['district_id']) || !empty($profile['subdistrict_id']) || !empty($profile['village_id']);

                if ($hasAddress) {
                    // Beberapa form menggunakan nama field "subdistrict_*". Map ke kolom "village_*" pada tabel.
                    $villageId = $profile['village_id'] ?? $profile['subdistrict_id'] ?? null;
                    $villageName = $profile['village_name'] ?? $profile['subdistrict_name'] ?? null;

                    Alamat::create([
                        'pengguna_id'    => $user->id,
                        'penerima'       => $profile['nama'] ?? $user->nama,
                        'alamat_lengkap' => $profile['alamat_lengkap'] ?? '',
                        'province_id'    => $profile['province_id'] ?? null,
                        'province_name'  => $profile['province_name'] ?? null,
                        'regency_id'     => $profile['regency_id'] ?? null,
                        'regency_name'   => $profile['regency_name'] ?? null,
                        'district_id'    => $profile['district_id'] ?? null,
                        'district_name'  => $profile['district_name'] ?? null,
                        'village_id'     => $villageId,
                        'village_name'   => $villageName,
                        'rt'             => $profile['rt'] ?? null,
                        'rw'             => $profile['rw'] ?? null,
                        'kode_pos'       => $profile['kode_pos'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                // Jangan gagalkan registrasi hanya karena alamat gagal disimpan.
                if (method_exists($this, 'logException')) {
                    $this->logException($e, ['action' => 'RegisteredUserController@complete.saveAddress']);
                }
            }

            // Bersihkan sesi
            $request->session()->forget('verified_registration_email');
            $request->session()->forget('registration_profile_data');

            $displayName = $user->nama ?? $user->username ?? 'Pengguna';
            return redirect()->route('home')
                ->with('success', 'Pendaftaran berhasil. Selamat datang, ' . $displayName . '!');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'RegisteredUserController@complete']);
            }
            return back()->withInput()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal melengkapi pendaftaran.') : 'Terjadi kesalahan.');
        }
    }

    // (Opsional) API registrasi full tanpa step
    public function store(Request $request)
    {
        if (!$request->expectsJson())
            return abort(404);
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:pengguna,username',
                'email' => 'required|email:lowercase|max:255|unique:pengguna,email',
                'password' => 'required|confirmed|min:6',
            ]);

            $user = User::create([
                'nama' => $request->input('nama'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role_slug' => User::defaultRoleSlug(),
            ]);
            $user->assignRole(User::defaultRoleSlug());

            event(new Registered($user));

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil',
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken,
            ], 201);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'RegisteredUserController@store']);
            }
            $message = method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Registrasi gagal.') : 'Terjadi kesalahan.';
            return response()->json(['message' => $message], 500);
        }
    }
}
