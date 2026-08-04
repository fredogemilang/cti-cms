@extends('ofis::layouts.app')

@section('content')
<section class="bg-slate-50 py-12 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-seo-breadcrumbs class="text-gray-500 mb-6" />
        <h1 class="text-4xl font-bold text-gray-900 mb-3">
            {{ t('package.archive_title', 'OFIS Smart Office Packages') }}
        </h1>
        <p class="text-gray-600 text-lg">
            {{ t('package.archive_subtitle', 'Comprehensive smart office solutions designed for modern enterprise efficiency and security.') }}
        </p>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($entries as $pkg)
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 hover:shadow-xl transition flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 bg-[#81b4c6]/20 rounded-xl flex items-center justify-center mb-6 text-[#81b4c6] group-hover:bg-[#fab54f] group-hover:text-black transition">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        @if(!empty($pkg->meta['subtitle']))
                            <span class="text-xs font-bold text-[#81b4c6] uppercase tracking-wider block mb-2">
                                {{ $pkg->meta['subtitle'] }}
                            </span>
                        @endif
                        <h2 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-ofis-teal transition">
                            <a href="{{ url('/package/' . $pkg->slug) }}">{{ $pkg->title }}</a>
                        </h2>
                        <p class="text-gray-600 text-sm mb-6 line-clamp-3">
                            {{ Str::limit(strip_tags($pkg->content), 140) }}
                        </p>
                    </div>

                    <div>
                        <a href="{{ url('/package/' . $pkg->slug) }}" class="inline-flex items-center gap-2 text-sm font-bold text-ofis-teal hover:text-[#fab54f] transition">
                            <span>Learn More</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-gray-500">
                    No packages available.
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
