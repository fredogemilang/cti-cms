<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Run redirect rules before route matching (so 404 paths can still redirect).
        $middleware->prepend(\App\Http\Middleware\HandleRedirects::class);

        // Set app locale from query → session → cookie → setting.
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\PageCache::class,
            \App\Http\Middleware\OptimizeHtml::class,
            \App\Http\Middleware\CompressResponse::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Auto-complete events that have ended
        $schedule->call(function () {
            \Plugins\Events\Models\Event::where('status', 'published')
                ->where('end_date', '<', now())
                ->update(['status' => 'completed']);
        })->daily()->at('00:01');

        // Prune old audit log entries (default 90 days, configurable via setting)
        $schedule->command('activity:prune')->dailyAt('03:00')->onOneServer();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
