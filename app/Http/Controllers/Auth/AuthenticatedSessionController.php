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
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if (method_exists($user, 'hasVerifiedEmail') && ! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        $route = $user->Route();

        $needsProfile = method_exists($user, 'isProfileComplete') ? ! $user->isProfileComplete() : false;
        $profileMsg = 'Lengkapi profil sebelum transaksi: nomor HP dan alamat lengkap (provinsi, kab/kota, kecamatan, kelurahan/desa, alamat lengkap) di menu Profil.';

        if (Route::has($route)) {
            $resp = redirect()->route($route)->with('success', 'Berhasil masuk.');
            if ($needsProfile) { $resp->with('info', $profileMsg); }
            return $resp;
        }

        // Fallback to app home resolution
        $resp = redirect()->intended(RouteServiceProvider::home())->with('success', 'Berhasil masuk.');
        if ($needsProfile) { $resp->with('info', $profileMsg); }
        return $resp;
        
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
