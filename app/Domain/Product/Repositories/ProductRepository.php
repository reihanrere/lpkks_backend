<?php

namespace App\Domain\Product\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function paginate($page, $size, $search)
    {
        return Product::orderBy('id', 'desc')
            ->where('product_name', 'like', '%' . $search . '%')
            ->paginate(
                $size,
                ['*'],
                'page',
                $page
            );
    }

    public function findById($id)
    {
        return Product::find($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = Product::find($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        $product = Product::find($id);
        return $product->delete();
    }
}
