<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API V1 Routes
Route::prefix('v1')->group(function () {
    // Rutas públicas de autenticación
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Rutas protegidas que requieren autenticación
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/check', [AuthController::class, 'check']);

        // Aquí puedes agregar más rutas protegidas de tu API
        Route::get('/test', function () {
            return response()->json([
                'success' => true,
                'message' => 'API V1 funcionando correctamente',
                'data' => [
                    'version' => '1.0',
                    'timestamp' => now()->toISOString()
                ]
            ]);
        });
    });
});

// Rutas legacy (sin versionar) - Redirigir a V1
Route::prefix('legacy')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/check', [AuthController::class, 'check']);
    });
});

// Ruta de información de la API
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'API de Habit Tracker',
        'data' => [
            'name' => 'Habit Tracker API',
            'version' => '1.0',
            'available_versions' => ['v1'],
            'documentation' => '/api/documentation',
            'endpoints' => [
                'v1' => [
                    'auth' => [
                        'POST /api/v1/register',
                        'POST /api/v1/login',
                        'POST /api/v1/logout',
                        'GET /api/v1/user',
                        'GET /api/v1/check'
                    ]
                ]
            ]
        ]
    ]);
});
