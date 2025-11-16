<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Step View
     */
    public function create()
    {
        return view('auth.register');
    }

    public function start(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:pengguna,username',
            'email' => 'required|email:lowercase|max:255|unique:pengguna,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'nama' => $request->input('username'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_slug' => User::defaultRoleSlug(),
        ]);
        $user->assignRole(User::defaultRoleSlug());

        event(new Registered($user));
        Auth::login($user);
        return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
    }


    
    public function store(Request $request)
    {
        if (! $request->expectsJson())
            return abort(404);

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
        // Tambahkan role di pivot untuk konsistensi fitur admin
        $user->assignRole(User::defaultRoleSlug());

        event(new Registered($user));

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ], 201);
    }
}
