<?php

namespace App\Support;

/**
 * Minimal hook/filter system (WordPress-style).
 *
 * Usage:
 *   Filter::add('cpt_entry.url', fn($url, $entry) => ...);
 *   $url = Filter::apply('cpt_entry.url', $url, $entry);
 */
class Filter
{
    /** @var array<string, callable[]> */
    protected static array $hooks = [];

    /**
     * Register a filter callback.
     */
    public static function add(string $tag, callable $callback, int $priority = 10): void
    {
        static::$hooks[$tag][] = ['callback' => $callback, 'priority' => $priority];

        // Stable sort by priority (lower = earlier)
        usort(static::$hooks[$tag], fn ($a, $b) => $a['priority'] <=> $b['priority']);
    }

    /**
     * Apply all registered filters for a tag. Returns the final value.
     */
    public static function apply(string $tag, mixed $value, ...$args): mixed
    {
        if (empty(static::$hooks[$tag])) {
            return $value;
        }

        foreach (static::$hooks[$tag] as $hook) {
            $value = call_user_func($hook['callback'], $value, ...$args);
        }

        return $value;
    }

    /**
     * Remove all hooks (useful in tests).
     */
    public static function clear(): void
    {
        static::$hooks = [];
    }

    /**
     * Check if a tag has registered hooks.
     */
    public static function has(string $tag): bool
    {
        return ! empty(static::$hooks[$tag]);
    }
}
