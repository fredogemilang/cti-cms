<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'OFIS Smart Office') }}</title>
    
    <!-- SEO & Metadata -->
    @if(isset($seo) && is_array($seo))
        <meta name="description" content="{{ $seo['meta_description'] ?? '' }}">
    @endif

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Theme Assets -->
    <link rel="stylesheet" href="{{ theme_asset('main-CBW6bIvn.css') }}">
    <script type="module" src="{{ theme_asset('main-CjwTO6mD.js') }}"></script>
</head>
<body class="font-body bg-white antialiased text-gray-800">

    <!-- Header Navigation -->
    @include('ofis::header')

    <!-- Main Content Area -->
    <main id="content">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('ofis::footer')

    <!-- Floating WhatsApp CTA Button -->
    <a href="https://wa.me/628111925345?text=Hello%20OFIS%20Team"
       target="_blank" rel="noopener"
       class="fixed bottom-6 right-6 z-40 flex items-center gap-2 bg-[#25D366] text-white px-4 py-3 rounded-full shadow-lg hover:scale-105 transition"
       aria-label="WhatsApp">
        <img src="{{ theme_asset('whatsapp-icon-1-150x150.png-DcSjyvTS.webp') }}" alt="WhatsApp" class="w-6 h-6" />
        <span class="hidden md:inline text-sm font-semibold">{{ t('common.whatsapp', 'WhatsApp Click To Chat') }}</span>
    </a>

    @stack('scripts')
</body>
</html>