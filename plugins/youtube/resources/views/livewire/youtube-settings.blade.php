<div class="space-y-6">
    <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">API Configuration</h3>
        
        <div class="space-y-4">
            <div>
                <label for="apiKey" class="form-label">YouTube API Key</label>
                <div x-data="{ show: false }" class="relative">
                    <input :type="show ? 'text' : 'password'" id="apiKey" wire:model="apiKey" class="form-input-field pr-10">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                        <span class="material-symbols-outlined text-gray-500" x-text="show ? 'visibility_off' : 'visibility'"></span>
                    </button>
                </div>
                @error('apiKey') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="channelId" class="form-label">YouTube Channel ID</label>
                <input type="text" id="channelId" wire:model="channelId" class="form-input-field">
                @error('channelId') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-3 mt-4">
                <button wire:click="testConnection" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-[#272B30] hover:bg-gray-200 dark:hover:bg-[#333] text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-colors">
                    <span wire:loading wire:target="testConnection" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
                    <span wire:loading.remove wire:target="testConnection" class="material-symbols-outlined text-sm">wifi</span>
                    Test Connection
                </button>
            </div>
        </div>

        @if($testError)
            <div class="mt-4 p-4 rounded-xl border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-900/50">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-500">error</span>
                    <div class="text-sm text-red-700 dark:text-red-400">
                        <p class="font-medium">Connection Test Failed</p>
                        <p class="mt-1">{{ $testError }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($channelInfo)
            <div class="mt-4 p-4 rounded-xl border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-900/50">
                <div class="flex items-center gap-4">
                    @if(isset($channelInfo['thumbnail']))
                        <img src="{{ $channelInfo['thumbnail'] }}" alt="Channel" class="w-12 h-12 rounded-full">
                    @else
                        <span class="material-symbols-outlined text-green-500 text-3xl">check_circle</span>
                    @endif
                    <div class="text-sm text-green-700 dark:text-green-400">
                        <p class="font-medium text-base">{{ $channelInfo['title'] ?? 'Connection Successful' }}</p>
                        <div class="flex gap-4 mt-1 text-xs">
                            <span>Subscribers: {{ number_format($channelInfo['subscriberCount'] ?? 0) }}</span>
                            <span>Videos: {{ number_format($channelInfo['videoCount'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="rounded-2xl bg-white dark:bg-[#111315] border border-gray-200 dark:border-[#272B30] p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Display Settings</h3>
        
        <div>
            <label for="perPage" class="form-label">Videos per page</label>
            <input type="number" id="perPage" wire:model="perPage" min="1" max="50" class="form-input-field max-w-xs">
            @error('perPage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="flex justify-end">
        <button wire:click="save" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#2563EB] hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
            <span wire:loading wire:target="save" class="material-symbols-outlined animate-spin text-sm">progress_activity</span>
            <span wire:loading.remove wire:target="save" class="material-symbols-outlined text-sm">save</span>
            Save Settings
        </button>
    </div>
</div>
