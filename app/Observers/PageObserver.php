<?php

namespace App\Observers;

class PageObserver extends LogsActivity
{
    protected function activityName(): string
    {
        return 'page';
    }
}
