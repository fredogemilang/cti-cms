<div class="bg-white border border-gray-200 rounded-lg p-4 space-y-4" wire:submit.prevent="save">
    <div class="flex items-center justify-between border-b pb-2">
        <h3 class="font-semibold text-gray-800">SEO Settings</h3>
        <button type="button" wire:click="save" class="text-sm text-blue-600 hover:underline">Save SEO</button>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Meta Title</label>
        <input type="text" wire:model.live.debounce.300ms="title" maxlength="70"
               class="w-full mt-1 rounded border-gray-300 text-sm" placeholder="Auto from page title">
        <div class="text-xs mt-1 {{ $this->titleLength > 60 ? 'text-red-600' : 'text-gray-500' }}">
            {{ $this->titleLength }}/60 chars
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Meta Description</label>
        <textarea wire:model.live.debounce.300ms="description" maxlength="160" rows="3"
                  class="w-full mt-1 rounded border-gray-300 text-sm"></textarea>
        <div class="text-xs mt-1 {{ $this->descriptionLength > 160 ? 'text-red-600' : 'text-gray-500' }}">
            {{ $this->descriptionLength }}/160 chars
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Focus Keyword</label>
        <input type="text" wire:model="focus_keyword" class="w-full mt-1 rounded border-gray-300 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Canonical URL</label>
        <input type="url" wire:model="canonical_url" placeholder="Leave blank to use current URL"
               class="w-full mt-1 rounded border-gray-300 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Robots</label>
        <select wire:model="robots" class="w-full mt-1 rounded border-gray-300 text-sm">
            <option value="index,follow">index, follow</option>
            <option value="noindex,follow">noindex, follow</option>
            <option value="index,nofollow">index, nofollow</option>
            <option value="noindex,nofollow">noindex, nofollow</option>
        </select>
    </div>

    <details class="border-t pt-3">
        <summary class="cursor-pointer text-sm font-medium text-gray-700">Open Graph & Twitter</summary>
        <div class="mt-3 space-y-3">
            <div>
                <label class="block text-xs text-gray-600">OG Title</label>
                <input type="text" wire:model="og_title" class="w-full mt-1 rounded border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs text-gray-600">OG Description</label>
                <textarea wire:model="og_description" rows="2" class="w-full mt-1 rounded border-gray-300 text-sm"></textarea>
            </div>
            <div>
                <label class="block text-xs text-gray-600">Twitter Card</label>
                <select wire:model="twitter_card" class="w-full mt-1 rounded border-gray-300 text-sm">
                    <option value="summary">summary</option>
                    <option value="summary_large_image">summary_large_image</option>
                </select>
            </div>
        </div>
    </details>

    <details class="border-t pt-3">
        <summary class="cursor-pointer text-sm font-medium text-gray-700">Schema.org</summary>
        <div class="mt-3">
            <label class="block text-xs text-gray-600">Schema Type</label>
            <select wire:model="schema_type" class="w-full mt-1 rounded border-gray-300 text-sm">
                <option value="">Auto-detect</option>
                <option value="Article">Article</option>
                <option value="BlogPosting">BlogPosting</option>
                <option value="NewsArticle">NewsArticle</option>
                <option value="WebPage">WebPage</option>
                <option value="Event">Event</option>
                <option value="Organization">Organization</option>
                <option value="FAQPage">FAQPage</option>
            </select>
        </div>
    </details>

    {{-- SERP preview --}}
    <div class="border-t pt-3">
        <p class="text-xs text-gray-500 mb-1">SERP Preview</p>
        <div class="border rounded p-3 bg-gray-50">
            <div class="text-blue-700 text-base">{{ $title ?: 'Page title here' }}</div>
            <div class="text-green-700 text-xs">{{ $canonical_url ?: request()->fullUrl() }}</div>
            <div class="text-gray-600 text-sm">{{ $description ?: 'Your meta description appears here…' }}</div>
        </div>
    </div>

    @if (session('flash'))
        <div class="text-sm text-green-600">{{ session('flash') }}</div>
    @endif
</div>
