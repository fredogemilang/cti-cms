<?php

namespace App\Http\Middleware;

use App\Models\NotFoundLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Log404
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log actual 404 responses on GET/HEAD requests
        if ($response->getStatusCode() !== 404) {
            return $response;
        }

        if (! in_array($request->method(), ['GET', 'HEAD'], true)) {
            return $response;
        }

        // Skip admin panel and API paths — those 404s are dev-related, not content issues
        $path = $request->path();
        $adminPath = trim(config('cms.path', 'admin'), '/');
        if ($adminPath !== '' && str_starts_with($path, $adminPath)) {
            return $response;
        }
        if (str_starts_with($path, 'api/') || str_starts_with($path, '_debugbar')) {
            return $response;
        }

        // Fire-and-forget: log the 404 hit after the response is sent
        // Using app terminating callback so it doesn't slow down the response
        app()->terminating(function () use ($request) {
            try {
                NotFoundLog::recordHit(
                    path: '/'.ltrim($request->path(), '/'),
                    fullUrl: $request->fullUrl(),
                    referrer: $request->header('referer'),
                    userAgent: $request->userAgent(),
                );
            } catch (\Throwable $e) {
                // Never let logging break the app
                report($e);
            }
        });

        return $response;
    }
}
