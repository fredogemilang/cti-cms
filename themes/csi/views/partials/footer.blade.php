@php
    use Themes\Csi\Support\CsiDataProvider;

    $currentLocale = app()->getLocale();
    $siteName = setting('site_name', 'PT Converion Solution Informatica');

    $footerPages = CsiDataProvider::menuItems('footer_pages');
    $footerSolutions = CsiDataProvider::menuItems('footer_solutions');
    $footerOther = CsiDataProvider::menuItems('footer_other');
    $postsActive = function_exists('is_plugin_active') && is_plugin_active('posts');
@endphp

<footer class="bg-[#2A303C] text-white pt-16 pb-8 border-t border-slate-700/50">
  <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px]">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-12 gap-8 pb-12">
      <!-- Col 1: Logo, Office & Social Media -->
      <div class="col-span-2 md:col-span-4 lg:col-span-5 flex flex-col justify-start">
        <div class="mb-4">
          <img src="{{ theme_asset('images/logo-footer.png') }}" alt="{{ $siteName }}" class="h-12 sm:h-14 w-auto object-contain" />
        </div>

        <span class="block text-xs font-bold text-white mb-1">{{ t('footer.city_label', 'Jakarta') }}</span>
        <p class="text-xs text-white/70 leading-relaxed max-w-[340px]">
          {!! nl2br(e(setting('contact_address', "Centennial Tower 12th Floor,\nJl. Jend. Gatot Subroto Kav. 24 – 25, Jakarta, 12930, Indonesia"))) !!}
        </p>

        <!-- Social media icons -->
        <div class="flex items-center gap-3 mt-3.5">
          @if(setting('social_linkedin', 'https://linkedin.com/company/converion'))
            <a href="{{ setting('social_linkedin', 'https://linkedin.com/company/converion') }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 hover:bg-[#04B1C7] flex items-center justify-center transition-colors" aria-label="LinkedIn">
              <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.35a1.64 1.64 0 1 0 0 3.28 1.64 1.64 0 0 0 0-3.28Z"/></svg>
            </a>
          @endif
          @if(setting('social_instagram', 'https://www.instagram.com/converionid/'))
            <a href="{{ setting('social_instagram', 'https://www.instagram.com/converionid/') }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 hover:bg-[#04B1C7] flex items-center justify-center transition-colors" aria-label="Instagram">
              <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
            </a>
          @endif
          @if(setting('social_youtube', 'https://www.youtube.com/channel/UCF1I-9jR-FxXz7PPeatYa1w'))
            <a href="{{ setting('social_youtube', 'https://www.youtube.com/channel/UCF1I-9jR-FxXz7PPeatYa1w') }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 hover:bg-[#04B1C7] flex items-center justify-center transition-colors" aria-label="YouTube">
              <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
          @endif
          @if(setting('social_facebook', 'https://www.facebook.com/converionID/'))
            <a href="{{ setting('social_facebook', 'https://www.facebook.com/converionID/') }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 hover:bg-[#04B1C7] flex items-center justify-center transition-colors" aria-label="Facebook">
              <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
          @endif
          @if(setting('social_tiktok', 'https://www.tiktok.com/@converionid'))
            <a href="{{ setting('social_tiktok', 'https://www.tiktok.com/@converionid') }}" target="_blank" rel="noopener noreferrer" class="w-8 h-8 rounded-full bg-white/10 hover:bg-[#04B1C7] flex items-center justify-center transition-colors" aria-label="TikTok">
              <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
            </a>
          @endif
        </div>
      </div>

      <!-- Col 2: Pages (lg:col-span-2) -->
      <div class="lg:col-span-2">
        <span class="block text-xs font-semibold text-white tracking-[0.25em] uppercase mb-4">{{ t('footer.pages_heading', 'PAGES') }}</span>
        <ul class="space-y-2.5 text-xs text-white/70">
          @foreach($footerPages as $item)
            @php
              $rawUrl = trim((string) ($item->getRawOriginal('url') ?? ''));
              if (! $postsActive && str_contains($rawUrl, 'blog')) {
                  continue;
              }
            @endphp
            <li>
              <a href="{{ $item->resolvedUrl($currentLocale) }}" target="{{ $item->target }}" class="hover:text-white transition-colors">{{ $item->localizedTitle($currentLocale) }}</a>
            </li>
          @endforeach
        </ul>
      </div>

      <!-- Col 3: Solutions (lg:col-span-3) -->
      <div class="lg:col-span-3">
        <span class="block text-xs font-semibold text-white tracking-[0.25em] uppercase mb-4">{{ t('footer.solutions_heading', 'SOLUTIONS') }}</span>
        <ul class="space-y-2.5 text-xs text-white/70">
          @foreach($footerSolutions as $item)
            <li>
              <a href="{{ $item->resolvedUrl($currentLocale) }}" target="{{ $item->target }}" class="hover:text-white transition-colors">{{ $item->localizedTitle($currentLocale) }}</a>
            </li>
          @endforeach
        </ul>
      </div>

      <!-- Col 4: Other (lg:col-span-2) -->
      <div class="lg:col-span-2">
        <span class="block text-xs font-semibold text-white tracking-[0.25em] uppercase mb-4">{{ t('footer.other_heading', 'OTHER') }}</span>
        <ul class="space-y-2.5 text-xs text-white/70">
          @foreach($footerOther as $item)
            <li>
              <a href="{{ $item->resolvedUrl($currentLocale) }}" target="{{ $item->target }}" class="hover:text-white transition-colors">{{ $item->localizedTitle($currentLocale) }}</a>
            </li>
          @endforeach
        </ul>
      </div>
    </div>

    <!-- Copyright Bottom Bar -->
    <div class="pt-8 border-t border-white/10 text-center text-xs text-white/50">
      {{ t('footer.copyright_prefix', 'Copyright :year', ['year' => date('Y')]) }}
      <strong class="text-white font-bold">PT {{ $siteName }}</strong>.
      {{ t('footer.copyright_suffix', 'All Rights Reserved.') }}
    </div>
  </div>
</footer>
