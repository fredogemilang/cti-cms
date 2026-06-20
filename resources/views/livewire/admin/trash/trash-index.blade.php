<div class="bg-white border border-gray-200 rounded-lg">
    @if (session('success'))
        <div class="m-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
    @endif

    {{-- Resource tabs --}}
    <div class="flex flex-wrap gap-2 p-3 border-b">
        @foreach ($resources as $key => $cfg)
            <button wire:click="$set('resource', '{{ $key }}')"
                    class="px-3 py-1.5 text-sm rounded {{ $resource === $key ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ $cfg['label'] }}
            </button>
        @endforeach
    </div>

    {{-- Bulk actions --}}
    @if (! empty($selected))
        <div class="p-3 border-b bg-yellow-50 flex items-center gap-3">
            <span class="text-sm">{{ count($selected) }} selected</span>
            <button wire:click="bulkRestore" class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700">Restore</button>
            <button wire:click="bulkForceDelete" wire:confirm="Delete permanently? This cannot be undone."
                    class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700">Delete Forever</button>
        </div>
    @endif

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 text-left">
            <tr>
                <th class="p-3 w-8"><input type="checkbox" onclick="document.querySelectorAll('.row-cb').forEach(cb => cb.checked = this.checked).forEach(cb => cb.dispatchEvent(new Event('change')))"></th>
                <th class="p-3">Title</th>
                <th class="p-3">Deleted At</th>
                <th class="p-3 w-48">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3">
                        <input type="checkbox" class="row-cb" wire:model.live="selected" value="{{ $item->id }}">
                    </td>
                    <td class="p-3">{{ $item->{$titleField} ?? '(untitled)' }}</td>
                    <td class="p-3 text-gray-500">{{ $item->deleted_at?->diffForHumans() }}</td>
                    <td class="p-3 space-x-2">
                        <button wire:click="restore({{ $item->id }})" class="text-blue-600 hover:underline">Restore</button>
                        <button wire:click="forceDelete({{ $item->id }})" wire:confirm="Permanently delete this item?"
                                class="text-red-600 hover:underline">Delete Forever</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-6 text-center text-gray-500">Trash is empty.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-3 border-t">{{ $items->links() }}</div>
</div>
