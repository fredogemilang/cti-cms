<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#111827] dark:text-[#FCFCFC] flex items-center gap-2">
                <span class="material-symbols-outlined text-red-600 text-3xl">smart_display</span>
                <span>YouTube Videos (See Kouventa in Action)</span>
            </h1>
            <p class="text-sm text-[#6F767E] mt-1">Manage YouTube video cards, categories, and ordering displayed in the interactive slider on the homepage.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ setting('social_youtube', 'https://www.youtube.com/@heliosinformatikanusantara4415') }}" target="_blank" rel="noopener noreferrer" class="h-10 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-[#6F767E] hover:text-red-600 dark:hover:text-red-500 hover:bg-gray-50 dark:hover:bg-[#272B30] flex items-center gap-2 text-xs font-bold transition-all shadow-sm">
                <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" class="shrink-0"><rect x="2" y="5" width="20" height="14" rx="4" fill="#FF0033"></rect><path d="M10 9v6l5-3Z" fill="#FFFFFF"></path></svg>
                <span>Channel Link</span>
            </a>

            <a href="{{ url('/#videos') }}" target="_blank" class="h-10 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30] flex items-center gap-2 text-xs font-bold transition-all shadow-sm">
                <span class="material-symbols-outlined text-sm">open_in_new</span>
                <span>View on Homepage</span>
            </a>

            <button type="button" wire:click="openCreateModal" class="h-10 px-4 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 flex items-center gap-2 text-xs font-bold transition-all shadow-sm">
                <span class="material-symbols-outlined text-sm">add</span>
                <span>Add Video</span>
            </button>
        </div>
    </div>

    {{-- Videos Table --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200 dark:border-[#272B30] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 text-[#6F767E] font-bold text-xs uppercase tracking-wider">
                        <th class="py-4 px-5 w-24">Order</th>
                        <th class="py-4 px-5 w-44">Thumbnail</th>
                        <th class="py-4 px-5">Title &amp; Category</th>
                        <th class="py-4 px-5">YouTube Link / ID</th>
                        <th class="py-4 px-5">Status</th>
                        <th class="py-4 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#272B30]">
                    @forelse($videos as $idx => $video)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-[#272B30]/30 transition-colors" wire:key="video-row-{{ $video->id }}">
                            {{-- Order Controls --}}
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                        wire:click="moveUp({{ $video->id }})"
                                        @if($loop->first) disabled @endif
                                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] disabled:opacity-30 disabled:pointer-events-none transition-colors"
                                        title="Move up">
                                        <span class="material-symbols-outlined text-base">keyboard_arrow_up</span>
                                    </button>
                                    <button type="button"
                                        wire:click="moveDown({{ $video->id }})"
                                        @if($loop->last) disabled @endif
                                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] disabled:opacity-30 disabled:pointer-events-none transition-colors"
                                        title="Move down">
                                        <span class="material-symbols-outlined text-base">keyboard_arrow_down</span>
                                    </button>
                                </div>
                            </td>

                            {{-- Video Thumbnail Preview --}}
                            <td class="py-4 px-5">
                                <a href="{{ $video->getWatchUrl() }}" target="_blank" rel="noopener noreferrer" class="group relative block w-36 aspect-video rounded-xl overflow-hidden bg-[#071C33] border border-gray-200 dark:border-gray-800 shadow-sm shrink-0">
                                    <img src="{{ $video->getThumbnailUrl() }}"
                                         alt="{{ $video->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         loading="lazy">
                                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                                        <span class="w-8 h-6 rounded-md bg-[#FF0033] text-white flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="#FFFFFF"><path d="M8 5v14l11-7Z"></path></svg>
                                        </span>
                                    </div>
                                </a>
                            </td>

                            {{-- Title & Category --}}
                            <td class="py-4 px-5">
                                <div class="font-bold text-[#111827] dark:text-[#FCFCFC] text-sm leading-snug line-clamp-2 max-w-lg">
                                    {{ $video->title }}
                                </div>
                                <div class="mt-1.5 flex items-center gap-2">
                                    @php
                                        $catColor = match($video->category) {
                                            'Artificial Intelligence' => 'bg-[#EAF7EF] text-[#0B5E2C] border-[#BDE8CC]',
                                            'Customer Engagement' => 'bg-[#E6F0FA] text-[#0F4C81] border-[#C3DCF5]',
                                            default => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/20 dark:text-purple-300',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $catColor }}">
                                        {{ $video->category }}
                                    </span>
                                    @if($video->thumbnail)
                                        <span class="inline-flex items-center gap-1 text-[11px] text-[#6F767E]" title="Custom thumbnail applied">
                                            <span class="material-symbols-outlined text-xs text-blue-500">image</span>
                                            <span>Custom Thumb</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- YouTube Link / ID --}}
                            <td class="py-4 px-5">
                                <div class="flex flex-col gap-1">
                                    <a href="{{ $video->getWatchUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-mono font-semibold text-[#2563EB] hover:underline">
                                        <span class="material-symbols-outlined text-sm">link</span>
                                        <span class="truncate max-w-[180px]">{{ $video->youtube_id }}</span>
                                        <span class="material-symbols-outlined text-xs">open_in_new</span>
                                    </a>
                                    <span class="text-[10px] text-[#6F767E] font-mono">ID: {{ $video->youtube_id }}</span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-5">
                                <button type="button" wire:click="toggleActive({{ $video->id }})" class="focus:outline-none">
                                    @if($video->is_active)
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
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button"
                                        wire:click="openEditModal({{ $video->id }})"
                                        class="h-8 px-2.5 rounded-lg border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] inline-flex items-center gap-1 text-xs font-bold transition-colors">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Edit</span>
                                    </button>

                                    <button type="button"
                                        wire:click="delete({{ $video->id }})"
                                        wire:confirm="Are you sure you want to remove '{{ $video->title }}' from the video slider?"
                                        class="h-8 w-8 rounded-lg border border-gray-200 dark:border-[#272B30] text-red-500 hover:text-white hover:bg-red-500 dark:hover:bg-red-600 flex items-center justify-center transition-colors"
                                        title="Delete video">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[#6F767E]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-4xl text-gray-400">smart_display</span>
                                    <p class="font-bold text-sm text-[#111827] dark:text-[#FCFCFC]">No YouTube videos configured yet</p>
                                    <p class="text-xs">Click "Add Video" above to add your first YouTube video card.</p>
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
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative w-full max-w-xl bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-2xl p-6 sm:p-7 space-y-5 animate-in fade-in zoom-in-95 duration-200"
                 @keydown.escape.window="$wire.closeModal()">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
                    <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] flex items-center gap-2">
                        <span class="material-symbols-outlined text-red-600">smart_display</span>
                        <span>{{ $editingId ? 'Edit YouTube Video' : 'Add New YouTube Video' }}</span>
                    </h3>
                    <button type="button" wire:click="closeModal" class="h-8 w-8 rounded-lg text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    {{-- Video Title --}}
                    <div>
                        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1">
                            Video Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               wire:model.defer="title"
                               placeholder="e.g. Tim Finance Masih Perlu Workflow Manual di Era Agentic AI?"
                               class="w-full h-10 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#111315] text-[#111827] dark:text-[#FCFCFC] text-sm font-semibold focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                        @error('title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- YouTube URL or Video ID --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">
                                YouTube URL or Video ID <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[10px] text-[#6F767E]">Supports youtube.com, youtu.be, shorts, or 11-char ID</span>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-red-500">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M10 15l5-3-5-3v6Z"></path></svg>
                            </span>
                            <input type="text"
                                   wire:model.live.debounce.300ms="youtube_url"
                                   placeholder="https://www.youtube.com/watch?v=oOY1BnbQPUc or oOY1BnbQPUc"
                                   class="w-full h-10 pl-9 pr-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#111315] text-[#111827] dark:text-[#FCFCFC] text-xs font-mono focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                        </div>
                        @error('youtube_url') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        @error('youtube_id') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Live Thumbnail & YouTube ID Detection Preview --}}
                    @if($youtube_id)
                        <div class="p-3.5 rounded-xl border border-green-200 dark:border-green-900/40 bg-green-50/50 dark:bg-green-950/20 flex items-center gap-4">
                            <div class="relative w-32 aspect-video rounded-lg overflow-hidden bg-black border border-green-300 dark:border-green-800 shrink-0">
                                <img src="https://i.ytimg.com/vi/{{ $youtube_id }}/hqdefault.jpg"
                                     alt="Preview Thumbnail"
                                     class="w-full h-full object-cover">
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="w-6 h-5 rounded bg-[#FF0033] flex items-center justify-center text-white">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="#FFFFFF"><path d="M8 5v14l11-7Z"></path></svg>
                                    </span>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-green-100 dark:bg-green-900/60 text-green-800 dark:text-green-300 text-xs font-bold font-mono">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    <span>ID Detected: {{ $youtube_id }}</span>
                                </div>
                                <p class="text-[11px] text-[#6F767E] mt-1">Live thumbnail detected automatically from YouTube servers.</p>
                            </div>
                        </div>
                    @endif

                    {{-- Category Selection --}}
                    <div>
                        <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC] mb-1">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-gray-200 dark:border-[#272B30] cursor-pointer hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition-colors {{ $category === 'Artificial Intelligence' ? 'ring-2 ring-[#2563EB] bg-blue-50/40 dark:bg-blue-900/10' : '' }}">
                                <input type="radio" wire:model.live="category" value="Artificial Intelligence" class="sr-only">
                                <span class="w-2.5 h-2.5 rounded-full border border-gray-400 {{ $category === 'Artificial Intelligence' ? 'bg-[#2563EB] border-[#2563EB]' : '' }}"></span>
                                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Artificial Intelligence</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-gray-200 dark:border-[#272B30] cursor-pointer hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition-colors {{ $category === 'Customer Engagement' ? 'ring-2 ring-[#2563EB] bg-blue-50/40 dark:bg-blue-900/10' : '' }}">
                                <input type="radio" wire:model.live="category" value="Customer Engagement" class="sr-only">
                                <span class="w-2.5 h-2.5 rounded-full border border-gray-400 {{ $category === 'Customer Engagement' ? 'bg-[#2563EB] border-[#2563EB]' : '' }}"></span>
                                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Customer Engagement</span>
                            </label>

                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-gray-200 dark:border-[#272B30] cursor-pointer hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition-colors {{ $category === 'custom' ? 'ring-2 ring-[#2563EB] bg-blue-50/40 dark:bg-blue-900/10' : '' }}">
                                <input type="radio" wire:model.live="category" value="custom" class="sr-only">
                                <span class="w-2.5 h-2.5 rounded-full border border-gray-400 {{ $category === 'custom' ? 'bg-[#2563EB] border-[#2563EB]' : '' }}"></span>
                                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Custom Category...</span>
                            </label>
                        </div>

                        @if($category === 'custom')
                            <div class="mt-2.5">
                                <input type="text"
                                       wire:model.defer="custom_category"
                                       placeholder="Enter custom category name (e.g. Case Study, Product Tour)"
                                       class="w-full h-10 px-3.5 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#111315] text-[#111827] dark:text-[#FCFCFC] text-xs font-semibold focus:border-[#2563EB] focus:ring-1 focus:ring-[#2563EB]">
                            </div>
                        @endif
                    </div>

                    {{-- Custom Thumbnail (Optional) --}}
                    <div class="space-y-2 pt-1 border-t border-gray-100 dark:border-[#272B30]">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">
                                Custom Thumbnail (Optional)
                            </label>
                            <span class="text-[10px] text-[#6F767E]">Leave empty to use automatic YouTube cover</span>
                        </div>

                        @if($thumbnail)
                            <div class="p-3 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B] flex items-center justify-between gap-4">
                                <div class="w-28 aspect-video rounded-lg overflow-hidden bg-black shrink-0 border border-gray-300 dark:border-gray-800">
                                    <img src="{{ resolve_block_asset($thumbnail) }}"
                                         alt="Custom Thumbnail"
                                         class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-mono text-[#6F767E] truncate">{{ $thumbnail }}</p>
                                    <button type="button" wire:click="clearThumbnail" class="mt-1 text-xs font-bold text-red-500 hover:text-red-600 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                        <span>Remove Custom Image</span>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="pt-0.5">
                            <livewire:admin.media-picker
                                :field="'video_thumbnail'"
                                :value="$thumbnail"
                                :label="'Select or Upload Custom Thumbnail'"
                                :compact="true"
                                :key="'video-thumb-picker-' . ($editingId ?? 'new') . '-' . md5($thumbnail)"
                            />
                        </div>
                    </div>

                    {{-- Active Toggle --}}
                    <div class="pt-2 flex items-center justify-between border-t border-gray-100 dark:border-[#272B30]">
                        <div>
                            <div class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Visible in Homepage Slider</div>
                            <div class="text-[11px] text-[#6F767E]">Toggle off to hide without deleting from the catalog.</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.defer="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-[#272B30] peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2563EB]"></div>
                        </label>
                    </div>

                    {{-- Modal Actions --}}
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100 dark:border-[#272B30]">
                        <button type="button" wire:click="closeModal" class="h-10 px-4 rounded-xl border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] text-xs font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 text-xs font-bold transition-all shadow-sm">
                            {{ $editingId ? 'Save Changes' : 'Add Video' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
