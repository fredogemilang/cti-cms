<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Videos</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $videos->total() }}</p>
        </div>
        <button wire:click="syncVideos" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#2563EB] hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <span wire:loading wire:target="syncVideos" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
            <span wire:loading.remove wire:target="syncVideos" class="material-symbols-outlined text-sm">sync</span>
            Sync from YouTube
        </button>
    </div>

    <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-4 flex flex-col sm:flex-row gap-4">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search videos..." class="form-input-field pl-10 w-full">
        </div>
        <select wire:model.live="statusFilter" class="form-input-field sm:w-48">
            <option value="">All Statuses</option>
            <option value="visible">Visible</option>
            <option value="hidden">Hidden</option>
            <option value="featured">Featured</option>
        </select>
        <select wire:model.live="playlistFilter" class="form-input-field sm:w-64">
            <option value="">All Playlists</option>
            @foreach($playlists as $playlist)
                <option value="{{ $playlist->id }}">{{ $playlist->title }}</option>
            @endforeach
        </select>
    </div>

    <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3 w-16">Thumbnail</th>
                        <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('title')">
                            <div class="flex items-center gap-1">
                                Title
                                @if($sortField === 'title')
                                    <span class="material-symbols-outlined text-sm">{{ $sortDirection === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('duration_seconds')">Duration</th>
                        <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('view_count')">Views</th>
                        <th class="px-6 py-3 cursor-pointer" wire:click="sortBy('published_at')">Published</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-center">Featured</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($videos as $video)
                        <tr class="border-b dark:border-[#272B30] hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                @if($video->thumbnail_default)
                                    <img src="{{ $video->thumbnail_default }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-gray-400">image</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white line-clamp-1">{{ $video->title }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $video->channel_title }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ $video->formatted_duration }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ $video->formatted_views }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ $video->published_at ? \Carbon\Carbon::parse($video->published_at)->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($video->is_visible)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Visible</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">Hidden</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($video->is_featured)
                                    <button wire:click="unsetFeatured({{ $video->id }})" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Remove Feature">
                                        <span class="material-symbols-outlined text-yellow-500 text-xl">star</span>
                                    </button>
                                @else
                                    <button wire:click="setFeatured({{ $video->id }})" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Set as Featured">
                                        <span class="material-symbols-outlined text-gray-400 hover:text-yellow-500 text-xl">star_outline</span>
                                    </button>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="toggleVisibility({{ $video->id }})" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Toggle Visibility">
                                        <span class="material-symbols-outlined text-[20px]">{{ $video->is_visible ? 'visibility' : 'visibility_off' }}</span>
                                    </button>
                                    <a href="{{ $video->youtube_url }}" target="_blank" class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Open on YouTube">
                                        <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-5xl mb-3">video_file</span>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No videos found</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Try syncing from YouTube or adjust your search.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($videos->hasPages())
            <div class="px-6 py-4 border-t dark:border-[#272B30]">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
</div>
