<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'article_id',
        'user_id',
        'rating',
        'comment',
    ];

    protected static function booted()
    {
        static::created(function ($review) {
            $review->updateArticleRating($review->article_id);
        });

        static::updated(function ($review) {
            $review->updateArticleRating($review->article_id);
        });

        static::deleted(function ($review) {
            $review->updateArticleRating($review->article_id);
        });
    }

    public function updateArticleRating($articleId)
    {
        $averageRating = Review::where('article_id', $articleId)
            ->avg('rating');

        $article = Article::find($articleId);
        if ($article) {
            $article->rating = round($averageRating, 1);
            $article->save();
        }
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}
