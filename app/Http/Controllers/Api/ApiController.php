<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;

abstract class ApiController extends Controller
{
    /**
     * Success response with status code
     */
    protected function successResponse($data = null, string $message = 'Operation successful', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status_code' => $code,
        ], $code);
    }

    /**
     * Error response with status code
     */
    protected function errorResponse(string $message = 'Operation failed', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'status_code' => $code,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse($errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Resource not found response
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Server error response
     */
    protected function serverErrorResponse(string $message = 'Internal server error'): JsonResponse
    {
        return $this->errorResponse($message, 500);
    }

    /**
     * Handle validation exception
     */
    protected function handleValidationException(ValidationException $exception): JsonResponse
    {
        return $this->validationErrorResponse($exception->errors());
    }

    /**
     * Resource response with status code
     */
    protected function resourceResponse(JsonResource $resource, string $message = 'Resource retrieved successfully', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource,
            'status_code' => $code,
        ], $code);
    }

    /**
     * Collection response with status code
     */
    protected function collectionResponse($collection, string $message = 'Resources retrieved successfully', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $collection,
            'status_code' => $code,
        ], $code);
    }
}
