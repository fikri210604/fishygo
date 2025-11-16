<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
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
        $request->validate([
            'nama_kategori' => ['required','string','max:255'],
            'gambar_kategori' => ['nullable','image','max:2048'],
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
    }

    public function edit(KategoriProduk $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, KategoriProduk $kategori)
    {
        $request->validate([
            'nama_kategori' => ['required','string','max:255'],
            'gambar_kategori' => ['nullable','image','max:2048'],
        ]);

        $payload = [ 'nama_kategori' => $request->input('nama_kategori') ];

        if ($request->hasFile('gambar_kategori')) {
            if (!empty($kategori->gambar_kategori)) {
                try { Storage::disk('public')->delete($kategori->gambar_kategori); } catch (\Throwable $e) {}
            }
            $payload['gambar_kategori'] = $request->file('gambar_kategori')->store('kategori', 'public');
        }

        $kategori->update($payload);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriProduk $kategori)
    {
        if (!empty($kategori->gambar_kategori)) {
            try { Storage::disk('public')->delete($kategori->gambar_kategori); } catch (\Throwable $e) {}
        }
        $kategori->delete();
        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
