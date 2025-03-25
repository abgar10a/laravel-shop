<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Article;
use App\Models\Order;

class OrderService
{

    public function getOrdersForUser($userId)
    {
        $orders = Order::getOrdersByUserId($userId);
        return $orders;
    }

    public function createOrder($orderData)
    {
        if ($article = Article::find($orderData['article_id'])) {
            $orderData['user_id'] = auth()->id();
            $orderData['price'] = $orderData['order_quantity'] * $article->price;
            $orderData['status'] = OrderStatus::DELIVERING;
            $orderData['order_date'] = now();

            return Order::create($orderData);
        }

        return [];
    }

}
