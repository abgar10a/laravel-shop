<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Models\Order;
use App\Models\Review;

class ReviewService
{

    public function getReviewsByArticleId($articleId)
    {
        $article = Article::find($articleId);

        if ($article) {
            $reviews = $article->reviewsWithUser();

            return ResponseHelper::build('Reviews retrieved successfully', ['reviews' => $reviews]);
        } else {
            return ResponseHelper::build(error: 'Article not found');
        }


    }

    public function createReview($data, $user)
    {
        $userOrder = Order::where('article_id', $data['article_id'])
            ->where('user_id', $user->id)
            ->first();

        if (!$userOrder) {
            return ResponseHelper::build(error: 'User can not rate this article');
        }

        $data['user_id'] = auth()->id();
        $review = Review::create($data);
        $this->updateArticleRating($review->article_id);

        return ResponseHelper::build('Review created successfully', ['review' => $review]);
    }

    public function updateReview($id, $reviewData, $user)
    {
        $review = Review::find($id);

        if (!$review) {
            return ResponseHelper::build(error: 'Review not found');
        } else if ($user->cannot('update', $review)) {
            return ResponseHelper::build(error: 'User can not update this review');
        }

        $review->update([
            'rating' => $reviewData['rating'],
            'comment' => $reviewData['comment'],
            'updated_at' => now()
        ]);
        $this->updateArticleRating($review->article_id);

        return ResponseHelper::build('Review updated successfully', ['review' => $review]);
    }

    public function deleteReview($id, $user)
    {
        $review = Review::find($id);

        if (!$review) {
            return ResponseHelper::build(error: 'Review not found');
        } else if ($user->cannot('delete', $review)) {
            return ResponseHelper::build(error: 'User can not delete this review');
        }

        $review->delete();
        $this->updateArticleRating($review->article_id);

        return ResponseHelper::build('Review deleted successfully');
    }

    public function updateArticleRating($articleId)
    {
        $averageRating = Review::where('article_id', $articleId)
            ->avg('rating');

        $article = Article::find($articleId);
        if ($article) {
            $article->rating = round($averageRating, 1);
            $article->updated_at = now();
            $article->save();
        }
    }

}
