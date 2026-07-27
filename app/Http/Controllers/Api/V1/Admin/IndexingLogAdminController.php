<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndexingLog;
use App\Models\SitemapPing;
use Illuminate\Http\Request;

class IndexingLogAdminController extends Controller
{
    /**
     * List IndexNow logs & sitemap pings
     */
    public function index(Request $request)
    {
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));

        $indexingLogs = IndexingLog::latest()->paginate($perPage);
        $sitemapPings = SitemapPing::latest()->take(10)->get();

        return response()->json([
            'success' => true,
            'indexing_logs' => $indexingLogs,
            'sitemap_pings' => $sitemapPings,
        ]);
    }
}
