<div class="rounded-2xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] p-5 shadow-sm">
    <div class="flex items-center gap-2 mb-4 text-[#6F767E]">
        <span class="material-symbols-outlined text-lg">notes</span>
        <span class="text-xs font-bold uppercase tracking-widest">Editorial Notes ({{ count($notes) }})</span>
    </div>

    @if($notableId > 0)
        <form wire:submit.prevent="addNote" class="space-y-3 mb-4">
            <textarea wire:model="newNote" rows="2"
                class="w-full rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] p-3 text-xs text-[#111827] dark:text-[#FCFCFC] focus:ring-1 focus:ring-[#2563EB] resize-none"
                placeholder="Add an internal note for team members..."></textarea>
            <div class="flex justify-end">
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#2563EB] text-white text-xs font-bold hover:bg-blue-700 transition-colors">
                    Add Note
                </button>
            </div>
        </form>

        <div class="space-y-3 max-h-60 overflow-y-auto no-scrollbar divide-y divide-gray-100 dark:divide-[#272B30]">
            @forelse($notes as $note)
                <div class="pt-3 first:pt-0 flex items-start justify-between gap-3 text-xs">
                    <div class="space-y-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-[#111827] dark:text-[#FCFCFC] truncate">{{ $note->user->name ?? 'User' }}</span>
                            <span class="text-[10px] text-[#6F767E]">{{ $note->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[#6F767E] dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $note->note }}</p>
                    </div>
                    @if($note->user_id === auth()->id() || auth()->user()->hasRole('super-admin'))
                        <button type="button" wire:click="deleteNote({{ $note->id }})" class="text-red-500 hover:text-red-700 shrink-0 p-1" title="Delete note">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    @endif
                </div>
            @empty
                <p class="text-xs text-[#6F767E] italic text-center py-2">No internal notes yet.</p>
            @endforelse
        </div>
    @else
        <p class="text-xs text-[#6F767E] italic text-center py-2">Save item first to add internal notes.</p>
    @endif
</div>
