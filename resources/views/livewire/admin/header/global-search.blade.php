<div class="relative hidden md:block" x-data @click.away="$wire.close()">
    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#6F767E] pointer-events-none">
        <span class="material-symbols-outlined text-[24px]">search</span>
    </span>
    <input
        wire:model.live.debounce.300ms="query"
        @focus="$wire.set('open', $wire.query.trim() !== '')"
        @keydown.escape="$wire.close()"
        type="text"
        placeholder="Search pages, users, forms..."
        class="w-80 rounded-xl border border-gray-300 bg-white py-3 pl-12 pr-12 text-sm font-medium text-[#111827] placeholder-[#6F767E] shadow-sm ring-1 ring-gray-300 focus:ring-2 focus:ring-primary dark:bg-[#1A1D1F] dark:text-[#FCFCFC] dark:border-[#272B30] dark:ring-0 dark:focus:ring-white/20 transition-all"
        autocomplete="off"
    />

    {{-- Loading indicator --}}
    <div wire:loading wire:target="query" class="absolute right-4 top-1/2 -translate-y-1/2">
        <svg class="animate-spin h-4 w-4 text-[#2563EB]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </div>

    {{-- Clear button --}}
    @if($query)
        <button
            wire:click="close"
            wire:loading.remove wire:target="query"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]"
        >
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    @endif

    {{-- Dropdown --}}
    @if($open)
        <div
            class="absolute left-0 mt-2 w-[420px] bg-white dark:bg-[#1A1A1A] rounded-2xl shadow-2xl z-50 border border-gray-200 dark:border-[#272B30] overflow-hidden"
            x-cloak
        >
            @if($isTooShort)
                <div class="px-5 py-8 text-center text-sm text-[#6F767E]">
                    Type at least 2 characters to search.
                </div>
            @elseif($isSearching && $totalResults === 0)
                <div class="px-5 py-12 text-center">
                    <span class="material-symbols-outlined text-[40px] text-[#6F767E]">search_off</span>
                    <p class="text-sm font-medium text-[#6F767E] mt-2">No results for "{{ $query }}"</p>
                </div>
            @else
                <div class="max-h-[480px] overflow-y-auto divide-y divide-gray-100 dark:divide-[#272B30]">
                    {{-- Pages --}}
                    @if(count($results['pages']) > 0)
                        <div class="py-2">
                            <div class="px-5 py-1 text-[10px] font-bold text-[#6F767E] uppercase tracking-widest">Pages</div>
                            @foreach($results['pages'] as $p)
                                <a href="{{ route('admin.pages.edit', $p->id) }}" wire:navigate wire:click="close"
                                   class="flex items-center gap-3 px-5 py-2.5 hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition">
                                    <span class="material-symbols-outlined text-[20px] text-blue-500">description</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC] truncate">{{ $p->title }}</p>
                                        <p class="text-[11px] text-[#6F767E] truncate">/{{ $p->slug }} · {{ $p->status }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Users --}}
                    @if(count($results['users']) > 0)
                        <div class="py-2">
                            <div class="px-5 py-1 text-[10px] font-bold text-[#6F767E] uppercase tracking-widest">Users</div>
                            @foreach($results['users'] as $u)
                                <a href="{{ route('admin.users.edit', $u->id) }}" wire:navigate wire:click="close"
                                   class="flex items-center gap-3 px-5 py-2.5 hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition">
                                    <span class="material-symbols-outlined text-[20px] text-emerald-500">person</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC] truncate">{{ $u->name }}</p>
                                        <p class="text-[11px] text-[#6F767E] truncate">{{ $u->email }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Forms --}}
                    @if(count($results['forms']) > 0)
                        <div class="py-2">
                            <div class="px-5 py-1 text-[10px] font-bold text-[#6F767E] uppercase tracking-widest">Forms</div>
                            @foreach($results['forms'] as $f)
                                <a href="{{ route('admin.forms.edit', $f->id) }}" wire:navigate wire:click="close"
                                   class="flex items-center gap-3 px-5 py-2.5 hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition">
                                    <span class="material-symbols-outlined text-[20px] text-purple-500">dynamic_form</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC] truncate">{{ $f->name }}</p>
                                        <p class="text-[11px] text-[#6F767E] truncate">/{{ $f->slug }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- CPT Entries --}}
                    @if(count($results['cpt_entries']) > 0)
                        <div class="py-2">
                            <div class="px-5 py-1 text-[10px] font-bold text-[#6F767E] uppercase tracking-widest">Content</div>
                            @foreach($results['cpt_entries'] as $e)
                                <a href="{{ $e->postType ? route('admin.cpt.entries.edit', [$e->postType->slug, $e->id]) : '#' }}" wire:navigate wire:click="close"
                                   class="flex items-center gap-3 px-5 py-2.5 hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition">
                                    <span class="material-symbols-outlined text-[20px] text-orange-500">article</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC] truncate">{{ $e->title }}</p>
                                        <p class="text-[11px] text-[#6F767E] truncate">{{ $e->postType?->plural_label ?? 'Entry' }} · {{ $e->status }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
