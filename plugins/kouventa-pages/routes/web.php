<?php

use Illuminate\Support\Facades\Route;
use Plugins\KouventaPages\Models\KouventaPage;

$adminPath = config('admin.path', config('cms.path', 'admin'));

// Admin Management Routes
Route::middleware(['web', 'auth'])->prefix("{$adminPath}/kouventa-pages")->name('admin.kouventa-pages.')->group(function () {
    // 1. Solutions (AI Solutions & Customer Engagement Solutions)
    Route::get('/solutions', function () {
        return view('kouventa-pages::index', [
            'category' => 'solutions',
            'heading' => 'Solutions',
            'subheading' => 'Manage AI Solutions and Customer Engagement Solutions pages with structured sections and bilingual support.',
        ]);
    })->name('solutions');

    Route::get('/solutions/{id}/edit', function ($id) {
        $page = KouventaPage::findOrFail($id);

        return view('kouventa-pages::edit', ['id' => $id, 'page' => $page]);
    })->whereNumber('id')->name('solutions.edit');

    // 2. Products (Artificial Intelligence & Customer Engagement)
    Route::get('/products', function () {
        return view('kouventa-pages::index', [
            'category' => 'products',
            'heading' => 'Products',
            'subheading' => 'Manage Artificial Intelligence and Customer Engagement product pages with structured sections and bilingual support.',
        ]);
    })->name('products');

    Route::get('/products/{id}/edit', function ($id) {
        $page = KouventaPage::findOrFail($id);

        return view('kouventa-pages::edit', ['id' => $id, 'page' => $page]);
    })->whereNumber('id')->name('products.edit');

    // 3. Pricing
    Route::get('/pricing', function () {
        return view('kouventa-pages::index', [
            'category' => 'pricing',
            'heading' => 'Pricing',
            'subheading' => 'Manage Pricing plans, comparison table, FAQs, and CTA sections with bilingual support.',
        ]);
    })->name('pricing');

    Route::get('/pricing/{id}/edit', function ($id) {
        $page = KouventaPage::findOrFail($id);

        return view('kouventa-pages::edit', ['id' => $id, 'page' => $page]);
    })->whereNumber('id')->name('pricing.edit');

    // 4. Clients (They Trusted Us)
    Route::get('/clients', function () {
        return view('kouventa-pages::clients');
    })->name('clients');

    // Legacy / Fallback routes
    Route::get('/', function () {
        return redirect()->route('admin.kouventa-pages.solutions');
    })->name('index');

    Route::get('/{id}/edit', function ($id) {
        $page = KouventaPage::findOrFail($id);

        if (str_starts_with($page->slug, 'solutions/')) {
            return redirect()->route('admin.kouventa-pages.solutions.edit', $page->id);
        }
        if (str_starts_with($page->slug, 'product/')) {
            return redirect()->route('admin.kouventa-pages.products.edit', $page->id);
        }

        return redirect()->route('admin.kouventa-pages.pricing.edit', $page->id);
    })->whereNumber('id')->name('edit');
});
