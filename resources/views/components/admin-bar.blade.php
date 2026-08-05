@auth
@php
    $adminPath = config('admin.path', 'ctrlpanel');
    $adminUrl = url($adminPath);
    $user = auth()->user();

    // Resolve current entity edit link if available
    $editUrl = null;
    $editLabel = null;

    $page = $page ?? view()->shared('page') ?? request()->attributes->get('page');
    $entry = $entry ?? view()->shared('entry') ?? view()->shared('cpt_entry') ?? request()->attributes->get('cpt_entry') ?? request()->attributes->get('entry');
    $post = $post ?? view()->shared('post') ?? request()->attributes->get('post');

    if (isset($entry) && $entry instanceof \App\Models\CptEntry) {
        $postTypeSlug = $entry->postType?->slug ?? $entry->post_type_slug ?? 'technology-alliance';
        $editUrl = url("{$adminPath}/cpt/entries/{$postTypeSlug}/{$entry->id}/edit");
        $editLabel = 'Edit ' . ($entry->postType?->singular_label ?? 'Entry');
    } elseif (isset($page) && $page instanceof \App\Models\Page) {
        $editUrl = url("{$adminPath}/pages/{$page->id}/edit");
        $editLabel = 'Edit Page';
    } elseif (isset($post) && class_exists(\Plugins\Posts\Models\Post::class) && $post instanceof \Plugins\Posts\Models\Post) {
        $editUrl = url("{$adminPath}/posts/{$post->id}/edit");
        $editLabel = 'Edit Post';
    }
@endphp

<!-- Ultra-Sleek Professional Admin Top Bar (Core Component) -->
<div id="cms-admin-bar" 
     style="background-color: #09090b !important; color: #d4d4d8 !important;"
     class="w-full h-8 bg-[#09090b] text-zinc-300 border-b border-zinc-800/80 text-[11px] flex items-center justify-between px-3 md:px-5 font-sans select-none shrink-0 tracking-tight z-[9999] relative">
    
    <!-- Left Section -->
    <div class="flex items-center gap-1 md:gap-2">
        <!-- Control Panel Brand Link -->
        <a href="{{ $adminUrl }}/dashboard" class="flex items-center gap-1.5 px-2 py-0.5 rounded text-zinc-200 hover:text-white hover:bg-white/10 transition-colors font-medium group">
            <span class="w-4 h-4 rounded bg-amber-500 flex items-center justify-center text-[10px] text-zinc-950 font-black tracking-tighter group-hover:scale-105 transition-transform shadow-xs">C</span>
            <span class="font-semibold text-zinc-100 group-hover:text-white">{{ setting('site_name', config('app.name', 'CMS')) }} Control Panel</span>
        </a>

        @if($editUrl)
        <span class="w-px h-3 bg-zinc-800/80 mx-0.5"></span>

        <!-- Edit Current Item (Contextual Button ala WordPress) -->
        <a href="{{ $editUrl }}" class="flex items-center gap-1.5 px-2 py-0.5 rounded text-blue-400 hover:text-blue-300 hover:bg-blue-500/10 transition-colors font-medium">
            <svg class="w-3 h-3 text-blue-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <span class="font-medium text-blue-300 hover:text-blue-200">{{ $editLabel }}</span>
        </a>
        @endif
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-2 md:gap-3">
        <!-- User Profile -->
        <div class="flex items-center gap-2 px-2 py-0.5 text-zinc-300 font-medium">
            <span class="w-4 h-4 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center font-bold text-[9px] shrink-0">
                {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
            </span>
            <span class="hidden sm:inline font-normal text-zinc-400">Logged in as <strong class="font-semibold text-zinc-200">{{ $user->name ?? 'Admin' }}</strong></span>
        </div>

        <span class="w-px h-3 bg-zinc-800/80 mx-0.5"></span>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-zinc-400 hover:text-red-400 px-1.5 py-0.5 rounded transition-colors text-[11px] cursor-pointer">
                Logout
            </button>
        </form>
    </div>
</div>
@endauth
