<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
  public function successResponse(
    mixed $data,
    string $message = '',
    int $code = 200
  ): JsonResponse {
    return response()->json([
      'success' => true,
      'message' => $message,
      'data' => $data
    ], $code);
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
