<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <x-admin.ui.card padding="p-6" class="lg:col-span-2 space-y-6">
        @if (session('success'))
            <x-admin.ui.alert type="success" class="mb-4">
                {{ session('success') }}
            </x-admin.ui.alert>
        @endif

        <div>
            <x-admin.ui.input 
                name="name" 
                type="text" 
                label="Name" 
                wire:model="name" 
                class="!py-2.5" 
            />
            @error('name') <p class="text-xs text-[#FF6A55] mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-admin.ui.input 
                name="key_name" 
                type="text" 
                label="Key" 
                wire:model="key_name" 
                disabled="{{ $template->is_system }}"
                class="!font-mono !py-2.5" 
            />
            @if ($template->is_system) <p class="text-xs text-[#6F767E] mt-1.5 font-medium">System templates have locked keys.</p> @endif
            @error('key_name') <p class="text-xs text-[#FF6A55] mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <x-admin.ui.input 
                name="subject" 
                type="text" 
                label="Subject" 
                wire:model="subject" 
                class="!py-2.5" 
            />
            @error('subject') <p class="text-xs text-[#FF6A55] mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-[#FCFCFC] mb-2">Body (HTML)</label>
            <textarea wire:model.live.debounce.500ms="body_html" rows="14" class="w-full px-4 py-3 rounded-2xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#0B0B0B] text-gray-900 dark:text-[#FCFCFC] focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900/30 transition font-mono text-sm"></textarea>
            @error('body_html') <p class="text-xs text-[#FF6A55] mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-[#FCFCFC] mb-2">Body (Plain Text — optional)</label>
            <textarea wire:model="body_text" rows="4" class="w-full px-4 py-3 rounded-2xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#0B0B0B] text-gray-900 dark:text-[#FCFCFC] focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900/30 transition font-mono text-sm"></textarea>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 dark:text-[#FCFCFC] mb-2">Internal description</label>
            <textarea wire:model="description" rows="2" class="w-full px-4 py-3 rounded-2xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#0B0B0B] text-gray-900 dark:text-[#FCFCFC] focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900/30 transition text-sm"></textarea>
        </div>

        <div class="flex items-center gap-3">
            <x-admin.ui.button wire:click="save" variant="primary" class="!py-2.5 !px-5 text-sm">Save</x-admin.ui.button>
            <a href="{{ route('admin.email-templates.index') }}" class="text-sm font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] transition">Cancel</a>
        </div>
    </x-admin.ui.card>

    <div class="space-y-4">
        <x-admin.ui.card padding="p-4" class="space-y-3">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white">Available variables</h3>
            <ul class="text-xs space-y-2 font-mono text-gray-700 dark:text-gray-300">
                @forelse ((array) $template->variables as $k => $label)
                    <li class="flex items-start gap-1">
                        <code class="text-xs bg-gray-100 dark:bg-[#272B30] text-[#2563EB] px-1.5 py-0.5 rounded font-mono shrink-0">{{ '{{ '.$k.' }}' }}</code>
                        <span class="text-[#6F767E]">— {{ $label }}</span>
                    </li>
                @empty
                    <li class="text-[#6F767E]">No variables defined.</li>
                @endforelse
                <li><code class="text-xs bg-gray-100 dark:bg-[#272B30] text-[#2563EB] px-1.5 py-0.5 rounded font-mono">{{ '{{ site.name }}' }}</code></li>
                <li><code class="text-xs bg-gray-100 dark:bg-[#272B30] text-[#2563EB] px-1.5 py-0.5 rounded font-mono">{{ '{{ site.url }}' }}</code></li>
            </ul>
        </x-admin.ui.card>

        <x-admin.ui.card padding="p-4" class="space-y-3">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white">Test send</h3>
            <x-admin.ui.input type="email" name="testEmail" wire:model="testEmail" placeholder="you@example.com" class="!py-2 text-sm" />
            @error('testEmail') <p class="text-xs text-[#FF6A55] mb-1.5 mt-1">{{ $message }}</p> @enderror
            <x-admin.ui.button wire:click="sendTest" variant="primary" class="w-full !py-2 text-sm justify-center">
                Send test
            </x-admin.ui.button>
        </x-admin.ui.card>

        <x-admin.ui.card padding="p-4" class="space-y-3">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white">Preview</h3>
            <div class="border border-gray-200 dark:border-[#272B30] rounded-2xl p-4 bg-gray-50 dark:bg-[#0B0B0B]/50 max-h-64 overflow-auto text-sm text-gray-900 dark:text-[#FCFCFC]">
                {!! $this->preview !!}
            </div>
        </x-admin.ui.card>

        <x-admin.ui.card padding="p-4" class="space-y-3">
            <h3 class="font-bold text-sm text-gray-900 dark:text-white">History (last 5)</h3>
            <ul class="text-xs space-y-2">
                @forelse ($versions as $v)
                    <li class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30]/30 pb-2 last:border-0 last:pb-0">
                        <span class="text-[#6F767E] font-medium">{{ $v->created_at?->diffForHumans() }}</span>
                        <button wire:click="rollback({{ $v->id }})" class="text-[11px] font-bold text-[#2563EB] hover:underline uppercase tracking-wider">Rollback</button>
                    </li>
                @empty
                    <li class="text-[#6F767E]">No history yet.</li>
                @endforelse
            </ul>
        </x-admin.ui.card>
    </div>
</div>

