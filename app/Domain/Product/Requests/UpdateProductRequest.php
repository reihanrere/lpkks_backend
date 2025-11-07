<?php

namespace App\Domain\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_name' => 'sometimes|string|max:255',
            'qty' => 'sometimes|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.string' => 'Product name must be a valid string',
            'qty.integer' => 'Quantity must be a valid integer',
        ];
    }
}
