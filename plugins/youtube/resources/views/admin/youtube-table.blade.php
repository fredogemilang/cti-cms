<div class="p-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <span class="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </span>
                YouTube Videos
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                <span>Total Videos: <strong class="text-gray-700 dark:text-gray-200">{{ $totalCount }}</strong></span>
                <span>•</span>
                <span>Last Synced: <strong class="text-gray-700 dark:text-gray-200">{{ $lastSynced }}</strong></span>
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.youtube.settings') }}" class="px-4 py-2 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-gray-200 rounded-xl font-medium text-xs hover:bg-gray-200 dark:hover:bg-zinc-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Settings
            </a>

            <button wire:click="syncNow" wire:loading.attr="disabled" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl text-xs shadow-lg shadow-red-600/20 transition flex items-center gap-2 disabled:opacity-50 cursor-pointer">
                <svg wire:loading.remove wire:target="syncNow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <svg wire:loading wire:target="syncNow" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span wire:loading.remove wire:target="syncNow">Sync Now</span>
                <span wire:loading wire:target="syncNow">Syncing YouTube...</span>
            </button>
        </div>
    </div>

    <!-- Notification Toast -->
    @if($syncNotification)
        <div class="mb-6 p-4 rounded-xl text-xs font-medium flex items-center justify-between {{ $syncNotificationType === 'success' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300 border border-red-200 dark:border-red-800' }}">
            <span>{{ $syncNotification }}</span>
            <button wire:click="$set('syncNotification', '')" class="text-xs hover:underline font-bold">Dismiss</button>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search videos by title or ID..." class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-xs text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-500 focus:outline-none">
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <select wire:model.live="statusFilter" class="px-3 py-2 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-xs text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-red-500 focus:outline-none">
                <option value="all">All Visibility Status</option>
                <option value="visible">Visible Only</option>
                <option value="hidden">Hidden Only</option>
                <option value="featured">Featured Only</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 dark:bg-zinc-800/50 text-gray-500 dark:text-gray-400 font-semibold border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-4">Video</th>
                        <th class="px-6 py-4">YouTube ID</th>
                        <th class="px-6 py-4">Published Date</th>
                        <th class="px-6 py-4">Visibility</th>
                        <th class="px-6 py-4">Featured</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($videos as $video)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/30 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="relative w-24 aspect-video rounded-lg overflow-hidden bg-zinc-900 flex-shrink-0 border border-zinc-200 dark:border-zinc-800">
                                        <img src="{{ $video->getBestThumbnail() }}" class="w-full h-full object-cover" alt="{{ $video->title }}">
                                        @if($video->duration)
                                            <span class="absolute bottom-1 right-1 bg-black/80 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                                                {{ $video->duration }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="max-w-md">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-gray-900 dark:text-gray-100 line-clamp-2 leading-snug">
                                                {{ $video->title }}
                                            </h4>
                                        </div>
                                        @if($video->is_featured)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-md border border-amber-300 dark:border-amber-700 mt-1">
                                                <svg class="w-3 h-3 fill-amber-500 text-amber-500" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                                Featured Video
                                            </span>
                                        @endif
                                        <p class="text-[11px] text-gray-400 mt-1 line-clamp-1">
                                            {{ $video->description }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-500 dark:text-gray-400">
                                <a href="{{ $video->getUrl() }}" target="_blank" class="hover:text-red-600 underline flex items-center gap-1">
                                    {{ $video->youtube_id }}
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                {{ $video->published_at ? $video->published_at->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleVisibility({{ $video->id }})" class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider transition cursor-pointer {{ $video->is_visible ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-gray-100 text-gray-600 dark:bg-zinc-800 dark:text-gray-400' }}">
                                    {{ $video->is_visible ? 'Visible' : 'Hidden' }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleFeatured({{ $video->id }})" title="Toggle Featured Video" class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider transition cursor-pointer flex items-center gap-1.5 {{ $video->is_featured ? 'bg-amber-100 text-amber-900 dark:bg-amber-950/70 dark:text-amber-200 border border-amber-300 dark:border-amber-700' : 'bg-gray-100 text-gray-500 dark:bg-zinc-800 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-zinc-700' }}">
                                    <svg class="w-3 h-3 {{ $video->is_featured ? 'fill-amber-500 text-amber-500' : 'text-gray-400' }}" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    {{ $video->is_featured ? 'Featured' : 'Normal' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ $video->getUrl() }}" target="_blank" class="p-1.5 text-gray-400 hover:text-red-600 transition" title="View on YouTube">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <button wire:click="deleteVideo({{ $video->id }})" onclick="confirm('Are you sure you want to remove this video?') || event.stopImmediatePropagation()" class="p-1.5 text-gray-400 hover:text-red-600 transition cursor-pointer" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                No YouTube videos found. Click <strong>Sync Now</strong> above to pull videos from your channel.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($videos->hasPages())
            <div class="p-4 border-t border-zinc-100 dark:border-zinc-800">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
</div>
