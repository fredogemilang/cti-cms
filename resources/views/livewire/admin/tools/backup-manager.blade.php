<div class="space-y-6">
    <div class="flex items-center justify-between bg-white dark:bg-[#1A1A1A] p-6 rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-sm">
        <div>
            <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">System Backups</h3>
            <p class="text-xs text-[#6F767E]">Backups are automatically generated daily at 02:00 AM, or you can trigger a manual backup anytime.</p>
        </div>
        <button wire:click="createBackup" wire:loading.attr="disabled"
            class="h-10 px-5 rounded-xl bg-[#2563EB] text-white text-xs font-bold hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
            <span wire:loading.remove wire:target="createBackup" class="material-symbols-outlined text-base">cloud_download</span>
            <span wire:loading wire:target="createBackup" class="material-symbols-outlined text-base animate-spin">refresh</span>
            Create Backup Now
        </button>
    </div>

    <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-100 dark:border-[#272B30] flex items-center justify-between">
            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Backup Files ({{ count($backups) }})</h4>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-[#272B30]">
            @forelse($backups as $b)
                <div class="p-4 sm:px-6 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-[#272B30]/30 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-[#2563EB] flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined text-xl">folder_zip</span>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $b['filename'] }}</div>
                            <div class="text-xs text-[#6F767E] flex items-center gap-2 mt-0.5">
                                <span>{{ $b['size'] }}</span>
                                <span>&bull;</span>
                                <span>{{ $b['created_at'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="downloadBackup('{{ $b['filename'] }}')"
                            class="px-3 py-1.5 rounded-xl border border-gray-200 dark:border-[#272B30] text-xs font-bold text-[#2563EB] hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">download</span>
                            Download
                        </button>
                        <button wire:click="deleteBackup('{{ $b['filename'] }}')" wire:confirm="Are you sure you want to delete this backup file?"
                            class="h-8 w-8 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-sm text-[#6F767E]">
                    No system backups generated yet. Click "Create Backup Now" above to generate your first backup.
                </div>
            @endforelse
        </div>
    </div>
</div>
