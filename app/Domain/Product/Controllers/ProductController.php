<?php

namespace App\Domain\Product\Controllers;

use App\Domain\Product\Resources\ProductResource;
use App\Domain\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ProductService $service
    ) {}

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get paginated product list",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="select pages",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *           name="size",
     *           in="query",
     *           description="Item per page",
     *           required=false,
     *           @OA\Schema(type="integer")
     *       ),
     *     @OA\Parameter(
     *          name="search",
     *          in="query",
     *          description="Search product",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Product list"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $size = $request->query('size', 10);
        $search = $request->query('search', '');

        $products = $this->service->getProducts($page, $size, $search);

        return $this->success([
            'data' => ProductResource::collection($products),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ], "Product list");
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Get product by id",
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function show($id): JsonResponse
    {
        $product = $this->service->getProduct($id);

        if (!$product) {
            return $this->error("Product not found", null,  404);
        }

        return $this->success(new ProductResource($product));
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Create new product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_name", "qty"},
     *             @OA\Property(property="product_name", type="string", example="iPhone 17 Pro Max"),
     *             @OA\Property(property="qty", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_name' => 'required|string',
            'qty' => 'required|integer'
        ]);

        $product = $this->service->createProduct($data);

        return $this->success(new ProductResource($product), "Product created");
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Update product data",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="product_name", type="string", example="Samsung S30 Ultra"),
     *             @OA\Property(property="qty", type="integer", example=50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->validate([
            'product_name' => 'sometimes|string',
            'qty' => 'sometimes|integer'
        ]);

        $is_exist = $this->service->getProduct($id);
        if (!$is_exist) {
            return $this->error("Product not found", null,  404);
        }

        $product = $this->service->updateProduct($id, $data);

        return $this->success(new ProductResource($product), "Product updated");
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Delete product by id",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $is_exist = $this->service->getProduct($id);
        if (!$is_exist) {
            return $this->error("Product not found", null,  404);
        }

        $this->service->deleteProduct($id);

        return $this->success([], "Product deleted");
    }


}
