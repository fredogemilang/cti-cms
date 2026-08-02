<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Playlists</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $playlists->total() }}</p>
        </div>
        <button wire:click="syncPlaylists" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#2563EB] hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <span wire:loading wire:target="syncPlaylists" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
            <span wire:loading.remove wire:target="syncPlaylists" class="material-symbols-outlined text-sm">sync</span>
            Sync Playlists
        </button>
    </div>

    <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-4">
        <div class="relative max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search playlists..." class="form-input-field pl-10 w-full">
        </div>
    </div>

    <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3 w-16">Thumbnail</th>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Videos</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($playlists as $playlist)
                        <tr class="border-b dark:border-[#272B30] hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                @if($playlist->thumbnail_url)
                                    <img src="{{ $playlist->thumbnail_url }}" alt="" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-gray-400">image</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $playlist->title }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ number_format($playlist->video_count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($playlist->is_visible)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">Visible</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">Hidden</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="toggleVisibility({{ $playlist->id }})" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Toggle Visibility">
                                        <span class="material-symbols-outlined text-[20px]">{{ $playlist->is_visible ? 'visibility' : 'visibility_off' }}</span>
                                    </button>
                                    <a href="{{ $playlist->youtube_url }}" target="_blank" class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Open on YouTube">
                                        <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-gray-300 dark:text-gray-600 text-5xl mb-3">playlist_play</span>
                                <p class="text-gray-500 dark:text-gray-400 font-medium">No playlists found</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Try syncing from YouTube or adjust your search.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($playlists->hasPages())
            <div class="px-6 py-4 border-t dark:border-[#272B30]">
                {{ $playlists->links() }}
            </div>
        @endif
    </div>
</div>
