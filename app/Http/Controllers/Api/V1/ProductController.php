<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Traits\ApiResponse;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Requests\Api\V1\UpdateProductRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    public function __construct(protected ProductService $productService) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Product::class);
        $products = $this->productService->getProducts(auth()->user());

        return $this->successResponse(
            data: ProductResource::collection($products),
            message: 'Products retrieved successfully.',
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request, $request->user());

        return $this->successResponse(
            data: new ProductResource($product),
            message: 'Product created successfully.',
            code: 201
        );
    }

    public function show(Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        return $this->successResponse(
            data: new ProductResource($product->load('vendor')),
            message: 'Product retrieved successfully.'
        );
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);
        $updatedProduct = $this->productService->updateProduct($request, $product);

        return $this->successResponse(
            data: new ProductResource($updatedProduct),
            message: 'Product updated successfully.'
        );
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);
        $product->delete();

        return $this->successResponse(
            data: null,
            message: 'Product deleted successfully.'
        );
    }
}
