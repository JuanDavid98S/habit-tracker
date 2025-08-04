<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Establecer la versión por defecto si no se especifica
        if (!$request->route()->getPrefix()) {
            $request->route()->setPrefix('v1');
        }

        // Agregar información de versión a la respuesta
        $response = $next($request);

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);

            // Agregar metadatos de versión si no existen
            if (!isset($data['meta'])) {
                $data['meta'] = [
                    'api_version' => $request->route()->getPrefix() ?: 'v1',
                    'timestamp' => now()->toISOString(),
                ];
            }

            $response->setData($data);
        }

        return $response;
    }
}
