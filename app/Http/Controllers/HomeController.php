<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\JenisIkan;
use App\Models\Article;
class HomeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $q = trim((string) $request->query('q'));
            $kategoriId = $request->query('kategori');
            $jenisId = $request->query('jenis');

            $artikel = Article::select('judul', 'thumbnail', 'slug', 'diterbitkan_pada')
                ->whereNotNull('diterbitkan_pada')
                ->latest()
                ->limit(6)
                ->get();

            $produk = Produk::select('produk_id', 'slug', 'nama_produk', 'harga', 'kategori_produk_id', 'jenis_ikan_id')
                ->with('photos')
                ->when($q !== '', function ($query) use ($q) {
                    $query->where('nama_produk', 'ILIKE', "%{$q}%");
                })
                ->when(!empty($kategoriId), function ($query) use ($kategoriId) {
                    $query->where('kategori_produk_id', $kategoriId);
                })
                ->when(!empty($jenisId), function ($query) use ($jenisId) {
                    $query->where('jenis_ikan_id', $jenisId);
                })
                ->orderBy('nama_produk')
                ->paginate(12, ['*'], 'page_produk')
                ->withQueryString();

            $kategori = KategoriProduk::select('kategori_produk_id', 'nama_kategori', 'gambar_kategori')
                ->orderBy('nama_kategori')
                ->paginate(6, ['*'], 'page_kategori')
                ->withQueryString();

            $jenis_ikan = JenisIkan::select('jenis_ikan_id', 'jenis_ikan', 'gambar_jenis_ikan')
                ->orderBy('jenis_ikan')
                ->get();

            // Untuk semua permintaan AJAX ke listing produk, kembalikan partial grid saja
            if ($request->ajax()) {
                return view('partials.products-grid', compact('produk'))->render();
            }

            return view('home', compact('artikel', 'produk', 'kategori', 'jenis_ikan', 'q', 'kategoriId', 'jenisId'));
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) {
                $this->logException($e, ['action' => 'HomeController@index']);
            }
            return back()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal memuat beranda.') : 'Terjadi kesalahan.');
        }
    }

}

