<div class="space-y-6">
    {{-- 1. Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold tracking-tight text-[#111827] dark:text-[#FCFCFC]">Instant Indexing Suite</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">IndexNow + Google API</span>
            </div>
            <p class="text-sm font-normal text-[#6F767E] dark:text-[#9A9FA5] mt-1">Real-time push change notifications sent to search engines (Google, Bing, Yandex, Seznam).</p>
        </div>
    </div>

    {{-- 2. Tab Navigation Bar --}}
    <div class="border-b border-gray-200 dark:border-[#272B30] flex items-center gap-2 overflow-x-auto pb-px">
        <button type="button" wire:click="setTab('indexnow')" class="px-5 py-3 text-sm font-bold border-b-2 transition-all inline-flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'indexnow' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
            <span class="material-symbols-outlined text-lg">bolt</span>
            <span>IndexNow Protocol</span>
            @if($indexNowEnabled)
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            @endif
        </button>

        <button type="button" wire:click="setTab('google')" class="px-5 py-3 text-sm font-bold border-b-2 transition-all inline-flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'google' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
            <span class="material-symbols-outlined text-lg">travel_explore</span>
            <span>Google Indexing API</span>
            @if($googleEnabled && $isGoogleConfigured)
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            @elseif($googleEnabled)
                <span class="w-2 h-2 rounded-full bg-amber-500" title="Missing credentials"></span>
            @endif
        </button>

        <button type="button" wire:click="setTab('logs')" class="px-5 py-3 text-sm font-bold border-b-2 transition-all inline-flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'logs' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC]' }}">
            <span class="material-symbols-outlined text-lg">view_list</span>
            <span>Batch Submit & Activity Logs</span>
        </button>
    </div>

    {{-- TAB 1: INDEXNOW PROTOCOL --}}
    @if($activeTab === 'indexnow')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 space-y-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
                        <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">IndexNow Configuration</h3>
                        <button type="button" wire:click="saveIndexNowSettings" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">
                            Save IndexNow Settings
                        </button>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Enable IndexNow Protocol</h4>
                            <p class="text-xs text-[#6F767E] mt-0.5">Allows sending instant change notifications to IndexNow search engine partners.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="indexNowEnabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Automatic Submit on Publish & Update</h4>
                            <p class="text-xs text-[#6F767E] mt-0.5">Automatically trigger pings whenever Pages, Posts, or CPT entries are published, updated, or deleted.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="indexNowAutoPing" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 space-y-4 shadow-sm">
                    <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] border-b border-gray-100 dark:border-[#272B30] pb-4">API Key & Verification</h3>

                    <div class="space-y-3">
                        <label class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block">API Key</label>
                        <input type="text" value="{{ $indexNowKey }}" readonly class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-mono font-bold text-[#111827] dark:text-[#FCFCFC] px-4">
                        
                        <button type="button" wire:click="regenerateIndexNowKey" class="w-full py-2 rounded-xl bg-gray-100 dark:bg-[#272B30] hover:bg-gray-200 text-xs font-bold text-[#111827] dark:text-[#FCFCFC] transition-colors">
                            Regenerate Key
                        </button>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-[#272B30] space-y-2">
                        <span class="text-xs font-semibold text-[#6F767E] block">Verification File:</span>
                        <a href="{{ url('/indexnow-' . $indexNowKey . '.txt') }}" target="_blank" class="text-xs font-bold text-blue-600 hover:underline block truncate">
                            /indexnow-{{ substr($indexNowKey, 0, 8) }}....txt <span class="material-symbols-outlined text-xs inline">open_in_new</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 2: GOOGLE INDEXING API --}}
    @if($activeTab === 'google')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 space-y-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-[#272B30] pb-4">
                        <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Google Indexing API Configuration</h3>
                        <button type="button" wire:click="saveGoogleSettings" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors">
                            Save Google Settings
                        </button>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Enable Google Indexing API</h4>
                            <p class="text-xs text-[#6F767E] mt-0.5">Allows sending instant page index requests directly to Google Indexing API v3.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="googleEnabled" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Automatic Submit on Publish & Update</h4>
                            <p class="text-xs text-[#6F767E] mt-0.5">Automatically trigger pings to Google whenever Pages, Posts, or CPT entries are updated or deleted.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="googleAutoPing" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    {{-- Credentials Mode Selector --}}
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Service Account Credentials (Encrypted Storage)</h4>
                            <div class="p-1 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] flex gap-1 text-xs">
                                <button type="button" wire:click="$set('credentialMode', 'json')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $credentialMode === 'json' ? 'bg-white dark:bg-[#272B30] text-blue-600 dark:text-blue-400 shadow-sm' : 'text-[#6F767E]' }}">
                                    Copy-Paste JSON
                                </button>
                                <button type="button" wire:click="$set('credentialMode', 'fields')" class="px-3 py-1.5 rounded-lg font-bold transition-all {{ $credentialMode === 'fields' ? 'bg-white dark:bg-[#272B30] text-blue-600 dark:text-blue-400 shadow-sm' : 'text-[#6F767E]' }}">
                                    Manual Fields
                                </button>
                            </div>
                        </div>

                        @if($credentialMode === 'json')
                            <div class="space-y-2">
                                <label class="text-xs font-semibold text-[#6F767E] block">Paste Raw Service Account JSON Content</label>
                                <textarea wire:model="jsonInput" rows="6" placeholder='{ "type": "service_account", "project_id": "...", "private_key": "-----BEGIN PRIVATE KEY-----\n...", "client_email": "..." }' class="w-full rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none p-4 text-xs font-mono text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500"></textarea>
                                <p class="text-[11px] text-[#6F767E]">The JSON text will be automatically encrypted in the database before saving.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-[#6F767E] block">Client Email</label>
                                    <input type="email" wire:model="clientEmailInput" placeholder="my-service-account@project.iam.gserviceaccount.com" class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-mono text-[#111827] dark:text-[#FCFCFC] px-4">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-[#6F767E] block">Private Key (RSA)</label>
                                    <textarea wire:model="privateKeyInput" rows="5" placeholder="-----BEGIN PRIVATE KEY-----&#10;MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC...&#10;-----END PRIVATE KEY-----" class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none p-4 text-xs font-mono text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Service Account Status Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 space-y-4 shadow-sm">
                    <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC] border-b border-gray-100 dark:border-[#272B30] pb-4">Service Account Status</h3>

                    @if($isGoogleConfigured)
                        <div class="p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-xl">check_circle</span>
                            <div>
                                <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 block">Service Account Configured</span>
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 block truncate max-w-[200px]">{{ $clientEmailInput }}</span>
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-2xl bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 flex items-center gap-3">
                            <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-xl">warning</span>
                            <div>
                                <span class="text-xs font-bold text-amber-800 dark:text-amber-300 block">Not Configured</span>
                                <span class="text-[10px] text-amber-600 dark:text-amber-400 block">Paste Service Account JSON to activate</span>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-2 text-xs text-[#6F767E] pt-2">
                        <span class="font-bold text-[#111827] dark:text-[#FCFCFC] block">Daily API Quota Tracker:</span>
                        <div class="flex items-center justify-between text-xs font-mono">
                            <span>Requests Sent Today:</span>
                            <span class="font-bold text-blue-600">{{ $googleRequestsToday }} / 200</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-gray-100 dark:bg-[#272B30] overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full" style="width: {{ min(100, ($googleRequestsToday / 200) * 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 3: BATCH SUBMIT & ACTIVITY LOGS --}}
    @if($activeTab === 'logs')
        <div class="space-y-6">
            {{-- Batch Submit Box --}}
            <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 space-y-4 shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Manual Batch URL Submission</h3>
                    <p class="text-xs text-[#6F767E] mt-0.5">Paste full URLs below (one per line) to immediately push change notifications to search engines.</p>
                </div>

                <div class="space-y-3">
                    <textarea wire:model="manualUrlsInput" rows="5" placeholder="https://example.com/page-1&#10;https://example.com/page-2" class="w-full rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none p-4 text-xs font-mono text-[#111827] dark:text-[#FCFCFC] focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                    <div class="flex items-center gap-4 text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">
                        <span>Target Services:</span>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" wire:model="submitIndexNow" class="rounded text-blue-600 focus:ring-blue-500">
                            <span>IndexNow (Bing/Yandex)</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" wire:model="submitGoogle" class="rounded text-blue-600 focus:ring-blue-500">
                            <span>Google Indexing API</span>
                        </label>
                    </div>

                    <button type="button" wire:click="submitManualBatch" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-base">send</span>
                        Submit Batch URLs Now
                    </button>
                </div>
            </div>

            {{-- Activity Logs Datatable --}}
            <div class="bg-white dark:bg-[#1A1A1A] rounded-3xl p-6 space-y-4 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 dark:border-[#272B30] pb-4">
                    <h3 class="text-lg font-bold text-[#111827] dark:text-[#FCFCFC]">Submission Activity Logs</h3>

                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="exportLogsCsv" class="px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 text-xs font-bold transition-colors inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base">download</span>
                            Export CSV
                        </button>
                        <button type="button" wire:click="resetFilters" class="px-3 py-2 rounded-xl bg-gray-100 dark:bg-[#272B30] hover:bg-gray-200 text-xs font-semibold transition-colors">
                            Reset Filters
                        </button>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B]">
                    <div>
                        <label class="text-[10px] font-bold text-[#6F767E] uppercase block mb-1">From Date</label>
                        <input type="date" wire:model.live="dateFrom" class="w-full h-9 rounded-xl bg-white dark:bg-[#1A1A1A] border-none text-xs text-[#111827] dark:text-[#FCFCFC] px-3">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-[#6F767E] uppercase block mb-1">To Date</label>
                        <input type="date" wire:model.live="dateTo" class="w-full h-9 rounded-xl bg-white dark:bg-[#1A1A1A] border-none text-xs text-[#111827] dark:text-[#FCFCFC] px-3">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-[#6F767E] uppercase block mb-1">Status</label>
                        <select wire:model.live="statusFilter" class="w-full h-9 rounded-xl bg-white dark:bg-[#1A1A1A] border-none text-xs text-[#111827] dark:text-[#FCFCFC] px-3">
                            <option value="">All Statuses</option>
                            <option value="success">Success (200 OK)</option>
                            <option value="error">Error / Failed</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-[#6F767E] uppercase block mb-1">Protocol</label>
                        <select wire:model.live="protocolFilter" class="w-full h-9 rounded-xl bg-white dark:bg-[#1A1A1A] border-none text-xs text-[#111827] dark:text-[#FCFCFC] px-3">
                            <option value="">All Protocols</option>
                            <option value="indexnow">IndexNow</option>
                            <option value="google">Google API</option>
                        </select>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-[#272B30] text-[#6F767E] uppercase font-bold text-[10px]">
                                <th class="py-3 px-2">Protocol</th>
                                <th class="py-3 px-2">URL</th>
                                <th class="py-3 px-2">Status</th>
                                <th class="py-3 px-2">Response Details</th>
                                <th class="py-3 px-2">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#272B30]">
                            @forelse($logs as $log)
                                <tr>
                                    <td class="py-3 px-2 font-bold">
                                        @if($log->protocol === 'google')
                                            <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 font-mono text-[10px]">GOOGLE</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 font-mono text-[10px]">INDEXNOW</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2 font-mono text-[#111827] dark:text-[#FCFCFC] max-w-[250px] truncate" title="{{ $log->url }}">
                                        {{ $log->url }}
                                    </td>
                                    <td class="py-3 px-2">
                                        @if($log->status_code === 200 || $log->status_code === 202)
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 font-bold text-[10px]">
                                                {{ $log->status_code }} OK
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 font-bold text-[10px]">
                                                {{ $log->status_code ?: 'ERROR' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2 font-mono text-[#6F767E] max-w-[200px] truncate">
                                        {{ Str::limit($log->response, 60) }}
                                    </td>
                                    <td class="py-3 px-2 text-[#6F767E] whitespace-nowrap">
                                        {{ $log->request_time ? $log->request_time->diffForHumans() : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-[#6F767E]">
                                        No indexing activity logs recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="pt-2">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
