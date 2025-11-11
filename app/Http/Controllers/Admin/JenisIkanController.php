<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisIkan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JenisIkanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $items = JenisIkan::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('jenis_ikan', 'ILIKE', "%{$q}%");
            })
            ->orderBy('jenis_ikan')
            ->paginate(12)
            ->withQueryString();
        return view('admin.jenis-ikan.index', compact('items', 'q'));
    }

    public function create()
    {
        return redirect()->route('admin.jenis-ikan.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_ikan' => ['required', 'string', 'max:255'],
            'gambar_jenis_ikan' => ['nullable', 'image', 'max:2048'],
        ]);

        $path = null;
        if ($request->hasFile('gambar_jenis_ikan')) {
            $path = $request->file('gambar_jenis_ikan')->store('jenis-ikan', 'public');
        }

        JenisIkan::create([
            'jenis_ikan' => $request->input('jenis_ikan'),
            'gambar_jenis_ikan' => $path,
        ]);

        return redirect()->route('admin.jenis-ikan.index')->with('success', 'Jenis ikan berhasil dibuat.');
    }

    public function edit(JenisIkan $jenis_ikan)
    {
        return view('admin.jenis-ikan.edit', ['jenisIkan' => $jenis_ikan]);
    }

    public function update(Request $request, JenisIkan $jenis_ikan)
    {
        $request->validate([
            'jenis_ikan' => ['required', 'string', 'max:255'],
            'gambar_jenis_ikan' => ['nullable', 'image', 'max:2048'],
        ]);

        $payload = ['jenis_ikan' => $request->input('jenis_ikan')];

        if ($request->hasFile('gambar_jenis_ikan')) {
            if (!empty($jenis_ikan->gambar_jenis_ikan)) {
                try {
                    Storage::disk('public')->delete($jenis_ikan->gambar_jenis_ikan);
                } catch (\Throwable $e) {
                }
            }
            $payload['gambar_jenis_ikan'] = $request->file('gambar_jenis_ikan')->store('jenis-ikan', 'public');
        }

        $jenis_ikan->update($payload);

        return redirect()->route('admin.jenis-ikan.index')->with('success', 'Jenis ikan berhasil diperbarui.');
    }

    public function destroy(JenisIkan $jenis_ikan)
    {
        if (!empty($jenis_ikan->gambar_jenis_ikan)) {
            try {
                Storage::disk('public')->delete($jenis_ikan->gambar_jenis_ikan);
            } catch (\Throwable $e) {
            }
        }
        $jenis_ikan->delete();
        return redirect()->route('admin.jenis-ikan.index')->with('success', 'Jenis ikan berhasil dihapus.');
    }
}
