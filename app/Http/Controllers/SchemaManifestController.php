<?php

namespace App\Http\Controllers;

use App\Services\Schema\SchemaRegistry;
use Illuminate\Http\Request;

class SchemaManifestController extends Controller
{
    public function __invoke(Request $request, SchemaRegistry $registry)
    {
        $manifest = $registry->getManifestData();
        $etag = $registry->calculateETag($manifest);

        $ifNoneMatch = $request->header('If-None-Match');
        if ($ifNoneMatch && trim($ifNoneMatch) === $etag) {
            return response('', 304)->header('ETag', $etag);
        }

        return response()->json($manifest, 200, [
            'Content-Type' => 'application/json; charset=utf-8',
            'ETag' => $etag,
            'Cache-Control' => 'public, max-age=3600, s-maxage=86400',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
