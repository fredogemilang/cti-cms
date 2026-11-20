<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-sm font-medium text-emerald-700 dark:text-emerald-300 flex items-center gap-2.5">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Caution Alert: Mandatory Uniform Page / Image Dimensions --}}
    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-300 dark:border-amber-500/30 flex items-start gap-3.5 shadow-xs">
        <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-2xl shrink-0 mt-0.5">warning</span>
        <div class="space-y-1 text-sm text-amber-800 dark:text-amber-200">
            <p class="font-bold text-amber-900 dark:text-amber-200">
                Caution: Ukuran gambar &amp; halaman di setiap page PDF wajib seragam (konsisten)
            </p>
            <p class="text-xs sm:text-[13px] leading-relaxed text-amber-800 dark:text-amber-300">
                Pastikan seluruh halaman / gambar pada file PDF memiliki <strong>lebar, tinggi, dan rasio aspek yang sama persis di setiap halaman</strong> (misalnya seluruh halaman A4 Portrait atau seluruh halaman Landscape berdimensi identik) agar tampilan, proporsi lipatan buku, dan perataan tiap halaman di flipbook tetap konsisten. Mengunggah PDF baru akan <strong>otomatis menggantikan (replace)</strong> file PDF lama sehingga hanya ada 1 PDF aktif.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column: Single-PDF Upload & Settings Form --}}
        <div class="lg:col-span-7 bg-white dark:bg-[#1A1A1A] p-6 rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-sm space-y-5">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-[#FCFCFC]">Upload / Replace Catalog PDF</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Hanya 1 file PDF aktif yang disimpan. File baru akan langsung menggantikan file sebelumnya.
                </p>
            </div>

            <form wire:submit.prevent="save" class="space-y-5">
                {{-- PDF Dropzone --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        PDF File (.pdf — max 50 MB)
                    </label>
                    <label
                        for="catalog-pdf-upload"
                        class="flex flex-col items-center justify-center w-full px-6 py-8 border-2 border-dashed rounded-2xl cursor-pointer transition-colors border-gray-300 dark:border-[#272B30] bg-gray-50/70 dark:bg-[#0B0B0B] hover:border-cyan-500 hover:bg-cyan-50/40 dark:hover:bg-cyan-950/10">
                        <span class="material-symbols-outlined text-4xl text-cyan-600 mb-2">picture_as_pdf</span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            @if($pdfFile)
                                {{ $pdfFile->getClientOriginalName() }} ({{ number_format($pdfFile->getSize() / 1048576, 2) }} MB)
                            @else
                                Click to choose a PDF file to {{ $activePdfUrl ? 'replace current PDF' : 'upload' }}
                            @endif
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Format PDF (.pdf) &middot; Pastikan dimensi semua halaman PDF sama persis
                        </span>
                        <input
                            id="catalog-pdf-upload"
                            type="file"
                            wire:model="pdfFile"
                            accept="application/pdf,.pdf"
                            class="hidden"
                        />
                    </label>

                    <div wire:loading wire:target="pdfFile" class="mt-2 flex items-center gap-2 text-xs font-medium text-cyan-700 dark:text-cyan-400">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span>Uploading temporary PDF...</span>
                    </div>

                    @error('pdfFile')
                        <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Page Heading & Subtitle Metadata (EN / ID) --}}
                <div class="pt-3 border-t border-gray-100 dark:border-[#272B30] space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Page Title (EN) *</label>
                            <input type="text" wire:model="titleEn" required class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white" />
                            @error('titleEn') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Page Title (ID) *</label>
                            <input type="text" wire:model="titleId" required class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white" />
                            @error('titleId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Subtitle (EN)</label>
                            <textarea rows="2" wire:model="subtitleEn" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">Subtitle (ID)</label>
                            <textarea rows="2" wire:model="subtitleId" class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-gray-50 dark:bg-[#0B0B0B] text-sm text-gray-900 dark:text-white"></textarea>
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2.5 text-xs font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                        <input type="checkbox" wire:model="allowDownload" class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500" />
                        <span>Show "Download PDF" button in the public flipbook toolbar</span>
                    </label>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save,pdfFile"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-60 text-white text-sm font-semibold shadow-sm transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-lg">save</span>
                        <span wire:loading.remove wire:target="save">{{ $pdfFile ? 'Save & Replace Catalog PDF' : 'Save Catalog Settings' }}</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Right Column: Active Catalog Status & Quick Preview Link --}}
        <div class="lg:col-span-5 bg-white dark:bg-[#1A1A1A] p-6 rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-sm space-y-5 h-fit">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-gray-900 dark:text-[#FCFCFC]">Active Catalog File</h3>
                @if($activePdfUrl)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Active
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        Empty
                    </span>
                @endif
            </div>

            @if($activePdfUrl)
                <div class="p-4 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] space-y-2.5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined">picture_as_pdf</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                {{ $currentConfig['original_filename'] ?: 'partner-reward-catalog.pdf' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                @if(!empty($currentConfig['file_size']))
                                    @php $bytes = (int) $currentConfig['file_size']; @endphp
                                    {{ $bytes >= 1048576 ? number_format($bytes / 1048576, 2) . ' MB' : number_format($bytes / 1024, 1) . ' KB' }} &middot;
                                @endif
                                @if(!empty($currentConfig['updated_at']))
                                    Updated {{ \Carbon\Carbon::parse($currentConfig['updated_at'])->diffForHumans() }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-gray-200 dark:border-[#272B30] flex flex-wrap gap-2">
                        <a href="{{ url('/partner-reward-catalog') }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-semibold transition-colors">
                            <span class="material-symbols-outlined text-base">auto_stories</span>
                            <span>Open Live Flipbook</span>
                        </a>

                        <a href="{{ $activePdfUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-200 text-xs font-semibold transition-colors">
                            <span class="material-symbols-outlined text-base">open_in_new</span>
                            <span>Raw PDF</span>
                        </a>

                        <button
                            type="button"
                            wire:click="removeCurrentPdf"
                            wire:confirm="Remove the active Partner Reward Catalog PDF?"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 text-xs font-semibold transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-base">delete</span>
                            <span>Remove</span>
                        </button>
                    </div>
                </div>
            @else
                <div class="p-6 rounded-xl bg-gray-50 dark:bg-[#0B0B0B] border border-dashed border-gray-200 dark:border-[#272B30] text-center space-y-2">
                    <span class="material-symbols-outlined text-3xl text-gray-400">menu_book</span>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">No Catalog PDF Uploaded Yet</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Upload a PDF file on the left panel to publish the interactive flipbook at <code class="font-mono text-[11px]">/partner-reward-catalog</code>.
                    </p>
                </div>
            @endif

            <div class="p-4 rounded-xl bg-blue-50/70 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 space-y-1.5 text-xs">
                <p class="font-bold text-blue-950 dark:text-blue-200">Public Flipbook URLs</p>
                <div class="space-y-1 font-mono text-[11px] text-blue-800 dark:text-blue-300">
                    <div>EN: <a href="{{ url('/partner-reward-catalog') }}" target="_blank" class="underline hover:text-blue-600">{{ url('/partner-reward-catalog') }}</a></div>
                    <div>ID: <a href="{{ url('/id/partner-reward-catalog') }}" target="_blank" class="underline hover:text-blue-600">{{ url('/id/partner-reward-catalog') }}</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
