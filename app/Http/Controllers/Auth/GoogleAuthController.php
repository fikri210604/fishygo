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
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google setelah login berhasil
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Login Google gagal: ' . $e->getMessage()]);
        }

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
                'role_slug' => 'user', // role default
                'email_verified_at' => now(),
            ]);
        } else {
            // Jika user sudah ada, update data penting
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar() ?? $user->avatar,
            ]);
        }

        // Login user
        Auth::login($user, remember: true);

        // Redirect ke dashboard (bisa kamu sesuaikan berdasarkan role)
        return redirect()->intended('/dashboard');
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
