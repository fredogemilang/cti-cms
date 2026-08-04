<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ t('errors.500_title', 'Server Error') }} - {{ setting('site_name', config('app.name')) }}</title>
    @if(setting('site_favicon'))
        <link rel="icon" href="{{ resolve_block_asset(setting('site_favicon')) }}">
    @endif
    @if(isset($activeTheme) && file_exists(public_path('themes/' . $activeTheme->slug . '/assets/css/theme.css')))
        <link rel="stylesheet" href="{{ asset('themes/' . $activeTheme->slug . '/assets/css/theme.css') }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --color-primary: #2563eb;
            --color-primary-hover: #1d4ed8;
        }
        .btn-theme-primary {
            background-color: var(--color-primary, #2563eb);
            color: #ffffff;
        }
        .btn-theme-primary:hover {
            background-color: var(--color-primary-hover, #1d4ed8);
        }
        .badge-theme {
            background-color: color-mix(in srgb, var(--color-primary, #2563eb) 10%, transparent);
            color: var(--color-primary, #2563eb);
            border-color: color-mix(in srgb, var(--color-primary, #2563eb) 25%, transparent);
        }
    </style>
</head>
<body class="bg-zinc-50 font-sans text-zinc-900 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-white rounded-3xl p-8 sm:p-12 border border-zinc-200/80 shadow-xl text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl badge-theme mb-6 border">
            <span class="text-3xl font-extrabold tracking-tight">500</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-900 mb-3">
            {{ t('errors.500_heading', 'Internal Server Error') }}
        </h1>
        <p class="text-zinc-600 mb-8 leading-relaxed">
            {{ t('errors.500_message', 'Something went wrong on our server. Our team has been notified.') }}
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ localized_url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 rounded-xl btn-theme-primary font-semibold shadow-sm transition-colors text-sm">
                {{ t('errors.back_to_home', 'Back to Home') }}
            </a>
        </div>
        <div class="text-xs text-zinc-400 border-t border-zinc-100 pt-6 mt-8">
            &copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}. All rights reserved.
        </div>
    </div>
</body>
</html>
