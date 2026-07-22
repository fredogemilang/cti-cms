<?php

namespace App\Traits;

use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasSeoMeta
{
    public function seoMeta(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    public function getOrCreateSeoMeta(): SeoMeta
    {
        /** @var SeoMeta */
        return $this->seoMeta()->firstOrCreate([]);
    }

    public function getResolvedSeoTitle(): string
    {
        /** @var SeoMeta|null $seoMeta */
        $seoMeta = $this->seoMeta;
        if ($seoMeta && ! empty($seoMeta->title)) {
            return $seoMeta->title;
        }

        /** @var mixed $title */
        $title = $this->getAttribute('title');

        return ! empty($title) ? (string) $title : (string) config('app.name');
    }

    public function getResolvedSeoDescription(): ?string
    {
        /** @var SeoMeta|null $seoMeta */
        $seoMeta = $this->seoMeta;
        if ($seoMeta && ! empty($seoMeta->description)) {
            return $seoMeta->description;
        }

        /** @var mixed $excerpt */
        $excerpt = $this->getAttribute('excerpt');

        return ! empty($excerpt) ? (string) $excerpt : null;
    }
}
