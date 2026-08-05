<?php

namespace Plugins\Posts\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Plugins\Posts\Models\Setting;

class Settings extends Component
{
    // General
    public $posts_per_page = 10;

    public $archive_slug = 'blog-news';

    public $archive_title = 'Blog & News';

    public $date_format = 'M d, Y';

    public array $archive_slug_translations = [];

    public array $archive_title_translations = [];

    // Comments
    public $enable_comments = true;

    public $comment_moderation = true;

    public $close_comments_days = 0; // 0 = never

    // Feed
    public $rss_full_text = false;

    public $rss_items = 10;

    // CPT Pairing
    public array $paired_cpts = [];

    public function mount()
    {
        $this->posts_per_page = Setting::get('posts_per_page', 10);
        $this->archive_slug = Setting::get('archive_slug', 'blog-news');
        $this->archive_title = Setting::get('archive_title', 'Blog & News');
        $this->date_format = Setting::get('date_format', 'M d, Y');

        $locales = function_exists('available_locales') ? available_locales() : ['id', 'en'];
        $defaultLocale = function_exists('setting') ? setting('default_locale', 'en') : 'en';

        foreach ($locales as $loc) {
            if ($loc !== $defaultLocale) {
                $this->archive_slug_translations[$loc] = Setting::get('archive_slug_'.$loc, '');
                $this->archive_title_translations[$loc] = Setting::get('archive_title_'.$loc, '');
            }
        }

        $this->enable_comments = (bool) Setting::get('enable_comments', true);
        $this->comment_moderation = (bool) Setting::get('comment_moderation', true);
        $this->close_comments_days = (int) Setting::get('close_comments_days', 0);

        $this->rss_full_text = (bool) Setting::get('rss_full_text', false);
        $this->rss_items = (int) Setting::get('rss_items', 10);

        $this->paired_cpts = json_decode(Setting::get('paired_cpts', json_encode(['technology-alliance'])), true) ?: ['technology-alliance'];
    }

    public function save()
    {
        $this->validate([
            'posts_per_page' => 'required|integer|min:1',
            'archive_slug' => 'required|string|max:255',
            'archive_title' => 'required|string|max:255',
            'date_format' => 'required|string',
            'enable_comments' => 'boolean',
            'comment_moderation' => 'boolean',
            'close_comments_days' => 'integer|min:0',
            'rss_full_text' => 'boolean',
            'rss_items' => 'required|integer|min:1',
        ]);

        Setting::set('posts_per_page', $this->posts_per_page);
        Setting::set('archive_slug', $this->archive_slug);
        Setting::set('archive_title', $this->archive_title);
        if (Schema::hasTable('settings')) {
            \App\Models\Setting::set('permalink_post_base', $this->archive_slug);
        }

        $locales = function_exists('available_locales') ? available_locales() : ['id', 'en'];
        $defaultLocale = function_exists('setting') ? setting('default_locale', 'en') : 'en';

        foreach ($locales as $loc) {
            if ($loc !== $defaultLocale) {
                $slugVal = trim($this->archive_slug_translations[$loc] ?? '');
                Setting::set('archive_slug_'.$loc, $slugVal);

                $titleVal = trim($this->archive_title_translations[$loc] ?? '');
                Setting::set('archive_title_'.$loc, $titleVal);
            }
        }

        Setting::set('date_format', $this->date_format);

        Setting::set('enable_comments', $this->enable_comments);
        Setting::set('comment_moderation', $this->comment_moderation);
        Setting::set('close_comments_days', $this->close_comments_days);

        Setting::set('rss_full_text', $this->rss_full_text);
        Setting::set('rss_items', $this->rss_items);

        Setting::set('paired_cpts', json_encode(array_values($this->paired_cpts)));

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Settings saved successfully.',
        ]);
    }

    public function toggleCptPairing(string $slug)
    {
        if (in_array($slug, $this->paired_cpts)) {
            $this->paired_cpts = array_values(array_filter($this->paired_cpts, fn ($s) => $s !== $slug));
        } else {
            $this->paired_cpts[] = $slug;
            $this->paired_cpts = array_values(array_unique($this->paired_cpts));
        }
    }

    public function render()
    {
        $availableCpts = DB::table('custom_post_types')
            ->select('id', 'name', 'singular_label', 'plural_label', 'slug', 'icon')
            ->orderBy('plural_label', 'asc')
            ->get();

        return view('posts::livewire.settings', [
            'availableCpts' => $availableCpts,
        ]);
    }
}
