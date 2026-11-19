<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;

class MenuPublicController extends Controller
{
    /**
     * Get active menu tree for frontend navigation
     */
    public function index()
    {
        $items = MenuItem::active()
            ->ordered()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->active()->ordered()])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}
