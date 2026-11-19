<x-admin.ui.table>
    <x-slot:thead>
        <x-admin.ui.table-header class="px-6">Name</x-admin.ui.table-header>
        <x-admin.ui.table-header>Key</x-admin.ui.table-header>
        <x-admin.ui.table-header>Subject</x-admin.ui.table-header>
        <x-admin.ui.table-header class="w-24 text-center">System</x-admin.ui.table-header>
        <x-admin.ui.table-header align="right" class="w-24 px-6">Actions</x-admin.ui.table-header>
    </x-slot:thead>

    @forelse ($templates as $t)
        <x-admin.ui.table-row>
            <x-admin.ui.table-cell class="px-6 font-semibold">{{ $t->name }}</x-admin.ui.table-cell>
            <x-admin.ui.table-cell class="font-mono text-xs text-gray-500 dark:text-[#6F767E]">{{ $t->key_name }}</x-admin.ui.table-cell>
            <x-admin.ui.table-cell class="truncate max-w-xs text-gray-700 dark:text-[#FCFCFC]">{{ $t->subject }}</x-admin.ui.table-cell>
            <x-admin.ui.table-cell class="text-center">
                @if ($t->is_system) 
                    <span class="inline-block px-2.5 py-0.5 bg-blue-100 text-[#2563EB] dark:bg-blue-900/30 text-xs font-bold uppercase tracking-wider rounded-lg">system</span> 
                @endif
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell align="right" class="px-6">
                <x-admin.ui.button 
                    href="{{ route('admin.email-templates.edit', $t->id) }}" 
                    variant="secondary"
                    class="!py-1.5 !px-3 text-xs"
                >
                    Edit
                </x-admin.ui.button>
            </x-admin.ui.table-cell>
        </x-admin.ui.table-row>
    @empty
        <tr>
            <td colspan="5" class="p-12 text-center">
                <div class="flex flex-col items-center gap-2">
                    <span class="material-symbols-outlined text-4xl text-[#6F767E]">mail</span>
                    <p class="text-sm font-medium text-[#6F767E]">No email templates yet.</p>
                    <p class="text-xs text-[#6F767E]">Run <code>php artisan db:seed --class=EmailTemplateSeeder</code></p>
                </div>
            </td>
        </tr>
    @endforelse
</x-admin.ui.table>

