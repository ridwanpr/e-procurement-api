<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterUserRequest;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(protected AuthService $authService) {}

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = $this->authService->registerUser($request);
        return $this->successResponse(
            data: [
                'user' => new UserResource($user['user']),
                'token' => $user['token']
            ],
            message: 'User registered successfully',
            code: 201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->loginUser($request);
        return $this->successResponse(
            data: [
                'user' => new UserResource($data['user']),
                'token' => $data['token']
            ],
            message: 'User logged in successfully'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            message: 'User logged out successfully',
        );
    }
}
