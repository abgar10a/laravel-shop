<?php

namespace App\Listeners;

use App\Events\OrderPrepare;
use App\Helpers\EmailHelper;
use http\Client\Curl\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrderToSellerNotification implements ShouldQueue
{

    public $connection = 'sync';
    public $queue = 'default';
    public $delay = null;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPrepare $event): void
    {
        $order = $event->order;
        $article = $order->article();
        $user = $order->user();
        $seller = $article->user();
        EmailHelper::sendEmail($seller, 'New order', [
            'article_id' => $order->article_id,
            'model' => $article->brand . ' ' . $article->name,
            'quantity' => $order->order_quantity,
            'address' => $user->city . ' ' . $user->address . ' ' . $user->postal_code,
            'url' => url("api/orders/$order->id"),
        ], 'order_seller');
    }
}
