<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::query()->latest()->paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255', 'unique:artikel,slug'],
            'isi' => ['required','string'],
            'published' => ['nullable','boolean'],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['judul']);
        // Ensure unique slug
        $base = $slug; $i = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $base.'-'.($i++);
        }

        $article = Article::create([
            'judul' => $data['judul'],
            'slug' => $slug,
            'isi' => $data['isi'],
            'penulis_id' => Auth::id(),
            'diterbitkan_pada' => ($request->boolean('published') ? now() : null),
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'judul' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255', Rule::unique('artikel','slug')->ignore($article->id, 'id')],
            'isi' => ['required','string'],
            'published' => ['nullable','boolean'],
        ]);

        $slug = $data['slug'] ?? Str::slug($data['judul']);
        if ($slug !== $article->slug) {
            $base = $slug; $i = 1;
            while (Article::where('slug', $slug)->where('id','!=',$article->id)->exists()) {
                $slug = $base.'-'.($i++);
            }
        }

        $article->update([
            'judul' => $data['judul'],
            'slug' => $slug,
            'isi' => $data['isi'],
            'diterbitkan_pada' => ($request->boolean('published') ? ($article->diterbitkan_pada ?? now()) : null),
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return view('admin.articles.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
