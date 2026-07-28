<?php

namespace App\Livewire\Admin;

use App\Services\IconLibraryService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class IconLibrariesSettings extends Component
{
    use WithFileUploads;

    public $uploadFile = null;

    public string $libraryName = '';

    public string $libraryPrefix = '';

    public bool $showUploadModal = false;

    public function openUploadModal(): void
    {
        $this->reset(['uploadFile', 'libraryName', 'libraryPrefix']);
        $this->showUploadModal = true;
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
    }

    public function uploadCustomLibrary(): void
    {
        $this->validate([
            'uploadFile' => 'required|file|mimes:json|max:5120',
            'libraryName' => 'required|string|max:100',
            'libraryPrefix' => 'required|string|alpha_dash|max:50',
        ]);

        $content = json_decode(File::get($this->uploadFile->getRealPath()), true);

        if (! is_array($content) || empty($content)) {
            $this->addError('uploadFile', 'Invalid icon pack JSON format. Must contain an array of icons.');

            return;
        }

        $icons = $content['icons'] ?? $content;
        $prefix = Str::slug($this->libraryPrefix, '-');

        $manifestData = [
            'name' => $this->libraryName,
            'prefix' => $prefix,
            'is_active' => true,
            'is_system' => false,
            'icons' => $icons,
        ];

        $targetDir = storage_path('app/icons');
        if (! File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $targetFile = "{$targetDir}/{$prefix}.json";
        File::put($targetFile, json_encode($manifestData, JSON_PRETTY_PRINT));

        app(IconLibraryService::class)->clearCache();

        session()->flash('success', "Icon library '{$this->libraryName}' uploaded successfully!");
        $this->closeUploadModal();
    }

    public function deleteLibrary(string $prefix): void
    {
        $targetFile = storage_path("app/icons/{$prefix}.json");
        if (File::exists($targetFile)) {
            File::delete($targetFile);
            app(IconLibraryService::class)->clearCache();
            session()->flash('success', 'Icon library deleted successfully.');
        }
    }

    public function toggleLibrary(string $prefix): void
    {
        $targetFile = storage_path("app/icons/{$prefix}.json");
        if (File::exists($targetFile)) {
            $content = json_decode(File::get($targetFile), true);
            if (is_array($content)) {
                $content['is_active'] = ! ($content['is_active'] ?? true);
                File::put($targetFile, json_encode($content, JSON_PRETTY_PRINT));
                app(IconLibraryService::class)->clearCache();
            }
        }
    }

    public function render()
    {
        $iconService = app(IconLibraryService::class);
        $libraries = $iconService->getLibraries();

        return view('livewire.admin.icon-libraries-settings', [
            'libraries' => $libraries,
        ]);
    }
}
