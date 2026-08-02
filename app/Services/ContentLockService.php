<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ContentLockService
{
    /**
     * Lock duration in seconds before automatically expiring.
     */
    protected int $ttl = 60;

    /**
     * Acquire or renew a lock on a content entity (Page or CptEntry).
     */
    public function acquire(string $type, int $id, int $userId): bool
    {
        $key = $this->getLockKey($type, $id);
        $currentLock = Cache::get($key);

        if ($currentLock && $currentLock['user_id'] !== $userId) {
            return false;
        }

        Cache::put($key, [
            'user_id' => $userId,
            'user_name' => auth()->user() ? auth()->user()->name : 'User #'.$userId,
            'locked_at' => now()->timestamp,
        ], $this->ttl);

        return true;
    }

    /**
     * Check who currently holds the lock on a content entity.
     */
    public function check(string $type, int $id, int $userId): ?array
    {
        $key = $this->getLockKey($type, $id);
        $lock = Cache::get($key);

        if ($lock && $lock['user_id'] !== $userId) {
            return $lock;
        }

        return null;
    }

    /**
     * Release lock when user leaves or closes editor.
     */
    public function release(string $type, int $id, int $userId): void
    {
        $key = $this->getLockKey($type, $id);
        $lock = Cache::get($key);

        if ($lock && $lock['user_id'] === $userId) {
            Cache::forget($key);
        }
    }

    protected function getLockKey(string $type, int $id): string
    {
        return "content_lock:{$type}:{$id}";
    }
}
