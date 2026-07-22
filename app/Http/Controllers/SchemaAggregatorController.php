<?php

namespace App\Http\Controllers;

use App\Services\SchemaAggregatorService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SchemaAggregatorController extends Controller
{
    public function __invoke(Request $request, SchemaAggregatorService $service)
    {
        $graphData = $service->getAggregatedGraph();

        if ($request->query('format') === 'jsonl') {
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
            ]);
        }

        return response()->json($graphData, 200, [
            'Content-Type' => 'application/ld+json; charset=utf-8',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
