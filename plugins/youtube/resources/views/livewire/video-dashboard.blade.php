<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-6 flex items-center gap-4">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                <span class="material-symbols-outlined text-3xl">video_library</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Videos</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalVideos) }}</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-6 flex items-center gap-4">
            <div class="p-3 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-xl">
                <span class="material-symbols-outlined text-3xl">playlist_play</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Playlists</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalPlaylists) }}</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-6 flex items-center gap-4">
            <div class="p-3 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl">
                <span class="material-symbols-outlined text-3xl">visibility</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Views</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalViews) }}</p>
            </div>
        </div>

        <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-6 flex items-center gap-4">
            <div class="p-3 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-xl">
                <span class="material-symbols-outlined text-3xl">sync</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Sync</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate" title="{{ $lastSync ? \Carbon\Carbon::parse($lastSync)->format('M d, Y H:i') : 'Never' }}">
                    {{ $lastSync ? \Carbon\Carbon::parse($lastSync)->diffForHumans() : 'Never' }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Featured Video</h3>
            @if($featuredVideo)
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    @if($featuredVideo['thumbnail_medium'])
                        <img src="{{ $featuredVideo['thumbnail_medium'] }}" alt="Featured Video" class="w-full sm:w-64 rounded-xl object-cover aspect-video">
                    @else
                        <div class="w-full sm:w-64 aspect-video bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center">
                            <span class="material-symbols-outlined text-gray-400 text-4xl">video_file</span>
                        </div>
                    @endif
                    <div>
                        <h4 class="text-base font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $featuredVideo['title'] }}</h4>
                        <div class="mt-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                            {{ $featuredVideo['formatted_views'] ?? '0' }} views
                        </div>
                        <a href="https://youtube.com/watch?v={{ $featuredVideo['youtube_id'] }}" target="_blank" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-[#272B30] hover:bg-gray-200 dark:hover:bg-[#333] text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                            Watch on YouTube
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-gray-400 text-5xl mb-2">star</span>
                    <p class="text-gray-500 dark:text-gray-400">No featured video set</p>
                    <a href="{{ route('admin.youtube.videos') ?? '#' }}" class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                        Go to Videos to feature one
                    </a>
                </div>
            @endif
        </div>

        <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <button wire:click="syncAll" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#2563EB] hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <span wire:loading wire:target="syncAll" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                    <span wire:loading.remove wire:target="syncAll" class="material-symbols-outlined text-sm">sync</span>
                    Sync All Data
                </button>
                <a href="/video" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-[#272B30] hover:bg-gray-200 dark:hover:bg-[#333] text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-sm">public</span>
                    View Video Page
                </a>
            </div>
        </div>
    </div>
</div>
