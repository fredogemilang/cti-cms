<div class="space-y-6">
    @if (session('success'))
        <div class="p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-gray-900">Webhooks</h2>
        <button wire:click="startCreate" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">+ New Webhook</button>
    </div>

    @if ($showForm)
        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            <h3 class="font-semibold">{{ $editingId ? 'Edit' : 'Create' }} Webhook</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" wire:model="name" class="mt-1 w-full rounded border-gray-300">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">URL</label>
                <input type="url" wire:model="url" placeholder="https://example.com/hooks/x" class="mt-1 w-full rounded border-gray-300">
                @error('url') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Events</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($eventOptions as $ev)
                        <label class="text-sm flex items-center gap-2">
                            <input type="checkbox" wire:model="events" value="{{ $ev }}"> <code class="text-xs">{{ $ev }}</code>
                        </label>
                    @endforeach
                </div>
                @error('events') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <label class="text-sm flex items-center gap-2">
                <input type="checkbox" wire:model="is_active"> Active
            </label>
            <div class="flex gap-3">
                <button wire:click="save" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                <button wire:click="$set('showForm', false)" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
            </div>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-left">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">URL</th>
                    <th class="p-3">Events</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 w-64">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($webhooks as $w)
                    <tr class="border-t">
                        <td class="p-3 font-medium">{{ $w->name }}</td>
                        <td class="p-3"><code class="text-xs">{{ \Illuminate\Support\Str::limit($w->url, 60) }}</code></td>
                        <td class="p-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach ((array) $w->events as $ev)
                                    <span class="px-1.5 py-0.5 bg-gray-100 text-xs rounded">{{ $ev }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="p-3">
                            @if ($w->is_active) <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded">active</span>
                            @else <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-xs rounded">inactive</span> @endif
                        </td>
                        <td class="p-3 space-x-2 text-xs">
                            <button wire:click="edit({{ $w->id }})" class="text-blue-600 hover:underline">Edit</button>
                            <button wire:click="test({{ $w->id }})" class="text-green-600 hover:underline">Test</button>
                            <button wire:click="rotateSecret({{ $w->id }})" wire:confirm="Rotate signing secret? Existing receivers will break until updated." class="text-yellow-600 hover:underline">Rotate Secret</button>
                            <button wire:click="delete({{ $w->id }})" wire:confirm="Delete this webhook?" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                    @if ($editingId === $w->id || ! $showForm)
                        <tr class="bg-gray-50 border-t">
                            <td colspan="5" class="px-3 py-2 text-xs text-gray-600">
                                <strong>Signing secret:</strong> <code class="bg-white px-1 py-0.5 rounded">{{ $w->signing_secret }}</code>
                                — receivers verify with: <code>hash_hmac('sha256', body, secret)</code>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">No webhooks yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Recent deliveries --}}
    @php $recent = \App\Models\WebhookDelivery::with('webhook')->latest()->limit(20)->get(); @endphp
    <div class="bg-white border border-gray-200 rounded-lg">
        <div class="p-3 border-b font-semibold">Recent Deliveries</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-left">
                <tr>
                    <th class="p-3 w-24">ID</th>
                    <th class="p-3">Webhook</th>
                    <th class="p-3">Event</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Code</th>
                    <th class="p-3">When</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recent as $d)
                    <tr class="border-t">
                        <td class="p-3 font-mono text-xs">#{{ $d->id }}</td>
                        <td class="p-3">{{ $d->webhook?->name ?? '(deleted)' }}</td>
                        <td class="p-3"><code class="text-xs">{{ $d->event }}</code></td>
                        <td class="p-3">
                            @php $colors = ['success' => 'green', 'failed' => 'red', 'retrying' => 'yellow', 'pending' => 'blue']; $c = $colors[$d->status] ?? 'gray'; @endphp
                            <span class="px-2 py-0.5 bg-{{ $c }}-100 text-{{ $c }}-700 text-xs rounded">{{ $d->status }}</span>
                            <span class="text-xs text-gray-500">(attempt {{ $d->attempts }})</span>
                        </td>
                        <td class="p-3 text-xs">{{ $d->response_code ?? '—' }}</td>
                        <td class="p-3 text-xs text-gray-500">{{ $d->created_at?->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-gray-500">No deliveries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
