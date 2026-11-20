<?php

namespace Plugins\PartnerRewardCatalog\Livewire;

use App\Models\Media;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Plugins\PartnerRewardCatalog\Support\CatalogStore;

class CatalogManager extends Component
{
    use WithFileUploads;

    public $pdfFile = null;

    public ?int $coverMediaId = null;

    public ?string $coverImagePath = null;

    public ?string $coverOriginalFilename = null;

    public bool $showMediaPicker = false;

    public string $titleEn = 'Partner Reward Catalog';

    public string $titleId = 'Katalog Reward Partner';

    public string $subtitleEn = '';

    public string $subtitleId = '';

    public string $promoTitleEn = 'Annual Partner Incentive Trip 2027';

    public string $promoTitleId = 'Annual Partner Incentive Trip 2027';

    public string $promoDescriptionEn = '';

    public string $promoDescriptionId = '';

    public string $promoPeriodEn = '';

    public string $promoPeriodId = '';

    public string $promoCtaTextEn = 'Explore the Partner Reward Catalog';

    public string $promoCtaTextId = 'Lihat Katalog Hadiah Mitra';

    public bool $allowDownload = true;

    public array $currentConfig = [];

    public ?string $activePdfUrl = null;

    public ?string $activeCoverUrl = null;

    public function mount(): void
    {
        $this->reloadState();
    }

    public function reloadState(): void
    {
        $this->currentConfig = CatalogStore::getConfig();
        $this->activePdfUrl = CatalogStore::pdfUrl($this->currentConfig);
        $this->activeCoverUrl = CatalogStore::coverImageUrl($this->currentConfig);
        $this->coverMediaId = $this->currentConfig['cover_media_id'];
        $this->coverImagePath = CatalogStore::coverImagePath($this->currentConfig);
        $this->coverOriginalFilename = $this->currentConfig['cover_original_filename'];
        $this->titleEn = $this->currentConfig['title_en'];
        $this->titleId = $this->currentConfig['title_id'];
        $this->subtitleEn = $this->currentConfig['subtitle_en'];
        $this->subtitleId = $this->currentConfig['subtitle_id'];
        $this->promoTitleEn = $this->currentConfig['promo_title_en'];
        $this->promoTitleId = $this->currentConfig['promo_title_id'];
        $this->promoDescriptionEn = $this->currentConfig['promo_description_en'];
        $this->promoDescriptionId = $this->currentConfig['promo_description_id'];
        $this->promoPeriodEn = $this->currentConfig['promo_period_en'];
        $this->promoPeriodId = $this->currentConfig['promo_period_id'];
        $this->promoCtaTextEn = $this->currentConfig['promo_cta_text_en'];
        $this->promoCtaTextId = $this->currentConfig['promo_cta_text_id'];
        $this->allowDownload = (bool) $this->currentConfig['allow_download'];
    }

    public function openCoverMediaPicker(): void
    {
        $this->showMediaPicker = true;
        $this->dispatch('open-media-picker', field: 'partner_cover_image');
    }

    #[On('media-selected')]
    public function onMediaSelected(string $field, int $mediaId, string $mediaPath, string $mediaUrl): void
    {
        if ($field !== 'partner_cover_image') {
            return;
        }

        $media = Media::find($mediaId);
        $this->coverMediaId = $mediaId;
        $this->coverImagePath = $mediaPath;
        $this->activeCoverUrl = $mediaUrl;
        $this->coverOriginalFilename = $media?->original_filename ?? basename($mediaPath);
        $this->showMediaPicker = false;
    }

    #[On('media-picker-closed')]
    public function onMediaPickerClosed(): void
    {
        $this->showMediaPicker = false;
    }

    public function clearCoverImage(): void
    {
        $this->coverMediaId = null;
        $this->coverImagePath = null;
        $this->coverOriginalFilename = null;
        $this->activeCoverUrl = null;
    }

    public function save(): void
    {
        $this->authorizeEdit();

        $this->validate([
            'pdfFile' => 'nullable|file|mimetypes:application/pdf,application/x-pdf|mimes:pdf|max:51200',
            'coverMediaId' => 'nullable|integer|exists:media,id',
            'coverImagePath' => 'nullable|string|max:500',
            'titleEn' => 'required|string|max:120',
            'titleId' => 'required|string|max:120',
            'subtitleEn' => 'nullable|string|max:400',
            'subtitleId' => 'nullable|string|max:400',
            'promoTitleEn' => 'required|string|max:160',
            'promoTitleId' => 'required|string|max:160',
            'promoDescriptionEn' => 'nullable|string|max:800',
            'promoDescriptionId' => 'nullable|string|max:800',
            'promoPeriodEn' => 'nullable|string|max:160',
            'promoPeriodId' => 'nullable|string|max:160',
            'promoCtaTextEn' => 'required|string|max:120',
            'promoCtaTextId' => 'required|string|max:120',
            'allowDownload' => 'boolean',
        ], [
            'pdfFile.mimes' => 'File katalog harus berformat PDF (.pdf).',
            'pdfFile.mimetypes' => 'File katalog harus berupa dokumen PDF yang valid.',
            'pdfFile.max' => 'Ukuran maksimal file PDF adalah 50 MB.',
        ]);

        $replacedPdf = $this->pdfFile !== null;
        $coverChanged = ($this->coverMediaId !== ($this->currentConfig['cover_media_id'] ?? null))
            || ($this->coverImagePath !== ($this->currentConfig['cover_image_path'] ?? null));

        CatalogStore::saveCatalog([
            'cover_media_id' => $this->coverMediaId,
            'cover_image_path' => $this->coverImagePath,
            'title_en' => $this->titleEn,
            'title_id' => $this->titleId,
            'subtitle_en' => $this->subtitleEn,
            'subtitle_id' => $this->subtitleId,
            'promo_title_en' => $this->promoTitleEn,
            'promo_title_id' => $this->promoTitleId,
            'promo_description_en' => $this->promoDescriptionEn,
            'promo_description_id' => $this->promoDescriptionId,
            'promo_period_en' => $this->promoPeriodEn,
            'promo_period_id' => $this->promoPeriodId,
            'promo_cta_text_en' => $this->promoCtaTextEn,
            'promo_cta_text_id' => $this->promoCtaTextId,
            'allow_download' => $this->allowDownload,
        ], $this->pdfFile);

        $this->pdfFile = null;
        $this->reloadState();

        $parts = [];
        if ($replacedPdf) {
            $parts[] = 'Catalog PDF replaced';
        }
        if ($coverChanged) {
            $parts[] = 'Partner cover updated';
        }

        session()->flash(
            'success',
            ! empty($parts)
                ? implode(' & ', $parts).' successfully!'
                : 'Catalog & Partner banner settings saved successfully!'
        );
    }

    public function removeCurrentPdf(): void
    {
        $this->authorizeEdit();
        CatalogStore::removePdf();
        $this->pdfFile = null;
        $this->reloadState();

        session()->flash('success', 'Current catalog PDF has been removed.');
    }

    public function removeCoverImage(): void
    {
        $this->authorizeEdit();
        CatalogStore::removeCoverImage();
        $this->clearCoverImage();
        $this->reloadState();

        session()->flash('success', 'Partner cover banner cleared. Default page media fallback is now active.');
    }

    protected function authorizeEdit(): void
    {
        $user = auth()->user();
        abort_unless($user && ($user->isSuperAdmin() || $user->hasPermission('partner-reward-catalog.edit')), 403);
    }

    public function render()
    {
        return view('partner-reward-catalog::livewire.catalog-manager');
    }
}
