<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'can:access-admin']);
    }

    public function index()
    {
        $admins = User::whereHas('roles', function ($q) {
            $q->where('slug', User::ROLE_ADMIN);
        })->orderBy('username')->paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:penggunas,username'],
            'email' => ['required','string','lowercase','email','max:255','unique:penggunas,email'],
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

        $admin = User::create([
            'nama' => $request->input('nama'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_slug' => User::ROLE_ADMIN,
        ]);
        $admin->assignRole(User::ROLE_ADMIN);

        return redirect()->route('admin.admins.index')->with('success', 'Admin berhasil dibuat.');
    }

    public function edit(User $admin)
    {
        abort_unless($admin->hasRole(User::ROLE_ADMIN), 404);
        return view('admin.admins.edit', ['admin' => $admin]);
    }

    public function update(Request $request, User $admin)
    {
        abort_unless($admin->hasRole(User::ROLE_ADMIN), 404);
        $validated = $request->validate([
            'nama' => ['required','string','max:255'],
            'username' => ['required','string','max:255', Rule::unique('penggunas','username')->ignore($admin->id, 'id')],
            'email' => ['required','string','lowercase','email','max:255', Rule::unique('penggunas','email')->ignore($admin->id, 'id')],
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
            'role_slug' => User::ROLE_ADMIN,
        ];
        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->input('password'));
        }
        $admin->update($payload);
        $admin->assignRole(User::ROLE_ADMIN);

        return redirect()->route('admin.admins.index')->with('success', 'Admin berhasil diperbarui.');
    }

    public function destroy(User $admin)
    {
        abort_unless($admin->hasRole(User::ROLE_ADMIN), 404);
        $admin->delete();
        return back()->with('success', 'Admin berhasil dihapus.');
    }
}
