<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Throwable;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::query()->orderBy('modul')->orderBy('nama')->get();
        return view('settings.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('settings.permissions.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|unique:permissions,nama',
                'slug' => 'required|alpha_dash|unique:permissions,slug',
                'modul' => 'nullable|string',
                'deskripsi' => 'nullable|string',
                'aktif' => 'nullable|boolean',
            ], [
                'nama.required' => 'Nama wajib diisi.',
                'nama.unique' => 'Nama sudah terdaftar.',
                'slug.required' => 'Slug wajib diisi.',
                'slug.unique' => 'Slug sudah terdaftar.',
            ]);

            Permission::create([
                'nama' => $request->input('nama'),
                'slug' => $request->input('slug'),
                'modul' => $request->input('modul') ?? null,
                'deskripsi' => $request->input('deskripsi') ?? null,
                'aktif' => ($request->aktif ?? true) ? '1' : '0',
            ]);

            return redirect()->route('admin.settings.permissions.index')->with('success', 'Permission berhasil dibuat.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Permission $permission)
    {
        return view('settings.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|unique:permissions,nama,' . $permission->id,
                'slug' => 'required|alpha_dash|unique:permissions,slug,' . $permission->id,
                'modul' => 'nullable|string',
                'deskripsi' => 'nullable|string',
                'aktif' => 'nullable|boolean',
            ], [
                'nama.required' => 'Nama wajib diisi.',
                'nama.unique' => 'Nama sudah terdaftar.',
                'slug.required' => 'Slug wajib diisi.',
                'slug.unique' => 'Slug sudah terdaftar.',
            ]);

            $permission->update([
                'nama' => $validated['nama'],
                'slug' => $validated['slug'],
                'modul' => $validated['modul'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'aktif' => ($validated['aktif'] ?? ($permission->aktif === '1')) ? '1' : '0',
            ]);

            return redirect()->route('admin.settings.permissions.index')->with('success', 'Permission berhasil diperbarui.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Permission $permission)
    {
        try {
            $permission->delete();
            return redirect()->route('admin.settings.permissions.index')->with('success', 'Permission berhasil dihapus.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
