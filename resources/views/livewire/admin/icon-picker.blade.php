<div>
    {{-- Trigger & Current Value Display --}}
    <div>
        @if($value)
            <div class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-xl w-fit">
                <div class="w-10 h-10 rounded-lg bg-white dark:bg-[#272B30] border border-gray-200 dark:border-gray-700 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    {!! render_icon($value, 'w-6 h-6') !!}
                </div>
                <div class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                    <div>{{ $value }}</div>
                </div>
                <div class="flex items-center gap-1 ml-2">
                    <button type="button" wire:click="openModal" class="p-1 text-gray-400 hover:text-blue-500 transition-colors" title="Change Icon">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </button>
                    <button type="button" wire:click="clearIcon" class="p-1 text-gray-400 hover:text-red-500 transition-colors" title="Remove Icon">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
            </div>
        @else
            <button type="button" wire:click="openModal" class="px-4 py-2 bg-gray-50 dark:bg-[#1A1A1A] hover:bg-gray-100 dark:hover:bg-[#272B30] border border-gray-200 dark:border-[#272B30] rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-300 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm text-blue-500">category</span>
                <span>{{ $label }}</span>
            </button>
        @endif
    </div>

    {{-- Icon Picker Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 rounded-2xl w-full max-w-3xl max-h-[85vh] flex flex-col shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-150">
                
                <!-- Modal Header -->
                <div class="p-4 md:p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-white dark:bg-[#111827]">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-500">category</span>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Choose Icon</h3>
                    </div>
                    <button type="button" wire:click="closeModal" class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-lg transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <!-- Search & Filters -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-[#161D2F] flex flex-col md:flex-row gap-3">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-sm">search</span>
                        <input 
                            type="text" 
                            wire:model.live.debounce.150ms="search" 
                            placeholder="Search icons (e.g. shield, server, user)..."
                            class="w-full pl-9 pr-4 py-2 text-xs bg-white dark:bg-[#0B0B0B] border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        >
                    </div>

                    <!-- Library Selector Tabs -->
                    <select wire:model.live="selectedLibrary" class="px-3 py-2 text-xs bg-white dark:bg-[#0B0B0B] border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Libraries</option>
                        @foreach($libraries as $prefix => $lib)
                            <option value="{{ $prefix }}">{{ $lib['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Icon Grid Content -->
                <div class="p-4 overflow-y-auto flex-1 max-h-[50vh]">
                    @if(count($icons) > 0)
                        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                            @foreach($icons as $iconItem)
                                <button 
                                    type="button" 
                                    wire:click="selectIcon('{{ $iconItem['key'] }}')"
                                    class="flex flex-col items-center justify-center p-3 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#1A1A1A] hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:border-blue-500 dark:hover:border-blue-500 transition-all group/item cursor-pointer text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400"
                                    title="{{ $iconItem['key'] }} ({{ $iconItem['label'] }})"
                                >
                                    <div class="w-8 h-8 flex items-center justify-center transition-transform group-hover/item:scale-110">
                                        {!! $iconItem['svg'] !!}
                                    </div>
                                    <span class="text-[10px] text-gray-500 dark:text-gray-400 truncate w-full text-center mt-1">
                                        {{ $iconItem['name'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 flex flex-col items-center justify-center text-center">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600 mb-2">search_off</span>
                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400">No icons match your search "{{ $search }}".</p>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-[#111827] flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>Showing {{ count($icons) }} icons</span>
                    <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 rounded-xl font-semibold text-gray-800 dark:text-white transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
