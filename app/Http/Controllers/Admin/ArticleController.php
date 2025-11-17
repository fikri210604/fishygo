<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $articles = Article::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('judul', 'ILIKE', "%{$q}%");
            })
            ->orderBy('judul')
            ->paginate(12)
            ->withQueryString();
        return view('admin.articles.index', compact('articles', 'q'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'judul' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', 'unique:artikel,slug'],
                'isi' => ['required', 'string'],
                'thumbnail' => ['nullable', 'image', 'max:2048'],
                'published' => ['nullable', 'boolean'],
            ]);

            $slug = $request->input('slug') ?: Str::slug($request->input('judul'));
            // Ensure unique slug
            $base = $slug;
            $i = 1;
            while (Article::where('slug', $slug)->exists()) {
                $slug = $base . '-' . ($i++);
            }

            $thumbPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbPath = $request->file('thumbnail')->store('article-thumbnails', 'public');
            }

            $article = Article::create([
                'judul' => $request->input('judul'),
                'slug' => $slug,
                'isi' => $request->input('isi'),
                'thumbnail' => $thumbPath,
                'penulis_id' => Auth::id(),
                'diterbitkan_pada' => ($request->boolean('published') ? now() : null),
            ]);
            return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dibuat.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'ArticleController@store']);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal membuat artikel.'));
        }
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        try {
            $data = $request->validate([
                'judul' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', Rule::unique('artikel', 'slug')->ignore($article->id, 'id')],
                'isi' => ['required', 'string'],
                'thumbnail' => ['nullable', 'image', 'max:2048'],
                'published' => ['nullable', 'boolean'],
            ]);

            $slug = $request->input('slug') ?: Str::slug($request->input('judul'));
            if ($slug !== $article->slug) {
                $base = $slug;
                $i = 1;
                while (Article::where('slug', $slug)->where('id', '!=', $article->id)->exists()) {
                    $slug = $base . '-' . ($i++);
                }
            }

            $thumbPath = $article->thumbnail;
            if ($request->hasFile('thumbnail')) {
                if (!empty($thumbPath)) {
                    try {
                        Storage::disk('public')->delete($thumbPath);
                    } catch (\Throwable $e) {
                    }
                }
                $thumbPath = $request->file('thumbnail')->store('article-thumbnails', 'public');
            }

            $article->update([
                'judul' => $request->input('judul'),
                'slug' => $slug,
                'isi' => $request->input('isi'),
                'thumbnail' => $thumbPath,
                'diterbitkan_pada' => ($request->boolean('published') ? ($article->diterbitkan_pada ?? now()) : null),
            ]);
            return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'ArticleController@update', 'id' => $article->id]);
            return back()->withInput()->with('error', $this->errorMessage($e, 'Gagal memperbarui artikel.'));
        }
    }

    public function destroy(Article $article)
    {
        try {
            // Hapus thumbnail bila ada
            if (!empty($article->thumbnail)) {
                try {
                    Storage::disk('public')->delete($article->thumbnail);
                } catch (\Throwable $e) {

                }
            }
            $article->delete();
            return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
        } catch (\Throwable $e) {
            $this->logException($e, ['action' => 'ArticleController@destroy', 'id' => $article->id]);
            return back()->with('error', $this->errorMessage($e, 'Gagal menghapus artikel.'));
        }
    }
}
