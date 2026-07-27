<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MediaAdminController extends Controller
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Upload a new media file
     */
    public function upload(Request $request)
    {
        $maxSize = config('media.max_file_size', 10240);
        $allowedExt = config('media.allowed_extensions', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx']);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:'.$maxSize,
            'alt_text' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $media = $this->mediaService->upload(
                $request->file('file'),
                $request->only(['alt_text', 'title', 'description'])
            );

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully.',
                'data' => [
                    'id' => $media->id,
                    'filename' => $media->original_filename,
                    'path' => $media->file_path,
                    'url' => $media->url,
                    'webp_url' => $media->webp_url,
                    'size' => $media->human_readable_size,
                    'mime_type' => $media->mime_type,
                    'alt_text' => $media->alt_text,
                    'title' => $media->title,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update media metadata
     */
    public function update(Request $request, int $id)
    {
        $media = Media::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        $media->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Media metadata updated successfully.',
            'data' => $media,
        ]);
    }

    /**
     * Delete media file
     */
    public function destroy(int $id)
    {
        $media = Media::findOrFail($id);
        $this->mediaService->delete($media);

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully.',
        ]);
    }

    /**
     * Bulk delete media files
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:media,id',
        ]);

        $medias = Media::whereIn('id', $validated['ids'])->get();
        $count = 0;

        foreach ($medias as $media) {
            $this->mediaService->delete($media);
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$count} media items.",
        ]);
    }
}
