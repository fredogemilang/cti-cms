<?php

namespace App\Livewire\Admin\Webhooks;

use App\Jobs\DeliverWebhook;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use App\Services\WebhookDispatcher;
use Livewire\Component;

class Index extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $url = '';

    public string $deployment_id = '';

    public array $events = ['form.submitted'];

    public bool $is_active = true;

    public function startCreate(): void
    {
        $this->checkPermission('webhooks.create');
        $this->reset(['editingId', 'name', 'url', 'deployment_id', 'events', 'is_active']);
        $this->events = ['form.submitted'];
        $this->is_active = true;
        $this->showForm = true;
    }

    public function updatedDeploymentId($val): void
    {
        $val = trim($val);
        if ($val !== '') {
            // If user pasted a full URL into deployment_id, extract the ID
            if (preg_match('/\/macros\/s\/([^\/]+)\/exec/i', $val, $m)) {
                $val = $m[1];
                $this->deployment_id = $val;
            }
            $this->url = "https://script.google.com/macros/s/{$val}/exec";
            if (empty($this->name)) {
                $this->name = 'Google Sheets Webhook';
            }
        }
    }

    public function updatedUrl($val): void
    {
        $val = trim($val);
        if (preg_match('/\/macros\/s\/([^\/]+)\/exec/i', $val, $m)) {
            $this->deployment_id = $m[1];
        }
    }

    public function edit(int $id): void
    {
        $this->checkPermission('webhooks.edit');
        $w = Webhook::findOrFail($id);
        $this->editingId = $w->id;
        $this->name = $w->name;
        $this->url = $w->url;
        if (preg_match('/\/macros\/s\/([^\/]+)\/exec/i', $w->url, $m)) {
            $this->deployment_id = $m[1];
        } else {
            $this->deployment_id = '';
        }
        $this->events = $w->events ?? ['form.submitted'];
        $this->is_active = $w->is_active;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->checkPermission($this->editingId ? 'webhooks.edit' : 'webhooks.create');

        // Auto format deployment_id to URL if filled
        if (! empty($this->deployment_id) && empty($this->url)) {
            $this->url = 'https://script.google.com/macros/s/'.trim($this->deployment_id).'/exec';
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:500'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', 'in:'.implode(',', WebhookDispatcher::EVENTS)],
        ]);

        if ($this->editingId) {
            $w = Webhook::findOrFail($this->editingId);
            $w->update([
                'name' => $this->name, 'url' => $this->url,
                'events' => $this->events, 'is_active' => $this->is_active,
            ]);
        } else {
            Webhook::create([
                'name' => $this->name, 'url' => $this->url,
                'events' => $this->events, 'is_active' => $this->is_active,
                'signing_secret' => Webhook::generateSecret(),
                'created_by' => auth()->id(),
            ]);
        }

        $this->showForm = false;
        $this->reset(['editingId', 'name', 'url', 'events']);
        session()->flash('success', 'Webhook saved.');
    }

    public function delete(int $id): void
    {
        $this->checkPermission('webhooks.delete');
        Webhook::findOrFail($id)->delete();
        session()->flash('success', 'Webhook deleted.');
    }

    public function rotateSecret(int $id): void
    {
        $this->checkPermission('webhooks.edit');
        $w = Webhook::findOrFail($id);
        $w->update(['signing_secret' => Webhook::generateSecret()]);
        session()->flash('success', 'Secret rotated. New value visible in detail view.');
    }

    public function test(int $id): void
    {
        $this->checkPermission('webhooks.edit');
        $w = Webhook::findOrFail($id);

        $delivery = WebhookDelivery::create([
            'webhook_id' => $w->id,
            'event' => 'test.ping',
            'payload' => [
                'event' => 'test.ping',
                'timestamp' => now()->toAtomString(),
                'data' => ['message' => 'This is a test payload from the admin UI.'],
            ],
            'status' => 'pending',
        ]);
        DeliverWebhook::dispatch($delivery->id);
        session()->flash('success', "Test delivery queued (#{$delivery->id}).");
    }

    protected function checkPermission(string $perm): void
    {
        abort_unless(auth()->user()?->hasPermission($perm), 403);
    }

    public function render()
    {
        return view('livewire.admin.webhooks.index', [
            'webhooks' => Webhook::latest()->get(),
            'eventOptions' => WebhookDispatcher::EVENTS,
        ]);
    }
}
