<?php

namespace App\Domain\Product\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->product_name,
            'qty'   => $this->qty,
            'created_at' => $this->created_at,
        ];
    }
}
