@php
    $channels = $contactHighlights ?? null;

    if (! is_array($channels) || empty($channels)) {
        $contactPage = \App\Models\Page::where('slug', 'contact-us')->first();
        if ($contactPage) {
            $raw = $contactPage->repeaterBlock('contact_highlights', []);
            if (! empty($raw)) {
                $channels = $raw;
            }
        }
    }

    if (! is_array($channels) || empty($channels)) {
        $isId = app()->getLocale() === 'id';
        $channels = [
            [
                'icon_type' => 'phone',
                'label' => $isId ? 'Telepon' : 'Phone',
                'value' => '+6221 806 22278',
                'url' => 'tel:+622180622278',
            ],
            [
                'icon_type' => 'whatsapp',
                'label' => 'Converion Care Hub (WA)',
                'value' => '+62822 9992 2278',
                'url' => 'https://wa.me/6282299922278',
            ],
            [
                'icon_type' => 'email',
                'label' => 'Email',
                'value' => 'marketing@converion.com',
                'url' => 'mailto:marketing@converion.com',
            ],
        ];
    }

    $channels = array_values(array_filter(
        $channels,
        fn ($item) => is_array($item) && trim((string) ($item['value'] ?? '')) !== ''
    ));
@endphp

@if(count($channels) > 0)
<!--email_off-->
<section id="contact-info-bar"
         class="w-full"
         style="background: linear-gradient(90deg, #04B1C7 35%, #2296E4 70%);">
  <div class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] py-4 md:py-0 md:min-h-[100px] flex flex-col md:flex-row items-start md:items-center justify-between gap-2 md:gap-6">
    @foreach($channels as $channel)
      @php
          $iconType = strtolower((string) ($channel['icon_type'] ?? 'phone'));
          $href = trim((string) ($channel['url'] ?? ''));
          $val = (string) ($channel['value'] ?? '');
          $isEmailChannel = $iconType === 'email' || str_starts_with(strtolower($href), 'mailto:') || str_contains($val, '@');
          $isExternal = str_starts_with($href, 'http://') || str_starts_with($href, 'https://');
      @endphp
      @if($isEmailChannel)
        @php
            $mailTarget = $href !== '' ? $href : 'mailto:'.$val;
            $valB64 = base64_encode($val);
            $hrefB64 = base64_encode($mailTarget);
        @endphp
        <a href="#contact-info-bar"
           x-data="{ e: '{{ $valB64 }}', m: '{{ $hrefB64 }}' }"
           x-init="$el.href = atob(m)"
           class="group flex items-center gap-[15px] px-[12px] sm:px-[21px] py-[14px] md:py-[18px] rounded-[10px] text-white hover:bg-white/10 transition-colors w-full md:w-auto">
          <div class="w-[40px] h-[40px] rounded-[5px] border border-white/50 flex items-center justify-center shrink-0 p-[8px] group-hover:border-white transition-colors" aria-hidden="true">
            <img src="{{ theme_asset('images/contact/mail-icon.svg') }}" alt="" class="w-[20px] h-[16px] object-contain" />
          </div>

          <div class="flex flex-col gap-[3px] min-w-0">
            <span class="text-[13px] font-normal leading-[21px] text-white/95">{{ $channel['label'] ?? '' }}</span>
            <span x-text="atob(e)" class="text-[14px] font-bold leading-[18px] text-white break-all sm:break-normal"></span>
          </div>
        </a>
      @else
        <a href="{{ $href !== '' ? $href : '#contact' }}"
           @if($isExternal) target="_blank" rel="noopener noreferrer" @endif
           class="group flex items-center gap-[15px] px-[12px] sm:px-[21px] py-[14px] md:py-[18px] rounded-[10px] text-white hover:bg-white/10 transition-colors w-full md:w-auto">
          <div class="w-[40px] h-[40px] rounded-[5px] border border-white/50 flex items-center justify-center shrink-0 p-[8px] group-hover:border-white transition-colors" aria-hidden="true">
            @if($iconType === 'whatsapp')
              <img src="{{ theme_asset('images/contact/wa-icon.svg') }}" alt="" class="w-[22px] h-[22px] object-contain" />
            @else
              <div class="relative w-[22px] h-[22px]">
                <img src="{{ theme_asset('images/contact/phone-ring-0.svg') }}" alt="" class="absolute inset-[8.33%] w-[83.34%] h-[83.34%] object-contain" />
                <img src="{{ theme_asset('images/contact/phone-ring-1.svg') }}" alt="" class="absolute top-[8.34%] right-[8.33%] bottom-[50.13%] left-[50.14%] w-[41.53%] h-[41.53%] object-contain" />
                <img src="{{ theme_asset('images/contact/phone-ring-2.svg') }}" alt="" class="absolute top-[21.87%] right-[21.86%] bottom-[50.13%] left-[50.14%] w-[28%] h-[28%] object-contain" />
                <img src="{{ theme_asset('images/contact/phone-ring-3.svg') }}" alt="" class="absolute top-[35.63%] right-[35.62%] bottom-[50.13%] left-[50.14%] w-[14.24%] h-[14.24%] object-contain" />
              </div>
            @endif
          </div>

          <div class="flex flex-col gap-[3px] min-w-0">
            <span class="text-[13px] font-normal leading-[21px] text-white/95">{{ $channel['label'] ?? '' }}</span>
            <span class="text-[14px] font-bold leading-[18px] text-white break-all sm:break-normal">{{ $val }}</span>
          </div>
        </a>
      @endif
    @endforeach
  </div>
</section>
<!--/email_off-->
@endif
