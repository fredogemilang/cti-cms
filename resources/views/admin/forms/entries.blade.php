@extends('layouts.admin')

@section('title', 'Form Entries')
@section('page-title')
    <div class="flex flex-col">
        <span class="text-xs font-bold text-[#6F767E] uppercase tracking-widest mb-1">Form Entries</span>
        <span class="text-[#111827] dark:text-white">{{ $form->name }}</span>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Header Actions --}}
        <div class="flex flex-wrap justify-between items-center gap-4">
            <a href="{{ route('admin.forms.index') }}" 
                class="flex items-center gap-2 text-sm font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                Back to Forms
            </a>

            <div class="flex items-center gap-3">
                {{-- Export Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false" 
                        class="flex items-center gap-2 h-10 px-4 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-sm font-bold text-[#111827] dark:text-white hover:bg-gray-50 dark:hover:bg-[#272B30]/80 transition-all">
                        <span class="material-symbols-outlined text-[20px]">download</span>
                        Export
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </button>
                    
                    <div x-show="open" x-transition.origin.top.right
                        class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-[#1A1A1A] rounded-xl shadow-lg border border-gray-200 dark:border-[#272B30] overflow-hidden z-50">
                        <a href="{{ route('admin.forms.export', $form) }}?format=xlsx" class="block px-4 py-2.5 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30]">Export Excel</a>
                        <a href="{{ route('admin.forms.export', $form) }}?format=pdf" class="block px-4 py-2.5 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30]">Export PDF</a>
                    </div>
                </div>

                <a href="{{ route('admin.forms.edit', $form) }}" 
                    class="px-6 py-3 font-bold rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white text-sm h-10 px-4 !py-0">
                    <span class="material-symbols-outlined text-[20px] mr-2">edit</span>
                    Edit Form
                </a>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-admin.ui.card padding="p-5" class="relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <span class="material-symbols-outlined text-6xl text-blue-500">inbox</span>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-[#6F767E] uppercase tracking-widest mb-1">Total</p>
                    <h3 class="text-2xl font-bold text-[#111827] dark:text-white">{{ $stats['total'] }}</h3>
                </div>
            </x-admin.ui.card>
            <x-admin.ui.card padding="p-5" class="relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <span class="material-symbols-outlined text-6xl text-green-500">today</span>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-[#6F767E] uppercase tracking-widest mb-1">Today</p>
                    <h3 class="text-2xl font-bold text-[#111827] dark:text-white">{{ $stats['today'] }}</h3>
                </div>
            </x-admin.ui.card>
            <x-admin.ui.card padding="p-5" class="relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <span class="material-symbols-outlined text-6xl text-yellow-500">date_range</span>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-[#6F767E] uppercase tracking-widest mb-1">This Week</p>
                    <h3 class="text-2xl font-bold text-[#111827] dark:text-white">{{ $stats['this_week'] }}</h3>
                </div>
            </x-admin.ui.card>
            <x-admin.ui.card padding="p-5" class="relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                    <span class="material-symbols-outlined text-6xl text-purple-500">calendar_month</span>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-[#6F767E] uppercase tracking-widest mb-1">This Month</p>
                    <h3 class="text-2xl font-bold text-[#111827] dark:text-white">{{ $stats['this_month'] }}</h3>
                </div>
            </x-admin.ui.card>
        </div>

        {{-- Filters --}}
        <x-admin.ui.card padding="p-5">
            <form method="GET" action="{{ route('admin.forms.entries', $form) }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-4">
                    <label class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-2 block">Search</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-[#6F767E] z-10">search</span>
                        <x-admin.ui.input 
                            name="search" 
                            type="text" 
                            value="{{ request('search') }}"
                            class="!pl-10 !py-2 !rounded-xl !h-10 text-sm" 
                            placeholder="Search in entries..." 
                        />
                    </div>
                </div>
                <div class="md:col-span-3">
                    <x-admin.ui.input 
                        name="date_from" 
                        type="date" 
                        label="From Date"
                        value="{{ request('date_from') }}"
                        class="!py-2 !rounded-xl !h-10 text-sm" 
                    />
                </div>
                <div class="md:col-span-3">
                    <x-admin.ui.input 
                        name="date_to" 
                        type="date" 
                        label="To Date"
                        value="{{ request('date_to') }}"
                        class="!py-2 !rounded-xl !h-10 text-sm" 
                    />
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <x-admin.ui.button type="submit" variant="primary" class="flex-1 !h-10 !rounded-xl text-sm font-bold">
                        Filter
                    </x-admin.ui.button>
                    @if(request()->hasAny(['search', 'date_from', 'date_to']))
                    <a href="{{ route('admin.forms.entries', $form) }}" 
                        class="h-10 w-10 rounded-xl bg-gray-100 dark:bg-[#272B30] text-[#6F767E] hover:text-red-500 transition-all flex items-center justify-center border border-gray-200 dark:border-[#272B30]"
                        title="Clear filters">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </a>
                    @endif
                </div>
            </form>
        </x-admin.ui.card>

        {{-- Entries Table --}}
        <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] overflow-hidden">
            @if($entries->isEmpty())
                <div class="text-center py-16">
                    <div class="h-20 w-20 rounded-full bg-gray-100 dark:bg-[#272B30] flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-4xl text-[#6F767E]">inbox</span>
                    </div>
                    @if(request()->hasAny(['search', 'date_from', 'date_to']))
                        <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mb-1">No entries found</h3>
                        <p class="text-[#6F767E]">Try adjusting your search criteria</p>
                    @else
                        <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mb-1">No submissions yet</h3>
                        <p class="text-[#6F767E] mb-4">Share your form to start collecting data</p>
                        <div class="flex items-center justify-center gap-2 bg-gray-50 dark:bg-[#272B30] py-2 px-4 rounded-lg inline-flex">
                            <span class="text-sm font-mono text-[#6F767E]">{{ url('/forms/' . $form->slug) }}</span>
                            <button class="text-[#2563EB] hover:text-blue-700" onclick="navigator.clipboard.writeText('{{ url('/forms/' . $form->slug) }}')">
                                <span class="material-symbols-outlined text-[16px]">content_copy</span>
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <x-admin.ui.table>
                    <x-slot:thead>
                        <x-admin.ui.table-header class="px-6 py-4 w-20">ID</x-admin.ui.table-header>
                        @foreach($form->fields->take(4) as $field)
                            @if(!in_array($field->type, ['section', 'divider', 'html']))
                            <x-admin.ui.table-header>{{ Str::limit($field->label, 20) }}</x-admin.ui.table-header>
                            @endif
                        @endforeach
                        <x-admin.ui.table-header>Submitted</x-admin.ui.table-header>
                        <x-admin.ui.table-header align="right" class="px-6 py-4 text-right">Actions</x-admin.ui.table-header>
                    </x-slot:thead>
                    
                    @foreach($entries as $entry)
                    <x-admin.ui.table-row>
                        <x-admin.ui.table-cell class="font-mono font-medium text-[#6F767E]">
                            #{{ $entry->id }}
                        </x-admin.ui.table-cell>
                        @foreach($form->fields->take(4) as $field)
                            @if(!in_array($field->type, ['section', 'divider', 'html']))
                            <x-admin.ui.table-cell class="text-[#111827] dark:text-[#FCFCFC]">
                                @php
                                    $value = $entry->getFieldValue($field->field_id);
                                    // Handle arrays (checkboxes/multiple uploads)
                                    if (is_array($value)) {
                                        $displayValue = implode(', ', $value);
                                    } else {
                                        $displayValue = $value;
                                    }
                                    
                                    // Handle file uploads - showing generic file link/icon
                                    if (in_array($field->type, ['file', 'image']) && $value) {
                                        $isImage = $field->type === 'image';
                                        $displayValue = '<span class="inline-flex items-center gap-1 text-xs bg-gray-100 dark:bg-[#272B30] px-2 py-1 rounded text-[#2563EB]"><span class="material-symbols-outlined text-[14px]">'.($isImage ? 'image' : 'description').'</span> File</span>';
                                    }
                                @endphp
                                
                                @if(in_array($field->type, ['file', 'image']) && $value)
                                    {!! $displayValue !!}
                                @else
                                    <span class="line-clamp-1 block max-w-xs">{{ $displayValue }}</span>
                                @endif
                            </x-admin.ui.table-cell>
                            @endif
                        @endforeach
                        <x-admin.ui.table-cell class="whitespace-nowrap text-[#6F767E]">
                            {{ $entry->created_at->format('M d, Y H:i') }}
                        </x-admin.ui.table-cell>
                        <x-admin.ui.table-cell align="right" class="px-6">
                            <button onclick="viewEntry({{ $entry->id }})" 
                                class="text-[#2563EB] hover:text-blue-700 font-bold text-xs">
                                View Details
                            </button>
                        </x-admin.ui.table-cell>
                    </x-admin.ui.table-row>
                    @endforeach
                </x-admin.ui.table>
                
                <!-- Pagination -->
                @if($entries->hasPages())
                <div class="px-8 py-6 border-t border-gray-100 dark:border-[#272B30] flex items-center justify-between">
                    <p class="text-sm font-medium text-[#6F767E]">
                        Showing {{ $entries->firstItem() }} to {{ $entries->lastItem() }} of {{ $entries->total() }} entries
                    </p>
                    <div class="flex items-center gap-2">
                        @if($entries->onFirstPage())
                        <span class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed select-none border border-gray-200 dark:border-[#272B30]">
                            <span class="material-symbols-outlined text-xl">chevron_left</span>
                        </span>
                        @else
                        <a href="{{ $entries->previousPageUrl() }}"
                            class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                            <span class="material-symbols-outlined text-xl">chevron_left</span>
                        </a>
                        @endif

                        @foreach($entries->getUrlRange(max(1, $entries->currentPage() - 2), min($entries->lastPage(), $entries->currentPage() + 2)) as $page => $url)
                            @if($page == $entries->currentPage())
                            <span class="h-10 w-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-blue-500/20 select-none">{{ $page }}</span>
                            @else
                            <a href="{{ $url }}" class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-sm font-bold text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($entries->hasMorePages())
                        <a href="{{ $entries->nextPageUrl() }}"
                            class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                            <span class="material-symbols-outlined text-xl">chevron_right</span>
                        </a>
                        @else
                        <span class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed select-none border border-gray-200 dark:border-[#272B30]">
                            <span class="material-symbols-outlined text-xl">chevron_right</span>
                        </span>
                        @endif
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
    
    {{-- Entry Detail Modal --}}
    <div id="entryModal" class="fixed inset-0 z-50 hidden" x-data x-cloak>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEntryModal()"></div>
        <div class="absolute right-0 top-0 bottom-0 w-full max-w-lg bg-white dark:bg-[#1A1A1A] shadow-2xl overflow-hidden transform transition-transform duration-300 translate-x-full" id="entryModalContent">
            {{-- Modal Header --}}
            <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-[#272B30]">
                <div>
                    <h3 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Entry Details</h3>
                    <p class="text-sm text-[#6F767E]" id="entryId"></p>
                </div>
                <button onclick="closeEntryModal()" class="h-10 w-10 rounded-xl bg-gray-100 dark:bg-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-white flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            {{-- Modal Content --}}
            <div id="entryContent" class="p-6 overflow-y-auto" style="max-height: calc(100vh - 82px);">
                {{-- Content loaded via JS --}}
            </div>
        </div>
    </div>

    @php
        $entriesDataForModal = $entries->keyBy('id')->map(function($entry) use ($form) {
            $data = [];
            foreach ($form->fields as $field) {
                if (!in_array($field->type, ['section', 'divider', 'html'])) {
                    $value = $entry->getFieldValue($field->field_id);
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    $data[] = [
                        'label' => $field->label,
                        'value' => $value ?? '-',
                        'type' => $field->type
                    ];
                }
            }
            return [
                'id' => $entry->id,
                'submitted_at' => $entry->created_at->format('M d, Y \a\t H:i'),
                'ip_address' => $entry->ip_address,
                'fields' => $data
            ];
        });
    @endphp

    <script>
        // Store entries data for modal
        const entriesData = @json($entriesDataForModal);

        function viewEntry(id) {
            const entry = entriesData[id];
            if (!entry) return;

            // Update header
            document.getElementById('entryId').textContent = '#' + entry.id + ' • ' + entry.submitted_at;

            // Build content
            let html = '<div class="space-y-4">';
            
            entry.fields.forEach(field => {
                let valueHtml = '';
                if (field.type === 'file' || field.type === 'image') {
                    if (field.value && field.value !== '-') {
                        valueHtml = `<a href="${field.value}" target="_blank" class="inline-flex items-center gap-2 text-[#2563EB] hover:underline">
                            <span class="material-symbols-outlined text-[18px]">${field.type === 'image' ? 'image' : 'description'}</span>
                            View File
                        </a>`;
                    } else {
                        valueHtml = '<span class="text-[#6F767E]">-</span>';
                    }
                } else if (field.type === 'textarea') {
                    valueHtml = `<div class="whitespace-pre-wrap text-[#111827] dark:text-[#FCFCFC]">${field.value || '-'}</div>`;
                } else {
                    valueHtml = `<div class="text-[#111827] dark:text-[#FCFCFC]">${field.value || '-'}</div>`;
                }

                html += `
                    <div class="bg-gray-50 dark:bg-[#0B0B0B] rounded-xl p-4">
                        <div class="text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">${field.label}</div>
                        \${valueHtml}
                    </div>
                `;
            });

            // Add metadata
            html += `
                <div class="border-t border-gray-200 dark:border-[#272B30] pt-4 mt-6">
                    <div class="text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Submission Info</div>
                    <div class="text-sm text-[#6F767E]">
                        <div class="flex justify-between py-1">
                            <span>IP Address</span>
                            <span class="font-mono">\${entry.ip_address || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Submitted</span>
                            <span>\${entry.submitted_at}</span>
                        </div>
                    </div>
                </div>
            `;

            html += '</div>';
            document.getElementById('entryContent').innerHTML = html;

            // Show modal
            document.getElementById('entryModal').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('entryModalContent').classList.remove('translate-x-full');
            }, 10);
        }

        function closeEntryModal() {
            document.getElementById('entryModalContent').classList.add('translate-x-full');
            setTimeout(() => {
                document.getElementById('entryModal').classList.add('hidden');
            }, 300);
        }

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeEntryModal();
        });
    </script>
@endsection
