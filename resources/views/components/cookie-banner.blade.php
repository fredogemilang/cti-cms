<div x-data="{ 
        accepted: localStorage.getItem('cookie_consent') === 'accepted',
        accept() {
            localStorage.setItem('cookie_consent', 'accepted');
            this.accepted = true;
        }
    }" 
    x-show="!accepted" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-cloak
    class="fixed bottom-6 left-6 right-6 md:left-auto md:right-6 md:max-w-md z-50 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-2xl shadow-2xl p-6 text-sm">
    <div class="flex items-start gap-4">
        <div class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-[#2563EB] flex items-center justify-center shrink-0">
            <span class="material-symbols-outlined text-xl">cookie</span>
        </div>
        <div class="space-y-2 flex-1">
            <h4 class="font-bold text-[#111827] dark:text-[#FCFCFC]">We value your privacy</h4>
            <p class="text-xs text-[#6F767E] leading-relaxed">
                We use essential cookies to ensure our website functions properly and to enhance your browsing experience.
            </p>
            <div class="flex items-center gap-3 pt-2">
                <button type="button" @click="accept()" class="px-4 py-2 rounded-xl bg-[#2563EB] text-white text-xs font-bold hover:bg-blue-700 transition-colors shadow-sm">
                    Accept All
                </button>
                <button type="button" @click="accept()" class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-[#272B30] text-[#111827] dark:text-[#FCFCFC] text-xs font-bold hover:brightness-95 transition-colors">
                    Essential Only
                </button>
            </div>
        </div>
    </div>
</div>
