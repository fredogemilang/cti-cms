<?php

namespace App\Providers;

use App\Http\Middleware\PageCache;
use App\Jobs\PingSitemap;
use App\Listeners\LogAuthEvents;
use App\Listeners\UpdateLastLoginAt;
use App\Models\CptEntry;
use App\Models\FormEntry;
use App\Models\Media;
use App\Models\Page;
use App\Models\User;
use App\Observers\CptEntryObserver;
use App\Observers\PageObserver;
use App\Observers\UserObserver;
use App\Services\WebhookDispatcher;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Registers all core CMS event listeners, model observers,
 * cache invalidation hooks, and webhook dispatch bindings.
 *
 * Extracted from AppServiceProvider for single-responsibility.
 */
class CmsEventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerAuthListeners();
        $this->registerCacheInvalidation();
        $this->registerModelObservers();
        $this->registerWebhookDispatchers();
    }

    /**
     * Auth event listeners: last-login tracking and audit log.
     */
    protected function registerAuthListeners(): void
    {
        Event::listen(Login::class, UpdateLastLoginAt::class);
        Event::listen(Login::class, [LogAuthEvents::class, 'handleLogin']);
        Event::listen(Logout::class, [LogAuthEvents::class, 'handleLogout']);
        Event::listen(Failed::class, [LogAuthEvents::class, 'handleFailed']);
    }

    /**
     * Auto-purge page cache and sitemap when content changes.
     */
    protected function registerCacheInvalidation(): void
    {
        // Page cache invalidation
        foreach ([Page::class, CptEntry::class] as $contentModel) {
            $contentModel::saved(fn () => PageCache::purgeAll());
            $contentModel::deleted(fn () => PageCache::purgeAll());
        }

        // Sitemap invalidation + search engine ping
        $invalidateSitemap = function ($model) {
            Cache::forget('sitemap.xml');
            if (($model->status ?? null) === 'published') {
                PingSitemap::dispatch(method_exists($model, 'getUrl') ? $model->getUrl() : null);
            }
        };

        Page::saved($invalidateSitemap);
        Page::deleted($invalidateSitemap);
        CptEntry::saved($invalidateSitemap);
        CptEntry::deleted($invalidateSitemap);
    }

    /**
     * Audit log: track CRUD on core models.
     */
    protected function registerModelObservers(): void
    {
        Page::observe(PageObserver::class);
        CptEntry::observe(CptEntryObserver::class);
        User::observe(UserObserver::class);
    }

    /**
     * Webhook event dispatch — fires for active webhooks subscribing to the event.
     */
    protected function registerWebhookDispatchers(): void
    {
        $dispatcher = fn () => app(WebhookDispatcher::class);

        Page::saved(function (Page $page) use ($dispatcher) {
            if ($page->status === 'published' && ($page->wasRecentlyCreated || $page->wasChanged('status'))) {
                $dispatcher()->dispatch('page.published', ['id' => $page->id, 'slug' => $page->slug, 'title' => $page->title]);
            } elseif ($page->wasChanged() && ! $page->wasRecentlyCreated) {
                $dispatcher()->dispatch('page.updated', ['id' => $page->id, 'slug' => $page->slug, 'title' => $page->title]);
            }
        });

        FormEntry::created(function (FormEntry $entry) use ($dispatcher) {
            $dispatcher()->dispatch('form.submitted', [
                'form_id' => $entry->form_id,
                'entry_id' => $entry->id,
                'data' => $entry->data,
            ]);
        });

        User::created(function (User $user) use ($dispatcher) {
            $dispatcher()->dispatch('user.registered', [
                'id' => $user->id, 'name' => $user->name, 'email' => $user->email,
            ]);
        });

        Media::created(function (Media $media) use ($dispatcher) {
            $dispatcher()->dispatch('media.uploaded', [
                'id' => $media->id, 'mime' => $media->mime_type, 'size' => $media->size,
            ]);
        });
    }
}
