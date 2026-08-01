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
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
            </a>
            <!-- Facebook -->
            <a href="{{ setting('seo_facebook_url', setting('social_facebook', 'https://www.facebook.com/centraldatatech')) }}" title="Facebook Central Data Technology" aria-label="Facebook" target="_blank" rel="noopener" class="w-8 h-8 bg-white text-zinc-900 rounded-full flex items-center justify-center hover:bg-zinc-200 transition">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
            </a>
            <!-- Instagram -->
            <a href="{{ setting('seo_instagram_url', setting('social_instagram', 'https://www.instagram.com/centraldataid/')) }}" title="Instagram Central Data Technology" aria-label="Instagram" target="_blank" rel="noopener" class="w-8 h-8 bg-white text-zinc-900 rounded-full flex items-center justify-center hover:bg-zinc-200 transition">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            </a>
            <!-- Twitter/X -->
            <a href="{{ setting('seo_twitter_handle', setting('social_twitter', 'https://twitter.com/centraldataID')) }}" title="Twitter Central Data Technology" aria-label="Twitter" target="_blank" rel="noopener" class="w-8 h-8 bg-white text-zinc-900 rounded-full flex items-center justify-center hover:bg-zinc-200 transition">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
            </a>
            <!-- YouTube -->
            <a href="{{ setting('seo_youtube_url', setting('social_youtube', 'https://www.youtube.com/channel/UCG0E2Kc-QvMRLJ70Q-XeemA/featured')) }}" title="YouTube Central Data Technology" aria-label="YouTube" target="_blank" rel="noopener" class="w-8 h-8 bg-white text-zinc-900 rounded-full flex items-center justify-center hover:bg-zinc-200 transition">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
            </a>
          </div>

          <p class="text-[13px] text-white/90 leading-relaxed mb-4 max-w-[250px]">
            {{ t('footer.social_desc', 'Keep up to date with all the latest digital technology news and trends.') }}
          </p>

          <a href="#" id="footer-subscribe-link" title="{{ t('newsletter.modal_title', 'Subscribe to Newsletter') }}" aria-label="{{ t('newsletter.modal_title', 'Subscribe to Newsletter') }}" class="text-white text-sm font-bold italic underline hover:text-gray-300">{{ t('newsletter.footer_link', 'Subscribe') }}</a>
        </div>

        <!-- Column 3: Quick Link -->
        <div>
          <span class="text-white text-base font-bold mb-3 block">{{ t('footer.quick_links', 'Quick Link') }}</span>
          <div class="w-8 h-0.5 bg-[#b82d25] mb-8"></div>

          <ul class="space-y-4 text-[13px] text-white/90">
            <li><a href="{{ localized_url('/about-us') }}" title="{{ t('nav.about_us', 'About Us') }}" class="hover:text-white transition">{{ t('nav.about_us', 'About Us') }}</a></li>
            <li><a href="{{ localized_url('/blog') }}" title="{{ t('nav.blog_news', 'Blog & News') }}" class="hover:text-white transition">{{ t('nav.blog_news', 'Blog & News') }}</a></li>
            <li><a href="{{ localized_url('/careers') }}" title="{{ t('nav.careers', 'Careers') }}" class="hover:text-white transition">{{ t('nav.careers', 'Careers') }}</a></li>
            <li><a href="{{ localized_url('/contact-us') }}" title="{{ t('nav.contact', 'Contact Us') }}" class="hover:text-white transition">{{ t('nav.contact', 'Contact Us') }}</a></li>
          </ul>
        </div>
      </div>

      <div class="mt-20 pt-6 text-center text-xs text-white/80 pb-6">
        <p>{{ t('footer.copyright', 'Copyright :year © All Right Reserved by :site', ['year' => date('Y'), 'site' => setting('site_name', 'Central Data Technology')]) }}</p>
      </div>
    </div>

    <!-- Subscription Modal -->
    <div id="subscribe-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" id="subscribe-modal-backdrop"></div>

      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="subscribe-modal-card">
        <button id="close-subscribe-modal" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div id="subscribe-form-container">
          <p class="text-2xl font-bold text-zinc-900 mb-2 font-prompt">{{ t('newsletter.modal_title', 'Subscribe to our Newsletter') }}</p>
          <p class="text-sm text-zinc-500 mb-6">{{ t('newsletter.modal_subtitle', 'Receive the latest insights and digital technology trends directly in your inbox.') }}</p>

          @php
            $tTheme = active_theme();
            $newsletterFormId = setting("theme_{$tTheme->slug}_form_assignments", [])['newsletter_form'] ?? null;
            $newsletterForm = $newsletterFormId ? \App\Models\Form::where('id', $newsletterFormId)->where('is_active', true)->with('fields')->first() : \App\Models\Form::where('slug', 'newsletter-subscription')->where('is_active', true)->with('fields')->first();
          @endphp

          @if($newsletterForm)
            @include('cdt::partials.tailwind-form', ['form' => $newsletterForm, 'variant' => 'light'])
          @else
            <form id="subscribe-modal-form" class="space-y-4 text-left">
              <div>
                <label for="subscribe-name" class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-2">Full Name</label>
                <input type="text" id="subscribe-name" required placeholder="John Doe"
                  class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 text-sm focus:outline-none focus:border-[#b82d25] focus:bg-white focus:ring-4 focus:ring-red-500/10 transition-all">
              </div>
              <div>
                <label for="subscribe-email" class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" id="subscribe-email" required placeholder="john@example.com"
                  class="w-full px-4 py-3 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 text-sm focus:outline-none focus:border-[#b82d25] focus:bg-white focus:ring-4 focus:ring-red-500/10 transition-all">
              </div>
              <button type="submit"
                class="w-full py-3 bg-gradient-to-r from-[#b82d25] to-red-600 text-white font-bold rounded-xl shadow-lg hover:shadow-red-500/20 hover:from-red-600 hover:to-red-700 transition duration-300 mt-2">
                Subscribe Now
              </button>
            </form>
          @endif
        </div>

        <div id="subscribe-success-container" class="hidden text-center py-6">
          <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center text-[#b82d25] mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          </div>
          <p class="text-2xl font-bold text-zinc-900 mb-2 font-prompt">Subscription Successful!</p>
          <p class="text-sm text-zinc-500">Thank you for subscribing. We will keep you updated with our latest news.</p>
        </div>
      </div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const subscribeLink = document.getElementById("footer-subscribe-link");
        const modal = document.getElementById("subscribe-modal");
        const backdrop = document.getElementById("subscribe-modal-backdrop");
        const card = document.getElementById("subscribe-modal-card");
        const closeBtn = document.getElementById("close-subscribe-modal");
        const form = document.getElementById("subscribe-modal-form");
        const formContainer = document.getElementById("subscribe-form-container");
        const successContainer = document.getElementById("subscribe-success-container");

        function openModal(e) {
          if (e) e.preventDefault();
          if (!modal) return;
          modal.classList.remove("hidden");
          modal.offsetWidth;
          modal.classList.add("opacity-100");
          modal.classList.remove("opacity-0");
          card.classList.add("scale-100", "opacity-100");
          card.classList.remove("scale-95", "opacity-0");
          document.body.style.overflow = "hidden";
        }

        function closeModal() {
          if (!modal) return;
          modal.classList.add("opacity-0");
          modal.classList.remove("opacity-100");
          card.classList.add("scale-95", "opacity-0");
          card.classList.remove("scale-100", "opacity-100");
          setTimeout(function() {
            modal.classList.add("hidden");
            document.body.style.overflow = "";
            if (form) form.reset();
            if (formContainer) formContainer.classList.remove("hidden");
            if (successContainer) successContainer.classList.add("hidden");
          }, 300);
        }

        if (subscribeLink) subscribeLink.addEventListener("click", openModal);
        if (closeBtn) closeBtn.addEventListener("click", closeModal);
        if (backdrop) backdrop.addEventListener("click", closeModal);
        document.addEventListener("keydown", function(e) {
          if (e.key === "Escape" && modal && !modal.classList.contains("hidden")) closeModal();
        });
        if (form) {
          form.addEventListener("submit", function(e) {
            e.preventDefault();
            if (formContainer) formContainer.classList.add("hidden");
            if (successContainer) successContainer.classList.remove("hidden");
          });
        }
      });
    </script>
</footer>
