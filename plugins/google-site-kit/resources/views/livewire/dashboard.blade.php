<div class="space-y-6">
    {{-- Mock Alert --}}
    @if (!$isConnected)
        <div class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-[#9CA3AF] text-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-indigo-400">info</span>
                <span>Operating in <strong>Mock/Sandbox Mode</strong>. Connect Google account in <a href="{{ route('admin.google-site-kit.settings') }}" class="text-indigo-400 hover:underline font-semibold">Settings</a> to see live data.</span>
            </div>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex border-b border-[#272B30] gap-4">
        <button wire:click="switchTab('analytics')" class="pb-3 text-sm font-bold transition-all relative {{ $activeTab === 'analytics' ? 'text-white border-b-2 border-indigo-500' : 'text-[#6F767E] hover:text-white' }}">
            Google Analytics 4
        </button>
        <button wire:click="switchTab('search-console')" class="pb-3 text-sm font-bold transition-all relative {{ $activeTab === 'search-console' ? 'text-white border-b-2 border-indigo-500' : 'text-[#6F767E] hover:text-white' }}">
            Google Search Console
        </button>
    </div>

    {{-- Stats Cards & Chart --}}
    @if ($activeTab === 'analytics')
        {{-- GA4 Metrics --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-5">
                <span class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Total Users</span>
                <span class="text-2xl font-extrabold text-white">{{ number_format($gaData['users'] ?? 0) }}</span>
            </div>
            <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-5">
                <span class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Sessions</span>
                <span class="text-2xl font-extrabold text-white">{{ number_format($gaData['sessions'] ?? 0) }}</span>
            </div>
            <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-5">
                <span class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Pageviews</span>
                <span class="text-2xl font-extrabold text-white">{{ number_format($gaData['pageviews'] ?? 0) }}</span>
            </div>
            <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-5">
                <span class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Bounce Rate</span>
                <span class="text-2xl font-extrabold text-white">{{ $gaData['bounce_rate'] ?? 0 }}%</span>
            </div>
        </div>

        {{-- SVG Chart --}}
        <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6">
            <h4 class="text-sm font-bold text-white mb-4">Traffic Performance (Last 30 Days)</h4>
            
            @php
                $chartPoints = $gaData['chart'] ?? [];
                $maxVal = count($chartPoints) ? max(array_column($chartPoints, 'users')) : 100;
                $minVal = count($chartPoints) ? min(array_column($chartPoints, 'users')) : 0;
                $range = $maxVal - $minVal ?: 1;
                
                $points = "";
                $areaPoints = "0,250 ";
                $svgWidth = 1000;
                $svgHeight = 250;
                $totalPoints = count($chartPoints);
                
                foreach ($chartPoints as $index => $point) {
                    $x = ($index / max($totalPoints - 1, 1)) * $svgWidth;
                    $y = $svgHeight - (($point['users'] - $minVal) / $range * 200) - 25;
                    $points .= "$x,$y ";
                    $areaPoints .= "$x,$y ";
                }
                $areaPoints .= "$svgWidth,$svgHeight";
            @endphp

            <div class="relative w-full h-[250px]">
                <svg viewBox="0 0 1000 250" class="w-full h-full" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#6366f1" stop-opacity="0.3"/>
                            <stop offset="100%" stop-color="#6366f1" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    
                    {{-- Grid Lines --}}
                    <line x1="0" y1="50" x2="1000" y2="50" stroke="#272B30" stroke-dasharray="4"/>
                    <line x1="0" y1="125" x2="1000" y2="125" stroke="#272B30" stroke-dasharray="4"/>
                    <line x1="0" y1="200" x2="1000" y2="200" stroke="#272B30" stroke-dasharray="4"/>

                    @if(count($chartPoints))
                        {{-- Filled area --}}
                        <polygon points="{{ $areaPoints }}" fill="url(#chartGrad)"/>
                        {{-- Line --}}
                        <polyline points="{{ $points }}" fill="none" stroke="#6366f1" stroke-width="3"/>
                    @endif
                </svg>
            </div>
        </div>

    @else
        {{-- Search Console Metrics --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-5">
                <span class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Total Clicks</span>
                <span class="text-2xl font-extrabold text-white">{{ number_format($scData['clicks'] ?? 0) }}</span>
            </div>
            <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-5">
                <span class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Impressions</span>
                <span class="text-2xl font-extrabold text-white">{{ number_format($scData['impressions'] ?? 0) }}</span>
            </div>
            <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-5">
                <span class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Average CTR</span>
                <span class="text-2xl font-extrabold text-white">{{ $scData['ctr'] ?? 0 }}%</span>
            </div>
            <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-5">
                <span class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider mb-2">Average Position</span>
                <span class="text-2xl font-extrabold text-white">{{ $scData['position'] ?? 0 }}</span>
            </div>
        </div>

        {{-- SVG Chart --}}
        <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6">
            <h4 class="text-sm font-bold text-white mb-4">Search Clicks Performance (Last 30 Days)</h4>
            
            @php
                $chartPoints = $scData['chart'] ?? [];
                $maxVal = count($chartPoints) ? max(array_column($chartPoints, 'clicks')) : 100;
                $minVal = count($chartPoints) ? min(array_column($chartPoints, 'clicks')) : 0;
                $range = $maxVal - $minVal ?: 1;
                
                $points = "";
                $areaPoints = "0,250 ";
                $svgWidth = 1000;
                $svgHeight = 250;
                $totalPoints = count($chartPoints);
                
                foreach ($chartPoints as $index => $point) {
                    $x = ($index / max($totalPoints - 1, 1)) * $svgWidth;
                    $y = $svgHeight - (($point['clicks'] - $minVal) / $range * 200) - 25;
                    $points .= "$x,$y ";
                    $areaPoints .= "$x,$y ";
                }
                $areaPoints .= "$svgWidth,$svgHeight";
            @endphp

            <div class="relative w-full h-[250px]">
                <svg viewBox="0 0 1000 250" class="w-full h-full" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="scGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#10b981" stop-opacity="0.3"/>
                            <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    
                    {{-- Grid Lines --}}
                    <line x1="0" y1="50" x2="1000" y2="50" stroke="#272B30" stroke-dasharray="4"/>
                    <line x1="0" y1="125" x2="1000" y2="125" stroke="#272B30" stroke-dasharray="4"/>
                    <line x1="0" y1="200" x2="1000" y2="200" stroke="#272B30" stroke-dasharray="4"/>

                    @if(count($chartPoints))
                        {{-- Filled area --}}
                        <polygon points="{{ $areaPoints }}" fill="url(#scGrad)"/>
                        {{-- Line --}}
                        <polyline points="{{ $points }}" fill="none" stroke="#10b981" stroke-width="3"/>
                    @endif
                </svg>
            </div>
        </div>
    @endif

    {{-- PageSpeed Insights --}}
    <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h4 class="text-sm font-bold text-white">PageSpeed Insights</h4>
                <p class="text-xs text-[#6F767E] mt-0.5">Real-time performance scores of your website homepage.</p>
            </div>
            
            <button wire:click="refreshSpeed" class="flex items-center gap-2 bg-[#272B30] hover:bg-[#32373F] text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition" {{ $loadingSpeed ? 'disabled' : '' }}>
                @if ($loadingSpeed)
                    <span class="inline-block animate-spin w-3 h-3 border-2 border-white border-t-transparent rounded-full"></span>
                    <span>Analyzing...</span>
                @else
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    <span>Run Analysis</span>
                @endif
            </button>
        </div>

        @if (session()->has('speed_success'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs">
                {{ session('speed_success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Mobile --}}
            <div class="flex items-center gap-6 p-4 bg-[#111] rounded-2xl border border-[#272B30]">
                {{-- SVG Gauge Circle --}}
                @php
                    $mobileScore = $speedData['mobile'] ?? 0;
                    $mobileColor = $mobileScore >= 90 ? '#10b981' : ($mobileScore >= 50 ? '#f59e0b' : '#ef4444');
                    $offset = 251.2 - (251.2 * $mobileScore / 100);
                @endphp
                <div class="relative w-24 h-24 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="48" cy="48" r="40" stroke="#272B30" stroke-width="8" fill="transparent"/>
                        <circle cx="48" cy="48" r="40" stroke="{{ $mobileColor }}" stroke-width="8" fill="transparent"
                                stroke-dasharray="251.2" stroke-dashoffset="{{ $offset }}" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute text-xl font-black text-white">{{ $mobileScore }}</span>
                </div>
                <div>
                    <span class="flex items-center gap-2 text-sm font-bold text-white">
                        <span class="material-symbols-outlined text-[18px]">phone_iphone</span>
                        <span>Mobile Performance</span>
                    </span>
                    <span class="block text-xs text-[#6F767E] mt-1">Lighthouse metrics benchmark for standard mobile networks.</span>
                </div>
            </div>

            {{-- Desktop --}}
            <div class="flex items-center gap-6 p-4 bg-[#111] rounded-2xl border border-[#272B30]">
                {{-- SVG Gauge Circle --}}
                @php
                    $desktopScore = $speedData['desktop'] ?? 0;
                    $desktopColor = $desktopScore >= 90 ? '#10b981' : ($desktopScore >= 50 ? '#f59e0b' : '#ef4444');
                    $offset = 251.2 - (251.2 * $desktopScore / 100);
                @endphp
                <div class="relative w-24 h-24 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="48" cy="48" r="40" stroke="#272B30" stroke-width="8" fill="transparent"/>
                        <circle cx="48" cy="48" r="40" stroke="{{ $desktopColor }}" stroke-width="8" fill="transparent"
                                stroke-dasharray="251.2" stroke-dashoffset="{{ $offset }}" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute text-xl font-black text-white">{{ $desktopScore }}</span>
                </div>
                <div>
                    <span class="flex items-center gap-2 text-sm font-bold text-white">
                        <span class="material-symbols-outlined text-[18px]">desktop_windows</span>
                        <span>Desktop Performance</span>
                    </span>
                    <span class="block text-xs text-[#6F767E] mt-1">Lighthouse metrics benchmark for high-speed fiber networks.</span>
                </div>
            </div>
        </div>
    </div>
</div>
