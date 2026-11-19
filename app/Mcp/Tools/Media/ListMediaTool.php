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

#[Description('List media files in the CMS media library. Returns filenames, paths, alt text, dimensions, and mime types. Supports filtering by mime type and search.')]
#[IsReadOnly]
#[IsIdempotent]
class ListMediaTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'search' => $schema->string()
                ->description('Search by filename or alt text.'),

            'mime_type' => $schema->string()
                ->description('Filter by MIME type prefix (e.g., "image", "image/webp", "application/pdf").'),

            'limit' => $schema->integer()
                ->description('Maximum results. Default: 50, Max: 200.')
                ->default(50),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.read');

        $query = Media::query()->orderBy('created_at', 'desc');

        if ($search = $request->get('search')) {
            $query->where(fn ($q) => $q
                ->where('filename', 'like', "%{$search}%")
                ->orWhere('alt_text', 'like', "%{$search}%")
            );
        }

        if ($mimeType = $request->get('mime_type')) {
            $query->where('mime_type', 'like', "{$mimeType}%");
        }

        $limit = min((int) ($request->get('limit') ?? 50), 200);

        $media = $query->take($limit)->get();

        return Response::structured([
            'total' => $media->count(),
            'media' => $media->map(fn ($m) => [
                'id' => $m->id,
                'filename' => $m->filename,
                'path' => $m->path,
                'url' => asset('storage/'.$m->path),
                'alt_text' => $m->alt_text,
                'mime_type' => $m->mime_type,
                'size' => $m->size,
                'width' => $m->width,
                'height' => $m->height,
                'has_variants' => ! empty($m->variants),
            ])->toArray(),
        ]);
    }
}
