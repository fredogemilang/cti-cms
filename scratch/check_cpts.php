<?php

use App\Models\CustomPostType;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$cpts = CustomPostType::all();
echo json_encode($cpts, JSON_PRETTY_PRINT);
