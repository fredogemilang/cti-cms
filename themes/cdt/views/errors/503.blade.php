@php
    $siteLogoSetting = setting('site_logo');
    $siteLogoUrl = $siteLogoSetting ? resolve_block_asset($siteLogoSetting) : asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.webp');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ t('errors.503_title', '503 - System Maintenance') }} | {{ setting('site_name', 'Central Data Technology') }}</title>
    @if(setting('site_favicon'))
        <link rel="icon" href="{{ resolve_block_asset(setting('site_favicon')) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Prompt:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .font-heading {
            font-family: 'Prompt', system-ui, -apple-system, sans-serif;
        }
        .bg-grid-pattern {
            background-image: radial-gradient(rgba(227, 6, 19, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-zinc-50 font-sans text-zinc-900 h-full flex flex-col justify-between relative overflow-x-hidden antialiased">

    <!-- Ambient Red Background Glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[500px] bg-gradient-to-b from-red-500/10 via-red-500/0 to-transparent pointer-events-none blur-3xl"></div>
    <div class="absolute inset-0 bg-grid-pattern opacity-60 pointer-events-none"></div>

    <!-- Header Logo Bar -->
    <header class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
        <a href="{{ localized_url('/') }}" class="inline-flex items-center gap-3 transition-opacity hover:opacity-90">
            <img src="{{ $siteLogoUrl }}" alt="{{ setting('site_name', 'Central Data Technology') }}" class="h-10 sm:h-12 w-auto object-contain" />
        </a>
        <div class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-500 bg-white px-3.5 py-2 rounded-xl border border-zinc-200 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
            <span>Maintenance Mode</span>
        </div>
    </header>

    <!-- Main Content Card -->
    <main class="relative z-10 my-auto py-12 px-6">
        <div class="max-w-xl mx-auto text-center">
            
            <!-- 503 Decorative Badge -->
            <div class="relative inline-flex items-center justify-center mb-8">
                <div class="absolute -inset-4 bg-gradient-to-r from-amber-500/20 to-red-500/20 rounded-full blur-xl animate-pulse"></div>
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-3xl bg-gradient-to-b from-white to-amber-50/50 border border-amber-100 shadow-xl shadow-amber-950/5 flex items-center justify-center flex-col gap-1">
                    <span class="text-4xl sm:text-5xl font-extrabold font-heading text-[#e30613] tracking-tight">503</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-amber-600">Maintenance</span>
                </div>
            </div>

            <!-- Headline -->
            <h1 class="text-3xl sm:text-4xl font-extrabold font-heading text-zinc-900 tracking-tight mb-4">
                {{ t('errors.503_heading', 'System Under Maintenance') }}
            </h1>
            
            <p class="text-zinc-600 text-base sm:text-lg leading-relaxed mb-8 max-w-md mx-auto">
                {{ t('errors.503_message', 'We are currently performing scheduled maintenance to enhance your experience. We will be back online shortly.') }}
            </p>

            <!-- Primary CTAs -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-[#e30613] hover:bg-[#c00510] text-white font-semibold shadow-lg shadow-red-600/25 transition-all transform hover:-translate-y-0.5 text-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Refresh Page</span>
                </button>
            </div>

        </div>
    </main>

    <!-- Footer Copyright -->
    <footer class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 text-center text-xs text-zinc-400">
        &copy; {{ date('Y') }} {{ setting('site_name', 'Central Data Technology') }}. All rights reserved.
    </footer>

</body>
</html>
