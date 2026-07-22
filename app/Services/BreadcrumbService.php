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
        $homeText = (string) setting('seo_breadcrumb_home_text', 'Home');
        $items = [
            [
                'name' => $homeText !== '' ? $homeText : 'Home',
                'url' => url('/'),
            ],
        ];

        if (! $entity) {
            return $items;
        }

        if ($entity instanceof Page) {
            if ($entity->slug !== 'home') {
                $items[] = [
                    'name' => (string) ($entity->title ?? ucfirst((string) $entity->slug)),
                    'url' => $entity->getUrl(),
                ];
            }
        } elseif ($entity instanceof CptEntry) {
            /** @var CustomPostType|null $postType */
            $postType = $entity->postType;
            if ($postType) {
                $items[] = [
                    'name' => (string) ($postType->plural_label ?: ucfirst((string) $postType->name)),
                    'url' => $postType->getArchiveUrl(),
                ];
            }

            $items[] = [
                'name' => (string) ($entity->title ?? 'Entry'),
                'url' => $entity->getUrl(),
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
            $class = class_basename($entity);
            if ($class === 'Post') {
                $postTax = (string) setting('seo_breadcrumb_post_taxonomy', 'categories');
                if ($postTax !== 'none' && method_exists($entity, 'categories')) {
                    $category = $entity->categories()->first();
                    if ($category) {
                        $catName = (string) ($category->name ?? 'Category');
                        $catUrl = method_exists($category, 'getUrl') ? $category->getUrl() : url('/category/'.($category->slug ?? ''));
                        $items[] = [
                            'name' => $catName,
                            'url' => $catUrl,
                        ];
                    }
                }

                $title = (string) ($entity->title ?? 'Post');
                $url = method_exists($entity, 'getUrl') ? $entity->getUrl() : url('/post/'.($entity->slug ?? ''));
                $items[] = [
                    'name' => $title,
                    'url' => $url,
                ];
            } else {
                $title = (string) ($entity->title ?? $entity->name ?? class_basename($entity));
                $url = method_exists($entity, 'getUrl') ? $entity->getUrl() : request()->fullUrl();
                $items[] = [
                    'name' => $title,
                    'url' => $url,
                ];
            }
        }

        return $items;
    }
}
