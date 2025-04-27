<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\Attribute;
use App\Models\Relations\AttributeArticleRel;
use App\Models\Relations\AttributeTypeRel;
use App\Models\Type;

class AttributeService
{
    public function getAttributesByType($typeIdentifier)
    {
        $type = Type::where('identifier', $typeIdentifier)->first();

        if ($type) {
            $attributes = $type->attributes->makeHidden('pivot');

            return ResponseHelper::build('Attributes retrieved successfully', ['attributes' => $attributes]);
        } else {
            return ResponseHelper::build(error: 'Type not found');
        }
    }

    public function createAttribute($attrData)
    {
        $existing = Attribute::where('identifier', $attrData['identifier'])->first();
        if ($existing) {
            return ResponseHelper::build(error: 'Attribute already exists');
        } else {
            $type = Type::where('identifier', $attrData['type'])->first();
            if ($type) {
                unset($attrData['type']);
                $attribute = Attribute::create($attrData);
                AttributeTypeRel::create([
                    'attribute_id' => $attribute->id,
                    'type_id' => $type->id,
                ]);

                return ResponseHelper::build('Attribute created successfully', ['attribute' => $attribute]);
            } else {
                return ResponseHelper::build(error: 'Type not found');
            }
        }
    }

    public function updateAttribute($attrData)
    {
        $existing = Attribute::find($attrData['id']);
        if ($existing) {
            unset($attrData['id']);
            $existing->update($attrData);

            return ResponseHelper::build('Attribute updated successfully', ['attribute' => $existing]);
        } else {
            return ResponseHelper::build(error: 'Attribute not found');
        }
    }

    public function deleteAttribute($id)
    {
        $attribute = Attribute::find($id);

        if ($attribute) {
            AttributeTypeRel::where('attribute_id', $attribute->id)->delete();
            AttributeArticleRel::where('attribute_id', $attribute->id)->delete();
            $attribute->delete();
            return ResponseHelper::build('Attribute deleted successfully');
        } else {
            return ResponseHelper::build(error: 'Attribute not found');
        }
    }
}
