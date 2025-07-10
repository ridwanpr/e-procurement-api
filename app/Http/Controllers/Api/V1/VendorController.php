<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreVendorRequest;
use App\Http\Resources\VendorResource;
use App\Services\VendorService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class VendorController extends Controller
{
    use ApiResponse;

    protected $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function store(StoreVendorRequest $request): JsonResponse
    {
        $vendor = $this->vendorService->createVendor($request, $request->user());

        return $this->successResponse(
            message: 'Vendor registered successfully',
            data: new VendorResource($vendor),
            code: 201
        );
    }
}
