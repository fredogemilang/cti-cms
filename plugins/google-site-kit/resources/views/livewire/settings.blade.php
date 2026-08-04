<div class="space-y-6">
    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Setup Instructions --}}
    <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-3">Google API Connection Setup</h3>
        <p class="text-sm text-[#9CA3AF] mb-4">
            To query stats directly from your Google Search Console and Google Analytics accounts, you must create credentials in the Google Cloud Console.
        </p>
        <ol class="list-decimal pl-5 text-sm text-[#9CA3AF] space-y-2">
            <li>Go to the <a href="https://console.cloud.google.com/" target="_blank" class="text-indigo-400 hover:underline">Google Cloud Console</a>.</li>
            <li>Create a new project or select an existing one.</li>
            <li>Enable the <strong>Google Search Console API</strong> and <strong>Google Analytics Data API</strong>.</li>
            <li>Under <strong>Credentials</strong>, create an <strong>OAuth 2.0 Client ID</strong> (Application Type: Web Application).</li>
            <li>Add the Authorized Redirect URI below to your client configuration.</li>
        </ol>
        
        <div class="mt-4 p-3 bg-[#111] rounded-xl border border-[#272B30] flex items-center justify-between">
            <div>
                <span class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Authorized Redirect URI</span>
                <code class="text-xs text-indigo-400">{{ route('admin.google-site-kit.callback') }}</code>
            </div>
        </div>
    </div>

    {{-- Connection Panel --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Form fields --}}
        <div class="md:col-span-2 bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6 space-y-6">
            <h3 class="text-md font-bold text-white">OAuth Credentials</h3>
            
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">OAuth Client ID</label>
                    <input type="text" wire:model.defer="clientId" class="w-full bg-[#111] border border-[#272B30] rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-indigo-500" placeholder="Paste your Google OAuth Client ID">
                    @error('clientId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">OAuth Client Secret</label>
                    <input type="password" wire:model.defer="clientSecret" class="w-full bg-[#111] border border-[#272B30] rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-indigo-500" placeholder="••••••••••••••••">
                    @error('clientSecret') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Google Analytics 4 Property ID</label>
                    <input type="text" wire:model.defer="propertyId" class="w-full bg-[#111] border border-[#272B30] rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g. 123456789">
                    <p class="text-xs text-[#6F767E] mt-1">Can be found in GA4 Admin → Property Settings → Property Details.</p>
                    @error('propertyId') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-3 rounded-xl transition">
                        Save Credentials
                    </button>
                </div>
            </form>
        </div>

        {{-- Connection status --}}
        <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-md font-bold text-white mb-4">Connection Status</h3>
                
                @if ($isConnected)
                    <div class="flex items-center gap-3 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-sm font-semibold text-emerald-400">Connected</span>
                    </div>
                    <p class="text-xs text-[#9CA3AF] mt-3">
                        CMS is connected to your Google account and fetching active statistics.
                    </p>
                @else
                    <div class="flex items-center gap-3 p-3 bg-rose-500/10 border border-rose-500/20 rounded-xl">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-sm font-semibold text-rose-400">Disconnected</span>
                    </div>
                    <p class="text-xs text-[#9CA3AF] mt-3">
                        CMS is operating in <strong>Mock/Sandbox Mode</strong>. Fill the client credentials and connect your account to view live production data.
                    </p>
                @endif
            </div>

            <div class="pt-6">
                @if ($isConnected)
                    <form action="{{ route('admin.google-site-kit.disconnect') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-[#272B30] hover:bg-rose-900/20 hover:text-rose-400 border border-[#272B30] hover:border-rose-500/20 text-white font-semibold text-sm py-3 rounded-xl transition">
                            Disconnect Account
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.google-site-kit.connect') }}" class="block text-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm py-3 rounded-xl transition">
                        Connect Google Account
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
