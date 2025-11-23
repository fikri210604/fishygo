<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::query()->orderBy('nama')->withCount('permissions')->get();
        $permissions = Permission::query()->orderBy('modul')->orderBy('nama')->get();
        return view('settings.roles.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        return view('settings.roles.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|unique:roles,nama',
                'slug' => 'required|alpha_dash|unique:roles,slug',
                'aktif' => 'nullable|boolean',
            ]);

            Role::create([
                'nama' => $validated['nama'],
                'slug' => $validated['slug'],
                'aktif' => ($validated['aktif'] ?? true) ? '1' : '0',
            ]);

            return redirect()->route('admin.settings.roles.index')->with('success', 'Role berhasil dibuat.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Role $role)
    {
        return view('settings.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|unique:roles,nama,' . $role->id,
                'slug' => 'required|alpha_dash|unique:roles,slug,' . $role->id,
                'aktif' => 'nullable|boolean',
            ]);

            $role->update([
                'nama' => $validated['nama'],
                'slug' => $validated['slug'],
                'aktif' => ($validated['aktif'] ?? ($role->aktif === '1')) ? '1' : '0',
            ]);

            return redirect()->route('admin.settings.roles.index')->with('success', 'Role berhasil diperbarui.');
        } catch (Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return redirect()->route('admin.settings.roles.index')->with('success', 'Role berhasil dihapus.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function editPermissions(Role $role)
    {
        $permissions = Permission::query()->orderBy('modul')->orderBy('nama')->get();
        $owned = $role->permissions()->pluck('permissions.id')->all();
        return view('settings.roles.permissions', compact('role', 'permissions', 'owned'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $ids = array_map('intval', $request->input('permission_ids', []));
        try {
            DB::transaction(function () use ($role, $ids) {
                $role->permissions()->sync($ids);
            });
            return redirect()->route('admin.settings.roles.index')->with('success', 'Permission role diperbarui.');
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
