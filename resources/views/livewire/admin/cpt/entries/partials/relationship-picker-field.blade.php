@php
    $selectedIds = is_array($rawValue)
        ? array_values(array_unique(array_filter(array_map('intval', array_filter($rawValue, fn ($v) => $v !== '' && $v !== null)))))
        : array_values(array_filter([(int) $rawValue]));
    $candById = $candidates->keyBy('id');
    $selectedEntries = collect($selectedIds)->map(fn ($id) => $candById->get($id))->filter()->values();
@endphp
<div class="space-y-2.5">
    @if($selectedEntries->isNotEmpty())
        <div class="flex flex-wrap gap-2">
            @foreach($selectedEntries as $selItem)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50 text-xs font-semibold">
                    <span class="material-symbols-outlined text-sm">link</span>
                    <span>{{ $selItem->title }}</span>
                    <button
                        type="button"
                        wire:click="removeRelationshipItem('{{ $fieldName }}', {{ $selItem->id }}, {{ $rowIndex !== null ? $rowIndex : 'null' }}, {{ $subFieldKey !== null ? "'".$subFieldKey."'" : 'null' }})"
                        class="hover:text-red-500 transition-colors ml-1"
                    >
                        <span class="material-symbols-outlined text-xs">close</span>
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-xs text-gray-400 italic">No related entries selected yet.</div>
    @endif

    <button
        type="button"
        wire:click="openRelationshipModal({{ $fieldId }}, {{ $rowIndex !== null ? $rowIndex : 'null' }}, {{ $subFieldKey !== null ? "'".$subFieldKey."'" : 'null' }})"
        class="px-4 py-2 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 transition-all flex items-center gap-2"
    >
        <span class="material-symbols-outlined text-sm text-blue-500">add_link</span>
        <span>Select {{ $label }}</span>
    </button>
</div>
