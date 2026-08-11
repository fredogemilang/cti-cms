@auth
@php
    $adminPath = config('admin.path', 'ctrlpanel');
    $adminUrl = url($adminPath);
    $user = auth()->user();

    // Resolve current entity edit link if available
    $editUrl = null;
    $editLabel = null;

    if (isset($entry) && $entry instanceof \App\Models\CptEntry) {
        $editUrl = url("{$adminPath}/cpt/entries/{$entry->postType->slug}/{$entry->id}/edit");
        $editLabel = 'Edit ' . ($entry->postType->singular_label ?? 'Entry');
    } elseif (isset($page) && $page instanceof \App\Models\Page) {
        $editUrl = url("{$adminPath}/pages/{$page->id}/edit");
        $editLabel = 'Edit Page';
    } elseif (isset($post) && class_exists(\Plugins\Posts\Models\Post::class) && $post instanceof \Plugins\Posts\Models\Post) {
        $editUrl = url("{$adminPath}/posts/{$post->id}/edit");
        $editLabel = 'Edit Post';
    }
@endphp

<style>
    #cms-admin-bar {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        background-color: #1d2327 !important;
        color: #f0f0f1 !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
        font-size: 13px !important;
        height: 32px !important;
        position: relative !important;
        z-index: 99999 !important;
        box-sizing: border-box !important;
        width: 100% !important;
        padding: 0 15px !important;
        border-bottom: 1px solid #101517 !important;
    }
    #cms-admin-bar a {
        color: #f0f0f1 !important;
        text-decoration: none !important;
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 0 10px !important;
        height: 32px !important;
        line-height: 32px !important;
        transition: background 0.1s ease-in-out, color 0.1s ease-in-out !important;
    }
    #cms-admin-bar a:hover {
        background-color: #2c3338 !important;
        color: #72aee6 !important;
    }
    #cms-admin-bar .admin-bar-left, #cms-admin-bar .admin-bar-right {
        display: flex !important;
        align-items: center !important;
    }
    #cms-admin-bar .divider {
        width: 1px !important;
        height: 16px !important;
        background-color: #3c434a !important;
        margin: 0 8px !important;
    }
    #cms-admin-bar .avatar {
        width: 20px !important;
        height: 20px !important;
        border-radius: 50% !important;
        background: #72aee6 !important;
        color: #fff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: bold !important;
        font-size: 11px !important;
    }
    #cms-admin-bar .user-info {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 0 10px !important;
        font-size: 13px !important;
        color: #c3c4c7 !important;
    }
    #cms-admin-bar button {
        background: none !important;
        border: none !important;
        color: #f0f0f1 !important;
        padding: 0 10px !important;
        height: 32px !important;
        line-height: 32px !important;
        font-family: inherit !important;
        font-size: 13px !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        transition: background 0.1s ease-in-out, color 0.1s ease-in-out !important;
    }
    #cms-admin-bar button:hover {
        background-color: #d63638 !important;
        color: #fff !important;
    }
    @media (max-width: 768px) {
        #cms-admin-bar {
            padding: 0 5px !important;
            font-size: 11px !important;
        }
        #cms-admin-bar a {
            padding: 0 4px !important;
            gap: 4px !important;
        }
        #cms-admin-bar .user-info {
            padding: 0 4px !important;
            gap: 4px !important;
            font-size: 11px !important;
        }
        #cms-admin-bar .user-info > span:not(.avatar) {
            display: none !important;
        }
        #cms-admin-bar .site-name-text {
            display: none !important;
        }
        #cms-admin-bar .edit-label-text {
            display: none !important;
        }
        #cms-admin-bar .divider {
            margin: 0 4px !important;
        }
    }
</style>

<div id="cms-admin-bar">
    <!-- Left Section -->
    <div class="admin-bar-left">
        <a href="{{ $adminUrl }}/dashboard" style="font-weight: 600;">
            <span style="display: inline-block; width: 16px; height: 16px; background: #89C55C; color: #fff; border-radius: 3px; text-align: center; line-height: 16px; font-size: 11px; font-weight: 900; margin-right: 4px;">C</span>
            <span class="site-name-text">{{ setting('site_name', config('app.name', 'CMS')) }}</span> Dashboard
        </a>

        @if($editUrl)
            <div class="divider"></div>
            <a href="{{ $editUrl }}">
                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                <span class="edit-label-text">{{ $editLabel }}</span>
            </a>
        @endif
    </div>

    <!-- Right Section -->
    <div class="admin-bar-right">
        <div class="user-info">
            <span class="avatar">
                {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
            </span>
            <span>Howdy, <strong>{{ $user->name ?? 'Admin' }}</strong></span>
        </div>
        
        <div class="divider"></div>
        
        <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0; display: inline;">
            @csrf
            <button type="submit">
                Logout
            </button>
        </form>
    </div>
</div>
@endauth
