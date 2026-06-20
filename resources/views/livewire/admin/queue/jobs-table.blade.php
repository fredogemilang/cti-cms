<div class="bg-white border border-gray-200 rounded-lg" wire:poll.5s>
    @if (session('success'))
        <div class="m-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
    @endif

    <div class="flex gap-2 p-3 border-b">
        <button wire:click="setTab('pending')" class="px-3 py-1.5 text-sm rounded {{ $tab==='pending' ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' }}">Pending</button>
        <button wire:click="setTab('failed')"  class="px-3 py-1.5 text-sm rounded {{ $tab==='failed'  ? 'bg-red-600 text-white'  : 'bg-gray-100 hover:bg-gray-200' }}">Failed</button>
        @if ($tab === 'failed')
            <button wire:click="retryAll" class="ml-auto px-3 py-1.5 text-sm bg-green-600 text-white rounded hover:bg-green-700">Retry All</button>
        @endif
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600">
            <tr>
                <th class="p-3 w-20">ID</th>
                <th class="p-3">Queue</th>
                <th class="p-3">Payload</th>
                <th class="p-3">{{ $tab === 'failed' ? 'Failed At' : 'Available At' }}</th>
                <th class="p-3 w-32">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                @php $payload = json_decode($row->payload, true); @endphp
                <tr class="border-t">
                    <td class="p-3 font-mono text-xs">{{ $row->id }}</td>
                    <td class="p-3">{{ $row->queue }}</td>
                    <td class="p-3">
                        <code class="text-xs text-gray-600">{{ \Illuminate\Support\Str::limit($payload['displayName'] ?? '—', 60) }}</code>
                        @if ($tab === 'failed' && ! empty($row->exception))
                            <details class="mt-1">
                                <summary class="cursor-pointer text-xs text-red-600">exception</summary>
                                <pre class="text-xs mt-1 bg-red-50 p-2 rounded overflow-auto">{{ \Illuminate\Support\Str::limit($row->exception, 800) }}</pre>
                            </details>
                        @endif
                    </td>
                    <td class="p-3 text-gray-500 text-xs">
                        @if ($tab === 'failed')
                            {{ $row->failed_at }}
                        @else
                            {{ date('Y-m-d H:i:s', (int) $row->available_at) }}
                        @endif
                    </td>
                    <td class="p-3">
                        @if ($tab === 'failed')
                            <button wire:click="retry('{{ $row->uuid }}')" class="text-blue-600 hover:underline mr-2">Retry</button>
                            <button wire:click="forget({{ $row->id }})" class="text-red-600 hover:underline">Forget</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-gray-500">No jobs.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-3 border-t">{{ $rows->links() }}</div>
</div>
