<div class="flex flex-col h-full bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] transition-colors duration-200 antialiased font-sans relative">
    {{-- Context Bar --}}
    <div class="flex items-center gap-3 px-6 py-4 md:px-10 border-b border-gray-200 dark:border-[#272B30] bg-white/50 dark:bg-[#0B0B0B]/50 shrink-0">
        <a class="h-9 w-9 flex items-center justify-center rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] transition-all shrink-0"
            href="{{ route('admin.taxonomies.index') }}">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
        </a>
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <h1 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] truncate">
                {{ $isEdit ? 'Edit Taxonomy' : 'Add New Taxonomy' }}
            </h1>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="flex-1 flex overflow-hidden">
        {{-- Left Panel: Main Content Editor --}}
        <div class="flex-1 overflow-y-auto p-6 md:p-10 no-scrollbar">
            <div class="max-w-4xl mx-auto space-y-8">
                <!-- General Settings -->
                <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] p-8 border border-gray-200 dark:border-[#272B30] shadow-sm">
                    <h2 class="text-lg font-bold mb-6 flex items-center gap-2 text-gray-900 dark:text-white">
                        <span class="material-symbols-outlined text-blue-600">info</span>
                        General Settings
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Singular Label -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-[#6F767E]">
                                    Singular Label <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.500ms="singularLabel"
                                    placeholder="e.g. Category"
                                    class="w-full rounded-xl border-none bg-gray-50 dark:bg-[#272B30]/40 py-3 px-4 text-sm text-gray-900 dark:text-[#FCFCFC] placeholder-[#6F767E] focus:ring-2 focus:ring-blue-600 transition-all"
                                >
                                @error('singularLabel') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Plural Label -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-[#6F767E]">
                                    Plural Label <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="pluralLabel"
                                    placeholder="e.g. Categories"
                                    class="w-full rounded-xl border-none bg-gray-50 dark:bg-[#272B30]/40 py-3 px-4 text-sm text-gray-900 dark:text-[#FCFCFC] placeholder-[#6F767E] focus:ring-2 focus:ring-blue-600 transition-all"
                                >
                                @error('pluralLabel') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name (ID) -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-[#6F767E]">
                                    ID <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="name"
                                    placeholder="e.g. product_category"
                                    class="w-full rounded-xl border-none bg-gray-50 dark:bg-[#272B30]/40 py-3 px-4 text-sm text-gray-900 dark:text-[#FCFCFC] placeholder-[#6F767E] focus:ring-2 focus:ring-blue-600 transition-all"
                                >
                                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                <p class="text-[10px] text-[#6F767E]">Internal identifier. Lowercase letters, numbers, and underscores only</p>
                            </div>

                            <!-- Slug -->
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-[#6F767E]">
                                    Slug <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.500ms="slug"
                                    placeholder="e.g. product-category"
                                    class="w-full rounded-xl border-none bg-gray-50 dark:bg-[#272B30]/40 py-3 px-4 text-sm text-gray-900 dark:text-[#FCFCFC] placeholder-[#6F767E] focus:ring-2 focus:ring-blue-600 transition-all"
                                >
                                @error('slug') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                <p class="text-[10px] text-[#6F767E]">URL-friendly identifier</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel: Sidebar --}}
        <aside class="w-[360px] bg-[#F4F5F6] dark:bg-[#0B0B0B] border-l border-gray-200 dark:border-[#272B30] overflow-y-auto no-scrollbar hidden lg:block shrink-0">
            <div class="p-6 space-y-6">
                <!-- Actions Card -->
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] p-5 border border-gray-200 dark:border-[#272B30] shadow-sm dark:shadow-none">
                    <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">rocket_launch</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Actions</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button 
                            type="button"
                            wire:click="save" 
                            wire:loading.attr="disabled"
                            class="w-full px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 shadow-lg shadow-blue-500/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-lg">save</span>
                                <span>{{ $isEdit ? 'Update Taxonomy' : 'Create Taxonomy' }}</span>
                            </span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                        @if($isEdit)
                            <button 
                                type="button"
                                wire:click="delete"
                                wire:confirm="Are you sure you want to delete this taxonomy? This will delete all associated terms."
                                class="w-full px-4 py-2 rounded-xl text-sm font-semibold text-red-600 hover:text-red-700 bg-red-50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/20 transition-all flex items-center justify-center gap-2"
                            >
                                <span class="material-symbols-outlined text-lg">delete</span>
                                Delete
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Configuration Card -->
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] p-5 border border-gray-200 dark:border-[#272B30] shadow-sm dark:shadow-none space-y-6">
                    <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">settings</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Configuration</span>
                    </div>

                    <div class="space-y-6">
                        <!-- Type -->
                        <div class="space-y-3">
                            <label class="text-xs font-bold uppercase tracking-wider text-[#6F767E]">Taxonomy Type</label>
                            <div class="space-y-3">
                                <label class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-[#272B30]/40 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all border {{ $isHierarchical ? 'border-blue-500' : 'border-transparent' }}">
                                    <input type="radio" wire:model.live="isHierarchical" name="is_hierarchical" value="1" class="mt-1 w-4 h-4 text-blue-600 focus:ring-blue-500 bg-[#1A1A1A] border-gray-600">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-blue-500">account_tree</span>
                                            <span class="font-bold text-sm text-gray-900 dark:text-white">Hierarchical</span>
                                        </div>
                                        <p class="text-xs text-[#6F767E] mt-1">Like Categories. Supports parent-child processing.</p>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-[#272B30]/40 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all border {{ !$isHierarchical ? 'border-orange-500' : 'border-transparent' }}">
                                    <input type="radio" wire:model.live="isHierarchical" name="is_hierarchical" value="0" class="mt-1 w-4 h-4 text-orange-600 focus:ring-orange-500 bg-[#1A1A1A] border-gray-600">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-orange-500">label</span>
                                            <span class="font-bold text-sm text-gray-900 dark:text-white">Flat</span>
                                        </div>
                                        <p class="text-xs text-[#6F767E] mt-1">Like Tags. Simple list of terms using commas.</p>
                                    </div>
                                </label>
                            </div>
                            @error('isHierarchical') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                        </div>

                        <!-- Visibility -->
                        <div class="space-y-3 pt-4 border-t border-gray-200 dark:border-[#272B30]">
                            <label class="text-xs font-bold uppercase tracking-wider text-[#6F767E]">Visibility</label>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-[#FCFCFC]">Show in Menu</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="showInMenu" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 dark:bg-[#272B30] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-[#6F767E] peer-checked:after:bg-blue-600 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600/20"></div>
                                    </label>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-[#FCFCFC]">Show in REST API</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model="showInRest" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 dark:bg-[#272B30] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-[#6F767E] peer-checked:after:bg-blue-600 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600/20"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Post Types Card -->
                <div class="rounded-2xl bg-white dark:bg-[#1A1A1A] p-5 border border-gray-200 dark:border-[#272B30] shadow-sm dark:shadow-none space-y-4">
                    <div class="flex items-center gap-2 mb-2 text-[#6F767E]">
                        <span class="material-symbols-outlined text-lg">link</span>
                        <span class="text-xs font-bold uppercase tracking-widest">Attach Directly To</span>
                    </div>

                    @if($availablePostTypes->count() > 0)
                        <div class="space-y-2">
                            @foreach($availablePostTypes as $cpt)
                                <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-[#272B30]/40 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all {{ in_array($cpt->slug, $postTypes) ? 'ring-1 ring-blue-500/50' : '' }}">
                                    <input 
                                        type="checkbox" 
                                        wire:click="togglePostType('{{ $cpt->slug }}')"
                                        @checked(in_array($cpt->slug, $postTypes))
                                        class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 bg-[#1A1A1A] border-gray-600"
                                    >
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm text-gray-500">{{ $cpt->icon }}</span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $cpt->plural_label }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center gap-3 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-700 dark:text-amber-400">
                            <span class="material-symbols-outlined">info</span>
                            <div>
                                <p class="font-medium text-xs">No CPTs found</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </aside>
    </div>
</div>
