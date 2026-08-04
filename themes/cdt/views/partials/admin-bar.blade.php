@auth
@php
    $adminPath = config('admin.path', 'ctrlpanel');
    $adminUrl = url($adminPath);
    $user = auth()->user();

    // Resolve current entity edit link if available
    $editUrl = null;
    $editLabel = null;

    if (isset($entry) && $entry instanceof \App\Models\CptEntry) {
        $editUrl = url("{$adminPath}/cpt/entries/{$entry->postType->slug}/{$entry->id}/edit");
        $editLabel = 'Edit ' . ($entry->postType->singular_label ?? 'Entry');
    } elseif (isset($page) && $page instanceof \App\Models\Page) {
        $editUrl = url("{$adminPath}/pages/{$page->id}/edit");
        $editLabel = 'Edit Page';
    }
@endphp

<!-- Ultra-Sleek Professional Admin Top Bar -->
<div id="cdt-admin-bar" 
     style="background-color: #09090b !important; opacity: 1 !important;"
     class="w-full h-7 bg-[#09090b] text-zinc-300 border-b border-zinc-800/80 text-[11px] flex items-center justify-between px-3 md:px-5 font-sans select-none shrink-0 tracking-tight">
    
    <!-- Left Section -->
    <div class="flex items-center gap-1 md:gap-2">
        <!-- Control Panel Brand Link -->
        <a href="{{ $adminUrl }}/dashboard" class="flex items-center gap-1.5 px-2 py-0.5 rounded text-zinc-200 hover:text-white hover:bg-white/10 transition-colors font-medium group">
            <span class="w-3.5 h-3.5 rounded bg-primary flex items-center justify-center text-[9px] text-white font-black tracking-tighter group-hover:scale-105 transition-transform shadow-xs">C</span>
            <span class="font-semibold text-zinc-100 group-hover:text-white">CDT Control Panel</span>
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
        <!-- User Profile Dropdown -->
        <div x-data="{ open: false }" @click.away="open = false" class="relative">
            <button @click="open = !open" type="button" class="flex items-center gap-1.5 px-2 py-0.5 rounded text-zinc-300 hover:text-white hover:bg-white/10 transition-colors font-medium">
                <span class="w-4 h-4 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center font-bold text-[9px] shadow-xs shrink-0">
                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                </span>
                <span class="hidden sm:inline font-normal text-zinc-400">Howdy, <strong class="font-semibold text-zinc-200">{{ $user->name ?? 'Admin' }}</strong></span>
                <svg class="w-2.5 h-2.5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 top-full mt-1.5 w-56 bg-[#121215] border border-zinc-800 rounded-lg shadow-2xl py-1 z-[100000] text-xs overflow-hidden" 
                 style="display: none; background-color: #121215 !important;">
                
                <!-- Profile Header -->
                <div class="px-4 py-2.5 border-b border-zinc-800/80 bg-zinc-950/60">
                    <p class="font-bold text-white text-xs truncate leading-tight">{{ $user->name ?? 'Admin' }}</p>
                    <p class="text-[11px] text-zinc-400 truncate mt-0.5 font-normal">{{ $user->email ?? '' }}</p>
                </div>

                <!-- Profile Links -->
                <div class="py-1">
                    <a href="{{ $adminUrl }}/profile" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-zinc-300 hover:bg-white/10 hover:text-white transition-colors">
                        <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Edit Profile</span>
                    </a>
                    <a href="{{ $adminUrl }}/dashboard" class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-zinc-300 hover:bg-white/10 hover:text-white transition-colors">
                        <svg class="w-4 h-4 text-zinc-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="border-t border-zinc-800/80 pt-1 pb-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-xs font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 text-left transition-colors">
                        <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endauth
