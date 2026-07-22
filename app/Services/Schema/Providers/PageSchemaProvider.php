<?php

namespace App\Services\Schema\Providers;

use App\Contracts\SchemaProviderInterface;
use App\Models\Page;
use Carbon\Carbon;

class PageSchemaProvider implements SchemaProviderInterface
{
    public function getIdentifier(): string
    {
        return 'pages';
    }

    public function getLabel(): string
    {
        return 'Published Pages';
    }

    public function getNodes(?string $since = null, int $page = 1, int $perPage = 50): array
    {
        $query = Page::query()
            ->where('status', 'published')
            ->orderBy('updated_at', 'desc');

        if ($since !== null && $since !== '') {
            $cleanSince = str_replace(' ', '+', urldecode($since));
            $parsedDate = is_numeric($cleanSince)
                ? Carbon::createFromTimestamp((int) $cleanSince)
                : Carbon::parse($cleanSince);

            $query->where('updated_at', '>=', $parsedDate);
        }

        $pages = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        $baseUrl = config('app.url', 'http://localhost');
        $orgId = $baseUrl.'/#organization';
        $websiteId = $baseUrl.'/#website';

        $nodes = [];
        foreach ($pages as $p) {
            $pageUrl = url('/'.$p->slug);
            $nodes[] = [
                '@type' => 'WebPage',
                '@id' => $pageUrl.'/#webpage',
                'url' => $pageUrl,
                'name' => $p->title,
                'description' => $p->getMetaDescription() ?: $p->title,
                'datePublished' => $p->created_at ? $p->created_at->toIso8601String() : null,
                'dateModified' => $p->updated_at ? $p->updated_at->toIso8601String() : null,
                'isPartOf' => [
                    '@id' => $websiteId,
                ],
                'publisher' => [
                    '@id' => $orgId,
                ],
            ];
        }

        return $nodes;
    }

    public function getTotalCount(?string $since = null): int
    {
        $query = Page::query()->where('status', 'published');

        if ($since !== null && $since !== '') {
            $cleanSince = str_replace(' ', '+', urldecode($since));
            $parsedDate = is_numeric($cleanSince)
                ? Carbon::createFromTimestamp((int) $cleanSince)
                : Carbon::parse($cleanSince);

            $query->where('updated_at', '>=', $parsedDate);
        }

        return $query->count();
    }

    public function getMetadata(): array
    {
        return [
            'entity' => 'Page',
            'version' => '1.0',
        ];
    }
}
