<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends ApiController
{
    /**
     * Registrar un nuevo usuario
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'Usuario registrado exitosamente', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al registrar usuario');
        }
    }

    /**
     * Iniciar sesión
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->unauthorizedResponse('Las credenciales proporcionadas son incorrectas.');
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'Sesión iniciada exitosamente');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al iniciar sesión');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(null, 'Sesión cerrada exitosamente');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al cerrar sesión');
        }
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function user(Request $request): JsonResponse
    {
        try {
            return $this->successResponse([
                'user' => $request->user(),
            ], 'Información del usuario obtenida');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al obtener información del usuario');
        }
    }

    /**
     * Verificar si el token es válido
     */
    public function check(Request $request): JsonResponse
    {
        try {
            return $this->successResponse([
                'user' => $request->user(),
            ], 'Token válido');
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Token inválido');
        }
    }
}
