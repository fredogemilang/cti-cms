<div x-data="{
    sortableInstance: null,
    init() {
        this.$nextTick(() => this.initSortable());
    },
    initSortable() {
        if (this.sortableInstance) {
            this.sortableInstance.destroy();
        }
        const el = this.$refs.questionsList;
        if (!el) return;
        this.sortableInstance = new Sortable(el, {
            animation: 200,
            handle: '.drag-handle',
            ghostClass: 'opacity-30',
            onEnd: (evt) => {
                const ids = [...evt.to.children].map(item => item.dataset.questionId);
                @this.updatedOrder(ids);
            }
        });
    }
}" class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Custom Questions</h3>
            <p class="text-xs text-[#6F767E] mt-0.5">Collect additional information from registrants</p>
        </div>
        <button wire:click="openAddModal" type="button"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold text-white bg-[#2563EB] hover:bg-blue-600 shadow-sm transition-all">
            <span class="material-symbols-outlined text-lg">add</span>
            Add Question
        </button>
    </div>

    {{-- Question List --}}
    <div x-ref="questionsList" class="space-y-2">
        @forelse($questions as $question)
            <div data-question-id="{{ $question->id }}"
                class="group flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] hover:border-[#2563EB]/50 transition-all">

                {{-- Drag Handle --}}
                <div class="drag-handle cursor-grab active:cursor-grabbing text-[#6F767E] hover:text-[#2563EB] transition-colors flex-shrink-0">
                    <span class="material-symbols-outlined text-lg">drag_indicator</span>
                </div>

                {{-- Question Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC] truncate">{{ $question->question }}</span>
                        @if($question->required)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Required</span>
                        @endif
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 dark:bg-[#272B30] dark:text-[#6F767E]">
                            {{ $question->type }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-[10px] text-[#6F767E] font-mono bg-gray-50 dark:bg-[#272B30] px-1.5 py-0.5 rounded">{{ $question->short_label }}</span>
                        @if($question->options->count() > 0)
                            <span class="text-[10px] text-[#6F767E]">{{ $question->options->count() }} options</span>
                        @endif
                        @if($question->image)
                            <span class="text-[10px] text-[#6F767E] flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">image</span> image
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <button wire:click="editQuestion({{ $question->id }})" type="button"
                        class="p-2 rounded-lg text-[#6F767E] hover:text-[#2563EB] hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all"
                        title="Edit">
                        <span class="material-symbols-outlined text-base">edit</span>
                    </button>
                    <button wire:click="openCloneModal({{ $question->id }})" type="button"
                        class="p-2 rounded-lg text-[#6F767E] hover:text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all"
                        title="Clone to another event">
                        <span class="material-symbols-outlined text-base">content_copy</span>
                    </button>
                    <button wire:click="deleteQuestion({{ $question->id }})" type="button"
                        wire:confirm="Delete this question? All answers will also be deleted."
                        class="p-2 rounded-lg text-[#6F767E] hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
                        title="Delete">
                        <span class="material-symbols-outlined text-base">delete</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-12 rounded-xl border-2 border-dashed border-gray-200 dark:border-[#272B30] text-center">
                <span class="material-symbols-outlined text-4xl text-[#6F767E] mb-3">quiz</span>
                <p class="text-sm font-semibold text-[#111827] dark:text-[#FCFCFC]">No questions yet</p>
                <p class="text-xs text-[#6F767E] mt-1">Add custom questions to collect registrant information</p>
            </div>
        @endforelse
    </div>

    {{-- ═══════════════════════════════════════════════════ ADD / EDIT MODAL ══ --}}
    @if($showModal)
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-[60] p-4"
        @keydown.escape.window="$wire.showModal = false">

        <div x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-[#1A1A1A] rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col shadow-xl"
            @click.away="$wire.showModal = false">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-[#272B30] flex-shrink-0">
                <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">
                    {{ $editingQuestion ? 'Edit Question' : 'Add Question' }}
                </h3>
                <button wire:click="$set('showModal', false)" type="button"
                    class="p-2 hover:bg-gray-100 dark:hover:bg-[#272B30] rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[#6F767E]">close</span>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-5">

                {{-- Question Text --}}
                <div>
                    <label class="block text-sm font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">
                        Question Text <span class="text-red-500">*</span>
                    </label>
                    <input wire:model.live="question_text" type="text" maxlength="255"
                        class="w-full h-11 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] px-4 text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent @error('question_text') border-red-500 @enderror"
                        placeholder="e.g., What is your dietary preference?">
                    @error('question_text') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Help Text --}}
                <div>
                    <label class="block text-sm font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Help Text</label>
                    <textarea wire:model.live="question_description" rows="2"
                        class="w-full rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] px-4 py-3 text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent resize-none"
                        placeholder="Optional helper text shown below the question"></textarea>
                </div>

                {{-- Short Label + Type Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">
                            Short Label <span class="text-red-500">*</span>
                        </label>
                        <input wire:model.live="short_label" type="text" maxlength="50"
                            class="w-full h-11 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] px-4 text-sm text-[#111827] dark:text-[#FCFCFC] font-mono focus:ring-2 focus:ring-[#2563EB] focus:border-transparent @error('short_label') border-red-500 @enderror"
                            placeholder="dietary_restriction">
                        <p class="text-[10px] text-[#6F767E] mt-1">Alphanumeric + underscore only. Used for export.</p>
                        @error('short_label') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">
                            Field Type <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="type"
                            class="w-full h-11 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] px-4 text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB]">
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="single_select">Single Select</option>
                            <option value="multi_select">Multi Select</option>
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                            <option value="date">Date</option>
                        </select>
                    </div>
                </div>

                {{-- Required Toggle --}}
                <div class="flex items-center gap-3">
                    <button type="button" wire:click="$toggle('required')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $required ? 'bg-[#2563EB]' : 'bg-gray-300 dark:bg-[#272B30]' }}">
                        <span class="inline-block h-4 w-4 rounded-full bg-white shadow transform transition-transform {{ $required ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                    <label class="text-sm font-medium text-[#111827] dark:text-[#FCFCFC] cursor-pointer">Required field</label>
                </div>

                {{-- Options Panel (for select types) --}}
                @if($type === 'single_select' || $type === 'multi_select')
                    <div class="rounded-xl border border-gray-200 dark:border-[#272B30] p-4 space-y-3 bg-gray-50 dark:bg-[#0B0B0B]">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">
                                Options <span class="text-red-500">*</span>
                            </label>
                            <button type="button" wire:click="addOption"
                                class="inline-flex items-center gap-1 text-xs font-bold text-[#2563EB] hover:text-blue-700 transition-colors">
                                <span class="material-symbols-outlined text-sm">add</span> Add Option
                            </button>
                        </div>

                        @forelse($options as $index => $option)
                            <div class="flex items-center gap-2">
                                <input wire:model.live="options.{{ $index }}" type="text"
                                    class="flex-1 h-10 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] px-4 text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] focus:border-transparent"
                                    placeholder="Option {{ $index + 1 }}">
                                @if(count($options) > 1)
                                    <button type="button" wire:click="removeOption({{ $index }})"
                                        class="p-2 rounded-lg text-[#6F767E] hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all flex-shrink-0">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-3">
                                <button type="button" wire:click="addOption"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold text-[#2563EB] border border-[#2563EB]/30 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                                    <span class="material-symbols-outlined text-sm">add</span> Add First Option
                                </button>
                            </div>
                        @endforelse

                        @error('options') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Image Upload --}}
                <div>
                    <label class="block text-sm font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">Question Image</label>
                    @if($image)
                        <div class="relative group">
                            <img src="{{ $image->temporaryUrl() }}"
                                class="w-full max-h-48 object-contain rounded-xl border border-gray-200 dark:border-[#272B30]">
                            <button type="button" wire:click="$set('image', null)"
                                class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-lg">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>
                    @else
                        <input wire:model.live="image" type="file" accept="image/*" id="question-image" class="hidden">
                        <label for="question-image"
                            class="flex flex-col items-center justify-center h-24 rounded-xl border-2 border-dashed border-gray-300 dark:border-[#272B30] cursor-pointer hover:border-[#2563EB] transition-all text-center">
                            <span class="material-symbols-outlined text-2xl text-[#6F767E]">add_photo_alternate</span>
                            <span class="text-xs font-medium text-[#6F767E] mt-1">Upload image (optional)</span>
                        </label>
                    @endif
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 dark:border-[#272B30] flex-shrink-0">
                <button type="button" wire:click="$set('showModal', false)"
                    class="px-5 py-2 rounded-xl text-sm font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                    Cancel
                </button>
                <button type="button" wire:click="saveQuestion"
                    class="px-5 py-2 rounded-xl text-sm font-bold text-white bg-[#2563EB] hover:bg-blue-600 shadow-sm transition-all">
                    {{ $editingQuestion ? 'Update Question' : 'Add Question' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════ CLONE MODAL ══ --}}
    @if($showCloneModal && $editingQuestion)
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-[60] p-4"
        @keydown.escape.window="$wire.showCloneModal = false">

        <div x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white dark:bg-[#1A1A1A] rounded-2xl max-w-lg w-full shadow-xl"
            @click.away="$wire.showCloneModal = false">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-[#272B30]">
                <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Clone Question</h3>
                <button wire:click="$set('showCloneModal', false)" type="button"
                    class="p-2 hover:bg-gray-100 dark:hover:bg-[#272B30] rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[#6F767E]">close</span>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                    <span class="material-symbols-outlined text-xl text-blue-600 mt-0.5">info</span>
                    <div class="text-sm text-blue-900 dark:text-blue-100">
                        <p class="font-bold">Clone "{{ $editingQuestion->question }}"</p>
                        <p class="text-xs mt-1 opacity-80">This will create a copy of this question in another event.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#111827] dark:text-[#FCFCFC] mb-1.5">
                        Target Event <span class="text-red-500">*</span>
                    </label>
                    <select wire:model.live="target_event_id"
                        class="w-full h-11 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] px-4 text-sm text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-[#2563EB] @error('target_event_id') border-red-500 @enderror">
                        <option value="">— Select an event —</option>
                        @foreach($availableTargetEvents as $event)
                            <option value="{{ $event->id }}">{{ $event->title }}</option>
                        @endforeach
                    </select>
                    @error('target_event_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-100 dark:border-[#272B30]">
                <button type="button" wire:click="$set('showCloneModal', false)"
                    class="px-5 py-2 rounded-xl text-sm font-semibold text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                    Cancel
                </button>
                <button type="button" wire:click="cloneQuestion"
                    class="px-5 py-2 rounded-xl text-sm font-bold text-white bg-purple-600 hover:bg-purple-700 shadow-sm transition-all">
                    Clone Question
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
