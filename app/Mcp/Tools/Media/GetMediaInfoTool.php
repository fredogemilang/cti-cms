<?php

namespace App\Mcp\Tools\Media;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Media;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Description('Get detailed information about a specific media file by ID, including all responsive variants (sm, md, lg, xl, thumb, lqip) and their URLs.')]
#[IsReadOnly]
#[IsIdempotent]
class GetMediaInfoTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The media file ID.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $validated = $request->validate(['id' => 'required|integer']);

        $media = Media::find($validated['id']);
        if (! $media) {
            return Response::error("Media file with ID {$validated['id']} not found.");
        }

        $variants = $media->variants ?? [];

        return Response::structured([
            'id' => $media->id,
            'filename' => $media->filename,
            'path' => $media->path,
            'url' => asset('storage/'.$media->path),
            'alt_text' => $media->alt_text,
            'mime_type' => $media->mime_type,
            'size' => $media->size,
            'width' => $media->width,
            'height' => $media->height,
            'disk' => $media->disk,
            'variants' => collect($variants)->map(fn ($path, $key) => [
                'key' => $key,
                'path' => $path,
                'url' => str_starts_with($path, 'data:') ? '(base64 LQIP)' : asset('storage/'.$path),
            ])->values()->toArray(),
            'created_at' => $media->created_at?->toIso8601String(),
        ]);
    }
}
