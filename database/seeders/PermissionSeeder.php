<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $permissions = collect([
            // Artikel
            ['slug' => 'article.create', 'nama' => 'Buat Artikel', 'modul' => 'article'],
            ['slug' => 'article.update', 'nama' => 'Ubah Artikel', 'modul' => 'article'],
            ['slug' => 'article.delete', 'nama' => 'Hapus Artikel', 'modul' => 'article'],
            ['slug' => 'article.publish', 'nama' => 'Terbitkan Artikel', 'modul' => 'article'],

            // Produk / Gudang
            ['slug' => 'product.create', 'nama' => 'Buat Produk', 'modul' => 'product'],
            ['slug' => 'product.update', 'nama' => 'Ubah Produk', 'modul' => 'product'],
            ['slug' => 'product.delete', 'nama' => 'Hapus Produk', 'modul' => 'product'],
            ['slug' => 'stock.adjust', 'nama' => 'Penyesuaian Stok', 'modul' => 'product'],
            ['slug' => 'stock.view', 'nama' => 'Lihat Stok', 'modul' => 'product'],
        ]);

        // Insert / Update permissions
        $permissions->each(function ($p) use ($now) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $p['slug']],
                array_merge($p, ['aktif' => 1, 'created_at' => $now, 'updated_at' => $now])
            );
        });

        $roleMap = [
            'admin'  => $permissions->pluck('slug')->all(), // semua izin
            'author' => ['article.create', 'article.update'],
            'gudang' => ['product.create', 'stock.adjust', 'stock.view'],
        ];

        foreach ($roleMap as $roleSlug => $permissionSlugs) {
            if (! $role = DB::table('roles')->where('slug', $roleSlug)->first()) continue;

            $permissionIds = DB::table('permissions')
                ->whereIn('slug', $permissionSlugs)
                ->pluck('id');

            foreach ($permissionIds as $pid) {
                DB::table('role_permission')->updateOrInsert(
                    ['role_id' => $role->id, 'permission_id' => $pid],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}
