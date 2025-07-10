<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\V1\LoginRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Api\V1\RegisterUserRequest;

class AuthService
{
  public function registerUser(RegisterUserRequest $request): array
  {
    $user = User::create($request->validated());
    $token = $user->createToken('api-token')->plainTextToken;

    return ['user' => $user, 'token' => $token];
  }

  public function loginUser(LoginRequest $request): array
  {
    $credentials = $request->validated();

    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['The provided credentials do not match our records.'],
      ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return ['user' => $user, 'token' => $token];
  }
}
