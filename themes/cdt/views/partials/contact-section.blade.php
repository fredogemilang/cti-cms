<!-- Contact Form Section -->
<section id="contact" class="py-20 relative overflow-hidden text-white bg-zinc-900">
  <div class="absolute inset-0 bg-form-image bg-cover bg-center"></div>
  <div class="absolute inset-0 bg-primary opacity-80"></div>
  
  <div class="relative z-10 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center" data-gsap="fade-up">
    <p class="text-sm font-bold mb-2 text-white">{{ t('home.contact_subtitle', 'Get In Touch') }}</p>
    <h2 class="text-3xl font-light mb-12">{{ t('home.contact_title_prefix', 'Have some') }} <span class="font-bold">{{ t('home.contact_title_main', 'Question?') }}</span></h2>
    
    @php
      $contactForm = get_assigned_form('contact_form');
    @endphp
    @if($contactForm)
      @include('cdt::partials.tailwind-form', ['form' => $contactForm, 'variant' => 'dark'])
    @else
      <p class="text-white/60 text-sm text-center">Contact form is being configured.</p>
    @endif
  </div>
</section>
