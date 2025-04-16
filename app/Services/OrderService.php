<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Events\ArticleProcessed;
use App\Helpers\EmailHelper;
use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Models\Order;

class OrderService
{

    public function getOrdersForUser($userId)
    {
        $orders = Order::getOrdersByUserId($userId);

        if ($orders['total_items'] === 0) {
            return ResponseHelper::build(error: "You don't have any orders");
        }

        return ResponseHelper::build('Orders for page ' . $orders['current_page'], $orders);
    }

    public function createOrder($orderData)
    {
        if ($article = Article::find($orderData['article_id'])) {
            $orderData['user_id'] = auth()->id();
            $orderData['price'] = $orderData['order_quantity'] * $article->price;
            $orderData['status'] = OrderStatus::DELIVERING;
            $orderData['order_date'] = now();
            $order = Order::create($orderData);

//            $this->notifyOrderUpdate(auth()->user(), OrderStatus::DELIVERING, $order->article_id, '/');
            $this->notifyOrderUpdate($order);

            return ResponseHelper::build('Order created successfully', ['order' => $order]);
        }

        return ResponseHelper::build(error: "Article not found");
    }

    public function updateOrder($user, $orderId, $status)
    {
        $order = Order::find($orderId);

        if ($user->cannot('update', $order)) {
            return ResponseHelper::build(error: "You don't have permission to update order");
        } else if (!$order) {
            return ResponseHelper::build(error: 'Order not found');
        }

        $order->update([
            'status' => $status
        ]);

        $this->notifyOrderUpdate($order);

        return ResponseHelper::build('Order updated successfully', ['order' => $order]);
    }

    public function notifyOrderUpdate($order)
    {
        return event(new ArticleProcessed($order));
    }
}
