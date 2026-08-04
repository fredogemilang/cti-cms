<footer class="bg-gradient-to-br from-[#0c2538] via-[#10334d] to-[#0a1e2d] text-white pt-16 pb-12 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pb-12 border-b border-white/10">
            <!-- Brand Info -->
            <div>
                <img src="{{ theme_asset('Logo-OFIS-e1711423097777.png-M5Jmiuvo.webp') }}" alt="OFIS" class="h-10 w-auto mb-6 brightness-0 invert" />
                <p class="text-gray-300 text-sm leading-relaxed mb-6">
                    {{ t('footer.about_text', 'One Future of Interconnected workspace for Smart workforce. OFIS from Blue Power Technology provides end-to-end smart office solutions for modern enterprises.') }}
                </p>
                <div class="flex items-center gap-4">
                    <a href="https://linkedin.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#fab54f] hover:text-black transition">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                    </a>
                    <a href="https://instagram.com" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#fab54f] hover:text-black transition">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Solution Packages -->
            <div>
                <span class="block text-[#fab54f] font-semibold text-lg mb-6">{{ t('footer.packages', 'OFIS Packages') }}</span>
                <ul class="space-y-3 text-sm text-gray-300">
                    <li><a href="{{ url('/package/efficiency') }}" class="hover:text-[#fab54f] transition">OFIS Efficiency</a></li>
                    <li><a href="{{ url('/package/safety-and-security') }}" class="hover:text-[#fab54f] transition">OFIS Safety and Security</a></li>
                    <li><a href="{{ url('/package/seat-management-system') }}" class="hover:text-[#fab54f] transition">OFIS Seat Management</a></li>
                    <li><a href="{{ url('/package/service-and-facility') }}" class="hover:text-[#fab54f] transition">OFIS Service and Facility</a></li>
                    <li><a href="{{ url('/package/smart-front-desk') }}" class="hover:text-[#fab54f] transition">OFIS Smart Front Desk</a></li>
                    <li><a href="{{ url('/package/smart-meeting-room') }}" class="hover:text-[#fab54f] transition">OFIS Smart Meeting Room</a></li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div>
                <span class="block text-[#fab54f] font-semibold text-lg mb-6">{{ t('footer.quick_links', 'Quick Links') }}</span>
                <ul class="space-y-3 text-sm text-gray-300">
                    <li><a href="{{ url('/') }}" class="hover:text-[#fab54f] transition">Home</a></li>
                    <li><a href="{{ url('/blog') }}" class="hover:text-[#fab54f] transition">Blog Articles</a></li>
                    <li><a href="{{ url('/#about') }}" class="hover:text-[#fab54f] transition">About Blue Power Technology</a></li>
                    <li><a href="{{ url('/#contact') }}" class="hover:text-[#fab54f] transition">Contact Us</a></li>
                </ul>
            </div>

            <!-- Our Office / Contact -->
            <div>
                <span class="block text-[#fab54f] font-semibold text-lg mb-6">{{ t('footer.our_office', 'OUR OFFICE') }}</span>
                <div class="text-sm text-gray-300 space-y-3 leading-relaxed">
                    <p class="font-semibold text-white">PT. Blue Power Technology</p>
                    <p>Centennial Tower 12th Floor,<br>Jl. Jend. Gatot Subroto Kav. 24-25,<br>Jakarta Selatan 12930, Indonesia</p>
                    <p class="pt-2"><strong class="text-white">Email:</strong> info@bluepowertechnology.com</p>
                    <p><strong class="text-white">Phone:</strong> +62 21 5795 8230</p>
                </div>
            </div>
        </div>

        <div class="pt-8 text-center text-xs text-gray-400 flex flex-col md:flex-row items-center justify-between gap-4">
            <p>© {{ date('Y') }} {{ config('app.name', 'OFIS') }} — PT Blue Power Technology. All Rights Reserved.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-white transition">Privacy Policy</a>
                <a href="#" class="hover:text-white transition">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
