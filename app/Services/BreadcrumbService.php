<?php

namespace App\Services;

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\CustomTaxonomy;
use App\Models\Page;
use App\Models\TaxonomyTerm;
use Illuminate\Database\Eloquent\Model;

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
        $homeText = ! empty($homeSetting) ? $homeSetting : t('common.home', 'Home');

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
        } elseif ($entity instanceof TaxonomyTerm) {
            /** @var CustomTaxonomy|null $taxonomy */
            $taxonomy = $entity->taxonomy;
            if ($taxonomy) {
                $items[] = [
                    'name' => (string) ($taxonomy->plural_label ?: ucfirst((string) $taxonomy->name)),
                    'url' => url('/'.ltrim((string) $taxonomy->slug, '/')),
                ];
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
