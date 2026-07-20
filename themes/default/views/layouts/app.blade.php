<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Site Kit Snippet --}}
    @if(view()->exists('google-site-kit::partials.tracking-snippet'))
        @include('google-site-kit::partials.tracking-snippet')
    @endif

    <title>@yield('title', setting('site_name', config('app.name', 'CMS')))</title>

    @if(setting('site_favicon'))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . setting('site_favicon')) }}">
    @endif

    {{-- SEO --}}
    @stack('meta')

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Theme CSS --}}
    <link rel="stylesheet" href="{{ asset('themes/default/assets/css/theme.css') }}">

    @livewireStyles
    @stack('styles')
</head>
<body class="@yield('body-class')">

    {{-- Header --}}
    @include($activeTheme->slug . '::partials.header')

    {{-- Main Content --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include($activeTheme->slug . '::partials.footer')

    @livewireScripts

    {{-- Sticky nav --}}
    <script>
    (function(){
        var last=0, nav=document.querySelector('.site-header');
        if(!nav)return;
        window.addEventListener('scroll',function(){
            var top=window.pageYOffset||document.documentElement.scrollTop;
            nav.classList.toggle('scrolled',top>40);
            nav.classList.toggle('nav-hidden',top>last&&top>120);
            last=top<=0?0:top;
        },{passive:true});
    })();
    </script>

    {{-- Mobile menu toggle --}}
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        var btn=document.querySelector('.mobile-toggle');
        var menu=document.querySelector('.nav-links');
        if(btn&&menu){
            btn.addEventListener('click',function(){
                menu.classList.toggle('open');
                btn.classList.toggle('active');
                btn.setAttribute('aria-expanded', menu.classList.contains('open'));
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
