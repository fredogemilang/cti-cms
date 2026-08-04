@extends('ofis::layouts.app')

@section('content')
<!-- Promo Banner -->
<div id="promo-sentinel" aria-hidden="true"></div>
<section id="promo-banner" class="promo-banner sticky top-0 z-40 w-full bg-cover bg-center bg-no-repeat transition-all duration-300 ease-out overflow-hidden" style="background-image:url('{{ theme_asset('bannerpromo-scaled.jpg-CC1eRu0q.webp') }}');">
    <div class="promo-inner relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex flex-row items-center justify-between gap-6 transition-all duration-300 ease-out py-5 md:py-7">
        <div class="relative flex flex-col justify-center z-10">
            <h2 class="promo-title font-bold text-white leading-tight mb-2 transition-all duration-300 ease-out text-3xl md:text-5xl">
                {{ t('home.promo_title', 'Revolutionize Your Workplace Today!') }}
            </h2>
            <h3 class="promo-sub font-medium text-white leading-tight transition-all duration-300 ease-out text-lg md:text-2xl">
                {{ t('home.promo_subtitle', "Start From IDR 1 Million, Let's") }} <span class="promo-try-on">{{ t('home.try_on', 'Try On') }}</span>
            </h3>
        </div>
    </div>
    <img src="{{ theme_asset('arrowpromo.png-C9UzomPj.webp') }}" alt="" class="promo-arrow pointer-events-none absolute top-0 h-full w-auto object-contain object-right" style="right:-24px;" loading="lazy"/>
</section>

<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-slate-50 to-white py-16 lg:py-24 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block px-4 py-1.5 bg-[#fab54f]/20 text-ofis-ink font-semibold text-sm rounded-full mb-6">
                    OFIS Smart Office Solutions
                </span>
                
                <!-- Mandatory H1 -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                    {{ $page->blocks['hero_title'] ?? t('home.hero_h1', 'One Future of Interconnected workspace for Smart workforce') }}
                </h1>
                
                <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                    {{ $page->blocks['hero_subtitle'] ?? t('home.hero_desc', 'Transform your office into an intelligent, highly efficient, and secure workspace. Empower your team with interconnected technology designed for modern enterprises.') }}
                </p>

                <div class="flex flex-wrap items-center gap-4">
                    <a href="#packages" class="py-3.5 px-8 bg-[#fab54f] hover:bg-[#fab54f]/90 text-ofis-ink font-bold rounded-full shadow-md transition transform hover:-translate-y-0.5">
                        {{ t('home.explore_packages', 'Explore Packages') }}
                    </a>
                    <a href="#contact" class="py-3.5 px-8 bg-[#81b4c6] hover:bg-[#6e9fb1] text-white font-bold rounded-full shadow-md transition transform hover:-translate-y-0.5">
                        {{ t('common.talk_to_experts', 'Talk to Experts') }}
                    </a>
                </div>
            </div>

            <div class="relative flex justify-center">
                <img src="{{ theme_asset('home-imgnomasking-png-Bs7Xi48Z.webp') }}" alt="OFIS Smart Office Interconnected Workspace" class="w-full max-w-lg h-auto rounded-3xl shadow-2xl" />
            </div>
        </div>
    </div>
</section>

<!-- OFIS Solution Packages Grid -->
<section id="packages" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                {{ t('home.packages_title', 'Explore OFIS Smart Office Packages') }}
            </h2>
            <p class="text-gray-600 text-lg">
                {{ t('home.packages_subtitle', 'Comprehensive packages tailored to elevate efficiency, security, and facility automation across your organization.') }}
            </p>
        </div>

        @php
            $cptPackage = \App\Models\CustomPostType::where('slug', 'package')->first();
            $packages = $cptPackage ? \App\Models\CptEntry::where('post_type_id', $cptPackage->id)->where('status', 'published')->get() : collect();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($packages as $pkg)
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-8 hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 bg-[#81b4c6]/20 rounded-xl flex items-center justify-center mb-6 text-[#81b4c6] group-hover:bg-[#fab54f] group-hover:text-black transition">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-[#81b4c6] uppercase tracking-wider block mb-2">
                            {{ $pkg->meta['subtitle'] ?? 'PACKAGE' }}
                        </span>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-ofis-teal transition">
                            {{ $pkg->title }}
                        </h3>
                        <p class="text-gray-600 text-sm mb-6 line-clamp-3">
                            {{ Str::limit($pkg->content, 140) }}
                        </p>
                    </div>

                    <div>
                        <a href="{{ url('/package/' . $pkg->slug) }}" class="inline-flex items-center gap-2 text-sm font-bold text-ofis-teal hover:text-[#fab54f] transition">
                            <span>{{ t('common.learn_more', 'Learn More') }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-gray-500">
                    No packages available at the moment.
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- About BPT Section -->
<section id="about" class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-[#fab54f] font-bold text-sm uppercase tracking-wider block mb-2">ABOUT BLUE POWER TECHNOLOGY</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-6">
                    {{ t('home.about_h2', 'Empower Your Workforce For Unprecedented Security and Productivity') }}
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    {{ t('home.about_p1', 'As an IT infrastructure provider subsidiary of CTI Group, Blue Power Technology (BPT) offers OFIS as a comprehensive smart office platform to digitalize corporate operations and building management in Indonesia.') }}
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center gap-3 text-gray-700 font-medium">
                        <svg class="w-5 h-5 text-[#fab54f]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        End-to-End Implementation & Consulting
                    </li>
                    <li class="flex items-center gap-3 text-gray-700 font-medium">
                        <svg class="w-5 h-5 text-[#fab54f]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        Open API Integration with Existing Hardware
                    </li>
                    <li class="flex items-center gap-3 text-gray-700 font-medium">
                        <svg class="w-5 h-5 text-[#fab54f]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        24/7 Local Support & Certified Engineers
                    </li>
                </ul>
            </div>

            <div class="flex justify-center">
                <img src="{{ theme_asset('aboutbptcompany.png-COZKYyc6.webp') }}" alt="About BPT" class="w-full max-w-md h-auto rounded-2xl shadow-xl" />
            </div>
        </div>
    </div>
</section>

<!-- Latest Blog Articles Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-[#81b4c6] font-bold text-sm uppercase tracking-wider block mb-2">LATEST INSIGHTS</span>
                <h2 class="text-3xl font-bold text-gray-900">
                    {{ t('home.blog_title', 'Latest Articles & News') }}
                </h2>
            </div>
            <a href="{{ url('/blog') }}" class="mt-4 md:mt-0 text-sm font-bold text-ofis-teal hover:text-[#fab54f] transition flex items-center gap-2">
                <span>View All Articles</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

        @php
            $latestPosts = \Plugins\Posts\Models\Post::where('status', 'published')->latest()->take(3)->get();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestPosts as $p)
                <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition">
                    <div class="h-48 bg-gray-100 relative overflow-hidden">
                        @if($p->featured_image)
                            <x-image :src="$p->featured_image" :alt="$p->title" class="w-full h-full object-cover" />
                        @else
                            <img src="{{ theme_asset('introducing-ofis-maxresdefault-768x432.jpg-CywJQ2Qk.webp') }}" alt="{{ $p->title }}" class="w-full h-full object-cover" />
                        @endif
                    </div>
                    <div class="p-6">
                        <span class="text-xs text-gray-400 block mb-2">{{ $p->published_at ? $p->published_at->format('M d, Y') : '' }}</span>
                        <h3 class="font-bold text-lg text-gray-900 mb-3 line-clamp-2 hover:text-ofis-teal transition">
                            <a href="{{ url('/blog/' . $p->slug) }}">{{ $p->title }}</a>
                        </h3>
                        <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                            {{ Str::limit(strip_tags($p->excerpt ?: $p->content), 120) }}
                        </p>
                        <a href="{{ url('/blog/' . $p->slug) }}" class="text-xs font-bold text-ofis-teal hover:underline">
                            Read Article →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Contact Us Section -->
<section id="contact" class="py-20 bg-slate-900 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold mb-4">
            {{ t('home.contact_h2', 'Ready to Transform Your Workplace?') }}
        </h2>
        <p class="text-gray-300 mb-8 max-w-xl mx-auto">
            {{ t('home.contact_p', 'Get in touch with our smart office specialists today for a free consultation and demonstration.') }}
        </p>

        <!-- Form Renderer / Static Fallback Form -->
        <div class="bg-white/5 border border-white/10 rounded-3xl p-8 text-left max-w-2xl mx-auto">
            @if (session('success'))
                <div class="bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 px-6 py-4 rounded-xl mb-6 font-medium text-sm">
                    <strong>✓ Success!</strong> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-500/20 border border-rose-500/40 text-rose-200 px-6 py-4 rounded-xl mb-6 font-medium text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('forms.submit', 'contact-form') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-2 text-gray-300">{{ t('form.name', 'Name') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-[#fab54f]" placeholder="Your Full Name">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-2 text-gray-300">{{ t('form.email', 'Email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-[#fab54f]" placeholder="you@company.com">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2 text-gray-300">{{ t('form.company', 'Company') }}</label>
                    <input type="text" name="company" value="{{ old('company') }}" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-[#fab54f]" placeholder="Company Name">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-2 text-gray-300">{{ t('form.message', 'Message') }}</label>
                    <textarea name="message" rows="4" required class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-[#fab54f]" placeholder="Tell us about your requirements...">{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-[#fab54f] hover:bg-[#fab54f]/90 text-ofis-ink font-bold rounded-xl shadow-lg transition">
                    {{ t('form.submit', 'Send Inquiry') }}
                </button>
            </form>
        </div>
    </div>
</section>
@endsection