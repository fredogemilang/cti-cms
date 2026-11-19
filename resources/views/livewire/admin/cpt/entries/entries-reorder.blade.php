<div class="p-6 space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white dark:bg-zinc-800 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.cpt.entries.index', $postType->slug) }}" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors">
                    <x-icon name="lucide:arrow-left" class="w-5 h-5" />
                </a>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                    Reorder {{ $postType->plural_label }}
                </h1>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                @if($hasHierarchy && $parentId)
                    @php $activeParent = $parentEntries->firstWhere('id', $parentId); @endphp
                    <span class="inline-flex items-center gap-1.5 font-semibold text-primary">
                        <x-icon name="lucide:folder-tree" class="w-4 h-4" />
                        Reordering Sub-Items of: {{ $activeParent?->title }}
                    </span>
                @else
                    Drag and drop items to reorder them. Item #01 will appear first on the website.
                @endif
            </p>
        </div>

        <!-- Hierarchy Navigation Pills -->
        @if($hasHierarchy && $parentEntries->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2">
                <button 
                    wire:click="$set('parentId', null)" 
                    class="px-4 py-2 text-xs font-bold rounded-xl transition-all border {{ is_null($parentId) ? 'bg-primary text-white border-primary shadow-md' : 'bg-zinc-100 text-zinc-700 border-zinc-200 hover:bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600' }}"
                >
                    📁 Top-Level {{ $postType->plural_label }}
                </button>

                @foreach($parentEntries as $pEntry)
                    @php $childCount = \App\Models\CptEntry::where('parent_id', $pEntry->id)->count(); @endphp
                    @if($childCount > 0)
                        <button 
                            wire:click="$set('parentId', {{ $pEntry->id }})" 
                            class="px-3.5 py-2 text-xs font-semibold rounded-xl transition-all border flex items-center gap-1.5 {{ $parentId === $pEntry->id ? 'bg-primary text-white border-primary shadow-md' : 'bg-zinc-100 text-zinc-700 border-zinc-200 hover:bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-200 dark:border-zinc-600' }}"
                        >
                            <span>└─ {{ $pEntry->title }}</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $parentId === $pEntry->id ? 'bg-white/20 text-white' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-600 dark:text-zinc-300' }}">
                                {{ $childCount }}
                            </span>
                        </button>
                    @endif
                @endforeach
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
                    @php
                        $subCount = $hasHierarchy && is_null($parentId) ? \App\Models\CptEntry::where('parent_id', $item->id)->count() : 0;
                    @endphp
                    <div 
                        wire:key="item-{{ $item->id }}-{{ $parentId ?? 'top' }}"
                        draggable="true"
                        @dragstart="draggingIndex = {{ $index }}"
                        @dragover.prevent
                        @drop.prevent="reorder(draggingIndex, {{ $index }})"
                        class="flex items-center justify-between p-4 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors cursor-move group"
                    >
                        <div class="flex items-center gap-4">
                            <!-- Drag Handle Icon -->
                            <div class="p-2 text-zinc-400 group-hover:text-zinc-700 dark:group-hover:text-zinc-200">
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
                                <div class="flex items-center gap-2">
                                    <h3 class="font-semibold text-zinc-900 dark:text-white text-base">
                                        {{ $item->title }}
                                    </h3>
                                    @if($subCount > 0)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800">
                                            {{ $subCount }} Sub-items
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                    /{{ $item->slug }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if($subCount > 0)
                                <button 
                                    wire:click="$set('parentId', {{ $item->id }})" 
                                    class="px-3 py-1.5 text-xs font-bold text-primary hover:text-white bg-red-50 hover:bg-primary border border-red-200 hover:border-primary rounded-xl transition-all"
                                >
                                    Reorder Sub-items →
                                </button>
                            @endif

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
