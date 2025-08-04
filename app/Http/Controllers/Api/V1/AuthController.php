<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\Api\V1\AuthResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $authData = [
                'user' => $user,
                'token' => $token,
            ];

            return $this->resourceResponse(
                new AuthResource($authData),
                'User registered successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error registering user');
        }
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->unauthorizedResponse('Invalid credentials provided.');
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            $authData = [
                'user' => $user,
                'token' => $token,
            ];

            return $this->resourceResponse(
                new AuthResource($authData),
                'Login successful'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error during login');
        }
    }

    /**
     * Logout user
     */
    public function logout(): JsonResponse
    {
        try {
            request()->user()->currentAccessToken()->delete();
            return $this->successResponse(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error during logout');
        }
    }

    /**
     * Get authenticated user information
     */
    public function user(): JsonResponse
    {
        try {
            return $this->resourceResponse(
                new UserResource(request()->user()),
                'User information retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error retrieving user information');
        }
    }

    /**
     * Check if token is valid
     */
    public function check(): JsonResponse
    {
        try {
            return $this->resourceResponse(
                new UserResource(request()->user()),
                'Token is valid'
            );
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Invalid token');
        }
    }
}
