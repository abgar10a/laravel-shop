<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_id',
        'status',
        'order_quantity',
        'order_date',
        'delivery_date',
        'price'
    ];

    public function article(): Article
    {
        return $this->belongsTo(Article::class)->first();
    }

    public function user(): User
    {
        return $this->belongsTo(User::class)->first();
    }

    public static function getOrdersByUserId($userId)
    {
        $ordersQuery = Order::select('id', 'article_id', 'status', 'price', 'order_quantity', 'order_date', 'delivery_date')
            ->where('user_id', $userId)
            ->orderBy('order_date', 'desc')
            ->paginate(10);

        $totalCount = $ordersQuery->total();

        $orders = $ordersQuery->through(function ($order) {
            $article = Article::find($order->article_id);
            $image = $article->images->first();

            return [
                'id' => $order->id,
                'status' => $order->status,
                'price' => $order->price,
                'order_quantity' => $order->order_quantity,
                'order_date' => $order->order_date,
                'delivery_date' => $order->delivery_date,
                'brand' => $article->brand,
                'name' => $article->name,
                'image' => $image ? asset('storage/' . $image->path) : null,
            ];
        })->toArray();

        return [
            'current_page' => $ordersQuery->currentPage(),
            'total_items' => $totalCount,
            'orders' => $orders['data'],
        ];
    }

    public static function getOrderedArticleQuantity($articleId)
    {
        $order = Order::where('article_id', $articleId);
        if ($order) {
            return $order->sum('order_quantity');
        } else return 0;
    }
}
