<?php

namespace App\Domain\Product\Services;

use AllowDynamicProperties;
use App\Domain\Product\Repositories\ProductRepository;

class ProductService
{
    public function __construct(
        protected ProductRepository $repo
    ) {}

    public function getProducts($page, $size, $search)
    {
        return $this->repo->paginate($page, $size, $search);
    }

    public function getProduct($id)
    {
        return $this->repo->findById($id);
    }

    public function createProduct(array $data)
    {
        return $this->repo->create($data);
    }

    public function updateProduct($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->repo->delete($id);
    }

}
