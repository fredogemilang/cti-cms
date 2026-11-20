<div x-data="{ activeLang: 'en' }" class="space-y-6">
    @php
        $hasPdf = ! empty($activePdfUrl);
        $hasCover = ! empty($activeCoverUrl);
        $hasSavedCover = ! empty($currentConfig['cover_image_path']);
        $coverIsUnsaved = ($coverMediaId !== ($currentConfig['cover_media_id'] ?? null))
            || ($coverImagePath !== ($currentConfig['cover_image_path'] ?? null));
        $fileSizeKb = ! empty($currentConfig['file_size']) ? round($currentConfig['file_size'] / 1024, 1) : null;
        $fileSizeFormatted = $fileSizeKb
            ? ($fileSizeKb >= 1024 ? round($fileSizeKb / 1024, 2).' MB' : $fileSizeKb.' KB')
            : null;
        $canEdit = auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('partner-reward-catalog.edit');
    @endphp

    {{-- Top Header & Action Bar --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-[#EFEFEF] dark:border-[#272B30] p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl font-bold text-[#111827] dark:text-[#FCFCFC]">Partner Reward Catalog</h1>
                    @if($hasPdf)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            PDF Ready
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#F4F5F6] text-[#6F767E] dark:bg-[#272B30] dark:text-[#9A9A9A]">
                            No PDF Uploaded
                        </span>
                    @endif

                    @if($hasCover)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300 border border-blue-200 dark:border-blue-900/60">
                            Cover Active
                        </span>
                    @endif
                </div>
                <p class="text-xs text-[#6F767E] dark:text-[#9A9A9A]">
                    Manage the interactive flipbook PDF (<code class="font-mono text-[#111827] dark:text-[#FCFCFC]">/partner-reward-catalog</code>) and the promo banner on the Partner page (<code class="font-mono text-[#111827] dark:text-[#FCFCFC]">/partner</code>).
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ url('/partner') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#F4F5F6] dark:bg-[#272B30] text-[#111827] dark:text-[#FCFCFC] hover:bg-gray-200 dark:hover:bg-white/10 transition">
                    <span class="material-symbols-outlined text-base">open_in_new</span>
                    <span>Partner Page</span>
                </a>
                <a href="{{ url('/partner-reward-catalog') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#F4F5F6] dark:bg-[#272B30] text-[#111827] dark:text-[#FCFCFC] hover:bg-gray-200 dark:hover:bg-white/10 transition">
                    <span class="material-symbols-outlined text-base">menu_book</span>
                    <span>Open Flipbook</span>
                </a>
                @if($canEdit)
                    <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save,pdfFile"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition disabled:opacity-60">
                        <span class="material-symbols-outlined text-base" wire:loading.remove wire:target="save">save</span>
                        <span wire:loading.remove wire:target="save">Save Changes</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Unified EN / ID Language Switcher --}}
        <div class="mt-5 pt-4 border-t border-[#EFEFEF] dark:border-[#272B30] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#6F767E] dark:text-[#9A9A9A]">Editing Language</span>
                <div class="inline-flex p-1 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B]">
                    <button type="button" @click="activeLang = 'en'"
                            :class="activeLang === 'en' ? 'bg-white dark:bg-[#272B30] text-[#111827] dark:text-[#FCFCFC] shadow-sm' : 'text-[#6F767E] dark:text-[#9A9A9A] hover:text-[#111827] dark:hover:text-[#FCFCFC]'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                        English (EN)
                    </button>
                    <button type="button" @click="activeLang = 'id'"
                            :class="activeLang === 'id' ? 'bg-white dark:bg-[#272B30] text-[#111827] dark:text-[#FCFCFC] shadow-sm' : 'text-[#6F767E] dark:text-[#9A9A9A] hover:text-[#111827] dark:hover:text-[#FCFCFC]'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                        Bahasa Indonesia (ID)
                    </button>
                </div>
            </div>
            <p class="text-[11px] text-[#6F767E] dark:text-[#9A9A9A]">
                Both English &amp; Indonesian fields save together when you click <strong>Save Changes</strong>.
            </p>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('success'))
        <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 p-4 flex items-center gap-3 text-sm font-medium text-emerald-900 dark:text-emerald-200">
            <span class="material-symbols-outlined text-lg text-emerald-600 dark:text-emerald-400">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- SECTION 1: Flipbook PDF & Public Viewer --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-[#EFEFEF] dark:border-[#272B30] p-6 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 border-b border-[#EFEFEF] dark:border-[#272B30]">
            <div>
                <h2 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">1. Catalog Flipbook PDF</h2>
                <p class="text-xs text-[#6F767E] dark:text-[#9A9A9A]">Single-file mode: uploading a new PDF automatically replaces the previous file.</p>
            </div>
            <span class="text-[11px] font-mono text-[#6F767E] dark:text-[#9A9A9A] bg-[#F4F5F6] dark:bg-[#0B0B0B] px-2.5 py-1 rounded-lg self-start">
                Route: /partner-reward-catalog
            </span>
        </div>

        {{-- Caution Banner --}}
        <div class="rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 p-4">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-xl text-amber-600 dark:text-amber-400 shrink-0 mt-0.5">warning</span>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-amber-950 dark:text-amber-200">
                        Caution: Ukuran halaman &amp; gambar di setiap page PDF wajib seragam (konsisten)
                    </p>
                    <p class="text-xs text-amber-900/80 dark:text-amber-300/80 leading-relaxed">
                        Pastikan seluruh halaman PDF memiliki lebar, tinggi, dan rasio aspek yang identik (misal semua A4 Portrait atau semua Landscape) agar proporsi lipatan buku dan zoom flipbook tetap presisi di setiap halaman.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Left: PDF File Dropzone & Current File Status --}}
            <div class="lg:col-span-5 space-y-3">
                <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">
                    Catalog PDF File (Max 50 MB)
                </label>

                <label class="block rounded-2xl border-2 border-dashed border-gray-300 dark:border-[#272B30] hover:border-blue-500 dark:hover:border-blue-500 bg-[#F4F5F6] dark:bg-[#0B0B0B] p-5 text-center cursor-pointer transition">
                    <input type="file" wire:model="pdfFile" accept="application/pdf,.pdf" class="sr-only" @if(!$canEdit) disabled @endif>
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">upload_file</span>
                        </div>
                        @if($pdfFile)
                            <p class="text-xs font-bold text-blue-600 dark:text-blue-400 break-all">
                                Ready: {{ $pdfFile->getClientOriginalName() }}
                            </p>
                            <p class="text-[11px] text-[#6F767E] dark:text-[#9A9A9A]">Click Save Changes above or below to replace the active PDF.</p>
                        @else
                            <p class="text-xs font-semibold text-[#111827] dark:text-[#FCFCFC]">
                                {{ $hasPdf ? 'Click to upload a replacement PDF' : 'Click to choose catalog PDF' }}
                            </p>
                            <p class="text-[11px] text-[#6F767E] dark:text-[#9A9A9A]">Format: .pdf &bull; Single active document</p>
                        @endif
                    </div>
                </label>

                <div wire:loading wire:target="pdfFile" class="text-xs font-semibold text-blue-600 dark:text-blue-400 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">hourglass_top</span>
                    <span>Uploading PDF to temporary staging…</span>
                </div>

                @error('pdfFile')
                    <p class="text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                {{-- Active PDF Card --}}
                <div class="rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] p-3.5 border border-[#EFEFEF] dark:border-[#272B30]">
                    @if($hasPdf)
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] truncate">
                                    {{ $currentConfig['original_filename'] ?? basename((string) ($currentConfig['file_path'] ?? 'catalog.pdf')) }}
                                </p>
                                <p class="text-[11px] text-[#6F767E] dark:text-[#9A9A9A] mt-0.5">
                                    {{ $fileSizeFormatted ?? 'PDF' }}
                                    @if(!empty($currentConfig['updated_at']))
                                        &bull; Updated {{ \Carbon\Carbon::parse($currentConfig['updated_at'])->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <a href="{{ $activePdfUrl }}" target="_blank" rel="noopener"
                                   class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-white dark:bg-[#1A1A1A] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30] hover:border-blue-500 transition">
                                    Raw PDF
                                </a>
                                @if($canEdit)
                                    <button type="button"
                                            wire:click="removeCurrentPdf"
                                            wire:confirm="Remove the active catalog PDF permanently?"
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition">
                                        Remove
                                    </button>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-[#6F767E] dark:text-[#9A9A9A]">
                            No PDF uploaded yet. Public visitors currently see a "Coming Soon" empty state.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Right: Flipbook Page Copy & Options (Controlled by activeLang tab) --}}
            <div class="lg:col-span-7 space-y-4">
                <div x-show="activeLang === 'en'" class="space-y-4">
                    <div>
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                            Flipbook Page Heading (EN) *
                        </label>
                        <input type="text" wire:model.live.debounce.250ms="titleEn"
                               class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                        @error('titleEn') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                            Flipbook Subtitle (EN)
                        </label>
                        <textarea wire:model="subtitleEn" rows="2"
                                  class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-3.5 resize-y"></textarea>
                    </div>
                </div>

                <div x-show="activeLang === 'id'" x-cloak class="space-y-4">
                    <div>
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                            Flipbook Page Heading (ID) *
                        </label>
                        <input type="text" wire:model.live.debounce.250ms="titleId"
                               class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                        @error('titleId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                            Flipbook Subtitle (ID)
                        </label>
                        <textarea wire:model="subtitleId" rows="2"
                                  class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-3.5 resize-y"></textarea>
                    </div>
                </div>

                <div class="rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] p-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Show "Download PDF" Button</p>
                        <p class="text-[11px] text-[#6F767E] dark:text-[#9A9A9A]">Allows partners to download the original PDF directly from the flipbook toolbar.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 cursor-pointer shrink-0">
                        <input type="checkbox" wire:model="allowDownload"
                               class="rounded border-gray-300 dark:border-gray-700 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs font-semibold text-[#111827] dark:text-[#FCFCFC]">Enabled</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: Partner Page Promo Banner (/partner) with CMS Media Library --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-[#EFEFEF] dark:border-[#272B30] p-6 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 border-b border-[#EFEFEF] dark:border-[#272B30]">
            <div>
                <h2 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">2. Partner Page Promo Banner</h2>
                <p class="text-xs text-[#6F767E] dark:text-[#9A9A9A]">Controls the highlighted promo card on the Partner page, including Media Library cover image and localized text.</p>
            </div>
            <span class="text-[11px] font-mono text-[#6F767E] dark:text-[#9A9A9A] bg-[#F4F5F6] dark:bg-[#0B0B0B] px-2.5 py-1 rounded-lg self-start">
                Route: /partner
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Left: CMS Media Library Cover Picker + Live Preview Card --}}
            <div class="lg:col-span-5 space-y-4">
                <div class="flex items-center justify-between">
                    <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider">
                        Promo Cover Image (Media Library)
                    </label>
                    @if($coverIsUnsaved)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/30 dark:text-amber-300">
                            Unsaved selection
                        </span>
                    @endif
                </div>

                {{-- Selected Cover Thumbnail + Media Library Trigger --}}
                <div class="rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border border-[#EFEFEF] dark:border-[#272B30] p-4 space-y-3">
                    <div class="w-full rounded-xl overflow-hidden bg-gray-200 dark:bg-[#1A1A1A] flex items-center justify-center" style="aspect-ratio: 16 / 9;">
                        @if($activeCoverUrl)
                            <img src="{{ $activeCoverUrl }}" alt="Partner promo cover preview" class="w-full h-full object-cover">
                        @else
                            <div class="text-center p-4 space-y-1">
                                <span class="material-symbols-outlined text-3xl text-[#6F767E] dark:text-[#9A9A9A]">image</span>
                                <p class="text-xs font-medium text-[#6F767E] dark:text-[#9A9A9A]">No custom Media selected</p>
                                <p class="text-[10px] text-[#6F767E] dark:text-[#9A9A9A]">Uses Partner page default image</p>
                            </div>
                        @endif
                    </div>

                    @if($coverOriginalFilename || $coverImagePath)
                        <p class="text-[11px] text-[#6F767E] dark:text-[#9A9A9A] truncate font-mono">
                            {{ $coverOriginalFilename ?? $coverImagePath }}
                        </p>
                    @endif

                    <div class="flex flex-wrap items-center gap-2">
                        @if($canEdit)
                            <button type="button" wire:click="openCoverMediaPicker"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white transition">
                                <span class="material-symbols-outlined text-base">photo_library</span>
                                <span>{{ $activeCoverUrl ? 'Change from Media Library' : 'Select from Media Library' }}</span>
                            </button>

                            @if($activeCoverUrl || $hasSavedCover)
                                <button type="button"
                                        wire:click="removeCoverImage"
                                        wire:confirm="Clear the cover selection and revert to Partner page default image? (The file stays safe in Media Library)"
                                        class="px-3 py-2.5 rounded-xl text-xs font-semibold text-red-600 dark:text-red-400 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] hover:bg-red-50 dark:hover:bg-red-950/40 transition">
                                    Clear
                                </button>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Compact Live Promo Card Preview --}}
                <div class="space-y-2">
                    <span class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider block">
                        Live Partner Card Preview
                    </span>
                    <div class="rounded-2xl border-2 border-blue-500 bg-white dark:bg-[#141414] p-3 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                        <div class="sm:col-span-5 rounded-xl overflow-hidden bg-gray-100 dark:bg-[#272B30]" style="aspect-ratio: 16 / 10;">
                            @if($activeCoverUrl)
                                <img src="{{ $activeCoverUrl }}" alt="Preview" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-[10px] font-medium text-[#6F767E] dark:text-[#9A9A9A]">
                                    Default Image
                                </div>
                            @endif
                        </div>
                        <div class="sm:col-span-7 space-y-1.5 min-w-0">
                            <p x-show="activeLang === 'en'" class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] truncate">{{ $promoTitleEn ?: 'Promo Title' }}</p>
                            <p x-show="activeLang === 'id'" x-cloak class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] truncate">{{ $promoTitleId ?: 'Judul Promo' }}</p>

                            <p x-show="activeLang === 'en'" class="text-[11px] text-[#6F767E] dark:text-[#9A9A9A] line-clamp-2">{{ $promoDescriptionEn }}</p>
                            <p x-show="activeLang === 'id'" x-cloak class="text-[11px] text-[#6F767E] dark:text-[#9A9A9A] line-clamp-2">{{ $promoDescriptionId }}</p>

                            <p x-show="activeLang === 'en'" class="text-[10px] font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $promoPeriodEn }}</p>
                            <p x-show="activeLang === 'id'" x-cloak class="text-[10px] font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $promoPeriodId }}</p>

                            <div class="pt-0.5">
                                <span x-show="activeLang === 'en'" class="inline-block px-2.5 py-1 rounded-md text-[10px] font-bold text-white" style="background-color:#EA8D46;">
                                    {{ $promoCtaTextEn ?: 'Explore' }}
                                </span>
                                <span x-show="activeLang === 'id'" x-cloak class="inline-block px-2.5 py-1 rounded-md text-[10px] font-bold text-white" style="background-color:#EA8D46;">
                                    {{ $promoCtaTextId ?: 'Lihat Katalog' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Localized Promo Banner Text Fields (EN / ID switched cleanly) --}}
            <div class="lg:col-span-7 space-y-4">
                <div x-show="activeLang === 'en'" class="space-y-4">
                    <div>
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                            Promo Title (EN) *
                        </label>
                        <input type="text" wire:model.live.debounce.250ms="promoTitleEn"
                               class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                        @error('promoTitleEn') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                            Promo Description (EN)
                        </label>
                        <textarea wire:model.live.debounce.250ms="promoDescriptionEn" rows="4"
                                  class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-3.5 resize-y"></textarea>
                        @error('promoDescriptionEn') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                                Promo Period (EN)
                            </label>
                            <input type="text" wire:model.live.debounce.250ms="promoPeriodEn"
                                   class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                            @error('promoPeriodEn') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                                Explore Button Label (EN) *
                            </label>
                            <input type="text" wire:model.live.debounce.250ms="promoCtaTextEn"
                                   class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                            @error('promoCtaTextEn') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div x-show="activeLang === 'id'" x-cloak class="space-y-4">
                    <div>
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                            Promo Title (ID) *
                        </label>
                        <input type="text" wire:model.live.debounce.250ms="promoTitleId"
                               class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                        @error('promoTitleId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                            Promo Description (ID)
                        </label>
                        <textarea wire:model.live.debounce.250ms="promoDescriptionId" rows="4"
                                  class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 p-3.5 resize-y"></textarea>
                        @error('promoDescriptionId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                                Promo Period (ID)
                            </label>
                            <input type="text" wire:model.live.debounce.250ms="promoPeriodId"
                                   class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                            @error('promoPeriodId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-[#6F767E] dark:text-[#9A9A9A] uppercase tracking-wider mb-1.5 block">
                                Explore Button Label (ID) *
                            </label>
                            <input type="text" wire:model.live.debounce.250ms="promoCtaTextId"
                                   class="w-full h-11 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500 px-4">
                            @error('promoCtaTextId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] px-4 py-3 flex items-center justify-between gap-3 text-xs">
                    <span class="text-[#6F767E] dark:text-[#9A9A9A]">Explore Button Target Destination</span>
                    <code class="font-mono font-semibold text-blue-600 dark:text-blue-400">/partner-reward-catalog</code>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Save Footer --}}
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-[#EFEFEF] dark:border-[#272B30] px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-xs text-[#6F767E] dark:text-[#9A9A9A]">
            Changes automatically clear frontend cache so <code class="font-mono">/partner</code> and <code class="font-mono">/partner-reward-catalog</code> update immediately.
        </p>
        @if($canEdit)
            <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save,pdfFile"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition disabled:opacity-60">
                <span class="material-symbols-outlined text-base" wire:loading.remove wire:target="save">save</span>
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        @endif
    </div>

    {{-- Native CMS Media Library Picker Modal --}}
    @if($showMediaPicker)
        <livewire:admin.media-picker field="partner_cover_image" :show-modal="true" :key="'partner-catalog-cover-picker'" />
    @endif
</div>
