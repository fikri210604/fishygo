<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user ke halaman login Google
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Callback dari Google setelah login berhasil
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (Exception $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'GoogleAuthController@callback']);
            }
            return redirect()->route('login')
                ->with('error', 'Login Google gagal: ' . ($e->getMessage() ?: 'Invalid state'))
                ->withErrors(['google' => 'Login Google gagal: ' . $e->getMessage()]);
        }

        try {
            // Temukan user berdasarkan email atau google_id
            $user = User::where('email', $googleUser->getEmail())
                ->orWhere('google_id', $googleUser->getId())
                ->first();

            // Jika belum ada user, buat baru
            if (!$user) {
                $user = User::create([
                    'username' => $this->generateUsername($googleUser),
                    'nama' => $googleUser->getName() ?? 'Pengguna Google',
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(16)),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role_slug' => User::ROLE_USER, // role default
                    'email_verified_at' => now(),
                ]);
                // Pastikan role di pivot terset agar muncul di admin
                $user->assignRole(User::ROLE_USER);
            } else {
                // Jika user sudah ada, update data penting
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar() ?? $user->avatar,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
                // Pastikan role terpasang
                $user->assignRole(User::ROLE_USER);
            }

            // Login user via web guard
            try { Auth::guard('web')->login($user, remember: true); } catch (\Throwable $e) { Auth::login($user, remember: true); }
            try {
                request()->session()->regenerate();
            } catch (\Throwable $e) {
            }

            // Tentukan route sesuai role, seperti alur login biasa
            $route = method_exists($user, 'Route') ? $user->Route() : null;
            $needsProfile = method_exists($user, 'isProfileComplete') ? !$user->isProfileComplete() : false;
            $profileMsg = 'Lengkapi profil sebelum transaksi: nomor HP dan alamat lengkap (provinsi, kab/kota, kecamatan, kelurahan/desa, alamat lengkap) di menu Profil.';
            try {
                session()->flash('success', 'Berhasil masuk dengan Google.');
                if ($needsProfile) {
                    session()->flash('info', $profileMsg);
                }
            } catch (\Throwable $e) {
            }

            if ($route && \Illuminate\Support\Facades\Route::has($route)) {
                $resp = redirect()->route($route)->with('success', 'Berhasil masuk dengan Google.');
                if ($needsProfile) {
                    $resp->with('info', $profileMsg);
                }
                return $resp;
            }

            // Fallback intended/home
            $resp = redirect()->intended(\App\Providers\RouteServiceProvider::home())
                ->with('success', 'Berhasil masuk dengan Google.');
            if ($needsProfile) {
                $resp->with('info', $profileMsg);
            }
            return $resp;
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'GoogleAuthController@callbackUser']);
            }
            return redirect()->route('login')
                ->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Login Google gagal.') : 'Terjadi kesalahan.')
                ->withErrors(['google' => 'Login Google gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate username unik dari email atau nama Google
     */
    private function generateUsername($googleUser): string
    {
        $base = Str::slug(Str::before($googleUser->getEmail(), '@') ?? $googleUser->getName());
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . '_' . $i++;
        }
        return $username;
    }
}
