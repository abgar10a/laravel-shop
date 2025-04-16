<?php

namespace App\Listeners;

use App\Events\ArticleProcessed;
use App\Helpers\EmailHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ArticleStatusNotification implements ShouldQueue
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
    public function handle(ArticleProcessed $event): void
    {
        $order = $event->order;
        $user = $order->user();
//        echo "dsdsds";
        EmailHelper::sendEmail($user, 'Order status update', [
            'order_status' => $order->status,
            'article_id' => $order->article_id,
            'url' => url("api/orders/$order->id"),
        ], 'order_status');
    }
}
