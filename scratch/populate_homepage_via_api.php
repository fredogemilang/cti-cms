<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\CptEntry;
use App\Models\CustomPostType;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// 1. Authenticate Admin User
$admin = User::where('is_active', true)->first() ?? User::factory()->create(['is_active' => true]);

echo "=== 🚀 Populating Homepage & CPT Content via Headless REST API ===\n\n";

// Function to handle authenticated JSON API requests
$callApi = function ($uri, $method, $data = []) use ($app, $admin) {
    $request = Request::create(
        $uri,
        $method,
        $data,
        [],
        [],
        [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_CONTENT_TYPE' => 'application/json',
        ]
    );
    $request->setUserResolver(fn () => $admin);
    $app->instance('request', $request);

    $httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    return $httpKernel->handle($request);
};

// -------------------------------------------------------------
// 1. UPDATE PAGE ID 1 BLOCKS (PUT /api/v1/admin/pages/1)
// -------------------------------------------------------------
$resPage = $callApi('/api/v1/admin/pages/1', 'PUT', [
    'title' => 'Trusted IT Consultant for Scalable and Secure Growth',
    'slug' => 'home',
    'template' => 'home',
    'status' => 'published',
    'blocks' => [
        ['name' => 'hero_prefix', 'value' => 'Speed Up Your'],
        ['name' => 'hero_title', 'value' => 'Transformation Journey'],
        ['name' => 'hero_subtitle', 'value' => 'Accelerate IT transformation journey with our end-to-end expertise, from strategy to execution across cloud, security, and observability.'],
        ['name' => 'hero_image', 'type' => 'media', 'value' => 'themes/cdt/assets/banner_hero-DHYDqbF8.jpg'],
        ['name' => 'hero_cta_text', 'value' => 'Learn More'],
        ['name' => 'hero_cta_url', 'value' => '#areas-of-expertise'],
        ['name' => 'hero_catalogue_text', 'value' => 'Access Solutions Catalogue'],
        ['name' => 'expertise_title', 'value' => 'Area Of Expertise'],
        ['name' => 'why_cdt_title', 'value' => 'Why CDT?'],
        ['name' => 'why_cdt_box_1_title', 'value' => 'NUMBER ONE IT SERVICE DELIVERY'],
        ['name' => 'why_cdt_box_1_desc', 'value' => 'Guarantee the best quality of IT service delivery with every stage delivery involves many IT experts\' role and ensure that service-level agreement (SLA) is applied.'],
        ['name' => 'why_cdt_box_2_title', 'value' => 'EXCELLENT CUSTOMER SERVICES'],
        ['name' => 'why_cdt_box_2_desc', 'value' => '24/7 customer response center, and many other convenient services were given fulfill customer requirement in today\'s digital era.'],
        ['name' => 'why_cdt_box_3_title', 'value' => 'YEARS OF EXPERIENCE EXPERTS'],
        ['name' => 'why_cdt_box_3_desc', 'value' => 'With years of experience and numerous of project portfolios, professional IT experts will measure and manage risk to ensure accuracy in implementing solutions into customer\'s IT environment.'],
        ['name' => 'cta_banner_title', 'value' => 'Ready to Accelerate Your Enterprise IT Transformation?'],
        ['name' => 'cta_banner_subtitle', 'value' => 'Consult with our certified IT experts today for tailored cloud, security, and infrastructure solutions.'],
        ['name' => 'cta_banner_button_text', 'value' => 'Contact Our Experts'],
    ],
]);

echo '1. PUT /api/v1/admin/pages/1 -> Status: '.$resPage->getStatusCode()."\n";

// -------------------------------------------------------------
// 2. ENSURE CPTs EXIST (products, solutions, customer-success)
// -------------------------------------------------------------
$cptsData = [
    ['name' => 'Products & Technology Alliance', 'singular_label' => 'Product', 'plural_label' => 'Products', 'slug' => 'products'],
    ['name' => 'Enterprise Solutions', 'singular_label' => 'Solution', 'plural_label' => 'Solutions', 'slug' => 'solutions'],
    ['name' => 'Customer Success Stories', 'singular_label' => 'Customer Story', 'plural_label' => 'Customer Success Stories', 'slug' => 'customer-success'],
];

foreach ($cptsData as $cptDef) {
    $cptModel = CustomPostType::where('slug', $cptDef['slug'])->first();
    if (! $cptModel) {
        $resCpt = $callApi('/api/v1/admin/cpt', 'POST', array_merge($cptDef, ['is_active' => true]));
        echo "2. POST /api/v1/admin/cpt ({$cptDef['slug']}) -> Status: ".$resCpt->getStatusCode()."\n";
    } else {
        echo "2. CPT '{$cptDef['slug']}' already exists (ID: {$cptModel->id})\n";
    }
}

// -------------------------------------------------------------
// 3. CREATE / UPDATE CPT ENTRIES (POST /api/v1/admin/cpt/{type}/entries)
// -------------------------------------------------------------
$products = [
    ['title' => 'Akamai', 'slug' => 'akamai', 'excerpt' => 'Leading content delivery network (CDN), cybersecurity, and cloud service provider.'],
    ['title' => 'Amazon Web Services', 'slug' => 'amazon-web-services', 'excerpt' => 'On-demand cloud computing platforms and APIs with pay-as-you-go pricing.'],
    ['title' => 'Dynatrace', 'slug' => 'dynatrace', 'excerpt' => 'AI-powered application performance management and enterprise observability platform.'],
    ['title' => 'Entrust', 'slug' => 'entrust', 'excerpt' => 'Trusted identity, payments, and data protection technology solutions.'],
    ['title' => 'F5', 'slug' => 'f5', 'excerpt' => 'Multi-cloud application security and delivery solutions for high availability.'],
    ['title' => 'Hitachi Vantara', 'slug' => 'hitachi-vantara', 'excerpt' => 'Enterprise data storage, hybrid cloud infrastructure, and digital solutions.'],
    ['title' => 'MicroStrategy', 'slug' => 'microstrategy', 'excerpt' => 'Enterprise analytics, business intelligence, and mobile software platform.'],
    ['title' => 'Zscaler', 'slug' => 'zscaler', 'excerpt' => 'Zero Trust exchange cloud security platform for hybrid and remote workforces.'],
];

foreach ($products as $prod) {
    $existing = CptEntry::whereHas('postType', fn ($q) => $q->where('slug', 'products'))->where('slug', $prod['slug'])->first();
    if (! $existing) {
        $resProd = $callApi('/api/v1/admin/cpt/products/entries', 'POST', array_merge($prod, ['status' => 'published']));
        echo "   - Product Entry ({$prod['slug']}) CREATED -> Status: ".$resProd->getStatusCode()."\n";
    } else {
        $resProd = $callApi('/api/v1/admin/cpt/products/entries/'.$existing->id, 'PUT', array_merge($prod, ['status' => 'published']));
        echo "   - Product Entry ({$prod['slug']}) UPDATED -> Status: ".$resProd->getStatusCode()."\n";
    }
}

$solutions = [
    ['title' => 'Security', 'slug' => 'security', 'excerpt' => 'Preventative cyber threat defense, WAF, IAM, and Zero Trust access control.'],
    ['title' => 'Cloud', 'slug' => 'cloud', 'excerpt' => 'Cloud-native development, hybrid cloud migration, and AWS infrastructure.'],
    ['title' => 'Observability', 'slug' => 'observability', 'excerpt' => 'Full-stack monitoring, real-time system performance intelligence, and AIOps.'],
    ['title' => 'Analytics', 'slug' => 'analytics', 'excerpt' => 'Modern BI, enterprise data warehousing, and actionable data insights.'],
    ['title' => 'Infrastructure', 'slug' => 'infrastructure', 'excerpt' => 'High-performance compute, resilient storage arrays, and hyperconverged infrastructure.'],
];

foreach ($solutions as $sol) {
    $existing = CptEntry::whereHas('postType', fn ($q) => $q->where('slug', 'solutions'))->where('slug', $sol['slug'])->first();
    if (! $existing) {
        $resSol = $callApi('/api/v1/admin/cpt/solutions/entries', 'POST', array_merge($sol, ['status' => 'published']));
        echo "   - Solution Entry ({$sol['slug']}) CREATED -> Status: ".$resSol->getStatusCode()."\n";
    } else {
        $resSol = $callApi('/api/v1/admin/cpt/solutions/entries/'.$existing->id, 'PUT', array_merge($sol, ['status' => 'published']));
        echo "   - Solution Entry ({$sol['slug']}) UPDATED -> Status: ".$resSol->getStatusCode()."\n";
    }
}

echo "\n✅ REST API Content Population Completed Successfully!\n";
