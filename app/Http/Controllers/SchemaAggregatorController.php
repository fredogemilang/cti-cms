<?php

namespace App\Http\Controllers;

use App\Services\Schema\SchemaRegistry;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchemaAggregatorController extends Controller
{
    public function index(Request $request, SchemaRegistry $registry)
    {
        $since = $request->query('since');
        $sinceString = is_string($since) ? $since : null;

        $graphData = $registry->getAggregatedGraph($sinceString);
        $etag = $registry->calculateETag($graphData);

        $ifNoneMatch = $request->header('If-None-Match');
        if ($ifNoneMatch && trim($ifNoneMatch) === $etag) {
            return response('', 304)->header('ETag', $etag);
        }

        $acceptHeader = (string) $request->header('Accept');
        $formatParam = (string) $request->query('format');

        if ($formatParam === 'jsonl' || str_contains($acceptHeader, 'application/x-jsonlines')) {
            return new StreamedResponse(function () use ($graphData) {
                $graph = $graphData['@graph'] ?? [];
                foreach ($graph as $node) {
                    echo json_encode($node, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                }
            }, 200, [
                'Content-Type' => 'application/x-jsonlines; charset=utf-8',
                'X-Accel-Buffering' => 'no',
                'ETag' => $etag,
            ]);
        }

        $contentType = str_contains($acceptHeader, 'application/json')
            ? 'application/json; charset=utf-8'
            : 'application/ld+json; charset=utf-8';

        return response()->json($graphData, 200, [
            'Content-Type' => $contentType,
            'ETag' => $etag,
            'Cache-Control' => 'public, max-age=3600, s-maxage=86400',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function showCollection(string $type, Request $request, SchemaRegistry $registry)
    {
        $provider = $registry->getProvider($type);
        if (! $provider) {
            abort(404, "Schema collection '{$type}' not found.");
        }

        $since = $request->query('since');
        $sinceString = is_string($since) ? $since : null;
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(100, max(1, (int) $request->query('per_page', 50)));

        $nodes = $provider->getNodes($sinceString, $page, $perPage);
        $total = $provider->getTotalCount($sinceString);

        $payload = [
            '@context' => 'https://schema.org',
            '@type' => 'DataCatalog',
            'collection' => $type,
            'page' => $page,
            'perPage' => $perPage,
            'totalItems' => $total,
            'totalPages' => (int) ceil($total / $perPage),
            '@graph' => $nodes,
        ];

        $etag = $registry->calculateETag($payload);

        $ifNoneMatch = $request->header('If-None-Match');
        if ($ifNoneMatch && trim($ifNoneMatch) === $etag) {
            return response('', 304)->header('ETag', $etag);
        }

        return response()->json($payload, 200, [
            'Content-Type' => 'application/ld+json; charset=utf-8',
            'ETag' => $etag,
            'Cache-Control' => 'public, max-age=3600, s-maxage=86400',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
