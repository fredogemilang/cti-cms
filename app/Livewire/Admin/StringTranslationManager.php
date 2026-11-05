<?php

namespace App\Livewire\Admin;

use App\Models\StringTranslation;
use App\Models\StringTranslationKey;
use App\Services\TranslationScannerService;
use Livewire\Component;
use Livewire\WithPagination;

class StringTranslationManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $selectedGroup = 'all';

    public string $selectedSourceType = 'all';

    public string $statusFilter = 'all'; // all, missing, completed

    public string $targetLocale = '';

    public array $editingTranslations = [];

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $availableLocales = available_locales();
        $defaultLocale = (string) setting('default_locale', config('app.locale', 'en'));

        $nonDefault = array_values(array_filter($availableLocales, fn ($l) => $l !== $defaultLocale));
        $this->targetLocale = ! empty($nonDefault) ? $nonDefault[0] : ($availableLocales[0] ?? $defaultLocale);
    }

    public function setTargetLocale(string $locale): void
    {
        $availableLocales = available_locales();
        if (in_array($locale, $availableLocales, true)) {
            $this->targetLocale = $locale;
            $this->resetPage();
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedGroup(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Scan website strings across themes, plugins, and core.
     */
    public function scanStrings(TranslationScannerService $scanner): void
    {
        $discovered = $scanner->scanAll();
        $msg = 'Successfully scanned website strings! Discovered '.count($discovered).' translation items.';
        session()->flash('message', $msg);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $msg,
        ]);
        $this->resetPage();
    }

    /**
     * Save an inline translation for a key.
     */
    public function saveTranslation(int $keyId, ?string $value = null): void
    {
        $translationKey = StringTranslationKey::findOrFail($keyId);
        $val = $value !== null ? $value : ($this->editingTranslations[$keyId] ?? '');

        StringTranslation::updateOrCreate(
            [
                'translation_key_id' => $translationKey->id,
                'locale' => $this->targetLocale,
            ],
            [
                'value' => trim((string) $val),
            ]
        );

        $msg = "Saved translation for '{$translationKey->key}'!";
        session()->flash('message', $msg);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $msg,
        ]);
    }

    public function render()
    {
        $availableLocales = available_locales();
        $defaultLocale = (string) setting('default_locale', config('app.locale', 'en'));

        if (empty($this->targetLocale)) {
            $nonDefault = array_values(array_filter($availableLocales, fn ($l) => $l !== $defaultLocale));
            $this->targetLocale = ! empty($nonDefault) ? $nonDefault[0] : ($availableLocales[0] ?? $defaultLocale);
        }

        // Calculate progress stats per locale
        $totalKeysCount = StringTranslationKey::count();

        $stats = [];
        foreach ($availableLocales as $loc) {
            $translatedCount = StringTranslation::where('locale', $loc)
                ->whereNotNull('value')
                ->where('value', '!=', '')
                ->count();

            $percentage = $totalKeysCount > 0 ? round(($translatedCount / $totalKeysCount) * 100) : 100;
            $stats[$loc] = [
                'total' => $totalKeysCount,
                'translated' => $translatedCount,
                'percentage' => $percentage,
            ];
        }

        // Build query for keys
        $query = StringTranslationKey::with(['translations', 'sources']);

        if (! empty($this->search)) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('key', 'like', "%{$s}%")
                    ->orWhere('default_value', 'like', "%{$s}%")
                    ->orWhere('group', 'like', "%{$s}%")
                    ->orWhereHas('translations', function ($tq) use ($s) {
                        $tq->where('value', 'like', "%{$s}%");
                    });
            });
        }

        if ($this->selectedGroup !== 'all') {
            $query->where('group', $this->selectedGroup);
        }

        if ($this->selectedSourceType !== 'all') {
            $query->whereHas('sources', function ($sq) {
                $sq->where('source_type', $this->selectedSourceType);
            });
        }

        if ($this->statusFilter === 'missing') {
            $query->whereDoesntHave('translations', function ($tq) {
                $tq->where('locale', $this->targetLocale)
                    ->whereNotNull('value')
                    ->where('value', '!=', '');
            });
        } elseif ($this->statusFilter === 'completed') {
            $query->whereHas('translations', function ($tq) {
                $tq->where('locale', $this->targetLocale)
                    ->whereNotNull('value')
                    ->where('value', '!=', '');
            });
        }

        $translationKeys = $query->orderBy('group')->orderBy('key')->paginate(15);

        // Pre-fill editing values
        foreach ($translationKeys as $tk) {
            /** @var StringTranslation|null $trans */
            $trans = $tk->translations->firstWhere('locale', $this->targetLocale);
            $this->editingTranslations[$tk->id] = $trans ? $trans->value : '';
        }

        $groups = StringTranslationKey::select('group')->distinct()->pluck('group')->toArray();

        return view('livewire.admin.string-translation-manager', [
            'translationKeys' => $translationKeys,
            'stats' => $stats,
            'availableLocales' => $availableLocales,
            'defaultLocale' => $defaultLocale,
            'groups' => $groups,
        ])->layout('layouts.admin');
    }
}
