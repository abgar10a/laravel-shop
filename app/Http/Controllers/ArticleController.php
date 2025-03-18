<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    private $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index()
    {
        $articles = Article::all();
        return response()->json($articles);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'quantity' => 'required|string|max:255',
            'type_id' => 'required|exists:types,id',
            'price' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
            'color_id' => 'required|exists:colors,id',
        ]);

        $article = $this->articleService->createArticle($validated);

        return response()->json($article, Response::HTTP_CREATED);
    }

    public function show(Article $article)
    {
        return response()->json($article);
    }

    public function edit(Article $article)
    {
        //
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'brand' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|required|string|max:255',
            'type_id' => 'sometimes|required|exists:types,id',
            'price' => 'sometimes|required|numeric|min:0',
            'user_id' => 'sometimes|required|exists:users,id',
            'color_id' => 'sometimes|required|exists:colors,id',
        ]);

        $article->update($validated);

        return response()->json($article);
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json(['message' => 'Article deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
