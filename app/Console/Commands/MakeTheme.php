<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Scaffold a new CMS theme with standard directory structure.
 *
 * Usage:
 *   php artisan make:theme starter
 *   php artisan make:theme corporate --author="John Doe"
 */
class MakeTheme extends Command
{
    protected $signature = 'make:theme
        {slug : The theme slug (kebab-case, e.g. starter)}
        {--author= : Theme author name}';

    protected $description = 'Scaffold a new CMS theme with standard directory structure.';

    public function handle(): int
    {
        $slug = Str::slug($this->argument('slug'));
        $themePath = base_path("themes/{$slug}");

        if (File::exists($themePath)) {
            $this->error("Theme directory already exists: themes/{$slug}");

            return self::FAILURE;
        }

        $name = Str::title(str_replace('-', ' ', $slug));
        $author = $this->option('author') ?? 'CMS Team';

        $this->info("Creating theme: {$name} ({$slug})");

        // Create directory structure
        $directories = [
            'views/layouts',
            'views/pages',
            'views/partials',
            'assets/css',
            'assets/js',
            'assets/images',
        ];

        foreach ($directories as $dir) {
            File::makeDirectory("{$themePath}/{$dir}", 0755, true);
        }

        // Generate theme.json
        File::put("{$themePath}/theme.json", $this->stubThemeJson($name, $slug, $author));

        // Generate layouts/app.blade.php
        File::put("{$themePath}/views/layouts/app.blade.php", $this->stubLayout($name, $slug));

        // Generate pages/home.blade.php
        File::put("{$themePath}/views/pages/home.blade.php", $this->stubHomePage($slug));

        // Generate pages/single.blade.php
        File::put("{$themePath}/views/pages/single.blade.php", $this->stubSinglePage($slug));

        // Generate partials/header.blade.php
        File::put("{$themePath}/views/partials/header.blade.php", $this->stubHeader($slug));

        // Generate partials/footer.blade.php
        File::put("{$themePath}/views/partials/footer.blade.php", $this->stubFooter());

        // Generate assets/css/theme.css
        File::put("{$themePath}/assets/css/theme.css", $this->stubCss());

        $this->newLine();
        $this->info('Theme scaffolded successfully!');
        $this->newLine();

        $this->table(['File', 'Purpose'], [
            ['theme.json', 'Theme manifest (name, version, page templates)'],
            ['views/layouts/app.blade.php', 'Base HTML layout'],
            ['views/pages/home.blade.php', 'Homepage template'],
            ['views/pages/single.blade.php', 'Default page template'],
            ['views/partials/header.blade.php', 'Site header/navigation'],
            ['views/partials/footer.blade.php', 'Site footer'],
            ['assets/css/theme.css', 'Theme stylesheet'],
        ]);

        $this->newLine();
        $this->comment('Next steps:');
        $this->line('  1. Go to Admin → Appearance → Themes');
        $this->line("  2. Activate the \"{$name}\" theme");
        $this->line("  3. Edit views in themes/{$slug}/views/");

        return self::SUCCESS;
    }

    protected function stubThemeJson(string $name, string $slug, string $author): string
    {
        $data = [
            'name' => $name,
            'slug' => $slug,
            'version' => '1.0.0',
            'description' => 'A starter theme for the CMS.',
            'author' => $author,
            'screenshot' => 'screenshot.png',
            'supports' => [
                'pages',
                'menus',
            ],
            'page_templates' => [
                'default' => [
                    'label' => 'Default',
                    'description' => 'Standard page with flexible block content',
                    'blocks' => [],
                ],
                'home' => [
                    'label' => 'Homepage',
                    'description' => 'Main landing page',
                    'blocks' => [
                        ['name' => 'hero_title', 'type' => 'text', 'label' => 'Hero Title', 'default' => 'Welcome'],
                        ['name' => 'hero_subtitle', 'type' => 'textarea', 'label' => 'Hero Subtitle', 'default' => 'Your website tagline goes here.'],
                        ['name' => 'hero_image', 'type' => 'media', 'label' => 'Hero Image', 'default' => ''],
                    ],
                ],
            ],
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n";
    }

    protected function stubLayout(string $name, string $slug): string
    {
        return <<<'BLADE'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $activeTheme->name ?? 'CMS')</title>

    {{-- SEO Meta --}}
    @stack('meta')

    {{-- Theme Stylesheet --}}
    <link rel="stylesheet" href="{{ asset('themes/' . ($activeTheme->slug ?? 'SLUG') . '/assets/css/theme.css') }}">

    {{-- Additional Styles --}}
    @stack('styles')

    @livewireStyles
</head>
<body class="@yield('body-class')">

    @include($activeTheme->slug . '::partials.header')

    <main>
        @yield('content')
    </main>

    @include($activeTheme->slug . '::partials.footer')

    @livewireScripts

    {{-- Additional Scripts --}}
    @stack('scripts')
</body>
</html>
BLADE;
    }

    protected function stubHomePage(string $slug): string
    {
        return <<<'BLADE'
@extends($activeTheme->slug . '::layouts.app')

@section('title', $page->title ?? 'Home')

@section('content')
<section class="hero">
    <div class="container">
        <h1>{{ $page?->block('hero_title') ?? 'Welcome' }}</h1>
        <p>{{ $page?->block('hero_subtitle') ?? 'Your website tagline goes here.' }}</p>
    </div>
</section>

<section class="content">
    <div class="container">
        @if($page && $page->blocks->count())
            @foreach($page->blocks as $block)
                <div class="block block--{{ $block->type }}">
                    {!! $block->rendered_content !!}
                </div>
            @endforeach
        @endif
    </div>
</section>
@endsection
BLADE;
    }

    protected function stubSinglePage(string $slug): string
    {
        return <<<'BLADE'
@extends($activeTheme->slug . '::layouts.app')

@section('title', $page->title ?? 'Page')

@section('content')
<article class="page-content">
    <div class="container">
        <h1>{{ $page->title }}</h1>

        @if($page->blocks->count())
            @foreach($page->blocks as $block)
                <div class="block block--{{ $block->type }}">
                    {!! $block->rendered_content !!}
                </div>
            @endforeach
        @endif
    </div>
</article>
@endsection
BLADE;
    }

    protected function stubHeader(string $slug): string
    {
        return <<<'BLADE'
<header class="site-header">
    <div class="container">
        <a href="{{ url('/') }}" class="site-logo">
            @if($logo = setting('site_logo'))
                <img src="{{ asset('storage/' . $logo) }}" alt="{{ setting('site_name', config('app.name')) }}">
            @else
                {{ setting('site_name', config('app.name')) }}
            @endif
        </a>

        <nav class="site-nav">
            {{-- Navigation items will be rendered here --}}
            <a href="{{ url('/') }}">Home</a>
        </nav>
    </div>
</header>
BLADE;
    }

    protected function stubFooter(): string
    {
        return <<<'BLADE'
<footer class="site-footer">
    <div class="container">
        <p>&copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}. All rights reserved.</p>
    </div>
</footer>
BLADE;
    }

    protected function stubCss(): string
    {
        return <<<'CSS'
/* ──────────────────────────────────────────
   Theme Base Styles
   ────────────────────────────────────────── */

:root {
    --color-primary: #2563eb;
    --color-primary-dark: #1d4ed8;
    --color-bg: #ffffff;
    --color-text: #1f2937;
    --color-text-muted: #6b7280;
    --color-border: #e5e7eb;
    --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
    --container-max: 1200px;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: var(--font-sans);
    color: var(--color-text);
    background: var(--color-bg);
    line-height: 1.6;
}

.container {
    max-width: var(--container-max);
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Header */
.site-header {
    padding: 1rem 0;
    border-bottom: 1px solid var(--color-border);
}

.site-header .container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.site-logo {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--color-text);
    text-decoration: none;
}

.site-logo img {
    max-height: 40px;
    width: auto;
}

.site-nav a {
    color: var(--color-text-muted);
    text-decoration: none;
    margin-left: 1.5rem;
    transition: color 0.2s;
}

.site-nav a:hover {
    color: var(--color-primary);
}

/* Hero */
.hero {
    padding: 4rem 0;
    text-align: center;
    background: linear-gradient(135deg, #f8fafc, #eef2ff);
}

.hero h1 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
}

.hero p {
    font-size: 1.125rem;
    color: var(--color-text-muted);
    max-width: 600px;
    margin: 0 auto;
}

/* Content */
.content, .page-content {
    padding: 3rem 0;
}

.page-content h1 {
    font-size: 2rem;
    margin-bottom: 1.5rem;
}

/* Footer */
.site-footer {
    padding: 2rem 0;
    border-top: 1px solid var(--color-border);
    text-align: center;
    color: var(--color-text-muted);
    font-size: 0.875rem;
}
CSS;
    }
}
