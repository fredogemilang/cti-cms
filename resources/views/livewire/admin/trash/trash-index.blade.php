<div class="space-y-6">
    @if (session('success'))
        <x-admin.ui.alert type="success" class="mb-4">
            {{ session('success') }}
        </x-admin.ui.alert>
    @endif

    {{-- Resource tabs --}}
    <div class="flex flex-wrap gap-2 p-1 bg-gray-100/50 dark:bg-[#0B0B0B]/30 rounded-2xl ring-1 ring-gray-200 dark:ring-[#272B30] w-fit items-center mb-6">
        @foreach ($resources as $key => $cfg)
            <button wire:click="$set('resource', '{{ $key }}')"
                    class="h-10 px-4 rounded-xl text-sm font-bold transition-all {{ $resource === $key ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                {{ $cfg['label'] }}
            </button>
        @endforeach
    </div>

    {{-- Bulk actions --}}
    @if (! empty($selected))
        <div class="p-4 rounded-2xl bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border border-yellow-500/20 flex items-center justify-between gap-3 mb-4 transition-all animate-fade-in">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-xl">info</span>
                <span class="text-sm font-bold">{{ count($selected) }} items selected</span>
            </div>
            <div class="flex items-center gap-2">
                <x-admin.ui.button 
                    wire:click="bulkRestore" 
                    variant="outline"
                    class="!py-1.5 !px-3 text-xs bg-white dark:bg-[#1A1A1A]"
                >
                    Restore
                </x-admin.ui.button>
                <x-admin.ui.button 
                    wire:click="bulkForceDelete" 
                    wire:confirm="Delete permanently? This cannot be undone."
                    variant="danger"
                    class="!py-1.5 !px-3 text-xs"
                >
                    Delete Forever
                </x-admin.ui.button>
            </div>
        </div>
    @endif

    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header class="w-8 px-6">
                <input type="checkbox" onclick="document.querySelectorAll('.row-cb').forEach(cb => cb.checked = this.checked).forEach(cb => cb.dispatchEvent(new Event('change')))" class="h-4 w-4 rounded accent-[#2563EB]">
            </x-admin.ui.table-header>
            <x-admin.ui.table-header>Title</x-admin.ui.table-header>
            <x-admin.ui.table-header>Deleted At</x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="w-48 px-6">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse ($items as $item)
            <x-admin.ui.table-row>
                <x-admin.ui.table-cell class="px-6">
                    <input type="checkbox" class="row-cb h-4 w-4 rounded accent-[#2563EB]" wire:model.live="selected" value="{{ $item->id }}">
                </x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="font-semibold">{{ $item->{$titleField} ?? '(untitled)' }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell class="text-xs text-[#6F767E]">{{ $item->deleted_at?->diffForHumans() }}</x-admin.ui.table-cell>
                <x-admin.ui.table-cell align="right" class="px-6">
                    <div class="flex items-center justify-end gap-2">
                        <x-admin.ui.button 
                            wire:click="restore({{ $item->id }})" 
                            variant="secondary"
                            class="!py-1.5 !px-3 text-xs"
                        >
                            Restore
                        </x-admin.ui.button>
                        <x-admin.ui.button 
                            wire:click="forceDelete({{ $item->id }})" 
                            wire:confirm="Permanently delete this item?"
                            variant="danger"
                            class="!py-1.5 !px-3 text-xs"
                        >
                            Delete Forever
                        </x-admin.ui.button>
                    </div>
                </x-admin.ui.table-cell>
            </x-admin.ui.table-row>
        @empty
            <tr>
                <td colspan="4" class="p-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-4xl text-[#6F767E]">delete_outline</span>
                        <p class="text-sm font-medium text-[#6F767E]">Trash is empty</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-admin.ui.table>

    @if ($items->hasPages())
        <div class="mt-6">
            {{ $items->links() }}
        </div>
    @endif
</div>

