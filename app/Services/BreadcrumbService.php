<?php

namespace App\Services;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use App\Models\Page;
use App\Models\TaxonomyTerm;
use Illuminate\Database\Eloquent\Model;
use Plugins\Posts\Models\Category;
use Plugins\Posts\Models\Post;
use Plugins\Posts\Models\Setting;

class BreadcrumbService
{
    /**
     * Resolve breadcrumb items for a given entity or current request URL.
     *
     * @return array<int, array{name: string, url: string|null}>
     */
    public function getItems(?Model $entity = null): array
    {
        $locale = app()->getLocale();
        $homeUrl = url($locale === 'id' ? '/id' : '/');
        $homeSetting = setting('seo_breadcrumb_home_text');
        $homeText = (! empty($homeSetting) && $homeSetting !== 'Home') ? $homeSetting : t('common.home', 'Home');

        $items = [
            [
                'name' => $homeText,
                'url' => $homeUrl,
            ],
        ];

        // Fallback: If no entity is explicitly provided, attempt to auto-resolve from request URL path
        if (! $entity) {
            $path = trim(request()->path(), '/');
            if (str_starts_with($path, 'id/')) {
                $path = substr($path, 3);
            } elseif ($path === 'id') {
                $path = '';
            }

            if (! empty($path)) {
                $segments = explode('/', $path);
                $firstSegment = $segments[0];

                // 1. Try matching CustomPostType slug
                $postType = CustomPostType::where('slug', $firstSegment)
                    ->orWhere('name', $firstSegment)
                    ->first();

                if ($postType) {
                    $entity = $postType;
                } else {
                    // 2. Try matching Page slug
                    $page = Page::where('slug', $firstSegment)->first();
                    if ($page) {
                        $entity = $page;
                    } else {
                        // 3. Try matching Posts plugin archive slug
                        $blogArchiveSlug = class_exists(Setting::class)
                            ? Setting::getArchiveSlug($locale)
                            : 'blog-news';

                        $isBlogSlug = ($firstSegment === $blogArchiveSlug || $firstSegment === 'blog' || $firstSegment === 'blog-news');
                        if (! $isBlogSlug && class_exists(Setting::class)) {
                            foreach (available_locales() as $loc) {
                                if ($firstSegment === Setting::getArchiveSlug($loc)) {
                                    $isBlogSlug = true;
                                    break;
                                }
                            }
                        }

                        if ($isBlogSlug) {
                            $blogArchiveTitle = class_exists(Setting::class)
                                ? Setting::getArchiveTitle($locale, t('blog.title', 'Blog & News'))
                                : t('blog.title', 'Blog & News');

                            $items[] = [
                                'name' => $blogArchiveTitle,
                                'url' => localized_url('/'.$blogArchiveSlug),
                            ];

                            return $items;
                        }
                    }
                }
            }
        }

        if (! $entity) {
            return $items;
        }

        if ($entity instanceof CustomPostType) {
            $cptLabel = $entity->getTranslation('plural_label', $locale) ?: match ($entity->slug) {
                'technology-alliance' => t('common.technology_alliance', 'Technology Alliance'),
                'solution' => t('nav.solutions', 'Solutions'),
                'industry' => t('industry.subtitle', 'Industry'),
                default => $entity->plural_label ?: ucfirst((string) $entity->name),
            };

            $archiveUrl = $entity->getArchiveUrl($locale);

            $items[] = [
                'name' => (string) $cptLabel,
                'url' => $archiveUrl,
            ];
        } elseif ($entity instanceof Page) {
            if ($entity->slug !== 'home') {
                $pageTitle = $entity->getTranslation('title', $locale, fallback: true) ?: $entity->title;
                $items[] = [
                    'name' => (string) ($pageTitle ?: ucfirst((string) $entity->slug)),
                    'url' => $entity->getUrl($locale),
                ];
            }
        } elseif ($entity instanceof CptEntry) {
            /** @var CustomPostType|null $postType */
            $postType = $entity->postType ?? CustomPostType::find($entity->post_type_id);

            // CPT Archive level
            if ($postType) {
                $cptLabel = $postType->getTranslation('plural_label', $locale) ?: match ($postType->slug) {
                    'technology-alliance' => t('common.technology_alliance', 'Technology Alliance'),
                    'solution' => t('nav.solutions', 'Solutions'),
                    'industry' => t('industry.subtitle', 'Industry'),
                    default => $postType->plural_label ?: ucfirst((string) $postType->name),
                };

                $archiveUrl = $postType->getArchiveUrl($locale);

                if ($postType->has_archive) {
                    $items[] = [
                        'name' => (string) $cptLabel,
                        'url' => $archiveUrl,
                    ];
                }
            }

            // Include parent entry if this is a sub-entry / hierarchical child
            /** @var CptEntry|null $parent */
            $parent = $entity->parent ?: $entity->parentRelatedEntries()->first();
            if ($parent) {
                $parentTitle = $parent->getTranslation('title', $locale) ?: $parent->title;
                $items[] = [
                    'name' => (string) ($parentTitle ?: 'Parent'),
                    'url' => $parent->getUrl($locale),
                ];
            }

            $entryTitle = $entity->getTranslation('title', $locale) ?: $entity->title;
            $items[] = [
                'name' => (string) ($entryTitle ?: 'Entry'),
                'url' => $entity->getUrl($locale),
            ];
        } elseif (class_exists(Post::class) && $entity instanceof Post) {
            $blogArchiveSlug = class_exists(Setting::class)
                ? Setting::getArchiveSlug($locale)
                : 'blog-news';

            $blogArchiveTitle = class_exists(Setting::class)
                ? Setting::getArchiveTitle($locale, t('blog.title', 'Blog & News'))
                : t('blog.title', 'Blog & News');

            $items[] = [
                'name' => $blogArchiveTitle,
                'url' => localized_url('/'.$blogArchiveSlug),
            ];

            $showCategoryInBreadcrumb = (bool) setting('seo_taxonomy_categories_index_enabled', true);
            $postTaxonomySetting = (string) setting('seo_breadcrumb_post_taxonomy', 'categories');
            if ($postTaxonomySetting === 'none') {
                $showCategoryInBreadcrumb = false;
            }

            if ($showCategoryInBreadcrumb) {
                /** @var Category|null $category */
                $category = $entity->categories->first();
                if ($category) {
                    $catName = $category->getTranslation('name', $locale) ?: $category->getAttribute('name');
                    $catUrl = $category->getUrl($locale);

                    $items[] = [
                        'name' => (string) $catName,
                        'url' => $catUrl,
                    ];
                }
            }

            $postTitle = $entity->getTranslation('title', $locale) ?: $entity->title;
            $items[] = [
                'name' => (string) $postTitle,
                'url' => $entity->getUrl($locale),
            ];
        } elseif (class_exists(Category::class) && $entity instanceof Category) {
            $blogArchiveSlug = class_exists(Setting::class)
                ? Setting::getArchiveSlug($locale)
                : 'blog-news';

            $blogArchiveTitle = class_exists(Setting::class)
                ? Setting::getArchiveTitle($locale, t('blog.title', 'Blog & News'))
                : t('blog.title', 'Blog & News');

            $items[] = [
                'name' => $blogArchiveTitle,
                'url' => localized_url('/'.$blogArchiveSlug),
            ];

            $catName = $entity->getTranslation('name', $locale) ?: $entity->getAttribute('name');
            $catUrl = $entity->getUrl($locale);

            $items[] = [
                'name' => (string) $catName,
                'url' => $catUrl,
            ];
        } elseif ($entity instanceof TaxonomyTerm) {
            /** @var CustomTaxonomy|null $taxonomy */
            $taxonomy = $entity->taxonomy;
            if ($taxonomy) {
                $taxSlug = $taxonomy->slug;
                $showTaxInBreadcrumb = (bool) setting("seo_taxonomy_{$taxSlug}_index_enabled", true);
                if ($showTaxInBreadcrumb) {
                    $items[] = [
                        'name' => (string) ($taxonomy->plural_label ?: ucfirst((string) $taxonomy->name)),
                        'url' => url('/'.ltrim((string) $taxonomy->slug, '/')),
                    ];
                }
            }

            $items[] = [
                'name' => (string) ($entity->name ?? 'Term'),
                'url' => $entity->getUrl(),
            ];
        } else {
            $title = (string) ($entity->title ?? $entity->name ?? class_basename($entity));
            $url = method_exists($entity, 'getUrl') ? $entity->getUrl($locale) : request()->fullUrl();
            $items[] = [
                'name' => $title,
                'url' => $url,
            ];
        }

        return $items;
    }
}
