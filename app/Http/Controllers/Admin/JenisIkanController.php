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
        try {
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
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'JenisIkanController@store']);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal membuat jenis ikan.'));
        }
    }

    public function edit(JenisIkan $jenis_ikan)
    {
        return view('admin.jenis-ikan.edit', ['jenisIkan' => $jenis_ikan]);
    }

    public function update(Request $request, JenisIkan $jenis_ikan)
    {
        try {
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
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'JenisIkanController@update', 'jenis_ikan_id' => $jenis_ikan->jenis_ikan_id]);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal memperbarui jenis ikan.'));
        }
    }

    public function destroy(JenisIkan $jenis_ikan)
    {
        try {
            if (!empty($jenis_ikan->gambar_jenis_ikan)) {
                try {
                    Storage::disk('public')->delete($jenis_ikan->gambar_jenis_ikan);
                } catch (\Throwable $e) {
                }
            }
            $jenis_ikan->delete();
            return redirect()->route('admin.jenis-ikan.index')->with('success', 'Jenis ikan berhasil dihapus.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'JenisIkanController@destroy', 'jenis_ikan_id' => $jenis_ikan->jenis_ikan_id]);
            return back()->with('error', $this->errorMessage($e, 'Gagal menghapus jenis ikan.'));
        }
    }
}
