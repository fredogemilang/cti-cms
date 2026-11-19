<div class="space-y-6">
    {{-- Header & Action Button --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Icon Libraries Management</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Manage active icon sets and upload custom SVG icon packs for your CMS.</p>
        </div>
        <button type="button" wire:click="openUploadModal" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-sm">upload</span>
            <span>Upload Custom Library</span>
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-700 dark:text-emerald-300 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Libraries Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($libraries as $prefix => $lib)
            <div class="p-6 bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm flex flex-col justify-between space-y-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800 flex items-center justify-center">
                            <span class="material-symbols-outlined">category</span>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $lib['name'] }}</h3>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                                prefix: {{ $prefix }}
                            </span>
                        </div>
                    </div>
                    <div>
                        @if(!empty($lib['is_system']))
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                Built-in System
                            </span>
                        @else
                            <button type="button" wire:click="deleteLibrary('{{ $prefix }}')" wire:confirm="Are you sure you want to delete this custom icon library?" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg transition-colors" title="Delete Library">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Icons Preview Grid -->
                <div class="p-3 bg-gray-50 dark:bg-[#1A1A1A] rounded-xl border border-gray-100 dark:border-gray-800">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 flex items-center justify-between">
                        <span>Icons Available</span>
                        <span>{{ count($lib['icons']) }} icons</span>
                    </div>
                    <div class="grid grid-cols-6 gap-2">
                        @foreach(array_slice($lib['icons'], 0, 12, true) as $iconName => $iconData)
                            <div class="w-8 h-8 rounded-lg bg-white dark:bg-[#0B0B0B] border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300" title="{{ $iconName }}">
                                {!! render_icon("{$prefix}:{$iconName}", "w-5 h-5") !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Upload Modal --}}
    @if($showUploadModal)
        <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-[#111827] border border-gray-200 dark:border-gray-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-500">upload</span>
                        <span>Upload Custom Icon Library</span>
                    </h3>
                    <button type="button" wire:click="closeUploadModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Library Name</label>
                        <input type="text" wire:model="libraryName" placeholder="e.g. Company Brand Icons" class="w-full px-3 py-2 bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white">
                        @error('libraryName') <span class="text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Library Prefix Slug</label>
                        <input type="text" wire:model="libraryPrefix" placeholder="e.g. brand-icons" class="w-full px-3 py-2 bg-gray-50 dark:bg-[#1A1A1A] border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white font-mono">
                        @error('libraryPrefix') <span class="text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 dark:text-gray-300 mb-1">Icon Pack JSON File</label>
                        <input type="file" wire:model="uploadFile" accept=".json" class="w-full text-gray-500 text-xs">
                        <p class="text-[10px] text-gray-400 mt-1">JSON file containing an array of SVG inner paths (key: SVG path string).</p>
                        @error('uploadFile') <span class="text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <button type="button" wire:click="closeUploadModal" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold">Cancel</button>
                    <button type="button" wire:click="uploadCustomLibrary" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold">Upload Library</button>
                </div>
            </div>
        </div>
    @endif
</div>
