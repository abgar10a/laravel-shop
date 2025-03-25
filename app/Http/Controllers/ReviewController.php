<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Review;
use App\Http\Requests\UpdateReviewRequest;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReviewController extends Controller
{

    private $reviewService;

    public function __construct()
    {
        $this->reviewService = app(ReviewService::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index($articleId)
    {
        try {
            $reviews = $this->reviewService->getReviewsByArticleId($articleId);

            if ($reviews->isEmpty()) {
                return ResponseHelper::error('No reviews found for this article', Response::HTTP_NOT_FOUND);
            }

            return ResponseHelper::success('Reviews retrieved successfully', $reviews);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $reviewData = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);

        try {
            $review = $this->reviewService->createReview($reviewData);

            return ResponseHelper::success('Review created successfully', $review, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|numeric|between:0,5',
            'comment' => 'nullable|string|max:255',
        ]);

        try {
            $review = Review::find($id);
            $isUserLoggedIn = $review->user_id === auth()->id();

            if (!$isUserLoggedIn) {
                return ResponseHelper::error('Unauthorized', Response::HTTP_FORBIDDEN);
            }

            if (!$review) {
                return ResponseHelper::error('Review not found', Response::HTTP_NOT_FOUND);
            }

            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return ResponseHelper::success('Review updated successfully', $review);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $review = Review::find($id);
            $isUserLoggedIn = $review->user_id === auth()->id();

            if (!$review) {
                return ResponseHelper::error('Review not found', Response::HTTP_NOT_FOUND);
            } else if (!$isUserLoggedIn) {
                return ResponseHelper::error('Unauthorized', Response::HTTP_FORBIDDEN);
            }

            $review->delete();

            return ResponseHelper::success('Review deleted successfully');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
