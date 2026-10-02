{{-- Deferred testimonials section — loaded via AJAX after initial page paint --}}
<!-- Testimonials Section -->
<section class="py-24 relative overflow-hidden bg-zinc-50/50" id="testimonials">
    <!-- Subtle Background Pattern -->
    <div class="absolute inset-0 bg-testimonial-image opacity-[0.5] bg-cover bg-center blur-sm"></div>
  
    <div class="relative z-10 mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
      @php
        $testimonialTitle = $page?->titleBlock('testimonial_title', ['prefix' => 'What Our', 'main' => 'Client Says']);
      @endphp
      <div class="text-center mb-16">
        <h2 class="text-4xl font-light text-zinc-500 leading-tight" data-gsap="fade-up">
          @if(!empty($testimonialTitle['prefix']))
            {!! $testimonialTitle['prefix'] !!}
          @endif
          <span class="font-bold text-dark">{!! $testimonialTitle['main'] !!}</span>
        </h2>
        <div class="h-1 bg-primary mt-4 mx-auto" style="width: 50px;" data-gsap="line-grow"></div>
      </div>
  
      <div class="max-w-6xl mx-auto relative" data-gsap="fade-up" data-gsap-delay="0.2">
        <!-- Top Control Bar -->
        <div class="flex justify-between items-end mb-6 px-2">
          <div
            class="swiper-pagination-custom text-zinc-400 font-medium tracking-widest uppercase text-xl [&_.swiper-pagination-current]:text-dark [&_.swiper-pagination-current]:font-bold [&_.swiper-pagination-current]:text-3xl">
          </div>
          <div class="flex gap-4">
            <button aria-label="{{ t('a11y.prev_testimonial', 'Previous testimonial') }}" title="{{ t('a11y.prev_testimonial', 'Previous testimonial') }}"
              class="swiper-button-prev-custom w-12 h-12 rounded-full border border-zinc-200 bg-white flex items-center justify-center text-dark hover:bg-primary hover:border-primary hover:text-white transition-colors shadow-sm">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button aria-label="{{ t('a11y.next_testimonial', 'Next testimonial') }}" title="{{ t('a11y.next_testimonial', 'Next testimonial') }}"
              class="swiper-button-next-custom w-12 h-12 rounded-full border border-zinc-200 bg-white flex items-center justify-center text-dark hover:bg-primary hover:border-primary hover:text-white transition-colors shadow-sm">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
        </div>
          <div class="swiper testimonials-swiper">
          <div class="swiper-wrapper">
  
            @foreach($testimonials as $testimonial)
              @php
                $logoPath = $testimonial->featured_image;
                $logoUrl = $logoPath
                    ? (str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath))
                    : null;
                $personName = $testimonial->getMeta('person') ?? $testimonial->title;
                $position = $testimonial->getMeta('position') ?? '';
                $companyName = $testimonial->getTranslation('title') ?? $testimonial->title;
                $testimonialContent = $testimonial->getTranslation('content') ?? $testimonial->content;
              @endphp
              <div class="swiper-slide h-full">
                <div
                  class="bg-white rounded-2xl shadow-xl overflow-hidden h-full flex flex-col lg:flex-row border border-zinc-100 min-h-[400px]">
                  <div class="lg:w-1/3 bg-zinc-50 p-12 flex flex-col justify-between border-r border-zinc-100">
                    @if($logoUrl)
                      <div class="h-32 flex justify-start items-center mb-8">
                        <x-image :src="$logoUrl" alt="{{ $testimonial->title }}" title="{{ $testimonial->title }}" class="max-h-full w-auto max-w-[320px] object-contain object-left" />
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
                      <h3 class="font-bold text-xl text-dark mb-1">{{ $personName }}</h3>
                      <p class="text-sm text-zinc-500 uppercase tracking-wider mb-2">{{ $position }}</p>
                      <p class="text-sm font-semibold text-primary">{{ $companyName }}</p>
                    </div>
                  </div>
                  <div class="lg:w-2/3 p-12 flex items-center relative">
                    <svg class="absolute top-8 left-8 w-24 h-24 text-zinc-100 -z-10 transform -scale-x-100"
                      fill="currentColor" viewBox="0 0 24 24">
                      <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                    </svg>
                    <div class="relative z-10 w-full">
                      <div class="text-base md:text-lg text-dark font-light leading-relaxed mb-6 space-y-4 [&_p]:mb-4 last:[&_p]:mb-0">{!! $testimonialContent !!}</div>
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
