<?php

namespace Plugins\PartnerRewardCatalog\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Plugins\PartnerRewardCatalog\Support\CatalogStore;

class CatalogManager extends Component
{
    use WithFileUploads;

    public $pdfFile = null;

    public string $titleEn = 'Partner Reward Catalog';

    public string $titleId = 'Katalog Reward Partner';

    public string $subtitleEn = '';

    public string $subtitleId = '';

    public bool $allowDownload = true;

    public array $currentConfig = [];

    public ?string $activePdfUrl = null;

    public function mount(): void
    {
        $this->reloadState();
    }

    public function reloadState(): void
    {
        $this->currentConfig = CatalogStore::getConfig();
        $this->activePdfUrl = CatalogStore::pdfUrl($this->currentConfig);
        $this->titleEn = $this->currentConfig['title_en'];
        $this->titleId = $this->currentConfig['title_id'];
        $this->subtitleEn = $this->currentConfig['subtitle_en'];
        $this->subtitleId = $this->currentConfig['subtitle_id'];
        $this->allowDownload = (bool) $this->currentConfig['allow_download'];
    }

    public function save(): void
    {
        $this->authorizeEdit();

        $this->validate([
            'pdfFile' => 'nullable|file|mimetypes:application/pdf,application/x-pdf|mimes:pdf|max:51200',
            'titleEn' => 'required|string|max:120',
            'titleId' => 'required|string|max:120',
            'subtitleEn' => 'nullable|string|max:400',
            'subtitleId' => 'nullable|string|max:400',
            'allowDownload' => 'boolean',
        ], [
            'pdfFile.mimes' => 'File katalog harus berformat PDF (.pdf).',
            'pdfFile.mimetypes' => 'File katalog harus berupa dokumen PDF yang valid.',
            'pdfFile.max' => 'Ukuran maksimal file PDF adalah 50 MB.',
        ]);

        $replaced = $this->pdfFile !== null;

        CatalogStore::saveCatalog([
            'title_en' => $this->titleEn,
            'title_id' => $this->titleId,
            'subtitle_en' => $this->subtitleEn,
            'subtitle_id' => $this->subtitleId,
            'allow_download' => $this->allowDownload,
        ], $this->pdfFile);

        $this->pdfFile = null;
        $this->reloadState();

        session()->flash(
            'success',
            $replaced
                ? 'Catalog PDF uploaded and replaced successfully! The live flipbook now displays the new PDF.'
                : 'Catalog settings updated successfully.'
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
