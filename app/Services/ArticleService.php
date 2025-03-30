<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Models\Order;
use App\Models\Relations\ArticleImageRel;
use App\Models\Upload;

class ArticleService
{

    public function createArticle(array $articleData, array $images = [])
    {
        try {
            $article = Article::create($articleData);

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

            $articleResponse = $article->load('images');

            return ResponseHelper::build('Article created successfully', ['article' => $articleResponse]);
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: 'Failed to create an article');
        }
    }

    public function updateArticle($id, array $articleData, array $images = [])
    {
        try {
            $article = Article::find($id);

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

            if (isset($articleData['remove_images'])) {
                $uploadIds = $articleData['remove_images'];
                ArticleImageRel::whereIn('upload_id', $uploadIds)->delete();
                Upload::whereIn('id', $uploadIds)->delete();
                unset($articleData['remove_images']);
            }

            $article->update($articleData);
            $articleResponse = $article->load('images');

            return ResponseHelper::build('Article updated successfully', ['article' => $articleResponse]);
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: 'Failed to update an article');
        }

    }

    public function getArticles(int $page)
    {
        try {
            if ($page < 1) {
                return ResponseHelper::build('All article list', ['articles' => Article::all()->toArray()]);
            } else {
                $articles = Article::select('id', 'brand', 'name', 'price', 'rating')
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
                    })
                    ->toArray();
                return ResponseHelper::build('Articles for page ' . $articles['current_page'], ['page' => $articles['current_page'], 'articles' => $articles['data']]);
            }
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: 'Failed to get articles list');
        }
    }

    public function getTopArticles()
    {
        try {
            $topArticles = Article::select('id', 'brand', 'name', 'price', 'rating')
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

            return ResponseHelper::build('Top articles', ['articles' => $topArticles]);
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: 'Failed to get articles list');
        }

    }

    public function getArticleById($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return ResponseHelper::build(error: 'Article not found');
        }

        $orderedQuantity = Order::getOrderedArticleQuantity($article->id);

        $articleResponse = [
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
                return [
                    'id' => $image->id,
                    'path' => $image->path,
                ];
            }),
            'reviews' => $article->reviewsWithUser()
        ];

        return ResponseHelper::build('Article successfully retrieved', $articleResponse);
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
