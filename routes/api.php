<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\HabitController;

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
    // Public authentication routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes that require authentication
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/check', [AuthController::class, 'check']);

        // Here you can add more protected API routes
        Route::get('/test', function () {
            return response()->json([
                'success' => true,
                'message' => 'API V1 is working correctly',
                'data' => [
                    'version' => '1.0',
                    'timestamp' => now()->toISOString()
                ],
                'status_code' => 200
            ], 200);
        });

        Route::apiResource('habits', HabitController::class);
    });
});

// Legacy routes (unversioned) - Redirect to V1
Route::prefix('legacy')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/check', [AuthController::class, 'check']);
    });
});

// API information route
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Habit Tracker API',
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
                    ],
                    'habits' => [
                        'GET /api/v1/habits',
                        'POST /api/v1/habits',
                        'GET /api/v1/habits/{id}',
                        'PUT /api/v1/habits/{id}',
                        'DELETE /api/v1/habits/{id}'
                    ]
                ]
            ]
        ],
        'status_code' => 200
    ], 200);
});
