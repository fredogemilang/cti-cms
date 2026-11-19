@props(['page' => null, 'entry' => null, 'post' => null, 'basePage' => null])
@auth
@php
    $adminPath = config('admin.path', 'ctrlpanel');
    $adminUrl = url($adminPath);
    $user = auth()->user();

    // Resolve current entity edit link if available
    $editUrl = null;
    $editLabel = null;

    $page = $page ?? view()->shared('page') ?? request()->attributes->get('page');
    $basePage = $basePage ?? view()->shared('basePage') ?? request()->attributes->get('basePage');
    $targetPage = (isset($page) && $page->id) ? $page : ($basePage ?? $page ?? null);
    $entry = $entry ?? view()->shared('entry') ?? view()->shared('cpt_entry') ?? request()->attributes->get('cpt_entry') ?? request()->attributes->get('entry');
    $post = $post ?? view()->shared('post') ?? request()->attributes->get('post');

    if (isset($entry) && $entry instanceof \App\Models\CptEntry) {
        $postTypeSlug = $entry->postType?->slug ?? $entry->post_type_slug ?? '';
        $editUrl = url("{$adminPath}/cpt/entries/{$postTypeSlug}/{$entry->id}/edit");
        $editLabel = 'Edit ' . ($entry->postType?->singular_label ?? 'Entry');
    } elseif (isset($targetPage) && class_exists(\Plugins\KouventaPages\Models\KouventaPage::class) && $targetPage instanceof \Plugins\KouventaPages\Models\KouventaPage) {
        $category = match (true) {
            str_starts_with($targetPage->slug ?? '', 'solutions') || str_starts_with($targetPage->template ?? '', 'solution') => 'solutions',
            str_starts_with($targetPage->slug ?? '', 'product') || str_starts_with($targetPage->template ?? '', 'product') => 'products',
            ($targetPage->slug ?? '') === 'pricing' || ($targetPage->template ?? '') === 'pricing' => 'pricing',
            default => 'products',
        };
        $editUrl = url("{$adminPath}/kouventa-pages/{$category}/{$targetPage->id}/edit");
        $editLabel = 'Edit ' . ($targetPage->title ?? 'Page');
    } elseif (isset($targetPage) && $targetPage instanceof \App\Models\Page) {
        $editUrl = url("{$adminPath}/pages/{$targetPage->id}/edit");
        $editLabel = 'Edit Page';
    } elseif (isset($post) && class_exists(\Plugins\Posts\Models\Post::class) && $post instanceof \Plugins\Posts\Models\Post) {
        $editUrl = url("{$adminPath}/posts/{$post->id}/edit");
        $editLabel = 'Edit Post';
    }
@endphp

<!-- Ultra-Sleek Professional Admin Top Bar (Core Component - 100% Self-Contained Isolated CSS) -->
<style>
#cms-admin-bar {
    box-sizing: border-box !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    position: sticky !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    height: 32px !important;
    min-height: 32px !important;
    max-height: 32px !important;
    background-color: #09090b !important;
    color: #d4d4d8 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    padding: 0 16px !important;
    margin: 0 !important;
    z-index: 99999 !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
    font-size: 11px !important;
    line-height: 1 !important;
    letter-spacing: -0.01em !important;
    user-select: none !important;
    -webkit-user-select: none !important;
}
#cms-admin-bar *, #cms-admin-bar *::before, #cms-admin-bar *::after {
    box-sizing: border-box !important;
}
#cms-admin-bar .cab-group {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}
#cms-admin-bar a {
    text-decoration: none !important;
    color: inherit !important;
    display: inline-flex !important;
    align-items: center !important;
    transition: all 0.15s ease !important;
}
#cms-admin-bar .cab-brand {
    color: #f4f4f5 !important;
    font-weight: 600 !important;
    gap: 6px !important;
    padding: 3px 6px !important;
    border-radius: 4px !important;
}
#cms-admin-bar .cab-brand:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
}
#cms-admin-bar .cab-badge {
    width: 16px !important;
    height: 16px !important;
    border-radius: 4px !important;
    background-color: #f59e0b !important;
    color: #09090b !important;
    font-weight: 900 !important;
    font-size: 10px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    line-height: 1 !important;
}
#cms-admin-bar .cab-divider {
    display: inline-block !important;
    width: 1px !important;
    height: 12px !important;
    background-color: rgba(255, 255, 255, 0.15) !important;
    margin: 0 4px !important;
    flex-shrink: 0 !important;
}
#cms-admin-bar .cab-edit-btn {
    color: #60a5fa !important;
    background-color: rgba(96, 165, 250, 0.12) !important;
    padding: 3px 8px !important;
    border-radius: 4px !important;
    font-weight: 500 !important;
    gap: 5px !important;
    font-size: 11px !important;
}
#cms-admin-bar .cab-edit-btn:hover {
    background-color: rgba(96, 165, 250, 0.22) !important;
    color: #93c5fd !important;
}
#cms-admin-bar .cab-edit-btn svg {
    width: 12px !important;
    height: 12px !important;
    max-width: 12px !important;
    max-height: 12px !important;
    flex-shrink: 0 !important;
    display: inline-block !important;
    color: #60a5fa !important;
    stroke: currentColor !important;
}
#cms-admin-bar .cab-user {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    color: #a1a1aa !important;
    font-size: 11px !important;
}
#cms-admin-bar .cab-avatar {
    width: 18px !important;
    height: 18px !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #2563eb, #4f46e5) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 9px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    line-height: 1 !important;
}
#cms-admin-bar .cab-username {
    color: #f4f4f5 !important;
    font-weight: 600 !important;
}
#cms-admin-bar .cab-logout-btn {
    all: unset !important;
    box-sizing: border-box !important;
    cursor: pointer !important;
    color: #a1a1aa !important;
    font-size: 11px !important;
    padding: 3px 6px !important;
    border-radius: 4px !important;
    transition: color 0.15s ease !important;
    display: inline-block !important;
    line-height: 1 !important;
    font-family: inherit !important;
}
#cms-admin-bar .cab-logout-btn:hover {
    color: #f87171 !important;
    background-color: rgba(239, 68, 68, 0.1) !important;
}
@media (max-width: 640px) {
    #cms-admin-bar {
        padding: 0 10px !important;
    }
    #cms-admin-bar .cab-user-text {
        display: none !important;
    }
    #cms-admin-bar .cab-brand-text {
        display: none !important;
    }
}
</style>

<div id="cms-admin-bar" style="background-color: #09090b !important; color: #d4d4d8 !important; position: sticky !important; top: 0 !important; z-index: 99999 !important; height: 32px !important; display: flex !important; align-items: center !important; justify-content: space-between !important; padding: 0 16px !important; width: 100% !important; box-sizing: border-box !important;">
    <!-- Left Section -->
    <div class="cab-group" style="display: flex; align-items: center; gap: 8px;">
        <!-- Control Panel Brand Link -->
        <a href="{{ $adminUrl }}/dashboard" class="cab-brand" style="display: inline-flex; align-items: center; gap: 6px; color: #f4f4f5; text-decoration: none;">
            <span class="cab-badge" style="width: 16px; height: 16px; border-radius: 4px; background: #f59e0b; color: #09090b; font-weight: 900; font-size: 10px; display: inline-flex; align-items: center; justify-content: center; line-height: 1;">C</span>
            <span class="cab-brand-text" style="font-weight: 600;">{{ setting('site_name', config('app.name', 'CMS')) }} Control Panel</span>
        </a>

        @if($editUrl)
        <span class="cab-divider" style="display: inline-block; width: 1px; height: 12px; background: rgba(255,255,255,0.15); margin: 0 4px;"></span>

        <!-- Edit Current Item (Contextual Button ala WordPress) -->
        <a href="{{ $editUrl }}" class="cab-edit-btn" style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; border-radius: 4px; background: rgba(96,165,250,0.12); color: #60a5fa; font-size: 11px; font-weight: 500; text-decoration: none;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px !important; height: 12px !important; max-width: 12px !important; max-height: 12px !important; display: inline-block !important; flex-shrink: 0 !important;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <span>{{ $editLabel }}</span>
        </a>
        @endif
    </div>

    <!-- Right Section -->
    <div class="cab-group" style="display: flex; align-items: center; gap: 8px;">
        <!-- User Profile -->
        <div class="cab-user" style="display: inline-flex; align-items: center; gap: 6px; color: #a1a1aa; font-size: 11px;">
            <span class="cab-avatar" style="width: 18px; height: 18px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #4f46e5); color: #fff; font-weight: 700; font-size: 9px; display: inline-flex; align-items: center; justify-content: center; line-height: 1;">
                {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
            </span>
            <span class="cab-user-text">Logged in as <strong class="cab-username" style="color: #f4f4f5; font-weight: 600;">{{ $user->name ?? 'Admin' }}</strong></span>
        </div>

        <span class="cab-divider" style="display: inline-block; width: 1px; height: 12px; background: rgba(255,255,255,0.15); margin: 0 4px;"></span>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0; display: inline;">
            @csrf
            <button type="submit" class="cab-logout-btn" style="background: none; border: none; color: #a1a1aa; font-size: 11px; cursor: pointer; padding: 3px 6px; border-radius: 4px; line-height: 1;">
                Logout
            </button>
        </form>
    </div>
</div>
@endauth
