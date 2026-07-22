<?php

namespace App\Livewire\Admin\Seo;

use App\Models\Media;
use App\Models\SeoMeta;
use Livewire\Attributes\On;
use Livewire\Component;

class SeoMetaBox extends Component
{
    public string $seoableType = '';

    public ?int $seoableId = null;

    /** Current locale being edited — synced from parent form via event. */
    public string $locale = '';

    public string $activeTab = 'seo';

    // SEO fields
    public ?string $title = null;

    public ?string $description = null;

    public ?string $canonical_url = null;

    public string $robots = 'index,follow';

    public ?string $og_title = null;

    public ?string $og_description = null;

    public ?int $og_image_id = null;

    public string $twitter_card = 'summary_large_image';

    public ?string $schema_type = null;

    public ?string $focus_keyword = null;

    public ?string $ai_summary = null;

    public bool $is_cornerstone = false;

    // Media picker state
    public bool $showMediaPicker = false;

    public ?string $ogImageUrl = null;

    public function mount(string $seoableType, ?int $seoableId = null, string $locale = ''): void
    {
        $this->seoableType = $seoableType;
        $this->seoableId = $seoableId;
        $this->locale = $locale;
        $this->loadExisting();
    }

    protected function loadExisting(): void
    {
        if (! $this->seoableId) {
            return;
        }
        $row = SeoMeta::where('seoable_type', $this->seoableType)
            ->where('seoable_id', $this->seoableId)
            ->where('locale', $this->locale)
            ->first();
        if (! $row) {
            // Reset to defaults when no data for this locale
            $this->resetSeoFields();

            return;
        }

        $this->fill($row->only([
            'title', 'description', 'canonical_url', 'robots',
            'og_title', 'og_description', 'og_image_id',
            'twitter_card', 'schema_type', 'focus_keyword',
            'ai_summary', 'is_cornerstone',
        ]));

        // Resolve OG image URL for display
        $this->resolveOgImageUrl();
    }

    /**
     * Reset all SEO fields to defaults (used when switching to a locale with no data).
     */
    protected function resetSeoFields(): void
    {
        $this->title = null;
        $this->description = null;
        $this->canonical_url = null;
        $this->robots = 'index,follow';
        $this->og_title = null;
        $this->og_description = null;
        $this->og_image_id = null;
        $this->ogImageUrl = null;
        $this->twitter_card = 'summary_large_image';
        $this->schema_type = null;
        $this->focus_keyword = null;
        $this->ai_summary = null;
        $this->is_cornerstone = false;
    }

    /**
     * Called from the parent editor when the locale changes.
     * Auto-saves current locale data first, then loads the new locale.
     */
    #[On('seo-locale-switched')]
    public function onLocaleSwitched(string $locale): void
    {
        if ($locale === $this->locale) {
            return;
        }

        // Auto-save current locale before switching
        $this->save();

        // Switch to new locale and load its data
        $this->locale = $locale;
        $this->loadExisting();
    }

    /**
     * Called from the parent editor after a successful save() so we can
     * attach SEO data to a freshly-created entity (when seoableId wasn't known on mount).
     */
    #[On('seo-attach')]
    public function attachTo(int $id): void
    {
        $this->seoableId = $id;
        $this->save();
    }

    public function save(): void
    {
        if (! $this->seoableId) {
            // Nothing to save yet; the parent will dispatch 'seo-attach' after creation.
            return;
        }

        // Only save if there is any data to save
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'canonical_url' => $this->canonical_url,
            'robots' => $this->robots ?: 'index,follow',
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image_id' => $this->og_image_id,
            'twitter_card' => $this->twitter_card ?: 'summary_large_image',
            'schema_type' => $this->schema_type,
            'focus_keyword' => $this->focus_keyword,
            'ai_summary' => $this->ai_summary,
            'is_cornerstone' => $this->is_cornerstone,
        ];

        // For non-default locales, only save if there's actual content
        if ($this->locale !== '') {
            $hasContent = collect($data)->only([
                'title', 'description', 'og_title', 'og_description',
                'focus_keyword', 'ai_summary',
            ])->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();

            if (! $hasContent) {
                // Delete the row if exists — no translated SEO data
                SeoMeta::where('seoable_type', $this->seoableType)
                    ->where('seoable_id', $this->seoableId)
                    ->where('locale', $this->locale)
                    ->delete();

                return;
            }
        }

        SeoMeta::updateOrCreate(
            [
                'seoable_type' => $this->seoableType,
                'seoable_id' => $this->seoableId,
                'locale' => $this->locale,
            ],
            $data
        );

        $this->dispatch('seo-saved');
    }

    public function getTitleLengthProperty(): int
    {
        return mb_strlen((string) $this->title);
    }

    public function getDescriptionLengthProperty(): int
    {
        return mb_strlen((string) $this->description);
    }

    /**
     * Resolve the display URL for the current og_image_id.
     */
    protected function resolveOgImageUrl(): void
    {
        if ($this->og_image_id) {
            $media = Media::find($this->og_image_id);
            $this->ogImageUrl = $media ? ($media->webp_url ?? $media->url) : null;
        } else {
            $this->ogImageUrl = null;
        }
    }

    // === MEDIA PICKER ===

    public function openOgImagePicker(): void
    {
        $this->showMediaPicker = true;
        $this->dispatch('open-media-picker', field: 'seo_og_image');
    }

    #[On('media-selected')]
    public function onMediaSelected(string $field, int $mediaId, string $mediaPath, string $mediaUrl): void
    {
        if ($field !== 'seo_og_image') {
            return;
        }

        $this->og_image_id = $mediaId;
        $this->ogImageUrl = $mediaUrl;
        $this->showMediaPicker = false;
    }

    #[On('media-picker-closed')]
    public function onMediaPickerClosed(): void
    {
        $this->showMediaPicker = false;
    }

    public function removeOgImage(): void
    {
        $this->og_image_id = null;
        $this->ogImageUrl = null;
    }

    public function render()
    {
        return view('livewire.admin.seo.seo-meta-box');
    }
}
