<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vendor;
use App\Http\Requests\Api\V1\StoreVendorRequest;

class VendorService
{
  public function createVendor(StoreVendorRequest $request, User $user): Vendor
  {
    return $user->vendor()->create($request->validated());
  }
}
