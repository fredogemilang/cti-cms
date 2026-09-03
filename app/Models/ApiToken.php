<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $fillable = [
        'tokenable_type', 'tokenable_id', 'name', 'token_hash', 'prefix',
        'abilities', 'allowed_ips', 'rate_limit_per_minute', 'last_used_at', 'expires_at',
    ];

    protected $casts = [
        'abilities' => 'array',
        'allowed_ips' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function tokenable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Generate a fresh token for a model. Returns the plaintext token —
     * persisted only as hash, so this is the one and only chance to reveal it.
     *
     * @return array{model: self, plaintext: string}
     */
    public static function generateFor(
        Model $owner,
        string $name,
        array $abilities = ['*'],
        array $allowedIps = [],
        ?int $rateLimit = null,
        ?\DateTimeInterface $expiresAt = null,
    ): array {
        $plaintext = Str::random(48);
        $prefix = substr($plaintext, 0, 6);
        $token = static::create([
            'tokenable_type' => $owner->getMorphClass(),
            'tokenable_id' => $owner->getKey(),
            'name' => $name,
            'token_hash' => hash('sha256', $plaintext),
            'prefix' => $prefix,
            'abilities' => $abilities ?: ['*'],
            'allowed_ips' => $allowedIps ?: null,
            'rate_limit_per_minute' => $rateLimit ?? 60,
            'expires_at' => $expiresAt,
        ]);

        return ['model' => $token, 'plaintext' => "{$prefix}.{$plaintext}"];
    }

    public function hasAbility(string $ability): bool
    {
        $abilities = $this->abilities ?? ['*'];

        return in_array('*', $abilities, true) || in_array($ability, $abilities, true);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // ─── MCP Ability Constants ───────────────────────────────────────

    /** Minimum ability to establish MCP connection. */
    const MCP_CONNECT = 'mcp.connect';

    /** Read-only access: list/get content, schema, theme info. */
    const MCP_READ = 'mcp.read';

    /** Write access: create/update content (implies mcp.read). */
    const MCP_WRITE = 'mcp.write';

    /** Destructive access: delete/trash content (standalone, not implied by write). */
    const MCP_DELETE = 'mcp.delete';

    /** Full admin access: settings, reports, user management (implies all). */
    const MCP_ADMIN = 'mcp.admin';

    /** Read theme config, views, template hierarchy. */
    const MCP_THEME_READ = 'mcp.theme.read';

    /** Create/modify theme files (for coding assistants). */
    const MCP_THEME_WRITE = 'mcp.theme.write';

    /** Upload files to media library. */
    const MCP_MEDIA_UPLOAD = 'mcp.media.upload';

    /** Publish content (transition from draft to published). */
    const MCP_CONTENT_PUBLISH = 'mcp.content.publish';

    // ─── MCP Ability Tiers (Single Source of Truth) ────────────────

    /**
     * All available MCP ability tiers.
     *
     * @return array<string, array{label: string, description: string, abilities: array<int, string>, rate_limit: int, badge_color: string}>
     */
    public static function mcpTiers(): array
    {
        return [
            'readonly' => [
                'label' => 'Read Only',
                'description' => 'Can read content, schema, and theme. No write access.',
                'abilities' => [self::MCP_CONNECT, self::MCP_READ, self::MCP_THEME_READ],
                'rate_limit' => 120,
                'badge_color' => 'blue',
            ],
            'editor' => [
                'label' => 'Editor',
                'description' => 'Can read, create/update content, and upload media. Cannot delete or modify settings.',
                'abilities' => [self::MCP_CONNECT, self::MCP_READ, self::MCP_WRITE, self::MCP_THEME_READ, self::MCP_MEDIA_UPLOAD, self::MCP_CONTENT_PUBLISH],
                'rate_limit' => 120,
                'badge_color' => 'green',
            ],
            'developer' => [
                'label' => 'Developer',
                'description' => 'Full read/write including theme development, media upload, and two-step delete. No settings/admin tools.',
                'abilities' => [self::MCP_CONNECT, self::MCP_READ, self::MCP_WRITE, self::MCP_DELETE, self::MCP_THEME_READ, self::MCP_THEME_WRITE, self::MCP_MEDIA_UPLOAD, self::MCP_CONTENT_PUBLISH],
                'rate_limit' => 300,
                'badge_color' => 'purple',
            ],
            'admin' => [
                'label' => 'Admin',
                'description' => 'Full access including reports, settings updates, and schema management.',
                'abilities' => [self::MCP_CONNECT, self::MCP_READ, self::MCP_WRITE, self::MCP_DELETE, self::MCP_ADMIN, self::MCP_THEME_READ, self::MCP_THEME_WRITE, self::MCP_MEDIA_UPLOAD, self::MCP_CONTENT_PUBLISH],
                'rate_limit' => 300,
                'badge_color' => 'red',
            ],
            'chatbot' => [
                'label' => 'Chatbot (Read-Only Published)',
                'description' => 'Read-only access to published content only. For customer-facing chatbots.',
                'abilities' => [self::MCP_CONNECT, self::MCP_READ],
                'rate_limit' => 60,
                'badge_color' => 'cyan',
            ],
        ];
    }

    /**
     * Read-only MCP abilities — safe for chatbots and viewers.
     */
    public static function mcpReadOnlyAbilities(): array
    {
        return static::mcpTiers()['readonly']['abilities'];
    }

    /**
     * Editor MCP abilities — for content editors creating/updating content.
     */
    public static function mcpEditorAbilities(): array
    {
        return static::mcpTiers()['editor']['abilities'];
    }

    /**
     * Full MCP abilities — for admin-level coding assistants.
     */
    public static function mcpFullAbilities(): array
    {
        return static::mcpTiers()['admin']['abilities'];
    }
}
