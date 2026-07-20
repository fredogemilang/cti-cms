<div class="space-y-6">
    @if (session('success'))
        <x-admin.ui.alert type="success" class="mb-4">
            {{ session('success') }}
        </x-admin.ui.alert>
    @endif

    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Webhooks</h2>
        <x-admin.ui.button 
            wire:click="startCreate" 
            variant="primary"
            class="!py-2 !px-4 text-sm"
        >
            + New Webhook
        </x-admin.ui.button>
    </div>

    @if ($showForm)
        <x-admin.ui.card padding="p-6" class="space-y-4">
            <h3 class="font-semibold text-lg text-gray-900 dark:text-white">{{ $editingId ? 'Edit' : 'Create' }} Webhook</h3>
            <div>
                <x-admin.ui.input 
                    name="name" 
                    type="text" 
                    label="Name" 
                    wire:model="name" 
                    class="!py-2.5" 
                />
                @error('name') <p class="text-xs text-[#FF6A55] mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <x-admin.ui.input 
                    name="url" 
                    type="url" 
                    label="URL" 
                    wire:model="url" 
                    placeholder="https://example.com/hooks/x" 
                    class="!py-2.5" 
                />
                @error('url') <p class="text-xs text-[#FF6A55] mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Events</label>
                <div class="grid grid-cols-2 gap-3 bg-gray-50/50 dark:bg-[#0B0B0B]/20 p-4 rounded-2xl border border-gray-100 dark:border-[#272B30]">
                    @foreach ($eventOptions as $ev)
                        <label class="text-sm flex items-center gap-2 cursor-pointer text-[#111827] dark:text-[#FCFCFC]">
                            <input type="checkbox" wire:model="events" value="{{ $ev }}" class="h-4 w-4 rounded accent-[#2563EB]">
                            <code class="text-xs bg-gray-100 dark:bg-[#272B30] px-1.5 py-0.5 rounded font-mono">{{ $ev }}</code>
                        </label>
                    @endforeach
                </div>
                @error('events') <p class="text-xs text-[#FF6A55] mt-1">{{ $message }}</p> @enderror
            </div>
            <label class="text-sm flex items-center gap-2 cursor-pointer text-[#111827] dark:text-[#FCFCFC]">
                <input type="checkbox" wire:model="is_active" class="h-4 w-4 rounded accent-[#2563EB]"> Active
            </label>
            <div class="flex gap-3">
                <x-admin.ui.button wire:click="save" variant="primary" class="!py-2 !px-4 text-sm">Save</x-admin.ui.button>
                <x-admin.ui.button wire:click="$set('showForm', false)" variant="secondary" class="!py-2 !px-4 text-sm">Cancel</x-admin.ui.button>
            </div>
        </x-admin.ui.card>
    @endif

    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header>Name</x-admin.ui.table-header>
            <x-admin.ui.table-header>URL</x-admin.ui.table-header>
            <x-admin.ui.table-header>Events</x-admin.ui.table-header>
            <x-admin.ui.table-header>Status</x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="w-64 px-6">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse ($webhooks as $w)
            <x-admin.ui.table-row>
                <x-admin.ui.table-cell class="font-semibold">{{ $w->name }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell><code class="text-xs font-mono text-[#2563EB] bg-gray-50 dark:bg-[#272B30]/30 px-1.5 py-0.5 rounded border border-gray-200 dark:border-[#272B30]/50">{{ \Illuminate\Support\Str::limit($w->url, 60) }}</code></x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    <div class="flex flex-wrap gap-1">
                        @foreach ((array) $w->events as $ev)
                            <span class="px-1.5 py-0.5 bg-gray-100 dark:bg-[#272B30] text-xs rounded font-mono">{{ $ev }}</span>
                        @endforeach
                    </div>
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell>
                    @if ($w->is_active) 
                        <span class="px-2 py-0.5 bg-[#3F8C5826] text-[#83BF6E] text-xs rounded-lg font-bold uppercase tracking-wider">active</span>
                    @else 
                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-[#272B30] text-[#6F767E] text-xs rounded-lg font-bold uppercase tracking-wider">inactive</span> 
                    @endif
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-6">
                    <div class="flex items-center justify-end gap-2">
                        <x-admin.ui.button wire:click="edit({{ $w->id }})" variant="secondary" class="!py-1.5 !px-3 text-xs">Edit</x-admin.ui.button>
                        <x-admin.ui.button wire:click="test({{ $w->id }})" variant="outline" class="!py-1.5 !px-3 text-xs">Test</x-admin.ui.button>
                        <x-admin.ui.button wire:click="rotateSecret({{ $w->id }})" wire:confirm="Rotate signing secret? Existing receivers will break until updated." variant="outline" class="!py-1.5 !px-3 text-xs">Secret</x-admin.ui.button>
                        <x-admin.ui.button wire:click="delete({{ $w->id }})" wire:confirm="Delete this webhook?" variant="danger" class="!py-1.5 !px-3 text-xs">Delete</x-admin.ui.button>
                    </div>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
            @if ($editingId === $w->id || ! $showForm)
                <tr class="bg-gray-50/50 dark:bg-[#0B0B0B]/10 border-t border-gray-100 dark:border-[#272B30]">
                    <td colspan="5" class="px-6 py-3 text-xs text-[#6F767E]">
                        <strong>Signing secret:</strong> <code class="bg-white dark:bg-[#272B30] text-[#2563EB] px-2 py-0.5 rounded font-mono border border-gray-200 dark:border-[#272B30]/50">{{ $w->signing_secret }}</code>
                        — receivers verify with: <code class="text-gray-500 dark:text-gray-400">hash_hmac('sha256', body, secret)</code>
                    </td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="5" class="p-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-4xl text-[#6F767E]">webhook</span>
                        <p class="text-sm font-medium text-[#6F767E]">No webhooks yet.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.ui.table>

    {{-- Recent deliveries --}}
    @php $recent = \App\Models\WebhookDelivery::with('webhook')->latest()->limit(20)->get(); @endphp
    <div class="space-y-3 mt-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-white">Recent Deliveries</h3>
        <x-admin.ui.table>
            <x-slot:thead>
                <x-admin.ui.table-header class="w-24">ID</x-admin.ui.table-header>
                <x-admin.ui.table-header>Webhook</x-admin.ui.table-header>
                <x-admin.ui.table-header>Event</x-admin.ui.table-header>
                <x-admin.ui.table-header>Status</x-admin.ui.table-header>
                <x-admin.ui.table-header>Code</x-admin.ui.table-header>
                <x-admin.ui.table-header>When</x-admin.ui.table-header>
            </x-slot:thead>

            @forelse ($recent as $d)
                <x-admin.ui.table-row>
                    <x-admin.ui.table-cell class="font-mono text-xs">#{{ $d->id }}</x-admin.ui.table-cell>
                    <x-admin.ui.table-cell class="font-semibold">{{ $d->webhook?->name ?? '(deleted)' }}</x-admin.ui.table-cell>
                    <x-admin.ui.table-cell><code class="text-xs font-mono bg-gray-100 dark:bg-[#272B30] px-1.5 py-0.5 rounded">{{ $d->event }}</code></x-admin.ui.table-cell>
                    <x-admin.ui.table-cell>
                        @php
                            $statusColors = [
                                'success' => 'bg-[#3F8C5826] text-[#83BF6E]',
                                'failed' => 'bg-red-500/10 text-[#FF6A55]',
                                'retrying' => 'bg-yellow-500/10 text-yellow-500',
                                'pending' => 'bg-blue-500/10 text-blue-500',
                            ];
                            $badgeClass = $statusColors[$d->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="px-2 py-0.5 rounded-lg text-xs font-bold uppercase tracking-wider {{ $badgeClass }}">{{ $d->status }}</span>
                        <span class="text-xs text-[#6F767E] ml-1">(attempt {{ $d->attempts }})</span>
                    </x-admin.ui.table-cell>
                    <x-admin.ui.table-cell class="text-xs font-mono font-bold">{{ $d->response_code ?? '—' }}</x-admin.ui.table-cell>
                    <x-admin.ui.table-cell class="text-xs text-[#6F767E]">{{ $d->created_at?->diffForHumans() }}</x-admin.ui.table-cell>
                </x-admin.ui.table-row>
            @empty
                <tr>
                    <td colspan="6" class="p-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-symbols-outlined text-4xl text-[#6F767E]">send</span>
                            <p class="text-sm font-medium text-[#6F767E]">No deliveries yet.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-admin.ui.table>
    </div>
</div>

