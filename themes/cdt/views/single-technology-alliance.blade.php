@extends('cdt::layouts.app')

@section('title', $entry->title . ' - ' . t('common.technology_alliance', 'Technology Alliance'))

@section('content')
@php
    $features = $entry->getMeta('features', []);
    $solutionsFeatured = $entry->getMeta('solutions_featured', []);
    $solutionsOther = $entry->getMeta('solutions_other', []);
    $solutionsDescription = $entry->getMeta('solutions_description', '');
    $banner = [
        'badge' => $entry->getMeta('banner_badge', ''),
        'headline' => $entry->getMeta('banner_headline', ''),
        'description' => $entry->getMeta('banner_description', ''),
        'cta' => $entry->getMeta('banner_cta', ''),
    ];
    $videos = $entry->getMeta('videos', []);
    $articles = $entry->getMeta('related_articles', []);
    $badges = $entry->getMeta('badges', []);
    $badgeImages = $entry->getMeta('hero_badge_images', []);
@endphp

<!-- Hero -->
<section class="bg-gradient-to-br from-red-50/20 via-white to-zinc-50/50 pt-8 lg:pt-28 pb-20 relative overflow-hidden">
  <div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute -top-48 -left-48 w-[600px] h-[600px] bg-[radial-gradient(circle,rgba(227,6,19,0.18)_0%,rgba(227,6,19,0)_70%)] rounded-full blur-3xl"></div>
    <div class="absolute top-[20%] -right-48 w-[500px] h-[500px] bg-[radial-gradient(circle,rgba(227,6,19,0.10)_0%,rgba(227,6,19,0)_70%)] rounded-full blur-3xl"></div>
  </div>
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    @php
      $cptPostType = $entry->postType;
      $hasCptArchive = $cptPostType && $cptPostType->has_archive;
    @endphp
    <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-zinc-400 mb-10" data-gsap="fade-in">
      <a href="{{ localized_url('/') }}" class="hover:text-primary transition-colors">{{ t('common.home', 'Home') }}</a>
      @if($hasCptArchive)
        <svg class="w-3 h-3 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ $cptPostType->getArchiveUrl() }}" class="hover:text-primary transition-colors">{{ $cptPostType->plural_label ?: t('common.technology_alliance', 'Technology Alliance') }}</a>
      @endif
      <svg class="w-3 h-3 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      <span class="text-zinc-800 font-bold">{{ $entry->title }}</span>
    </nav>

    @if($entry->featured_image)
    <div class="lg:hidden mb-8 bg-zinc-50/50 border border-zinc-200/80 rounded-3xl p-8 flex flex-col items-center shadow-sm">
      <img src="{{ asset('storage/' . $entry->featured_image) }}" alt="{{ $entry->title }}" class="h-24 w-auto object-contain" />
      <div class="mt-6 text-[10px] font-bold tracking-widest text-zinc-400 uppercase">{{ t('alliance.official_partner', 'Official Technology Partner') }}</div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
      <div class="lg:col-span-8 flex flex-col">
        <div class="mb-6">
          <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight text-zinc-900 mb-4" data-gsap="fade-up">{{ $entry->title }}</h1>

          @if(!empty($badges) && is_array($badges))
          <div class="flex flex-wrap items-center gap-2 mb-4">
            @foreach($badges as $badge)
              @php
                $badgeText = is_array($badge) ? ($badge['text'] ?? $badge['title'] ?? '') : (string)$badge;
              @endphp
              @if(!empty(trim($badgeText)))
                <span class="inline-flex items-center justify-center text-xs font-bold bg-red-100 text-primary px-3 py-1.5 rounded-full whitespace-nowrap">{{ trim($badgeText) }}</span>
              @endif
            @endforeach
          </div>
          @endif

          <div class="w-16 h-1.5 bg-primary rounded-full" data-gsap="line-grow"></div>
        </div>

        @if($badgeImages)
        <div class="flex flex-wrap items-center gap-4 mb-8" data-gsap="fade-up" data-gsap-delay="0.08">
          @foreach($badgeImages as $img)
            @php $imgUrl = resolve_block_asset($img); @endphp
            @if($imgUrl)
            <img src="{{ $imgUrl }}" alt="Certification Badge" class="h-20 w-auto object-contain" loading="lazy" />
            @endif
          @endforeach
        </div>
        @endif

        @if($entry->getTranslation('content'))
        <div data-gsap="fade-up" data-gsap-delay="0.1" class="prose max-w-none text-zinc-600 text-base md:text-lg leading-relaxed mb-12 max-w-3xl">{!! $entry->getTranslation('content') !!}</div>
        @endif

        @if($features)
        <div class="mb-8">
          <h2 data-gsap="fade-up" data-gsap-delay="0.2" class="text-4xl font-light text-zinc-500 leading-tight">{{ t('alliance.why_prefix', 'Why') }}<br><span class="font-bold text-dark">{{ $entry->title }}?</span></h2>
          <div class="h-1 bg-primary mt-4 w-16" data-gsap="line-grow"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
          @foreach($features as $f)
          <div data-gsap="fade-up" data-gsap-delay="{{ $loop->index * 0.1 }}" class="bg-white/70 backdrop-blur-md p-6 rounded-2xl border border-zinc-200/80 shadow-sm hover:shadow-md transition-all">
            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary mb-4">
              {!! render_icon($f['icon'] ?? 'shield', 'w-6 h-6') !!}
            </div>
            <h3 class="font-bold text-zinc-900 text-base mb-2">{{ $f['title'] }}</h3>
            <p class="text-zinc-500 text-xs md:text-sm leading-relaxed">{{ $f['description'] }}</p>
          </div>
          @endforeach
        </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-4" data-gsap="fade-up" data-gsap-delay="0.6">
          <a href="#solutions" class="inline-flex items-center gap-2 bg-primary text-white px-8 py-4 rounded-full font-bold hover:bg-red-700 transition-all shadow-md">
            {{ t('alliance.explore_solutions', 'Explore :title Solutions', ['title' => $entry->title]) }}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
          <a href="#explore" class="inline-flex items-center gap-2 bg-white border border-zinc-200 text-zinc-800 px-8 py-4 rounded-full font-bold hover:bg-zinc-50 transition-all">
            {{ t('alliance.talk_to_experts', 'Talk to our Experts') }}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
        </div>
      </div>

      <div class="lg:col-span-4 flex flex-col gap-8 lg:sticky lg:top-24 self-start">
        @if($entry->featured_image)
        <div data-gsap="fade-up" class="hidden lg:flex bg-zinc-50/50 border border-zinc-200/80 rounded-3xl p-8 flex-col items-center shadow-sm">
          <img src="{{ asset('storage/' . $entry->featured_image) }}" alt="{{ $entry->title }}" class="max-w-[200px] h-auto object-contain" />
          <div class="mt-6 text-[10px] font-bold tracking-widest text-zinc-400 uppercase">{{ t('alliance.official_partner', 'Official Technology Partner') }}</div>
        </div>
        @endif

        @if($articles)
        <div data-gsap="fade-up" data-gsap-delay="0.2" class="bg-white border border-zinc-200/80 rounded-3xl p-8 shadow-sm">
          <div class="flex items-center gap-2.5 mb-6">
            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
            <span class="text-zinc-800 font-extrabold text-xs tracking-wider uppercase">{{ t('alliance.related_insights', 'Related Insights') }}</span>
          </div>
          <ul class="space-y-6">
            @foreach($articles as $article)
            <li>
              <a href="{{ $article['link'] }}" class="group block">
                <span class="text-[10px] font-bold tracking-wider uppercase text-primary">{{ $article['category'] }}</span>
                <p class="text-sm font-bold text-zinc-800 group-hover:text-primary transition-colors mt-1">{{ $article['title'] }}</p>
              </a>
            </li>
            @if(!$loop->last)<div class="w-full h-px bg-zinc-100 my-4"></div>@endif
            @endforeach
          </ul>
        </div>
        @endif
      </div>
    </div>
  </div>
</section>

<!-- Solutions -->
@if($solutionsFeatured || $solutionsOther)
<section id="solutions" class="py-16 md:py-32 bg-zinc-50 relative">
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row gap-16 lg:gap-24">
      <div class="lg:w-1/3">
        <div class="sticky top-32">
          <h2 data-gsap="fade-up" class="text-4xl font-light text-zinc-500 leading-tight">{{ $entry->title }}<br><span class="font-bold text-dark">{{ t('alliance.solutions_suffix', 'Solutions') }}</span></h2>
          <div class="h-1 bg-primary mt-4 w-16" data-gsap="line-grow"></div>
          <p data-gsap="fade-up" data-gsap-delay="0.1" class="text-lg text-zinc-600 leading-relaxed mt-8">{{ $solutionsDescription }}</p>
          <a href="#explore" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-full font-bold hover:bg-red-700 transition-colors shadow-sm mt-8">
            {{ t('alliance.consult_with_expert', 'Consult with Expert') }} <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
        </div>
      </div>
      <div class="lg:w-2/3 flex flex-col gap-12 pb-16">
        @php
          $relProducts = $entry->relatedEntries('product_id')->get();
        @endphp

        @if($relProducts->isNotEmpty())
        <div class="flex flex-col gap-6">
          <div class="text-xs font-bold text-primary uppercase tracking-wider">{{ t('alliance.featured_solutions', 'Featured Solutions') }}</div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($relProducts as $rel)
            <a data-gsap="fade-up" data-gsap-delay="{{ $loop->index * 0.1 }}" href="{{ $rel->getUrl() }}" class="group bg-white hover:bg-red-50/30 rounded-3xl p-8 border border-zinc-200/80 shadow-sm hover:shadow-xl hover:border-primary/50 transition-all flex flex-col justify-between">
              <div>
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform">
                  {!! render_icon($rel->getMeta('icon', 'shield-check'), 'w-7 h-7') !!}
                </div>
                <h3 class="text-xl font-bold text-zinc-900 mb-3 group-hover:text-primary transition-colors">{{ $rel->getTranslation('title') }}</h3>
                <p class="text-zinc-600 text-base leading-relaxed mb-6">{{ strip_tags($rel->content ?: ($rel->excerpt ?: '')) }}</p>
              </div>
              <span class="inline-flex items-center gap-1.5 text-xs font-bold text-primary uppercase tracking-wider">{{ t('common.explore_more', 'Explore More') }} <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
            </a>
            @endforeach
          </div>
        </div>
        @elseif($solutionsFeatured)
        <div class="flex flex-col gap-6">
          <div class="text-xs font-bold text-primary uppercase tracking-wider">{{ t('alliance.featured_solutions', 'Featured Solutions') }}</div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($solutionsFeatured as $f)
            <a data-gsap="fade-up" data-gsap-delay="{{ $loop->index * 0.1 }}" href="{{ resolve_solution_url($f['link'] ?? '#', $entry->slug) }}" class="group bg-white hover:bg-red-50/30 rounded-3xl p-8 border border-zinc-200/80 shadow-sm hover:shadow-xl hover:border-primary/50 transition-all flex flex-col justify-between">
              <div>
                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-6 group-hover:scale-110 transition-transform">
                  {!! render_icon($f['icon'] ?? 'shield', 'w-7 h-7') !!}
                </div>
                <h3 class="text-xl font-bold text-zinc-900 mb-3 group-hover:text-primary transition-colors">{{ $f['title'] }}</h3>
                <p class="text-zinc-600 text-base leading-relaxed mb-6">{{ $f['description'] ?? '' }}</p>
              </div>
              <span class="inline-flex items-center gap-1.5 text-xs font-bold text-primary uppercase tracking-wider">{{ t('common.explore_more', 'Explore More') }} <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg></span>
            </a>
            @endforeach
          </div>
        </div>
        @endif

        @if($solutionsOther)
        @if($solutionsFeatured)<div class="h-px bg-zinc-200/80 my-2"></div>@endif
        <div class="flex flex-col gap-6">
          <div class="text-xs font-bold text-zinc-400 uppercase tracking-wider">{{ t('alliance.other_solutions', 'Other Solutions') }}</div>
          @foreach($solutionsOther as $o)
          <div data-gsap="fade-up" data-gsap-delay="{{ $loop->index * 0.1 }}" class="group bg-white rounded-3xl p-8 lg:p-10 border border-zinc-200/80 shadow-sm hover:shadow-xl transition-all">
            <div class="flex flex-col md:flex-row gap-6 items-start">
              <div class="w-14 h-14 shrink-0 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                {!! render_icon($o['icon'] ?? 'shield', 'w-7 h-7') !!}
              </div>
              <div>
                <h3 class="text-xl font-bold text-zinc-900 mb-3">{{ $o['title'] }}</h3>
                <p class="text-zinc-600 text-base leading-relaxed">{{ $o['description'] ?? '' }}</p>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>
  </div>
</section>
@endif

<!-- Banner -->
@if($banner['headline'])
<section data-gsap="fade-up" class="py-10 md:py-14 bg-gradient-to-r from-red-800 via-primary to-red-700 relative overflow-hidden z-20 shadow-inner">
  <div class="absolute inset-0 pointer-events-none">
    <div class="absolute top-1/2 left-0 w-64 h-64 bg-white/10 rounded-full blur-[60px] -translate-y-1/2 -translate-x-1/2"></div>
    <div class="absolute top-1/2 right-0 w-96 h-96 bg-black/20 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/3"></div>
  </div>
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="flex flex-col md:flex-row items-center justify-between gap-8 md:gap-12">
      @php $bannerLogo = resolve_block_asset($meta['banner_logo'] ?? null); $fallbackLogo = $bannerLogo ?: ($entry->featured_image ? resolve_block_asset($entry->featured_image) : null); @endphp
      @if($fallbackLogo)
      <div class="flex-shrink-0">
        <div class="w-24 h-24 md:w-28 md:h-28 bg-white rounded-full flex items-center justify-center p-4 shadow-[0_10px_25px_rgba(0,0,0,0.3)] ring-4 ring-white/20 transform hover:scale-105 transition-transform duration-500">
          <img src="{{ $fallbackLogo }}" alt="{{ $entry->title }}" class="w-full h-auto object-contain drop-shadow-sm" />
        </div>
      </div>
      @endif
      <div class="flex-1 text-center md:text-left">
        @if($banner['badge'])
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-black/20 border border-white/10 text-[10px] font-bold uppercase tracking-widest text-white mb-3">
          <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> {{ $banner['badge'] }}
        </div>
        @endif
        <h2 class="text-3xl md:text-4xl font-extrabold text-white leading-tight">{!! $banner['headline'] !!}</h2>
        @if($banner['description'])
        <p class="text-white/80 text-base mt-2">{{ $banner['description'] }}</p>
        @endif
      </div>
      @if($banner['cta'])
      <a href="#explore" class="inline-flex items-center px-8 py-4 font-bold text-primary bg-white rounded-full hover:bg-zinc-100 shadow-lg transition-all">
        {{ $banner['cta'] }} <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
      @endif
    </div>
  </div>
</section>
@endif

<!-- Videos -->
@if($videos)
<section class="relative w-full py-24 md:py-32 bg-gradient-to-b from-white via-zinc-50/50 to-white flex flex-col items-center justify-center overflow-hidden border-t border-zinc-100">
  <div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-primary/20 rounded-full blur-[130px] opacity-80"></div>
    <div class="absolute -bottom-40 -right-40 w-[600px] h-[600px] bg-red-700/15 rounded-full blur-[130px] opacity-75"></div>
  </div>
  <div class="relative z-10 w-full max-w-[1200px] px-4 flex flex-col items-center">
    <div class="mb-12 flex flex-col items-center text-center w-full" data-gsap="fade-up">
      <h2 class="text-4xl font-light text-zinc-500 leading-tight">{{ t('alliance.insight_prefix', 'Insight') }} <span class="font-bold text-dark">{{ t('alliance.videos_suffix', 'Videos') }}</span></h2>
      <div class="h-1 bg-primary mt-4 w-16 mx-auto" data-gsap="line-grow"></div>
    </div>
    <div class="flex flex-wrap justify-center gap-8 w-full mb-12">
      @foreach($videos as $v)
      @php $vid = $v['video_id'] ?? $v['videoId'] ?? ''; @endphp
      @if($vid)
      <div data-gsap="fade-up" data-gsap-delay="{{ $loop->index * 0.1 }}" class="w-full md:w-[calc(33.333%-1.5rem)] max-w-[360px] min-w-[280px] bg-white rounded-3xl border border-zinc-200/80 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden flex flex-col justify-between">
        <button data-video-id="{{ $vid }}" class="video-trigger text-left group relative w-full aspect-video bg-zinc-900 overflow-hidden block" onclick="openVideo('{{ $vid }}')">
          <img src="https://img.youtube.com/vi/{{ $vid }}/hqdefault.jpg" alt="{{ $v['title'] }}" class="absolute inset-0 w-full h-full object-cover opacity-85 group-hover:opacity-100 transition-all duration-500" />
          <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-14 h-14 bg-primary text-white rounded-full flex items-center justify-center transform group-hover:scale-110 transition-transform shadow-lg shadow-primary/30">
              <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
          </div>
        </button>
        <div class="p-6">
          <span class="text-xs font-bold text-primary uppercase tracking-widest mb-2 block">{{ $v['category'] }}</span>
          <h3 class="text-lg font-bold text-zinc-900 leading-snug">{{ $v['title'] }}</h3>
        </div>
      </div>
      @endif
      @endforeach
    </div>
    <a href="{{ url('/videos') }}" class="inline-flex items-center gap-2 text-zinc-600 hover:text-primary font-bold uppercase tracking-wider text-sm transition-colors group/btn relative z-10" data-gsap="fade-up">
      {{ t('alliance.view_all_videos', 'View All Videos') }}
      <svg class="w-5 h-5 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
    </a>
  </div>
</section>
@endif

<!-- Explore & Form -->
<section id="explore" class="relative bg-zinc-50 py-20 md:py-28 overflow-hidden border-t border-zinc-100">
  <div class="absolute inset-0 pointer-events-none overflow-hidden">
    <div class="absolute top-1/2 left-0 w-[500px] h-[500px] bg-primary/3 rounded-full blur-[130px] opacity-40 -translate-y-1/2 -translate-x-1/2"></div>
    <div class="absolute top-1/2 right-0 w-[500px] h-[500px] bg-zinc-200/40 rounded-full blur-[130px] opacity-30 -translate-y-1/2 translate-x-1/2"></div>
  </div>
  <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">
      <div class="w-full lg:w-1/2 flex flex-col justify-center">
        <div class="mb-10">
          <h2 data-gsap="fade-up" class="text-4xl font-light text-zinc-500 leading-tight">{{ t('alliance.explore_prefix', 'Explore') }} {{ $entry->title }}<br><span class="font-bold text-zinc-900">{{ t('alliance.with_cdt', 'with CDT') }}</span></h2>
          <div class="h-1 bg-primary mt-4 w-16" data-gsap="line-grow"></div>
        </div>
        <div class="space-y-8 mt-12">
          <div class="flex items-start gap-5" data-gsap="fade-up" data-gsap-delay="0.1">
            <div class="w-14 h-14 bg-red-50 text-primary rounded-2xl flex items-center justify-center shrink-0">
              <x-icon name="lucide:book-open" class="w-7 h-7 text-primary" />
            </div>
            <div><h3 class="text-lg font-bold text-zinc-900">{{ t('alliance.advanced_action_title', 'Advanced Action and Review') }}</h3><p class="text-base text-zinc-500">{{ t('alliance.advanced_action_desc', 'PT Central Data Technology (CDT) is a subsidiary of the CTI Group that focuses on distributing IT infrastructure solutions to customers.') }}</p></div>
          </div>
          <div class="flex items-start gap-5" data-gsap="fade-up" data-gsap-delay="0.2">
            <div class="w-14 h-14 bg-red-50 text-primary rounded-2xl flex items-center justify-center shrink-0">
              <x-icon name="lucide:users" class="w-7 h-7 text-primary" />
            </div>
            <div><h3 class="text-lg font-bold text-zinc-900">{{ t('alliance.understand_it_expert_title', 'Understand IT Expert') }}</h3><p class="text-base text-zinc-500">{{ t('alliance.understand_it_expert_desc', 'By providing IT experts, we have secured CDT\'s presence in a variety of industries.') }}</p></div>
          </div>
          <div class="flex items-start gap-5" data-gsap="fade-up" data-gsap-delay="0.3">
            <div class="w-14 h-14 bg-red-50 text-primary rounded-2xl flex items-center justify-center shrink-0">
              <x-icon name="lucide:award" class="w-7 h-7 text-primary" />
            </div>
            <div><h3 class="text-lg font-bold text-zinc-900">{{ t('alliance.certified_specialist_title', 'Certified Specialist') }}</h3><p class="text-base text-zinc-500">{{ t('alliance.certified_specialist_desc', 'CDT IT specialists are certified to ensure solution quality follows with strict implementation standards.') }}</p></div>
          </div>
        </div>

      </div>
      <div class="w-full lg:w-1/2" data-gsap="fade-up" data-gsap-delay="0.2">
        <div class="bg-white rounded-3xl border border-zinc-200/60 p-8 md:p-12 shadow-sm">
          <div class="mb-8">
            <span class="text-xs font-bold text-primary uppercase tracking-widest block mb-2">{{ t('alliance.request_consultation', 'Request Consultation') }}</span>
            <h3 class="text-2xl font-bold text-gray-900">{{ t('alliance.manage_business_with_us', 'Manage Your Business With Us!') }}</h3>
            <p class="text-sm text-zinc-400 mt-1 font-light">{{ t('alliance.fill_out_fields_desc', 'Fill out the fields below, and our :title solutions team will connect with you.', ['title' => $entry->title]) }}</p>
          </div>
          @php
            $theme = active_theme();
            $assignments = setting("theme_{$theme->slug}_form_assignments", []);
            $formId = $assignments['alliance_form'] ?? $assignments['contact_form'] ?? null;
            $allianceForm = $formId ? \App\Models\Form::where('id', $formId)->where('is_active', true)->with('fields')->first() : null;
          @endphp

          @if($allianceForm)
            @include('cdt::partials.tailwind-form', ['form' => $allianceForm, 'entry' => $entry])
          @else
            <p class="text-sm text-zinc-500">Form is being configured. Please assign a form at <a href="{{ route('admin.forms.assignments') }}" class="text-primary hover:underline font-semibold">Form Assignments</a>.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Client Stories Carousel -->
@php
  $clientStories = \App\Models\CptEntry::where('post_type_id', \App\Models\CustomPostType::where('slug', 'client-says')->value('id'))
    ->where('status', 'published')
    ->limit(20)
    ->get();
@endphp
@if($clientStories->isNotEmpty())
<section class="py-24 relative overflow-hidden bg-white" id="client-story">
  <div class="absolute inset-0 bg-testimonial-image opacity-[0.5] bg-cover bg-center blur-sm"></div>

  <div class="relative z-10 mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 w-full">
    <div class="text-center mb-16 flex flex-col items-center w-full">
      <h2 class="text-4xl font-light text-zinc-500 leading-tight tracking-tight" data-gsap="fade-up">
        {{ t('home.testimonial_title_prefix', 'What Our') }} <span class="font-bold text-dark">{{ t('home.testimonial_title_main', 'Client Says') }}</span>
      </h2>
      <div class="h-1 bg-primary mt-4 w-16 mx-auto" data-gsap="line-grow"></div>
    </div>

    <div class="max-w-6xl mx-auto relative" data-gsap="fade-up" data-gsap-delay="0.2">
      <div class="flex justify-between items-end mb-6 px-2">
        <div class="product-testimonials-pagination text-zinc-400 font-medium tracking-widest uppercase text-xl [&_.swiper-pagination-current]:text-dark [&_.swiper-pagination-current]:font-bold [&_.swiper-pagination-current]:text-3xl"></div>
        <div class="flex gap-4">
          <button class="w-12 h-12 rounded-full border border-zinc-200 bg-white flex items-center justify-center text-dark hover:bg-primary hover:border-primary hover:text-white transition-colors shadow-sm swiper-button-prev-product">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
          </button>
          <button class="w-12 h-12 rounded-full border border-zinc-200 bg-white flex items-center justify-center text-dark hover:bg-primary hover:border-primary hover:text-white transition-colors shadow-sm swiper-button-next-product">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
          </button>
        </div>
      </div>

      <div class="swiper product-testimonials-swiper">
        <div class="swiper-wrapper">
          @foreach($clientStories as $story)
          @php
            $logoPath = $story->featured_image;
            $logoUrl = $logoPath
                ? (str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath))
                : null;
            $personName = $story->getMeta('person') ?? $story->title;
            $position = $story->getMeta('position') ?? '';
            $companyName = $story->getTranslation('title') ?? $story->title;
            $storyContent = $story->getTranslation('content') ?? $story->content ?? '';
          @endphp
          <div class="swiper-slide h-full">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col lg:flex-row border border-zinc-100 min-h-[400px]">
              <div class="lg:w-1/3 bg-zinc-50 p-10 md:p-12 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-zinc-100">
                @if($logoUrl)
                <div class="h-32 flex justify-start items-center mb-8">
                  <img src="{{ $logoUrl }}" alt="{{ $story->title }}" class="max-h-full w-auto max-w-[320px] object-contain object-left mix-blend-multiply" />
                </div>
                @else
                <div class="h-32 mb-8"></div>
                @endif
                <div>
                  <div class="flex text-primary mb-4">
                    @for($i=0; $i<5; $i++)
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    @endfor
                  </div>
                  @if($personName)<h3 class="font-bold text-lg text-zinc-900 uppercase">{{ $personName }}</h3>@endif
                  @if($position)<p class="text-sm text-zinc-500 uppercase tracking-wide mb-2">{{ $position }}</p>@endif
                  <p class="text-sm font-bold text-primary uppercase">{{ $companyName }}</p>
                </div>
              </div>
              <div class="lg:w-2/3 p-10 md:p-12 flex items-center relative">
                <svg class="absolute top-8 left-8 w-24 h-24 text-zinc-100 -z-10 transform -scale-x-100" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" /></svg>
                <div class="relative z-10">
                  <div class="text-base md:text-lg text-zinc-900 font-light leading-relaxed">{!! $storyContent !!}</div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
@endif

@if($videos)
<div id="videoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 opacity-0 pointer-events-none transition-opacity" style="transition: opacity 0.3s;">
  <div class="relative w-full max-w-5xl px-4">
    <button onclick="closeVideo()" class="absolute -top-12 right-4 text-white hover:text-primary"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
    <div class="aspect-video w-full bg-black rounded-lg overflow-hidden"><iframe id="youtubeIframe" class="w-full h-full" src="" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>
  </div>
</div>
<script>
function openVideo(id){document.getElementById('youtubeIframe').src='https://www.youtube-nocookie.com/embed/'+id+'?autoplay=1';document.getElementById('videoModal').classList.remove('opacity-0','pointer-events-none');}
function closeVideo(){document.getElementById('videoModal').classList.add('opacity-0','pointer-events-none');document.getElementById('youtubeIframe').src='';}
</script>
@endif
@endsection
