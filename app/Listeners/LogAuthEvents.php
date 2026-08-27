<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthEvents
{
    public function __construct(protected ActivityLogger $logger) {}

    public function handleLogin(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        cookie()->queue('cms_logged_in', '1', 60 * 24 * 7, '/', null, null, false);
        $this->logger->log('user.login', $event->user, 'Signed in');
    }

    public function handleLogout(Logout $event): void
    {
        cookie()->queue(cookie()->forget('cms_logged_in', '/', null));

        if (! $event->user instanceof User) {
            return;
        }

        $this->logger->log('user.logout', $event->user, 'Signed out');
    }

    public function handleFailed(Failed $event): void
    {
        // Don't link to a user — failed attempts shouldn't attach to the targeted account
        $this->logger->log('user.login_failed', null, 'Failed login attempt', [
            'email' => $event->credentials['email'] ?? 'unknown',
        ]);
    }
}
