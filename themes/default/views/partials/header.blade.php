<header class="site-header">
    <div class="container header-inner">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="site-logo">
            @if($logo = setting('site_logo'))
                <img src="{{ asset('storage/' . $logo) }}" alt="{{ setting('site_name', config('app.name')) }}">
            @else
                <span class="logo-text">{{ setting('site_name', config('app.name', 'CMS')) }}</span>
            @endif
        </a>

        {{-- Desktop Navigation --}}
        <nav class="nav-links" aria-label="Main navigation">
            @php
                $menuItems = \App\Models\MenuItem::whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();
            @endphp

            @foreach($menuItems ?? [] as $item)
                @if($item->url)
                    <a href="{{ $item->url }}"
                       class="{{ request()->is(ltrim($item->url, '/') . '*') ? 'active' : '' }}">
                        {{ $item->title }}
                    </a>
                @elseif($item->route && Route::has($item->route))
                    <a href="{{ route($item->route) }}"
                       class="{{ request()->routeIs($item->route . '*') ? 'active' : '' }}">
                        {{ $item->title }}
                    </a>
                @endif
            @endforeach

            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
        </nav>

        {{-- Mobile Toggle --}}
        <button class="mobile-toggle" aria-label="Toggle menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
