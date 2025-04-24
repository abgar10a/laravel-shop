<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Exceptions\OrderAddressMissingException;
use App\Helpers\ResponseHelper;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct()
    {
        $this->orderService = app(OrderService::class);
    }

    /**
     * @OA\Get(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Get orders",
     *     description="Get orders for user",
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
     *         description="Orders for page",
     *
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Orders for page 1"),
     *             @OA\Property(property="error", type="boolean", example="false"),
     *             @OA\Property(property="data",
     *                          type="object",
     *                          @OA\Property(property="page", type="integer", example="1"),
     *                          @OA\Property(property="orders", type="array",
     *                          @OA\Items(type="object",
     *                                    @OA\Property(property="id", type="integer", example="111"),
     *                                    @OA\Property(property="brand", type="string", example="Brand"),
     *                                    @OA\Property(property="name", type="string", example="Model"),
     *                                    @OA\Property(property="status", type="string", example="delivering"),
     *                                    @OA\Property(property="order_date", type="string", example="2025-03-30 03:18:10"),
     *                                   @OA\Property(property="delivery_date", type="string", example="null"),
     *                                   @OA\Property(property="price", type="double", example="33.33"),
     *                                    @OA\Property(property="order_quantity", type="integer", example="4"),
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
     *     ),
     * )
     */
    public function index()
    {
        try {
            $ordersResponse = $this->orderService->getOrdersForUser(auth()->id());

            if (isset($ordersResponse['error'])) {
                return ResponseHelper::error($ordersResponse['error'], Response::HTTP_BAD_REQUEST);
            }

            return ResponseHelper::successData($ordersResponse['message'], $ordersResponse);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     tags={"Orders"},
     *     summary="Post order",
     *     description="Post order",
     *     security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"article_id", "order_quantity"},
     *
     *              @OA\Property(property="article_id", type="integer", example="63"),
     *              @OA\Property(property="order_quantity", type="integer", example="3"),
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
     *                                       @OA\Property(property="id", type="integer", example="111"),
     *                                       @OA\Property(property="brand", type="string", example="Brand"),
     *                                       @OA\Property(property="name", type="string", example="Model"),
     *                                       @OA\Property(property="status", type="string", example="delivering"),
     *                                       @OA\Property(property="order_date", type="string", example="2025-03-30 03:18:10"),
     *                                       @OA\Property(property="delivery_date", type="string", example="null"),
     *                                       @OA\Property(property="price", type="double", example="33.33"),
     *                                       @OA\Property(property="order_quantity", type="integer", example="4"),
     *                                       @OA\Property(property="image", type="string", example="path/to/image"),
     *                                       )
     *                          ),
     *              )
     *         )
     *     ),
     *
     *
     * @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized."),
     *              @OA\Property(property="error", type="boolean", example="true")
     *          )
     *      ),
     *
     * @OA\Response(
     *            response=404,
     *            description="Article not found",
     *
     *            @OA\JsonContent(
     *
     *                @OA\Property(property="message", type="string", example="Article not found."),
     *                @OA\Property(property="error", type="boolean", example="true")
     *            )
     *        ),
     *
     * @OA\Response(
     *          response=500,
     *          description="The selected type id is invalid.",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="The selected type id is invalid.."),
     *              @OA\Property(property="error", type="boolean", example="true")
     *          )
     *      ),
     * )
     */
    public function store(Request $request)
    {
        try {
            if ($request->user()->cannot('create', Order::class)) {
                return ResponseHelper::error("You don't have permission to create orders");
            }

            $orderData = $request->validate([
                'article_id' => 'required|exists:articles,id',
                'order_quantity' => 'required|integer|min:1',
            ]);

            $orderResponse = $this->orderService->createOrder($orderData);

            if (isset($orderResponse['error'])) {
                return ResponseHelper::error($orderResponse['error'], Response::HTTP_NOT_FOUND);
            }

            return ResponseHelper::successData($orderResponse['message'], $orderResponse, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/orders/{orderId}",
     *     tags={"Orders"},
     *     summary="Update order",
     *     description="Update order",
     *     security={{"bearerAuth":{}}},
     *
     *          @OA\Parameter(
     *            name="orderId",
     *            in="path",
     *            description="Order id",
     *            required=false,
     *            @OA\Schema(
     *                type="integer",
     *                default=1,
     *                example=6
     *            )
     *        ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(
     *              required={"status"},
     *
     *              @OA\Property(property="status", type="string", example="delivering"),
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
     *                                       @OA\Property(property="id", type="integer", example="111"),
     *                                       @OA\Property(property="brand", type="string", example="Brand"),
     *                                       @OA\Property(property="name", type="string", example="Model"),
     *                                       @OA\Property(property="status", type="string", example="delivering"),
     *                                       @OA\Property(property="order_date", type="string", example="2025-03-30 03:18:10"),
     *                                       @OA\Property(property="delivery_date", type="string", example="null"),
     *                                       @OA\Property(property="price", type="double", example="33.33"),
     *                                       @OA\Property(property="order_quantity", type="integer", example="4"),
     *                                       @OA\Property(property="image", type="string", example="path/to/image"),
     *                                       )
     *                          ),
     *              )
     *         )
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $orderData = $request->validate([
                'status' => ['required', Rule::in(OrderStatus::cases())]
            ]);

            $orderResponse = $this->orderService->updateOrder($request->user(), $id, $orderData['status']);

            if (isset($orderResponse['error'])) {
                return ResponseHelper::error($orderResponse['error']);
            }

            return ResponseHelper::successData($orderResponse['message'], $orderResponse);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
