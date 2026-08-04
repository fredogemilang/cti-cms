@php
    $siteLogoSetting = setting('site_logo');
    $siteLogoUrl = $siteLogoSetting ? resolve_block_asset($siteLogoSetting) : asset('themes/cdt/assets/cropped-logo-cdt-D0j3NVKg.webp');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ t('errors.500_title', '500 - Server Error') }} | {{ setting('site_name', 'Central Data Technology') }}</title>
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
        <a href="{{ localized_url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-zinc-600 hover:text-[#e30613] transition-colors bg-white px-3.5 py-2 rounded-xl border border-zinc-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>{{ t('errors.back', 'Back to Home') }}</span>
        </a>
    </header>

    <!-- Main Content Card -->
    <main class="relative z-10 my-auto py-12 px-6">
        <div class="max-w-xl mx-auto text-center">
            
            <!-- 500 Decorative Badge -->
            <div class="relative inline-flex items-center justify-center mb-8">
                <div class="absolute -inset-4 bg-gradient-to-r from-red-500/20 to-red-600/20 rounded-full blur-xl animate-pulse"></div>
                <div class="relative w-28 h-28 sm:w-32 sm:h-32 rounded-3xl bg-gradient-to-b from-white to-red-50/50 border border-red-100 shadow-xl shadow-red-950/5 flex items-center justify-center flex-col gap-1">
                    <span class="text-4xl sm:text-5xl font-extrabold font-heading text-[#e30613] tracking-tight">500</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Server Error</span>
                </div>
            </div>

            <!-- Headline -->
            <h1 class="text-3xl sm:text-4xl font-extrabold font-heading text-zinc-900 tracking-tight mb-4">
                {{ t('errors.500_heading', 'Internal Server Error') }}
            </h1>
            
            <p class="text-zinc-600 text-base sm:text-lg leading-relaxed mb-8 max-w-md mx-auto">
                {{ t('errors.500_message', 'Something went wrong on our server. Our technical team has been notified and is working to resolve it.') }}
            </p>

            <!-- Primary CTAs -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mb-12">
                <a href="{{ localized_url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-[#e30613] hover:bg-[#c00510] text-white font-semibold shadow-lg shadow-red-600/25 transition-all transform hover:-translate-y-0.5 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>{{ t('errors.back_to_home', 'Back to Home') }}</span>
                </a>
                <a href="{{ localized_url('/contact') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-white hover:bg-zinc-100 text-zinc-800 font-semibold border border-zinc-200 shadow-sm transition-all text-sm">
                    <span>{{ t('errors.contact_support', 'Contact Support') }}</span>
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <!-- Quick Navigation Shortcuts -->
            <div class="pt-8 border-t border-zinc-200/80">
                <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-4">Or explore popular sections</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-left">
                    <a href="{{ localized_url('/technology-alliance') }}" class="p-3.5 rounded-xl bg-white border border-zinc-200/80 hover:border-red-200 hover:shadow-md transition-all group">
                        <div class="text-xs font-bold text-zinc-900 group-hover:text-[#e30613] transition-colors mb-0.5">Technology Alliance</div>
                        <div class="text-[11px] text-zinc-500">Explore partner solutions</div>
                    </a>
                    <a href="{{ localized_url('/careers') }}" class="p-3.5 rounded-xl bg-white border border-zinc-200/80 hover:border-red-200 hover:shadow-md transition-all group">
                        <div class="text-xs font-bold text-zinc-900 group-hover:text-[#e30613] transition-colors mb-0.5">Careers</div>
                        <div class="text-[11px] text-zinc-500">Join our team</div>
                    </a>
                    <a href="{{ localized_url('/about') }}" class="col-span-2 sm:col-span-1 p-3.5 rounded-xl bg-white border border-zinc-200/80 hover:border-red-200 hover:shadow-md transition-all group">
                        <div class="text-xs font-bold text-zinc-900 group-hover:text-[#e30613] transition-colors mb-0.5">About Us</div>
                        <div class="text-[11px] text-zinc-500">Learn about CDT</div>
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer Copyright -->
    <footer class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 text-center text-xs text-zinc-400">
        &copy; {{ date('Y') }} {{ setting('site_name', 'Central Data Technology') }}. All rights reserved.
    </footer>

</body>
</html>
