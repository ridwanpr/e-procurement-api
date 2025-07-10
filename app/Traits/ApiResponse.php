<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
  public function successResponse(
    mixed $data,
    string $message = '',
    int $code = 200
  ): JsonResponse {
    $response_data = [
      'success' => true,
      'message' => $message,
    ];

    if ($data instanceof ResourceCollection && $data->resource instanceof \Illuminate\Pagination\LengthAwarePaginator) {
      $paginatedData = $data->response()->getData(true);
      $response_data = array_merge($response_data, $paginatedData);
    } elseif ($data) {
      $response_data['data'] = $data;
    }

    return response()->json($response_data, $code);
  }

  public function errorResponse(
    string $message,
    int $code = 400
  ): JsonResponse {
    return response()->json([
      'success' => false,
      'message' => $message
    ], $code);
  }
}
