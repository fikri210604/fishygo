<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'can:access-admin']);
    }

    public function index()
    {
        $users = User::query()
            ->where(function ($q) {
                $q->whereHas('roles', function ($rq) {
                    $rq->where('slug', User::ROLE_USER);
                })->orWhere('role_slug', User::ROLE_USER);
            })
            ->orderBy('username')
            ->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:pengguna,username'],
            'email' => ['required','string','lowercase','email','max:255','unique:pengguna,email'],
            'password' => ['required','string','min:6'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal :min karakter.',
        ], [
            'nama' => 'Nama',
            'username' => 'Username',
            'email' => 'Email',
        ]);

        $user = User::create([
            'nama' => $request->input('nama'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_slug' => User::ROLE_USER,
        ]);
        $user->assignRole(User::ROLE_USER);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        abort_unless($user->hasRole(User::ROLE_USER), 404);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->hasRole(User::ROLE_USER), 404);

        $validated = $request->validate([
            'nama' => ['required','string','max:255'],
            'username' => ['required','string','max:255', Rule::unique('pengguna','username')->ignore($user->id, 'id')],
            'email' => ['required','string','lowercase','email','max:255', Rule::unique('pengguna','email')->ignore($user->id, 'id')],
            'password' => ['nullable','string','min:6'],
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'password.min' => 'Password minimal :min karakter.',
        ], [
            'nama' => 'Nama',
            'username' => 'Username',
            'email' => 'Email',
        ]);

        $payload = [
            'nama' => $request->input('nama'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'role_slug' => User::ROLE_USER,
        ];
        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->input('password'));
        }

        $user->update($payload);
        $user->assignRole(User::ROLE_USER);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->hasRole(User::ROLE_USER), 404);
        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
