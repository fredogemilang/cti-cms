<div class="p-6 space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.cpt.entries.index', $postType->slug) }}" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                    <x-icon name="lucide:arrow-left" class="w-5 h-5" />
                </a>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                    Reorder {{ $postType->plural_label }}
                </h1>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                Drag and drop items to reorder them. The top item will appear as #1 on the website.
            </p>
        </div>

        @if($postType->hierarchical && $parentEntries->isNotEmpty())
            <div class="flex items-center gap-3">
                <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">Parent Filter:</label>
                <select wire:model.live="parentId" class="form-select text-sm rounded-xl border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white">
                    <option value="">Top-Level {{ $postType->plural_label }}</option>
                    @foreach($parentEntries as $pEntry)
                        <option value="{{ $pEntry->id }}">Sub-items of: {{ $pEntry->title }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800/50 flex items-center gap-3">
            <x-icon name="lucide:check-circle-2" class="w-5 h-5 text-emerald-600" />
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Drag & Drop Container -->
    <div 
        x-data="{
            draggingIndex: null,
            items: @js($entries->pluck('id')->toArray()),
            reorder(from, to) {
                if (from === to) return;
                const item = this.items.splice(from, 1)[0];
                this.items.splice(to, 0, item);
                $wire.updateOrder(this.items);
            }
        }"
        class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden"
    >
        @if($entries->isEmpty())
            <div class="p-12 text-center text-zinc-500 dark:text-zinc-400">
                <x-icon name="lucide:layers" class="w-12 h-12 mx-auto mb-3 text-zinc-400 opacity-50" />
                <p class="font-medium text-base">No items found for this selection.</p>
            </div>
        @else
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($entries as $index => $item)
                    <div 
                        wire:key="item-{{ $item->id }}"
                        draggable="true"
                        @dragstart="draggingIndex = {{ $index }}"
                        @dragover.prevent
                        @drop.prevent="reorder(draggingIndex, {{ $index }})"
                        class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors cursor-move group"
                    >
                        <div class="flex items-center gap-4">
                            <!-- Drag Handle Icon -->
                            <div class="p-2 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                </svg>
                            </div>

                            <!-- Position Badge -->
                            <span class="w-8 h-8 rounded-full bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-bold text-xs flex items-center justify-center border border-zinc-200 dark:border-zinc-600">
                                {{ sprintf('%02d', $index + 1) }}
                            </span>

                            @if($item->featured_image)
                                <img src="{{ asset('storage/' . $item->featured_image) }}" alt="" class="w-10 h-10 rounded-lg object-cover border border-zinc-200 dark:border-zinc-700" />
                            @endif

                            <div>
                                <h3 class="font-semibold text-zinc-900 dark:text-white text-base">
                                    {{ $item->title }}
                                </h3>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                    /{{ $item->slug }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full uppercase tracking-wider {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                {{ $item->status }}
                            </span>

                            <span class="text-xs font-mono text-zinc-400 bg-zinc-100 dark:bg-zinc-700 px-2 py-1 rounded">
                                Order: {{ $item->menu_order }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
