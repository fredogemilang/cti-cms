<?php

namespace Plugins\RmaXyora\Observers;

use App\Models\FormEntry;
use Plugins\RmaXyora\Services\RmaNotificationService;

class FormEntryObserver
{
    protected RmaNotificationService $notificationService;

    public function __construct(RmaNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the FormEntry "creating" event.
     */
    public function creating(FormEntry $entry): void
    {
        $form = $entry->form ?: \App\Models\Form::find($entry->form_id);
        
        if ($form && $form->slug === 'rma-form') {
            // Overrule default status to pending for RMA requests
            $entry->status = 'pending';
        }
    }

    /**
     * Handle the FormEntry "created" event.
     */
    public function created(FormEntry $entry): void
    {
        $form = $entry->form ?: \App\Models\Form::find($entry->form_id);

        if ($form && $form->slug === 'rma-form') {
            // Send email to customer on submission
            $this->notificationService->sendRmaCreatedNotification($entry);
        }
    }

    /**
     * Handle the FormEntry "updated" event.
     */
    public function updated(FormEntry $entry): void
    {
        $form = $entry->form ?: \App\Models\Form::find($entry->form_id);

        if ($form && $form->slug === 'rma-form') {
            // Send status change notification only if status has indeed been updated
            if ($entry->wasChanged('status')) {
                $this->notificationService->sendRmaStatusUpdatedNotification($entry);
            }
        }
    }
}
