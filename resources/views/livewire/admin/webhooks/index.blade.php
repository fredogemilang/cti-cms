<div class="space-y-8">
    @if (session('success'))
        <x-admin.ui.alert type="success" class="mb-4">
            {{ session('success') }}
        </x-admin.ui.alert>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#141619] p-6 rounded-3xl border border-gray-200/80 dark:border-[#272B30] shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">webhook</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Webhooks & Integrations</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Automate real-time data sync with Google Sheets, Zapier, Make, or custom APIs.</p>
                </div>
            </div>
        </div>
        <button 
            type="button"
            wire:click="startCreate" 
            class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm transition-all duration-300 shadow-md shadow-blue-500/20 hover:shadow-lg hover:-translate-y-0.5 flex items-center justify-center gap-2 self-start md:self-auto"
        >
            <span class="material-symbols-outlined text-[20px]">add</span>
            New Webhook
        </button>
    </div>

    {{-- Form Section (Create / Edit) --}}
    @if ($showForm)
        <div class="bg-white dark:bg-[#141619] p-6 md:p-8 rounded-3xl border border-blue-500/30 dark:border-blue-500/20 shadow-xl space-y-6 relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-500 text-2xl">{{ $editingId ? 'edit_note' : 'add_link' }}</span>
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $editingId ? 'Edit Webhook Configuration' : 'Create New Webhook Integration' }}</h3>
                </div>
                <button type="button" wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors p-1">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-6">
                {{-- Webhook Name --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                        Webhook Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model="name" 
                        placeholder="e.g. Google Sheets - Marketing Leads Webhook" 
                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-[#0E1012] border border-gray-200 dark:border-[#2E3238] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    />
                    @error('name') <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Google Apps Script Preset Integration Box --}}
                <div class="bg-emerald-500/5 dark:bg-emerald-500/10 border border-emerald-500/20 dark:border-emerald-500/30 p-5 rounded-2xl space-y-4 relative">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5 text-emerald-700 dark:text-emerald-400 font-bold text-sm">
                            <div class="h-7 w-7 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">table_chart</span>
                            </div>
                            <span>Google Apps Script Integration (Recommended for Google Sheets)</span>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-[11px] font-bold rounded-full uppercase tracking-wider">
                            Preset Mode
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                            Google Sheet Deployment ID
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                wire:model.live="deployment_id" 
                                placeholder="Paste your Deployment ID (e.g. AKfycbxiKdyEd_pGA3d5ytablXj...)" 
                                class="w-full pl-4 pr-10 py-3 rounded-xl bg-white dark:bg-[#0B0C0E] border border-gray-200 dark:border-[#2E3238] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 font-mono text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                            />
                            @if($deployment_id)
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-500">
                                    <span class="material-symbols-outlined text-lg">check_circle</span>
                                </div>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            Pasting your Deployment ID will automatically generate the official Google Apps Script endpoint URL below.
                        </p>
                    </div>
                </div>

                {{-- Endpoint Webhook URL --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                        Endpoint Webhook URL <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="url" 
                        wire:model="url" 
                        placeholder="https://script.google.com/macros/s/YOUR_DEPLOYMENT_ID/exec" 
                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 dark:bg-[#0E1012] border border-gray-200 dark:border-[#2E3238] text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 font-mono text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                    />
                    @error('url') <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Events Selector Cards --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                        Subscribed Events <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-gray-50 dark:bg-[#0E1012] p-4 rounded-2xl border border-gray-200 dark:border-[#2E3238]">
                        @foreach ($eventOptions as $ev)
                            @php
                                $eventIcons = [
                                    'form.submitted' => 'assignment_turned_in',
                                    'page.published' => 'article',
                                    'page.updated' => 'edit_document',
                                    'post.published' => 'newspaper',
                                    'user.registered' => 'person_add',
                                    'media.uploaded' => 'upload_file',
                                    'event.registration.created' => 'event',
                                ];
                                $icon = $eventIcons[$ev] ?? 'hub';
                            @endphp
                            <label class="flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-[#16181B] border border-gray-200 dark:border-[#25282E] hover:border-blue-500/50 cursor-pointer transition-all">
                                <input type="checkbox" wire:model="events" value="{{ $ev }}" class="h-4 w-4 rounded text-blue-600 focus:ring-blue-500 accent-blue-600">
                                <span class="material-symbols-outlined text-blue-500 text-lg">{{ $icon }}</span>
                                <code class="text-xs font-bold font-mono text-gray-800 dark:text-gray-200">{{ $ev }}</code>
                            </label>
                        @endforeach
                    </div>
                    @error('events') <p class="text-xs text-red-500 mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                {{-- Active Toggle & Action Buttons --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-gray-100 dark:border-[#272B30]">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:after:border-gray-600 peer-checked:bg-blue-600 relative"></div>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">Active Integration</span>
                    </label>

                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$set('showForm', false)" class="px-5 py-2.5 rounded-2xl bg-gray-100 dark:bg-[#272B30] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-[#333] font-bold text-sm transition-all">
                            Cancel
                        </button>
                        <button type="button" wire:click="save" class="px-6 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition-all">
                            Save Webhook
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Registered Webhooks Table --}}
    <div class="bg-white dark:bg-[#141619] rounded-3xl border border-gray-200/80 dark:border-[#272B30] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-[#272B30] flex items-center justify-between">
            <h3 class="font-bold text-base text-gray-900 dark:text-white">Active Endpoints</h3>
            <span class="text-xs text-gray-500 font-mono">{{ $webhooks->count() }} registered</span>
        </div>

        <x-admin.ui.table>
            <x-slot:thead>
                <x-admin.ui.table-header>Name</x-admin.ui.table-header>
                <x-admin.ui.table-header>Endpoint URL</x-admin.ui.table-header>
                <x-admin.ui.table-header>Events</x-admin.ui.table-header>
                <x-admin.ui.table-header>Status</x-admin.ui.table-header>
                <x-admin.ui.table-header align="right" class="w-64 px-6">Actions</x-admin.ui.table-header>
            </x-slot:thead>

            @forelse ($webhooks as $w)
                @php
                    $isGoogleSheet = str_contains($w->url, 'script.google.com');
                @endphp
                <x-admin.ui.table-row class="hover:bg-gray-50/50 dark:hover:bg-[#1B1E22]">
                    <x-admin.ui.table-cell class="font-bold text-gray-900 dark:text-white">
                        <div class="flex items-center gap-2.5">
                            @if($isGoogleSheet)
                                <div class="h-7 w-7 rounded-lg bg-emerald-500/10 text-emerald-500 flex items-center justify-center shrink-0" title="Google Sheets Integration">
                                    <span class="material-symbols-outlined text-base">table_chart</span>
                                </div>
                            @else
                                <div class="h-7 w-7 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-base">webhook</span>
                                </div>
                            @endif
                            <span>{{ $w->name }}</span>
                        </div>
                    </x-admin.ui.table-cell>
                    <x-admin.ui.table-cell>
                        <code class="text-xs font-mono text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-1 rounded-lg border border-blue-100 dark:border-blue-900/40 break-all">
                            {{ \Illuminate\Support\Str::limit($w->url, 50) }}
                        </code>
                    </x-admin.ui.table-cell>
                    <x-admin.ui.table-cell>
                        <div class="flex flex-wrap gap-1">
                            @foreach ((array) $w->events as $ev)
                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-[#272B30] text-gray-700 dark:text-gray-300 text-xs rounded-md font-mono font-semibold">{{ $ev }}</span>
                            @endforeach
                        </div>
                    </x-admin.ui.table-cell>
                    <x-admin.ui.table-cell>
                        @if ($w->is_active) 
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold rounded-lg border border-emerald-500/20">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                ACTIVE
                            </span>
                        @else 
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 dark:bg-[#272B30] text-gray-500 text-xs font-bold rounded-lg">
                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                INACTIVE
                            </span> 
                        @endif
                    </x-admin.ui.table-cell>
                    <x-admin.ui.table-cell align="right" class="px-6">
                        <div class="flex items-center justify-end gap-2">
                            <button type="button" wire:click="edit({{ $w->id }})" class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-[#272B30] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-[#333] text-xs font-bold transition-all">
                                Edit
                            </button>
                            <button type="button" wire:click="test({{ $w->id }})" class="px-3 py-1.5 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 text-xs font-bold transition-all">
                                Test Ping
                            </button>
                            <button type="button" wire:click="delete({{ $w->id }})" wire:confirm="Delete this webhook?" class="px-3 py-1.5 rounded-xl bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-500/20 border border-red-500/20 text-xs font-bold transition-all">
                                Delete
                            </button>
                        </div>
                    </x-admin.ui.table-cell>
                </x-admin.ui.table-row>
            @empty
                <tr>
                    <td colspan="5" class="p-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <div class="h-16 w-16 rounded-full bg-gray-100 dark:bg-[#272B30] flex items-center justify-center text-gray-400 mb-2">
                                <span class="material-symbols-outlined text-3xl">webhook</span>
                            </div>
                            <h4 class="font-bold text-gray-900 dark:text-white">No Webhooks Registered Yet</h4>
                            <p class="text-xs text-gray-500 max-w-sm">Add your Google Sheet Deployment ID or custom API endpoint to start receiving automated lead notifications.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-admin.ui.table>
    </div>

    {{-- Recent Deliveries Table --}}
    @php $recent = \App\Models\WebhookDelivery::with('webhook')->latest()->limit(20)->get(); @endphp
    <div class="bg-white dark:bg-[#141619] rounded-3xl border border-gray-200/80 dark:border-[#272B30] shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-[#272B30] flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-gray-400 text-lg">history</span>
                <h3 class="font-bold text-base text-gray-900 dark:text-white">Recent Webhook Deliveries Log</h3>
            </div>
            <span class="text-xs text-gray-500 font-mono">Last {{ $recent->count() }} attempts</span>
        </div>

        <x-admin.ui.table>
            <x-slot:thead>
                <x-admin.ui.table-header class="w-24">ID</x-admin.ui.table-header>
                <x-admin.ui.table-header>Webhook Endpoint</x-admin.ui.table-header>
                <x-admin.ui.table-header>Event Trigger</x-admin.ui.table-header>
                <x-admin.ui.table-header>Status</x-admin.ui.table-header>
                <x-admin.ui.table-header>HTTP Code</x-admin.ui.table-header>
                <x-admin.ui.table-header>Timestamp</x-admin.ui.table-header>
            </x-slot:thead>

            @forelse ($recent as $d)
                <x-admin.ui.table-row class="hover:bg-gray-50/50 dark:hover:bg-[#1B1E22]">
                    <x-admin.ui.table-cell class="font-mono text-xs font-bold text-gray-500">#{{ $d->id }}</x-admin.ui.table-cell>
                    <x-admin.ui.table-cell class="font-bold text-gray-900 dark:text-white">{{ $d->webhook?->name ?? '(deleted)' }}</x-admin.ui.table-cell>
                    <x-admin.ui.table-cell><code class="text-xs font-mono bg-gray-100 dark:bg-[#272B30] text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded-md font-semibold">{{ $d->event }}</code></x-admin.ui.table-cell>
                    <x-admin.ui.table-cell>
                        @php
                            $statusBadges = [
                                'success' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                'failed' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                'retrying' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                'pending' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                            ];
                            $badgeStyle = $statusBadges[$d->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider border {{ $badgeStyle }}">
                            {{ $d->status }}
                        </span>
                        <span class="text-[11px] text-gray-400 ml-1.5">(attempt {{ $d->attempts }})</span>
                    </x-admin.ui.table-cell>
                    <x-admin.ui.table-cell class="text-xs font-mono font-bold">
                        @if($d->response_code == 200)
                            <span class="text-emerald-500 font-bold">200 OK</span>
                        @elseif($d->response_code)
                            <span class="text-red-500 font-bold">{{ $d->response_code }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </x-admin.ui.table-cell>
                    <x-admin.ui.table-cell class="text-xs text-gray-500">{{ $d->created_at?->diffForHumans() }}</x-admin.ui.table-cell>
                </x-admin.ui.table-row>
            @empty
                <tr>
                    <td colspan="6" class="p-12 text-center text-gray-500 text-sm">
                        No delivery attempts logged yet.
                    </td>
                </tr>
            @endforelse
        </x-admin.ui.table>
    </div>
</div>
