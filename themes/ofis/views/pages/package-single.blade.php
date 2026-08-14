@extends('ofis::layouts.app')

@section('content')
<main id="content" class="pt-8 pb-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
    <!-- Breadcrumbs -->
    <div class="mb-4">
      <x-seo-breadcrumbs :entity="$entry" class="text-sm text-ofis-teal font-medium" />
    </div>

    <!-- Title -->
    <h1 class="text-3xl md:text-4xl font-bold text-ofis-ink mb-6">
      {{ $entry->meta['tagline'] ?? $entry->title }}
    </h1>

    <!-- Hero Image Banner -->
    @php
        $heroImg = $entry->meta['hero_image'] ?? $entry->featured_image ?? 'tingkatkan-efisiensi-bisnis-dengan-digital-signage-cm38qk4kuaghd-DbEiefuy.webp';
        $heroSrc = str_starts_with($heroImg, 'uploads/') || str_starts_with($heroImg, 'media/') ? asset('storage/' . $heroImg) : theme_asset($heroImg);
    @endphp
    <div class="w-full h-[300px] md:h-[450px] rounded-3xl overflow-hidden mb-12 shadow-sm">
      <x-image :src="$heroSrc" :alt="$entry->title" class="w-full h-full object-cover" />
    </div>

    <!-- Details Header & Intro -->
    <h2 class="text-2xl font-bold text-ofis-ink mb-3">OFIS {{ $entry->title }}</h2>
    <div class="text-gray-600 mb-10 text-[16px] leading-relaxed max-w-5xl">
      {!! nl2br(e($entry->content)) !!}
    </div>

    <!-- Accordions -->
    @if(!empty($entry->meta['accordions']) && is_array($entry->meta['accordions']))
      <div class="space-y-4">
        @foreach($entry->meta['accordions'] as $accIndex => $acc)
          @php
              $accImg = $acc['image'] ?? 'mengenal-energy-management-system-ems-solusi-cerdas-efisiensi-energi-perusahaan-energy-management-system-ofis-1024x576.jpg-CIjyZrcz.webp';
              $accSrc = str_starts_with($accImg, 'uploads/') || str_starts_with($accImg, 'media/') ? asset('storage/' . $accImg) : theme_asset($accImg);
          @endphp
          <details name="package-accordion" class="group bg-[#fab54f] rounded-2xl overflow-hidden cursor-pointer" @if($accIndex === 0) open @endif>
            <summary class="flex items-center justify-between px-6 py-5 font-semibold text-ofis-ink outline-none marker:content-none [&::-webkit-details-marker]:hidden">
              {{ $acc['title'] ?? '' }}
              <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </summary>
            <div class="px-6 pb-6 pt-2 bg-white border-x border-b border-[#fab54f] rounded-b-2xl flex flex-col md:flex-row gap-8">
              <div class="flex-1">
                @if(!empty($acc['items']) && is_array($acc['items']))
                  <ul class="list-disc pl-5 space-y-4 text-gray-600 text-[15px] leading-relaxed">
                    @foreach($acc['items'] as $item)
                      <li>{{ $item }}</li>
                    @endforeach
                  </ul>
                @else
                  <p class="text-gray-600 text-[15px] leading-relaxed">{{ $acc['description'] ?? '' }}</p>
                @endif
              </div>
              <div class="w-full md:w-[40%] shrink-0">
                <x-image :src="$accSrc" :alt="$acc['title'] ?? ''" class="w-full h-auto rounded-xl object-cover shadow-sm" />
              </div>
            </div>
          </details>
        @endforeach
      </div>
    @elseif(!empty($entry->meta['features']) && is_array($entry->meta['features']))
      <div class="space-y-4">
        @foreach($entry->meta['features'] as $featIndex => $feat)
          @php
              $featImg = $feat['image'] ?? 'mengenal-energy-management-system-ems-solusi-cerdas-efisiensi-energi-perusahaan-energy-management-system-ofis-1024x576.jpg-CIjyZrcz.webp';
              $featSrc = str_starts_with($featImg, 'uploads/') || str_starts_with($featImg, 'media/') ? asset('storage/' . $featImg) : theme_asset($featImg);
          @endphp
          <details name="package-accordion" class="group bg-[#fab54f] rounded-2xl overflow-hidden cursor-pointer" @if($featIndex === 0) open @endif>
            <summary class="flex items-center justify-between px-6 py-5 font-semibold text-ofis-ink outline-none marker:content-none [&::-webkit-details-marker]:hidden">
              {{ $feat['title'] ?? '' }}
              <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </summary>
            <div class="px-6 pb-6 pt-2 bg-white border-x border-b border-[#fab54f] rounded-b-2xl flex flex-col md:flex-row gap-8">
              <div class="flex-1">
                <p class="text-gray-600 text-[15px] leading-relaxed whitespace-pre-line">{{ $feat['description'] ?? '' }}</p>
              </div>
              <div class="w-full md:w-[40%] shrink-0">
                <x-image :src="$featSrc" :alt="$feat['title'] ?? ''" class="w-full h-auto rounded-xl object-cover shadow-sm" />
              </div>
            </div>
          </details>
        @endforeach
      </div>
    @endif
  </div>

  <!-- Form Studio Contact Section -->
  <section id="contact" class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col py-12 md:py-16">
    <h2 class="text-3xl md:text-4xl font-bold text-ofis-ink text-center leading-tight mb-3">
      {{ t('contact.title', 'Let Us Help You Through Your Smart Office Transformation') }}
    </h2>
    <p class="text-ofis-ink/70 text-center mb-10 max-w-2xl mx-auto">
      {{ t('contact.subtitle', 'Fill out the form below and let our team help you find the OFIS solution that is perfectly tailored to meet your needs.') }}
    </p>

    @if(session('success'))
      <div class="w-full max-w-5xl mx-auto mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-center font-medium">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('forms.submit', 'contact-form') }}" method="POST" class="ofis-form w-full max-w-5xl mx-auto bg-white border border-gray-100 rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.12)] p-6 md:p-10 grid md:grid-cols-2 gap-x-6 gap-y-5">
      @csrf
      <div class="field">
        <label for="f-name" class="field-label">{{ t('form.name', 'Name') }} <span class="text-ofis-yellow">*</span></label>
        <input id="f-name" type="text" name="name" placeholder="Your full name" class="field-input" required/>
      </div>

      <div class="field">
        <label for="f-email" class="field-label">{{ t('form.email', 'Corporate Email Address') }} <span class="text-ofis-yellow">*</span></label>
        <input id="f-email" type="email" name="email" placeholder="you@company.com" class="field-input" required/>
      </div>

      <div class="field">
        <label for="f-company" class="field-label">{{ t('form.company', 'Company') }} <span class="text-ofis-yellow">*</span></label>
        <input id="f-company" type="text" name="company" placeholder="Your company name" class="field-input" required/>
      </div>

      <div class="field">
        <label for="f-message" class="field-label">{{ t('form.message', 'Message / Requirements') }} <span class="text-ofis-yellow">*</span></label>
        <input id="f-message" type="text" name="message" placeholder="Tell us about your needs" class="field-input" required/>
      </div>

      <div class="md:col-span-2 pt-2">
        <button type="submit" class="w-full md:w-auto py-4 px-10 bg-[#fab54f] hover:bg-[#fab54f]/90 text-ofis-ink font-bold text-base rounded-full shadow-md transition">
          {{ t('form.submit', 'Submit Inquiry') }}
        </button>
      </div>
    </form>
  </section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('details[name="package-accordion"]').forEach((detail) => {
      detail.addEventListener('toggle', () => {
        if (detail.open) {
          document.querySelectorAll('details[name="package-accordion"]').forEach((otherDetail) => {
            if (otherDetail !== detail) {
              otherDetail.removeAttribute('open');
            }
          });
        }
      });
    });
  });
</script>
@endsection
