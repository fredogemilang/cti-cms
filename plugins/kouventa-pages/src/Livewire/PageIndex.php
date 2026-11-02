<?php

namespace Plugins\KouventaPages\Livewire;

use Livewire\Component;
use Plugins\KouventaPages\Models\KouventaPage;

class PageIndex extends Component
{
    public string $category = 'all';

    public function mount(string $category = 'all'): void
    {
        $this->category = $category;
    }

    public function render()
    {
        $query = KouventaPage::query()->orderBy('id', 'asc');

        if ($this->category === 'solutions') {
            $query->where('slug', 'like', 'solutions/%');
        } elseif ($this->category === 'products') {
            $query->where('slug', 'like', 'product/%');
        } elseif ($this->category === 'pricing') {
            $query->where('slug', 'pricing');
        }

        return view('kouventa-pages::livewire.page-index', [
            'pages' => $query->get(),
            'category' => $this->category,
        ]);
    }
}
