<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Support\HighlightCode;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View;
use Illuminate\Support\Facades\Cache;

class ArticleController extends Controller
{
    public function index(): View\Factory|View\View|Application
    {
        $articles = Article::query()
            ->with(['categories', 'author'])
            ->latest()
            ->paginate(10);

        return view('articles.index', [
            'articles' => $articles
        ]);
    }

    public function show(Article $article): View\Factory|View\View|Application
    {
        return view('articles.show', compact('article'));
    }
}
