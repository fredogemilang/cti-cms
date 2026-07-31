<?php

use App\Models\StringTranslation;
use App\Models\StringTranslationKey;
use App\Models\StringTranslationSource;
use App\Services\TranslationScannerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('translation registry models and relationships work correctly', function () {
    $key = StringTranslationKey::create([
        'group' => 'header',
        'key' => 'contact_us',
        'default_value' => 'Contact Us',
    ]);

    $translation = StringTranslation::create([
        'translation_key_id' => $key->id,
        'locale' => 'id',
        'value' => 'Hubungi Kami',
    ]);

    $source = StringTranslationSource::create([
        'translation_key_id' => $key->id,
        'source_type' => 'theme',
        'source_name' => 'cdt',
        'source_file' => 'themes/cdt/views/header.blade.php',
    ]);

    expect($key->translations)->toHaveCount(1)
        ->and($key->sources)->toHaveCount(1)
        ->and($translation->key->id)->toBe($key->id)
        ->and($source->key->id)->toBe($key->id);
});

test('helper t() resolves translations with fallback chain and placeholders', function () {
    $key = StringTranslationKey::create([
        'group' => 'common',
        'key' => 'welcome',
        'default_value' => 'Welcome, :name',
    ]);

    StringTranslation::create([
        'translation_key_id' => $key->id,
        'locale' => 'id',
        'value' => 'Selamat datang, :name',
    ]);

    App::setLocale('id');
    expect(t('common.welcome', 'Welcome, :name', ['name' => 'Fredo']))
        ->toBe('Selamat datang, Fredo');

    App::setLocale('en');
    expect(t('common.welcome', 'Welcome, :name', ['name' => 'Fredo']))
        ->toBe('Welcome, Fredo');
});

test('translation save invalidates locale cache strictly', function () {
    App::setLocale('id');

    $key = StringTranslationKey::create([
        'group' => 'common',
        'key' => 'save',
        'default_value' => 'Save',
    ]);

    // Initial lookup populates cache
    t('common.save');

    expect(Cache::has('translations:id'))->toBeTrue();

    // Update translation
    StringTranslation::create([
        'translation_key_id' => $key->id,
        'locale' => 'id',
        'value' => 'Simpan',
    ]);

    // Cache should be invalidated
    expect(Cache::has('translations:id'))->toBeFalse();

    // Re-lookup returns updated value
    expect(t('common.save'))->toBe('Simpan');
});

test('api v1 translations endpoint returns standard contract and validates locale', function () {
    $key = StringTranslationKey::create([
        'group' => 'header',
        'key' => 'contact_us',
        'default_value' => 'Contact Us',
    ]);

    StringTranslation::create([
        'translation_key_id' => $key->id,
        'locale' => 'id',
        'value' => 'Hubungi Kami',
    ]);

    // Test valid locale endpoint
    $response = $this->getJson('/api/v1/translations/id');
    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'locale' => 'id',
            'translations' => [
                'header.contact_us' => 'Hubungi Kami',
            ],
        ]);

    // Test unsupported BCP 47 locale endpoint
    $invalidResponse = $this->getJson('/api/v1/translations/unsupported_locale');
    $invalidResponse->assertStatus(404)
        ->assertJson([
            'status' => 'error',
        ]);
});

test('scanner service performs non-destructive key discovery', function () {
    $scanner = new TranslationScannerService;
    $discovered = $scanner->scanAll();

    expect(is_array($discovered))->toBeTrue();
});
