<div class="space-y-6">
    {{-- Flash messages --}}
    @if (session('success'))
        <x-admin.ui.alert type="success">{{ session('success') }}</x-admin.ui.alert>
    @endif

    {{-- New Token Flash Alert --}}
    @if ($newPlaintextToken)
        <div class="p-6 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-300">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-2xl text-amber-500 mt-0.5">key</span>
                <div class="flex-1">
                    <p class="font-bold text-base">Token Baru Dibuat — Salin Sekarang!</p>
                    <p class="text-sm mt-1 opacity-90">Token ini hanya ditampilkan satu kali dan tidak bisa dilihat kembali demi keamanan.</p>
                    <div class="mt-3 flex items-center gap-2">
                        <code class="flex-1 p-3 bg-white dark:bg-[#0B0B0B] border border-amber-300 dark:border-amber-900/50 rounded-xl font-mono text-xs break-all select-all text-gray-900 dark:text-[#FCFCFC]">
                            {{ $newPlaintextToken }}
                        </code>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quick Tabs --}}
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-[#272B30] pb-2">
        <button 
            type="button" 
            wire:click="setTab('tokens')" 
            class="px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 {{ $activeTab === 'tokens' ? 'bg-blue-500 text-white shadow-sm' : 'text-gray-600 dark:text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#1A1D1F]' }}"
        >
            <span class="material-symbols-outlined text-lg">vpn_key</span>
            Tokens & Akses
        </button>
        <button 
            type="button" 
            wire:click="setTab('analytics')" 
            class="px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 {{ $activeTab === 'analytics' ? 'bg-blue-500 text-white shadow-sm' : 'text-gray-600 dark:text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#1A1D1F]' }}"
        >
            <span class="material-symbols-outlined text-lg">analytics</span>
            Aktivitas & Metrik
        </button>
        <button 
            type="button" 
            wire:click="setTab('docs')" 
            class="px-4 py-2 rounded-xl text-sm font-bold transition flex items-center gap-2 {{ $activeTab === 'docs' ? 'bg-blue-500 text-white shadow-sm' : 'text-gray-600 dark:text-[#6F767E] hover:bg-gray-100 dark:hover:bg-[#1A1D1F]' }}"
        >
            <span class="material-symbols-outlined text-lg">menu_book</span>
            Panduan Koneksi AI
        </button>
    </div>

    {{-- TAB 1: TOKENS & ACCESS --}}
    @if ($activeTab === 'tokens')
        <x-admin.ui.card padding="p-8">
            <h3 class="text-lg font-bold text-gray-900 dark:text-[#FCFCFC] mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-500">add_moderator</span>
                Buat Token MCP Baru
            </h3>
            <p class="text-xs text-gray-500 dark:text-[#6F767E] mb-6">
                Buat token otentikasi untuk AI assistant (Antigravity, Cursor, Windsurf) atau chatbot. Pilih permission tier sesuai kebutuhan.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-admin.ui.input 
                    name="tokenName" 
                    label="Nama Token / Device" 
                    wire:model="tokenName"
                    placeholder="Contoh: Cursor Fredo, Chatbot Prod"
                    required
                />

                <x-admin.ui.select 
                    name="tokenTier" 
                    label="Permission Tier" 
                    wire:model.live="tokenTier"
                    required
                >
                    @foreach ($tiers as $key => $tier)
                        <option value="{{ $key }}">{{ $tier['label'] }} ({{ $tier['rate_limit'] }} req/m)</option>
                    @endforeach
                </x-admin.ui.select>

                <x-admin.ui.input 
                    name="rateLimit" 
                    type="number"
                    label="Rate Limit (req/min)" 
                    wire:model="rateLimit"
                    min="1" 
                    max="6000"
                    required
                />

                <x-admin.ui.input 
                    name="allowedIps" 
                    label="Whitelist IP (Opsional)" 
                    wire:model="allowedIps"
                    placeholder="Dipisah koma"
                />
            </div>

            {{-- Tier Description Preview --}}
            @if(isset($tiers[$tokenTier]))
                <div class="mt-4 p-4 rounded-xl bg-gray-50 dark:bg-[#1A1D1F] border border-gray-100 dark:border-[#272B30] text-xs space-y-1">
                    <div class="font-bold text-gray-700 dark:text-[#FCFCFC] flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm text-blue-500">info</span>
                        {{ $tiers[$tokenTier]['label'] }}:
                        <span class="font-normal text-gray-500 dark:text-[#6F767E]">{{ $tiers[$tokenTier]['description'] }}</span>
                    </div>
                    <div class="text-gray-500 dark:text-[#6F767E] flex flex-wrap gap-1 pt-1">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 mr-1">Abilities:</span>
                        @foreach ($tiers[$tokenTier]['abilities'] as $ability)
                            <span class="px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-600 dark:text-blue-400 font-mono text-[11px]">{{ $ability }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <x-admin.ui.button wire:click="createToken" variant="primary">
                    <span class="material-symbols-outlined text-sm mr-1">key</span>
                    Generate MCP Token
                </x-admin.ui.button>
            </div>
        </x-admin.ui.card>

        {{-- Tokens Table --}}
        <x-admin.ui.card padding="p-0" class="overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-[#272B30] flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-[#FCFCFC]">Daftar Token MCP Aktif</h4>
                    <p class="text-xs text-gray-500 dark:text-[#6F767E] mt-0.5">Token dengan awalan [MCP] khusus protokol Model Context Protocol</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-[#1A1D1F] text-gray-600 dark:text-[#6F767E]">
                    {{ $tokens->count() }} Token
                </span>
            </div>

            <x-admin.ui.table>
                <x-slot:thead>
                    <x-admin.ui.table-header>Nama Token</x-admin.ui.table-header>
                    <x-admin.ui.table-header>Prefix Hash</x-admin.ui.table-header>
                    <x-admin.ui.table-header>Abilities</x-admin.ui.table-header>
                    <x-admin.ui.table-header>Rate Limit</x-admin.ui.table-header>
                    <x-admin.ui.table-header>Terakhir Digunakan</x-admin.ui.table-header>
                    <x-admin.ui.table-header align="right" class="px-8 w-24">Aksi</x-admin.ui.table-header>
                </x-slot:thead>

                @forelse ($tokens as $t)
                    <x-admin.ui.table-row>
                        <x-admin.ui.table-cell class="font-semibold text-gray-900 dark:text-[#FCFCFC]">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base text-purple-500">smart_toy</span>
                                {{ $t->name }}
                            </div>
                        </x-admin.ui.table-cell>
                        <x-admin.ui.table-cell class="font-mono text-xs text-gray-500">{{ $t->prefix }}…</x-admin.ui.table-cell>
                        <x-admin.ui.table-cell>
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @if (in_array('*', $t->abilities ?? []))
                                    <span class="px-1.5 py-0.5 rounded bg-red-500/10 text-red-600 dark:text-red-400 font-mono text-[10px]">Full Access (*)</span>
                                @else
                                    @foreach (array_slice($t->abilities ?? [], 0, 3) as $ab)
                                        <span class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-[#1A1D1F] text-gray-600 dark:text-gray-300 font-mono text-[10px]">{{ $ab }}</span>
                                    @endforeach
                                    @if (count($t->abilities ?? []) > 3)
                                        <span class="text-[10px] text-gray-400 self-center">+{{ count($t->abilities) - 3 }}</span>
                                    @endif
                                @endif
                            </div>
                        </x-admin.ui.table-cell>
                        <x-admin.ui.table-cell class="text-xs">{{ $t->rate_limit_per_minute }}/menit</x-admin.ui.table-cell>
                        <x-admin.ui.table-cell class="text-xs text-gray-500 dark:text-[#6F767E]">
                            {{ $t->last_used_at ? $t->last_used_at->diffForHumans() : 'Belum pernah' }}
                        </x-admin.ui.table-cell>
                        <x-admin.ui.table-cell align="right" class="px-8">
                            <x-admin.ui.button 
                                type="button" 
                                variant="danger" 
                                wire:click="revokeToken({{ $t->id }})" 
                                wire:confirm="Yakin ingin mencabut token ini? Klien AI yang menggunakannya akan langsung terputus." 
                                class="!py-1.5 !px-3 text-xs"
                            >
                                Revoke
                            </x-admin.ui.button>
                        </x-admin.ui.table-cell>
                    </x-admin.ui.table-row>
                @empty
                    <x-admin.ui.table-row>
                        <x-admin.ui.table-cell colspan="6" class="p-8 text-center text-gray-500 dark:text-[#6F767E]">
                            <div class="flex flex-col items-center">
                                <span class="material-symbols-outlined text-4xl text-[#6F767E] mb-2">smart_toy</span>
                                <span>Belum ada token MCP yang dibuat. Buat token pertama di atas.</span>
                            </div>
                        </x-admin.ui.table-cell>
                    </x-admin.ui.table-row>
                @endforelse
            </x-admin.ui.table>
        </x-admin.ui.card>
    @endif

    {{-- TAB 2: ANALYTICS & LOGS --}}
    @if ($activeTab === 'analytics')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-admin.ui.card padding="p-6">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total MCP Tokens</div>
                <div class="text-3xl font-extrabold text-gray-900 dark:text-[#FCFCFC] mt-2">{{ $stats['total_tokens'] }}</div>
                <div class="text-xs text-green-500 mt-1">{{ $stats['active_tokens'] }} aktif 7 hari terakhir</div>
            </x-admin.ui.card>

            <x-admin.ui.card padding="p-6">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Operasi MCP (30 Hari)</div>
                <div class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-2">{{ $stats['total_operations_30d'] }}</div>
                <div class="text-xs text-gray-400 mt-1">Total create / update / delete via AI</div>
            </x-admin.ui.card>

            <x-admin.ui.card padding="p-6">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe Operasi</div>
                <div class="flex items-center gap-3 mt-3">
                    @foreach ($stats['operations_by_action'] as $act => $cnt)
                        <span class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ $act === 'created' ? 'bg-green-500/10 text-green-600' : ($act === 'updated' ? 'bg-blue-500/10 text-blue-600' : 'bg-red-500/10 text-red-600') }}">
                            {{ ucfirst($act) }}: {{ $cnt }}
                        </span>
                    @endforeach
                    @if (empty($stats['operations_by_action']))
                        <span class="text-xs text-gray-400">Belum ada mutasi data via AI</span>
                    @endif
                </div>
            </x-admin.ui.card>
        </div>

        {{-- Activity Log Table --}}
        <x-admin.ui.card padding="p-0" class="overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-[#272B30]">
                <h4 class="font-bold text-gray-900 dark:text-[#FCFCFC]">Audit Log MCP Terbaru</h4>
                <p class="text-xs text-gray-500 dark:text-[#6F767E] mt-0.5">Riwayat 20 eksekusi tool mutasi data via MCP</p>
            </div>

            <x-admin.ui.table>
                <x-slot:thead>
                    <x-admin.ui.table-header>Waktu</x-admin.ui.table-header>
                    <x-admin.ui.table-header>Aksi</x-admin.ui.table-header>
                    <x-admin.ui.table-header>Deskripsi</x-admin.ui.table-header>
                    <x-admin.ui.table-header>User / Author</x-admin.ui.table-header>
                    <x-admin.ui.table-header>IP Address</x-admin.ui.table-header>
                </x-slot:thead>

                @forelse ($recentActivity as $act)
                    <x-admin.ui.table-row>
                        <x-admin.ui.table-cell class="text-xs text-gray-500 font-mono">{{ $act->created_at?->diffForHumans() }}</x-admin.ui.table-cell>
                        <x-admin.ui.table-cell>
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase {{ $act->action === 'created' ? 'bg-green-500/10 text-green-600' : ($act === 'updated' ? 'bg-blue-500/10 text-blue-600' : 'bg-red-500/10 text-red-600') }}">
                                {{ $act->action }}
                            </span>
                        </x-admin.ui.table-cell>
                        <x-admin.ui.table-cell class="text-sm font-medium text-gray-800 dark:text-gray-200">
                            {{ $act->description }}
                        </x-admin.ui.table-cell>
                        <x-admin.ui.table-cell class="text-xs text-gray-500">{{ $act->user?->name ?? 'System' }}</x-admin.ui.table-cell>
                        <x-admin.ui.table-cell class="text-xs font-mono text-gray-400">{{ $act->ip_address ?? '-' }}</x-admin.ui.table-cell>
                    </x-admin.ui.table-row>
                @empty
                    <x-admin.ui.table-row>
                        <x-admin.ui.table-cell colspan="5" class="p-8 text-center text-gray-500 dark:text-[#6F767E]">
                            Belum ada log operasi MCP tercatat.
                        </x-admin.ui.table-cell>
                    </x-admin.ui.table-row>
                @endforelse
            </x-admin.ui.table>
        </x-admin.ui.card>
    @endif

    {{-- TAB 3: DOCUMENTATION & CONNECTION GUIDE --}}
    @if ($activeTab === 'docs')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Cursor / Windsurf / Antigravity Setup --}}
            <x-admin.ui.card padding="p-8" class="space-y-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-[#FCFCFC] flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-500">terminal</span>
                    Konfigurasi MCP Client (Cursor / Windsurf / Antigravity)
                </h3>
                <p class="text-xs text-gray-600 dark:text-[#6F767E]">
                    Tambahkan konfigurasi berikut ke file pengaturan MCP client Anda (misal: <code>~/.cursor/mcp.json</code> atau settings client).
                </p>

                <div class="relative">
                    <pre class="p-4 rounded-xl bg-gray-950 text-gray-200 text-xs font-mono overflow-x-auto leading-relaxed">{
  "mcpServers": {
    "cti-cms": {
      "url": "{{ url('/mcp/cms') }}",
      "headers": {
        "Authorization": "Bearer &lt;TOKEN_MCP_ANDA&gt;"
      }
    }
  }
}</pre>
                </div>

                <div class="p-4 rounded-xl bg-blue-500/10 text-xs text-blue-700 dark:text-blue-300 space-y-1">
                    <p class="font-bold">Protokol Endpoint:</p>
                    <p>• <strong>POST /mcp/cms</strong> — JSON-RPC 2.0 endpoint</p>
                    <p>• <strong>GET /mcp/cms</strong> — Server-Sent Events (SSE) streaming</p>
                    <p>• <strong>DELETE /mcp/cms</strong> — Session termination</p>
                </div>
            </x-admin.ui.card>

            {{-- Content Feed API --}}
            <x-admin.ui.card padding="p-8" class="space-y-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-[#FCFCFC] flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">feed</span>
                    Content Feed API (Untuk Chatbot Tanpa Crawling)
                </h3>
                <p class="text-xs text-gray-600 dark:text-[#6F767E]">
                    Menyajikan seluruh konten published (Pages, CPT, Posts) dalam format flat JSON yang siap dikonsumsi LLM / RAG pipeline.
                </p>

                <div class="p-4 rounded-xl bg-gray-950 text-gray-200 text-xs font-mono overflow-x-auto">
                    <div class="text-gray-400"># Request Full Feed (English)</div>
                    <div>GET {{ url('/api/v1/content-feed/en') }}</div>
                    <div class="mt-2 text-gray-400"># Incremental Sync (Konten berubah sejak tanggal)</div>
                    <div>GET {{ url('/api/v1/content-feed/en?since=2026-09-01') }}</div>
                </div>

                <div class="p-4 rounded-xl bg-emerald-500/10 text-xs text-emerald-700 dark:text-emerald-300 space-y-1">
                    <p class="font-bold">Keunggulan Feed Ini:</p>
                    <p>• <strong>Zero-crawling</strong> — tidak membebani server dengan scraper</p>
                    <p>• <strong>Clean Text</strong> — seluruh tag HTML & aset media otomatis di-strip</p>
                    <p>• <strong>Sync Incremental</strong> — parameter <code>?since=</code> hanya mengembalikan delta perubahan</p>
                </div>
            </x-admin.ui.card>
        </div>
    @endif
</div>
