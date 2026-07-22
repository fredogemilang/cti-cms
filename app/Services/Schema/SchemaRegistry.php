<?php

namespace App\Services\Schema;

use App\Contracts\SchemaProviderInterface;
use Illuminate\Support\Facades\Cache;

class SchemaRegistry
{
    public const CACHE_PREFIX = 'cti_schema_v2_';

    /**
     * @var array<string, SchemaProviderInterface>
     */
    protected array $providers = [];

    public function registerProvider(SchemaProviderInterface $provider): void
    {
        $this->providers[$provider->getIdentifier()] = $provider;
    }

    /**
     * @return array<string, SchemaProviderInterface>
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getProvider(string $identifier): ?SchemaProviderInterface
    {
        return $this->providers[$identifier] ?? null;
    }

    public function getManifestData(): array
    {
        $baseUrl = config('app.url', 'http://localhost');
        $manifest = [
            '@context' => 'https://schema.org',
            '@type' => 'DataCatalog',
            'name' => setting('site_title', config('app.name', 'CTI CMS')).' Schema Manifest',
            'description' => 'AI Knowledge Graph Index & Manifest for CTI CMS',
            'generatedAt' => now()->toIso8601String(),
            'generator' => 'CTI CMS 2.0 AI Engine',
            'schemaVersion' => '2.0',
            'license' => 'CC BY 4.0',
            'collections' => [],
        ];

        foreach ($this->providers as $id => $provider) {
            $count = $provider->getTotalCount();
            $manifest['collections'][$id] = [
                'label' => $provider->getLabel(),
                'endpoint' => url("/schema/{$id}"),
                'totalCount' => $count,
                'metadata' => $provider->getMetadata(),
            ];
        }

        $manifest['graphHash'] = md5(json_encode($manifest['collections']));

        return $manifest;
    }

    public function getAggregatedGraph(?string $since = null): array
    {
        $nodes = [];
        foreach ($this->providers as $provider) {
            $providerNodes = $provider->getNodes($since, 1, 100);
            $nodes = array_merge($nodes, $providerNodes);
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => $nodes,
        ];
    }

    public function calculateETag(array $data): string
    {
        return '"'.sha1((string) json_encode($data)).'"';
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_PREFIX.'manifest');
        Cache::forget(self::CACHE_PREFIX.'graph_root');
    }
}
