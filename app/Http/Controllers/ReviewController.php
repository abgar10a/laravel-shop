<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
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
     * @OA\Get(
     *     path="/reviews/{articleId}",
     *     tags={"Reviews"},
     *     summary="Get reviews",
     *     description="Get reviews for article",
     *     security={},
     *
     *         @OA\Parameter(
     *             name="articleId",
     *             in="path",
     *             description="Article id",
     *             required=false,
     *             @OA\Schema(
     *                 type="integer",
     *                 default=1,
     *                 example=6
     *             )
     *         ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Reviews for article",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Reviews retrieved successfully"),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="reviews", type="array",
     *                          @OA\Items(type="object",
     *                                    @OA\Property(property="id", type="string", example="111"),
     *                                    @OA\Property(property="rating", type="integer", example="4"),
     *                                    @OA\Property(property="comment", type="string", example="nice article"),
     *                                   @OA\Property(property="user",
     *                                                  type="object",
     *                                                @OA\Property(property="id", type="string", example="111"),
     *                                                @OA\Property(property="name", type="string", example="Marcus"),
     *                                                  ),
     *                                   )
     *                          ),
     *              )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthorized."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     ),
     *
     *      @OA\Response(
     *           response=404,
     *           description="Article not found",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="message", type="string", example="Article not found."),
     *               @OA\Property(property="error", type="boolean", example="true")
     *           )
     *      ),
     *
     *      @OA\Response(
     *           response=500,
     *           description="Something went wrong",
     *
     *           @OA\JsonContent(
     *
     *                @OA\Property(property="message", type="string", example="Something went wrong."),
     *                @OA\Property(property="error", type="boolean", example="true")
     *            )
     *       ),
     * )
     */
    public function index($articleId)
    {
        try {
            $reviewResponse = $this->reviewService->getReviewsByArticleId($articleId);

            if (isset($reviewResponse['error'])) {
                return ResponseHelper::error($reviewResponse['error'], Response::HTTP_NOT_FOUND);
            }

            return ResponseHelper::successData($reviewResponse['message'], $reviewResponse);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/reviews",
     *     tags={"Reviews"},
     *     summary="Post review",
     *     description="Post review",
     *     security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"article_id", "rating", "comment"},
     *
     *              @OA\Property(property="article_id", type="integer", example="63"),
     *              @OA\Property(property="rating", type="integer", example="3"),
     *              @OA\Property(property="comment", type="string", example="nice"),
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Order created successfully."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="order", type="object",
     *                                       @OA\Property(property="id", type="string", example="111"),
     *                                       @OA\Property(property="rating", type="integer", example="3"),
     *                                       @OA\Property(property="comment", type="string", example="nice"),
     *                                       @OA\Property(property="user_id", type="string", example="1"),
     *                                       @OA\Property(property="article_id", type="double", example="33.33"),
     *                                       @OA\Property(property="order_date", type="string", example="2025-03-30 03:18:10"),
     *                                       @OA\Property(property="delivery_date", type="string", example="null"),
     *                                       )
     *                          ),
     *              )
     *         )
     *     ),
     *
     * )
     */
    public function store(Request $request)
    {
        $reviewData = $request->validate([
            'article_id' => 'required|exists:articles,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);

        try {
            $reviewResponse = $this->reviewService->createReview($reviewData, $request->user());

            if (isset($reviewResponse['error'])) {
                return ResponseHelper::error($reviewResponse['error'], Response::HTTP_NOT_FOUND);
            }

            return ResponseHelper::successData($reviewResponse['message'], $reviewResponse, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/reviews/{reviewId}",
     *     tags={"Reviews"},
     *     summary="Update review",
     *     description="Update review",
     *     security={{"bearerAuth":{}}},
     *
     *       @OA\Parameter(
     *           name="reviewId",
     *           in="path",
     *           description="Review id",
     *           required=false,
     *           @OA\Schema(
     *               type="integer",
     *               default=1,
     *               example=6
     *           )
     *       ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"rating", "comment"},
     *
     *              @OA\Property(property="rating", type="integer", example="3"),
     *              @OA\Property(property="comment", type="string", example="nice"),
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Order updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Order updated successfully."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="order", type="object",
     *                                       @OA\Property(property="id", type="string", example="111"),
     *                                       @OA\Property(property="rating", type="integer", example="3"),
     *                                       @OA\Property(property="comment", type="string", example="nice"),
     *                                       @OA\Property(property="user_id", type="string", example="1"),
     *                                       @OA\Property(property="article_id", type="double", example="33.33"),
     *                                       @OA\Property(property="order_date", type="string", example="2025-03-30 03:18:10"),
     *                                       @OA\Property(property="delivery_date", type="string", example="null"),
     *                                       )
     *                          ),
     *              )
     *         )
     *     ),
     *
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $reviewData = $request->validate([
                'rating' => 'required|numeric|between:0,5',
                'comment' => 'required|string|max:255',
            ]);

            $reviewResponse = $this->reviewService->updateReview($id, $reviewData, $request->user());

            if (isset($reviewResponse['error'])) {
                return ResponseHelper::error($reviewResponse['error'], Response::HTTP_NOT_FOUND);
            }

            return ResponseHelper::successData('Review updated successfully', $reviewResponse, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete (
     *     path="/reviews/{reviewId}",
     *     tags={"Reviews"},
     *     summary="Delete review",
     *     description="Delete review",
     *     security={{"bearerAuth":{}}},
     *
     *       @OA\Parameter(
     *           name="reviewId",
     *           in="path",
     *           description="Review id",
     *           required=false,
     *           @OA\Schema(
     *               type="integer",
     *               default=1,
     *               example=6
     *           )
     *       ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Order updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Order updated successfully."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *         )
     *     ),
     *
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            if (!$id) {
                return ResponseHelper::error('Review id is required', Response::HTTP_BAD_REQUEST);
            }

            $reviewResponse = $this->reviewService->deleteReview($id, $request->user());

            if ($reviewResponse['error']) {
                return ResponseHelper::error($reviewResponse['error'], Response::HTTP_NOT_FOUND);
            }

            return ResponseHelper::success('Review deleted successfully');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
