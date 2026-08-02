<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ t('errors.503_title', 'System Under Maintenance') }} - {{ setting('site_name', 'CDT') }}</title>
    @if(setting('site_favicon'))
        <link rel="icon" href="{{ resolve_block_asset(setting('site_favicon')) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-50 font-sans text-zinc-900 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-lg w-full bg-white rounded-3xl p-8 sm:p-12 border border-zinc-200/80 shadow-xl text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-amber-50 text-amber-600 mb-6 border border-amber-100">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-900 mb-3">
            {{ t('errors.503_heading', 'System Under Maintenance') }}
        </h1>
        <p class="text-zinc-600 mb-8 leading-relaxed">
            {{ t('errors.503_message', 'We are currently performing scheduled maintenance to enhance your experience. We will be back online shortly.') }}
        </p>
        <div class="text-xs text-zinc-400 border-t border-zinc-100 pt-6">
            &copy; {{ date('Y') }} {{ setting('site_name', 'CDT') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
