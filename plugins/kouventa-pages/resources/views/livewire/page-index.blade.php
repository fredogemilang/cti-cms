<div class="space-y-6">
    <div class="bg-white dark:bg-[#1A1A1A] rounded-2xl border border-gray-200 dark:border-[#272B30] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-[#272B30] bg-gray-50/50 dark:bg-[#272B30]/20 text-[#6F767E] font-bold text-xs uppercase tracking-wider">
                        <th class="py-4 px-6">Page & URL</th>
                        <th class="py-4 px-6">Module Type</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Last Updated</th>
                        <th class="py-4 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-[#272B30]">
                    @forelse($pages as $p)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-[#272B30]/30 transition-colors">
                            <td class="py-4 px-6">
                                <div>
                                    <a href="{{ route('admin.kouventa-pages.edit', $p->id) }}" class="font-bold text-[#111827] dark:text-[#FCFCFC] hover:text-[#2563EB] text-base transition-colors flex items-center gap-2">
                                        {{ $p->title }}
                                    </a>
                                    <div class="text-xs text-[#6F767E] flex items-center gap-1 mt-1 font-mono">
                                        <span class="material-symbols-outlined text-xs">link</span>
                                        /{{ $p->slug }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @if(str_starts_with($p->slug, 'product/'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 dark:bg-blue-900/20 text-[#2563EB]">
                                        <span class="material-symbols-outlined text-[14px]">inventory_2</span>
                                        Product
                                    </span>
                                @elseif(str_starts_with($p->slug, 'solutions/'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600">
                                        <span class="material-symbols-outlined text-[14px]">psychology</span>
                                        Solution
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-50 dark:bg-purple-900/20 text-purple-600">
                                        <span class="material-symbols-outlined text-[14px]">payments</span>
                                        Pricing
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($p->status === 'published')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#3F8C5826] text-[#83BF6E] uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-100 dark:bg-[#272B30] text-[#6F767E] uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-[#6F767E] text-xs">
                                {{ $p->updated_at ? $p->updated_at->diffForHumans() : '—' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ $p->getUrl() }}" target="_blank" class="h-9 px-3 rounded-xl border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-[#FCFCFC] hover:bg-gray-50 dark:hover:bg-[#272B30] flex items-center gap-1.5 text-xs font-bold transition-all" title="View live page">
                                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                                        <span>View</span>
                                    </a>
                                    <a href="{{ route('admin.kouventa-pages.edit', $p->id) }}" class="h-9 px-3.5 rounded-xl bg-[#2563EB] text-white hover:bg-blue-700 flex items-center gap-1.5 text-xs font-bold transition-all shadow-sm" title="Edit content & sections">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                        <span>Edit Sections</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-[#6F767E]">
                                <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">auto_stories</span>
                                <p class="text-sm font-medium">No pages found in plugin database.</p>
                                <p class="text-xs text-gray-400 mt-1">Run plugin seeders to initialize the 5 interactive pages.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

