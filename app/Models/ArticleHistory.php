<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleHistory extends Model
{
    protected $table = 'article_history';

    protected $fillable = [
        'article_id',
        'user_id',
        'action',
        'order_id',
        'quantity',
        'details',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function article(){
        return $this->belongsTo(Article::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
