<?php

namespace App\Models\Relations;

use App\Models\Attribute;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeTypeRel extends Model
{
    use HasFactory;

    protected $table = 'attributes_type_rel';

    protected $fillable = [
        'type_id',
        'attribute_id',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }
}
