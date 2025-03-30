<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Helpers\ResponseHelper;
use App\Models\Order;
use App\Services\EmailService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Info(
 *     title="Order API",
 *     version="1.0.0",
 *     description="API for managing orders",
 *     @OA\Contact(email="your-email@example.com")
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 */
class OrderController extends Controller
{
    protected $orderService;
    protected $emailService;

    public function __construct()
    {
        $this->orderService = app(OrderService::class);
        $this->emailService = app(EmailService::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $orders = $this->orderService->getOrdersForUser(auth()->id());

            if ($orders->isEmpty()) {
                return ResponseHelper::error('No orders found for user', Response::HTTP_NOT_FOUND);
            }

            return ResponseHelper::successData('Orders retrieved successfully', $orders);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $orderData = $request->validate([
                'article_id' => 'required|exists:articles,id',
                'order_quantity' => 'required|integer|min:1',
            ]);

            $order = $this->orderService->createOrder($orderData);

            $emailData = [
                'order_status' => $order->status,
                'article_id' => $order->article_id,
                'url' => url('/'),
            ];

            $this->emailService->sendEmail(auth()->user(), 'Order status update', $emailData, 'order_status');

            return ResponseHelper::successData('Order created successfully', $order, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return ResponseHelper::successData('Order retrieved successfully', $order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return ResponseHelper::error('Order not found', Response::HTTP_NOT_FOUND);
        } else if ($order->user_id !== auth()->id()) {
            return ResponseHelper::error('Unauthorized', Response::HTTP_FORBIDDEN);
        }

        $order->update([
            'status' => OrderStatus::CANCELED
        ]);

        return ResponseHelper::successData('Order updated successfully', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return ResponseHelper::error('Order not found', Response::HTTP_NOT_FOUND);
            } else if ($order->user_id !== auth()->id()) {
                return ResponseHelper::error('Unauthorized', Response::HTTP_FORBIDDEN);
            }

            $order->delete();

            return ResponseHelper::successData('Order deleted successfully');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
