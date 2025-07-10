<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->vendor !== null;
    }

    public function view(User $user, Product $product): bool
    {
        return $user->vendor?->id === $product->vendor_id;
    }

    public function create(User $user): bool
    {
        return $user->vendor !== null;
    }

    public function update(User $user, Product $product): bool
    {
        return $user->vendor?->id === $product->vendor_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->vendor?->id === $product->vendor_id;
    }
}
