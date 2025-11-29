<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRequestDetails
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log detailed request information for debugging
        \Log::info('Request Details', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'is_secure' => $request->secure(),
            'scheme' => $request->getScheme(),
            'host' => $request->getHost(),
            'headers' => [
                'X-Forwarded-Proto' => $request->header('X-Forwarded-Proto'),
                'X-Forwarded-Host' => $request->header('X-Forwarded-Host'),
                'X-Forwarded-Port' => $request->header('X-Forwarded-Port'),
                'X-Forwarded-For' => $request->header('X-Forwarded-For'),
                'X-Forwarded-SSL' => $request->header('X-Forwarded-SSL'),
            ],
            'input' => $request->except(['password', '_token']),
        ]);

        return $next($request);
    }
}
