<?php

namespace App\Contracts;

interface SchemaProviderInterface
{
    /**
     * Get the unique string identifier for this schema collection (e.g. 'organization', 'pages', 'products').
     */
    public function getIdentifier(): string;

    /**
     * Get the human-readable label for this provider.
     */
    public function getLabel(): string;

    /**
     * Get the schema nodes array for this collection.
     *
     * @param  string|null  $since  ISO date or timestamp string for incremental sync filtering
     * @param  int  $page  Page number for pagination
     * @param  int  $perPage  Items per page limit
     * @return array<int, array<string, mixed>>
     */
    public function getNodes(?string $since = null, int $page = 1, int $perPage = 50): array;

    /**
     * Get total item count matching the since criteria.
     */
    public function getTotalCount(?string $since = null): int;

    /**
     * Get provider-specific metadata.
     *
     * @return array<string, mixed>
     */
    public function getMetadata(): array;
}
