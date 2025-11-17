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
use App\Models\ProdukFoto;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $items = Produk::with('photos')
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
        try {
        $request->validate([
            'kode_produk' => ['nullable', 'string', 'max:50', 'unique:produk,kode_produk'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:produk,slug'],
            'photos.*' => ['nullable','image','max:4096'],
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

        $produk = Produk::create([
            'produk_id' => (string) Str::uuid(),
            'kode_produk' => $kode,
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
            'rating_avg' => 0,
            'rating_count' => 0,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Simpan galeri foto jika ada
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            $orderBase = (int) ($produk->photos()->max('urutan') ?? 0);
            $i = 0; $firstPath = null;
            foreach ($files as $file) {
                if (!$file || ! $file->isValid()) { continue; }
                $path = $file->store('produk', 'public');
                if ($firstPath === null) { $firstPath = $path; }
                ProdukFoto::create([
                    'produk_id' => $produk->produk_id,
                    'path' => $path,
                    'is_primary' => false,
                    'urutan' => $orderBase + ($i++),
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
            // Tandai foto pertama sebagai primary
            if ($firstPath) {
                $first = $produk->photos()->orderBy('created_at')->first();
                if ($first) { $first->update(['is_primary' => true]); }
            }
        }

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dibuat.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'ProdukController@store']);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal membuat produk.'));
        }
    }

    public function edit(Produk $produk)
    {
        return view('admin.produk.edit', compact('produk'));
    }

    public function update(Request $request, Produk $produk)
    {
        try {
        $request->validate([
            'kode_produk' => ['required', 'string', 'max:50', 'unique:produk,kode_produk,' . $produk->produk_id . ',produk_id'],
            'nama_produk' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:produk,slug,' . $produk->produk_id . ',produk_id'],
            'photos.*' => ['nullable','image','max:4096'],
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

        // Tidak lagi menerima input gambar tunggal; gunakan galeri

        $produk->update($payload);

        // Hapus foto yang dipilih
        $deleteIds = (array) $request->input('delete_photo_ids', []);
        if (!empty($deleteIds)) {
            $toDelete = ProdukFoto::whereIn('produk_foto_id', $deleteIds)
                ->where('produk_id', $produk->produk_id)->get();
            foreach ($toDelete as $pf) {
                try { Storage::disk('public')->delete($pf->path); } catch (\Throwable $e) {}
                $pf->delete();
            }
        }

        // Update urutan
        $orders = (array) $request->input('order', []);
        if (!empty($orders)) {
            foreach ($orders as $pid => $ord) {
                ProdukFoto::where('produk_foto_id', $pid)
                    ->where('produk_id', $produk->produk_id)
                    ->update(['urutan' => (int) $ord]);
            }
        }

        // Tambah foto baru jika ada
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            $orderBase = (int) ($produk->photos()->max('urutan') ?? 0) + 1;
            $i = 0;
            foreach ($files as $file) {
                if (!$file || ! $file->isValid()) { continue; }
                $path = $file->store('produk', 'public');
                ProdukFoto::create([
                    'produk_id' => $produk->produk_id,
                    'path' => $path,
                    'is_primary' => false,
                    'urutan' => $orderBase + ($i++),
                    'updated_by' => Auth::id(),
                    'created_by' => Auth::id(),
                ]);
            }
        }

        // Set primary photo jika dipilih
        $primaryId = $request->input('primary_photo_id');
        if ($primaryId) {
            ProdukFoto::where('produk_id', $produk->produk_id)->update(['is_primary' => false]);
            $primary = ProdukFoto::where('produk_foto_id', $primaryId)->where('produk_id', $produk->produk_id)->first();
            if ($primary) {
                $primary->update(['is_primary' => true]);
                $produk->update(['gambar_produk' => $primary->path]);
            }
        }

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'ProdukController@update', 'produk_id' => $produk->produk_id]);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal memperbarui produk.'));
        }
    }

    public function destroy(Produk $produk)
    {
        try {
            // Hapus semua foto galeri
            foreach ($produk->photos as $pf) {
                try { Storage::disk('public')->delete($pf->path); } catch (\Throwable $e) {}
                $pf->delete();
            }
            $produk->delete();
            return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'ProdukController@destroy', 'produk_id' => $produk->produk_id]);
            return back()->with('error', $this->errorMessage($e, 'Gagal menghapus produk.'));
        }
    }
}
