<?php

namespace App\Observers;

use App\Jobs\IndexSearchable;
use App\Models\Page;
use Illuminate\Database\Eloquent\Model;

class PageObserver extends LogsActivity
{
    protected function activityName(): string
    {
        return 'page';
    }

    public function saved(Model $model): void
    {
        IndexSearchable::dispatch(Page::class, $model->id);
    }

    public function deleted(Model $model): void
    {
        parent::deleted($model);
        IndexSearchable::dispatch(Page::class, $model->id, 'unindex');
    }

    public function restored(Model $model): void
    {
        parent::restored($model);
        IndexSearchable::dispatch(Page::class, $model->id);
    }
}
