<?php

namespace App\Services;

use App\Services\Schema\SchemaRegistry;

class SchemaAggregatorService
{
    public function getAggregatedGraph(): array
    {
        return app(SchemaRegistry::class)->getAggregatedGraph();
    }

    public function clearCache(): void
    {
        app(SchemaRegistry::class)->clearCache();
    }
}
