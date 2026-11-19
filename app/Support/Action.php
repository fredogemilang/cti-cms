<?php

namespace App\Support;

/**
 * Minimal action system (WordPress-style do_action / add_action).
 *
 * Unlike Filter, actions do not pass a value through the callbacks — they let
 * themes and plugins run code at a well-defined point in the core lifecycle.
 *
 * Usage:
 *   Action::add('routes.before_catch_all', fn (string $adminPath, string $localePattern) => ...);
 *   Action::run('routes.before_catch_all', $adminPath, $localePattern);
 *
 * Core actions:
 *   - routes.before_catch_all (string $adminPath, string $localePattern)
 *       Fired inside the `web` middleware group after CPT/taxonomy routes and
 *       right before the page catch-all routes. Use it for theme-specific
 *       URL schemes (e.g. /{vendor}/{product}). $localePattern is a regex of
 *       non-default locales, or 'nothing-to-match' when there are none.
 */
class Action
{
    /** @var array<string, array<int, array{callback: callable, priority: int}>> */
    protected static array $hooks = [];

    /**
     * Register an action callback.
     */
    public static function add(string $tag, callable $callback, int $priority = 10): void
    {
        static::$hooks[$tag][] = ['callback' => $callback, 'priority' => $priority];

        // Stable sort by priority (lower = earlier)
        usort(static::$hooks[$tag], fn ($a, $b) => $a['priority'] <=> $b['priority']);
    }

    /**
     * Run all callbacks registered for a tag.
     */
    public static function run(string $tag, mixed ...$args): void
    {
        foreach (static::$hooks[$tag] ?? [] as $hook) {
            call_user_func($hook['callback'], ...$args);
        }
    }

    /**
     * Check if a tag has registered callbacks.
     */
    public static function has(string $tag): bool
    {
        return ! empty(static::$hooks[$tag]);
    }

    /**
     * Remove all callbacks (useful in tests).
     */
    public static function clear(): void
    {
        static::$hooks = [];
    }
}
