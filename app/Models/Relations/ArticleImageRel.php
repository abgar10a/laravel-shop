<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleImageRel extends Model
{
    /** @use HasFactory<\Database\Factories\ArticleImageRelFactory> */
    use HasFactory;

    protected $table = 'article_image_rel';

    protected $fillable = [
        'article_id',
        'upload_id',
        'sequence',
    ];
}
