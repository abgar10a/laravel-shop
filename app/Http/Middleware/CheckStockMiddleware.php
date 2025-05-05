<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStockMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $requestData = $request->validate([
                'article_id' => 'required|exists:articles,id',
                'order_quantity' => 'required|integer|min:1',
            ]);

            $article = Article::findOrFail($requestData['article_id'])
                ->inStock($requestData['order_quantity'])
                ->first();

            if (!empty($article)) {
                return $next($request);
            }

            return ResponseHelper::error('No articles in stock', Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Unable to process order.' . $th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
