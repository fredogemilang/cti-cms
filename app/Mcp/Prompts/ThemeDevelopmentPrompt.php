<?php

namespace App\Mcp\Prompts;

use App\Services\ThemeLoader;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

#[Name('theme-development')]
#[Description('Comprehensive prompt with strict frontend guidelines for developing Blade templates in the active theme.')]
class ThemeDevelopmentPrompt extends Prompt
{
    public function arguments(): array
    {
        return [
            new Argument('template_slug', 'Specific template slug to inspect (optional)', false),
        ];
    }

    public function handle(Request $request): Response
    {
        $theme = app(ThemeLoader::class)->getActiveTheme();
        $themeName = $theme ? $theme->getName() : 'cdt';
        $themePath = $theme ? $theme->getPath() : base_path("themes/{$themeName}");

        $output = <<<TEXT
# CTI CMS Theme Development Prompt & Strict Rules

You are developing templates for the active theme: **{$themeName}** (Path: `{$themePath}`).

## 1. Zero-Approximation & Pixel-Perfect Rule (User Rule #11):
- DILARANG membuat versi MVP / setengah jadi yang menyederhanakan HTML acuan.
- Agen WAJIB menyalin dan menyesuaikan struktur DOM HTML asli 1-to-1 baris demi baris, termasuk seluruh class CSS Tailwind, inline styles, animasi `@keyframes`, SVG icons, dan komponen visual.

## 2. Mandatory Local Asset Rule (No External CDN - User Rule #10):
- Semua aset frontend (CSS, JS library, Swiper, Alpine, font, icon SVG) WAJIB disimpan secara LOKAL di `themes/{theme}/assets/` dan dipanggil via `theme_asset('path/to/asset')`.
- DILARANG menggunakan URL CDN eksternal (jsDelivr, cdnjs, unpkg, dll.).

## 3. Image Rendering System:
- **User-Uploaded Content**: Always render using the `<x-image>` component.
  ```blade
  <x-image :path="\$page->block('hero_image')" alt="Hero banner" class="w-full h-auto" />
  ```
- **Block Assets**: Always wrap storage paths with `resolve_block_asset()`.
  ```blade
  {{ resolve_block_asset(\$page->block('image_key')) }}
  ```
- **Static Theme Icons/Logos**: Use `theme_asset()`.
  ```blade
  <img src="{{ theme_asset('images/logo.svg') }}" alt="Logo" />
  ```

## 4. Translation & Internationalization:
- NEVER hardcode user-facing strings in Blade.
- Always wrap text with the `t('key', 'Default fallback')` helper:
  ```blade
  {{ t('home.learn_more', 'Learn More') }}
  ```

## 5. Block Value Extraction in Page Templates:
- Scalar / String: `\$page->block('block_name')`
- Repeater array: `\$page->repeaterBlock('repeater_name', [])`
- Compound title: `\$page->titleBlock('section_title')` -> returns `['prefix' => '...', 'main' => '...']`
- Compound button: `\$page->buttonBlock('cta_button')` -> returns `['text' => '...', 'url' => '...', 'target' => '...']`
- Compound card: `\$page->cardBlock('info_card')`

## 6. Plugin Fail-Safe Rule (User Rule #15):
- If querying posts or blog entities, always check plugin status first:
  ```blade
  @if (function_exists('is_plugin_active') && is_plugin_active('posts'))
      {{-- Blog posts query / render --}}
  @endif
  ```
TEXT;

        return Response::text($output);
    }
}
