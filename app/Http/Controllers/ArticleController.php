<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    private $articleService;

    public function __construct()
    {
        $this->articleService = app(ArticleService::class);
    }

    public function index(Request $request)
    {
        $page = $request->has('page') ? (int)$request->query('page') : 0;
        $articles = $this->articleService->getArticles($page);
        return ResponseHelper::success('Articles', $articles);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $articleData = $request->validate([
                'brand' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'quantity' => 'required|string|max:255',
                'type_id' => 'required|exists:types,id',
                'price' => 'required|numeric|min:0',
                'user_id' => 'required|exists:users,id',
                'color_id' => 'required|exists:colors,id',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $images = $request->hasFile('images') ? $request->file('images') : [];

            $article = $this->articleService->createOrUpdateArticle($articleData, $images);

            return ResponseHelper::success('Article saved successfully', $article, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function show($id)
    {
        try {
            $article = Article::find($id);

            if (!$article) {
                return ResponseHelper::error('Article not found', Response::HTTP_NOT_FOUND);
            }

            return ResponseHelper::success('Get article', $this->articleService->getArticleById($article));
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function edit(Article $article)
    {
        //
    }

    public function update(Request $request)
    {
        try {
            $articleData = $request->validate([
                'brand' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'quantity' => 'required|string|max:255',
                'type_id' => 'required|exists:types,id',
                'price' => 'required|numeric|min:0',
                'color_id' => 'required|exists:colors,id',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $images = $request->hasFile('images') ? $request->file('images') : [];

            $article = $this->articleService->createOrUpdateArticle($articleData, $images);

            return ResponseHelper::success('Article updated successfully', $article, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            if (!$id) {
                return ResponseHelper::error('Wrong parameter', Response::HTTP_BAD_REQUEST);
            }

            $deleted = $this->articleService->deleteArticle($id);
            if ($deleted) {
                return ResponseHelper::success('Article deleted successfully');
            } else {
                return ResponseHelper::error('Article not found', Response::HTTP_NOT_FOUND);
            }
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTopArticles() {
        $articles = $this->articleService->getTopArticles();

        return ResponseHelper::success('Get top articles', $articles);
    }
}
