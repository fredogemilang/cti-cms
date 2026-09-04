<?php

namespace App\Observers;

use App\Jobs\IndexSearchable;
use App\Models\CptEntry;
use Illuminate\Database\Eloquent\Model;

class CptEntryObserver extends LogsActivity
{
    protected function activityName(): string
    {
        return 'entry';
    }

    protected function subjectLabel(Model $model): string
    {
        $type = $model->postType?->singular_label ?? 'entry';

        return "{$type}: ".($model->title ?? '(untitled)');
    }

    public function saved(Model $model): void
    {
        IndexSearchable::dispatch(CptEntry::class, $model->id);
    }

    public function deleted(Model $model): void
    {
        parent::deleted($model);
        IndexSearchable::dispatch(CptEntry::class, $model->id, 'unindex');
    }

    public function restored(Model $model): void
    {
        parent::restored($model);
        IndexSearchable::dispatch(CptEntry::class, $model->id);
    }
}
