<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg p-6 space-y-4">
        @if (session('success'))
            <div class="p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" wire:model="name" class="mt-1 w-full rounded border-gray-300">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Key</label>
            <input type="text" wire:model="key_name" @disabled($template->is_system) class="mt-1 w-full rounded border-gray-300 font-mono">
            @if ($template->is_system) <p class="text-xs text-gray-500 mt-1">System templates have locked keys.</p> @endif
            @error('key_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Subject</label>
            <input type="text" wire:model="subject" class="mt-1 w-full rounded border-gray-300">
            @error('subject') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Body (HTML)</label>
            <textarea wire:model.live.debounce.500ms="body_html" rows="14" class="mt-1 w-full rounded border-gray-300 font-mono text-sm"></textarea>
            @error('body_html') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Body (Plain Text — optional)</label>
            <textarea wire:model="body_text" rows="4" class="mt-1 w-full rounded border-gray-300 font-mono text-sm"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Internal description</label>
            <textarea wire:model="description" rows="2" class="mt-1 w-full rounded border-gray-300 text-sm"></textarea>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="save" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
            <a href="{{ route('admin.email-templates.index') }}" class="text-gray-600 hover:underline">Cancel</a>
        </div>
    </div>

    <div class="space-y-4">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold mb-2">Available variables</h3>
            <ul class="text-xs space-y-1 font-mono text-gray-700">
                @forelse ((array) $template->variables as $k => $label)
                    <li><code>{{ '{{ '.$k.' }}' }}</code> — <span class="text-gray-500">{{ $label }}</span></li>
                @empty
                    <li class="text-gray-500">No variables defined.</li>
                @endforelse
                <li><code>{{ '{{ site.name }}' }}</code></li>
                <li><code>{{ '{{ site.url }}' }}</code></li>
            </ul>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold mb-2">Test send</h3>
            <input type="email" wire:model="testEmail" placeholder="you@example.com"
                   class="w-full rounded border-gray-300 text-sm mb-2">
            @error('testEmail') <p class="text-xs text-red-600 mb-1">{{ $message }}</p> @enderror
            <button wire:click="sendTest" class="w-full px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                Send test
            </button>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold mb-2">Preview</h3>
            <div class="border rounded p-3 bg-gray-50 max-h-64 overflow-auto text-sm">
                {!! $this->preview !!}
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold mb-2">History (last 5)</h3>
            <ul class="text-xs space-y-2">
                @forelse ($versions as $v)
                    <li class="flex items-center justify-between">
                        <span class="text-gray-600">{{ $v->created_at?->diffForHumans() }}</span>
                        <button wire:click="rollback({{ $v->id }})" class="text-blue-600 hover:underline">Rollback</button>
                    </li>
                @empty
                    <li class="text-gray-500">No history yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
