<?php

namespace App\Models;

use App\Models\Relations\ArticleImageRel;
use App\Models\Relations\AttributeArticleRel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'brand',
        'name',
        'quantity',
        'type_id',
        'price',
        'user_id',
        'rating'
    ];

    public function user(): User
    {
        return $this->belongsTo(User::class)->first();
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'article_color');
    }

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

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function attributes()
    {
        return $this->hasManyThrough(
            Attribute::class,
            AttributeArticleRel::class,
            'article_id',
            'id',
            'id',
            'attribute_id'
        )->select(['attributes.id', 'attributes.identifier', 'attributes.title', 'attributes_article_rel.value']);
    }

    public function reviewsWithUser()
    {
        return $this->reviews->map(function ($review) {
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

    public function scopePrice($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeInStock($query, $quantity = 0)
    {
        return $query->where('quantity', '>', max($quantity - 1, 0));
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(CONCAT(brand, " ", name)) LIKE ?', ['%' . strtolower($search) . '%']);
        });
    }

    public function searchableAs()
    {
        return 'articles';
    }

    public function toSearchableArray(): array
    {
        return array_merge($this->toArray(), [
            'id' => (string)$this->id,
            'created_at' => $this->created_at->timestamp,
        ]);
    }
}
