<?php

namespace Plugins\KouventaPages\Livewire;

use Livewire\Component;
use Plugins\KouventaPages\Models\KouventaPage;

class PageIndex extends Component
{
    public function render()
    {
        $pages = KouventaPage::orderBy('id', 'asc')->get();

        return view('kouventa-pages::livewire.page-index', [
            'pages' => $pages,
        ]);
    }
}

