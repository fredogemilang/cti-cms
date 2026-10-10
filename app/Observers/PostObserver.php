<?php

namespace App\Observers;

use App\Jobs\IndexSearchable;
use Illuminate\Database\Eloquent\Model;

class PostObserver
{
    public function saved(Model $post): void
    {
        IndexSearchable::dispatch(get_class($post), $post->id);
    }

    public function deleted(Model $post): void
    {
        IndexSearchable::dispatch(get_class($post), $post->id, 'unindex');
    }

    public function restored(Model $post): void
    {
        IndexSearchable::dispatch(get_class($post), $post->id);
    }
}
