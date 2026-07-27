<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$request = Request::create('/api/v1/cpt/products', 'GET');
$response = $app->handle($request);

echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
