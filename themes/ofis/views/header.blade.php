<header class="bg-white shadow-sm relative z-50">
    <section class="relative max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between py-4 bg-white">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ theme_asset('Logo-OFIS-e1711423097777.png-M5Jmiuvo.webp') }}" alt="{{ config('app.name', 'OFIS') }}" class="h-12 w-auto" />
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden lg:flex items-center gap-10">
            <nav class="flex items-center gap-8 text-base font-medium">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-ofis-teal border-b-2 border-[#fab54f] pb-1' : 'text-gray-500 hover:text-ofis-teal' }} pb-1 whitespace-nowrap">
                    {{ t('nav.home', 'Home') }}
                </a>

                <!-- Packages Dropdown -->
                <div class="relative group cursor-pointer">
                    <a href="{{ url('/package') }}" class="text-gray-500 group-hover:text-ofis-teal whitespace-nowrap flex items-center gap-1 pb-1">
                        {{ t('nav.packages', 'Packages') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    <div class="absolute left-0 top-full pt-4 w-[260px] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <div class="bg-[#81b4c6] text-white shadow-lg rounded-b-lg overflow-hidden">
                            <a href="{{ url('/package/efficiency') }}" class="block px-6 py-3.5 hover:bg-[#6e9fb1] transition text-[15px] font-normal">
                                {{ t('packages.efficiency', 'Efficiency') }}
                            </a>
                            <a href="{{ url('/package/safety-and-security') }}" class="block px-6 py-3.5 hover:bg-[#6e9fb1] transition text-[15px] font-normal">
                                {{ t('packages.safety_security', 'Safety and Security') }}
                            </a>
                            <a href="{{ url('/package/seat-management-system') }}" class="block px-6 py-3.5 hover:bg-[#6e9fb1] transition text-[15px] font-normal">
                                {{ t('packages.seat_management', 'Seat Management System') }}
                            </a>
                            <a href="{{ url('/package/service-and-facility') }}" class="block px-6 py-3.5 hover:bg-[#6e9fb1] transition text-[15px] font-normal">
                                {{ t('packages.service_facility', 'Service and Facility') }}
                            </a>
                            <a href="{{ url('/package/smart-front-desk') }}" class="block px-6 py-3.5 hover:bg-[#6e9fb1] transition text-[15px] font-normal">
                                {{ t('packages.smart_front_desk', 'Smart Front Desk') }}
                            </a>
                            <a href="{{ url('/package/smart-meeting-room') }}" class="block px-6 py-3.5 hover:bg-[#6e9fb1] transition text-[15px] font-normal">
                                {{ t('packages.smart_meeting_room', 'Smart Meeting Room') }}
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ url('/blog') }}" class="{{ request()->is('blog*') ? 'text-ofis-teal border-b-2 border-[#fab54f] pb-1' : 'text-gray-500 hover:text-ofis-teal' }} whitespace-nowrap">
                    {{ t('nav.blog', 'Blog') }}
                </a>

                <a href="{{ url('/#about') }}" class="text-gray-500 hover:text-ofis-teal whitespace-nowrap">
                    {{ t('nav.about', 'About BPT') }}
                </a>
            </nav>

            <!-- Contact CTA -->
            <a href="{{ url('/#contact') }}" class="transition py-2.5 px-6 bg-[#fab54f] hover:bg-[#fab54f]/90 text-ofis-ink text-sm font-semibold rounded-full shadow-sm">
                {{ t('nav.contact_us', 'Contact Us') }}
            </a>
        </div>
    </section>
</header>
