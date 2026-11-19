<?php

namespace App\Http\Controllers;

use App\Services\IndexNowService;
use Illuminate\Http\Response;

class IndexNowController extends Controller
{
    public function showKey(string $key, IndexNowService $service): Response
    {
        $currentKey = $service->getKey();

        if ($key !== $currentKey) {
            abort(404);
        }

        return response($currentKey, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
