<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-[#111827] dark:text-[#FCFCFC]">Authors</h2>
            <p class="text-[#6F767E] mt-1">Manage post authors and their profiles</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Form -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200 dark:border-[#272B30] p-6 shadow-sm">
                <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] mb-4">
                    {{ $editingAuthor ? 'Edit Author' : 'Add New Author' }}
                </h3>
                
                <form wire:submit.prevent="{{ $editingAuthor ? 'update' : 'store' }}" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="form-label">Name</label>
                        <input type="text" wire:model.live="name" class="form-input-field" placeholder="e.g. Jane Doe">
                        <p class="mt-1 text-xs text-[#6F767E]">The author's full name or display name.</p>
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="form-label">Slug</label>
                        <input type="text" wire:model="slug" class="form-input-field" placeholder="e.g. jane-doe">
                        <p class="mt-1 text-xs text-[#6F767E]">The "slug" is the URL-friendly version of the name.</p>
                        @error('slug') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" wire:model="email" class="form-input-field" placeholder="e.g. jane@example.com">
                        <p class="mt-1 text-xs text-[#6F767E]">Optional email address for this author.</p>
                        @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label class="form-label">Biography / Description</label>
                        <textarea wire:model="bio" rows="4" class="form-input-field" placeholder="Brief biography..."></textarea>
                        <p class="mt-1 text-xs text-[#6F767E]">Brief description of the author's background.</p>
                        @error('bio') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <button type="submit" class="px-4 py-2 bg-[#2563EB] text-white rounded-lg font-semibold hover:bg-[#1D4ED8] transition-all text-sm">
                            {{ $editingAuthor ? 'Update Author' : 'Add New Author' }}
                        </button>
                        @if($editingAuthor)
                        <button type="button" wire:click="cancelEdit" class="px-4 py-2 bg-gray-100 dark:bg-[#272B30] text-[#6F767E] rounded-lg font-semibold hover:bg-gray-200 dark:hover:bg-[#374151] transition-all text-sm">
                            Cancel
                        </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: List -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B]">
                            <th class="px-6 py-4 text-xs font-bold text-[#6F767E] uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#6F767E] uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#6F767E] uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-xs font-bold text-[#6F767E] uppercase tracking-wider text-right">Posts</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-[#272B30]">
                        @forelse($authors as $author)
                        <tr class="group hover:bg-gray-50 dark:hover:bg-[#272B30]/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 font-bold">
                                        {{ strtoupper(substr($author->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#111827] dark:text-[#FCFCFC] group-hover:text-blue-600 transition-colors">
                                            {{ $author->name }}
                                        </div>
                                        
                                        <!-- Hover Actions -->
                                        <div class="flex items-center gap-2 mt-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                            <button wire:click="edit({{ $author->id }})" class="text-[11px] font-bold text-blue-600 hover:text-blue-700 uppercase tracking-tighter">Edit</button>
                                            <span class="text-gray-300 dark:text-gray-600">|</span>
                                            <button wire:click="delete({{ $author->id }})" wire:confirm="Are you sure you want to delete this author? This will delete all posts associated with this author!" class="text-[11px] font-bold text-red-500 hover:text-red-700 uppercase tracking-tighter">Delete</button>
                                        </div>

                                        @if($author->bio)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs mt-1">{{ $author->bio }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#6F767E]">
                                {{ $author->slug }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#6F767E]">
                                {{ $author->email ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center justify-center px-2 py-1 rounded-md bg-gray-100 dark:bg-[#272B30] font-bold text-xs">
                                    {{ $author->posts_count }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-[#6F767E]">
                                No authors found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if($authors->hasPages())
                <div class="px-8 py-6 border-t border-gray-100 dark:border-[#272B30] flex items-center justify-between">
                    <p class="text-sm font-medium text-[#6F767E]">
                        Showing {{ $authors->firstItem() }} to {{ $authors->lastItem() }} of {{ $authors->total() }} authors
                    </p>
                    <div class="flex items-center gap-2">
                        @if($authors->onFirstPage())
                        <button disabled
                            class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed">
                            <span class="material-symbols-outlined text-xl">chevron_left</span>
                        </button>
                        @else
                        <button wire:click="previousPage"
                            class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                            <span class="material-symbols-outlined text-xl">chevron_left</span>
                        </button>
                        @endif

                        @foreach($authors->getUrlRange(max(1, $authors->currentPage() - 2), min($authors->lastPage(), $authors->currentPage() + 2)) as $page => $url)
                            @if($page == $authors->currentPage())
                            <button class="h-10 w-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-blue-500/20">{{ $page }}</button>
                            @else
                            <button wire:click="gotoPage({{ $page }})" class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] flex items-center justify-center text-sm font-bold text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">{{ $page }}</button>
                            @endif
                        @endforeach

                        @if($authors->hasMorePages())
                        <button wire:click="nextPage"
                            class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                            <span class="material-symbols-outlined text-xl">chevron_right</span>
                        </button>
                        @else
                        <button disabled
                            class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed">
                            <span class="material-symbols-outlined text-xl">chevron_right</span>
                        </button>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
