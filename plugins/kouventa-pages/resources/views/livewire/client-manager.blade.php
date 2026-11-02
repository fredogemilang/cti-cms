<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#111827] dark:text-[#FCFCFC]">Clients (They Trusted Us)</h1>
            <p class="text-sm text-[#6F767E] mt-1">Manage client logos and optional website links displayed in the "They trusted us" section on the homepage.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/#our-customer') }}" target="_blank" class="h-10 px-4 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30] flex items-center gap-2 text-xs font-bold transition-all shadow-sm">
                <span class="material-symbols-outlined text-sm">open_in_new</span>
                <span>View Section on Homepage</span>
            </a>

            <button type="button" wire:click="openCreateModal" class="h-10 px-4 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 flex items-center gap-2 text-xs font-bold transition-all shadow-sm">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>Add Client</span>
            </button>
        </div>
    </div>

    {{-- Clients Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200 dark:border-[#272B30] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 text-[#6F767E] font-bold text-xs uppercase tracking-wider">
                        <th class="py-4 px-5 w-24">Order</th>
                        <th class="py-4 px-5 w-48">Logo Preview</th>
                        <th class="py-4 px-5">Client Name</th>
                        <th class="py-4 px-5">Website Link (Optional)</th>
                        <th class="py-4 px-5">Status</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#272B30]">
                    @forelse($clients as $idx => $client)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-[#272B30]/30 transition-colors" wire:key="client-row-{{ $client->id }}">
                            {{-- Order Controls --}}
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                        wire:click="moveUp({{ $client->id }})"
                                        @if($loop->first) disabled @endif
                                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] disabled:opacity-30 disabled:pointer-events-none transition-colors"
                                        title="Move up">
                                        <span class="material-symbols-outlined text-base">keyboard_arrow_up</span>
                                    </button>
                                    <button type="button"
                                        wire:click="moveDown({{ $client->id }})"
                                        @if($loop->last) disabled @endif
                                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] disabled:opacity-30 disabled:pointer-events-none transition-colors"
                                        title="Move down">
                                        <span class="material-symbols-outlined text-base">keyboard_arrow_down</span>
                                    </button>
                                </div>
                            </td>

                            {{-- Logo Card Preview --}}
                            <td class="py-4 px-5">
                                <div class="w-40 h-16 rounded-xl border border-[#E3E8E6] bg-white flex items-center justify-center px-4 py-2 shadow-sm">
                                    @if($client->logo)
                                        <img src="{{ $client->getLogoUrl() }}"
                                             alt="{{ $client->alt_text ?: $client->name }}"
                                             style="max-height: {{ min($client->max_height ?: 44, 42) }}px; max-width: 100%; width: auto; height: auto; object-fit: contain;">
                                    @else
                                        <span class="text-xs text-gray-400">No Logo</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Client Name --}}
                            <td class="py-4 px-5">
                                <div class="font-bold text-[#111827] dark:text-[#FCFCFC] text-base">{{ $client->name }}</div>
                                <div class="text-xs text-[#6F767E] mt-0.5">Max height: {{ $client->max_height ?: 44 }}px</div>
                            </td>

                            {{-- Website URL --}}
                            <td class="py-4 px-5">
                                @if(!empty($client->website_url))
                                    <a href="{{ $client->website_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-[#2563EB] hover:underline text-xs font-mono font-semibold">
                                        <span class="material-symbols-outlined text-sm">link</span>
                                        <span class="truncate max-w-[240px]">{{ $client->website_url }}</span>
                                        <span class="material-symbols-outlined text-xs">open_in_new</span>
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-[#6F767E] italic">
                                        <span class="material-symbols-outlined text-sm">link_off</span>
                                        <span>No link (Logo only)</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-5">
                                <button type="button" wire:click="toggleActive({{ $client->id }})" class="focus:outline-none">
                                    @if($client->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#3F8C5826] text-[#83BF6E] uppercase tracking-wider hover:opacity-80 transition-opacity">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-100 dark:bg-[#272B30] text-[#6F767E] uppercase tracking-wider hover:opacity-80 transition-opacity">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            Hidden
                                        </span>
                                    @endif
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                        wire:click="openEditModal({{ $client->id }})"
                                        class="h-9 px-3.5 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 flex items-center gap-1.5 text-xs font-bold transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Edit</span>
                                    </button>
                                    <button type="button"
                                        wire:click="deleteClient({{ $client->id }})"
                                        wire:confirm="Are you sure you want to delete '{{ $client->name }}' from 'They trusted us'?"
                                        class="h-9 w-9 rounded-xl border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-red-600 hover:border-red-200 dark:hover:border-red-900/40 hover:bg-red-50 dark:hover:bg-red-900/10 flex items-center justify-center transition-all"
                                        title="Delete client">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#6F767E]">
                                <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">handshake</span>
                                <p class="text-sm font-medium">No clients added yet.</p>
                                <p class="text-xs text-gray-400 mt-1">Click "Add Client" to add a client logo to the "They trusted us" section.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add / Edit Client Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            {{-- Backdrop (Click outside dialog to close) --}}
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="relative bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden z-10">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-[#272B30]">
                    <div class="flex items-center gap-2.5">
                        <div class="h-9 w-9 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-[#2563EB] flex items-center justify-center">
                            <span class="material-symbols-outlined text-lg">{{ $editingId ? 'edit' : 'add_business' }}</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">
                                {{ $editingId ? 'Edit Client' : 'Add New Client' }}
                            </h3>
                            <p class="text-xs text-[#6F767E]">Homepage "They trusted us" section</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="h-8 w-8 rounded-lg text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form wire:submit.prevent="save" class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
                    {{-- Client Name --}}
                    <div>
                        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">
                            Client / Company Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                            wire:model.defer="name"
                            placeholder="e.g. Astra Otoparts"
                            class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-[#111827] dark:text-[#FCFCFC] font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('name')
                            <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Client Logo Image (Media Picker) --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">
                            Client Logo Image <span class="text-red-500">*</span>
                        </label>

                        @if($logo)
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div class="w-44 h-16 rounded-xl border border-[#E3E8E6] bg-white flex items-center justify-center px-4 py-2 shadow-sm shrink-0">
                                    <img src="{{ resolve_block_asset($logo) }}"
                                         alt="{{ $name ?: 'Client Logo' }}"
                                         style="max-height: {{ $max_height ?: 44 }}px; max-width: 100%; width: auto; height: auto; object-fit: contain;">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-mono text-[#6F767E] truncate">{{ $logo }}</p>
                                    <button type="button" wire:click="clearLogo" class="mt-1.5 text-xs font-bold text-red-500 hover:text-red-600 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                        <span>Remove / Change Image</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="pt-1">
                            <livewire:admin.media-picker
                                :field="'client_logo'"
                                :value="$logo"
                                :label="'Select or Upload Logo'"
                                :compact="true"
                                :key="'client-logo-picker-' . ($editingId ?? 'new') . '-' . md5($logo)"
                            />
                        </div>

                        @error('logo')
                            <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Website Link (Optional) --}}
                    <div>
                        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">
                            Website Link <span class="text-[#6F767E] font-normal">(Optional)</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined text-base text-[#6F767E] absolute left-3.5 top-1/2 -translate-y-1/2">link</span>
                            <input type="text"
                                wire:model.defer="website_url"
                                placeholder="https://example.com (leave empty if not linked)"
                                class="w-full h-11 pl-10 pr-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-[#111827] dark:text-[#FCFCFC] font-mono focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <p class="text-[11px] text-[#6F767E] mt-1.5">
                            If filled, clicking this client's logo on the homepage will open the website in a new tab. If left empty, the logo is displayed without a link.
                        </p>
                        @error('website_url')
                            <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Logo Max Height & Active Toggle --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">
                                Logo Max Height (px)
                            </label>
                            <input type="number"
                                min="20"
                                max="64"
                                wire:model.live="max_height"
                                class="w-full h-11 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-[#111827] dark:text-[#FCFCFC] font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div class="flex flex-col justify-end pb-1">
                            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                <input type="checkbox" wire:model.defer="is_active" class="w-4 h-4 rounded border-gray-300 text-[#2563EB] focus:ring-[#2563EB]">
                                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Show on Homepage</span>
                            </label>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 dark:border-[#272B30]">
                        <button type="button" wire:click="closeModal" class="h-10 px-4 rounded-xl border border-gray-200 dark:border-[#272B30] text-xs font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 text-xs font-bold flex items-center gap-2 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-sm">save</span>
                            <span>{{ $editingId ? 'Save Changes' : 'Add Client' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

