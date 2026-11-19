<div class="space-y-6" wire:poll.5s>
    @if (session('success'))
        <x-admin.ui.alert type="success" class="mb-4">
            {{ session('success') }}
        </x-admin.ui.alert>
    @endif

    <div class="flex items-center justify-between">
        <div class="flex gap-2 p-1 bg-gray-100/50 dark:bg-[#0B0B0B]/30 rounded-2xl ring-1 ring-gray-200 dark:ring-[#272B30] w-fit items-center">
            <button 
                wire:click="setTab('pending')" 
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all {{ $tab === 'pending' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
            >
                Pending
            </button>
            <button 
                wire:click="setTab('failed')" 
                class="h-10 px-4 rounded-xl text-sm font-bold transition-all {{ $tab === 'failed' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}"
            >
                Failed
            </button>
        </div>
        @if ($tab === 'failed' && count($rows) > 0)
            <x-admin.ui.button 
                wire:click="retryAll" 
                variant="primary" 
                class="!py-2 !px-4 text-sm"
            >
                Retry All
            </x-admin.ui.button>
        @endif
    </div>

    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header class="w-20">ID</x-admin.ui.table-header>
            <x-admin.ui.table-header>Queue</x-admin.ui.table-header>
            <x-admin.ui.table-header>Payload</x-admin.ui.table-header>
            <x-admin.ui.table-header>{{ $tab === 'failed' ? 'Failed At' : 'Available At' }}</x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="w-32 px-6">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse ($rows as $row)
            @php $payload = json_decode($row->payload, true); @endphp
            <x-admin.ui.table-row>
                <x-admin.ui.table-cell class="font-mono text-xs">{{ $row->id }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="font-semibold">{{ $row->queue }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    <code class="text-xs text-gray-600 dark:text-[#6F767E] bg-gray-50 dark:bg-[#272B30]/30 px-1.5 py-0.5 rounded">{{ \Illuminate\Support\Str::limit($payload['displayName'] ?? '—', 60) }}</code>
                    @if ($tab === 'failed' && ! empty($row->exception))
                        <details class="mt-2 text-xs">
                            <summary class="cursor-pointer font-bold text-red-500 hover:text-red-600 transition-colors">Exception stack trace</summary>
                            <pre class="mt-2 bg-red-500/10 text-[#FF6A55] p-3 rounded-xl overflow-auto max-h-60 font-mono text-xs whitespace-pre-wrap">{{ \Illuminate\Support\Str::limit($row->exception, 800) }}</pre>
                        </details>
                    @endif
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-xs text-[#6F767E]">
                    @if ($tab === 'failed')
                        {{ $row->failed_at }}
                    @else
                        {{ date('Y-m-d H:i:s', (int) $row->available_at) }}
                    @endif
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-6">
                    @if ($tab === 'failed')
                        <div class="flex items-center justify-end gap-2">
                            <x-admin.ui.button 
                                wire:click="retry('{{ $row->uuid }}')" 
                                variant="outline"
                                class="!py-1.5 !px-3 text-xs"
                            >
                                Retry
                            </x-admin.ui.button>
                            <x-admin.ui.button 
                                wire:click="forget({{ $row->id }})" 
                                variant="danger"
                                class="!py-1.5 !px-3 text-xs"
                            >
                                Forget
                            </x-admin.ui.button>
                        </div>
                    @endif
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @empty
            <tr>
                <td colspan="5" class="p-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-4xl text-[#6F767E]">work_history</span>
                        <p class="text-sm font-medium text-[#6F767E]">No jobs in queue.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.ui.table>

    @if ($rows->hasPages())
        <div class="mt-6">
            {{ $rows->links() }}
        </div>
    @endif
</div>

