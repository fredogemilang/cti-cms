<div>
    @if($showTrigger)
    {{-- Trigger Button / Preview --}}
    <div class="space-y-3">
        @if($compact)
            {{-- COMPACT MODE (128x128px) --}}
            @if($value && $selectedMedia)
                {{-- Preview Selected Media --}}
                <div class="relative w-32 h-32 rounded-xl overflow-hidden group bg-gray-100 dark:bg-[#272B30] border border-gray-200 dark:border-gray-700 shadow-sm">
                    <img 
                        src="{{ $selectedMedia['webp_url'] ?? $selectedMedia['url'] }}" 
                        alt="{{ $selectedMedia['original_filename'] ?? 'Selected media' }}"
                        class="w-full h-full object-cover">
                    
                    {{-- Overlay Actions --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <button 
                            type="button"
                            wire:click="openModal"
                            class="p-1.5 bg-white/90 dark:bg-black/90 rounded-lg hover:bg-white dark:hover:bg-black transition-colors"
                            title="Change">
                            <span class="material-symbols-outlined text-[#111827] dark:text-[#FCFCFC] text-sm">swap_horiz</span>
                        </button>
                        <button 
                            type="button"
                            wire:click="removeMedia"
                            class="p-1.5 bg-white/90 dark:bg-black/90 rounded-lg hover:bg-white dark:hover:bg-black transition-colors"
                            title="Remove">
                            <span class="material-symbols-outlined text-red-600 text-sm">delete</span>
                        </button>
                    </div>
                </div>
                <div class="text-xs text-gray-500 truncate max-w-[200px]">{{ $selectedMedia['original_filename'] }}</div>
            @else
                {{-- Empty State / Select Button --}}
                <button 
                    type="button"
                    wire:click="openModal"
                    class="w-32 h-32 bg-gray-50 dark:bg-[#0B0B0B] rounded-xl border-2 border-dashed border-gray-200 dark:border-[#272B30] flex flex-col items-center justify-center gap-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-[#272B30]/50 hover:border-blue-300 dark:hover:border-blue-700 transition-all group">
                    <div class="w-10 h-10 rounded-full bg-white dark:bg-[#272B30] flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-xl text-blue-600">add</span>
                    </div>
                    <span class="text-[10px] font-bold text-[#6F767E] uppercase tracking-wider text-center px-2">{{ $label }}</span>
                </button>
            @endif

        @else
            {{-- DEFAULT MODE (Large / Featured Image) --}}
            @if($value && $selectedMedia)
                {{-- Preview Selected Media --}}
                <div class="relative aspect-video w-full rounded-xl overflow-hidden group bg-gray-100 dark:bg-[#272B30] border border-gray-200 dark:border-gray-700 shadow-sm">
                    <img 
                        src="{{ $selectedMedia['webp_url'] ?? $selectedMedia['url'] }}" 
                        alt="{{ $selectedMedia['original_filename'] ?? 'Selected media' }}"
                        class="w-full h-full object-cover">
                    
                    {{-- Overlay Actions --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <button 
                            type="button"
                            wire:click="openModal"
                            class="p-2 bg-white/90 dark:bg-black/90 rounded-lg hover:bg-white dark:hover:bg-black transition-colors"
                            title="Change">
                            <span class="material-symbols-outlined text-[#111827] dark:text-[#FCFCFC]">swap_horiz</span>
                        </button>
                        <button 
                            type="button"
                            wire:click="removeMedia"
                            class="p-2 bg-white/90 dark:bg-black/90 rounded-lg hover:bg-white dark:hover:bg-black transition-colors"
                            title="Remove">
                            <span class="material-symbols-outlined text-red-600">delete</span>
                        </button>
                    </div>
                </div>
            @else
                {{-- Empty State / Select Button --}}
                <button 
                    type="button"
                    wire:click="openModal"
                    class="w-full aspect-video bg-gray-50 dark:bg-[#0B0B0B] rounded-xl border-2 border-dashed border-gray-200 dark:border-[#272B30] flex flex-col items-center justify-center gap-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-[#272B30]/50 hover:border-blue-300 dark:hover:border-blue-700 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-white dark:bg-[#272B30] flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-2xl text-gray-400 group-hover:text-blue-500 transition-colors">add_photo_alternate</span>
                    </div>
                    <span class="text-xs font-bold text-[#6F767E] uppercase tracking-wider">{{ $label }}</span>
                </button>
            @endif
        @endif
    </div>
    @endif

    {{-- Modal --}}
    @if($showModal)
    <template x-teleport="body">
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        @keydown.escape.window="$wire.closeModal()">
        
        {{-- Modal Content --}}
        <div 
            x-data="{ 
                handleModalDragOver(e) {
                    if (e.dataTransfer && e.dataTransfer.types && Array.from(e.dataTransfer.types).includes('Files')) {
                        if ($wire.activeTab !== 'upload') {
                            $wire.set('activeTab', 'upload');
                        }
                    }
                }
            }"
            @dragover.prevent="handleModalDragOver($event)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-[#1A1A1A] rounded-3xl max-w-5xl w-full h-[680px] max-h-[92vh] flex flex-col shadow-xl"
            @click.away="$wire.closeModal()">
            
            {{-- Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-[#272B30]">
                <div class="flex items-center gap-4">
                    <h3 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $label }}</h3>
                    
                    {{-- Tabs --}}
                    <div class="flex bg-gray-100 dark:bg-[#272B30] rounded-lg p-1">
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'library')"
                            class="px-4 py-1.5 rounded-md text-xs font-bold transition-all {{ $activeTab === 'library' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white' }}">
                            Media Library
                        </button>
                        @can('media.upload')
                        <button 
                            type="button"
                            wire:click="$set('activeTab', 'upload')"
                            class="px-4 py-1.5 rounded-md text-xs font-bold transition-all {{ $activeTab === 'upload' ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white' }}">
                            Upload New
                        </button>
                        @endcan
                    </div>
                </div>
                
                <button 
                    type="button"
                    wire:click="closeModal" 
                    class="p-2 hover:bg-gray-100 dark:hover:bg-[#272B30] rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[#6F767E]">close</span>
                </button>
            </div>

            {{-- Flash Messages --}}
            @if(session()->has('picker-success'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('picker-success') }}</p>
            </div>
            @endif
            @if(session()->has('picker-error'))
            <div class="mx-6 mt-4 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('picker-error') }}</p>
            </div>
            @endif

            {{-- Content --}}
            <div class="flex-1 overflow-hidden p-6 flex flex-col min-h-0">
                @if($activeTab === 'library')
                    {{-- Library Tab --}}
                    <div class="h-full flex-1 flex flex-col min-h-0">
                        {{-- Search & Filters --}}
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-1 relative group">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6F767E] group-focus-within:text-[#2563EB] transition-colors text-lg">search</span>
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="Search media..." 
                                    class="w-full h-10 pl-11 pr-4 rounded-xl border-none bg-white dark:bg-[#1A1A1A] text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all placeholder:text-[#6F767E]">
                            </div>
                            <select 
                                wire:model.live="filterType"
                                class="px-4 py-2 rounded-xl border border-gray-300 dark:border-[#272B30] dark:bg-[#0B0B0B] text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="all">All Types</option>
                                <option value="images">Images</option>
                                <option value="documents">Documents</option>
                            </select>

                            @if($multiple)
                            <button 
                                type="button"
                                wire:click="toggleSelectAll"
                                class="px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] text-xs font-bold text-[#6F767E] hover:text-[#2563EB] hover:border-blue-500 transition-all flex items-center gap-1.5 shrink-0">
                                <span class="material-symbols-outlined text-sm">select_all</span>
                                <span>Toggle Select All</span>
                            </button>
                            @endif
                        </div>

                        {{-- Media Grid --}}
                        <div class="flex-1 overflow-y-auto">
                            @if($mediaItems->count() > 0)
                            <div class="grid grid-cols-4 md:grid-cols-6 gap-3">
                                @foreach($mediaItems as $item)
                                @php
                                    $isSelected = in_array($item->id, $selectedMediaIds, true);
                                @endphp
                                <div 
                                    wire:click="selectMedia({{ $item->id }})"
                                    class="relative aspect-square rounded-xl overflow-hidden border-2 cursor-pointer transition-all {{ $isSelected ? 'border-blue-500 ring-2 ring-blue-200 dark:ring-blue-800' : 'border-gray-200 dark:border-[#272B30] hover:border-blue-300 dark:hover:border-blue-700' }}">
                                    @if($item->isImage())
                                        <img 
                                            src="{{ $item->webp_url ?? $item->url }}" 
                                            alt="{{ $item->alt_text ?? $item->original_filename }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-[#272B30]">
                                            <span class="material-symbols-outlined text-3xl text-[#6F767E]">description</span>
                                        </div>
                                    @endif
                                    
                                    {{-- Selection Indicator --}}
                                    @if($isSelected)
                                    <div class="absolute top-2 right-2 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-md z-10">
                                        <span class="material-symbols-outlined text-sm">check</span>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            
                            {{-- Pagination --}}
                            <div class="mt-4">
                                {{ $mediaItems->links() }}
                            </div>
                            @else
                            <div class="flex flex-col items-center justify-center h-full text-center">
                                <span class="material-symbols-outlined text-5xl text-gray-300 dark:text-[#272B30] mb-3">perm_media</span>
                                <p class="text-sm text-[#6F767E]">No media found</p>
                                @can('media.upload')
                                <button 
                                    type="button"
                                    wire:click="$set('activeTab', 'upload')"
                                    class="mt-3 text-sm font-semibold text-[#2563EB] hover:underline">
                                    Upload new media
                                </button>
                                @endcan
                            </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Upload Tab --}}
                    <div class="h-full flex-1 flex flex-col min-h-0">
                        {{-- Drag & Drop Zone --}}
                        <div 
                            x-data="{ 
                                isDragging: false,
                                handleDrop(e) {
                                    this.isDragging = false;
                                    const files = e.dataTransfer.files;
                                    if (files.length > 0) {
                                        const input = $refs.pickerFileInput;
                                        const dataTransfer = new DataTransfer();
                                        for (let i = 0; i < files.length; i++) {
                                            dataTransfer.items.add(files[i]);
                                        }
                                        input.files = dataTransfer.files;
                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                }
                            }"
                            @drop.prevent="handleDrop($event)"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-[#272B30]'"
                            class="flex-1 border-2 border-dashed rounded-xl flex flex-col items-center justify-center transition-all p-6">
                            
                            @if(!empty($uploadFiles) || $uploadFile)
                                @php
                                    $stagedFiles = !empty($uploadFiles) ? (is_array($uploadFiles) ? $uploadFiles : [$uploadFiles]) : [$uploadFile];
                                @endphp
                                {{-- Preview uploaded files list --}}
                                <div class="w-full max-h-64 overflow-y-auto space-y-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-[#6F767E] uppercase tracking-wider">Staged Files ({{ count($stagedFiles) }})</span>
                                        <button 
                                            type="button"
                                            wire:click="clearUpload"
                                            class="text-xs font-bold text-red-600 hover:underline">
                                            Clear All
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        @foreach($stagedFiles as $f)
                                        <div class="p-3 bg-gray-50 dark:bg-[#272B30]/50 rounded-xl border border-gray-200 dark:border-[#272B30] flex items-center gap-3">
                                            <span class="material-symbols-outlined text-blue-600 text-xl">image</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] truncate">{{ $f->getClientOriginalName() }}</p>
                                                <p class="text-[10px] text-[#6F767E]">{{ number_format($f->getSize() / 1024, 1) }} KB</p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-[#272B30] flex items-center justify-center mb-4">
                                    <span class="material-symbols-outlined text-4xl text-[#6F767E]">cloud_upload</span>
                                </div>
                                <h4 class="text-lg font-semibold text-[#111827] dark:text-[#FCFCFC] mb-2">
                                    Drag and drop or click to browse multiple files
                                </h4>
                                <p class="text-sm text-[#6F767E] mb-4">
                                    Maximum file size: {{ config('media.max_file_size', 10240) / 1024 }}MB per file
                                </p>
                                <label class="cursor-pointer px-6 py-3 bg-[#2563EB] text-white rounded-xl font-semibold hover:bg-[#1D4ED8] transition-all flex items-center gap-2">
                                    <span class="material-symbols-outlined text-lg">add_photo_alternate</span>
                                    <span>Select File(s)</span>
                                    <input 
                                        x-ref="pickerFileInput"
                                        type="file" 
                                        wire:model="uploadFiles" 
                                        multiple
                                        accept="{{ $accept }}"
                                        class="hidden">
                                </label>
                            @endif
                        </div>

                        {{-- Upload Progress --}}
                        <div wire:loading wire:target="uploadFiles, uploadFile" class="mt-4 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-3">
                                <div class="animate-spin">
                                    <span class="material-symbols-outlined text-blue-600">refresh</span>
                                </div>
                                <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Processing file(s)...</span>
                            </div>
                        </div>

                        @error('uploadFiles.*')
                        <div class="mt-4 p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ $message }}</p>
                        </div>
                        @enderror
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-[#272B30]">
                <div>
                    @if($activeTab === 'library')
                        @if($multiple)
                            <p class="text-sm text-[#6F767E]">
                                Selected: <span class="font-bold text-blue-600">{{ count($selectedMediaIds) }} item(s)</span>
                            </p>
                        @elseif($selectedMedia)
                            <p class="text-sm text-[#6F767E]">
                                Selected: <span class="font-semibold text-[#111827] dark:text-[#FCFCFC]">{{ $selectedMedia['original_filename'] }}</span>
                            </p>
                        @endif
                    @endif
                </div>
                <div class="flex gap-3">
                    <button 
                        type="button"
                        wire:click="closeModal"
                        class="px-6 py-2.5 rounded-xl border border-gray-300 dark:border-[#272B30] text-sm font-semibold text-[#111827] dark:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">
                        Cancel
                    </button>
                    
                    @if($activeTab === 'library')
                        <button 
                            type="button"
                            wire:click="confirmSelection"
                            @if(($multiple && empty($selectedMediaIds)) || (!$multiple && !$selectedMediaId)) disabled @endif
                            class="px-6 py-2.5 rounded-xl bg-[#2563EB] text-white text-sm font-semibold hover:bg-[#1D4ED8] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            @if($multiple)
                                Insert Selected ({{ count($selectedMediaIds) }})
                            @else
                                {{ str_starts_with($label, 'Insert') ? $label : 'Select' }}
                            @endif
                        </button>
                    @else
                        <button 
                            type="button"
                            wire:click="uploadAndSelect"
                            wire:loading.attr="disabled"
                            @if(empty($uploadFiles) && !$uploadFile) disabled @endif
                            class="px-6 py-2.5 rounded-xl bg-[#2563EB] text-white text-sm font-semibold hover:bg-[#1D4ED8] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="uploadAndSelect">Upload & Select</span>
                            <span wire:loading wire:target="uploadAndSelect">Uploading...</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </template>
    @endif
</div>
