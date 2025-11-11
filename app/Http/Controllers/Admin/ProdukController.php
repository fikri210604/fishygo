<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\KategoriProduk;
use App\Models\JenisIkan;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $items = Produk::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama_produk', 'ILIKE', "%{$q}%");
            })
            ->orderBy('nama_produk')
            ->paginate(12)
            ->withQueryString();
        return view('admin.produk.index', compact('items', 'q'));
    }

    public function create()
    {
        return view('admin.produk.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => ['nullable', 'string', 'max:50', 'unique:produk,kode_produk'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:produk,slug'],
            'gambar_produk' => ['nullable', 'image', 'max:4096'],
            'kategori_produk_id' => ['required', 'integer', 'exists:kategori_produk,kategori_produk_id'],
            'jenis_ikan_id' => ['required', 'integer', 'exists:jenis_ikan,jenis_ikan_id'],
            'harga' => ['required', 'numeric', 'min:0'],
            'harga_promo' => ['nullable', 'numeric', 'min:0'],
            'promo_mulai' => ['nullable', 'date'],
            'promo_selesai' => ['nullable', 'date', 'after_or_equal:promo_mulai'],
            'deskripsi' => ['required', 'string'],
            'satuan' => ['required', 'string', 'max:10'],
            'stok' => ['required', 'integer', 'min:0'],
            'berat_gram' => ['nullable', 'integer', 'min:0'],
            'kadaluarsa' => ['nullable', 'date'],
            'aktif' => ['nullable', 'in:0,1'],
        ], ['kode_produk.unique' => 'Kode produk sudah digunakan.']);

        // Generate kode produk berdasarkan kategori & jenis jika tidak diisi
        $kode = $request->input('kode_produk');
        if (empty($kode)) {
            $kategori = KategoriProduk::find((int) $request->input('kategori_produk_id'));
            $jenis = JenisIkan::find((int) $request->input('jenis_ikan_id'));
            $abbr = function (string $text): string {
                $text = trim($text);
                if ($text === '')
                    return 'PRD';
                $parts = preg_split('/\s+/', $text);
                $ini = '';
                foreach ($parts as $p) {
                    $ini .= mb_substr($p, 0, 1);
                }
                $ini = strtoupper(mb_substr($ini, 0, 3));
                if (strlen($ini) < 3) {
                    $ini = strtoupper(substr(str_replace(' ', '', $text), 0, 3));
                }
                return $ini ?: 'PRD';
            };
            $base = strtoupper($abbr($kategori?->nama_kategori ?? 'CAT') . '-' . $abbr($jenis?->jenis_ikan ?? 'JNS'));
            $code = $base . '-' . strtoupper(Str::random(4));
            $i = 1;
            while (Produk::where('kode_produk', $code)->exists()) {
                $suffix = $i < 10 ? '0' . $i : (string) $i;
                $code = $base . '-' . $suffix;
                $i++;
            }
            $kode = $code;
        }
        $slug = $request->input('slug') ?: Str::slug($request->input('nama_produk')) . '-' . Str::lower(Str::random(6));

        $img = null;
        if ($request->hasFile('gambar_produk')) {
            $img = $request->file('gambar_produk')->store('produk', 'public');
        }

        Produk::create([
            'produk_id' => (string) Str::uuid(),
            'kode_produk' => $kode,
            'slug' => $slug,
            'nama_produk' => $request->input('nama_produk'),
            'gambar_produk' => $img,
            'kategori_produk_id' => $request->input('kategori_produk_id'),
            'jenis_ikan_id' => $request->input('jenis_ikan_id'),
            'harga' => $request->input('harga'),
            'harga_promo' => $request->input('harga_promo'),
            'promo_mulai' => $request->input('promo_mulai'),
            'promo_selesai' => $request->input('promo_selesai'),
            'deskripsi' => $request->input('deskripsi'),
            'satuan' => $request->input('satuan'),
            'stok' => $request->input('stok'),
            'berat_gram' => $request->input('berat_gram'),
            'kadaluarsa' => $request->input('kadaluarsa'),
            'aktif' => $request->boolean('aktif') ? '1' : '1',
            'rating_avg' => 0,
            'rating_count' => 0,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Produk $produk)
    {
        return view('admin.produk.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:produk,kode_produk,' . $produk->produk_id . ',produk_id'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:produk,slug,' . $produk->produk_id . ',produk_id'],
            'gambar_produk' => ['nullable', 'image', 'max:4096'],
            'kategori_produk_id' => ['required', 'integer', 'exists:kategori_produk,kategori_produk_id'],
            'jenis_ikan_id' => ['required', 'integer', 'exists:jenis_ikan,jenis_ikan_id'],
            'harga' => ['required', 'numeric', 'min:0'],
            'harga_promo' => ['nullable', 'numeric', 'min:0'],
            'promo_mulai' => ['nullable', 'date'],
            'promo_selesai' => ['nullable', 'date', 'after_or_equal:promo_mulai'],
            'deskripsi' => ['required', 'string'],
            'satuan' => ['required', 'string', 'max:10'],
            'stok' => ['required', 'integer', 'min:0'],
            'berat_gram' => ['nullable', 'integer', 'min:0'],
            'kadaluarsa' => ['nullable', 'date'],
            'aktif' => ['nullable', 'in:0,1'],
        ], ['kode_produk.unique' => 'Kode produk sudah digunakan.']);

        $slug = $request->input('slug') ?: $produk->slug;

        $payload = [
            'kode_produk' => $request->input('kode_produk'),
            'slug' => $slug,
            'nama_produk' => $request->input('nama_produk'),
            'kategori_produk_id' => $request->input('kategori_produk_id'),
            'jenis_ikan_id' => $request->input('jenis_ikan_id'),
            'harga' => $request->input('harga'),
            'harga_promo' => $request->input('harga_promo'),
            'promo_mulai' => $request->input('promo_mulai'),
            'promo_selesai' => $request->input('promo_selesai'),
            'deskripsi' => $request->input('deskripsi'),
            'satuan' => $request->input('satuan'),
            'stok' => $request->input('stok'),
            'berat_gram' => $request->input('berat_gram'),
            'kadaluarsa' => $request->input('kadaluarsa'),
            'aktif' => $request->boolean('aktif') ? '1' : '1',
            'updated_by' => Auth::id(),
        ];

        if ($request->hasFile('gambar_produk')) {
            if (!empty($produk->gambar_produk)) {
                try {
                    Storage::disk('public')->delete($produk->gambar_produk);
                } catch (\Throwable $e) {
                }
            }
            $payload['gambar_produk'] = $request->file('gambar_produk')->store('produk', 'public');
        }

        $produk->update($payload);

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        if (!empty($produk->gambar_produk)) {
            try {
                Storage::disk('public')->delete($produk->gambar_produk);
            } catch (\Throwable $e) {
            }
        }
        $produk->delete();
        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus.');
    }
}
