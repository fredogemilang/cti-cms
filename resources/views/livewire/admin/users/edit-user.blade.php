<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <div class="lg:col-span-8 space-y-8">
        <x-admin.ui.card padding="p-10">
            <div class="mb-10">
                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">User Information</h2>
                <p class="text-sm text-[#6F767E] mt-1">Manage user's basic account details and identity.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <x-admin.ui.input 
                    name="name" 
                    label="Full Name" 
                    wire:model.blur="name" 
                />
                <x-admin.ui.input 
                    name="username" 
                    label="Username" 
                    wire:model.blur="username" 
                />
                <div class="md:col-span-2">
                    <x-admin.ui.input 
                        name="email" 
                        type="email"
                        label="Email Address" 
                        wire:model.blur="email" 
                    />
                </div>
            </div>
            <div class="mt-8 space-y-1">
                <label class="block text-sm font-bold text-gray-700 dark:text-[#FCFCFC] mb-2">Bio</label>
                <textarea wire:model.blur="bio" class="w-full rounded-2xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#0B0B0B] text-gray-900 dark:text-[#FCFCFC] focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900/30 transition py-3 px-4 placeholder:text-[#6F767E] resize-none @error('bio') border-red-500 dark:border-red-500 @enderror"
                    placeholder="Write a short bio..."
                    rows="5"></textarea>
                @error('bio') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                <p class="text-xs text-[#6F767E] mt-2">Brief description for the user. Maximum 200 characters.</p>
            </div>
        </x-admin.ui.card>

        <x-admin.ui.card padding="p-10">
            <div class="mb-10">
                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Roles & Permissions</h2>
                <p class="text-sm text-[#6F767E] mt-1">Assign roles to define user access levels.</p>
            </div>
            <div class="space-y-4">
                @foreach($roles as $role)
                <label class="flex items-center p-4 rounded-2xl border border-gray-100 dark:border-[#272B30] hover:bg-gray-50 dark:hover:bg-[#272B30]/30 transition cursor-pointer group">
                    <input type="radio" wire:model.live="selectedRole" value="{{ $role->id }}" name="role_selection"
                        class="w-5 h-5 text-blue-600 border-gray-300 dark:border-[#272B30] focus:ring-blue-500 dark:bg-[#0B0B0B]">
                    <div class="ml-4">
                        <span class="block text-sm font-bold text-[#111827] dark:text-[#FCFCFC] group-hover:text-blue-500 transition-colors">{{ $role->name }}</span>
                        @if($role->is_super_admin)
                            <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400 rounded-full uppercase tracking-wider">Super Admin</span>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>
        </x-admin.ui.card>

        <x-admin.ui.card padding="p-10">
            <div class="mb-10">
                <h2 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Security</h2>
                <p class="text-sm text-[#6F767E] mt-1">Enhance account safety and access control.</p>
            </div>
            <div class="space-y-6">
                <div
                    id="password-change-row"
                    class="flex items-center justify-between p-6 rounded-2xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-100 dark:border-[#272B30]">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-500/10 text-blue-500">
                            <span class="material-symbols-outlined">lock</span>
                        </div>
                        <div>
                            <p class="text-[15px] font-bold text-[#111827] dark:text-[#FCFCFC]">Change Password</p>
                            <p class="text-xs text-[#6F767E]">
                                Last changed {{ $user->password_changed_at ? $user->password_changed_at->diffForHumans() : 'Never' }}
                            </p>
                        </div>
                    </div>
                    <x-admin.ui.button
                        type="button"
                        variant="outline"
                        x-data=""
                        @click="$dispatch('open-password-modal'); $nextTick(() => document.getElementById('password-change-form').scrollIntoView({ behavior: 'smooth', block: 'nearest' }))"
                        class="!py-2.5 !px-6 text-xs uppercase tracking-wider"
                    >
                        Update
                    </x-admin.ui.button>
                </div>
                
                <!-- Password Change Modal/Form -->
                <div 
                    id="password-change-form"
                    x-data="{ open: false }" 
                    @open-password-modal.window="open = true" 
                    x-show="open" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-4"
                    x-cloak 
                    class="space-y-4 p-6 rounded-2xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-100 dark:border-[#272B30]"
                >
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Set New Password</h3>
                        <button 
                            @click="document.getElementById('password-change-row').scrollIntoView({ behavior: 'smooth', block: 'center' }); setTimeout(() => open = false, 300)" 
                            class="text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]"
                        >
                            <span class="material-symbols-outlined text-lg">close</span>
                        </button>
                    </div>
                    <x-admin.ui.input 
                        name="password" 
                        type="password"
                        label="New Password" 
                        wire:model="password" 
                        placeholder="Min. 8 characters"
                    />
                    <x-admin.ui.input 
                        name="password_confirmation" 
                        type="password"
                        label="Confirm Password" 
                        wire:model="password_confirmation" 
                        placeholder="Confirm new password"
                    />
                    <div class="flex justify-end pt-2">
                        <x-admin.ui.button 
                            type="button" 
                            variant="primary" 
                            wire:click="updatePassword" 
                            class="!py-2.5 !px-6 text-xs uppercase tracking-wider"
                        >
                            Save Password
                        </x-admin.ui.button>
                    </div>
                </div>
            </div>
        </x-admin.ui.card>
    </div>
    <div class="lg:col-span-4 space-y-8">
        <x-admin.ui.card padding="p-10" class="text-center">
            <h2 class="block text-sm font-bold text-gray-700 dark:text-[#FCFCFC] mb-8">Profile Picture</h2>
            <div class="relative group inline-block">
                <div
                    class="h-40 w-40 rounded-3xl bg-gray-50 dark:bg-[#0B0B0B] flex items-center justify-center border-2 border-dashed border-gray-200 dark:border-[#272B30] overflow-hidden relative">
                    
                    <div wire:loading wire:target="avatar" class="absolute inset-0 bg-black/50 flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-white animate-spin">refresh</span>
                    </div>

                    @if ($avatar)
                        <img src="{{ $avatar->temporaryUrl() }}" alt="Profile Preview" class="h-full w-full object-cover">
                    @elseif($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Profile" class="h-full w-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-5xl text-[#6F767E]">person</span>
                    @endif
                </div>
                
                <input type="file" wire:model="avatar" id="avatar-upload" class="hidden" accept="image/*">
                
                <label for="avatar-upload"
                    class="absolute -bottom-3 -right-3 h-12 w-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-lg hover:scale-110 transition-transform border-4 border-white dark:border-[#1A1A1A] cursor-pointer">
                    <span class="material-symbols-outlined text-[24px]">edit</span>
                </label>
            </div>
            @error('avatar') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
            <p class="text-xs text-[#6F767E] mt-8 font-medium leading-relaxed uppercase tracking-wider">
                JPG, GIF or PNG. <br /> Max size of 1MB</p>
        </x-admin.ui.card>

        <x-admin.ui.card padding="p-10">
            <h2 class="block text-sm font-bold text-gray-700 dark:text-[#FCFCFC] mb-8">Account Metadata</h2>
            <div class="space-y-4">
                <div
                    class="flex justify-between items-center pb-4 border-b border-gray-100 dark:border-[#272B30]">
                    <span class="text-sm font-medium text-[#6F767E]">Joined Date</span>
                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-[#6F767E]">Last Login</span>
                    <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </span>
                </div>
                <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-[#272B30]">
                    <span class="text-sm font-medium text-[#6F767E]">Status</span>
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700 dark:bg-green-950/50 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </x-admin.ui.card>
    </div>
</div>
