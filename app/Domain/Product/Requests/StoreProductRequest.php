<?php

namespace App\Domain\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
//    public function authorize(): bool
//    {
//        return true; // ganti dengan policy kalau ada otorisasi khusus
//    }

    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_name.required' => 'Product name is required',
            'qty.required' => 'Quantity is required',
        ];
    }
}
