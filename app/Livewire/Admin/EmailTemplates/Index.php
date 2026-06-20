<?php

namespace App\Livewire\Admin\EmailTemplates;

use App\Models\EmailTemplate;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.email-templates.index', [
            'templates' => EmailTemplate::orderBy('name')->get(),
        ]);
    }
}
