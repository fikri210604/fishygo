<?php

namespace App\Http\Controllers;

use App\Models\Produk;

class DetailProdukController extends Controller
{
    public function show(Produk $produk)
    {
        try {
            $produk->load([
                'kategori',
                'jenisIkan',
                'photos' => fn($q) => $q->orderBy('urutan'),
                'reviews' => fn($q) => $q->with('pengguna')->latest()
            ]);

            $photos = $produk->photos->map(function ($p) {
                return asset('storage/' . $p->path);
            })->values();

            if ($photos->isEmpty()) {
                $photos = collect([asset('assets/images/no-image.svg')]);
            }

            $primaryIndex = $produk->photos
                ->search(fn($p) => (bool) $p->is_primary);

            $primaryIndex = $primaryIndex === false ? 0 : (int) $primaryIndex;

            $rekomendasi = Produk::where('jenis_ikan_id', $produk->jenis_ikan_id)
                ->where('produk_id', '!=', $produk->produk_id)
                ->limit(8)
                ->get();

            return view('detail-produk', [
                'produk'       => $produk,
                'photos'       => $photos,        // sesuai kebutuhan Blade baru
                'primaryIndex' => $primaryIndex,  // index awal foto
                'rekomendasi'  => $rekomendasi
            ]);

        } catch (\Throwable $e) {
            $this->logException($e, [
                'action' => 'DetailProdukController@show',
                'produk_id' => $produk->produk_id ?? null
            ]);

            return back()->with(
                'error',
                $this->errorMessage($e, 'Gagal memuat detail produk.')
            );
        }
    }
}
