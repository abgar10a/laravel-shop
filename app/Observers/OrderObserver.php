<?php

namespace App\Observers;

use App\Enums\ArticleProcessType;
use App\Enums\OrderStatus;
use App\Events\ArticleProcessed;
use App\Jobs\ProcessOrder;
use App\Models\ArticleHistory;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        ArticleHistory::create([
            'article_id' => $order->article_id,
            'user_id' => $order->user_id,
            'action' => ArticleProcessType::ORDERED->value,
            'order_id' => $order->id,
            'quantity' => $order->order_quantity,
        ]);
        event(new ArticleProcessed($order));
        ProcessOrder::dispatch($order);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->getOriginal('status') === OrderStatus::DELIVERING->value && $order->status === OrderStatus::CANCELED->value) {
            ArticleHistory::create([
                'article_id' => $order->article_id,
                'user_id' => $order->user_id,
                'action' => ArticleProcessType::ORDER_CANCELLED->value,
                'order_id' => $order->id,
                'quantity' => $order->order_quantity,
            ]);
            event(new ArticleProcessed($order));
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
