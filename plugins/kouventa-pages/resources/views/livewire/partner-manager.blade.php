<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#111827] dark:text-[#FCFCFC]">Logo Partners (Technology Partners)</h1>
            <p class="text-sm text-[#6F767E] mt-1">Manage partner logos and optional website links displayed in the "Technology Partners" section on the homepage.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/#our-partner') }}" target="_blank" class="h-10 px-4 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30] flex items-center gap-2 text-xs font-bold transition-all shadow-sm">
                <span class="material-symbols-outlined text-sm">open_in_new</span>
                <span>View Section on Homepage</span>
            </a>

            <button type="button" wire:click="openCreateModal" class="h-10 px-4 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 flex items-center gap-2 text-xs font-bold transition-all shadow-sm">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>Add Partner</span>
            </button>
        </div>
    </div>

    {{-- Partners Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200 dark:border-[#272B30] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 text-[#6F767E] font-bold text-xs uppercase tracking-wider">
                        <th class="py-4 px-5 w-24">Order</th>
                        <th class="py-4 px-5 w-48">Logo Preview</th>
                        <th class="py-4 px-5">Partner Name</th>
                        <th class="py-4 px-5">Website Link (Optional)</th>
                        <th class="py-4 px-5">Status</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#272B30]">
                    @forelse($partners as $idx => $partner)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-[#272B30]/30 transition-colors" wire:key="partner-row-{{ $partner->id }}">
                            {{-- Order Controls --}}
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                        wire:click="moveUp({{ $partner->id }})"
                                        @if($loop->first) disabled @endif
                                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] disabled:opacity-30 disabled:pointer-events-none transition-colors"
                                        title="Move up">
                                        <span class="material-symbols-outlined text-base">keyboard_arrow_up</span>
                                    </button>
                                    <button type="button"
                                        wire:click="moveDown({{ $partner->id }})"
                                        @if($loop->last) disabled @endif
                                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] disabled:opacity-30 disabled:pointer-events-none transition-colors"
                                        title="Move down">
                                        <span class="material-symbols-outlined text-base">keyboard_arrow_down</span>
                                    </button>
                                </div>
                            </td>

                            {{-- Logo Card Preview (Navy #071C33 background so white logos are clearly visible) --}}
                            <td class="py-4 px-5">
                                <div class="w-40 h-16 rounded-xl border border-gray-700 bg-[#071C33] flex items-center justify-center px-4 py-2 shadow-sm">
                                    @if($partner->logo)
                                        <img src="{{ $partner->getLogoUrl() }}"
                                             alt="{{ $partner->alt_text ?: $partner->name }}"
                                             style="max-height: {{ min($partner->max_height ?: 44, 42) }}px; max-width: 100%; width: auto; height: auto; object-fit: contain;">
                                    @else
                                        <span class="text-xs text-gray-400">No Logo</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Partner Name --}}
                            <td class="py-4 px-5">
                                <div class="font-bold text-[#111827] dark:text-[#FCFCFC] text-base">{{ $partner->name }}</div>
                                <div class="text-xs text-[#6F767E] mt-0.5">Max height: {{ $partner->max_height ?: 44 }}px</div>
                            </td>

                            {{-- Website URL --}}
                            <td class="py-4 px-5">
                                @if(!empty($partner->website_url))
                                    <a href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-[#2563EB] hover:underline text-xs font-mono font-semibold">
                                        <span class="material-symbols-outlined text-sm">link</span>
                                        <span class="truncate max-w-[240px]">{{ $partner->website_url }}</span>
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
                                <button type="button" wire:click="toggleActive({{ $partner->id }})" class="focus:outline-none">
                                    @if($partner->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#3F8C5826] text-[#83BF6E] uppercase tracking-wider hover:opacity-80 transition-opacity">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-100 text-gray-500 dark:bg-[#272B30] dark:text-gray-400 uppercase tracking-wider hover:opacity-80 transition-opacity">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            Hidden
                                        </span>
                                    @endif
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-5 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button type="button"
                                        wire:click="openEditModal({{ $partner->id }})"
                                        class="h-8 px-3 rounded-lg border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] hover:bg-gray-50 dark:hover:bg-[#272B30] text-[#111827] dark:text-[#FCFCFC] text-xs font-bold inline-flex items-center gap-1.5 transition-colors shadow-sm">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Edit</span>
                                    </button>

                                    <button type="button"
                                        wire:click="deletePartner({{ $partner->id }})"
                                        wire:confirm="Are you sure you want to remove {{ $partner->name }} from Technology Partners?"
                                        class="h-8 w-8 rounded-lg border border-transparent hover:border-red-200 dark:hover:border-red-900/50 hover:bg-red-50 dark:hover:bg-red-900/20 text-[#6F767E] hover:text-red-600 inline-flex items-center justify-center transition-colors"
                                        title="Delete">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-5 text-center">
                                <div class="max-w-sm mx-auto text-center space-y-3">
                                    <div class="h-12 w-12 rounded-2xl bg-gray-100 dark:bg-[#272B30] text-[#6F767E] flex items-center justify-center mx-auto">
                                        <span class="material-symbols-outlined text-2xl">hub</span>
                                    </div>
                                    <div class="font-bold text-[#111827] dark:text-[#FCFCFC] text-base">No Technology Partners Added</div>
                                    <p class="text-xs text-[#6F767E]">Start adding technology partner logos to display on the homepage.</p>
                                    <button type="button" wire:click="openCreateModal" class="inline-flex items-center gap-2 h-9 px-4 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 text-xs font-bold transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-sm">add</span>
                                        <span>Add First Partner</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-xl rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-6 shadow-2xl space-y-6 z-10">
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">
                            {{ $editingId ? 'Edit Technology Partner' : 'Add Technology Partner' }}
                        </h2>
                        <p class="text-xs text-[#6F767E] mt-0.5">Logo will appear in the Technology Partners section.</p>
                    </div>
                    <button type="button" wire:click="closeModal" class="h-8 w-8 rounded-lg hover:bg-gray-100 dark:hover:bg-[#272B30] flex items-center justify-center text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    {{-- Partner Name --}}
                    <div>
                        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1">
                            Partner Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               wire:model.defer="name"
                               placeholder="e.g. Meta, AWS, Microsoft Foundry"
                               class="w-full h-10 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#111315] text-[#111827] dark:text-[#FCFCFC] text-sm font-semibold focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                        @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Partner Logo Image (Media Picker) --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">
                            Partner Logo Image <span class="text-red-500">*</span>
                        </label>

                        @if($logo)
                            <div class="p-4 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div class="w-44 h-16 rounded-xl border border-gray-700 bg-[#071C33] flex items-center justify-center px-4 py-2 shadow-sm shrink-0">
                                    <img src="{{ resolve_block_asset($logo) }}"
                                         alt="{{ $name ?: 'Partner Logo' }}"
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
                                :field="'partner_logo'"
                                :value="$logo"
                                :label="'Select or Upload Partner Logo'"
                                :compact="true"
                                :key="'partner-logo-picker-' . ($editingId ?? 'new') . '-' . md5($logo)"
                            />
                        </div>

                        @error('logo')
                            <p class="text-xs text-red-500 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Website URL (Optional) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Website Link (Optional)</label>
                            <span class="text-[10px] text-[#6F767E]">If filled, the logo card becomes clickable</span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <span class="material-symbols-outlined text-sm">link</span>
                            </span>
                            <input type="text"
                                   wire:model.defer="website_url"
                                   placeholder="https://example.com"
                                   class="w-full h-10 pl-9 pr-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#111315] text-[#111827] dark:text-[#FCFCFC] text-xs font-mono focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                        </div>
                        @error('website_url') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Alt Text & Max Height --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1">
                                Image Alt Text (SEO & Accessibility)
                            </label>
                            <input type="text"
                                   wire:model.defer="alt_text"
                                   placeholder="e.g. Logo Meta - Technology Partner"
                                   class="w-full h-10 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#111315] text-[#111827] dark:text-[#FCFCFC] text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1">
                                Max Height (px)
                            </label>
                            <input type="number"
                                   wire:model.defer="max_height"
                                   min="16"
                                   max="72"
                                   class="w-full h-10 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#111315] text-[#111827] dark:text-[#FCFCFC] text-xs font-semibold">
                        </div>
                    </div>

                    {{-- Active Toggle --}}
                    <div class="pt-2 flex items-center justify-between border-t border-gray-100 dark:border-[#272B30]">
                        <div>
                            <div class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Visible on Homepage</div>
                            <div class="text-[11px] text-[#6F767E]">Toggle off to temporarily hide without deleting.</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.defer="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-[#272B30] peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2563EB]"></div>
                        </label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 dark:border-[#272B30]">
                        <button type="button" wire:click="closeModal" class="h-10 px-4 rounded-xl border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] text-xs font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 text-xs font-bold transition-all shadow-sm">
                            {{ $editingId ? 'Save Changes' : 'Add Partner' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

