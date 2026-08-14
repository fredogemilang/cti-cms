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