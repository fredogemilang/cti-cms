<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageRevision;

class PageRevisionAdminController extends Controller
{
    /**
     * List revisions for a specific Page
     */
    public function index(int $id)
    {
        $page = Page::findOrFail($id);
        $revisions = PageRevision::where('page_id', $page->id)
            ->with('user:id,name,email')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $revisions,
        ]);
    }

    /**
     * Restore a specific revision to the Page
     */
    public function restore(int $id, int $revisionId)
    {
        $revision = PageRevision::where('page_id', $id)->where('id', $revisionId)->firstOrFail();
        $revision->restore();

        return response()->json([
            'success' => true,
            'message' => 'Page revision restored successfully.',
            'data' => $revision->page->fresh('blocks'),
        ]);
    }
}
