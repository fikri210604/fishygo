<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()->intended(RouteServiceProvider::home());
            }

            $request->user()->sendEmailVerificationNotification();

            return back()->with('status', 'verification-link-sent');
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'EmailVerificationNotificationController@store']); }
            return back()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal mengirim tautan verifikasi.') : 'Terjadi kesalahan.');
        }
    }
}
