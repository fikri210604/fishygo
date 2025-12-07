<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\ReviewProduk;
use App\Models\JenisIkan;
use App\Models\Article;

class WelcomeController extends Controller
{
    public function index()
    {
        // Ambil produk aktif beserta foto utama jika ada
        $data = Produk::query()
            ->aktif()
            ->with(['primaryPhoto'])
            ->select(['produk_id','slug','nama_produk','deskripsi','harga','stok','rating_avg as rating'])
            ->orderByDesc('created_at')
            ->take(9)
            ->get();

        // Info pelengkap lain jika diperlukan di landing
        $review = ReviewProduk::query()->latest()->take(5)->get();
        $jenis = JenisIkan::orderBy('jenis_ikan')->get();
        $artikel = Article::paginate(4);

        return view('welcome', compact('data','review','jenis','artikel'));
    }
}
