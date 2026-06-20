<div class="space-y-6">
    @if (session('success'))
        <div class="p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
    @endif

    @if ($newPlaintextToken)
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded">
            <p class="font-semibold text-yellow-800">Token baru — salin sekarang. Tidak akan ditampilkan lagi.</p>
            <code class="block mt-2 p-2 bg-white border rounded font-mono text-sm break-all">{{ $newPlaintextToken }}</code>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h3 class="font-semibold mb-4">Create new token</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" wire:model="name" class="mt-1 w-full rounded border-gray-300" placeholder="e.g. Mobile app">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Allowed IPs (optional, comma-separated)</label>
                <input type="text" wire:model="allowedIps" class="mt-1 w-full rounded border-gray-300" placeholder="203.0.113.10, 198.51.100.0">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Rate Limit (req/min)</label>
                <input type="number" wire:model="rateLimit" min="1" max="6000" class="mt-1 w-full rounded border-gray-300">
                @error('rateLimit') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <button wire:click="create" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create token</button>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="p-3">Name</th>
                    <th class="p-3">Prefix</th>
                    <th class="p-3">Rate Limit</th>
                    <th class="p-3">Last used</th>
                    <th class="p-3 w-24">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tokens as $t)
                    <tr class="border-t">
                        <td class="p-3">{{ $t->name }}</td>
                        <td class="p-3 font-mono text-xs">{{ $t->prefix }}…</td>
                        <td class="p-3">{{ $t->rate_limit_per_minute }}/min</td>
                        <td class="p-3 text-gray-500">{{ $t->last_used_at?->diffForHumans() ?? 'never' }}</td>
                        <td class="p-3">
                            <button wire:click="revoke({{ $t->id }})" wire:confirm="Revoke this token?" class="text-red-600 hover:underline">Revoke</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">No tokens yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
