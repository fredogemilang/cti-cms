<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ThemeAssetTest extends TestCase
{
    public function test_default_theme_has_css_asset_file(): void
    {
        $this->assertFileExists(base_path('themes/default/assets/css/theme.css'));
    }

    public function test_theme_publish_command_publishes_default_theme_assets(): void
    {
        Artisan::call('theme:publish', ['--all' => true, '--force' => true]);

        $this->assertFileExists(public_path('themes/default/assets/css/theme.css'));
    }
}
