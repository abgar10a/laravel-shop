<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'types';

    protected $fillable = [
        'title',
        'identifier',
    ];

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attributes_type_rel', 'type_id', 'attribute_id')->withPivot([]);
    }

    public function hasAttributeWithIdentifier($identifier){
        return $this->attributes()->where('identifier', $identifier)->exists();
    }
}
