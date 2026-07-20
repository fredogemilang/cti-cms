<div class="space-y-6">
    @if (session('success'))
        <x-admin.ui.alert type="success">{{ session('success') }}</x-admin.ui.alert>
    @endif

    @if ($newPlaintextToken)
        <x-admin.ui.alert type="warning" class="border border-yellow-200 dark:border-yellow-900/30">
            <p class="font-semibold text-yellow-800 dark:text-yellow-400">Token baru — salin sekarang. Tidak akan ditampilkan lagi.</p>
            <code class="block mt-2 p-3 bg-white dark:bg-[#0B0B0B] border border-gray-100 dark:border-[#272B30] rounded-xl font-mono text-sm break-all text-gray-900 dark:text-[#FCFCFC]">{{ $newPlaintextToken }}</code>
        </x-admin.ui.alert>
    @endif

    <x-admin.ui.card padding="p-8">
        <h3 class="text-lg font-bold text-gray-900 dark:text-[#FCFCFC] mb-6">Create new token</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-admin.ui.input 
                name="name" 
                label="Name" 
                wire:model="name"
                placeholder="e.g. Mobile app"
            />
            <x-admin.ui.input 
                name="allowedIps" 
                label="Allowed IPs (optional, comma-separated)" 
                wire:model="allowedIps"
                placeholder="203.0.113.10, 198.51.100.0"
            />
            <x-admin.ui.input 
                name="rateLimit" 
                type="number"
                label="Rate Limit (req/min)" 
                wire:model="rateLimit"
                min="1" 
                max="6000"
            />
        </div>
        <div class="mt-6 flex justify-end">
            <x-admin.ui.button wire:click="create" variant="primary">
                Create token
            </x-admin.ui.button>
        </div>
    </x-admin.ui.card>

    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header>Name</x-admin.ui.table-header>
            <x-admin.ui.table-header>Prefix</x-admin.ui.table-header>
            <x-admin.ui.table-header>Rate Limit</x-admin.ui.table-header>
            <x-admin.ui.table-header>Last used</x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="px-8 w-24">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse ($tokens as $t)
            <x-admin.ui.table-row>
                <x-admin.ui.table-cell class="font-semibold">{{ $t->name }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="font-mono text-xs">{{ $t->prefix }}…</x-admin.ui.table-cell>
                <x-admin.ui.table-cell>{{ $t->rate_limit_per_minute }}/min</x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-gray-500 dark:text-[#6F767E]">{{ $t->last_used_at?->diffForHumans() ?? 'never' }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-8">
                    <x-admin.ui.button 
                        type="button" 
                        variant="danger" 
                        wire:click="revoke({{ $t->id }})" 
                        wire:confirm="Revoke this token?" 
                        class="!py-1.5 !px-4 text-xs"
                    >
                        Revoke
                    </x-admin.ui.button>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @empty
            <x-admin.ui.table-row>
                <x-admin.ui.table-cell colspan="5" class="p-8 text-center text-gray-500 dark:text-[#6F767E]">
                    <div class="flex flex-col items-center">
                        <span class="material-symbols-outlined text-4xl text-[#6F767E] mb-2">vpn_key</span>
                        <span>No tokens yet.</span>
                    </div>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @endforelse
    </x-admin.ui.table>
</div>
