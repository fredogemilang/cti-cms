<div>
    <!-- Filters & Search -->
    <div class="space-y-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Left: Search input -->
            <div class="flex flex-wrap items-center gap-3 flex-1">
                <div class="relative group w-full sm:w-auto">
                    <input
                        wire:model.live.debounce.300ms="search"
                        class="h-12 w-full sm:w-[320px] rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-12 pr-4 text-sm font-medium text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all placeholder:text-[#6F767E]"
                        placeholder="Cari nama, email, SN, atau produk..." type="text" />
                    <span
                        class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[#6F767E] group-focus-within:text-[#2563EB] transition-colors">search</span>
                    
                    <!-- Loading indicator -->
                    <div wire:loading wire:target="search" class="absolute right-4 top-1/2 -translate-y-1/2">
                        <svg class="animate-spin h-5 w-5 text-[#2563EB]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                @if($search || $statusFilter)
                <button
                    wire:click="$set('search', ''); $set('statusFilter', '')"
                    class="h-12 px-4 rounded-xl bg-gray-100 dark:bg-[#272B30] text-[#6F767E] font-medium text-sm hover:bg-gray-200 dark:hover:bg-[#333] transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">close</span>
                    Reset Filter
                </button>
                @endif
            </div>

            <!-- Right: Display rows selector -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-[#6F767E]">Tampilkan:</span>
                    <select 
                        wire:model.live="perPage"
                        class="h-12 rounded-xl border-none bg-white dark:bg-[#1A1A1A] pl-4 pr-10 text-sm font-bold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all cursor-pointer"
                    >
                        <option value="10">10 Baris</option>
                        <option value="25">25 Baris</option>
                        <option value="50">50 Baris</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Row 2: Status Filter Segment Buttons -->
        <div class="mb-4">
            <div class="inline-flex w-fit items-center bg-gray-100/50 dark:bg-[#0B0B0B]/30 p-1 rounded-2xl ring-1 ring-gray-200 dark:ring-[#272B30]">
                @php
                    $statuses = [
                        '' => ['label' => 'Semua', 'count' => $statusCounts['all'], 'color' => 'blue'],
                        'pending' => ['label' => 'Menunggu Verifikasi', 'count' => $statusCounts['pending'], 'color' => 'amber'],
                        'processing' => ['label' => 'Sedang Diproses', 'count' => $statusCounts['processing'], 'color' => 'indigo'],
                        'completed' => ['label' => 'Disetujui', 'count' => $statusCounts['completed'], 'color' => 'green'],
                        'rejected' => ['label' => 'Ditolak', 'count' => $statusCounts['rejected'], 'color' => 'red'],
                    ];
                @endphp

                @foreach($statuses as $value => $data)
                    <button
                        wire:click="$set('statusFilter', '{{ $value }}')"
                        class="h-10 px-4 rounded-xl text-sm font-bold transition-all flex items-center gap-2 {{ $statusFilter === $value ? 'bg-white dark:bg-[#1A1A1A] text-[#2563EB] shadow-sm ring-1 ring-gray-200 dark:ring-[#272B30]' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
                        {{ $data['label'] }}
                        <span class="px-2 py-0.5 rounded-lg {{ $statusFilter === $value ? 'bg-blue-50 dark:bg-blue-900/20 text-[#2563EB]' : 'bg-gray-200/50 dark:bg-[#272B30] text-[#6F767E]' }} text-[10px] font-bold">
                            {{ $data['count'] }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RMA Requests Table -->
    <div class="rounded-3xl bg-white dark:bg-[#1A1A1A] shadow-sm border border-gray-200 dark:border-[#272B30] overflow-hidden relative">
        <!-- Loading Bar -->
        <div wire:loading.delay.shortest class="absolute top-0 left-0 right-0 h-1 z-20 overflow-hidden">
            <div class="h-full bg-[#2563EB] animate-indeterminate origin-left"></div>
        </div>
        
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-[#0B0B0B]/20 border-b border-gray-100 dark:border-[#272B30]">
                        <th class="px-6 py-5 text-[11px] font-bold text-[#6F767E] uppercase tracking-widest">Nomor RMA</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-[#6F767E] uppercase tracking-widest">Nama Lengkap</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-[#6F767E] uppercase tracking-widest">Email</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-[#6F767E] uppercase tracking-widest">Nama Produk</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-[#6F767E] uppercase tracking-widest">Serial Number</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-[#6F767E] uppercase tracking-widest">Status</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-[#6F767E] uppercase tracking-widest">Tanggal Masuk</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-[#6F767E] uppercase tracking-widest text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-[#272B30]/30 transition-opacity duration-200" wire:loading.class="opacity-50 pointer-events-none">
                    @forelse($entries as $entry)
                        @php
                            $data = $entry->data ?? [];
                            $statusColor = match ($entry->status) {
                                'pending' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20',
                                'processing' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20',
                                'completed' => 'bg-green-500/10 text-green-600 dark:text-green-400 border-green-500/20',
                                'rejected' => 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
                                default => 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/20',
                            };
                            $statusLabel = match ($entry->status) {
                                'pending' => 'Menunggu Verifikasi',
                                'processing' => 'Sedang Diproses',
                                'completed' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                default => ucfirst($entry->status),
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/30 dark:hover:bg-[#272B30]/10 transition-colors">
                            <td class="px-6 py-4 font-bold text-[#2563EB] whitespace-nowrap">
                                #{{ sprintf('RMA-%04d', $entry->id) }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                {{ $entry->getFieldValue('nama_lengkap') ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                {{ $entry->getFieldValue('alamat_email') ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                {{ $entry->getFieldValue('nama_produk') ?: '-' }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                {{ $entry->getFieldValue('serial_number_produk') ?: '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusColor }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                {{ $entry->created_at ? $entry->created_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="selectRma({{ $entry->id }})" class="p-2 rounded-lg bg-gray-50 dark:bg-[#272B30] text-[#6F767E] hover:text-[#2563EB] hover:bg-[#2563EB]/10 transition-all flex items-center justify-center">
                                        <span class="material-symbols-outlined text-lg">visibility</span>
                                    </button>
                                    <button onclick="confirm('Apakah Anda yakin ingin menghapus pengajuan ini?') || event.stopImmediatePropagation()" wire:click="deleteRma({{ $entry->id }})" class="p-2 rounded-lg bg-gray-50 dark:bg-[#272B30] text-[#6F767E] hover:text-red-500 hover:bg-red-500/10 transition-all flex items-center justify-center">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-700">hourglass_empty</span>
                                    <span class="font-medium text-sm">Tidak ada pengajuan RMA yang ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Footer -->
        @if($entries->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-[#272B30]/30 bg-gray-50/20 dark:bg-[#0B0B0B]/10">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

    <!-- Details and Status Edit Modal -->
    @if($showModal && $selectedRma)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

            <!-- Modal Content -->
            <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl overflow-hidden shadow-2xl ring-1 ring-black/5 dark:ring-white/10 max-w-2xl w-full z-10 transition-all flex flex-col max-h-[90vh]">
                <!-- Modal Header -->
                <div class="px-8 py-6 border-b border-gray-100 dark:border-[#272B30] flex items-center justify-between bg-gray-50/50 dark:bg-[#0B0B0B]/20">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                            Detail Pengajuan RMA #{{ sprintf('RMA-%04d', $selectedRma->id) }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Diajukan pada {{ $selectedRma->created_at ? $selectedRma->created_at->format('d F Y H:i') : '-' }}
                        </p>
                    </div>
                    <button wire:click="closeModal" class="p-2 rounded-xl text-gray-400 hover:bg-gray-100 dark:hover:bg-[#272B30] hover:text-gray-700 dark:hover:text-gray-200 transition-all">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="px-8 py-6 space-y-6 overflow-y-auto max-h-[60vh] no-scrollbar">
                    
                    <!-- Customer and Product Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Informasi Pengaju</span>
                            <div class="mt-2 space-y-3">
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Nama Lengkap</label>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $selectedRma->getFieldValue('nama_lengkap') ?: '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Alamat Email</label>
                                    <p class="text-sm font-semibold text-[#2563EB]">{{ $selectedRma->getFieldValue('alamat_email') ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Informasi Produk</span>
                            <div class="mt-2 space-y-3">
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Nama Produk</label>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $selectedRma->getFieldValue('nama_produk') ?: '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Serial Number</label>
                                    <p class="text-sm font-mono font-semibold text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-[#272B30] px-2 py-0.5 rounded border border-gray-150 dark:border-gray-800 inline-block">{{ $selectedRma->getFieldValue('serial_number_produk') ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RMA Request Detail Grid -->
                    <div class="border-t border-gray-100 dark:border-[#272B30]/30 pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Detail Pengajuan</span>
                            <div class="mt-2 space-y-3">
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Jenis Pengajuan</label>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $selectedRma->getFieldValue('jenis_pengajuan') ?: '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Jumlah Unit</label>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $selectedRma->getFieldValue('jumlah_unit') ?: '-' }} Unit</p>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Tanggal Pembelian</label>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $selectedRma->getFieldValue('tanggal_pembelian') ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Lampiran & Alasan</span>
                            <div class="mt-2 space-y-3">
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Dokumen Bukti & Kondisi</label>
                                    @php
                                        $buktiLink = $selectedRma->getFieldValue('bukti_pembelian');
                                    @endphp
                                    <div class="mt-1">
                                        @if(filter_var($buktiLink, FILTER_VALIDATE_URL))
                                            <a href="{{ $buktiLink }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#2563EB]/10 text-[#2563EB] hover:bg-[#2563EB]/20 text-xs font-bold transition-all">
                                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                                                Buka Tautan Lampiran
                                            </a>
                                        @elseif($buktiLink)
                                            <!-- Check if it is a local path -->
                                            @if(str_starts_with($buktiLink, 'form-submissions/') || str_starts_with($buktiLink, 'media/'))
                                                <a href="{{ asset('storage/' . $buktiLink) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#2563EB]/10 text-[#2563EB] hover:bg-[#2563EB]/20 text-xs font-bold transition-all">
                                                    <span class="material-symbols-outlined text-sm">download</span>
                                                    Unduh / Buka Dokumen
                                                </a>
                                            @else
                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 font-mono">{{ $buktiLink }}</p>
                                            @endif
                                        @else
                                            <p class="text-xs text-gray-400 italic">Tidak ada lampiran dokumen</p>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Alasan Pengajuan RMA</label>
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-[#0B0B0B]/30 p-3 rounded-xl border border-gray-100 dark:border-gray-800">
                                        {{ $selectedRma->getFieldValue('alasan_pengajuan_rma') ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Review Panel -->
                    <div class="border-t border-gray-100 dark:border-[#272B30]/30 pt-6">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Review & Update Status</span>
                        <div class="mt-3 flex flex-col sm:flex-row items-end sm:items-center gap-4">
                            <div class="flex-1 w-full">
                                <label for="statusSelect" class="text-xs text-gray-500 dark:text-gray-400 block mb-1">Status Pengajuan</label>
                                <select 
                                    id="statusSelect"
                                    wire:model="newStatus"
                                    class="h-12 w-full rounded-xl border-none bg-gray-50 dark:bg-[#0B0B0B]/30 pl-4 pr-10 text-sm font-bold text-[#111827] dark:text-[#FCFCFC] ring-1 ring-gray-200 dark:ring-[#272B30] focus:ring-2 focus:ring-[#2563EB] transition-all cursor-pointer"
                                >
                                    <option value="pending">Menunggu Verifikasi (Pending)</option>
                                    <option value="processing">Sedang Diproses (Processing)</option>
                                    <option value="completed">Disetujui / Selesai (Completed)</option>
                                    <option value="rejected">Ditolak (Rejected)</option>
                                </select>
                            </div>
                            <button 
                                wire:click="updateStatus"
                                class="h-12 w-full sm:w-auto px-6 rounded-xl bg-[#2563EB] text-white font-bold text-sm hover:bg-blue-700 transition-all shadow-md shadow-blue-500/10 flex items-center justify-center gap-2"
                            >
                                <span class="material-symbols-outlined text-lg">save</span>
                                Simpan Status
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="px-8 py-4 border-t border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#0B0B0B]/20 text-right">
                    <button 
                        wire:click="closeModal" 
                        class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#272B30] text-sm font-bold transition-all"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
