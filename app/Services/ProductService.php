<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Requests\Api\V1\UpdateProductRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
  public function getProducts(User $user, int $perPage = 10): LengthAwarePaginator
  {
    return $user->vendor->products()->paginate($perPage);
  }

  public function createProduct(StoreProductRequest $request, User $user): Product
  {
    $vendor = $user->vendor;
    return $vendor->products()->create($request->validated());
  }

  public function updateProduct(UpdateProductRequest $request, Product $product): Product
  {
    $product->update($request->validated());
    return $product;
  }
}
