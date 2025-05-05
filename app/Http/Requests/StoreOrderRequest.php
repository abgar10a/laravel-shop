<?php

namespace App\Http\Requests;

use App\Enums\UserTypes;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();

        return $user->user_type === UserTypes::INDIVIDUAL->value;
    }

    public function rules(): array
    {
        return [
            'article_id' => 'required|exists:articles,id',
            'order_quantity' => 'required|integer|min:1',
        ];
    }
}
