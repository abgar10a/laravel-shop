<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Models\Order;
use App\Models\Relations\ArticleImageRel;
use App\Models\Upload;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ArticleService
{
    public function createArticle(array $articleData, array $images = [])
    {
        try {
            $article = Article::create($articleData);

            $this->relateArticleImage($images, $article);

            $articleResponse = $article->load('images');

            return ResponseHelper::build(__('articles.created'), ['article' => $articleResponse]);
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: __('articles.failed_to_create'));
        }
    }

    public function updateArticle($id, array $articleData, array $images = [], $user = null)
    {
        try {
            $article = Article::find($id);

            if ($user->cannot('update', $article)) {
                return ResponseHelper::build(
                    error: __('user.permission_fail', [
                        'action' => 'update',
                        'model' => 'article',
                    ]));
            }

            $this->relateArticleImage($images, $article);

            if (isset($articleData['remove_images'])) {
                $uploadIds = $articleData['remove_images'];
                ArticleImageRel::whereIn('upload_id', $uploadIds)->delete();
                Upload::whereIn('id', $uploadIds)->delete();
                unset($articleData['remove_images']);
            }

            $article->update($articleData);
            $articleResponse = $article->load('images');

            return ResponseHelper::build(__('articles.updated'), ['article' => $articleResponse]);
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: __('articles.failed', ['action' => 'update']));
        }

    }

    public function getArticles(int $page = 1, int $perPage = 10, $filters = [], $order = [])
    {
        try {
            if ($page < 1) {
                return ResponseHelper::build('All article list', ['articles' => Article::all()->toArray()]);
            }

            $cachedArticles = Cache::get(request()->getRequestUri(), []);
            if (!empty($cachedArticles)) {
//                dd($page);
                return ResponseHelper::build(trans_choice('articles.retrieved_list_cache', $page), $cachedArticles);
            }
            $articles = $this->articleQuerry($filters, $order)->select('id', 'brand', 'name', 'price', 'rating')
//                    ->inStock()
                ->with(['images' => function ($query) {
                    $query->where('article_image_rel.sequence', 1);
                }])
                ->paginate($perPage);

            $totalCount = $articles->total();

            $transformedArticles = $articles->through(function ($article) {
                $image = $article->images->first();

                return [
                    'id' => $article->id,
                    'brand' => $article->brand,
                    'name' => $article->name,
                    'price' => $article->price,
                    'rating' => $article->rating,
                    'image' => $image ? asset(Storage::url($image->path)) : null,
                ];
            })->toArray();

            $articleList = [
                'page' => $transformedArticles['current_page'],
                'total_items' => $totalCount,
                'articles' => $transformedArticles['data']
            ];

            Cache::put(request()->getRequestUri(), $articleList, 86400);

            return ResponseHelper::build(trans_choice('articles.retrieved_list', $page), $articleList);
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: 'Failed to get articles list' . $th->getMessage());;
        }
    }

    public function getTopArticles()
    {
        try {
            $cachedArticles = Cache::get(request()->getRequestUri(), []);
            if (!empty($cachedArticles)) {
                return ResponseHelper::build('Top articles from cache', ['articles' => $cachedArticles]);
            }

            $topArticles = Article::select('id', 'brand', 'name', 'price', 'rating')
                ->inStock()
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
                        'image' => $image ? asset(Storage::url($image->path)) : null,
                    ];
                });

            Cache::put(request()->getRequestUri(), $topArticles, 86400);

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
                    'path' => asset(Storage::url($image->path)),
                ];
            }),
            'reviews' => $article->reviewsWithUser(),
            'attributes' => $article->attributes
        ];

        return ResponseHelper::build('Article successfully retrieved', $articleResponse);
    }

    public function deleteArticle($id, $user)
    {
        try {
            $article = Article::find($id);
            if ($article) {
                if ($user->cannot('delete', $article)) {
                    return ResponseHelper::build(error: "You don't have permission to remove article");
                }

                $article->delete();
                return ResponseHelper::build('Article successfully deleted');
            } else {
                return ResponseHelper::build(error: 'Article not found');
            }
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: 'Failed to delete article');
        }
    }

    public function relateArticleImage(array $images, $article): void
    {
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
    }

    // TODO use typesense
    public function articleQuerry($filters = [], $order = [])
    {
        $query = Article::query();

        if ($filters) {
            if (isset($filters['type'])) {
                $query->where('type_id', $filters['type']);
            }

            if (isset($filters['brand'])) {
                $brand = $filters['brand'];
                $query->where('brand', 'like', "%$brand%");
            }

            if (isset($filters['price_min'])) {
                $query->where('price', '>=', $filters['price_min']);
            }

            if (isset($filters['price_max'])) {
                $query->where('price', '<=', $filters['price_max']);
            }
        }

        if (isset($order['by'], $order['direction'])) {
            $query->orderBy($order['by'], $order['direction']);
        }

        return $query;
    }
}
