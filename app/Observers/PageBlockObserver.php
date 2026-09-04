<?php

namespace App\Observers;

use App\Jobs\IndexSearchable;
use App\Models\Page;
use App\Models\PageBlock;

class PageBlockObserver
{
    public function saved(PageBlock $block): void
    {
        if ($block->page_id) {
            IndexSearchable::dispatch(Page::class, $block->page_id);
        }
    }

    public function deleted(PageBlock $block): void
    {
        if ($block->page_id) {
            IndexSearchable::dispatch(Page::class, $block->page_id);
        }
    }
}
