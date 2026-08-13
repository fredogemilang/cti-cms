<?php

namespace App\Providers;

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
use App\Services\CacheManager;
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
        $contentModels = [Page::class, CptEntry::class];
        if (class_exists(\Plugins\Posts\Models\Post::class)) {
            $contentModels[] = \Plugins\Posts\Models\Post::class;
        }

        // Page cache invalidation + targeted cache warming
        foreach ($contentModels as $contentModel) {
            $contentModel::saved(function ($model) {
                CacheManager::purgeAll();

                // Targeted cache warming on content publish/save
                if (setting('page_cache_enabled', false) && setting('page_cache_warm_on_save', true) && ($model->status ?? null) === 'published') {
                    $urls = ['/'];
                    $locales = function_exists('available_locales') ? available_locales() : [config('app.locale', 'en')];

                    if (method_exists($model, 'getUrl')) {
                        foreach ($locales as $loc) {
                            try {
                                $u = $model->getUrl($loc);
                                if (! empty($u)) {
                                    $urls[] = $u;
                                }
                            } catch (\Throwable) {
                            }
                        }
                    }

                    \App\Jobs\WarmCacheJob::dispatch(array_values(array_unique($urls)));
                }
            });

            $contentModel::deleted(fn () => CacheManager::purgeAll());
        }

        // Sitemap invalidation + search engine ping
        $invalidateSitemap = function ($model) {
            Cache::forget('sitemap.xml');
            if (($model->status ?? null) === 'published') {
                PingSitemap::dispatch(method_exists($model, 'getUrl') ? $model->getUrl() : null);
            }
        };

        foreach ($contentModels as $contentModel) {
            $contentModel::saved($invalidateSitemap);
            $contentModel::deleted($invalidateSitemap);
        }
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
            $form = $entry->form;
            $dispatcher()->dispatch('form.submitted', [
                'form_id' => $entry->form_id,
                'form_name' => $form ? ($form->name ?? $form->title) : null,
                'entry_id' => $entry->id,
                'data' => $entry->data,
                'ip_address' => $entry->ip_address,
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
