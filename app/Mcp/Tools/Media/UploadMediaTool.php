<?php

namespace App\Mcp\Tools\Media;

use App\Mcp\Guards\McpAbilityGuard;
use App\Models\Activity;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Upload an image/media file to the CMS library through the official MediaService pipeline (WebP compression, responsive variants sm/md/lg/xl, and database registration). Accepts base64 encoded data. Returns the relative path for use in content blocks and meta fields.')]
class UploadMediaTool extends Tool
{
    public function schema(JsonSchema $schema): array
    {
        return [
            'base64_data' => $schema->string()
                ->description('Base64-encoded image data string.')
                ->required(),

            'filename' => $schema->string()
                ->description('Target filename with extension (e.g., "hero-banner.png"). Must match allowed media extensions.')
                ->required(),

            'alt_text' => $schema->string()
                ->description('Accessible alt text description for the image.'),

            'title' => $schema->string()
                ->description('Media title. Defaults to filename.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        McpAbilityGuard::authorize('mcp.media.upload');

        $base64Data = $request->get('base64_data');
        $rawFilename = $request->get('filename');

        if (! $base64Data) {
            return Response::error('The "base64_data" parameter is required.');
        }

        if (! $rawFilename) {
            return Response::error('The "filename" parameter is required (e.g. "image.png").');
        }

        // 1. Sanitize and validate file extension
        $cleanBasename = basename($rawFilename);
        $extension = strtolower(pathinfo($cleanBasename, PATHINFO_EXTENSION));
        $allowedExtensions = config('media.allowed_extensions', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);

        if (! in_array($extension, $allowedExtensions, true)) {
            return Response::error("File extension '{$extension}' is not allowed. Allowed: ".implode(', ', $allowedExtensions));
        }

        $safeName = Str::slug(pathinfo($cleanBasename, PATHINFO_FILENAME)).'.'.$extension;

        // 2. Decode base64
        $cleanedBase64 = preg_replace('#^data:[\w/]+;base64,#i', '', $base64Data);
        $binary = base64_decode($cleanedBase64, true);
        if ($binary === false) {
            return Response::error('Invalid base64 payload provided.');
        }

        // 3. Validate size
        $maxSizeBytes = (int) config('media.max_file_size', 10240) * 1024;
        $size = strlen($binary);
        if ($size > $maxSizeBytes) {
            $maxMb = round($maxSizeBytes / 1024 / 1024, 1);

            return Response::error("File size ({$size} bytes) exceeds the maximum allowed limit of {$maxMb}MB.");
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'mcp_upl_');
        file_put_contents($tmpPath, $binary);

        try {
            // 4. Validate MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpPath) ?: 'application/octet-stream';
            finfo_close($finfo);

            $allowedMimes = config('media.allowed_mimes', [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            ]);

            if (! in_array($mimeType, $allowedMimes, true)) {
                return Response::error("MIME type '{$mimeType}' is not allowed for upload.");
            }

            $uploadedFile = new UploadedFile($tmpPath, $safeName, $mimeType, null, true);

            $token = McpAbilityGuard::resolveToken();
            $mediaService = app(MediaService::class);

            $media = $mediaService->upload($uploadedFile, [
                'alt_text' => $request->get('alt_text') ?: $safeName,
                'title' => $request->get('title') ?: pathinfo($safeName, PATHINFO_FILENAME),
                'uploaded_by' => $token?->tokenable_id ?? auth()->id() ?? null,
            ]);

            // Audit log
            Activity::create([
                'user_id' => $token?->tokenable_id ?? auth()->id(),
                'action' => 'created',
                'subject_type' => Media::class,
                'subject_id' => $media->id,
                'description' => "Media \"{$media->filename}\" uploaded via MCP",
                'properties' => ['source' => 'mcp', 'token_id' => $token?->id, 'path' => $media->path],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return Response::structured([
                'success' => true,
                'media' => [
                    'id' => $media->id,
                    'filename' => $media->filename,
                    'path' => $media->path,
                    'webp_path' => $media->webp_path,
                    'url' => $media->getUrl(),
                    'mime_type' => $media->mime_type,
                    'size_kb' => round($media->size / 1024, 1),
                    'width' => $media->width,
                    'height' => $media->height,
                    'alt_text' => $media->alt_text,
                ],
                'instruction' => 'Use "path" (e.g. "'.$media->path.'") when assigning this image to page blocks or CPT meta fields. Never store absolute or external URLs in database.',
            ]);
        } catch (\RuntimeException $e) {
            // MediaService rejects unsalvageable files (e.g. an SVG that cannot be
            // sanitized). Surface it as a tool-level error like every other
            // rejection path here, instead of letting it escape as a protocol error.
            return Response::error($e->getMessage());
        } finally {
            if (file_exists($tmpPath)) {
                @unlink($tmpPath);
            }
        }
    }
}
