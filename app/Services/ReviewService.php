<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\Review;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReviewService
{

    public function getReviewsByArticleId($articleId)
    {
        return Review::where('article_id', $articleId)->get();
    }

    public function createReview($data)
    {
        try {
            $data['user_id'] = auth()->id();
            return Review::create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create review: ' . $e->getMessage());
        }
    }

    public function getReviewById($id)
    {
        return Review::findOrFail($id);
    }

    public function updateReview($id, $reviewData)
    {
        $review = Review::find($id);

        if (!$review) {
            throw new ModelNotFoundException('Review not found');
        }

        if ($review->user_id !== auth()->id()) {
            throw new \Exception('Unauthorized');
        }

        $review->update($reviewData);

        return $review;
    }

    public function deleteReview($id)
    {
        $review = Review::find($id);

        if (!$review) {
            throw new ModelNotFoundException('Review not found');
        }

        if ($review->user_id !== Auth::id()) {
            throw new Exception('Unauthorized');
        }

        $review->delete();
    }

}
