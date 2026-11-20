<?php

namespace Plugins\PartnerRewardCatalog\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Plugins\PartnerRewardCatalog\Support\CatalogStore;

class CatalogManager extends Component
{
    use WithFileUploads;

    public $pdfFile = null;

    public $coverImageFile = null;

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

    public function save(): void
    {
        $this->authorizeEdit();

        $this->validate([
            'pdfFile' => 'nullable|file|mimetypes:application/pdf,application/x-pdf|mimes:pdf|max:51200',
            'coverImageFile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
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
            'coverImageFile.image' => 'Cover banner harus berupa file gambar yang valid.',
            'coverImageFile.mimes' => 'Cover banner harus berformat JPG, PNG, atau WebP.',
            'coverImageFile.max' => 'Ukuran maksimal cover banner adalah 10 MB.',
        ]);

        $replacedPdf = $this->pdfFile !== null;
        $replacedCover = $this->coverImageFile !== null;

        CatalogStore::saveCatalog([
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
        ], $this->pdfFile, $this->coverImageFile);

        $this->pdfFile = null;
        $this->coverImageFile = null;
        $this->reloadState();

        $parts = [];
        if ($replacedPdf) {
            $parts[] = 'Catalog PDF replaced';
        }
        if ($replacedCover) {
            $parts[] = 'Partner cover banner replaced';
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
        $this->coverImageFile = null;
        $this->reloadState();

        session()->flash('success', 'Active Partner cover banner has been removed.');
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
