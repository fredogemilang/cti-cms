<?php

namespace App\Services\Ai;

class AiResourceRegistry
{
    /**
     * @var array<int, array{label: string, url: string, description: ?string}>
     */
    protected array $resources = [];

    public function registerResource(string $label, string $url, ?string $description = null): void
    {
        $this->resources[] = [
            'label' => $label,
            'url' => $url,
            'description' => $description,
        ];
    }

    /**
     * @return array<int, array{label: string, url: string, description: ?string}>
     */
    public function getResources(): array
    {
        return $this->resources;
    }
}
