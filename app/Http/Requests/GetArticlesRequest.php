<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetArticlesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'perPage' => ['nullable', 'numeric', 'between:1,50'],

            'search' => ['nullable', 'string', 'max:255'],

            'filter' => ['nullable', 'array'],
            'filter.type' => ['nullable', 'string', 'max:255', 'exists:types,identifier'],
            'filter.brand' => ['nullable', 'string', 'max:255'],
            'filter.price_min' => ['nullable', 'numeric', 'min:0'],
            'filter.price_max' => ['nullable', 'numeric', 'min:0'],

            'order' => ['nullable', 'array'],
            'order.by' => ['nullable', 'in:name,price,created_at'],
            'order.direction' => ['nullable', 'in:asc,desc'],
        ];
    }
}
