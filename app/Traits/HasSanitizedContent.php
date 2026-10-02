<?php

namespace App\Traits;

use App\Services\ContentSanitizerService;

trait HasSanitizedContent
{
    /**
     * Boot the trait to automatically sanitize HTML content during Eloquent saving event.
     */
    public static function bootHasSanitizedContent(): void
    {
        static::saving(function ($model) {
            $sanitizer = app(ContentSanitizerService::class);

            // Clean 'content' column if populated and string
            if (isset($model->content) && is_string($model->content) && ! empty($model->content)) {
                $model->content = $sanitizer->sanitize($model->content);
            }

            // Clean 'value' column for PageBlock (wysiwyg/textarea)
            if (isset($model->value) && is_string($model->value) && ! empty($model->value)) {
                $type = $model->type ?? null;
                if (! $type || in_array($type, ['wysiwyg', 'textarea'], true)) {
                    $model->value = $sanitizer->sanitize($model->value);
                }
            }

            // Clean multi-locale translations JSON column
            if (! empty($model->translations) && is_array($model->translations)) {
                $translations = $model->translations;
                $changed = false;

                foreach ($translations as $locale => $fields) {
                    if (isset($fields['content']) && is_string($fields['content']) && ! empty($fields['content'])) {
                        $sanitized = $sanitizer->sanitize($fields['content']);
                        if ($sanitized !== $fields['content']) {
                            $translations[$locale]['content'] = $sanitized;
                            $changed = true;
                        }
                    }

                    if (isset($fields['value']) && is_string($fields['value']) && ! empty($fields['value'])) {
                        $sanitized = $sanitizer->sanitize($fields['value']);
                        if ($sanitized !== $fields['value']) {
                            $translations[$locale]['value'] = $sanitized;
                            $changed = true;
                        }
                    }
                }

                if ($changed) {
                    $model->translations = $translations;
                }
            }
        });
    }
}
