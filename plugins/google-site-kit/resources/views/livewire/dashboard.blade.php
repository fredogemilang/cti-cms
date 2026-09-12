<div class="space-y-8">
    @php
        $rangeLabels = [
            '7days' => 'Last 7 days',
            '14days' => 'Last 14 days',
            '28days' => 'Last 28 days',
            '90days' => 'Last 90 days',
        ];
        $currentRangeLabel = $rangeLabels[$dateRange] ?? 'Last 28 days';
    @endphp

    {{-- Top Action Bar: Branding, Date Range Selector & Status --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-5">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500/20 via-emerald-500/10 to-amber-500/10 border border-[#272B30] flex items-center justify-center shadow-inner">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="text-lg font-extrabold text-white tracking-tight">Google Site Kit Dashboard</h2>
                    @if ($isConnected)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Connected
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            Sandbox Data
                        </span>
                    @endif
                </div>
                <p class="text-xs text-[#6F767E] mt-0.5">Unified Search Console, GA4 & PageSpeed Insights performance.</p>
            </div>
        </div>

        {{-- Controls: Date Range Selector & Settings link --}}
        <div class="flex items-center flex-wrap gap-2.5">
            {{-- Date Range Toggle Pills --}}
            <div class="inline-flex items-center p-1 bg-[#111111] border border-[#272B30] rounded-xl text-xs font-semibold">
                @foreach (['7days' => '7D', '14days' => '14D', '28days' => '28D', '90days' => '90D'] as $key => $shortLabel)
                    <button wire:click="changeDateRange('{{ $key }}')"
                            class="px-3 py-1.5 rounded-lg transition-all {{ $dateRange === $key ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'text-[#6F767E] hover:text-white hover:bg-[#272B30]/50' }}">
                        {{ $shortLabel }}
                    </button>
                @endforeach
            </div>

            <a href="{{ route('admin.google-site-kit.settings') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#272B30] hover:bg-[#32373F] text-white text-xs font-semibold rounded-xl transition border border-[#32373F]">
                <span class="material-symbols-outlined text-[16px]">settings</span>
                <span>Settings</span>
            </a>
        </div>
    </div>

    {{-- Sandbox Mode Alert --}}
    @if (!$isConnected)
        <div class="p-4 rounded-2xl bg-gradient-to-r from-indigo-950/40 via-purple-950/30 to-transparent border border-indigo-500/20 text-[#9CA3AF] text-xs flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-indigo-400 text-[18px]">info</span>
                </div>
                <span>Currently displaying <strong>CDT Sandbox / Benchmark Data</strong>. Connect your Google account with OAuth Client ID in <a href="{{ route('admin.google-site-kit.settings') }}" class="text-indigo-400 hover:underline font-bold">Site Kit Settings</a> to stream live Search Console and GA4 metrics.</span>
            </div>
            <a href="{{ route('admin.google-site-kit.settings') }}" class="shrink-0 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-bold transition">
                Configure OAuth
            </a>
        </div>
    @endif

    {{-- 4 Primary KPI Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Visitors --}}
        <div class="bg-[#1A1A1A] border border-[#272B30] hover:border-[#3a4047] transition rounded-2xl p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between text-[#6F767E] mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Total Visitors</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[16px]">group</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white tracking-tight">{{ number_format($funnelData['visitors'] ?? 0) }}</span>
                    @php $visChange = $funnelData['visitors_change'] ?? 0; @endphp
                    <span class="inline-flex items-center text-xs font-bold {{ $visChange >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        <span class="material-symbols-outlined text-[14px]">{{ $visChange >= 0 ? 'arrow_upward' : 'arrow_downward' }}</span>
                        {{ abs($visChange) }}%
                    </span>
                </div>
                <p class="text-[11px] text-[#6F767E] mt-1.5">vs previous {{ $currentRangeLabel }} (all channels)</p>
            </div>
        </div>

        {{-- Card 2: Total Impressions --}}
        <div class="bg-[#1A1A1A] border border-[#272B30] hover:border-[#3a4047] transition rounded-2xl p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between text-[#6F767E] mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Search Impressions</span>
                <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white tracking-tight">{{ number_format($funnelData['impressions'] ?? 0) }}</span>
                    @php $impChange = $funnelData['impressions_change'] ?? 0; @endphp
                    <span class="inline-flex items-center text-xs font-bold {{ $impChange >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        <span class="material-symbols-outlined text-[14px]">{{ $impChange >= 0 ? 'arrow_upward' : 'arrow_downward' }}</span>
                        {{ abs($impChange) }}%
                    </span>
                </div>
                <p class="text-[11px] text-[#6F767E] mt-1.5">vs previous {{ $currentRangeLabel }} on Google Search</p>
            </div>
        </div>

        {{-- Card 3: Total Clicks --}}
        <div class="bg-[#1A1A1A] border border-[#272B30] hover:border-[#3a4047] transition rounded-2xl p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between text-[#6F767E] mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Search Clicks</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[16px]">ads_click</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white tracking-tight">{{ number_format($funnelData['clicks'] ?? 0) }}</span>
                    @php $clkChange = $funnelData['clicks_change'] ?? 0; @endphp
                    <span class="inline-flex items-center text-xs font-bold {{ $clkChange >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        <span class="material-symbols-outlined text-[14px]">{{ $clkChange >= 0 ? 'arrow_upward' : 'arrow_downward' }}</span>
                        {{ abs($clkChange) }}%
                    </span>
                </div>
                <p class="text-[11px] text-[#6F767E] mt-1.5">vs previous {{ $currentRangeLabel }} organic traffic</p>
            </div>
        </div>

        {{-- Card 4: CTR & Avg Position --}}
        <div class="bg-[#1A1A1A] border border-[#272B30] hover:border-[#3a4047] transition rounded-2xl p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between text-[#6F767E] mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Average CTR</span>
                <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                </div>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white tracking-tight">{{ $funnelData['ctr'] ?? 0 }}%</span>
                    <span class="text-xs font-bold text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-md border border-indigo-500/20">
                        Avg Pos: {{ $funnelData['position'] ?? 4.1 }}
                    </span>
                </div>
                <p class="text-[11px] text-[#6F767E] mt-1.5">Click-through rate & average SERP position</p>
            </div>
        </div>
    </div>

    {{-- Unified Search Funnel & Trend Chart --}}
    <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-5 border-b border-[#272B30] gap-4">
            <div>
                <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-400 text-[20px]">filter_alt</span>
                    <span>Search Funnel: How your site is doing in Search</span>
                </h3>
                <p class="text-xs text-[#6F767E] mt-0.5">Tracking the visitor flow from Search Impressions down to On-Site engagement ({{ $currentRangeLabel }}).</p>
            </div>
            
            {{-- Chart Legend --}}
            <div class="flex items-center gap-5 text-xs font-semibold">
                <span class="inline-flex items-center gap-1.5 text-white">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#818cf8]"></span>
                    <span>Impressions</span>
                </span>
                <span class="inline-flex items-center gap-1.5 text-white">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#10b981]"></span>
                    <span>Clicks</span>
                </span>
            </div>
        </div>

        {{-- Funnel Stages Steps --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 py-6">
            {{-- Step 1: Impressions --}}
            <div class="p-4 rounded-xl bg-[#111111] border border-[#272B30]/80 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-purple-500/5 rounded-full -mr-6 -mt-6"></div>
                <span class="text-[11px] font-bold text-[#6F767E] uppercase tracking-wider block mb-1">1. Impressions</span>
                <span class="text-2xl font-black text-white block">{{ number_format($funnelData['impressions'] ?? 0) }}</span>
                <span class="text-[11px] text-[#6F767E] mt-1 block">Saw site in search</span>
            </div>

            {{-- Step 2: Clicks --}}
            <div class="p-4 rounded-xl bg-[#111111] border border-[#272B30]/80 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-500/5 rounded-full -mr-6 -mt-6"></div>
                <span class="text-[11px] font-bold text-[#6F767E] uppercase tracking-wider block mb-1">2. Clicks</span>
                <span class="text-2xl font-black text-white block">{{ number_format($funnelData['clicks'] ?? 0) }}</span>
                <span class="text-[11px] text-[#6F767E] mt-1 block">Clicked through link</span>
            </div>

            {{-- Step 3: CTR --}}
            <div class="p-4 rounded-xl bg-[#111111] border border-[#272B30]/80 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-amber-500/5 rounded-full -mr-6 -mt-6"></div>
                <span class="text-[11px] font-bold text-[#6F767E] uppercase tracking-wider block mb-1">3. Click-Through</span>
                <span class="text-2xl font-black text-white block">{{ $funnelData['ctr'] ?? 0 }}%</span>
                <span class="text-[11px] text-[#6F767E] mt-1 block">Click conversion rate</span>
            </div>

            {{-- Step 4: Visitors on Site --}}
            <div class="p-4 rounded-xl bg-[#111111] border border-[#272B30]/80 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-500/5 rounded-full -mr-6 -mt-6"></div>
                <span class="text-[11px] font-bold text-[#6F767E] uppercase tracking-wider block mb-1">4. Search Visitors</span>
                <span class="text-2xl font-black text-white block">{{ number_format($funnelData['visitors'] ?? 0) }}</span>
                <span class="text-[11px] text-[#6F767E] mt-1 block">Users on site from search</span>
            </div>
        </div>

        {{-- High-Fidelity Dual-Line SVG Chart --}}
        @php
            $chartPoints = $funnelData['chart'] ?? [];
            $totalPoints = count($chartPoints);
            $maxImp = $totalPoints ? max(array_column($chartPoints, 'impressions')) : 100;
            $maxClicks = $totalPoints ? max(array_column($chartPoints, 'clicks')) : 10;
            
            $svgWidth = 1000;
            $svgHeight = 240;
            
            $impLine = "";
            $impArea = "0,240 ";
            $clkLine = "";
            $clkArea = "0,240 ";
            
            foreach ($chartPoints as $index => $pt) {
                $x = ($index / max($totalPoints - 1, 1)) * $svgWidth;
                
                // Scale Impressions (top 15% margin)
                $normImp = $maxImp > 0 ? ($pt['impressions'] / $maxImp) : 0;
                $yImp = $svgHeight - ($normImp * 180) - 30;
                $impLine .= "$x,$yImp ";
                $impArea .= "$x,$yImp ";
                
                // Scale Clicks (top 20% margin)
                $normClk = $maxClicks > 0 ? ($pt['clicks'] / $maxClicks) : 0;
                $yClk = $svgHeight - ($normClk * 180) - 30;
                $clkLine .= "$x,$yClk ";
                $clkArea .= "$x,$yClk ";
            }
            $impArea .= "$svgWidth,$svgHeight";
            $clkArea .= "$svgWidth,$svgHeight";
        @endphp

        <div class="relative w-full h-[240px] mt-2">
            <svg viewBox="0 0 1000 240" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="impGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#818cf8" stop-opacity="0.25"/>
                        <stop offset="100%" stop-color="#818cf8" stop-opacity="0"/>
                    </linearGradient>
                    <linearGradient id="clkGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#10b981" stop-opacity="0.3"/>
                        <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                    </linearGradient>
                </defs>

                {{-- Horizontal Grid Lines --}}
                <line x1="0" y1="30" x2="1000" y2="30" stroke="#272B30" stroke-dasharray="4" stroke-opacity="0.7"/>
                <line x1="0" y1="90" x2="1000" y2="90" stroke="#272B30" stroke-dasharray="4" stroke-opacity="0.7"/>
                <line x1="0" y1="150" x2="1000" y2="150" stroke="#272B30" stroke-dasharray="4" stroke-opacity="0.7"/>
                <line x1="0" y1="210" x2="1000" y2="210" stroke="#272B30" stroke-dasharray="4" stroke-opacity="0.7"/>

                @if ($totalPoints > 0)
                    {{-- Area Fill --}}
                    <polygon points="{{ $impArea }}" fill="url(#impGradient)"/>
                    <polygon points="{{ $clkArea }}" fill="url(#clkGradient)"/>

                    {{-- Polylines --}}
                    <polyline points="{{ $impLine }}" fill="none" stroke="#818cf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <polyline points="{{ $clkLine }}" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>

                    {{-- Data Point Dots --}}
                    @foreach ($chartPoints as $index => $pt)
                        @php
                            $x = ($index / max($totalPoints - 1, 1)) * $svgWidth;
                            $yClk = $svgHeight - (($maxClicks > 0 ? ($pt['clicks'] / $maxClicks) : 0) * 180) - 30;
                        @endphp
                        <circle cx="{{ $x }}" cy="{{ $yClk }}" r="3" fill="#10b981" stroke="#111111" stroke-width="2">
                            <title>{{ $pt['label'] }}: {{ number_format($pt['clicks']) }} Clicks, {{ number_format($pt['impressions']) }} Impressions</title>
                        </circle>
                    @endforeach
                @endif
            </svg>
        </div>

        {{-- Date Ticks along the bottom axis --}}
        @if ($totalPoints > 0)
            <div class="flex justify-between items-center text-[10px] font-semibold text-[#6F767E] pt-2 border-t border-[#272B30]/60">
                <span>{{ $chartPoints[0]['label'] ?? '' }}</span>
                @if ($totalPoints > 6)
                    <span>{{ $chartPoints[(int)($totalPoints * 0.25)]['label'] ?? '' }}</span>
                    <span>{{ $chartPoints[(int)($totalPoints * 0.50)]['label'] ?? '' }}</span>
                    <span>{{ $chartPoints[(int)($totalPoints * 0.75)]['label'] ?? '' }}</span>
                @endif
                <span>{{ $chartPoints[$totalPoints - 1]['label'] ?? '' }}</span>
            </div>
        @endif
    </div>

    {{-- 2-Column: Traffic Acquisition Channels & Audience Demographics --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Traffic Channels (7 cols) --}}
        <div class="lg:col-span-7 bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-400 text-[18px]">pie_chart</span>
                        <span>Traffic Acquisition Channels</span>
                    </h3>
                    <p class="text-xs text-[#6F767E] mt-0.5">Where your visitors originate from across {{ $currentRangeLabel }}.</p>
                </div>
                <span class="text-xs font-bold text-[#6F767E] bg-[#111] px-3 py-1.5 rounded-xl border border-[#272B30]">
                    {{ number_format($channelsData['total_sessions'] ?? 0) }} Sessions
                </span>
            </div>

            <div class="space-y-4">
                @foreach ($channelsData['channels'] ?? [] as $channel)
                    <div class="p-3.5 rounded-xl bg-[#111111] border border-[#272B30]/60 hover:border-[#3a4047] transition">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-[18px]" style="color: {{ $channel['color'] }}">{{ $channel['icon'] }}</span>
                                <span class="text-xs font-bold text-white">{{ $channel['name'] }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-white">{{ number_format($channel['sessions']) }}</span>
                                <span class="text-xs font-extrabold px-2 py-0.5 rounded-md" style="color: {{ $channel['color'] }}; background-color: {{ $channel['color'] }}1A">
                                    {{ $channel['percentage'] }}%
                                </span>
                            </div>
                        </div>
                        {{-- Progress Bar --}}
                        <div class="w-full h-2 bg-[#272B30] rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width: {{ $channel['percentage'] }}%; background-color: {{ $channel['color'] }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Devices & Geography (5 cols) --}}
        <div class="lg:col-span-5 bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6 flex flex-col justify-between">
            <div>
                <div class="mb-5">
                    <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-indigo-400 text-[18px]">devices</span>
                        <span>Devices & Top Countries</span>
                    </h3>
                    <p class="text-xs text-[#6F767E] mt-0.5">Audience device platform and geographical reach.</p>
                </div>

                {{-- Device Distribution Segmented Bar --}}
                <div class="mb-6 p-4 rounded-xl bg-[#111] border border-[#272B30]/80">
                    <span class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block mb-3">Device Breakdown</span>
                    <div class="flex h-3 w-full rounded-full overflow-hidden gap-0.5 mb-3 bg-[#272B30]">
                        @foreach ($deviceData['devices'] ?? [] as $dev)
                            <div style="width: {{ $dev['percentage'] }}%; background-color: {{ $dev['color'] }}" title="{{ $dev['name'] }}: {{ $dev['percentage'] }}%"></div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between text-xs font-semibold">
                        @foreach ($deviceData['devices'] ?? [] as $dev)
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $dev['color'] }}"></span>
                                <span class="text-white">{{ $dev['name'] }}</span>
                                <span class="text-[#6F767E]">({{ $dev['percentage'] }}%)</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Top Countries List --}}
                <div class="space-y-2.5">
                    <span class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block mb-1">Top Audience Locations</span>
                    @foreach ($deviceData['countries'] ?? [] as $c)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-[#111] border border-[#272B30]/50 text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="text-base">{{ $c['flag'] }}</span>
                                <span class="font-bold text-white">{{ $c['country'] }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[#6F767E] font-medium">{{ number_format($c['users']) }} users</span>
                                <span class="font-bold text-indigo-400 w-12 text-right">{{ $c['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Top Search Queries Table (Search Console) --}}
    <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-[#272B30]">
            <div>
                <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-400 text-[20px]">manage_search</span>
                    <span>Top Search Queries</span>
                </h3>
                <p class="text-xs text-[#6F767E] mt-0.5">Top keywords driving organic Google Search impressions and clicks ({{ $currentRangeLabel }}).</p>
            </div>

            {{-- Live Search Box --}}
            <div class="relative w-full md:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#6F767E] text-[18px]">search</span>
                <input type="text"
                       wire:model.live.debounce.300ms="searchQuery"
                       placeholder="Filter search queries..."
                       class="w-full pl-9 pr-3.5 py-2 bg-[#111] border border-[#272B30] focus:border-indigo-500 rounded-xl text-xs text-white placeholder-[#6F767E] outline-none transition"/>
            </div>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[#6F767E] uppercase text-[10px] tracking-wider border-b border-[#272B30]/80">
                        <th class="py-3 px-4 font-bold">Search Query</th>
                        <th class="py-3 px-4 font-bold text-right">Clicks</th>
                        <th class="py-3 px-4 font-bold text-right">Impressions</th>
                        <th class="py-3 px-4 font-bold text-right">CTR</th>
                        <th class="py-3 px-4 font-bold text-right">Position</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#272B30]/40">
                    @forelse ($topQueries as $q)
                        <tr class="hover:bg-[#202020] transition group">
                            <td class="py-3.5 px-4 font-bold text-white group-hover:text-indigo-400 transition flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#6F767E] text-[14px]">search</span>
                                <span>{{ $q['query'] }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-white">
                                {{ number_format($q['clicks']) }}
                            </td>
                            <td class="py-3.5 px-4 text-right text-[#9CA3AF]">
                                {{ number_format($q['impressions']) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-400">
                                {{ $q['ctr'] }}%
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <span class="inline-block px-2 py-0.5 rounded-md font-bold text-[11px] {{ $q['position'] <= 3 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-[#272B30] text-[#9CA3AF]' }}">
                                    #{{ $q['position'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-[#6F767E]">
                                No search queries found matching "{{ $searchQuery }}".
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Performing Content Table (GA4) --}}
    <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6">
        <div class="flex items-center justify-between pb-5 border-b border-[#272B30]">
            <div>
                <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-400 text-[20px]">article</span>
                    <span>Most Popular Content</span>
                </h3>
                <p class="text-xs text-[#6F767E] mt-0.5">Top performing website pages ranked by total pageviews and sessions.</p>
            </div>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[#6F767E] uppercase text-[10px] tracking-wider border-b border-[#272B30]/80">
                        <th class="py-3 px-4 font-bold">Page Title & Path</th>
                        <th class="py-3 px-4 font-bold text-right">Pageviews</th>
                        <th class="py-3 px-4 font-bold text-right">Unique Sessions</th>
                        <th class="py-3 px-4 font-bold text-right">Bounce Rate</th>
                        <th class="py-3 px-4 font-bold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#272B30]/40">
                    @forelse ($topPages as $p)
                        <tr class="hover:bg-[#202020] transition group">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white group-hover:text-indigo-400 transition">{{ $p['title'] }}</div>
                                <div class="text-[11px] text-[#6F767E] font-mono mt-0.5">{{ $p['path'] }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-white">
                                {{ number_format($p['pageviews']) }}
                            </td>
                            <td class="py-3.5 px-4 text-right text-[#9CA3AF] font-semibold">
                                {{ number_format($p['sessions']) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-semibold text-[#9CA3AF]">
                                {{ $p['bounce_rate'] }}%
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ $p['url'] }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#272B30] hover:bg-[#32373F] text-[#9CA3AF] hover:text-white transition"
                                   title="View on live website">
                                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-xs text-[#6F767E]">
                                No pages recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PageSpeed Insights & Core Web Vitals --}}
    <div class="bg-[#1A1A1A] border border-[#272B30] rounded-2xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-[#272B30] gap-4">
            <div>
                <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-400 text-[20px]">speed</span>
                    <span>PageSpeed Insights & Core Web Vitals</span>
                </h3>
                <p class="text-xs text-[#6F767E] mt-0.5">Google Lighthouse performance benchmark and real-user experience metrics.</p>
            </div>
            
            <button wire:click="refreshSpeed"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs px-4 py-2.5 rounded-xl transition shadow-lg shadow-indigo-600/20 shrink-0"
                    {{ $loadingSpeed ? 'disabled' : '' }}>
                @if ($loadingSpeed)
                    <span class="inline-block animate-spin w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full"></span>
                    <span>Analyzing Lighthouse...</span>
                @else
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    <span>Run PageSpeed Test</span>
                @endif
            </button>
        </div>

        @if (session()->has('speed_success'))
            <div class="mt-4 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center gap-2">
                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                <span>{{ session('speed_success') }}</span>
            </div>
        @endif

        {{-- Overall Gauges Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
            {{-- Mobile Performance --}}
            @php
                $mScore = $speedData['mobile']['score'] ?? 84;
                $mColor = $speedData['mobile']['color'] ?? ($mScore >= 90 ? '#10B981' : ($mScore >= 50 ? '#F59E0B' : '#EF4444'));
                $mOffset = 251.2 - (251.2 * $mScore / 100);
            @endphp
            <div class="flex items-center gap-5 p-5 bg-[#111111] rounded-2xl border border-[#272B30]">
                <div class="relative w-20 h-20 shrink-0 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="40" cy="40" r="32" stroke="#272B30" stroke-width="7" fill="transparent"/>
                        <circle cx="40" cy="40" r="32" stroke="{{ $mColor }}" stroke-width="7" fill="transparent"
                                stroke-dasharray="201.06" stroke-dashoffset="{{ 201.06 - (201.06 * $mScore / 100) }}" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute text-lg font-black text-white">{{ $mScore }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-white text-[18px]">phone_iphone</span>
                        <span class="text-sm font-extrabold text-white">Mobile Performance</span>
                    </div>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[11px] font-bold" style="color: {{ $mColor }}; background-color: {{ $mColor }}1A">
                        {{ $speedData['mobile']['label'] ?? 'Needs Improvement' }}
                    </span>
                    <p class="text-xs text-[#6F767E] mt-1">Simulated 4G network lighthouse benchmark.</p>
                </div>
            </div>

            {{-- Desktop Performance --}}
            @php
                $dScore = $speedData['desktop']['score'] ?? 97;
                $dColor = $speedData['desktop']['color'] ?? ($dScore >= 90 ? '#10B981' : ($dScore >= 50 ? '#F59E0B' : '#EF4444'));
            @endphp
            <div class="flex items-center gap-5 p-5 bg-[#111111] rounded-2xl border border-[#272B30]">
                <div class="relative w-20 h-20 shrink-0 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="40" cy="40" r="32" stroke="#272B30" stroke-width="7" fill="transparent"/>
                        <circle cx="40" cy="40" r="32" stroke="{{ $dColor }}" stroke-width="7" fill="transparent"
                                stroke-dasharray="201.06" stroke-dashoffset="{{ 201.06 - (201.06 * $dScore / 100) }}" stroke-linecap="round"/>
                    </svg>
                    <span class="absolute text-lg font-black text-white">{{ $dScore }}</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-white text-[18px]">desktop_windows</span>
                        <span class="text-sm font-extrabold text-white">Desktop Performance</span>
                    </div>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[11px] font-bold" style="color: {{ $dColor }}; background-color: {{ $dColor }}1A">
                        {{ $speedData['desktop']['label'] ?? 'Good' }}
                    </span>
                    <p class="text-xs text-[#6F767E] mt-1">High-speed broadband network lighthouse benchmark.</p>
                </div>
            </div>
        </div>

        {{-- Core Web Vitals Diagnostic Cards --}}
        <div>
            <span class="text-xs font-bold text-[#6F767E] uppercase tracking-wider block mb-3">Core Web Vitals Assessment</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                @foreach ($speedData['vitals'] ?? [] as $vital)
                    <div class="p-4 rounded-xl bg-[#111111] border border-[#272B30]/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-black text-white tracking-wider">{{ $vital['name'] }}</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 uppercase">
                                    {{ $vital['status'] }}
                                </span>
                            </div>
                            <span class="text-xl font-black text-white block my-1">{{ $vital['value'] }}</span>
                            <span class="text-[10px] font-semibold text-indigo-400 block mb-2">{{ $vital['target'] }}</span>
                            <p class="text-[11px] text-[#6F767E] leading-relaxed">{{ $vital['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
