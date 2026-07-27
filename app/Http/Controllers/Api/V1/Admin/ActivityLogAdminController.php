<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogAdminController extends Controller
{
    /**
     * List activity log entries with filtering & pagination
     */
    public function index(Request $request)
    {
        $perPage = min(100, max(1, (int) $request->query('per_page', 20)));
        $q = Activity::with('causer');

        if ($logName = $request->query('log_name')) {
            $q->where('log_name', $logName);
        }

        if ($search = $request->query('q')) {
            $q->where('description', 'like', "%{$search}%");
        }

        $activities = $q->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }
}
