<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            {{-- Brand --}}
            <div class="footer-brand">
                <a href="{{ url('/') }}" class="footer-logo">
                    {{ setting('site_name', config('app.name', 'CMS')) }}
                </a>
                @if($tagline = setting('site_tagline'))
                    <p class="footer-tagline">{{ $tagline }}</p>
                @endif
            </div>

            {{-- Quick Links --}}
            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="{{ url('/') }}">Home</a></li>
                    @php
                        $pages = \App\Models\Page::where('status', 'published')
                            ->where('slug', '!=', 'home')
                            ->orderBy('menu_order')
                            ->take(5)
                            ->get();
                    @endphp
                    @foreach($pages as $p)
                        <li><a href="{{ route('pages.show', $p->slug) }}">{{ $p->title }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div class="footer-contact">
                <h4>Contact</h4>
                <ul>
                    @if($email = setting('contact_email'))
                        <li><a href="mailto:{{ $email }}">{{ $email }}</a></li>
                    @endif
                    @if($phone = setting('contact_phone'))
                        <li><a href="tel:{{ $phone }}">{{ $phone }}</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ setting('site_name', config('app.name', 'CMS')) }}. All rights reserved.</p>
        </div>
    </div>
</footer>
