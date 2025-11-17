<?php

namespace App\Http\Controllers;

use App\Models\Produk;

class DetailProdukController extends Controller
{
    public function show(Produk $produk)
    {
        try {
            $produk->load(['kategori', 'jenisIkan', 'photos', 'reviews' => function ($q) {
                $q->with('pengguna')->latest();
            }]);

            $photos = $produk->photos->sortBy('urutan')->values();
            $primaryIndex = $photos->search(function ($pf) { return (bool) $pf->is_primary; });
            $primaryIndex = $primaryIndex === false ? 0 : (int) $primaryIndex;
            $photoUrls = $photos->map(fn($pf) => asset('storage/'.$pf->path))->values()->all();

            $rekomendasi = Produk::where('jenis_ikan_id', $produk->jenis_ikan_id)
                ->where('produk_id', '!=', $produk->produk_id)
                ->limit(8)
                ->get();

            return view('detail-produk', compact('produk', 'rekomendasi', 'photoUrls', 'primaryIndex'));
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'DetailProdukController@show', 'produk_id' => $produk->produk_id ?? null]);
            return back()->with('error', $this->errorMessage($e, 'Gagal memuat detail produk.'));
        }
    }
}

