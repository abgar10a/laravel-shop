<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeTypeRel extends Model
{
    use HasFactory;

    protected $table = 'attributes_type_rel';

    protected $fillable = [
        'type_id',
        'attibute_id',
    ];
}
