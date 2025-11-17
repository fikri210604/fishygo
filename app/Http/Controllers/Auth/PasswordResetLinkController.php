<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status == Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withInput($request->only('email'))
                    ->withErrors(['email' => __($status)]);
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'PasswordResetLinkController@store']);
            }
            return back()->withInput()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal mengirim tautan reset password.') : 'Terjadi kesalahan.');
        }
    }
}
