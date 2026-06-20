<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Resolve `Authorization: Bearer <token>` into an authenticated API token
     * (binds the owner onto $request->user()) and enforce per-token rate limit,
     * IP allowlist, and optional ability requirement (passed via route arg).
     */
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        $bearer = $request->bearerToken();
        if (! $bearer) {
            return $this->unauthorized('Missing bearer token.');
        }

        // Tokens are stored as "prefix.plaintext". Hash the plaintext portion only.
        $plaintext = str_contains($bearer, '.') ? substr($bearer, strpos($bearer, '.') + 1) : $bearer;
        $hash = hash('sha256', $plaintext);

        /** @var ApiToken|null $token */
        $token = ApiToken::where('token_hash', $hash)->first();
        if (! $token || $token->isExpired()) {
            return $this->unauthorized('Invalid or expired token.');
        }

        if ($ability && ! $token->hasAbility($ability)) {
            return $this->forbidden("Token missing required ability: {$ability}");
        }

        $ips = $token->allowed_ips ?? [];
        if (! empty($ips) && ! in_array($request->ip(), $ips, true)) {
            return $this->forbidden('Source IP not allowed for this token.');
        }

        $rateKey = "api-token:{$token->id}";
        $limit = max(1, (int) $token->rate_limit_per_minute);
        if (RateLimiter::tooManyAttempts($rateKey, $limit)) {
            return response()->json(['error' => 'Rate limit exceeded'], 429, [
                'Retry-After' => RateLimiter::availableIn($rateKey),
            ]);
        }
        RateLimiter::hit($rateKey, 60);

        // Bind owner to the request like a regular user.
        $owner = $token->tokenable;
        if ($owner) {
            $request->setUserResolver(fn () => $owner);
        }
        $request->attributes->set('api_token', $token);

        $token->last_used_at = now();
        $token->saveQuietly();

        return $next($request);
    }

    protected function unauthorized(string $msg): Response
    {
        return response()->json(['error' => $msg], 401);
    }

    protected function forbidden(string $msg): Response
    {
        return response()->json(['error' => $msg], 403);
    }
}
