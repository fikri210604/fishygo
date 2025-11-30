<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            $user = Auth::user();

            if (method_exists($user, 'isUser') && $user->isUser() && method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            $route = $user->Route();

            $needsProfile = method_exists($user, 'isUser') && $user->isUser()
                && method_exists($user, 'isProfileComplete') && !$user->isProfileComplete();
            $profileMsg = 'Lengkapi profil sebelum transaksi: nomor HP dan alamat lengkap (provinsi, kab/kota, kecamatan, kelurahan/desa, alamat lengkap) di menu Profil.';

            if (Route::has($route)) {
                $resp = redirect()->route($route)->with('success', 'Berhasil masuk.');
                if ($needsProfile) {
                    $resp->with('info', $profileMsg);
                }
                return $resp;
            }
            $resp = redirect()->intended(RouteServiceProvider::home())->with('success', 'Berhasil masuk.');
            if ($needsProfile) {
                $resp->with('info', $profileMsg);
            }
            return $resp;
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'AuthenticatedSessionController@store']);
            }
            return back()->withInput()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal masuk. Periksa kredensial Anda.') : 'Terjadi kesalahan.');
        }

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect('/');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'AuthenticatedSessionController@destroy']);
            }
            return redirect('/')->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal keluar dari sesi.') : 'Terjadi kesalahan.');
        }
    }
}
