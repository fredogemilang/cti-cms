@extends('ofis::layouts.app')

@php
    $packages = $packages ?? \App\Models\CptEntry::whereHas('postType', function($q){ $q->where('slug', 'package'); })->where('status', 'published')->get();
    $posts = $posts ?? \Plugins\Posts\Models\Post::where('status', 'published')->latest()->take(6)->get();
@endphp

@section('content')
<div id="promo-sentinel" aria-hidden="true"></div>

<!-- Promo Banner -->
<section id="promo-banner" class="promo-banner sticky top-0 z-40 w-full bg-cover bg-center bg-no-repeat transition-all duration-300 ease-out overflow-hidden" style="background-image:url('{{ theme_asset('bannerpromo-scaled.jpg-CC1eRu0q.webp') }}');">
  <div class="promo-inner relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-row items-center justify-between gap-6 transition-all duration-300 ease-out py-5 md:py-7">
    <div class="relative flex flex-col justify-center z-10">
      <h2 class="promo-title font-bold text-white leading-tight mb-2 transition-all duration-300 ease-out text-3xl md:text-5xl">
        {{ t('home.promo_title', 'Revolutionize Your Workplace Today!') }}
      </h2>
      <h3 class="promo-sub font-medium text-white leading-tight transition-all duration-300 ease-out text-lg md:text-2xl">
        Start From IDR 1 Million, Let's <span class="promo-try-on">{{ t('home.try_on', 'Try On') }}</span>
      </h3>
    </div>
  </div>
  <img src="{{ theme_asset('arrowpromo.png-C9UzomPj.webp') }}" alt="" class="promo-arrow pointer-events-none absolute top-0 h-full w-auto object-contain object-right" style="right:-24px;" loading="lazy"/>
</section>

<style>
  #promo-banner .promo-arrow {
    animation: promoArrowPoint 1.6s ease-in-out infinite;
    transform-origin: right center;
  }
  @keyframes promoArrowPoint {
    0%, 100% { transform: translateX(-24px); }
    50%      { transform: translateX(0); }
  }

  .promo-try-on {
    position: relative;
    display: inline-block;
    white-space: nowrap;
  }
  .promo-try-on::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: -3px;
    height: 3px;
    border-radius: 999px;
    background: linear-gradient(90deg, transparent 0%, #FFB627 15%, #fab54f 50%, #FFB627 85%, transparent 100%);
    box-shadow: 0 0 8px rgba(255, 182, 39, 0.6);
    animation: promoUnderline 1.6s cubic-bezier(.65,.05,.36,1) infinite;
    transform-origin: center center;
  }
  @keyframes promoUnderline {
    0%, 100% { transform: scaleX(1) scaleY(1); }
    50%      { transform: scaleX(0) scaleY(0.4); }
  }

  @media (prefers-reduced-motion: reduce) {
    .promo-try-on::after { animation: none; transform: none; }
  }

  #promo-banner.is-stuck { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
  #promo-banner.is-stuck .promo-inner { padding-top: 0.75rem; padding-bottom: 0.75rem; }
  #promo-banner.is-stuck .promo-title { font-size: 1.5rem; line-height: 1.2; margin-bottom: 0.25rem; }
  @media (min-width: 768px) {
    #promo-banner.is-stuck .promo-title { font-size: 1.875rem; }
  }
  #promo-banner.is-stuck .promo-sub { font-size: 1rem; }
  @media (min-width: 768px) {
    #promo-banner.is-stuck .promo-sub { font-size: 1.125rem; }
  }

  @media (prefers-reduced-motion: reduce) {
    #promo-banner .promo-arrow { animation: none; }
  }
</style>

<!-- Hero Section -->
<section class="relative w-full" style="background-image:url('{{ theme_asset('home-bgempower-Bb-nl347.webp') }}');background-size:cover;background-position:50% 100%;background-repeat:no-repeat;">
  <div class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-center py-12 md:py-20">
    <div class="relative flex flex-col gap-6 items-start">
      <h1 class="text-4xl md:text-[2.75rem] font-bold text-ofis-ink leading-tight">
        {!! t('home.hero_h1', 'Empower Your<br/>Workforce For<br/>Unprecedented Security<br/>With OFIS') !!}
      </h1>
      <p class="text-base font-normal text-ofis-ink leading-relaxed max-w-lg pr-4">
        {{ t('home.hero_desc', 'Step into the future of work. Elevate employee productivity and well-being with our seamlessly integrated security, convenience, and automation solutions. Break down barriers, foster collaboration and unleash the true potential of your workforce to reach unprecedented performance levels with our smart office solution.') }}
      </p>
      <a href="#contact" class="inline-flex items-center gap-2 transition py-3 px-8 bg-[#fab54f] hover:bg-[#fab54f]/90 text-ofis-ink text-sm font-semibold rounded-full shadow-sm mt-2">
        {{ t('home.request_demo', 'Request Demo') }}
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </a>
    </div>
    <div class="relative flex flex-col gap-5">
      <img src="{{ theme_asset('home-imgnomasking-png-Bs7Xi48Z.webp') }}" alt="OFIS Empower Workforce" class="max-w-full h-auto" loading="lazy"/>
    </div>
  </div>
</section>

<!-- Floating Card & Packages Section -->
<section class="relative w-full" style="background-image:url('{{ theme_asset('bgpackage.png-kL6ZKSss.webp') }}');background-size:cover;background-position:50% 100%;background-repeat:no-repeat;">
  <div class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col py-8 md:py-12">
    
    <!-- Floating Overlap Banner -->
    <div class="relative flex flex-col bg-[#f5f5fb] rounded-3xl px-8 py-10 md:px-14 md:py-14 gap-5 mb-12 -mt-20 md:-mt-28 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15)] z-10">
      <h2 class="font-bold text-ofis-ink text-center leading-tight" style="font-size:32px;">
        {!! t('home.overlap_title', 'Transform Your Workplace<br/>Into the Most Advanced Smart Office') !!}
      </h2>
      <p class="text-lg md:text-xl font-normal text-ofis-ink text-center max-w-4xl mx-auto">
        {{ t('home.overlap_desc', 'With OFIS advance smart office technologies and end-to-end solutions provided by Blue Power Technology (BPT), create a better workforce performance become easily possible with smart office solution.') }}
      </p>
    </div>

    <h2 class="text-3xl md:text-4xl font-bold text-ofis-ink text-center leading-tight mb-6">
      {{ t('home.packages_title', 'OFIS Packages') }}
    </h2>

    <!-- Dynamic Packages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
      @forelse($packages as $package)
        @php
            $meta = is_array($package->meta) ? $package->meta : json_decode($package->meta ?? '{}', true);
            $iconMap = [
                'safety-and-security' => 'safetyicon.png-6vSYl0Qc.webp',
                'smart-front-desk' => 'smartfrontdesk_icon.png-D1P0O56F.webp',
                'smart-meeting-room' => 'smartmeetingroom_icon-300x300.png-DVEldxHX.webp',
                'efficiency' => 'efficiency-icon.png-iX-ROHkz.webp',
                'service-and-facility' => 'serviceandfacility_icon.png-DseRFpzh.webp',
                'seat-management-system' => 'seat-management.png-BiTTasKa.webp',
            ];
            $iconFile = $iconMap[$package->slug] ?? 'efficiency-icon.png-iX-ROHkz.webp';
        @endphp
        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition flex flex-col p-6">
          <div class="flex items-start gap-4 mb-3">
            <div class="w-14 h-14 bg-[#FFF6E8] rounded-xl flex items-center justify-center shrink-0 p-2 border border-orange-100">
              <img src="{{ theme_asset($iconFile) }}" alt="{{ $package->title }}" class="w-10 h-10 object-contain" loading="lazy"/>
            </div>
            <h3 class="text-xl font-bold text-ofis-ink leading-tight mt-1">{{ $package->title }}</h3>
          </div>
          <p class="text-sm text-gray-500 line-clamp-3 mb-4 flex-1">
            {{ $meta['subtitle'] ?? Str::limit(strip_tags($package->content), 120) }}
          </p>
          <a href="{{ url('/package/' . $package->slug) }}" class="inline-block text-ofis-teal font-semibold text-sm hover:underline mt-auto">
            {{ t('common.continue_reading', 'Continue Reading →') }}
          </a>
        </article>
      @empty
        <!-- Fallback static cards if DB empty -->
        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition flex flex-col p-6">
          <div class="flex items-start gap-4 mb-3">
            <div class="w-14 h-14 bg-[#FFF6E8] rounded-xl flex items-center justify-center shrink-0 p-2 border border-orange-100">
              <img src="{{ theme_asset('safetyicon.png-6vSYl0Qc.webp') }}" alt="Safety and Security" class="w-10 h-10 object-contain" loading="lazy"/>
            </div>
            <h3 class="text-xl font-bold text-ofis-ink leading-tight mt-1">Safety and Security</h3>
          </div>
          <p class="text-sm text-gray-500 line-clamp-3 mb-4 flex-1">Transform your smart office solution with a comprehensive, end-to-end OFIS solution to safeguard your employees and all...</p>
          <a href="{{ url('/package/safety-and-security/') }}" class="inline-block text-ofis-teal font-semibold text-sm hover:underline mt-auto">Continue Reading →</a>
        </article>
      @endforelse
    </div>
  </div>
</section>

<!-- Customers Marquee Section -->
<section class="relative w-full flex flex-col py-8 md:py-12">
  <div class="relative flex items-center justify-center gap-4 mb-8 px-4">
    <img src="data:image/webp;base64,UklGRh4FAABXRUJQVlA4WAoAAAAQAAAASwAATQAAQUxQSG8EAAABkFZte942DwRBeCEIghjUDGIGNoOIQc3AYeAxEARDeCG8EO4f/k4HICImQNfTe4V1Nj1YWhDtpS8cgv33bdbYd3vsDTH1XXV432QObej6D7g9ZLCaJKUPdPd8YNS2BO2hD27ab6wHZZinl+0ZVO0XKM8Eow4L2MYau3PajISOG9MzUI4SFEk5IFoLcJP0oZ2YmJ/KR9qxwIskVWiSplP1qZXxqICkD27aHeEl9cSJhfGZynr0oUkJXjpsNCnB68DAnjGY997wkgqko56QtBC2Y07Twx/wl1lpEJJ60HEBSQbM2ewd0D30Ds7+JnWQjnpWKTfO+usJWyFa23gLcEswHi3MegG+OBDrCrzvSw5TklIpJqUKnhpuewOUDrxIyiUnqTi8b/tA1ekcLBm8SEpvcAvcdPoD5SaDqosj/DrgyxLAshCm88lpN33wdEUrl0ddLVDuWZl12cGH3NV5Hkr+APWSnPEe+Ln0hinppDmUSx/mWwzKFYNJ55Pjlyb+fckH19UCP1cq7UuC1yU1/v2RAvnaiH9Nm8+voOs9LPP59b7rcUPhxns0tasrfk+7unT3XO+Je/Rk6qdl9W1bamcXKNenK7mfltW3bamdnIs+d6fuPUpDCy5W8LkOfd/XaVnZxmw75Sbf6xrbWOc69H1fpyWYoepkKtMK8Noo2cncjWOfzczSzgJEG7LOtktbm2DcOVlasPW5S9ptxJB08R71YOfSL6dbLUkaiaSvWHCdLg5eTUplcnbXZYL+KwyIVrtiZqWfAhh1nIfGYfuKunstRdD6VYW7NIX1D418ZlvWEZ92agvELDF7aNUsvcNPNjfYFPaQ9aQA33d1Deq4x6/ANbro9BeNjBj8HHaymBz+0x0ZCh44nHaYuXStgT63MBxmKDs1xu5SK+pBBOehAh+aA2xVNrA9NuA77E+bEB9yuFCjPOPMdHyKrgtsFBfWRDOUGg1FSBbcLE/HIB9cNHSRJquB2rkB5wpnv6HHtVnA7pWB6oEC+Y2LdUwW3U5V44IPrDmc+UAW3MwXKfc50xxvykSq4ndDKdFsBu+EXJp2t4HaiEumuD6uupLLCqvMV3I4Myl2Ovy60AKZ0QRXc9vIE7aYOwPMpoBWdHKYkSRXcJJUGEOmct90l8Ab8pr00zdNgOh2sSZJGYJkDiAre9oM5OFtVHNx27qywJknqgm17SY2zc1cP53mQpAqe71KFeSNziCJJVs+armcnXpdytY0qzBvZSqTN4+bwurLgtlGFeSMLylfIHMqFAm4bVZg3Wum/Q+ZE3iu2UQ9uG1WYJVnw8yUyJ/Kmw22jHtw2mmAp3Qr2LTInsqTkuG1UYU0bNXarvtecyJJy4LZRhTVteiBa0TebEyYpB24bVViTpJEwfbs5bpJy4LZRhdVUglnfb46bpBy4bTQBDm5/QBa4ScrgtilsW9afzIGbpB7cJPXQ9Vl/NQdukip4VnFm/eUcuEmJfbc/pRy4aYAAPOuP5wAHVy5Zf7844Kb/pPV1SPpOAFZQOCCIAAAAkAgAnQEqTABOAD5ZIo1FI6IhGjQAOAWEtIAK4A9/3v8L//YSPtOAdjoXajz9EUzxd96mbr8lv1UTOHjemXyQXTAYnmJNx9wsxISCEAAA/vq2f/78yift1TehEmW78c5bMCkGEukaDq+xVKD3cXP6B//wQ+XOpTvE7/qhj0WWDAe0xb+gSAAAAA==" alt="customersicon" class="h-16 w-auto" loading="lazy" />
    <h2 class="text-3xl md:text-4xl font-bold text-ofis-ink text-center leading-tight mb-6">Our Customers</h2>
  </div>

  <div class="customers-marquee relative w-full overflow-hidden">
    <div class="customers-track flex items-center gap-12 md:gap-16 w-max">
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-7-1-r9evx5u8id83uvyjvsiji3i3qd1azgq3rurr0d1y4g-CyB6Z_7x.png') }}" alt="customer 7" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-8-1-r9evx8nr2vbytpugfbqf7kshiinemk1as8q7g6xrls-CwfU6eOQ.png') }}" alt="customer 8" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-9-1-r9evxcf3u7h445oztdcxhjubw24vhcg84rc5das6ww-BBdLhyc6.png') }}" alt="customer 9" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-10-1-r9evxeas7vjordm9ie66mjd92tvlwqnot0n4bupekg-DYqAP6pW.png') }}" alt="customer 10" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-0-1-r9evwwfum0v8msc7eog9t5vhsibmuhosek8w7lfvuo-0zA1qmQR.png') }}" alt="customer 0" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-1-1-r9evwxdosuwiyeau96uwdnmydw7026siqowdovehog-DhhBDJrR.png') }}" alt="customer 1" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-3-2-r9ew6cpfc1t1dmly7rj7vs0ctzqi8x7mdcenxrfda8-BevCQC1u.png') }}" alt="customer 3" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-4-1-r9evx30pxv48w22nc9ansm7py7f7cdewrgtakj64n4-BHeDcie1.png') }}" alt="customer 4" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-5-1-r9evx3yk4p5j7o1a6rpad3z6jlakk2in3lgs1t4qgw-CtHuvFm6.png') }}" alt="customer 5" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-6-2-r9evx3yk4p5j7o1a6rpad3z6jlakk2in3lgs1t4qgw-kugv1Ka2.png') }}" alt="customer 6" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />

      <!-- duplicate for seamless loop -->
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-7-1-r9evx5u8id83uvyjvsiji3i3qd1azgq3rurr0d1y4g-CyB6Z_7x.png') }}" alt="" aria-hidden="true" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-8-1-r9evx8nr2vbytpugfbqf7kshiinemk1as8q7g6xrls-CwfU6eOQ.png') }}" alt="" aria-hidden="true" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-9-1-r9evxcf3u7h445oztdcxhjubw24vhcg84rc5das6ww-BBdLhyc6.png') }}" alt="" aria-hidden="true" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
      <img src="{{ theme_asset('home-bpt---customize-customer-section---landing-page-ofis-10-1-r9evxeas7vjordm9ie66mjd92tvlwqnot0n4bupekg-DYqAP6pW.png') }}" alt="" aria-hidden="true" class="h-24 md:h-28 object-contain shrink-0" loading="lazy" />
    </div>
  </div>
</section>

<style>
  .customers-marquee {
    -webkit-mask-image: linear-gradient(to right, transparent 0, #000 8%, #000 92%, transparent 100%);
    mask-image: linear-gradient(to right, transparent 0, #000 8%, #000 92%, transparent 100%);
  }

  .customers-track {
    animation: customersScroll 40s linear infinite;
    will-change: transform;
  }

  .customers-marquee:hover .customers-track {
    animation-play-state: paused;
  }

  @keyframes customersScroll {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
  }

  @media (prefers-reduced-motion: reduce) {
    .customers-track { animation: none; }
  }
</style>

<!-- Advantages Section -->
<section class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col py-8 md:py-12">
  <h2 class="text-3xl md:text-4xl font-bold text-ofis-ink text-center leading-tight">
    {{ t('home.advantages_title', 'Advantages of Implementing OFIS in Your Office') }}
  </h2>
  <p class="text-base font-normal text-ofis-ink text-center mt-3">
    {{ t('home.advantages_subtitle', "It's time to seize the multitude of benefits by implementing OFIS to your business operations.") }}
  </p>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-14 lg:gap-16 xl:gap-20 mt-20">
    <article class="advantage-card relative bg-white border border-gray-200 rounded-2xl pt-16 pb-10 px-8 text-center">
      <div class="advantage-icon absolute left-1/2 -top-12 -translate-x-1/2 w-24 h-24 rounded-full bg-ofis-teal/80 flex items-center justify-center p-5 shadow-sm">
        <img src="data:image/webp;base64,UklGRq4DAABXRUJQVlA4WAoAAAAQAAAAMQAANgAAQUxQSDUDAAABoFbbdtDqWhIi4UhAwnHw4aBxAA4aB8UBOOA6yHMQCUdCJOwfh`..." alt="Boost Productivity" class="w-full h-full object-contain" loading="lazy" />
      </div>
      <h3 class="text-xl md:text-2xl font-bold text-ofis-ink">Boost Productivity and Maintain Efficiency</h3>
      <p class="text-gray-600 mt-4 max-w-md mx-auto">Improve your employees' productivity by simplifying their activities and save more money by monitoring and reducing your resources consumption.</p>
    </article>

    <article class="advantage-card relative bg-white border border-gray-200 rounded-2xl pt-16 pb-10 px-8 text-center">
      <div class="advantage-icon absolute left-1/2 -top-12 -translate-x-1/2 w-24 h-24 rounded-full bg-ofis-teal/80 flex items-center justify-center p-5 shadow-sm">
        <img src="data:image/webp;base64,UklGRtYCAABXRUJQVlA4WAoAAAAQAAAAMQAAMAAAQUxQSGACAAABgFXbdtDoSoiEJ6ESnoPBAXEADsDBxAE4QEIkIOFJiITzQaFpFUTEBKivTUc0iLq+9JteeRrjD1iFVgaTlLwEhH1rbMSopzlg+c4CJenDFZZvTDBLUppWSb66JA2NpZ81ZklpaeBSQDVJDt4tWCVZwElMOxGwSJqJ1CkTkhScrgLEywq4pMraKRgvO1XSUNYkDYRJclrq8iJ0TTTdF2ZdK7nLSnljnFKatuKX/zeZrUvFJau1serVABa9oB6LlIgujSQVYJcF25Abo3JAkxSkHjRJldWkzCbJqVLyhkkn1iUknbiklVmSCEmKS8U7JR3MSZrZJDmnJGtfaCyMBViUGstrCEa9ApB0Yp9FNMA8KszKXHdZ4zyLlECfnwBFkpyQbK/HKM1sujotg3IQekvTfWF+szP3kGV7s1AleR4kZSJJMrAu941q+gfCtMCfpJ1NX30FrdLKSVRYJTnYd2Q7MEgNwiVZsOphjfdneiJZKZKGzXUNqp5yn599mCLSB3Y9qNvtOjyzkvQp6LrzOP7twcpfvy3fTiew+U1y9ct66DsQo1169pEsB7DZL0nyA9r4W5LttPRj0s76G7ba3cD2GyvUMb2ZKb9hewCbSxb4E8ulHmMfScMBxAlFb5NPW+Pt0UuyHECb3CyX2gDaNg05WB/UbdvKA0m+N+7Pkk1XJx68TVK6k9JQ6nkekyc9BKjXRnH3l7S39KCrEcGt6+1cJRusV/KgJDN3NzM9LbANnyUvFahJXW0H4t8eJC8VoNVR3S0HcIwvyXyqDWh19aTv+t64b3X1pJ/MxwlRiyc9BlZQOCBQAAAAkAQAnQEqMgAxAD5VIIxFI6IhGvQAOAVEtIAAZkaKRq8XcITj0yXBg2Oxbrs4AP2z//0iWr/btaf/zQCz//6KVLvl/5rMnv+tZ///R8AAAAA=" alt="Improve Collaboration" class="w-full h-full object-contain" loading="lazy" />
      </div>
      <h3 class="text-xl md:text-2xl font-bold text-ofis-ink">Improve Collaboration</h3>
      <p class="text-gray-600 mt-4 max-w-md mx-auto">Improve collaboration with internal and external team and unleash the true potential of your workforce.</p>
    </article>

    <article class="advantage-card relative bg-white border border-gray-200 rounded-2xl pt-16 pb-10 px-8 text-center">
      <div class="advantage-icon absolute left-1/2 -top-12 -translate-x-1/2 w-24 h-24 rounded-full bg-ofis-teal/80 flex items-center justify-center p-5 shadow-sm">
        <img src="data:image/webp;base64,UklGRm4DAABXRUJQVlA4WAoAAAAQAAAAMQAAMQAAQUxQSAUDAAABkFXbdtBaW8KRsCVEQhxcHIADcNA4aBxQB62DIwEJR8KRsD+A9PUpiIgJwOUacmL85RgvrpgxuirfShuLGGOmh7aRCENVtEuOlXZ5aIJljugDQLdpI5a6NeCtEc8/e6rx3MURqvPc9bCqY6SkpAOX7ZuGsx2SkiNg35v9BFj2TvzrE4HItXLop03v1AaXJP43ik5YndYVVMy/MgPXCoAsBIAu7fwJ4wPAVk9JSp8JvhTlByVygT1S6Xtr3SXtxJJZvmDf56uSUTClfDJcL66Ywfhc2WPvRElJhwFgJPFQThheQg+YXYUkVVdF05Oka8ZDBwGgeMoJADz0AFBqrU81m9LlgOlyx6IwAGCmv5UFAOzQhEmXAFyRhkneWluNEcT5EAGmn8BIs8zW3qqwiE3xziTOL804mz4A0MURJjU0VTD1TjUsIS/AM0AFLql2ajfwNKoDdMWG++PAovmq6llrrbvqVdUGT3xrcrjsZK7Bw06Wji5+U9WRjvOmvd2+1E7wxKK/b5oWar94CYPaL15i1XbjcZmqVBuZ3KeRJlIZl8/XVbiNUZmqY+buHuHeMEjtA0110jbQRfzDlUgfmPR2zQOe4PwV1eFpd+jSCwNyvGTfINI2LQPoLwwsmpEH7tfUUYCuxdJHrq9cXNSB4ooHHsq3kqhyNE0Autpy+1QHsGiHq4KZe2iLMFR5a5JZJgGGBoMAI0iptUMTLEMOmC7/MOkwAHW5rQZYaMakSwNcnn/21EaSAJqC+JKHGgCS7HqgyktKOnBeoqAp5rEp1WCGsx2SkmDfV7somQVLKGa7ssWVE0p8LoBl78R4ydzAlyTfW9s9lc0wZ5abf8nQTnDxlKT01WBvRcEv+ZKeAGAkAeCRcuLHU4hY11pLXWZQUfH7JlK3RjX8H2BdM9fJ8Av2fbYRABEARuyxdw6VlHTYL+yQlGXkUEXTk+f+TSfJ8lSzKX1EHwC6DRux1C0A10imYdLRLgtGwHZ5qMLiGNkURyYxfHPLlKfmEWwhLxj3fQzlUGy4BABWUDggQgAAAPADAJ0BKjIAMgA+YSySRiQioaEqCACADAlpCHAAAJMcY3d3dC4ycIAA/sgACf//+NrCV4kO///xoXOxF//+NrAAAA==" alt="Extra Secure" class="w-full h-full object-contain" loading="lazy" />
      </div>
      <h3 class="text-xl md:text-2xl font-bold text-ofis-ink">Extra Secure</h3>
      <p class="text-gray-600 mt-4 max-w-md mx-auto">Provide unparalleled extra security by safeguarding your employees and valuable assets safe from all types of threats.</p>
    </article>

    <article class="advantage-card relative bg-white border border-gray-200 rounded-2xl pt-16 pb-10 px-8 text-center">
      <div class="advantage-icon absolute left-1/2 -top-12 -translate-x-1/2 w-24 h-24 rounded-full bg-ofis-teal/80 flex items-center justify-center p-5 shadow-sm">
        <img src="data:image/webp;base64,UklGRuYBAABXRUJQVlA4WAoAAAAQAAAAMQAANQAAQUxQSHYBAAABkJVtb95mLwRDEIRAEIOFQc2gZhAzaBikDDYGhmAIgiAI70ESp9G+n9OImABc1+YcbQ/cv5C9jZLlNqEJhsU83ZX5AICUa50TABTmuyoVwNNJ0goAZQ2x0OukT+MSZqIJAIhRo3xTcKhsUbzjtHsUNmiVXWMU75jZdt2jNCpWAhBuUZQmACBGiYJKe6ouzoowyEaSXhAis2A/15oTAGR+3ZXcZHcuRrkLhWyjnSy4PzfnoDfFH33SaGLkFuzNzSixGjEzB5Lv7oTS+yuM0bmjswYR/qDvtuSuEmRD21UYyVe4zTgFM2Tm/5bUl0jPnpA61yFZt8dOM5IKdLryZk+NbKcCiJN0bhg9SO2UbEbaqQBvlvTmJ4SDJsYFg80ANK7XzheaQKyOrJyBtE6fqyYAEkbFXXD1AhKuF/ZLhTr00ZWvKyunu1LnfMEMt4u7DC2s96HQZOBBkwCoND1Z6IKQlZyOOgVBG/XIDFHzL6j/KFOWo3n+BFZQOCBKAAAAUAQAnQEqMgA2AD5VHoxFI6GhHP7EADgFRLSAAGeJf9sO4+NSxRnTdZ/AAAD9s//9ImOlf//R8ACr/6Y+ub//+j7U/5rXj/TvQAA=" alt="Everything is Touchless" class="w-full h-full object-contain" loading="lazy" />
      </div>
      <h3 class="text-xl md:text-2xl font-bold text-ofis-ink">Everything is Touchless</h3>
      <p class="text-gray-600 mt-4 max-w-md mx-auto">Allow employees and visitors to use facilities without physical contact, promoting a safer and more efficient workplace.</p>
    </article>
  </div>
</section>

<!-- Why Choose OFIS Section -->
<section class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col py-8 md:py-12">
  <h2 class="text-4xl font-bold text-ofis-ink text-center leading-tight">
    {{ t('home.why_choose_title', 'Why Choose OFIS') }}
  </h2>
  <div class="relative grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10 mt-8 max-w-5xl mx-auto">
    <div class="flex flex-row items-start text-left gap-5">
      <img src="data:image/webp;base64,UklGRpAEAABXRUJQVlA4WAoAAAAQAAAAOwAAPAAAQUxQSA8EAAABoFXbttXYGhK2hCUBCTi4OAgOEgfgoHAADrgOtoQtYUnYEsYHeVTlGIiICeBbd83gH51V139gvAecrqMJZbldlnauU3zBou6TBt156foD+HT8s0GbaoNNtenKqLba+fPZJGpuAWXLOnK68VArX1i6I2+HBoerxhewWoGxZq834HCH6lh9fMNgwuLTBU5nYODh/39W7rW7M+gW5aE3Vq23gNH8s6bW4HQFeFhhUxsU/bWIZ7oVoDtcihYoUxfQ+KWpZzzpPgDSuPCE0Uv/tUgzLk37Xqiul9mEpWsCyqdlWveWmV3bJbamK6M+YOzemLVvAxT7e2WpvpsXYDJh9ekB1ZXrZH1nrGqv2zxGDN2MF7MNmJvmAzjcnxyur6JqP8fC08kMnkd642nhGt3xkg4vFu1r4c1H8HK2E3tdAJazBqcbMJs8japb4ZdnPbu6w6Ja9QaktyeR5sibkZltfMWq1nu3kG6HegCLyXXotuBanhyq+xvEFFB9DCbEtgKk42XoHoXr0lsBmv9N9kt14eVDuzuv48a1enAdUq1QFLoBP2pOz0pqH954We48rebc3acfK5ye0499StsziOAPVzeGrrrC6nVkc3/1esk8y0ejHWZzmwGmLfsM6fhRqM4f0Rz5eLDz9r3v4+m52cYlf54N+y2IdP5stgflfk5PFp8OpXv9eZJqTVtcxq1l1n26lGZvai+Xaj2sI8SZ+6EBLGZVWwHu3Zd5A0pTa/pz2dx4czaBogMxbwVK1bqOEeM9tQWU7V4YtQCTCWXJtgdUH5c0g2ukfeLlnGZwjWYGjLuVSNUMVlsAkWZcmhm8Gc12iTQDftSJwxyH00rpugPRbMBiBm+XdAWaGUC1DtAcoHQLcSjAfCnpjQ9He4HqDJAuQBrApczPDh8wW/m4+oCH9XJqBqc7LCaRmuNWu45wevtstsKotn1kTnei27tOzPa1pNpPIB0+CzuwpdphMiGatglOH6BTcFV+UbnGpHBYAUoAPKyX4O8ol+7PtG8B5b7fBi2obb8B3fis2IFpb142r1lIrwmPqjpAdf5stEKovT6AzXpv/sz2rdkCoIzNB6zWzw43mM2h8LTAqN0bBC8fVigan4QGHK683TSDdw8T2GyfnO5AWt/7tOw6AyXd31vMAObuWX6v2UeuQ7fGq1LtA9chzfJboQOUCRhS9/EyLt0+AEPApMNvcXouVRcgDtXMrtYAFm333Z1fj652XQDiSNXcRoBFu2r8Ho/cRya98bREFJ5OOjMebeXPq/89e3c0+c5QiHMfy6VMWw3ojt8x+z+Rqg+YVTM4XL+jeg5prtUK1bY2czht3zGrZlDs0B0oqbp+B3GYAaRD2IFIa/C1UQAOe7cCxMDXz6q3y78ZwbcCAFZQOCBaAAAAcAUAnQEqPAA9AD5hKJBFJCKhmA78AEAGBLSABsfgwkG+9pOh0S+L5nUqCLIZEtVNApmXgAD+/s0Z+Oba0Jwcrx5HFU039r/if/+9t1HvYXxh0/RdnssAAAAA=" alt="complete" class="w-14 h-14 object-contain shrink-0" loading="lazy"/>
      <div class="flex flex-col gap-2">
        <h3 class="text-xl font-bold text-ofis-ink">Complete Office Automation</h3>
        <p class="text-base text-gray-600">Keep your productivity at peak levels with complete automation for all your systems and applications.</p>
      </div>
    </div>
    <div class="flex flex-row items-start text-left gap-5">
      <img src="data:image/webp;base64,UklGRmQDAABXRUJQVlA4WAoAAAAQAAAANgAAOAAAQUxQSN4CAAABkFXbdtBaW0IkHAmREAcXB42D4oA4eDgIDngOIgEJR0IkrA9SClUQERMgSdb4zWPSl073nwS7Fmn6zcJ8LVElkxSkEJ7IlO8mXlqIav3HrJpiDZrmH/vNrzIt/+ZGvWKNX/bXB3Oco/zmjsM02iiJqt/MlEQfOboQ3mstaWRrPa9SrsN4UiMO8E9hY+jpNDGOOhiug430jTm9TOndYJGU2VJKO0mWhuEmc9agc+6kU5G0kXTxno1VWtpRTRN+7e3nY76pd1MFcFMjXWoM13sS/5XxFFeqMqsy4yTZUPdkilb+pIDL+K/oAzddfCCfuiJNVzeG8z2JqoQnq1RNrJINg1R8eFPoPWgH6KadPyXGQRdvUaG+wRvUNy5lemvNSYOQUkrrLcGBSTIH0qlK2kaB8R0yp9d3Xg/IumYmo9da2z2yjWFLuhY7L8MlZUpw/n0l2VTKO2qYOUopB3MH9g/HASzfXc98LhlYJUWHIzrLN1aG00BTzjkvUKSphPQuZQq5BJmzfLEyPEbDjUOSUmO4SFKGcM2mfI6XzKnSAu2d3xu4aYKsU6QN7rXOX6Ynne2g6WDWQE57EDcnKixtfykczAVv7d/JnCfXTJU5wKLEETpnlyS7eWc2UyOpsVruJB1YMPMPd29kSQcSXVJh1kaS5Dx9coJwSTPlZ2YL2klqLDE5pkYMZg6uBwvAPFMUO0CR4eacH4mtNbp1omxr+0tqvDLeWnlGUuj8zXjSOey4DmZJvYdHwsEhFdjfKa8dN2V4SRvLIys9SMrOcA2SZnqQdWp+8B8sUlhslbrmkIpkTpEUnaeX2GGWZB2qOUXDXL7cOMp45yil09lxSZnDgaKbZ14aB1yKnawPxZyiuxv2QU6QLEn0WmujyCbdjuvzStawM3zpwUS9kFlHls5RTxZeFwI+eja899Zap/tFaK211R4x5/7+esLZk90bC93uSxy6v7DeV3i23ZcOf7R8BVZQOCBgAAAAsAQAnQEqNwA5AD5hLJJGJCKhoSoIAIAMCWkAAE1rQte+aeeUDyPkFz2ImXYQAAD+9zKBl+Ysew2Hn9v4QIPAi8ANmQuo/eHwhA8KDfuOlOVklINSSVYpdcn8dFn+fUAA" alt="customizeable" class="w-14 h-14 object-contain shrink-0" loading="lazy"/>
      <div class="flex flex-col gap-2">
        <h3 class="text-xl font-bold text-ofis-ink">Customizable Application and Modular System</h3>
        <p class="text-base text-gray-600">Tailor your package based on the applications and processes that best fits your company's needs and budget.</p>
      </div>
    </div>
    <div class="flex flex-row items-start text-left gap-5">
      <img src="data:image/webp;base64,UklGRvwCAABXRUJQVlA4WAoAAAAQAAAANgAANgAAQUxQSKcCAAABoFXbbt5a2xAEQRACQQxuGcQMWgYxg2MGKYNeBoJgCIIgCPshjk+a7+c5IiYAX5bnHslw90iGtwW3lC2Zvu+7k+T+6aQvN7CkG8alMxZoDf587ckwzJbgCuib8qUnu2JegwZA8V3NEAy1LwNohADAc/uGUzEuLCMY90OlX premature-end" alt="affordable" class="w-14 h-14 object-contain shrink-0" loading="lazy"/>
      <div class="flex flex-col gap-2">
        <h3 class="text-xl font-bold text-ofis-ink">Affordable Pricing</h3>
        <p class="text-base text-gray-600">Choose the best smart office plan that fit your company's budget and need.</p>
      </div>
    </div>
    <div class="flex flex-row items-start text-left gap-5">
      <img src="data:image/webp;base64,UklGRngEAABXRUJQVlA4WAoAAAAQAAAANgAANwAAQUxQSCQEAAABkE5teyZJN8KL8CIUQgymDCoGVQYVgy6DKoNagyAE4UUIwvOj0h/nrEBETADjtdbqfGhTXksp65yMrRlDv2t98aElXD1UMAcsHU2ftq4TMGdXDTnTm6bo+iPU1 premature-end" alt="Open to Connect" class="w-14 h-14 object-contain shrink-0" loading="lazy"/>
      <div class="flex flex-col gap-2">
        <h3 class="text-xl font-bold text-ofis-ink">Open to Connect with Other System</h3>
        <p class="text-base text-gray-600">Our system is integrated and easily connects to other systems or applications that your company already has in place.</p>
      </div>
    </div>
  </div>
</section>

<!-- Client Testimonial Section -->
<section class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col py-12 md:py-16">
  <h2 class="text-3xl md:text-4xl font-bold text-ofis-ink text-center leading-tight">What Our Client Say</h2>

  <div class="max-w-5xl mx-auto w-full mt-12 flex flex-col items-center text-center">
    <div class="flex items-center justify-center gap-2 mb-6" aria-label="5 out of 5 stars">
      @for($i=0; $i<5; $i++)
        <svg class="w-6 h-6 text-ofis-yellow" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.196-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.05 9.384c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.957z"/></svg>
      @endfor
    </div>

    <p class="text-ofis-ink leading-relaxed max-w-4xl">
      "Using OFIS has made our team more efficient and comfortable. It helps us focus on important tasks easily. The Smart Office technology has given our office a modern feel and has made our daily operations more productive and effective by streamlining our processes. With 100% support from BPT and their expertise in this field, OFIS is perfectly suited for modern business needs"
    </p>

    <div class="mt-10">
      <h3 class="text-lg font-bold text-ofis-ink">Andreas Vasallo</h3>
      <p class="text-ofis-ink/80 mt-1">General Affairs at MRT Jakarta</p>
    </div>
  </div>
</section>

<!-- Form Studio Contact Section -->
<section id="contact" class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col py-12 md:py-16">
  <h2 class="text-3xl md:text-4xl font-bold text-ofis-ink text-center leading-tight mb-3">
    {{ t('home.contact_h2', 'Let Us Help You Through Your Smart Office Transformation') }}
  </h2>
  <p class="text-ofis-ink/70 text-center mb-10 max-w-2xl mx-auto">
    {{ t('home.contact_p', 'Fill out the form below and let our team help you find the OFIS solution that is perfectly tailored to meet your needs.') }}
  </p>

  <div class="w-full max-w-5xl mx-auto">
    @if (session('success'))
      <div class="bg-emerald-500/20 border border-emerald-500/40 text-emerald-800 px-6 py-4 rounded-2xl mb-6 font-medium text-sm text-center">
        <strong>✓ Success!</strong> {{ session('success') }}
      </div>
    @endif

    @if (isset($errors) && $errors->any())
      <div class="bg-rose-500/20 border border-rose-500/40 text-rose-800 px-6 py-4 rounded-2xl mb-6 font-medium text-sm">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('forms.submit', 'contact-form') }}" method="POST" class="ofis-form bg-white border border-gray-100 rounded-3xl shadow-[0_20px_60px_-15px_rgba(0,0,0,0.12)] p-6 md:p-10 grid md:grid-cols-2 gap-x-6 gap-y-5">
      @csrf
      <div class="field">
        <label for="f-name" class="field-label">{{ t('form.name', 'Name') }} <span class="text-ofis-yellow">*</span></label>
        <input id="f-name" type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" class="field-input" required/>
      </div>

      <div class="field">
        <label for="f-email" class="field-label">{{ t('form.email', 'Corporate Email Address') }} <span class="text-ofis-yellow">*</span></label>
        <input id="f-email" type="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" class="field-input" required/>
      </div>

      <div class="field">
        <label for="f-phone" class="field-label">Phone Number <span class="text-ofis-yellow">*</span></label>
        <input id="f-phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+62 8xx xxxx xxxx" class="field-input" required/>
      </div>

      <div class="field">
        <label for="f-position" class="field-label">Position <span class="text-ofis-yellow">*</span></label>
        <div class="relative">
          <select id="f-position" name="position" class="field-input appearance-none pr-10" required>
            <option value="" disabled selected>Select your position</option>
            <option value="President Director">President Director</option>
            <option value="C-Level">C-Level</option>
            <option value="General Manager">General Manager</option>
            <option value="Manager">Manager</option>
            <option value="Supervisor / Team Leader">Supervisor / Team Leader</option>
            <option value="Staff">Staff</option>
            <option value="Others">Others</option>
          </select>
          <svg class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
      </div>

      <div class="field md:col-span-2">
        <label for="f-company" class="field-label">{{ t('form.company', 'Company') }} <span class="text-ofis-yellow">*</span></label>
        <input id="f-company" type="text" name="company" value="{{ old('company') }}" placeholder="Your company name" class="field-input" required/>
      </div>

      <div class="field md:col-span-2">
        <label for="f-message" class="field-label">{{ t('form.message', 'Message') }} <span class="text-ofis-yellow">*</span></label>
        <textarea id="f-message" name="message" rows="5" placeholder="Tell us about your needs…" class="field-input resize-y" required>{{ old('message') }}</textarea>
      </div>

      <div class="md:col-span-2 flex justify-center mt-4">
        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-[#fab54f] hover:bg-[#fab54f]/90 text-ofis-ink font-semibold px-12 py-3.5 rounded-full shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-0.5 w-full md:w-auto">
          {{ t('form.submit', 'Send Message') }}
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </button>
      </div>
    </form>
  </div>
</section>

<style>
  .ofis-form .field { display: flex; flex-direction: column; gap: 0.5rem; }
  .ofis-form .field-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-ofis-ink);
    letter-spacing: 0.01em;
  }
  .ofis-form .field-input {
    width: 100%;
    padding: 0.75rem 1rem;
    background: #F8FAFB;
    border: 1.5px solid #E5EEF1;
    border-radius: 0.75rem;
    color: var(--color-ofis-ink);
    font-size: 0.95rem;
    transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
    outline: none;
  }
  .ofis-form .field-input::placeholder { color: #9aa6ab; }
  .ofis-form .field-input:hover {
    border-color: #cfdde2;
    background: #fff;
  }
  .ofis-form .field-input:focus {
    border-color: var(--color-ofis-teal);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(77, 138, 151, 0.12);
  }
</style>

<!-- Newsletter Subscribe Banner -->
<section class="relative w-full bg-ofis-teal">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 py-8">
    <div class="relative flex flex-row items-center gap-4">
      <img src="data:image/webp;base64,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" alt="subscribemail" class="w-16 h-16 object-contain shrink-0" loading="lazy"/>
      <div class="flex flex-col gap-1">
        <h3 class="text-2xl md:text-3xl font-semibold text-white leading-tight">Stay Ahead Of The Game, Subscribe Now!</h3>
        <p class="text-sm text-white/90">Join our newsletter to be the first to know about the latest OFIS promotions, news, and breakthrough innovations!</p>
      </div>
    </div>
    <div class="relative flex shrink-0">
      <a href="#contact" class="inline-block transition py-3 px-6 bg-[#fab54f] hover:bg-[#fab54f]/90 text-ofis-ink text-base font-semibold rounded-full whitespace-nowrap">Subscribe now</a>
    </div>
  </div>
</section>

<!-- News & Insights (Swiper Blog Carousel) Section -->
<section class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-col py-12 md:py-16">
  <h2 class="text-3xl md:text-4xl font-bold text-ofis-ink text-center leading-tight mb-10">
    {{ t('home.blog_title', 'News & Insights') }}
  </h2>

  <div class="relative news-swiper-wrap">
    <button type="button" aria-label="Previous" class="news-prev hidden md:flex absolute -left-4 lg:-left-6 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white border border-gray-200 shadow-md items-center justify-center text-ofis-ink hover:bg-ofis-teal hover:text-white hover:border-ofis-teal transition">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button type="button" aria-label="Next" class="news-next hidden md:flex absolute -right-4 lg:-right-6 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white border border-gray-200 shadow-md items-center justify-center text-ofis-ink hover:bg-ofis-teal hover:text-white hover:border-ofis-teal transition">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
    </button>

    <div class="swiper news-swiper">
      <div class="swiper-wrapper pb-2">
        @forelse($posts as $index => $post)
          @php
            $isTeal = ($index % 2 === 0);
            $cardClass = $isTeal ? 'news-card--teal' : 'news-card--light';
            $imageSrc = $post->featured_image ? asset('storage/' . $post->featured_image) : theme_asset('manfaat-access-control-system-untuk-keamanan-dan-efisiensi-bisnis-anda-access-control-system-ofis-1024x576.jpg-wwVIGsk4.webp');
          @endphp
          <div class="swiper-slide h-auto">
            <article class="news-card {{ $cardClass }} group rounded-3xl p-6 flex flex-col h-full">
              <div class="flex items-center gap-3 mb-5">
                <span class="badge">Blog Detail</span>
                <span class="divider"></span>
                <span class="date">{{ optional($post->published_at ?? $post->created_at)->format('F d, Y') }}</span>
              </div>
              <a href="{{ url('/blog/' . $post->slug) }}" class="block overflow-hidden rounded-2xl mb-5">
                <img src="{{ $imageSrc }}" alt="{{ $post->title }}" class="w-full h-44 object-cover group-hover:scale-105 transition duration-500" loading="lazy"/>
              </a>
              <h3 class="title">{{ $post->title }}</h3>
              <p class="excerpt">{{ Str::limit(strip_tags($post->content), 120) }}</p>
              <a href="{{ url('/blog/' . $post->slug) }}" class="read-more mt-auto pt-5 inline-flex items-center gap-2">
                Continue Reading
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </a>
            </article>
          </div>
        @empty
          <div class="swiper-slide h-auto">
            <article class="news-card news-card--teal group rounded-3xl p-6 flex flex-col h-full">
              <div class="flex items-center gap-3 mb-5">
                <span class="badge">Blog Detail</span>
                <span class="divider"></span>
                <span class="date">December 17, 2024</span>
              </div>
              <a href="{{ url('/blog') }}" class="block overflow-hidden rounded-2xl mb-5">
                <img src="{{ theme_asset('manfaat-access-control-system-untuk-keamanan-dan-efisiensi-bisnis-anda-access-control-system-ofis-1024x576.jpg-wwVIGsk4.webp') }}" alt="" class="w-full h-44 object-cover group-hover:scale-105 transition duration-500" loading="lazy"/>
              </a>
              <h3 class="title">Manfaat Access Control System untuk Keamanan dan Efisiensi Bisnis Anda</h3>
              <p class="excerpt">Sistem access control modern membantu menjaga area sensitif tetap aman sambil mempermudah lalu-lintas karyawan harian…</p>
              <a href="{{ url('/blog') }}" class="read-more mt-auto pt-5 inline-flex items-center gap-2">
                Continue Reading
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </a>
            </article>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Bottom Visit Us Row -->
  <div class="mt-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
    <div class="flex items-center gap-5">
      <h4 class="text-sm font-bold tracking-[0.2em] text-ofis-ink uppercase">VISIT US</h4>
      <ul class="flex items-center gap-4">
        <li><a href="https://www.facebook.com/bluepowerid/" aria-label="Facebook" class="text-ofis-ink hover:text-ofis-teal transition"><svg class="w-5 h-5" viewBox="0 0 320 512" fill="currentColor"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg></a></li>
        <li><a href="https://www.instagram.com/bluepowerid/" aria-label="Instagram" class="text-ofis-ink hover:text-ofis-teal transition"><svg class="w-5 h-5" viewBox="0 0 448 512" fill="currentColor"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg></a></li>
        <li><a href="https://id.linkedin.com/company/blue-power-technology" aria-label="LinkedIn" class="text-ofis-ink hover:text-ofis-teal transition"><svg class="w-5 h-5" viewBox="0 0 448 512" fill="currentColor"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/></svg></a></li>
        <li><a href="https://www.youtube.com/channel/UCF1I-9jR-FxXz7PPeatYa1w" aria-label="YouTube" class="text-ofis-ink hover:text-ofis-teal transition"><svg class="w-5 h-5" viewBox="0 0 576 512" fill="currentColor"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"/></svg></a></li>
        <li><a href="https://www.tiktok.com/@bluepowerid" aria-label="TikTok" class="text-ofis-ink hover:text-ofis-teal transition"><svg class="w-5 h-5" viewBox="0 0 448 512" fill="currentColor"><path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"/></svg></a></li>
      </ul>
    </div>

    <a href="{{ url('/blog') }}" class="inline-flex items-center gap-2 self-start md:self-auto px-7 py-3 rounded-full border-2 border-ofis-ink/80 text-ofis-ink font-semibold hover:bg-ofis-ink hover:text-white transition">
      Explore More
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </a>
  </div>
</section>

<style>
  .news-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
  .news-card:hover { transform: translateY(-4px); }
  .news-card .badge {
    display: inline-block;
    padding: 0.3rem 0.85rem;
    border-radius: 0.6rem;
    font-size: 0.8rem;
    font-weight: 600;
  }
  .news-card .divider { width: 1px; height: 1rem; }
  .news-card .date { font-size: 0.85rem; }
  .news-card .title { font-size: 1.25rem; font-weight: 700; line-height: 1.35; }
  .news-card .excerpt {
    margin-top: 0.85rem;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .news-card .read-more { font-weight: 600; }

  .news-card--teal { background: var(--color-ofis-teal); color: #fff; }
  .news-card--teal .badge { background: #fff; color: var(--color-ofis-ink); }
  .news-card--teal .divider { background: rgba(255,255,255,0.4); }
  .news-card--teal .date { color: rgba(255,255,255,0.85); }
  .news-card--teal .title { color: #fff; }
  .news-card--teal .excerpt { color: rgba(255,255,255,0.9); }
  .news-card--teal .read-more { color: #fff; }
  .news-card--teal .read-more:hover { color: #ffe9c2; }

  .news-card--light {
    background: #fff;
    color: var(--color-ofis-ink);
    border: 1px solid #e8eef0;
    box-shadow: 0 14px 36px -18px rgba(0,0,0,0.18);
  }
  .news-card--light .badge { background: #F1F6F7; color: var(--color-ofis-ink); }
  .news-card--light .divider { background: #d6dee0; }
  .news-card--light .date { color: #6b7785; }
  .news-card--light .title { color: var(--color-ofis-ink); }
  .news-card--light .excerpt { color: #5b6770; }
  .news-card--light .read-more { color: var(--color-ofis-teal); }
  .news-card--light .read-more:hover { color: var(--color-ofis-teal-dark); }

  .news-swiper-wrap .swiper { padding-bottom: 1rem; }
</style>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (typeof Swiper !== 'undefined') {
      new Swiper('.news-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        breakpoints: {
          640: { slidesPerView: 2, spaceBetween: 20 },
          1024: { slidesPerView: 3, spaceBetween: 24 },
        },
        navigation: {
          nextEl: '.news-next',
          prevEl: '.news-prev',
        },
      });
    }
  });
</script>
@endpush
@endsection