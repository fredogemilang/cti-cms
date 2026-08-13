@php
    $blogSlug = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::getArchiveSlug(app()->getLocale()) : 'blog-news';
    $blogTitle = class_exists(\Plugins\Posts\Models\Setting::class) ? \Plugins\Posts\Models\Setting::getArchiveTitle(app()->getLocale()) : t('nav.blog_news', 'Blog & News');
    $blogUrl = localized_url('/' . $blogSlug);
@endphp

<footer class="relative text-white py-16 bg-footer-image bg-cover bg-center">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

      <!-- Back to top button - Fixed Position -->
      <a href="#" id="back-to-top" title="Back to top" aria-label="Back to top" class="fixed bottom-8 left-8 z-50 hidden lg:flex flex-col items-center group opacity-0 pointer-events-none translate-y-4 transition-all duration-300">
        <div class="w-12 h-12 bg-[#b82d25] rounded-full flex items-center justify-center text-white shadow-lg group-hover:bg-red-700 transition">
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
        </div>
        <span class="text-[#b82d25] text-[10px] font-semibold mt-2 tracking-wide">Back To Top</span>
      </a>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-24 pl-0 lg:pl-12">

        <!-- Column 1: Address -->
        <div>
          <span class="text-white text-base font-bold mb-3 block">{{ t('footer.address', 'Address') }}</span>
          <div class="w-8 h-0.5 bg-[#b82d25] mb-8"></div>

          <div class="flex gap-4 text-sm mb-6 text-white/90">
            <div class="bg-white text-zinc-900 rounded-full w-6 h-6 flex items-center justify-center shrink-0">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
            </div>
            <p class="leading-relaxed">{{ setting('seo_org_address', setting('contact_address', 'Centennial Tower 12th Floor Jl. Jend. Gatot Subroto Kav. 24-25 Jakarta, 12930. Indonesia')) }}</p>
          </div>

          <div class="flex gap-4 text-sm mb-6 items-center text-white/90">
            <div class="bg-white text-zinc-900 rounded-full w-6 h-6 flex items-center justify-center shrink-0">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
            </div>
            <p>{!! safe_phone(setting('seo_org_phone', setting('site_phone', '(+62 21) 80622200')), 'hover:text-white transition') !!}</p>
          </div>

          <div class="flex gap-4 text-sm items-center text-white/90">
            <div class="bg-white text-zinc-900 rounded-full w-6 h-6 flex items-center justify-center shrink-0">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
            </div>
            <p>{!! safe_email(setting('seo_org_email', setting('site_email', 'marketing@centraldatatech.com')), 'hover:text-white transition') !!}</p>
          </div>
        </div>

        <!-- Column 2: Socials -->
        <div>
          <span class="text-white text-base font-bold mb-3 block">{{ t('footer.follow_us', 'Follow Us On Social Media') }}</span>
          <div class="w-8 h-0.5 bg-[#b82d25] mb-8"></div>

          <div class="flex gap-3 mb-6">
            <!-- LinkedIn -->
            <a href="{{ setting('seo_linkedin_url', setting('social_linkedin', 'https://www.linkedin.com/company/central-data-technology-pt-/')) }}" title="LinkedIn Central Data Technology" aria-label="LinkedIn" target="_blank" rel="noopener" class="w-8 h-8 bg-white text-zinc-900 rounded-full flex items-center justify-center hover:bg-zinc-200 transition">
              <x-icon name="linkedin" class="w-4 h-4 text-zinc-900" />
            </a>
            <!-- Facebook -->
            <a href="{{ setting('seo_facebook_url', setting('social_facebook', 'https://www.facebook.com/centraldatatech')) }}" title="Facebook Central Data Technology" aria-label="Facebook" target="_blank" rel="noopener" class="w-8 h-8 bg-white text-zinc-900 rounded-full flex items-center justify-center hover:bg-zinc-200 transition">
              <x-icon name="facebook" class="w-4 h-4 text-zinc-900" />
            </a>
            <!-- Instagram -->
            <a href="{{ setting('seo_instagram_url', setting('social_instagram', 'https://www.instagram.com/centraldataid/')) }}" title="Instagram Central Data Technology" aria-label="Instagram" target="_blank" rel="noopener" class="w-8 h-8 bg-white text-zinc-900 rounded-full flex items-center justify-center hover:bg-zinc-200 transition">
              <x-icon name="instagram" class="w-4 h-4 text-zinc-900" />
            </a>
            <!-- Twitter/X -->
            <a href="{{ setting('seo_twitter_handle', setting('social_twitter', 'https://twitter.com/centraldataID')) }}" title="Twitter Central Data Technology" aria-label="Twitter" target="_blank" rel="noopener" class="w-8 h-8 bg-white text-zinc-900 rounded-full flex items-center justify-center hover:bg-zinc-200 transition">
              <x-icon name="twitter" class="w-4 h-4 text-zinc-900" />
            </a>
            <!-- YouTube -->
            <a href="{{ setting('seo_youtube_url', setting('social_youtube', 'https://www.youtube.com/channel/UCG0E2Kc-QvMRLJ70Q-XeemA/featured')) }}" title="YouTube Central Data Technology" aria-label="YouTube" target="_blank" rel="noopener" class="w-8 h-8 bg-white text-zinc-900 rounded-full flex items-center justify-center hover:bg-zinc-200 transition">
              <x-icon name="youtube" class="w-4 h-4 text-zinc-900" />
            </a>
          </div>

          <p class="text-[13px] text-white/90 leading-relaxed mb-4 max-w-[250px]">
            {{ t('footer.social_desc', 'Keep up to date with all the latest digital technology news and trends.') }}
          </p>

          <a href="#" @click.prevent="$dispatch('open-subscribe')" title="{{ t('newsletter.modal_title', 'Subscribe to Newsletter') }}" aria-label="{{ t('newsletter.modal_title', 'Subscribe to Newsletter') }}" class="text-white text-sm font-bold italic underline hover:text-gray-300">{{ t('newsletter.footer_link', 'Subscribe') }}</a>
        </div>

        <!-- Column 3: Quick Link -->
        <div>
          <span class="text-white text-base font-bold mb-3 block">{{ t('footer.quick_links', 'Quick Link') }}</span>
          <div class="w-8 h-0.5 bg-[#b82d25] mb-8"></div>

          <ul class="space-y-4 text-[13px] text-white/90">
            <li><a href="{{ localized_url('/about-us') }}" title="{{ t('nav.about_us', 'About Us') }}" class="hover:text-white transition">{{ t('nav.about_us', 'About Us') }}</a></li>
            <li><a href="{{ $blogUrl }}" title="{{ $blogTitle }}" class="hover:text-white transition">{{ $blogTitle }}</a></li>
            <li><a href="{{ localized_url('/careers') }}" title="{{ t('nav.careers', 'Careers') }}" class="hover:text-white transition">{{ t('nav.careers', 'Careers') }}</a></li>
            <li><a href="{{ localized_url('/contact-us') }}" title="{{ t('nav.contact', 'Contact Us') }}" class="hover:text-white transition">{{ t('nav.contact', 'Contact Us') }}</a></li>
          </ul>
        </div>
      </div>

      <div class="mt-20 pt-6 text-center text-xs text-white/80 pb-6">
        <p>{{ t('footer.copyright', 'Copyright :year © All Right Reserved by :site', ['year' => date('Y'), 'site' => setting('site_name', 'Central Data Technology')]) }}</p>
      </div>
    </div>

    <!-- Subscription Modal (Alpine) -->
    <div x-data="{ subscribeOpen: false, subscribeSuccess: false }" @open-subscribe.window="subscribeOpen = true"
      x-on:keydown.escape.window="subscribeOpen = false"
      x-effect="if (subscribeOpen) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; }">

      <!-- Backdrop -->
      <div x-show="subscribeOpen"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="modal-sheet-backdrop fixed inset-0 z-[100] bg-black/60 backdrop-blur-sm" style="display: none;"
        @click="subscribeOpen = false; subscribeSuccess = false"></div>

      <!-- Content -->
      <div x-show="subscribeOpen"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-full lg:translate-y-0 lg:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 lg:scale-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0 lg:scale-100"
        x-transition:leave-end="opacity-0 translate-y-full lg:translate-y-0 lg:scale-95"
        class="modal-sheet-content fixed inset-0 z-[101] flex items-end lg:items-center justify-center lg:p-6"
        style="display: none;">

        <div class="bg-white rounded-t-3xl lg:rounded-2xl p-8 w-full lg:max-w-md shadow-2xl relative">
          <!-- Drag Handle (mobile only) -->
          <div class="w-12 h-1 bg-gray-200 rounded-full mx-auto -mt-4 mb-4 lg:hidden"></div>

          <!-- Close button -->
          <button @click="subscribeOpen = false; subscribeSuccess = false"
            class="absolute top-4 right-4 p-2 text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 rounded-full transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>

          <!-- Form -->
          <div x-show="!subscribeSuccess">
            <p class="text-2xl font-bold text-zinc-900 mb-2 font-prompt">{{ t('newsletter.modal_title', 'Subscribe to our Newsletter') }}</p>
            <p class="text-sm text-zinc-500 mb-6">{{ t('newsletter.modal_subtitle', 'Receive the latest insights and digital technology trends directly in your inbox.') }}</p>

            @php
              $newsletterForm = get_assigned_form('newsletter_form');
            @endphp

            @if($newsletterForm)
              @include('cdt::partials.tailwind-form', ['form' => $newsletterForm, 'variant' => 'light'])
            @else
              <form @submit.prevent="subscribeSuccess = true" class="space-y-4 text-left">
                <div>
                  <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-2">Full Name</label>
                  <input type="text" required placeholder="John Doe"
                    class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 text-sm focus:outline-none focus:border-[#b82d25] focus:bg-white focus:ring-4 focus:ring-red-500/10 transition-all">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-2">Email Address</label>
                  <input type="email" required placeholder="john@example.com"
                    class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 text-sm focus:outline-none focus:border-[#b82d25] focus:bg-white focus:ring-4 focus:ring-red-500/10 transition-all">
                </div>
                <button type="submit"
                  class="w-full py-3 bg-gradient-to-r from-[#b82d25] to-red-600 text-white font-bold rounded-xl shadow-lg hover:shadow-red-500/20 hover:from-red-600 hover:to-red-700 transition duration-300 mt-2">
                  Subscribe Now
                </button>
              </form>
            @endif
          </div>

          <!-- Success State -->
          <div x-show="subscribeSuccess" class="text-center py-6" style="display: none;">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center text-[#b82d25] mx-auto mb-4">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-2xl font-bold text-zinc-900 mb-2 font-prompt">Subscription Successful!</p>
            <p class="text-sm text-zinc-500">Thank you for subscribing. We will keep you updated with our latest news.</p>
          </div>
        </div>
      </div>
    </div>

</footer>
