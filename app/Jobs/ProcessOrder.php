<?php

namespace App\Jobs;

use App\Events\ArticleProcessed;
use App\Events\OrderPrepare;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order) {
        $this->order = $order;
    }

    public function handle(): void
    {
        logger('Inside job: ProcessOrder for order ID ' . $this->order->id);
        event(new OrderPrepare($this->order));

//        $order = $this->order;
//        $article = $order->article();
//        $user = $article->seller();
//        EmailHelper::sendEmail($user, 'New order', [
//            'article_id' => $order->article_id,
//            'model' => $article->brand . ' ' . $article->model,
//            'quantity' => $order->order_quantity,
//            'address' => $order->user()->address,
//            'url' => url("api/orders/$order->id"),
//        ], 'order_seller');
    }
}
