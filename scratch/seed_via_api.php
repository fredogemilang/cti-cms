<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

// 1. Authenticate Admin User
$admin = User::where('is_active', true)->first() ?? User::factory()->create(['is_active' => true]);

// 2. Create CPT via POST /api/v1/admin/cpt API
$req1 = Request::create('/api/v1/admin/cpt', 'POST', [
    'name' => 'Technology Alliances API',
    'singular_label' => 'Alliance API',
    'plural_label' => 'Alliances API',
    'slug' => 'tech_alliances_api',
    'is_active' => true,
]);
$req1->setUserResolver(fn () => $admin);
$res1 = $app->handle($req1);
echo '1. POST /api/v1/admin/cpt Response Status: '.$res1->getStatusCode()."\n";
echo 'Data: '.$res1->getContent()."\n\n";

// 3. Create CPT Entry via POST /api/v1/admin/cpt/tech_alliances_api/entries API
$req2 = Request::create('/api/v1/admin/cpt/tech-alliances-api/entries', 'POST', [
    'title' => 'Akamai Cloud Security',
    'slug' => 'akamai-cloud-security',
    'excerpt' => 'Leading content delivery network and cloud security via API',
    'status' => 'published',
]);
$req2->setUserResolver(fn () => $admin);
$res2 = $app->handle($req2);
echo '2. POST /api/v1/admin/cpt/tech_alliances_api/entries Response Status: '.$res2->getStatusCode()."\n";
echo 'Data: '.$res2->getContent()."\n\n";

// 4. Update Page Blocks via PUT /api/v1/admin/pages/1 API
$req3 = Request::create('/api/v1/admin/pages/1', 'PUT', [
    'title' => 'Home Page via API',
    'blocks' => [
        ['name' => 'hero_title', 'value' => 'Trusted IT Consultant (Updated via REST API)'],
        ['name' => 'hero_subtitle', 'value' => 'Empowering business transformation via Headless REST API requests.'],
    ],
]);
$req3->setUserResolver(fn () => $admin);
$res3 = $app->handle($req3);
echo '3. PUT /api/v1/admin/pages/1 Response Status: '.$res3->getStatusCode()."\n";
echo 'Data: '.$res3->getContent()."\n";
