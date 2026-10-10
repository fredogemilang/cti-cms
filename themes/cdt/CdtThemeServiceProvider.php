<?php

namespace Themes\Cdt;

use App\Models\CptEntry;
use App\Models\CptEntryRelationship;
use App\Models\CustomPostType;
use App\Models\MetaField;
use App\Support\Action;
use App\Support\Filter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Themes\Cdt\Http\Controllers\ShortUrlController;

/**
 * CDT theme behavior that used to be hardcoded in core (Phase 0.3).
 *
 * Booted by App\Services\ThemeLoader only while `cdt` is the active theme,
 * so no active_theme() checks are needed here.
 */
class CdtThemeServiceProvider extends ServiceProvider
{
    /** CPTs that use short URLs: /{vendor} and /{vendor}/{product} */
    protected const VENDOR_CPT = 'technology-alliance';

    protected const PRODUCT_CPTS = ['tech-products', 'products'];

    /** CPTs whose entries are hidden on the frontend when untranslated */
    protected const LOCALE_STRICT_CPTS = ['customer-success', 'client-says'];

    public function register(): void
    {
        require_once __DIR__.'/src/helpers.php';
    }

    public function boot(): void
    {
        $this->registerShortUrls();
        $this->registerFrontendData();
        $this->registerAdminCustomizations();

        Filter::add('forms.attribution_cookie', fn () => 'cdt_attribution');

        Filter::add('search.highlight_class', fn () => 'bg-red-100 text-[#E30613] font-semibold dark:bg-red-950/50 dark:text-red-400 px-1 rounded');

        Filter::add('breadcrumb.cpt_label', function ($label, $cpt) {
            return $label ?: match ($cpt->slug) {
                'technology-alliance' => t('common.technology_alliance', 'Technology Alliance'),
                'solution' => t('nav.solutions', 'Solutions'),
                'industry' => t('industry.subtitle', 'Industry'),
                default => null,
            };
        });
    }

    /**
     * /{vendor} → technology-alliance entry, /{vendor}/{product} → tech-products entry.
     */
    protected function registerShortUrls(): void
    {
        Filter::add('cpt_entry.url', function ($url, $entry, $locale = null) {
            if (! $entry || ! isset($entry->postType)) {
                return $url;
            }

            $cptSlug = $entry->postType->slug;
            $locale ??= app()->getLocale();
            $defaultLocale = setting('default_locale', config('app.locale', 'en'));

            $localePrefix = ($locale !== $defaultLocale && setting('locale_url_structure', 'prefix') === 'prefix')
                ? '/'.$locale
                : '';

            $entrySlug = $entry->getTranslation('slug', $locale, fallback: true) ?? $entry->slug;

            if ($cptSlug === self::VENDOR_CPT) {
                return trailing_slash_url(url($localePrefix.'/'.ltrim($entrySlug, '/')));
            }

            if (in_array($cptSlug, self::PRODUCT_CPTS, true)) {
                $parentVendor = $entry->parentRelatedEntries()->first();
                $parentSlug = $parentVendor
                    ? ($parentVendor->getTranslation('slug', $locale, fallback: true) ?? $parentVendor->slug)
                    : ($entry->getMeta('parent_vendor') ?: null);

                if ($parentSlug) {
                    return trailing_slash_url(url($localePrefix.'/'.ltrim($parentSlug, '/').'/'.ltrim($entrySlug, '/')));
                }
            }

            return $url;
        }, 10);

        Filter::add('cpt_entry.url_redirect', function ($redirectUrl, $entry) {
            if (! $entry || ! isset($entry->postType)) {
                return $redirectUrl;
            }

            $cptSlug = $entry->postType->slug;
            if ($cptSlug === self::VENDOR_CPT || in_array($cptSlug, self::PRODUCT_CPTS, true)) {
                $shortUrl = $entry->getUrl();
                $currentUrl = request()->url();
                if (rtrim($shortUrl, '/') !== rtrim($currentUrl, '/') && ! str_ends_with($currentUrl, '/preview')) {
                    return $shortUrl;
                }
            }

            return $redirectUrl;
        }, 10);

        Action::add('routes.before_catch_all', function (string $adminPath, string $localePattern) {
            if ($localePattern !== 'nothing-to-match') {
                Route::get('/{locale}/{vendorSlug}/{productSlug}', [ShortUrlController::class, 'localeShortProductSingle'])
                    ->where('locale', $localePattern)
                    ->where('vendorSlug', '[a-zA-Z0-9\-]+')
                    ->where('productSlug', '[a-zA-Z0-9\-]+')
                    ->name('locale.cdt.product.single');

                Route::get('/{locale}/{vendorSlug}', [ShortUrlController::class, 'localeShortVendorSingle'])
                    ->where('locale', $localePattern)
                    ->where('vendorSlug', '(?!'.preg_quote($adminPath, '/').')[a-zA-Z0-9\-]+')
                    ->name('locale.cdt.vendor.single');
            }

            Route::get('/{vendorSlug}/{productSlug}', [ShortUrlController::class, 'shortProductSingle'])
                ->where('vendorSlug', '[a-zA-Z0-9\-]+')
                ->where('productSlug', '[a-zA-Z0-9\-]+')
                ->name('cdt.product.single');

            Route::get('/{vendorSlug}', [ShortUrlController::class, 'shortVendorSingle'])
                ->where('vendorSlug', '(?!'.preg_quote($adminPath, '/').')[a-zA-Z0-9\-]+')
                ->name('cdt.vendor.single');
        });
    }

    /**
     * Home testimonials/partners, deferred sections and archive tweaks.
     */
    protected function registerFrontendData(): void
    {
        Filter::add('home.view_data', function (array $data) {
            return $data + [
                'testimonials' => $this->testimonials(),
                'partners' => $this->partners(),
            ];
        });

        Filter::add('deferred_section.allowed', fn (array $sections) => [...$sections, 'testimonials', 'contact']);

        Filter::add('deferred_section.data', function (array $data, string $section) {
            if ($section === 'testimonials') {
                $data['testimonials'] = $this->testimonials();
            }

            return $data;
        });

        Filter::add('cpt_archive.per_page', function (int $perPage, $postType) {
            return in_array($postType->slug, self::LOCALE_STRICT_CPTS, true) ? 6 : $perPage;
        });

        Filter::add('cpt.hide_untranslated_entries', function (bool $hide, $postType) {
            return $hide || in_array($postType->slug, self::LOCALE_STRICT_CPTS, true);
        });
    }

    /**
     * Admin sidebar defaults and the tech-product → vendor relationship field.
     */
    protected function registerAdminCustomizations(): void
    {
        $cdtMenuKeys = [
            'cpt:technology-alliance',
            'cpt:solutions',
            'cpt:customer-success',
            'cpt:client-says',
            'cpt:tech-products',
        ];

        Filter::add('admin_menu.default_sections', function (array $map) use ($cdtMenuKeys) {
            return $map + array_fill_keys($cdtMenuKeys, 'CONTENT');
        });

        Filter::add('admin_menu.default_order', function (array $order) use ($cdtMenuKeys) {
            $order = array_values(array_diff($order, $cdtMenuKeys));
            $mediaIndex = array_search('builtin:media', $order, true);
            $insertAt = $mediaIndex === false ? count($order) : $mediaIndex + 1;
            array_splice($order, $insertAt, 0, $cdtMenuKeys);

            return $order;
        });

        // Products store their vendor as a reverse relationship (vendor = parent).
        Filter::add('cpt_entry.form_meta', function (array $meta, $entry, $postType) {
            if (! $postType->metaFields->where('name', 'parent_vendor_id')->first()) {
                return $meta;
            }

            $parentId = CptEntryRelationship::where('child_entry_id', $entry->id)->orderByDesc('id')->value('parent_entry_id');
            if ($parentId) {
                $meta['parent_vendor_id'] = (string) $parentId;
            }

            return $meta;
        });

        Filter::add('cpt_entry.sync_relationship_field', function (bool $handled, $field, $entry, array $meta) {
            if ($handled || $field->name !== 'parent_vendor_id') {
                return $handled;
            }

            $parentMetaFieldId = MetaField::where('name', 'product_id')
                ->where('options->target_cpt', 'tech-products')
                ->value('id') ?? 43;

            CptEntryRelationship::where('child_entry_id', $entry->id)
                ->where('meta_field_id', $parentMetaFieldId)
                ->delete();

            $parentVendorId = $meta['parent_vendor_id'] ?? null;
            if (! empty($parentVendorId)) {
                $parentEntryId = (int) (is_array($parentVendorId) ? ($parentVendorId[0] ?? 0) : $parentVendorId);
                if ($parentEntryId > 0) {
                    CptEntryRelationship::create([
                        'parent_entry_id' => $parentEntryId,
                        'child_entry_id' => $entry->id,
                        'meta_field_id' => $parentMetaFieldId,
                        'order' => 0,
                    ]);

                    $parentEntry = CptEntry::find($parentEntryId);
                    if ($parentEntry) {
                        $metaData = $entry->meta ?? [];
                        $metaData['parent_vendor'] = $parentEntry->slug;
                        $entry->meta = $metaData;
                        if (empty($entry->featured_image) && ! empty($parentEntry->featured_image)) {
                            $entry->featured_image = $parentEntry->featured_image;
                        }
                        $entry->save();
                    }
                }
            }

            return true;
        });
    }

    /**
     * Published client-says entries, newest first, only those translated to the current locale.
     */
    protected function testimonials(): Collection
    {
        $cpt = CustomPostType::where('slug', 'client-says')->first();
        if (! $cpt) {
            return collect();
        }

        $currentLocale = app()->getLocale();

        return CptEntry::with('author')
            ->where('post_type_id', $cpt->id)
            ->where('status', 'published')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->orderByDesc('id')
            ->get()
            ->filter(fn ($entry) => $entry->hasContentForLocale($currentLocale))
            ->values();
    }

    /**
     * Published technology-alliance entries ordered by title.
     */
    protected function partners(): Collection
    {
        $cpt = CustomPostType::where('slug', self::VENDOR_CPT)->first();
        if (! $cpt) {
            return collect();
        }

        return CptEntry::where('post_type_id', $cpt->id)
            ->where('status', 'published')
            ->orderBy('title')
            ->get();
    }
}
