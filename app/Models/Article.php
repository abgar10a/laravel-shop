<?php

namespace App\Models;

use App\Models\Relations\ArticleImageRel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;

    protected $fillable = [
        'brand',
        'name',
        'quantity',
        'type_id',
        'price',
        'user_id',
        'color_id',
        'rating'
    ];

    public function images()
    {
        return $this->hasManyThrough(
            Upload::class,
            ArticleImageRel::class,
            'article_id',
            'id',
            'id',
            'upload_id'
        )->select(['uploads.id', 'uploads.path', 'article_image_rel.sequence'])
            ->orderBy('article_image_rel.sequence');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'article_id', 'id');
    }

    public function reviewsWithUser(){
        return $this->reviews->map(function ($review){
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'user' => [
                    'id' => $review->user_id,
                    'name' => User::find($review->user_id)->name
                ],
            ];
        });
    }
}
