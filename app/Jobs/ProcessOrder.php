<?php

namespace App\Jobs;

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
    }
}
