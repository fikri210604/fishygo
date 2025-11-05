<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticlePublicController extends Controller
{
    public function index()
    {
        $articles = Article::query()
            ->whereNotNull('diterbitkan_pada')
            ->orderByDesc('diterbitkan_pada')
            ->paginate(10);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        if (is_null($article->diterbitkan_pada)) {
            abort(404);
        }
        return view('articles.show', compact('article'));
    }
}
