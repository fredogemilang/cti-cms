@extends('kouventa::layouts.app')

@section('content')
<div class="kv-canvas-wrap">
<!-- ============ NAV + HERO ============ -->
<section style="background: #071C33; color: #FFFFFF; display: flex; flex-direction: column; position: relative">
<div style="position: absolute; inset: 0; overflow: hidden; pointer-events: none"><div style="position: absolute; right: -240px; top: -200px; width: 900px; height: 900px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.07)"></div></div>
<div style="padding: 88px 80px 96px; display: flex; flex-direction: column; align-items: center; text-align: center; gap: 18px; position: relative">
<span style="font-family: 'JetBrains Mono', monospace; font-size: 13px; letter-spacing: 0.1em; text-transform: uppercase; color: #93A4B6">Blog</span>
<h1 style="margin: 0; max-width: 900px; font-size: 64px; line-height: 1.05; font-weight: 800; letter-spacing: -0.035em">Explore the newest articles<br><span style="color: #2BD46A">in our blog.</span></h1>
</div>
</section>

<!-- ============ ARTICLES ============ -->
<section style="padding: 56px 80px 112px; display: flex; flex-direction: column; gap: 48px">
<div role="group" aria-label="Filter by category" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px">

</div>

@if(is_plugin_active('posts'))
    @php
        $posts = \Plugins\Posts\Models\Post::published()->latest()->paginate(9);
    @endphp

    @if($posts->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 w-full my-12">
            @foreach($posts as $post)
                <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col hover:shadow-lg transition">
                    @if($post->featured_image)
                        <img src="" alt="" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-[#071C33] flex items-center justify-center text-emerald-400 font-bold text-xl">
                            Kouventa
                        </div>
                    @endif
                    <div class="p-6 flex flex-col flex-grow justify-between gap-4">
                        <div class="flex flex-col gap-2">
                            <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">
                                
                            </span>
                            <h3 class="text-xl font-bold text-[#0E1A24] leading-snug">
                                <a href="" class="hover:text-emerald-600 transition">
                                    
                                </a>
                            </h3>
                            <p class="text-sm text-[#4A5866] line-clamp-3">
                                
                            </p>
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-400 pt-4 border-t border-slate-100">
                            <span></span>
                            <a href="" class="font-bold text-emerald-600 hover:underline">
                                 →
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8 flex justify-center">
            
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 my-12">
            <p class="text-slate-500 font-medium"></p>
        </div>
    @endif
@else
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 my-12">
        <p class="text-slate-500 font-medium"></p>
    </div>
@endif
</section>

<!-- ============ FOOTER ============ -->
</div>
@endsection
