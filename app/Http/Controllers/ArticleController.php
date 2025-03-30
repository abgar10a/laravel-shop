<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
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

    /**
     * @OA\Get(
     *     path="/articles",
     *     tags={"Article"},
     *     summary="Get articles",
     *     description="Get articles",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *          name="page",
     *          in="query",
     *          description="The page number for pagination",
     *          required=false,
     *          @OA\Schema(
     *              type="integer",
     *              default=1,
     *              example=6
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Articles for page",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Articles for page 1"),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="page", type="integer", example="1"),
     *                          @OA\Property(property="articles", type="array",
     *                          @OA\Items(type="object",
     *                                    @OA\Property(property="id", type="integer", example="111"),
     *                                    @OA\Property(property="brand", type="string", example="Brand"),
     *                                    @OA\Property(property="name", type="string", example="Model name"),
     *                                    @OA\Property(property="price", type="double", example="33.33"),
     *                                    @OA\Property(property="rating", type="double", example="4.5"),
     *                                    @OA\Property(property="image", type="string", example="path/to/image"),
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
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $page = (int)$request->has('page') ?: 0;
            $articleResponse = $this->articleService->getArticles($page);

            if (isset($articleResponse['error'])) {
                return ResponseHelper::error($articleResponse['error']);
            }

            return ResponseHelper::successData($articleResponse['message'], $articleResponse);
        } catch (\Throwable $th) {
            return ResponseHelper::error('Something went wrong');
        }
    }

    /**
     * @OA\Post(
     *     path="/articles",
     *     tags={"Article"},
     *     summary="Post article",
     *     description="Post article",
     *     security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"brand", "name", "quantity", "type_id", "price", "user_id", "color_id", "rating"},
     *
     *              @OA\Property(property="brand", type="string", example="Brand"),
     *              @OA\Property(property="name", type="string", example="model"),
     *              @OA\Property(property="quantity", type="integer", example="63"),
     *              @OA\Property(property="type_id", type="integer", example="3"),
     *              @OA\Property(property="price", type="double", example="33.33"),
     *              @OA\Property(property="user_id", type="string", example="5555"),
     *              @OA\Property(property="color_id", type="integer", example="5"),
     *              @OA\Property(property="images", type="array",
     *                           @OA\Items(type="string", format="binary", description="The image file to upload")
     *              )
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article saved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Article saved successfully."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="article", type="object",
     *                                       @OA\Property(property="id", type="string", example="98918"),
     *                                       @OA\Property(property="brand", type="string", example="Brand"),
     *                                       @OA\Property(property="name", type="string", example="model"),
     *                                       @OA\Property(property="quantity", type="integer", example="63"),
     *                                       @OA\Property(property="type_id", type="integer", example="3"),
     *                                       @OA\Property(property="price", type="double", example="33.33"),
     *                                       @OA\Property(property="user_id", type="integer", example="5555"),
     *                                       @OA\Property(property="color_id", type="integer", example="5"),
     *                                       @OA\Property(property="images", type="array",
     *                                                   @OA\Items(type="object",
     *                                                             @OA\Property(property="id", type="integer", example="111"),
     *                                                             @OA\Property(property="path", type="string", example="image/path"),
     *                                                             @OA\Property(property="sequence", type="integer", example="1"),
     *                                                            )
     *                                                   ),
     *                          ),
     *              )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="The selected type id is invalid.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="The selected type id is invalid.."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     ),
     *
     *     @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="message", type="string", example="Unauthorized."),
     *               @OA\Property(property="error", type="boolean", example="true")
     *           )
     *       )
     * )
     */
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

            $articleResponse = $this->articleService->createArticle($articleData, $images);

            if (isset($articleResponse['error'])) {
                return ResponseHelper::error($articleResponse['error']);
            }

            return ResponseHelper::successData('Article saved successfully', $articleResponse, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * @OA\Get(
     *     path="/articles/{articleId}",
     *     tags={"Article"},
     *     summary="Get article",
     *     description="Get article",
     *     security={{"bearerAuth":{}}},
     *
     *          @OA\Parameter(
     *           name="articleId",
     *           in="path",
     *           description="Article id",
     *           required=false,
     *           @OA\Schema(
     *               type="integer",
     *               default=1,
     *               example=6
     *           )
     *       ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article successfully retrieved",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Article successfully retrieved."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="article", type="object",
     *                                       @OA\Property(property="id", type="string", example="98918"),
     *                                       @OA\Property(property="brand", type="string", example="Brand"),
     *                                       @OA\Property(property="name", type="string", example="model"),
     *                                       @OA\Property(property="quantity", type="integer", example="63"),
     *                                       @OA\Property(property="type_id", type="integer", example="3"),
     *                                       @OA\Property(property="price", type="double", example="33.33"),
     *                                       @OA\Property(property="user_id", type="integer", example="5555"),
     *                                       @OA\Property(property="color_id", type="integer", example="5"),
     *                                       @OA\Property(property="rating", type="integer", example="4.4"),
     *                                       @OA\Property(property="images", type="array",
     *                                                   @OA\Items(type="object",
     *                                                             @OA\Property(property="id", type="integer", example="111"),
     *                                                             @OA\Property(property="path", type="string", example="image/path"),
     *                                                             @OA\Property(property="sequence", type="integer", example="1"),
     *                                                            )
     *                                                   ),
     *                                      @OA\Property(property="reviews", type="array",
     *                                                   @OA\Items(type="object",
     *                                                             @OA\Property(property="id", type="integer", example="111"),
     *                                                             @OA\Property(property="rating", type="integer", example="4"),
     *                                                             @OA\Property(property="comment", type="string", example="commentt"),
     *                                                            @OA\Property(property="user", type="object",
     *                                                                         @OA\Property(property="id", type="integer", example="111"),
     *                                                                         @OA\Property(property="name", type="string", example="Ernesto"),
     *                                                                        )
     *                                                            )
     *                                                   ),
     *                          ),
     *              )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Article not found.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Article not found."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     ),
     *
     *     @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="message", type="string", example="Unauthorized."),
     *               @OA\Property(property="error", type="boolean", example="true")
     *           )
     *       )
     * )
     */
    public function show($id)
    {
        try {
            $articleResponse = $this->articleService->getArticleById($id);

            if (isset($articleResponse['error'])) {
                return ResponseHelper::error($articleResponse['error']);
            }

            return ResponseHelper::successData($articleResponse['message'], $articleResponse);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/articles/{articleId}",
     *     tags={"Article"},
     *     summary="Update article",
     *     description="Update article",
     *     security={{"bearerAuth":{}}},
     *
     *          @OA\Parameter(
     *           name="articleId",
     *           in="path",
     *           description="Article id",
     *           required=false,
     *           @OA\Schema(
     *               type="integer",
     *               default=1,
     *               example=6
     *           )
     *       ),
     *
     *           @OA\RequestBody(
     *           required=true,
     *
     *           @OA\JsonContent(
     *               nullable={"brand", "name", "quantity", "type_id", "price", "user_id", "color_id", "rating"},
     *
     *               @OA\Property(property="brand", type="string", example="Brand"),
     *               @OA\Property(property="name", type="string", example="model"),
     *               @OA\Property(property="quantity", type="integer", example="63"),
     *               @OA\Property(property="type_id", type="integer", example="3"),
     *               @OA\Property(property="price", type="double", example="33.33"),
     *               @OA\Property(property="user_id", type="string", example="5555"),
     *               @OA\Property(property="color_id", type="integer", example="5"),
     *               @OA\Property(property="images", type="array",
     *                            @OA\Items(type="string", format="binary", description="The image file to upload")
     *               ),
     *               @OA\Property(property="remove_images", type="array",
     *                             @OA\Items(type="integer", example="3")
     *                )
     *           )
     *       ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Article updated successfully."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="article", type="object",
     *                                       @OA\Property(property="id", type="string", example="98918"),
     *                                       @OA\Property(property="brand", type="string", example="Brand"),
     *                                       @OA\Property(property="name", type="string", example="model"),
     *                                       @OA\Property(property="quantity", type="integer", example="63"),
     *                                       @OA\Property(property="type_id", type="integer", example="3"),
     *                                       @OA\Property(property="price", type="double", example="33.33"),
     *                                       @OA\Property(property="user_id", type="integer", example="5555"),
     *                                       @OA\Property(property="color_id", type="integer", example="5"),
     *                                       @OA\Property(property="rating", type="integer", example="4.4"),
     *                                       @OA\Property(property="images", type="array",
     *                                                   @OA\Items(type="object",
     *                                                             @OA\Property(property="id", type="integer", example="111"),
     *                                                             @OA\Property(property="path", type="string", example="image/path"),
     *                                                             @OA\Property(property="sequence", type="integer", example="1"),
     *                                                            )
     *                                                   ),
     *                                      @OA\Property(property="reviews", type="array",
     *                                                   @OA\Items(type="object",
     *                                                             @OA\Property(property="id", type="integer", example="111"),
     *                                                             @OA\Property(property="rating", type="integer", example="4"),
     *                                                             @OA\Property(property="comment", type="string", example="commentt"),
     *                                                            @OA\Property(property="user", type="object",
     *                                                                         @OA\Property(property="id", type="integer", example="111"),
     *                                                                         @OA\Property(property="name", type="string", example="Ernesto"),
     *                                                                        )
     *                                                            )
     *                                                   ),
     *                          ),
     *              )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Article not found.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Article not found."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     ),
     *
     *     @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="message", type="string", example="Unauthorized."),
     *               @OA\Property(property="error", type="boolean", example="true")
     *           )
     *       )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            if (!$id) {
                return ResponseHelper::error('Wrong parameter', Response::HTTP_BAD_REQUEST);
            }

            $articleData = $request->validate([
                'brand' => 'nullable|string|max:255',
                'name' => 'nullable|string|max:255',
                'quantity' => 'nullable|numeric|min:0',
                'type_id' => 'nullable|exists:types,id',
                'price' => 'nullable|numeric|min:0',
                'color_id' => 'nullable|exists:colors,id',
                'remove_images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $images = $request->hasFile('images') ? $request->file('images') : [];

            $articleResponse = $this->articleService->updateArticle($id, $articleData, $images, $request->user());

            if (isset($articleResponse['error'])) {
                return ResponseHelper::error($articleResponse['error']);
            }

            return ResponseHelper::successData('Article updated successfully', $articleResponse, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete (
     *     path="/articles/{articleId}",
     *     tags={"Article"},
     *     summary="Delete article",
     *     description="Delete article",
     *     security={{"bearerAuth":{}}},
     *
     *          @OA\Parameter(
     *           name="articleId",
     *           in="path",
     *           description="Article id",
     *           required=false,
     *           @OA\Schema(
     *               type="integer",
     *               default=1,
     *               example=6
     *           )
     *       ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Article deleted successfully."),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Article not found.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Article not found."),
     *             @OA\Property(property="error", type="boolean", example="true")
     *         )
     *     ),
     *
     *     @OA\Response(
     *           response=401,
     *           description="Unauthorized",
     *
     *           @OA\JsonContent(
     *
     *               @OA\Property(property="message", type="string", example="Unauthorized."),
     *               @OA\Property(property="error", type="boolean", example="true")
     *           )
     *       )
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            if (!$id) {
                return ResponseHelper::error('Article id is required', Response::HTTP_BAD_REQUEST);
            }

            $deletedResponse = $this->articleService->deleteArticle($id, $request->user());

            if ($deletedResponse['error']) {
                return ResponseHelper::error($deletedResponse['error']);
            }

            return ResponseHelper::successData($deletedResponse['message']);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/articles/top-articles",
     *     tags={"Article"},
     *     summary="Get top articles",
     *     description="Get top articles",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Top articles",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Top articles"),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="articles", type="array",
     *                          @OA\Items(type="object",
     *                                    @OA\Property(property="id", type="integer", example="111"),
     *                                    @OA\Property(property="brand", type="string", example="Brand"),
     *                                    @OA\Property(property="name", type="string", example="Model name"),
     *                                    @OA\Property(property="price", type="double", example="33.33"),
     *                                    @OA\Property(property="rating", type="double", example="4.5"),
     *                                    @OA\Property(property="image", type="string", example="path/to/image"),
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
     *     )
     * )
     */
    public function getTopArticles()
    {
        try {
            $articleResponse = $this->articleService->getTopArticles();

            if (isset($articles['error'])) {
                return ResponseHelper::error($articles['error']);
            }

            return ResponseHelper::successData($articleResponse['message'], $articleResponse);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
