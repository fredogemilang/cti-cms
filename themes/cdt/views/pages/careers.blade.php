@extends('cdt::layouts.app')

@section('title', $page->getMetaTitle())

@section('content')
  <!-- Main Content Area -->
  <main class="min-h-screen bg-white">

    <!-- ═══════════════ SECTION 1: CAREER HERO ═══════════════ -->
    <section class="relative w-full pt-8 lg:pt-28 pb-20 flex items-center justify-center overflow-hidden bg-white">
      <!-- Clean light background with subtle gradient -->
      <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-zinc-50 to-white"></div>
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-primary/5 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-red-100/50 rounded-full blur-[100px]"></div>
      </div>

      <div class="relative z-10 mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 w-full">

        <!-- Breadcrumbs -->
        <nav class="flex items-center space-x-2 text-xs font-semibold tracking-wide text-zinc-400 mb-10" aria-label="Breadcrumb" data-gsap="fade-in">
          <a href="{{ localized_url('/') }}" class="hover:text-primary transition-colors" title="Home" aria-label="Home">Home</a>
          <svg class="w-3 h-3 text-zinc-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
          </svg>
          <span class="text-zinc-800 font-bold" aria-current="page">{{ $page->title }}</span>
        </nav>

        <div class="max-w-5xl mx-auto flex flex-col items-center text-center">

          <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-zinc-100 border border-zinc-200 text-sm font-bold uppercase tracking-widest text-primary mb-8" data-gsap="fade-up">
            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> {{ $page->getBlockValue('hero_badge', 'Join CDTroops') }}
          </span>

          <div class="overflow-hidden mb-6">
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 leading-[1.1] tracking-tight" data-gsap="fade-up" data-gsap-delay="0.1">
              {{ $page->getBlockValue('hero_title_prefix', 'Start Your Journey') }} <br />
              {{ t('careers.hero_with', 'With') }} <span class="text-primary">{{ $page->getBlockValue('hero_title_main', 'Central Data Technology') }}</span>
            </h1>
          </div>

          <div class="overflow-hidden mb-12 max-w-3xl">
            <p class="text-lg md:text-xl text-zinc-600 font-light leading-relaxed" data-gsap="fade-up" data-gsap-delay="0.2">
              {{ $page->getBlockValue('hero_subtitle', 'CDT’s business focus is to be a trusted and reliable digital partner that provides the best IT solutions for organizations and companies in transforming business. That is why we need you to make it happen.') }}
            </p>
          </div>

          <div data-gsap="fade-up" data-gsap-delay="0.3">
            <a href="#why-cdt" class="inline-flex items-center justify-center px-10 py-4 font-bold text-white uppercase tracking-wider transition-all duration-300 bg-primary rounded-full shadow-lg shadow-primary/30 hover:bg-red-700 hover:shadow-xl hover:-translate-y-1 gap-3 group">
              {{ t('careers.discover_more', 'Discover More Below') }}
              <svg class="w-5 h-5 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 13l5 5m0 0l5-5m-5 5V6"></path>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════ SECTION 2: WHY CDT ═══════════════ -->
    <section id="why-cdt" class="py-24 bg-zinc-50 border-t border-b border-zinc-100 relative overflow-hidden">
      <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-16">

          <!-- Left Column: Title -->
          <div class="lg:w-1/4 shrink-0">
            <div class="sticky top-28">
              <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight mb-6" data-gsap="fade-up">
                {{ $page->getBlockValue('why_cdt_title_prefix', 'Why') }} <br>
                <span class="font-bold text-dark italic">{{ $page->getBlockValue('why_cdt_title_main', 'CDT?') }}</span>
              </h2>
              <div class="h-1 bg-primary w-16 mb-8" data-gsap="line-grow"></div>
              <p class="text-zinc-600 font-light leading-relaxed hidden lg:block" data-gsap="fade-in" data-gsap-delay="0.1">
                {{ $page->getBlockValue('why_cdt_subtitle', 'We provide a workplace where your ideas matter, your growth is fast-tracked, and your contributions are recognized.') }}
              </p>
            </div>
          </div>

          <!-- Right Column: Values Cards Grid -->
          <div class="lg:w-3/4">
            @php
              $whyCards = $page->getBlockValue('why_cdt_cards', []);
              if (is_string($whyCards)) {
                  $whyCards = json_decode($whyCards, true) ?: [];
              }
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              @foreach($whyCards as $idx => $card)
                <div class="bg-white border border-zinc-100 rounded-3xl p-8 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col h-full group" data-gsap="fade-up" data-gsap-delay="{{ $idx * 0.1 }}">
                  <div class="flex items-center justify-between mb-8">
                    <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                      @if($idx === 0)
                        <!-- Grow Icon -->
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                      @elseif($idx === 1)
                        <!-- Enjoy Icon -->
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                      @else
                        <!-- Appreciate Icon -->
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                      @endif
                    </div>
                    <span class="text-xs font-bold text-zinc-300 group-hover:text-primary transition-colors uppercase tracking-widest">{{ $card['number'] ?? '0'.($idx+1) }}</span>
                  </div>

                  <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $card['title'] ?? '' }}</h3>
                  <p class="text-zinc-600 font-light leading-relaxed flex-grow">{{ $card['description'] ?? '' }}</p>
                </div>
              @endforeach
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- ═══════════════ SECTION 3: LIFE AT CDT ═══════════════ -->
    <section id="life-at-cdt" 
      x-data="{ previewImage: null, isPaused: false }" 
      @keydown.escape.window="previewImage = null; isPaused = false"
      class="py-24 bg-zinc-50 relative overflow-hidden">
      <!-- Custom CSS for marquee tracks -->
      <style>
        @keyframes marquee-left {
          0% { transform: translate3d(0, 0, 0); }
          100% { transform: translate3d(-50%, 0, 0); }
        }
        @keyframes marquee-right {
          0% { transform: translate3d(-50%, 0, 0); }
          100% { transform: translate3d(0, 0, 0); }
        }
        .animate-marquee-left {
          animation: marquee-left 40s linear infinite;
          will-change: transform;
          backface-visibility: hidden;
        }
        .animate-marquee-right {
          animation: marquee-right 40s linear infinite;
          will-change: transform;
          backface-visibility: hidden;
        }
        .marquee-row:hover .animate-marquee-left,
        .marquee-row:hover .animate-marquee-right,
        .marquee-paused .animate-marquee-left,
        .marquee-paused .animate-marquee-right {
          animation-play-state: paused !important;
        }
        /* Contain each row so paint stays isolated to its layer */
        .marquee-row {
          contain: layout style;
        }
      </style>

      <div class="w-full">
        <!-- Title -->
        <div class="text-center mb-16 px-4" data-gsap="fade-up">
          <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight mb-4">
            {{ $page->getBlockValue('life_cdt_title_prefix', 'Life at') }} <span class="font-extrabold text-dark">{{ $page->getBlockValue('life_cdt_title_main', 'CDT') }}</span>
          </h2>
          <div class="h-1 bg-primary w-24 mx-auto mb-6"></div>
          <p class="text-zinc-600 font-light max-w-2xl mx-auto leading-relaxed">
            {{ $page->getBlockValue('life_cdt_subtitle', 'See how CDTroops collaborate, recharge, and grow together in a modern, supportive, and balanced ecosystem.') }}
          </p>
        </div>

        @php
          $gallery = $page->getBlockValue('life_cdt_gallery', []);
          if (is_string($gallery)) {
              $gallery = json_decode($gallery, true) ?: [];
          }
          if (empty($gallery)) {
              $gallery = [
                'themes/cdt/assets/images/life-at/2.png',
                'themes/cdt/assets/images/life-at/1.png',
                'themes/cdt/assets/images/life-at/14.png',
                'themes/cdt/assets/images/life-at/3.png',
                'themes/cdt/assets/images/life-at/Fitness-First-Membership-scaled.jpg',
                'themes/cdt/assets/images/life-at/13.png',
              ];
          }
          $total = count($gallery);
          $chunkSize = max(1, (int) ceil($total / 3));
          $chunks = array_chunk($gallery, $chunkSize);

          $row1 = $chunks[0] ?? $gallery;
          $row2 = $chunks[1] ?? $row1;
          $row3 = $chunks[2] ?? $row1;

          if (empty($row2)) $row2 = $row1;
          if (empty($row3)) $row3 = $row1;
        @endphp

        <!-- Marquee Tracks (3 Rows) -->
        <div class="relative w-full overflow-hidden" :class="{ 'marquee-paused': isPaused || previewImage }">
          <div class="pointer-events-none absolute inset-y-0 left-0 w-24 sm:w-48 bg-gradient-to-r from-zinc-50 via-zinc-50/70 to-transparent z-10"></div>
          <div class="pointer-events-none absolute inset-y-0 right-0 w-24 sm:w-48 bg-gradient-to-l from-zinc-50 via-zinc-50/70 to-transparent z-10"></div>

          <!-- Row 1: Marquee Left -->
          <div class="marquee-row overflow-hidden w-full relative mb-6 py-2">
            <div class="flex gap-6 animate-marquee-left whitespace-nowrap w-max">
              <div class="flex gap-6 shrink-0">
                @foreach(array_merge($row1, $row1) as $img)
                  <div @click="previewImage = '{{ resolve_block_asset($img, 'xl') }}'; isPaused = true" 
                    class="life-card w-[320px] aspect-video rounded-3xl overflow-hidden border border-zinc-200/80 bg-white shadow-sm hover:shadow-xl hover:scale-[1.02] cursor-pointer transition-all duration-300 shrink-0 group relative">
                    <x-image :src="resolve_block_asset($img, 'sm')" alt="CDT Culture Life" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="eager" decoding="async" />
                    <div class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                      <span class="w-10 h-10 rounded-full bg-white/90 text-dark flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                      </span>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- Row 2: Marquee Right -->
          <div class="marquee-row overflow-hidden w-full relative mb-6 py-2">
            <div class="flex gap-6 animate-marquee-right whitespace-nowrap w-max">
              <div class="flex gap-6 shrink-0">
                @foreach(array_merge($row2, $row2) as $img)
                  <div @click="previewImage = '{{ resolve_block_asset($img, 'xl') }}'; isPaused = true" 
                    class="life-card w-[320px] aspect-video rounded-3xl overflow-hidden border border-zinc-200/80 bg-white shadow-sm hover:shadow-xl hover:scale-[1.02] cursor-pointer transition-all duration-300 shrink-0 group relative">
                    <x-image :src="resolve_block_asset($img, 'sm')" alt="CDT Culture Life" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="eager" decoding="async" />
                    <div class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                      <span class="w-10 h-10 rounded-full bg-white/90 text-dark flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                      </span>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- Row 3: Marquee Left -->
          <div class="marquee-row overflow-hidden w-full relative py-2">
            <div class="flex gap-6 animate-marquee-left whitespace-nowrap w-max">
              <div class="flex gap-6 shrink-0">
                @foreach(array_merge($row3, $row3) as $img)
                  <div @click="previewImage = '{{ resolve_block_asset($img, 'xl') }}'; isPaused = true" 
                    class="life-card w-[320px] aspect-video rounded-3xl overflow-hidden border border-zinc-200/80 bg-white shadow-sm hover:shadow-xl hover:scale-[1.02] cursor-pointer transition-all duration-300 shrink-0 group relative">
                    <x-image :src="resolve_block_asset($img, 'sm')" alt="CDT Culture Life" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="eager" decoding="async" />
                    <div class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                      <span class="w-10 h-10 rounded-full bg-white/90 text-dark flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                      </span>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

        </div>

        <!-- Image Preview Lightbox Modal -->
        <template x-teleport="body">
          <div 
            x-show="previewImage"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="previewImage = null; isPaused = false"
            class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4 sm:p-8"
            style="display: none;">
            
            <div 
              @click.stop 
              class="relative max-w-5xl max-h-[85vh] rounded-3xl overflow-hidden shadow-2xl bg-zinc-900 border border-white/10 flex flex-col items-center">
              
              <!-- Close Button -->
              <button 
                type="button" 
                @click="previewImage = null; isPaused = false"
                class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-black/60 hover:bg-black/90 text-white flex items-center justify-center transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>

              <img 
                :src="previewImage" 
                alt="Life at CDT Preview" 
                class="max-w-full max-h-[80vh] object-contain rounded-2xl">
            </div>
          </div>
        </template>
      </div>
    </section>

    <!-- ═══════════════ SECTION 4: JOB VACANCY & APPLICATION MODAL ═══════════════ -->
    @php
      $cpt = \App\Models\CustomPostType::where('slug', 'jobs')->first();
      $jobEntries = [];
      if ($cpt) {
          $jobEntries = \App\Models\CptEntry::published()
              ->where('post_type_id', $cpt->id)
              ->orderBy('published_at', 'desc')
              ->get();
      }

      $terms = \App\Models\TaxonomyTerm::whereHas('taxonomy', fn($q) => $q->where('slug', 'job_category'))->get();

      $formattedJobs = $jobEntries->map(function($j) {
          $t = $j->terms->first();
          $catSlug = $t ? $t->slug : 'general';
          $catLabel = $t ? $t->name : 'General';

          $rawResp = $j->getMeta('responsibilities', []);
          $responsibilities = is_array($rawResp)
              ? array_values(array_filter($rawResp))
              : array_values(array_filter(explode("\n", strip_tags(str_replace(['<li>', '</li>', '<br>', '<br/>'], ["\n", '', "\n", "\n"], (string)$rawResp)))));

          $rawReq = $j->getMeta('requirements', []);
          $requirements = is_array($rawReq)
              ? array_values(array_filter($rawReq))
              : array_values(array_filter(explode("\n", strip_tags(str_replace(['<li>', '</li>', '<br>', '<br/>'], ["\n", '', "\n", "\n"], (string)$rawReq)))));

          return [
              'id' => $j->id,
              'title' => $j->title,
              'category' => $catSlug,
              'categoryLabel' => $catLabel,
              'location' => $j->getMeta('location', 'DKI Jakarta'),
              'type' => $j->getMeta('employment_type', 'Full-time'),
              'shortDesc' => $j->getMeta('short_description', $j->excerpt ?? ''),
              'responsibilities' => $responsibilities,
              'requirements' => $requirements,
          ];
      })->toArray();

      if (empty($formattedJobs)) {
          $formattedJobs = [
              [
                  'id' => 1,
                  'title' => 'Account Executive',
                  'category' => 'sales-bd',
                  'categoryLabel' => 'Sales & BD',
                  'location' => 'DKI Jakarta',
                  'type' => 'Full-time',
                  'shortDesc' => 'Achieve and exceed sales target, build relationships, and expand customer segments.',
                  'responsibilities' => [
                      'Achieve and exceed sales target and deliver service excellence',
                      'Build and develop strong relationship with existing and potential end-users',
                      'Identify and capture new customer segments to expand market coverage'
                  ],
                  'requirements' => [
                      'Bachelor degree in Business, IT, or related fields',
                      'Minimum 2-3 years of experience in B2B corporate sales, preferably in IT solutions',
                      'Strong communication, negotiation, and presentation skills'
                  ]
              ],
              [
                  'id' => 2,
                  'title' => 'Solution Architect – Enterprise',
                  'category' => 'technical-engineering',
                  'categoryLabel' => 'Technical & Engineering',
                  'location' => 'DKI Jakarta',
                  'type' => 'Full-time',
                  'shortDesc' => 'Serve as a trusted advisor, build stakeholder relationships, and guide client architecture adoption.',
                  'responsibilities' => [
                      'Serve as the primary technical contact and trusted advisor for assigned clients',
                      'Build and maintain strong relationships with technical and business stakeholders',
                      'Understand each customer’s architecture, use case, and success metrics to guide adoption'
                  ],
                  'requirements' => [
                      'Bachelor degree in Computer Science, Information Technology, or equivalent',
                      'Proven experience as a Solution Architect, Systems Engineer, or Technical Consultant',
                      'Deep knowledge of cloud infrastructure (AWS, Azure, or GCP)'
                  ]
              ]
          ];
      }
    @endphp

    <section id="job-vacancy" class="py-24 bg-zinc-50 border-t border-zinc-100"
      x-data="{
        selectedCategory: 'all',
        expandedJobId: null,
        showApplyModal: false,
        selectedJob: null,
        formName: '',
        formPosition: '',
        formPhone: '',
        formEmail: '',
        formLinkedin: '',
        formConsent: false,
        formSuccess: false,
        jobs: {{ json_encode($formattedJobs) }},
        toggleExpand(id) {
          const cards = this.jobs.map(j => document.getElementById('job-card-' + j.id)).filter(Boolean);
          const firstRects = cards.map(c => ({ el: c, rect: c.getBoundingClientRect() }));

          if (this.expandedJobId === id) {
            this.expandedJobId = null;
          } else {
            this.expandedJobId = id;
          }

          this.$nextTick(() => {
            firstRects.forEach(({ el, rect }) => {
              const lastRect = el.getBoundingClientRect();
              const dx = rect.left - lastRect.left;
              const dy = rect.top - lastRect.top;
              const dw = rect.width / lastRect.width;
              const dh = rect.height / lastRect.height;

              el.style.transformOrigin = 'top left';
              el.style.transform = `translate(${dx}px, ${dy}px) scale(${dw}, ${dh})`;
              el.style.transition = 'none';

              el.offsetHeight;

              el.style.transition = 'transform 600ms cubic-bezier(0.25, 1, 0.5, 1), background-color 600ms, border-color 600ms, box-shadow 600ms';
              el.style.transform = 'none';
            });

            setTimeout(() => {
              firstRects.forEach(({ el }) => {
                el.style.transition = '';
                el.style.transform = '';
                el.style.transformOrigin = '';
              });

              const activeCard = document.getElementById('job-card-' + id);
              if (activeCard) {
                activeCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
              }
            }, 600);
          });
        },
        openApply(job) {
          this.selectedJob = job;
          this.formPosition = job ? job.title : '';
          this.showApplyModal = true;
          this.formSuccess = false;
          document.body.style.overflow = 'hidden';
        },
        closeModals() {
          this.showApplyModal = false;
          this.selectedJob = null;
          document.body.style.overflow = '';
        },
        submitApplication() {
          this.formSuccess = true;
          setTimeout(() => {
            this.closeModals();
            this.formName = '';
            this.formPosition = '';
            this.formPhone = '';
            this.formEmail = '';
            this.formLinkedin = '';
            this.formConsent = false;
          }, 2500);
        }
      }">

      <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
        <!-- Heading -->
        <div class="text-center mb-16" data-gsap="fade-up">
          <h2 class="text-4xl md:text-5xl font-light text-zinc-500 leading-tight mb-4">
            {{ $page->getBlockValue('jobs_title_prefix', 'Job') }} <span class="font-bold text-dark">{{ $page->getBlockValue('jobs_title_main', 'Vacancy') }}</span>
          </h2>
          <div class="h-1 bg-primary w-24 mx-auto mb-6" data-gsap="line-grow"></div>
          <p class="text-zinc-600 font-light max-w-xl mx-auto leading-relaxed">
            {{ $page->getBlockValue('jobs_subtitle', 'Start finding your purpose with CDT. See our latest vacancies below.') }}
          </p>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center justify-center gap-3 mb-12" data-gsap="fade-up">
          <button
            @click="selectedCategory = 'all'; expandedJobId = null;"
            :class="selectedCategory === 'all' ? 'bg-primary text-white border-primary shadow-md' : 'bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50'"
            class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider border transition-all duration-300 flex items-center gap-2 cursor-pointer">
            All Openings
            <span :class="selectedCategory === 'all' ? 'bg-white/20 text-white' : 'bg-zinc-100 text-zinc-500'" class="px-2 py-0.5 rounded-md text-[10px]" x-text="jobs.length"></span>
          </button>

          @foreach($terms as $term)
            <button
              @click="selectedCategory = '{{ $term->slug }}'; expandedJobId = null;"
              :class="selectedCategory === '{{ $term->slug }}' ? 'bg-primary text-white border-primary shadow-md' : 'bg-white text-zinc-600 border-zinc-200 hover:bg-zinc-50'"
              class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider border transition-all duration-300 flex items-center gap-2 cursor-pointer">
              {{ $term->name }}
              <span :class="selectedCategory === '{{ $term->slug }}' ? 'bg-white/20 text-white' : 'bg-zinc-100 text-zinc-500'" class="px-2 py-0.5 rounded-md text-[10px]" x-text="jobs.filter(j => j.category === '{{ $term->slug }}').length"></span>
            </button>
          @endforeach
        </div>

        <!-- Job Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-all duration-500">
          <template x-for="job in jobs" :key="job.id">
            <div
              x-show="selectedCategory === 'all' || job.category === selectedCategory"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 translate-y-4"
              x-transition:enter-end="opacity-100 translate-y-0"
              :id="'job-card-' + job.id"
              :class="expandedJobId === job.id ? 'lg:col-span-3 md:col-span-2 bg-zinc-50/80 border-primary shadow-lg' : 'col-span-1 bg-white hover:shadow-xl hover:-translate-y-1 border-zinc-200/60'"
              class="border rounded-3xl p-8 flex flex-col justify-between transition-all duration-500 ease-in-out group relative overflow-hidden">

              <div x-show="expandedJobId === job.id" class="absolute top-0 left-0 w-full h-1.5 bg-primary"></div>

              <!-- LAYOUT A: Collapsed Card Content -->
              <div
                x-show="expandedJobId !== job.id"
                x-transition:enter="transition ease-out duration-300 delay-100"
                x-transition:enter-start="opacity-0 scale-98"
                x-transition:enter-end="opacity-100 scale-100"
                class="flex flex-col justify-between h-full">
                <div>
                  <div class="flex items-center gap-2.5 mb-6">
                    <span x-text="job.categoryLabel" class="px-3 py-1 bg-red-50 text-primary text-[10px] font-bold uppercase tracking-wider rounded-lg"></span>
                    <span x-text="job.type" class="px-3 py-1 bg-zinc-100 text-zinc-500 text-[10px] font-bold uppercase tracking-wider rounded-lg"></span>
                  </div>

                  <h3 x-text="job.title" class="text-xl md:text-2xl font-bold text-gray-900 mb-3 group-hover:text-primary transition-colors duration-300"></h3>

                  <div class="flex items-center gap-2 text-zinc-400 mb-6 text-sm">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span x-text="job.location" class="font-medium text-zinc-500"></span>
                  </div>

                  <p x-text="job.shortDesc" class="text-zinc-500 font-light leading-relaxed mb-8 text-sm"></p>
                </div>

                <div class="flex items-center gap-4 pt-6 border-t border-zinc-100">
                  <button
                    @click="toggleExpand(job.id)"
                    class="flex-1 px-4 py-3 border border-zinc-200 text-zinc-700 hover:border-primary hover:text-primary transition-all duration-300 rounded-xl text-xs font-bold uppercase tracking-wider text-center cursor-pointer">
                    See Details
                  </button>
                  <button
                    @click="openApply(job)"
                    class="flex-1 px-4 py-3 bg-primary text-white hover:bg-red-700 shadow-sm hover:shadow-md transition-all duration-300 rounded-xl text-xs font-bold uppercase tracking-wider cursor-pointer">
                    Apply Now
                  </button>
                </div>
              </div>

              <!-- LAYOUT B: Expanded Card Content -->
              <div
                x-show="expandedJobId === job.id"
                x-transition:enter="transition ease-out duration-400 delay-200"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start w-full">

                <div class="lg:col-span-5 flex flex-col justify-between h-full">
                  <div>
                    <div class="flex items-center gap-2.5 mb-6">
                      <span x-text="job.categoryLabel" class="px-3 py-1 bg-red-100 text-primary text-[10px] font-bold uppercase tracking-wider rounded-lg"></span>
                      <span x-text="job.type" class="px-3 py-1 bg-zinc-200 text-zinc-700 text-[10px] font-bold uppercase tracking-wider rounded-lg"></span>
                    </div>

                    <h3 x-text="job.title" class="text-2xl md:text-3xl font-bold text-gray-900 mb-4"></h3>

                    <div class="flex items-center gap-2 text-zinc-500 mb-6 text-sm">
                      <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      <span x-text="job.location" class="font-medium"></span>
                    </div>

                    <p x-text="job.shortDesc" class="text-zinc-600 font-light leading-relaxed mb-8"></p>
                  </div>

                  <div class="flex flex-wrap items-center gap-4 pt-6 border-t border-zinc-200/80">
                    <button
                      @click="openApply(job)"
                      class="px-8 py-3.5 bg-primary text-white hover:bg-red-700 shadow-md hover:shadow-lg transition-all duration-300 rounded-xl text-xs font-bold uppercase tracking-wider cursor-pointer">
                      Apply for this position
                    </button>
                    <button
                      @click="toggleExpand(job.id)"
                      class="px-6 py-3.5 border border-zinc-300 text-zinc-600 hover:bg-zinc-100 hover:text-zinc-800 transition-all duration-300 rounded-xl text-xs font-bold uppercase tracking-wider cursor-pointer flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
                      </svg>
                      Collapse
                    </button>
                  </div>
                </div>

                <div class="lg:col-span-7 space-y-6 lg:border-l lg:border-zinc-200 lg:pl-8 pt-6 lg:pt-0">
                  <div>
                    <h3 class="text-xs font-bold uppercase tracking-widest text-primary mb-3">Key Responsibilities</h3>
                    <ul class="space-y-2.5">
                      <template x-for="(resp, idx) in job.responsibilities" :key="idx">
                        <li class="flex items-start gap-3 text-sm text-zinc-600 leading-relaxed font-light">
                          <span class="w-1.5 h-1.5 rounded-full bg-primary mt-2 flex-shrink-0"></span>
                          <span x-text="resp"></span>
                        </li>
                      </template>
                    </ul>
                  </div>

                  <div class="pt-4 border-t border-zinc-200/60">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-primary mb-3">Requirements & Qualifications</h3>
                    <ul class="space-y-2.5">
                      <template x-for="(req, idx) in job.requirements" :key="idx">
                        <li class="flex items-start gap-3 text-sm text-zinc-600 leading-relaxed font-light">
                          <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 mt-2 flex-shrink-0"></span>
                          <span x-text="req"></span>
                        </li>
                      </template>
                    </ul>
                  </div>
                </div>

              </div>

            </div>
          </template>
        </div>

      </div>

      <!-- Candidate Application Modal (job_application_form) -->
      <div
        x-show="showApplyModal"
        class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
        @click.self="closeModals()">

        <div
          x-show="showApplyModal"
          x-transition:enter="transition ease-out duration-300 transform"
          x-transition:enter-start="opacity-0 scale-95 translate-y-4"
          x-transition:enter-end="opacity-100 scale-100 translate-y-0"
          x-transition:leave="transition ease-in duration-200 transform"
          x-transition:leave-start="opacity-100 scale-100 translate-y-0"
          x-transition:leave-end="opacity-0 scale-95 translate-y-4"
          class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden relative max-h-[90vh] flex flex-col"
          @keydown.escape.window="closeModals()">

          <button @click="closeModals()" class="absolute top-6 right-6 p-2 text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 rounded-full transition-colors z-20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>

          <div class="p-8 overflow-y-auto flex-1">
            <div class="mb-8">
              <span class="text-xs font-bold text-primary uppercase tracking-wider block mb-1">Applying For</span>
              <h3 x-text="selectedJob ? selectedJob.title : ''" class="text-2xl font-bold text-gray-900"></h3>
              <p class="text-xs text-zinc-400 mt-1 uppercase font-semibold tracking-wider">PT Central Data Technology</p>
            </div>

            <div x-show="formSuccess" x-transition class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center mb-4">
              <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
              <div class="text-lg font-bold text-emerald-900 mb-2">Application Submitted!</div>
              <p class="text-emerald-700 text-sm font-light">
                Thank you for applying. Our HR team will review your details and contact you shortly.
              </p>
            </div>

            <form x-show="!formSuccess" @submit.prevent="submitApplication()" class="space-y-5">
              <div>
                <label class="block text-xs font-bold text-zinc-600 uppercase tracking-wider mb-2">Full Name *</label>
                <input type="text" required x-model="formName" placeholder="e.g. John Doe" class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm focus:outline-none focus:border-primary transition-all">
              </div>

              <div>
                <label class="block text-xs font-bold text-zinc-600 uppercase tracking-wider mb-2">Preferred Job Position *</label>
                <input type="text" required x-model="formPosition" placeholder="e.g. Solution Architect" class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm focus:outline-none focus:border-primary transition-all">
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-bold text-zinc-600 uppercase tracking-wider mb-2">Phone Number *</label>
                  <input type="tel" required x-model="formPhone" placeholder="+62 812-3456-7890" class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm focus:outline-none focus:border-primary transition-all">
                </div>
                <div>
                  <label class="block text-xs font-bold text-zinc-600 uppercase tracking-wider mb-2">Email Address *</label>
                  <input type="email" required x-model="formEmail" placeholder="johndoe@email.com" class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm focus:outline-none focus:border-primary transition-all">
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold text-zinc-600 uppercase tracking-wider mb-2">LinkedIn Profile URL <span class="text-primary font-normal">(Optional - Will Be Prioritized)</span></label>
                <input type="url" x-model="formLinkedin" placeholder="https://linkedin.com/in/username" class="w-full px-4 py-3 border border-zinc-200 rounded-xl text-sm focus:outline-none focus:border-primary transition-all">
              </div>

              <div class="space-y-4 pt-2">
                <div class="flex items-start gap-3">
                  <input type="checkbox" required id="privacy-consent-jobs-modal" x-model="formConsent" class="mt-1 h-4 w-4 rounded border-zinc-300 text-primary focus:ring-primary cursor-pointer">
                  <label for="privacy-consent-jobs-modal" class="text-sm font-semibold text-red-600 cursor-pointer select-none leading-relaxed">
                    By ticking this box, I agree that my personal information will be given to Central Data Technology (CDT)
                  </label>
                </div>
              </div>

              <div class="flex items-center justify-end gap-4 pt-4">
                <button type="button" @click="closeModals()" class="px-5 py-3 border border-zinc-200 text-zinc-600 hover:bg-zinc-100 transition-colors rounded-xl text-xs font-bold uppercase tracking-wider">Cancel</button>
                <button type="submit" class="px-8 py-3 bg-primary text-white hover:bg-red-700 shadow-md hover:shadow-lg transition-all rounded-xl text-xs font-bold uppercase tracking-wider">Submit Application</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form Section -->
    @include('cdt::partials.contact-section')

  </main>
@endsection
