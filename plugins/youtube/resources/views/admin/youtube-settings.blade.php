<div class="p-6 max-w-4xl">
    <!-- Header Section -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <a href="{{ route('admin.youtube.index') }}" class="p-2 bg-gray-100 dark:bg-zinc-800 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                YouTube Settings
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Configure your YouTube Channel ID and API credentials for automatic video synchronization.
            </p>
        </div>
    </div>

    <!-- Notification Toast -->
    @if($notification)
        <div class="mb-6 p-4 rounded-xl text-xs font-medium flex items-center justify-between {{ $notificationType === 'success' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300 border border-red-200 dark:border-red-800' }}">
            <span>{{ $notification }}</span>
            <button wire:click="$set('notification', '')" class="text-xs hover:underline font-bold">Dismiss</button>
        </div>
    @endif

    <!-- Settings Card Form -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
        <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                YouTube Channel ID
            </label>
            <input type="text" wire:model="channelId" placeholder="UCG0E2Kc-QvMRLJ70Q-XeemA" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-xs text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-500 focus:outline-none">
            <p class="text-[11px] text-gray-400 mt-1">
                Find your Channel ID in your YouTube Channel URL e.g. <code>youtube.com/channel/UCG0E2Kc-QvMRLJ70Q-XeemA</code>.
            </p>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                YouTube Data API v3 Key (Optional)
            </label>
            <input type="text" wire:model="apiKey" placeholder="AIzaSy..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl text-xs text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-500 focus:outline-none font-mono">
            <p class="text-[11px] text-gray-400 mt-1">
                Google Cloud Platform YouTube Data API v3 Key. If left empty, public RSS feed will be used automatically.
            </p>
        </div>

        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
            <div>
                <label class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider block">
                    Automatic Daily Sync
                </label>
                <p class="text-[11px] text-gray-400">
                    Automatically run background sync every day at midnight.
                </p>
            </div>
            <input type="checkbox" wire:model="autoSync" class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
        </div>

        <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800 flex items-center gap-4">
            <button wire:click="saveSettings" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs shadow-lg shadow-red-600/20 transition cursor-pointer">
                Save Settings
            </button>

            <button wire:click="testSync" wire:loading.attr="disabled" class="px-5 py-2.5 bg-gray-100 dark:bg-zinc-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200 font-medium rounded-xl text-xs transition cursor-pointer">
                Test Connection & Sync
            </button>
        </div>
    </div>
</div>
