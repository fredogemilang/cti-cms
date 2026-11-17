@php
    use Themes\Csi\Support\CsiDataProvider;

    $resolvedForm = $contactForm ?? (function_exists('get_assigned_form') ? get_assigned_form('contact_form') : null);
    $resolvedSideImage = $contactSideImage ?? CsiDataProvider::findMediaByTitlePrefix('contact-side-image');
@endphp

@if($resolvedForm)
<section id="contact" class="py-[60px] scroll-mt-20 bg-[#F7FAFF] relative overflow-hidden">
  <img id="contact-bg-glow" src="{{ theme_asset('images/contact-bg-glow.svg') }}" alt="" class="absolute -left-[132px] top-[289px] w-[649px] h-[649px] pointer-events-none select-none z-0 will-change-transform" />
  <div class="absolute bottom-0 inset-x-0 h-[460px] pointer-events-none select-none z-0" style="background: linear-gradient(0deg, #F7FAFF 0%, #F7FAFF 32%, rgba(247, 250, 255, 0.75) 55%, rgba(247, 250, 255, 0) 100%);"></div>

  <div class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px]">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-stretch">
      <div id="contact-form-column" class="lg:col-span-7 flex flex-col justify-start">
        <h2 id="contact-heading" class="text-3xl sm:text-[32px] font-bold text-slate-900 leading-tight">
          {{ t('contact.heading', 'Let’s Build Better IT Together') }}
        </h2>
        <p id="contact-subtitle" class="mt-2 text-base text-slate-600 leading-relaxed">
          {{ t('contact.subtitle', 'Planning an IT project? Our team at Converion is ready to help you find the right technology for your business.') }}
        </p>

        @include('csi::partials.tailwind-form', ['form' => $resolvedForm])
      </div>

      <div id="contact-image-column" class="lg:col-span-5 hidden lg:block">
        <div id="contact-side-photo-wrap" class="w-full h-full min-h-[460px] rounded-2xl overflow-hidden shadow-xl border border-white">
          @if($resolvedSideImage)
            <x-image :media="$resolvedSideImage" alt="Converion Office" sizes="(max-width: 1024px) 100vw, 45vw" class="w-full h-full object-cover will-change-transform" />
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endif
