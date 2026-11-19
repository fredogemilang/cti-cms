<?php

namespace App\View\Components;

use App\Services\IconLibraryService;
use Illuminate\View\Component;

class Icon extends Component
{
    public function __construct(
        public ?string $name = null,
        public string $class = 'w-5 h-5'
    ) {}

    public function render()
    {
        return function (array $data) {
            $svg = app(IconLibraryService::class)->renderSvg($this->name, $this->class);

            return $svg ?: '<!-- Icon not found: '.e($this->name).' -->';
        };
    }
}
