@extends('ofis::layouts.app')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#10334d] text-white py-16 lg:py-20 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Mandatory SEO Breadcrumbs Component -->
        <x-seo-breadcrumbs :entity="$entry" class="text-white/70 mb-8" />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                @if(!empty($entry->meta['subtitle']))
                    <span class="inline-block px-4 py-1.5 bg-[#fab54f]/20 text-[#fab54f] font-semibold text-sm rounded-full mb-4">
                        {{ $entry->meta['subtitle'] }}
                    </span>
                @endif

                <!-- Mandatory Single H1 -->
                <h1 class="text-4xl sm:text-5xl font-bold leading-tight mb-6">
                    {{ $entry->title }}
                </h1>

                <div class="text-gray-300 text-lg leading-relaxed mb-8">
                    {!! nl2br(e($entry->content)) !!}
                </div>

                <a href="#contact" class="inline-block py-3.5 px-8 bg-[#fab54f] hover:bg-[#fab54f]/90 text-ofis-ink font-bold rounded-full shadow-lg transition">
                    {{ t('common.request_demo', 'Request Demo & Quote') }}
                </a>
            </div>

            @if(!empty($entry->meta['hero_image']))
                <div class="flex justify-center">
                    <x-image :src="$entry->meta['hero_image']" :alt="$entry->title" class="w-full max-w-md h-auto rounded-3xl shadow-2xl border border-white/10" />
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Features / Sub-products Grid -->
@if(!empty($entry->meta['features']) && is_array($entry->meta['features']))
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    {{ t('package.features_title', 'Features & Capabilities Included') }}
                </h2>
                <p class="text-gray-600">
                    Discover how {{ $entry->title }} optimizes operations and security across your organization.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($entry->meta['features'] as $feat)
                    <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-[#81b4c6]/15 rounded-xl flex items-center justify-center mb-6 text-ofis-teal">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            {{ $feat['title'] ?? '' }}
                        </h3>
                        <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">
                            {{ $feat['description'] ?? '' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

<!-- Contact / CTA Section -->
<section id="contact" class="py-20 bg-white border-t border-slate-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Interested in {{ $entry->title }}?
        </h2>
        <p class="text-gray-600 mb-8 max-w-xl mx-auto">
            Contact our engineering specialists to schedule a live demo or request customized pricing.
        </p>

        <a href="https://wa.me/628111925345?text=I%20am%20interested%20in%20{{ urlencode($entry->title) }}"
           target="_blank" rel="noopener"
           class="inline-flex items-center gap-3 py-4 px-8 bg-[#25D366] hover:bg-[#25D366]/90 text-white font-bold rounded-full shadow-lg transition text-lg">
            <img src="{{ theme_asset('whatsapp-icon-1-150x150.png-DcSjyvTS.webp') }}" alt="" class="w-6 h-6" />
            <span>Chat via WhatsApp</span>
        </a>
    </div>
</section>
@endsection
