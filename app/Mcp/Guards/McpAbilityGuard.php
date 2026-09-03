<?php

namespace App\Mcp\Guards;

use App\Models\ApiToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Centralized permission guard for MCP tool execution.
 *
 * Checks the authenticated ApiToken's abilities before allowing
 * any MCP tool to execute. Supports hierarchical ability resolution:
 *
 *   mcp.admin  → implies all other mcp.* abilities
 *   mcp.write  → implies mcp.read
 *   mcp.delete → standalone (requires explicit grant)
 *
 * Usage in MCP tools:
 *   McpAbilityGuard::authorize('mcp.write');       // throws 403
 *   McpAbilityGuard::can('mcp.theme.write');        // returns bool
 */
class McpAbilityGuard
{
    /**
     * Ability hierarchy — left ability implies all right abilities.
     */
    protected static array $hierarchy = [
        'mcp.admin' => ['mcp.read', 'mcp.write', 'mcp.delete', 'mcp.theme.read', 'mcp.theme.write', 'mcp.media.upload', 'mcp.content.publish', 'mcp.connect'],
        'mcp.write' => ['mcp.read', 'mcp.connect'],
        'mcp.delete' => ['mcp.connect'],
        'mcp.read' => ['mcp.connect'],
        'mcp.theme.write' => ['mcp.theme.read', 'mcp.connect'],
        'mcp.theme.read' => ['mcp.connect'],
        'mcp.media.upload' => ['mcp.connect'],
        'mcp.content.publish' => ['mcp.connect'],
    ];

    /**
     * Check if the current request's API token has the given ability.
     */
    public static function can(string $ability): bool
    {
        $token = static::resolveToken();
        if (! $token) {
            return false;
        }

        // Wildcard: token with '*' has all abilities
        if ($token->hasAbility('*')) {
            return true;
        }

        // Direct ability check
        if ($token->hasAbility($ability)) {
            return true;
        }

        // Hierarchical: check if any granted ability implies the requested one
        $grantedAbilities = $token->abilities ?? [];
        foreach ($grantedAbilities as $granted) {
            $implied = static::$hierarchy[$granted] ?? [];
            if (in_array($ability, $implied, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Authorize the given ability or abort with 403.
     *
     * @throws HttpException
     */
    public static function authorize(string $ability, ?string $message = null): void
    {
        if (! static::can($ability)) {
            abort(403, $message ?? "MCP token missing required ability: {$ability}");
        }
    }

    /**
     * Check multiple abilities — ALL must be present.
     */
    public static function canAll(array $abilities): bool
    {
        foreach ($abilities as $ability) {
            if (! static::can($ability)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check multiple abilities — at least ONE must be present.
     */
    public static function canAny(array $abilities): bool
    {
        foreach ($abilities as $ability) {
            if (static::can($ability)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the effective abilities for the current token (expanded with hierarchy).
     *
     * @return array<int, string>
     */
    public static function effectiveAbilities(): array
    {
        $token = static::resolveToken();
        if (! $token) {
            return [];
        }

        if ($token->hasAbility('*')) {
            return ['*'];
        }

        $effective = [];
        foreach ($token->abilities ?? [] as $ability) {
            $effective[] = $ability;
            $implied = static::$hierarchy[$ability] ?? [];
            $effective = array_merge($effective, $implied);
        }

        return array_values(array_unique($effective));
    }

    /**
     * Resolve the API token from the current request attributes.
     */
    public static function resolveToken(): ?ApiToken
    {
        if (! app()->bound('request')) {
            return null;
        }

        return app('request')->attributes->get('api_token');
    }
}
