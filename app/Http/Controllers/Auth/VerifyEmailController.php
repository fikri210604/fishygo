<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        try {
            // Jika sudah terverifikasi, arahkan ke halaman login dengan popup sukses
            if ($request->user()->hasVerifiedEmail()) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('success', 'Email berhasil diverifikasi. Silakan login.');
            }

            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }

            // Setelah verifikasi, paksa logout lalu arahkan ke halaman login dengan notifikasi
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Email berhasil diverifikasi. Silakan login.');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'VerifyEmailController']); }
            return redirect()->route('login')->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal memverifikasi email.') : 'Terjadi kesalahan.');
        }
    }
}
