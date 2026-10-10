<?php

namespace App\Services;

use App\Models\EditorialNote;
use App\Models\User;
use App\Notifications\AdminAlert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class EditorialWorkflowService
{
    /**
     * Check if a user has editorial approval permission.
     */
    public function canApprove(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
            return true;
        }

        return method_exists($user, 'hasPermission') && $user->hasPermission('content.approve');
    }

    /**
     * Get allowed statuses for a user.
     *
     * @return array<string, string>
     */
    public function allowedStatuses(?User $user = null): array
    {
        if ($this->canApprove($user)) {
            return [
                'draft' => 'Draft',
                'pending_review' => 'Pending Review',
                'published' => 'Published',
                'scheduled' => 'Scheduled',
            ];
        }

        return [
            'draft' => 'Draft',
            'pending_review' => 'Pending Review',
        ];
    }

    /**
     * Resolve final status on save based on user permission.
     *
     * @return array{status: string, downgraded: bool, message: ?string}
     */
    public function resolveStatus(string $requestedStatus, ?User $user = null): array
    {
        $user ??= auth()->user();

        if (in_array($requestedStatus, ['published', 'scheduled'], true) && ! $this->canApprove($user)) {
            return [
                'status' => 'pending_review',
                'downgraded' => true,
                'message' => 'Status automatically set to Pending Review (requires editorial approval to publish).',
            ];
        }

        return [
            'status' => $requestedStatus,
            'downgraded' => false,
            'message' => null,
        ];
    }

    /**
     * Handle transition hooks: notifications, activity logs, editorial notes.
     */
    public function handleTransition(
        Model $model,
        string $newStatus,
        ?string $oldStatus = null,
        ?User $actor = null,
        ?string $note = null
    ): void {
        $actor ??= auth()->user();
        $actorName = $actor?->name ?? 'User';
        $title = $model->title ?? $model->name ?? class_basename($model).' #'.$model->getKey();
        $classBasename = class_basename($model);

        // 1. Entering pending_review
        if ($newStatus === 'pending_review' && $oldStatus !== 'pending_review') {
            $approvers = User::whereHas('roles', function ($q) {
                $q->where('is_super_admin', true)
                    ->orWhereHas('permissions', fn ($p) => $p->where('name', 'content.approve'));
            })->get();

            if ($approvers->isNotEmpty()) {
                Notification::send($approvers, new AdminAlert(
                    title: 'Content Pending Review',
                    message: "{$classBasename} \"{$title}\" was submitted for review by {$actorName}.",
                    icon: 'rate_review',
                    color: 'orange'
                ));
            }

            if (function_exists('activity')) {
                activity()->log(
                    'content.submitted_for_review',
                    $model,
                    "Submitted {$classBasename} '{$title}' for editorial review",
                    ['by' => $actorName, 'status' => 'pending_review']
                );
            }
        }

        // 2. Approved from pending_review -> published
        if ($oldStatus === 'pending_review' && $newStatus === 'published') {
            if ($model->author_id && $model->author && $model->author_id !== $actor?->id) {
                $model->author->notify(new AdminAlert(
                    title: 'Content Approved & Published',
                    message: "Your {$classBasename} \"{$title}\" was approved and published by {$actorName}.",
                    icon: 'check_circle',
                    color: 'green'
                ));
            }

            if (function_exists('activity')) {
                activity()->log(
                    'content.approved',
                    $model,
                    "Approved and published {$classBasename} '{$title}'",
                    ['by' => $actorName, 'status' => 'published']
                );
            }
        }

        // 3. Rejected / Changes Requested from pending_review -> draft with note
        if ($oldStatus === 'pending_review' && $newStatus === 'draft') {
            if ($note) {
                EditorialNote::create([
                    'noteable_type' => $model->getMorphClass(),
                    'noteable_id' => $model->getKey(),
                    'user_id' => $actor?->id,
                    'note' => $note,
                ]);
            }

            if ($model->author_id && $model->author && $model->author_id !== $actor?->id) {
                $noteMsg = $note ? ": {$note}" : '.';
                $model->author->notify(new AdminAlert(
                    title: 'Changes Requested',
                    message: "Changes requested on {$classBasename} \"{$title}\" by {$actorName}{$noteMsg}",
                    icon: 'assignment_late',
                    color: 'red'
                ));
            }

            if (function_exists('activity')) {
                activity()->log(
                    'content.changes_requested',
                    $model,
                    "Changes requested on {$classBasename} '{$title}'",
                    ['by' => $actorName, 'note' => $note, 'status' => 'draft']
                );
            }
        }
    }
}
