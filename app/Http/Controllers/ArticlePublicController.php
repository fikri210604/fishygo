<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticlePublicController extends Controller
{
    public function index()
    {
        try {
            $articles = Article::query()
                ->whereNotNull('diterbitkan_pada')
                ->orderByDesc('diterbitkan_pada')
                ->paginate(10);

            return view('articles.index', compact('articles'));
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'ArticlePublicController@index']); }
            return back()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal memuat artikel.') : 'Terjadi kesalahan.');
        }
    }

    public function show(Article $article)
    {
        try {
            if (is_null($article->diterbitkan_pada)) {
                abort(404);
            }
            return view('articles.show', compact('article'));
        } catch (\Throwable $e) {
            if (method_exists($this, 'logException')) { $this->logException($e, ['action' => 'ArticlePublicController@show', 'id' => $article->id ?? null]); }
            return back()->with('error', method_exists($this, 'errorMessage') ? $this->errorMessage($e, 'Gagal menampilkan artikel.') : 'Terjadi kesalahan.');
        }
    }
}
