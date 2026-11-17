<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-sm font-medium text-emerald-700 dark:text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Location Tabs --}}
    <div class="flex flex-wrap gap-2 bg-white dark:bg-[#1A1A1A] p-3 rounded-2xl border border-gray-200 dark:border-[#272B30]">
        @foreach($menus as $menu)
            <button
                type="button"
                wire:click="selectMenu({{ $menu->id }})"
                class="px-4 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer {{ $selectedMenuId === $menu->id ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-[#272B30] text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                {{ $menu->name }}
                <span class="ml-1 opacity-70 font-mono text-[10px]">({{ $menu->location }})</span>
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left: Add / Edit Form --}}
        <div class="lg:col-span-5 bg-white dark:bg-[#1A1A1A] p-6 rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-sm space-y-4 h-fit">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900 dark:text-[#FCFCFC]">
                    {{ $editingItemId ? 'Edit Menu Item' : 'Add Menu Item' }}
                </h3>
                @if($editingItemId)
                    <button type="button" wire:click="resetForm" class="text-xs text-gray-500 hover:text-gray-800 dark:hover:text-white cursor-pointer">
                        Cancel Edit
                    </button>
                @endif
            </div>

            <form wire:submit.prevent="saveItem" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Label (EN) *</label>
                    <input type="text" wire:model="titleEn" required class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white" placeholder="e.g. About Us" />
                    @error('titleEn') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Label (ID)</label>
                    <input type="text" wire:model="titleId" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white" placeholder="e.g. Tentang Kami" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">URL (EN / Default) *</label>
                    <input type="text" wire:model="urlEn" required class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white font-mono" placeholder="#about or /about-us" />
                    @error('urlEn') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">URL (ID Override, optional)</label>
                    <input type="text" wire:model="urlId" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white font-mono" placeholder="Leave empty to use EN URL" />
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Item Type</label>
                        <select wire:model="itemType" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white">
                            <option value="link">Standard Link</option>
                            <option value="mega_solutions">Mega Menu: Solutions</option>
                            <option value="mega_alliances">Mega Menu: Alliances</option>
                            <option value="button">Header CTA Button</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Target</label>
                        <select wire:model="target" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white">
                            <option value="_self">Same Tab (_self)</option>
                            <option value="_blank">New Tab (_blank)</option>
                        </select>
                    </div>
                </div>

                <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                    <input type="checkbox" wire:model="isActive" class="rounded border-gray-300 text-blue-600" />
                    <span>Active</span>
                </label>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all cursor-pointer">
                    {{ $editingItemId ? 'Update Menu Item' : 'Add Menu Item' }}
                </button>
            </form>
        </div>

        {{-- Right: Menu Items List --}}
        <div class="lg:col-span-7 bg-white dark:bg-[#1A1A1A] p-6 rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-[#FCFCFC]">{{ $selectedMenu?->name ?? 'Menu Items' }}</h3>
                    <p class="text-xs text-gray-500">{{ $selectedMenu?->description }}</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 dark:bg-[#272B30] text-gray-600 dark:text-gray-300">
                    {{ $items->count() }} items
                </span>
            </div>

            @forelse($items as $item)
                <div class="p-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/70 dark:bg-[#0B0B0B] flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $item->title }}</span>
                            @if(!empty($item->translations['id']['title']))
                                <span class="text-xs text-gray-400">/ {{ $item->translations['id']['title'] }}</span>
                            @endif
                            @if($item->item_type !== 'link')
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-cyan-500/10 text-cyan-600 dark:text-cyan-400">{{ $item->item_type }}</span>
                            @endif
                            @if(!$item->is_active)
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-500/10 text-red-600">Hidden</span>
                            @endif
                        </div>
                        <div class="text-xs font-mono text-gray-500 truncate mt-0.5">{{ $item->url }}</div>
                    </div>

                    <div class="flex items-center gap-1 shrink-0">
                        <button type="button" wire:click="moveUp({{ $item->id }})" class="p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-[#272B30] text-gray-500 cursor-pointer" title="Move Up">
                            <span class="material-symbols-outlined text-base">arrow_upward</span>
                        </button>
                        <button type="button" wire:click="moveDown({{ $item->id }})" class="p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-[#272B30] text-gray-500 cursor-pointer" title="Move Down">
                            <span class="material-symbols-outlined text-base">arrow_downward</span>
                        </button>
                        <button type="button" wire:click="toggleActive({{ $item->id }})" class="p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-[#272B30] text-gray-500 cursor-pointer" title="Toggle Active">
                            <span class="material-symbols-outlined text-base">{{ $item->is_active ? 'visibility' : 'visibility_off' }}</span>
                        </button>
                        <button type="button" wire:click="editItem({{ $item->id }})" class="p-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-500/10 text-blue-600 cursor-pointer" title="Edit">
                            <span class="material-symbols-outlined text-base">edit</span>
                        </button>
                        <button type="button" wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this menu item?" class="p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 text-red-600 cursor-pointer" title="Delete">
                            <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center text-xs text-gray-500">No items in this menu yet.</div>
            @endforelse
        </div>
    </div>
</div>
