@extends('cdt::layouts.app')

@section('title', translate($page, 'title') . ' - ' . setting('site_name', 'Central Data Technology'))

@section('content')
  <!-- ═══════════════ CONTACT US HERO SECTION ═══════════════ -->
  <section class="relative h-[400px] md:h-[500px] flex items-center pt-20 overflow-hidden bg-gray-900 text-white">
    <!-- Immersive background -->
    <div class="absolute inset-0 z-0">
      <x-image :src="resolve_block_asset($page->getBlockValue('hero_image', 'photo-1423666639041-f56000c27a9a-w2070.jpg'))"
        class="w-full h-full object-cover object-center" alt="Contact Us Background">
      <div class="absolute inset-0 bg-gradient-to-r from-primary via-primary/95 to-transparent w-full lg:w-3/4"></div>
    </div>

    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10 w-full">
      <div class="max-w-3xl text-white">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-white/70 mb-10" aria-label="Breadcrumb" data-gsap="fade-in">
          <a href="{{ url('/') }}" class="hover:text-white transition-colors">{{ t('contact.breadcrumb_home', 'Home') }}</a>
          <svg class="w-3 h-3 text-white/40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
          <span class="text-white font-bold" aria-current="page">{{ translate($page, 'title') }}</span>
        </nav>

        <div class="overflow-hidden mb-2">
          <p class="text-lg md:text-xl font-light text-white/90" data-gsap="fade-up">{{ $page->getBlockValue('hero_subtitle', 'Get in Touch') }}</p>
        </div>
        <div class="overflow-hidden mb-6">
          <h1 class="text-4xl md:text-5xl lg:text-[54px] font-bold leading-tight" data-gsap="fade-up" data-gsap-delay="0.1">
            {{ translate($page, 'title') }}
          </h1>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════ CONTENT DETAILS SECTION ═══════════════ -->
  <section class="py-20 md:py-28 bg-zinc-50">
    <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <!-- Left: Office & Contact Info -->
        <div class="lg:col-span-5 space-y-8" data-gsap="fade-up">
          <div>
            <span class="text-xs font-bold text-primary uppercase tracking-widest block mb-2">{{ $page->getBlockValue('office_label', 'Our Office') }}</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-6">{{ $page->getBlockValue('office_heading', 'Central Data Technology') }}</h2>
            <p class="text-zinc-600 font-light leading-relaxed max-w-md">
              {{ $page->getBlockValue('office_desc', 'CDT is ready to help your enterprise adopt modern IT infrastructures, cloud architectures, cybersecurity, and observability solutions.') }}
            </p>
          </div>

          <!-- Info Cards -->
          <div class="space-y-8">
            <!-- Headquarter Section -->
            <div class="border-b border-zinc-200/80 pb-6">
              <span class="text-[10px] font-bold text-primary uppercase tracking-widest block mb-3">{{ $page->getBlockValue('headquarter_label', 'Headquarter') }}</span>
              <div class="space-y-4">
                <!-- Address -->
                <div class="flex items-start gap-4">
                  <div class="w-10 h-10 bg-red-50 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm text-zinc-600 font-light leading-relaxed">
                      {{ setting('seo_org_address', setting('contact_address', 'Centennial Tower 12th Floor Jl. Jend. Gatot Subroto Kav. 24-25 Jakarta, 12930. Indonesia')) }}
                    </p>
                  </div>
                </div>

                <!-- Phone & Fax -->
                <div class="flex items-start gap-4">
                  <div class="w-10 h-10 bg-red-50 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 011.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                  </div>
                  <div class="text-sm text-zinc-600 font-light space-y-1">
                    <p>Phone: {!! safe_phone(setting('seo_org_phone', setting('contact_phone_hq', '(+62 21) 80622200'))) !!}</p>
                    <p>Fax: <span class="font-medium">{{ setting('contact_fax', '(+62 21) 80622211') }}</span></p>
                  </div>
                </div>

                <!-- Email -->
                <div class="flex items-start gap-4">
                  <div class="w-10 h-10 bg-red-50 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm text-zinc-600 font-light">
                      Email: {!! safe_email(setting('seo_org_email', setting('contact_email_marketing', 'marketing@centraldatatech.com'))) !!}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Customer Response Center Section -->
            <div>
              <span class="text-[10px] font-bold text-primary uppercase tracking-widest block mb-3">{{ $page->getBlockValue('crc_label', 'Customer Response Center') }}</span>
              <div class="space-y-4">
                <!-- Hotline -->
                <div class="flex items-start gap-4">
                  <div class="w-10 h-10 bg-red-50 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm text-zinc-600 font-light">
                      Hotline: {!! safe_phone($page->getBlockValue('crc_phone', setting('contact_phone_crc', '(021) 30122048')), 'hover:text-primary transition-colors font-semibold') !!}
                    </p>
                  </div>
                </div>

                <!-- Email -->
                <div class="flex items-start gap-4">
                  <div class="w-10 h-10 bg-red-50 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm text-zinc-600 font-light">
                      Email: {!! safe_email($page->getBlockValue('crc_email', setting('contact_email_crc', 'crc@centraldatatech.com'))) !!}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Embed Google Maps -->
          <div class="rounded-3xl overflow-hidden border border-zinc-200 shadow-sm h-64 w-full">
            <iframe 
              src="{{ $page->getBlockValue('maps_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.2548969209906!2d106.82087969999999!3d-6.230088800000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e2fb43fa4f%3A0x768b341c07865401!2sPT%20Central%20Data%20Technology!5e0!3m2!1sen!2sid!4v1781757203148!5m2!1sen!2sid') }}" 
              class="w-full h-full border-none" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>

        <!-- Right: Modern Form Card -->
        <div class="lg:col-span-7 bg-white rounded-3xl border border-zinc-200/60 p-8 md:p-12 shadow-sm" data-gsap="fade-up" data-gsap-delay="0.1">
          <div class="mb-8">
            <span class="text-xs font-bold text-primary uppercase tracking-widest block mb-2">{{ $page->getBlockValue('form_label', 'Message Us') }}</span>
            <h3 class="text-2xl font-bold text-gray-900">{{ $page->getBlockValue('form_heading', 'Send us a request') }}</h3>
            <p class="text-sm text-zinc-400 mt-1 font-light">{{ $page->getBlockValue('form_subheading', "Fill out the form below, and we'll connect you with a solutions expert.") }}</p>
          </div>

          @php
            $tTheme = active_theme();
            $assignments = setting("theme_{$tTheme->slug}_form_assignments", []);
            $contactFormId = is_array($assignments) ? ($assignments['contact_form'] ?? 2) : 2;
            $contactForm = $contactFormId ? \App\Models\Form::where('id', $contactFormId)->where('is_active', true)->with('fields')->first() : null;
          @endphp

          @if($contactForm)
            @include('cdt::partials.tailwind-form', ['form' => $contactForm, 'variant' => 'light'])
          @else
            <p class="text-sm text-zinc-500">Form is being configured.</p>
          @endif
        </div>

      </div>
    </div>
  </section>
@endsection
