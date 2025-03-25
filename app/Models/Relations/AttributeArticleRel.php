<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeArticleRel extends Model
{
    use HasFactory;

    protected $table = 'attributes_article_rel';

    protected $fillable = [
        'article_id',
        'attribute_id',
        'value'
    ];
}
