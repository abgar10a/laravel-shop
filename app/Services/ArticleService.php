<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Order;
use App\Models\Relations\ArticleImageRel;
use App\Models\Upload;

class ArticleService
{

    public function createOrUpdateArticle(array $articleData, array $images = [], $articleId = null)
    {
        try {
            $article = $articleId ? Article::find($articleId) : Article::create($articleData);

            if (!empty($images)) {
                $sequence = 1;

                foreach ($images as $image) {
                    $imagePath = $image->store('articleImages', 'public');

                    $upload = Upload::create([
                        'type' => $image->getClientMimeType(),
                        'path' => $imagePath,
                    ]);

                    ArticleImageRel::create([
                        'article_id' => $article->id,
                        'upload_id' => $upload->id,
                        'sequence' => $sequence++,
                    ]);
                }
            }
            return $article->load('images');
        } catch (\Throwable $th) {
            return response($th->getMessage());
        }

    }

    public function getArticles(int $page)
    {
        try {
            if ($page < 1) {
                return Article::all();
            } else {
                return Article::select('id', 'brand', 'name', 'price', 'rating')
                    ->where('quantity', '>', 0)
                    ->with(['images' => function ($query) {
                        $query->where('article_image_rel.sequence', 1);
                    }])
                    ->paginate(10)
                    ->through(function ($article) {
                        $image = $article->images->first();

                        return [
                            'id' => $article->id,
                            'brand' => $article->brand,
                            'name' => $article->name,
                            'price' => $article->price,
                            'rating' => $article->rating,
                            'image' => $image ? asset('storage/' . $image->path) : null,
                        ];
                    });
            }
        } catch (\Throwable $th) {
            return response($th->getMessage());
        }
    }

    public function getTopArticles()
    {
        try {
            return Article::select('id', 'brand', 'name', 'price', 'rating')
                ->orderBy('rating', 'desc')
                ->take(10)
                ->with(['images' => function ($query) {
                    $query->where('article_image_rel.sequence', 1);
                }])
                ->get()
                ->map(function ($article) {
                    $image = $article->images->first();

                    return [
                        'id' => $article->id,
                        'brand' => $article->brand,
                        'name' => $article->name,
                        'price' => $article->price,
                        'rating' => $article->rating,
                        'image' => $image ? asset('storage/' . $image->path) : null,
                    ];
                });
        } catch (\Throwable $th) {
            return response($th->getMessage());
        }

    }

    public function getArticleById(Article $article)
    {
        $orderedQuantity = Order::getOrderedArticleQuantity($article->id);

        return [
            'id' => $article->id,
            'brand' => $article->brand,
            'name' => $article->name,
            'quantity' => $article->quantity - $orderedQuantity,
            'type_id' => $article->type_id,
            'price' => $article->price,
            'user_id' => $article->user_id,
            'color_id' => $article->color_id,
            'rating' => $article->rating,
            'images' => $article->images->map(function ($image) {
                return asset('storage/' . $image->path);
            }),
            'reviews' => $article->reviewsWithUser()
        ];
    }

    public function deleteArticle($id)
    {
        try {
            $article = Article::find($id);
            if ($article) {
                $article->delete();
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }
}
