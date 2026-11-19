<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SiteHealthController extends Controller
{
    public function index()
    {
        // Gather system metrics
        $phpVersion = PHP_VERSION;
        $laravelVersion = app()->version();

        $dbVersion = 'Unknown';
        try {
            $results = DB::select('SELECT VERSION() as version');
            $dbVersion = $results[0]->version ?? 'Unknown';
        } catch (\Throwable $e) {
        }

        $memoryLimit = ini_get('memory_limit');
        $maxExecutionTime = ini_get('max_execution_time');
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');

        $diskFree = @disk_free_space(base_path());
        $diskTotal = @disk_total_space(base_path());
        $diskFreeMb = $diskFree ? round($diskFree / (1024 * 1024 * 1024), 2) : 'N/A';
        $diskTotalMb = $diskTotal ? round($diskTotal / (1024 * 1024 * 1024), 2) : 'N/A';

        $requiredExtensions = ['bcmath', 'ctype', 'fileinfo', 'json', 'mbstring', 'openssl', 'pdo', 'tokenizer', 'xml', 'gd'];
        $extensionsStatus = [];
        foreach ($requiredExtensions as $ext) {
            $extensionsStatus[$ext] = extension_loaded($ext);
        }

        return view('admin.site-health', [
            'phpVersion' => $phpVersion,
            'laravelVersion' => $laravelVersion,
            'dbVersion' => $dbVersion,
            'memoryLimit' => $memoryLimit,
            'maxExecutionTime' => $maxExecutionTime,
            'uploadMaxFilesize' => $uploadMaxFilesize,
            'postMaxSize' => $postMaxSize,
            'diskFreeMb' => $diskFreeMb,
            'diskTotalMb' => $diskTotalMb,
            'extensionsStatus' => $extensionsStatus,
        ]);
    }
}
