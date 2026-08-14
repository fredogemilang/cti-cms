<?php

namespace App\Settings\Actions;

use App\Jobs\WarmCacheJob;
use App\Settings\Contracts\SettingsAction;

class WarmPageCache implements SettingsAction
{
    public function handle(array $values): array
    {
        $concurrency = isset($values['page_cache_warm_concurrency']) ? (int) $values['page_cache_warm_concurrency'] : 5;
        $job = new WarmCacheJob(null, $concurrency);
        $result = $job->handle();

        if ($result['warmed'] > 0) {
            return [
                'type' => 'success',
                'message' => "Cache warming complete. {$result['warmed']} pages preloaded successfully.",
            ];
        }

        return [
            'type' => 'warning',
            'message' => 'No pages were warmed. Please ensure Page Cache is enabled.',
        ];
    }
}
