@php
    $postsArchiveSlug = 'blog'; // default fallback
    if (class_exists(\Plugins\Posts\Models\Setting::class)) {
        try {
            $postsArchiveSlug = \Plugins\Posts\Models\Setting::get('archive_slug', 'blog');
        } catch (\Throwable) {
            // Silently fall back to default
        }
    }
@endphp
<nav class="navbar fixed-top py-2 py-lg-3">
    <div class="container d-flex align-items-center">
        <a class="navbar-brand" href="{{ url('/') }}">
            @if(setting('site_logo'))
                <img src="{{ asset('storage/' . setting('site_logo')) }}" alt="{{ setting('site_name', 'iCCom') }} Logo" height="80" fetchpriority="high">
            @else
                <picture>
                    <source media="(max-width: 576px)" srcset="{{ asset('themes/iccom/assets/logo-40h.webp') }}" type="image/webp">
                    <source srcset="{{ asset('themes/iccom/assets/logo-80h.webp') }}" type="image/webp">
                    <img src="{{ asset('themes/iccom/assets/logo.png') }}" alt="iCCom Logo" height="80" width="77" fetchpriority="high">
                </picture>
            @endif
        </a>

        {{-- Always-visible nav pill container (no collapse) --}}
        <div class="nav-pill-container d-flex align-items-center bg-white rounded-pill px-1 px-lg-2 py-1 shadow-sm">
            <a class="nav-link px-2 px-lg-4 py-1 py-lg-2 {{ request()->is('/') ? 'active rounded-pill text-white' : 'text-dark' }}" href="{{ url('/') }}">Home</a>
            <a class="nav-link px-2 px-lg-4 py-1 py-lg-2 {{ request()->is('event*') ? 'active rounded-pill text-white' : 'text-dark' }}" href="{{ url('/event') }}">Events</a>
            <a class="nav-link px-2 px-lg-4 py-1 py-lg-2 {{ request()->is($postsArchiveSlug . '*') ? 'active rounded-pill text-white' : 'text-dark' }}" href="{{ url('/' . $postsArchiveSlug) }}">Blog</a>
        </div>

        {{-- Desktop: full text button --}}
        <div class="d-none d-lg-block">
            <a href="{{ url('/membership') }}" class="btn btn-primary btn-cta rounded-pill px-4">Become a Member</a>
        </div>

        {{-- Mobile: SVG icon only --}}
        <div class="d-lg-none">
            <a href="{{ url('/membership') }}" class="btn btn-primary btn-cta rounded-pill btn-cta-mobile p-2" title="Become a Member">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </a>
        </div>
    </div>
</nav>
