<div>
    <!-- Filters & Search -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-3">
            <div class="relative group">
                <input
                    wire:model.live.debounce.300ms="search"
                    class="h-12 w-full md:w-[320px] rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-12 pr-4 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all placeholder:text-[#6F767E]"
                    placeholder="Search users by name or email..." type="text" />
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6F767E] group-focus-within:text-[#2563EB] transition-colors">search</span>
                
                <!-- Loading indicator for search -->
                <div wire:loading wire:target="search" class="absolute right-4 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-5 w-5 text-[#2563EB]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Role Filter -->
            <select
                wire:model.live="roleFilter"
                class="h-12 rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-4 pr-10 text-sm font-bold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all cursor-pointer">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>

            <!-- Status Filter -->
            <select
                wire:model.live="statusFilter"
                class="h-12 rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-4 pr-10 text-sm font-bold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all cursor-pointer">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            @if($search || $roleFilter || $statusFilter)
            <button
                wire:click="clearFilters"
                class="h-12 px-4 rounded-xl bg-gray-100 dark:bg-[#272B30] text-[#6F767E] font-medium text-sm hover:bg-gray-200 dark:hover:bg-[#333] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">close</span>
                Clear
            </button>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-[#6F767E]">Display:</span>
            <select
                wire:model.live="perPage"
                class="h-12 rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-4 pr-10 text-sm font-bold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all cursor-pointer">
                <option value="10">10 Rows</option>
                <option value="20">20 Rows</option>
                <option value="50">50 Rows</option>
                <option value="100">100 Rows</option>
            </select>
            @can('users.create')
            <a href="{{ route('admin.users.create') }}" wire:navigate
                class="px-6 py-3 font-bold rounded-2xl transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-0.5 inline-flex items-center justify-center bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white text-sm">
                Add New User
            </a>
            @endcan
        </div>
    </div>

    <!-- Users Table -->
    <x-admin.ui.table>
        <x-slot:thead>
            <x-admin.ui.table-header class="px-8 w-10">
                <input
                    wire:model.live="selectAll"
                    class="custom-checkbox"
                    type="checkbox" />
            </x-admin.ui.table-header>
            <x-admin.ui.table-header sortBy="name" :field="$sortField" :direction="$sortDirection">
                User
            </x-admin.ui.table-header>
            <x-admin.ui.table-header>Roles</x-admin.ui.table-header>
            <x-admin.ui.table-header>Status</x-admin.ui.table-header>
            <x-admin.ui.table-header sortBy="last_login_at" :field="$sortField" :direction="$sortDirection">
                Last Login
            </x-admin.ui.table-header>
            <x-admin.ui.table-header align="right" class="px-8">Actions</x-admin.ui.table-header>
        </x-slot:thead>

        @forelse($users as $user)
        @php
            $colors = ['blue', 'purple', 'orange', 'green', 'pink', 'cyan', 'amber', 'indigo'];
            $colorIndex = crc32($user->email) % count($colors);
            $avatarColor = $colors[$colorIndex];
            $colorClasses = [
                'blue' => 'bg-blue-500/10 text-blue-500',
                'purple' => 'bg-purple-500/10 text-purple-500',
                'orange' => 'bg-orange-500/10 text-orange-500',
                'green' => 'bg-green-500/10 text-green-500',
                'pink' => 'bg-pink-500/10 text-pink-500',
                'cyan' => 'bg-cyan-500/10 text-cyan-500',
                'amber' => 'bg-amber-500/10 text-amber-500',
                'indigo' => 'bg-indigo-500/10 text-indigo-500',
            ];
        @endphp
        <x-admin.ui.table-row wire:key="user-{{ $user->id }}">
            <x-admin.ui.table-cell class="px-8">
                <input
                    wire:model.live="selectedUsers"
                    value="{{ $user->id }}"
                    class="custom-checkbox"
                    type="checkbox" />
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell>
                <div class="flex items-center gap-4">
                    <div class="h-11 w-11 rounded-2xl {{ $colorClasses[$avatarColor] }} flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-[15px] font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $user->name }}</p>
                        <p class="text-xs text-[#6F767E]">{{ $user->email }}</p>
                    </div>
                </div>
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell>
                @if($user->roles->isNotEmpty())
                    <div class="flex flex-wrap gap-1">
                        @foreach($user->roles as $role)
                            @if($role->is_super_admin)
                                <span class="inline-flex items-center rounded-lg bg-red-100 dark:bg-red-900/30 px-2.5 py-1 text-[11px] font-bold text-red-700 dark:text-red-400 uppercase tracking-wider">{{ $role->name }}</span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-gray-100 dark:bg-[#272B30] px-2.5 py-1 text-[11px] font-bold text-[#6F767E] dark:text-[#FCFCFC] uppercase tracking-wider">{{ $role->name }}</span>
                            @endif
                        @endforeach
                    </div>
                @else
                    <span class="inline-flex items-center rounded-lg bg-gray-100 dark:bg-[#272B30] px-2.5 py-1 text-[11px] font-bold text-[#6F767E] uppercase tracking-wider">No Role</span>
                @endif
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell>
                @if($user->is_active)
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#3F8C5826] px-2.5 py-1 text-[11px] font-bold text-[#83BF6E] uppercase tracking-wider">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#83BF6E]"></span>
                    Active
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 dark:bg-red-900/30 px-2.5 py-1 text-[11px] font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                    Inactive
                </span>
                @endif
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell>
                <p class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC]">
                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                </p>
            </x-admin.ui.table-cell>
            <x-admin.ui.table-cell align="right" class="px-8">
                <div class="flex gap-2 items-center justify-end">
                    @can('users.view')
                    <a href="{{ route('admin.users.show', $user) }}" wire:navigate
                        class="w-9 h-9 p-2 rounded-xl text-cyan-600 dark:text-cyan-400 hover:bg-cyan-50 dark:hover:bg-cyan-950/20 flex items-center justify-center transition-colors"
                        data-tooltip="View User">
                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                    </a>
                    @endcan
                    
                    @can('users.edit')
                    <a href="{{ route('admin.users.edit', $user) }}" wire:navigate
                        class="w-9 h-9 p-2 rounded-xl text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/20 flex items-center justify-center transition-colors"
                        data-tooltip="Edit User">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                    </a>
                    @endcan
                    
                    @can('users.delete')
                    @if($user->id !== auth()->id())
                    <button 
                        x-data
                        @click="$dispatch('open-delete-modal', { userId: {{ $user->id }}, userName: '{{ addslashes($user->name) }}' })"
                        class="w-9 h-9 p-2 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 flex items-center justify-center transition-colors"
                        data-tooltip="Delete User">
                        <span class="material-symbols-outlined text-[20px]">delete</span>
                    </button>
                    @endif
                    @endcan
                </div>
            </x-admin.ui.table-cell>
        </x-admin.ui.table-row>
        @empty
        <tr>
            <td colspan="6" class="px-8 py-16 text-center">
                <div class="flex flex-col items-center">
                    <div class="h-16 w-16 rounded-full bg-gray-100 dark:bg-[#272B30] flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-3xl text-[#6F767E]">group</span>
                    </div>
                    <p class="text-[#6F767E] font-medium">
                        @if($search || $roleFilter || $statusFilter)
                            No users found matching your criteria
                        @else
                            No users found
                        @endif
                    </p>
                    @if($search || $roleFilter || $statusFilter)
                    <button wire:click="clearFilters" class="mt-3 text-sm text-[#2563EB] hover:underline">Clear filters</button>
                    @endif
                </div>
            </td>
        </tr>
        @endforelse
    </x-admin.ui.table>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="mt-6 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-6 flex items-center justify-between shadow-sm">
        <p class="text-sm font-medium text-[#6F767E]">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
        </p>
        <div class="flex items-center gap-2">
            @if($users->onFirstPage())
            <button disabled
                class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed border border-gray-200 dark:border-[#272B30]">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            @else
            <button wire:click="previousPage"
                class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            @endif

            @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                @if($page == $users->currentPage())
                <button class="h-10 w-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center text-sm font-bold shadow-lg shadow-blue-500/20">{{ $page }}</button>
                @else
                <button wire:click="gotoPage({{ $page }})" class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-sm font-bold text-[#6F767E] hover:bg-gray-50 dark:hover:bg-[#272B30] transition-all">{{ $page }}</button>
                @endif
            @endforeach

            @if($users->hasMorePages())
            <button wire:click="nextPage"
                class="h-10 w-10 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] flex items-center justify-center text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
            @else
            <button disabled
                class="h-10 w-10 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center text-[#6F767E] opacity-50 cursor-not-allowed border border-gray-200 dark:border-[#272B30]">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
            @endif
        </div>
    </div>
    @endif

    <!-- Bulk Action Bar (Floating) -->
    @if(count($selectedUsers) > 0)
    <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 transform transition-all duration-300"
        x-data="{ showRoleModal: false }"
        x-init="$el.classList.remove('translate-y-24', 'opacity-0')"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-24 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100">
        <div class="bg-[#2563EB] border border-[#2563EB] rounded-2xl shadow-2xl px-6 py-3 flex items-center gap-6">
            <div class="flex items-center gap-3 border-r border-blue-400/30 pr-6">
                <span class="bg-white text-[#2563EB] text-xs font-bold px-2.5 py-1 rounded-full min-w-[24px] text-center">{{ count($selectedUsers) }}</span>
                <span class="text-sm font-semibold text-white">Selected</span>
            </div>
            <div class="flex items-center gap-4">
                <button 
                    @click="showRoleModal = true"
                    class="flex items-center gap-2 text-sm font-bold text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                    Change Role
                </button>
                <button 
                    @click="$dispatch('open-activate-modal')"
                    class="flex items-center gap-2 text-sm font-bold text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    Activate
                </button>
                <button 
                    @click="$dispatch('open-deactivate-modal')"
                    class="flex items-center gap-2 text-sm font-bold text-white/70 hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[20px]">block</span>
                    Deactivate
                </button>
                <button 
                    x-data
                    @click="$dispatch('open-bulk-delete-modal')"
                    class="flex items-center gap-2 text-sm font-bold text-white/70 hover:text-[#FF6A55] transition-colors">
                    <span class="material-symbols-outlined text-[20px]">delete</span>
                    Delete
                </button>
            </div>
            <button wire:click="clearSelection" class="ml-2 w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/10 text-white/70 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        
        <!-- Change Role Modal (inline) -->
        <div 
            x-show="showRoleModal"
            x-cloak
            @click.away="showRoleModal = false"
            class="absolute bottom-full left-1/2 -translate-x-1/2 mb-4 w-72 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-2xl shadow-2xl p-4">
            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] mb-3">Change Role To</h4>
            <select 
                wire:model="bulkRoleId"
                class="w-full h-11 rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B] px-4 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] mb-3">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <x-admin.ui.button type="button" variant="secondary" @click="showRoleModal = false" class="flex-1 !h-10 !py-0 !rounded-xl text-sm">
                    Cancel
                </x-admin.ui.button>
                <x-admin.ui.button 
                    type="button"
                    variant="primary"
                    wire:click="changeRoleSelected($wire.bulkRoleId)"
                    @click="showRoleModal = false"
                    class="flex-1 !h-10 !py-0 !rounded-xl text-sm">
                    Apply
                </x-admin.ui.button>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Modal -->
    <div 
        x-data="{ 
            show: false, 
            userId: null, 
            userName: '',
            bulk: false
        }"
        @open-delete-modal.window="show = true; userId = $event.detail.userId; userName = $event.detail.userName; bulk = false"
        @open-bulk-delete-modal.window="show = true; bulk = true"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 dark:bg-[#0B0B0B]/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div 
            @click.outside="show = false"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-[440px] bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-[#272B30] rounded-3xl shadow-2xl p-8">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-[#FF6A55]/10 flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-[#FF6A55] text-3xl">delete_forever</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-[#FCFCFC] mb-3">Delete User</h3>
                <p class="text-gray-500 dark:text-[#6F767E] leading-relaxed mb-8">
                    <template x-if="bulk">
                        <span>Are you sure you want to delete <span class="font-bold">{{ count($selectedUsers) }}</span> selected user(s)? This action cannot be undone.</span>
                    </template>
                    <template x-if="!bulk">
                        <span>Are you sure you want to delete "<span class="font-bold" x-text="userName"></span>"? This action cannot be undone.</span>
                    </template>
                </p>
                <div class="flex items-center gap-3 w-full">
                    <x-admin.ui.button type="button" variant="secondary" @click="show = false" class="flex-1">
                        Cancel
                    </x-admin.ui.button>
                    <x-admin.ui.button 
                        type="button"
                        variant="danger"
                        @click="
                            if (bulk) {
                                $wire.deleteSelected();
                            } else {
                                $wire.deleteUser(userId);
                            }
                            show = false;
                        "
                        class="flex-1">
                        Delete
                    </x-admin.ui.button>
                </div>
            </div>
        </div>
    </div>

    <!-- Activate Modal -->
    <div 
        x-data="{ show: false }"
        @open-activate-modal.window="show = true"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 dark:bg-[#0B0B0B]/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div 
            @click.outside="show = false"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-[440px] bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-[#272B30] rounded-3xl shadow-2xl p-8">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-[#83BF6E]/10 flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-[#83BF6E] text-3xl">check_circle</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-[#FCFCFC] mb-3">Activate Users</h3>
                <p class="text-gray-500 dark:text-[#6F767E] leading-relaxed mb-8">
                    Are you sure you want to activate <span class="font-bold">{{ count($selectedUsers) }}</span> selected user(s)? They will be able to access the system.
                </p>
                <div class="flex items-center gap-3 w-full">
                    <x-admin.ui.button type="button" variant="secondary" @click="show = false" class="flex-1">
                        Cancel
                    </x-admin.ui.button>
                    <x-admin.ui.button 
                        type="button"
                        @click="$wire.activateSelected(); show = false;"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold rounded-2xl py-3 px-6 shadow-sm transition-all">
                        Activate
                    </x-admin.ui.button>
                </div>
            </div>
        </div>
    </div>

    <!-- Deactivate Modal -->
    <div 
        x-data="{ show: false }"
        @open-deactivate-modal.window="show = true"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/50 dark:bg-[#0B0B0B]/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div 
            @click.outside="show = false"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-[440px] bg-white dark:bg-[#1A1A1A] border border-gray-100 dark:border-[#272B30] rounded-3xl shadow-2xl p-8">
            <div class="flex flex-col items-center text-center">
                <div class="h-16 w-16 rounded-full bg-amber-500/10 flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-amber-500 text-3xl">block</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-[#FCFCFC] mb-3">Deactivate Users</h3>
                <p class="text-gray-500 dark:text-[#6F767E] leading-relaxed mb-8">
                    Are you sure you want to deactivate <span class="font-bold">{{ count($selectedUsers) }}</span> selected user(s)? They will no longer be able to access the system.
                </p>
                <div class="flex items-center gap-3 w-full">
                    <x-admin.ui.button type="button" variant="secondary" @click="show = false" class="flex-1">
                        Cancel
                    </x-admin.ui.button>
                    <x-admin.ui.button 
                        type="button"
                        @click="$wire.deactivateSelected(); show = false;"
                        class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-2xl py-3 px-6 shadow-sm transition-all">
                        Deactivate
                    </x-admin.ui.button>
                </div>
            </div>
        </div>
    </div>
</div>

