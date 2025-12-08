<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KategoriProdukController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $items = KategoriProduk::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama_kategori', 'ILIKE', "%{$q}%");
            })
            ->orderBy('nama_kategori')
            ->paginate(12)
            ->withQueryString();
        return view('admin.kategori.index', compact('items', 'q'));
    }

    public function create()
    {
        return redirect()->route('admin.kategori.index');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_kategori' => ['required', 'string', 'max:255'],
                'gambar_kategori' => ['nullable', 'image', 'max:2048'],
            ]);

            $path = null;
            if ($request->hasFile('gambar_kategori')) {
                $path = $request->file('gambar_kategori')->store('kategori', 'public');
            }

            KategoriProduk::create([
                'nama_kategori' => $request->input('nama_kategori'),
                'gambar_kategori' => $path,
            ]);
            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dibuat.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'KategoriProdukController@store']);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal membuat kategori.'));
        }
    }

    public function edit(KategoriProduk $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriProduk $kategori)
    {
        try {
            $request->validate([
                'nama_kategori' => ['required', 'string', 'max:255'],
                'gambar_kategori' => ['nullable', 'image', 'max:2048'],
            ]);

            $payload = ['nama_kategori' => $request->input('nama_kategori')];

            if ($request->hasFile('gambar_kategori')) {
                if (!empty($kategori->gambar_kategori)) {
                    try {
                        Storage::disk('public')->delete($kategori->gambar_kategori);
                    } catch (\Throwable $e) {
                    }
                }
                $payload['gambar_kategori'] = $request->file('gambar_kategori')->store('kategori', 'public');
            }

            $kategori->update($payload);

            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'KategoriProdukController@update', 'kategori_produk_id' => $kategori->kategori_produk_id]);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal memperbarui kategori.'));
        }
    }

    public function destroy(KategoriProduk $kategori)
    {
        try {
            // Blokir jika ada produk (termasuk yang soft-deleted) yang terkait dengan pesanan
            $hasPesanan = DB::table('produk')
                ->join('pesanan_item', 'pesanan_item.produk_id', '=', 'produk.produk_id')
                ->where('produk.kategori_produk_id', $kategori->kategori_produk_id)
                ->exists();
            if ($hasPesanan) {
                return back()->with('error', 'Kategori tidak dapat dihapus karena terdapat produk yang sudah terkait dengan pesanan (termasuk yang sudah dihapus sementara). Pindahkan produk ke kategori lain atau biarkan kategori ini sebagai arsip.');
            }

            // Blokir jika masih ada produk (aktif atau soft-deleted) di kategori ini
            $anyProduk = DB::table('produk')->where('kategori_produk_id', $kategori->kategori_produk_id)->exists();
            if ($anyProduk) {
                return back()->with('error', 'Kategori masih memiliki produk (termasuk yang dihapus sementara). Pindahkan produk ke kategori lain atau hapus permanen produk yang tidak terikat pesanan.');
            }
            if (!empty($kategori->gambar_kategori)) {
                try {
                    Storage::disk('public')->delete($kategori->gambar_kategori);
                } catch (\Throwable $e) {
                }
            }
            $kategori->delete();
            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'KategoriProdukController@destroy', 'kategori_produk_id' => $kategori->kategori_produk_id]);
            return back()->with('error', $this->errorMessage($e, 'Gagal menghapus kategori.'));
        }
    }
}
