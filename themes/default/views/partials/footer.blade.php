<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            {{-- Brand --}}
            <div class="footer-brand">
                <a href="{{ url('/') }}" class="footer-logo">
                    <span class="footer-logo-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    </span>
                    <span>{{ setting('site_name', config('app.name', 'CMS')) }}</span>
                </a>
                @if($tagline = setting('site_tagline'))
                    <p class="footer-tagline">{{ $tagline }}</p>
                @else
                    <p class="footer-tagline">Build something amazing with your new CMS.</p>
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
                        <li>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                        </li>
                    @endif
                    @if($phone = setting('contact_phone'))
                        <li>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <a href="tel:{{ $phone }}">{{ $phone }}</a>
                        </li>
                    @endif
                    @if(!setting('contact_email') && !setting('contact_phone'))
                        <li class="footer-contact-empty">No contact info configured</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ setting('site_name', config('app.name', 'CMS')) }}. All rights reserved.</p>
        </div>
    </div>
</footer>
