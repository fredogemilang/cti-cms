@extends('csi::layouts.app')

@section('content')
@php
    $heroTitle = $page->block('hero_title', 'Privacy Policy');
    $heroSubtitle = $page->block(
        'hero_subtitle',
        'At Converion Solution Informatica (CSI), ensuring the privacy, confidentiality, and security of your personal data is fundamental to our enterprise mission. Learn how we collect, safeguard, and process your information across all digital touchpoints.'
    );
    $introHeading = $page->block('intro_heading', 'Introduction & Scope');
    $introBody = (string) $page->block('intro_body', '');
    $tocHeading = $page->block('toc_heading', 'Policy Contents');

    $clauses = array_values(array_filter(
        $page->repeaterBlock('clauses_list', []),
        fn ($item) => is_array($item) && trim((string) ($item['title'] ?? '')) !== ''
    ));

    $totalClauses = count($clauses);

    // Protect all inline email addresses against Cloudflare Email Obfuscation (/cdn-cgi/l/email-protection)
    $renderSafeRichText = static function (string $text): string {
        $escaped = e($text);
        $withEmails = preg_replace_callback(
            '/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i',
            static fn (array $m) => function_exists('safe_email')
                ? safe_email($m[0], 'text-[#04B1C7] font-semibold hover:underline')
                : e($m[0]),
            $escaped
        ) ?? $escaped;

        return $withEmails;
    };

    $introParagraphs = array_values(array_filter(
        array_map('trim', preg_split('/\r\n\r\n|\n\n/', $introBody) ?: [])
    ));
@endphp

<div class="relative bg-white overflow-x-clip">
  <!-- Ambient Blur & Decorative Arc Lines (Figma 607:5163) -->
  <img src="{{ theme_asset('images/hero-glow-layer.png') }}"
       alt=""
       class="pointer-events-none select-none absolute left-1/2 -translate-x-1/2 top-[120px] w-[897px] max-w-none h-[427px] opacity-50 blur-[100px] -scale-y-100 rotate-180 z-0" />
  <img src="{{ theme_asset('images/about/about-arc-right.svg') }}"
       alt=""
       class="hidden md:block pointer-events-none select-none absolute -right-[50px] top-[19px] w-[162px] h-[717px] z-0" />
  <img src="{{ theme_asset('images/about/about-arc-left.svg') }}"
       alt=""
       class="hidden md:block pointer-events-none select-none absolute -left-[64px] top-[122px] w-[162px] h-[717px] rotate-180 z-0" />

  <!-- ========================================================================= -->
  <!-- 1. HERO HEADER (Figma 607:5220)                                           -->
  <!-- ========================================================================= -->
  <section id="privacy-hero" class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[52px] pb-[44px] sm:py-[60px]">
    <div class="flex flex-col gap-[18px] items-center text-center w-full">
      <x-seo-breadcrumbs :entity="$page" class="mb-1 justify-center" />

      <h1 id="privacy-hero-title"
          class="text-[34px] sm:text-[42px] xl:text-[46px] font-bold leading-[1.2] pb-1 bg-gradient-to-r from-[#04B1C7] from-[40%] to-[#2296E4] bg-clip-text text-transparent">
        {{ $heroTitle }}
      </h1>

      @if(!empty($heroSubtitle))
        <p id="privacy-hero-subtitle" class="text-base sm:text-[18px] leading-[26px] text-black font-normal max-w-[1040px]">
          {!! nl2br(e($heroSubtitle)) !!}
        </p>
      @endif
    </div>
  </section>

  <!-- ========================================================================= -->
  <!-- 2. INTRODUCTION & SCOPE BANNER (Figma 607:5724)                           -->
  <!-- ========================================================================= -->
  @if($introHeading !== '' || count($introParagraphs) > 0)
  <section id="privacy-intro" class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px]">
    <div class="border-[0.5px] border-[#04B1C7] rounded-[12px] p-[24px] sm:p-[30px]"
         style="background-image: linear-gradient(116.19deg, rgb(239, 253, 255) 0%, rgb(255, 255, 255) 50%, rgb(255, 246, 238) 100%);">
      <div class="flex flex-col gap-[18px] items-start w-full">
        <div class="flex items-center gap-[10px] w-full">
          <img src="{{ theme_asset('images/privacy/shield-check.svg') }}"
               alt=""
               class="w-[32px] h-[32px] shrink-0 object-contain" />
          <h2 class="text-[21px] font-bold leading-[28px] text-black">
            {{ $introHeading }}
          </h2>
        </div>

        <div class="h-px w-full bg-[#ED7F22]"></div>

        <div class="text-[15px] sm:text-[16px] leading-[26px] text-black font-normal space-y-4 w-full">
          @foreach($introParagraphs as $para)
            <p>{!! $renderSafeRichText($para) !!}</p>
          @endforeach
        </div>
      </div>
    </div>
  </section>
  @endif

  <!-- ========================================================================= -->
  <!-- 3. INTERACTIVE CLAUSE EXPLORER (Figma 647:9241)                           -->
  <!-- ========================================================================= -->
  @if($totalClauses > 0)
  <section id="privacy-clauses"
           class="relative z-10 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-[120px] pt-[30px] pb-[60px]"
           x-data="{
              activeClause: 0,
              total: {{ $totalClauses }},
              selectClause(index) {
                this.activeClause = index;
                if (window.innerWidth < 1024) {
                  const reader = document.getElementById('privacy-clause-reader');
                  if (reader) {
                    reader.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  }
                }
              },
              nextClause() {
                this.activeClause = (this.activeClause + 1) % this.total;
                const reader = document.getElementById('privacy-clause-reader');
                if (reader) {
                  reader.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
              },
              prevClause() {
                if (this.activeClause > 0) {
                  this.activeClause = this.activeClause - 1;
                }
              }
           }">
    <div class="flex flex-col lg:flex-row gap-[30px] items-start w-full py-[10px]">
      <!-- Left Sticky Sidebar: Policy Contents -->
      <aside class="w-full lg:w-[380px] xl:w-[400px] shrink-0 bg-white border-[0.5px] border-[#04B1C7] rounded-[12px] p-[22px] sm:p-[30px] lg:sticky lg:top-24">
        <div class="flex flex-col gap-[18px] items-start w-full">
          <div class="flex items-center gap-[10px] w-full">
            <div class="relative w-[16px] h-[16px] shrink-0" aria-hidden="true">
              <img src="{{ theme_asset('images/privacy/toc-0.svg') }}" alt="" class="absolute left-[18.78%] top-[15.62%] w-[74.97%] h-[12.5%]" />
              <img src="{{ theme_asset('images/privacy/toc-1.svg') }}" alt="" class="absolute left-0 top-[15.65%] w-[12.57%] h-[12.5%]" />
              <img src="{{ theme_asset('images/privacy/toc-2.svg') }}" alt="" class="absolute left-[0.04%] top-[43.76%] w-[12.53%] h-[12.51%]" />
              <img src="{{ theme_asset('images/privacy/toc-3.svg') }}" alt="" class="absolute left-[0.03%] top-[71.88%] w-[12.54%] h-[12.5%]" />
              <img src="{{ theme_asset('images/privacy/toc-4.svg') }}" alt="" class="absolute left-[18.78%] top-[43.73%] w-[81.22%] h-[12.5%]" />
              <img src="{{ theme_asset('images/privacy/toc-5.svg') }}" alt="" class="absolute left-[18.78%] top-[71.85%] w-[53.1%] h-[12.5%]" />
            </div>
            <h2 class="text-[16px] font-bold leading-snug text-black">
              {{ $tocHeading }}
            </h2>
          </div>

          <div class="h-px w-full bg-[#ED7F22]/35"></div>

          <div class="flex flex-col gap-[5px] w-full">
            @foreach($clauses as $idx => $clause)
              <button type="button"
                      @click="selectClause({{ $idx }})"
                      :aria-pressed="(activeClause === {{ $idx }}).toString()"
                      :class="activeClause === {{ $idx }}
                          ? 'bg-[#ED7F22] text-white'
                          : 'bg-transparent text-black hover:bg-[#EFFDFF]'"
                      class="w-full flex items-center gap-[10px] p-[10px] rounded-[7px] text-left cursor-pointer transition-colors">
                <span :class="activeClause === {{ $idx }} ? 'bg-white text-black' : 'bg-[rgba(30,171,179,0.12)] text-black'"
                      class="w-[32px] h-[32px] rounded-full shrink-0 flex items-center justify-center text-[13px] font-bold leading-[21px] transition-colors">
                  {{ $idx + 1 }}
                </span>

                <span :class="activeClause === {{ $idx }} ? 'font-bold text-white' : 'font-normal text-black'"
                      class="flex-1 min-w-0 text-[13px] leading-[21px] text-left">
                  {{ $clause['title'] }}
                </span>

                <span class="shrink-0 w-5 h-5 flex items-center justify-center text-[16px] font-semibold leading-none" aria-hidden="true">
                  <span x-show="activeClause === {{ $idx }}">&minus;</span>
                  <span x-show="activeClause !== {{ $idx }}" x-cloak>+</span>
                </span>
              </button>
            @endforeach
          </div>
        </div>
      </aside>

      <!-- Right Active Clause Reader + Clause Pagination -->
      <div id="privacy-clause-reader" class="flex-1 min-w-0 flex flex-col gap-[24px] sm:gap-[30px] scroll-mt-24 w-full">
        <div class="bg-white border-[0.5px] border-[#04B1C7] rounded-[12px] p-[24px] sm:p-[30px] w-full">
          @foreach($clauses as $idx => $clause)
            @php
                $clauseCards = [];
                if (! empty($clause['cards'])) {
                    $decodedCards = is_array($clause['cards']) ? $clause['cards'] : json_decode((string) $clause['cards'], true);
                    if (is_array($decodedCards)) {
                        $clauseCards = $decodedCards;
                    }
                }
                $clauseItems = array_values(array_filter(
                    array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($clause['items'] ?? '')) ?: [])
                ));
                $introBlocks = array_values(array_filter(
                    array_map('trim', preg_split('/\r\n\r\n|\n\n/', (string) ($clause['intro'] ?? '')) ?: [])
                ));
                $outroBlocks = array_values(array_filter(
                    array_map('trim', preg_split('/\r\n\r\n|\n\n/', (string) ($clause['outro'] ?? '')) ?: [])
                ));
            @endphp

            <article x-show="activeClause === {{ $idx }}"
                     @if($idx !== 0) x-cloak @endif
                     class="flex flex-col gap-[18px] w-full">
              <!-- Clause Header -->
              <div class="flex items-center gap-[10px] w-full">
                <img src="{{ theme_asset('images/privacy/clause-user.svg') }}"
                     alt=""
                     class="w-[20px] h-[22px] shrink-0 object-contain" />
                <h3 class="text-[16px] sm:text-[18px] font-bold leading-snug text-black">
                  {{ $clause['title'] }}
                </h3>
              </div>

              <div class="h-px w-full bg-[#ED7F22]/35"></div>

              <!-- Opening Paragraphs -->
              @foreach($introBlocks as $para)
                <p class="text-[13px] sm:text-[14px] font-normal leading-[22px] text-black">
                  {!! $renderSafeRichText($para) !!}
                </p>
              @endforeach

              <!-- Optional 3-Column Feature Mini Cards (Figma Clause 1 layout) -->
              @if(count($clauseCards) > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-[10px] w-full">
                  @foreach($clauseCards as $card)
                    @php
                        $cardIcon = trim((string) ($card['icon'] ?? 'card-identifiable.svg'));
                        $cardTitle = trim((string) ($card['title'] ?? ''));
                        $cardDesc = trim((string) ($card['description'] ?? ''));
                    @endphp
                    <div class="bg-[#F0F5FF] rounded-[7px] px-[21px] py-[18px] flex flex-col gap-[10px] items-start">
                      <div class="flex items-center gap-[10px] w-full">
                        <img src="{{ theme_asset('images/privacy/'.$cardIcon) }}"
                             alt=""
                             class="w-[18px] h-[18px] shrink-0 object-contain" />
                        <h4 class="text-[14px] font-bold leading-[18px] text-black">
                          {{ $cardTitle }}
                        </h4>
                      </div>
                      <p class="text-[13px] font-normal leading-[18px] text-black">
                        {!! $renderSafeRichText($cardDesc) !!}
                      </p>
                    </div>
                  @endforeach
                </div>
              @endif

              <!-- Structured Sub-Items -->
              @if(count($clauseItems) > 0)
                <div class="flex flex-col gap-[10px] w-full">
                  @foreach($clauseItems as $itemLine)
                    <div class="bg-[#F0F5FF] rounded-[7px] px-[18px] py-[14px] text-[13px] sm:text-[14px] font-normal leading-[21px] text-black">
                      @if(preg_match('/^([a-z]|[ivx]+|\d+)[\.\)]\s+(.+)$/is', $itemLine, $m))
                        <div class="flex items-start gap-2.5">
                          <span class="font-bold text-[#04B1C7] shrink-0">{{ $m[1] }}.</span>
                          <span class="flex-1">{!! $renderSafeRichText($m[2]) !!}</span>
                        </div>
                      @elseif(str_contains($itemLine, ':') && mb_strlen(explode(':', $itemLine, 2)[0]) <= 45)
                        @php [$itemHead, $itemRest] = array_map('trim', explode(':', $itemLine, 2)); @endphp
                        <div>
                          <strong class="font-bold text-black">{{ $itemHead }}:</strong> {!! $renderSafeRichText($itemRest) !!}
                        </div>
                      @else
                        <div>{!! $renderSafeRichText($itemLine) !!}</div>
                      @endif
                    </div>
                  @endforeach
                </div>
              @endif

              <!-- Closing Paragraphs -->
              @foreach($outroBlocks as $para)
                <p class="text-[13px] sm:text-[14px] font-normal leading-[22px] text-black">
                  {!! $renderSafeRichText($para) !!}
                </p>
              @endforeach
            </article>
          @endforeach
        </div>

        <!-- Bottom Clause Navigation Card -->
        <div class="bg-white border-[0.5px] border-[#04B1C7] rounded-[12px] px-[24px] sm:px-[30px] py-[22px] sm:py-[26px] flex flex-wrap items-center justify-between gap-4 w-full">
          <p class="text-[13px] leading-[18px] text-[#656F80]">
            <span>{{ t('privacy_page.reading', 'Reading:') }} </span>
            <strong class="font-bold text-[#656F80]">
              {{ t('privacy_page.clause', 'Clause') }}
              <span x-text="activeClause + 1">1</span>
              {{ t('privacy_page.of', 'of') }}
              {{ $totalClauses }}
            </strong>
          </p>

          <div class="flex items-center gap-3 ml-auto">
            <button type="button"
                    x-show="activeClause > 0"
                    x-cloak
                    @click="prevClause()"
                    class="inline-flex items-center justify-center gap-2 px-[15px] py-[10px] rounded-[6px] border border-[#E4EBFA] hover:border-[#04B1C7] text-[14px] font-medium text-[#213C41] transition-colors cursor-pointer">
              <img src="{{ theme_asset('images/privacy/chevron-next.svg') }}" alt="" class="w-[5px] h-[9px] invert opacity-70" />
              <span>{{ t('privacy_page.prev_clause', 'Previous') }}</span>
            </button>

            <button type="button"
                    @click="nextClause()"
                    class="bg-[#ED7F22] hover:bg-[#d86f1b] text-white inline-flex items-center justify-center gap-[12px] px-[18px] py-[10px] rounded-[6px] text-[14px] font-medium leading-[16px] transition-colors cursor-pointer min-w-[160px]">
              <span x-text="activeClause === total - 1 ? '{{ t('privacy_page.back_to_first', 'Back to Clause 1') }}' : '{{ t('privacy_page.next_clause', 'Next Clause') }}'">
                {{ t('privacy_page.next_clause', 'Next Clause') }}
              </span>
              <img src="{{ theme_asset('images/privacy/chevron-next.svg') }}" alt="" class="w-[5px] h-[9px] rotate-180" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
  @endif
</div>

<!-- ========================================================================= -->
<!-- 4. SHARED CONTACT FORM FOOTER SECTION (Figma 607:5371)                    -->
<!-- ========================================================================= -->
@include('csi::partials.contact-section')
@endsection
