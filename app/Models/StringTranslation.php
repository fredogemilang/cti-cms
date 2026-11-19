<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $translation_key_id
 * @property string $locale
 * @property string|null $value
 * @property StringTranslationKey|null $key
 */
class StringTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'translation_key_id',
        'locale',
        'value',
    ];

    /**
     * Get the parent translation key.
     */
    public function key(): BelongsTo
    {
        return $this->belongsTo(StringTranslationKey::class, 'translation_key_id');
    }

    /**
     * Flush cache strictly per locale when a translation is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function (StringTranslation $translation) {
            Cache::forget("translations:{$translation->locale}");
        });

        static::deleted(function (StringTranslation $translation) {
            Cache::forget("translations:{$translation->locale}");
        });
    }

    /**
     * Build resolved dictionary for a given locale with fallback chain:
     * Requested Locale -> Fallback Locale -> default_value -> key
     */
    public static function getDictionary(string $locale): array
    {
        return Cache::remember("translations:{$locale}", 3600, function () use ($locale) {
            $defaultLocale = config('app.locale', 'en');
            $fallbackLocale = config('app.fallback_locale', 'en');

            // 1. Fetch all canonical keys
            $keys = StringTranslationKey::all();
            $dictionary = [];

            // Base initialization with default_value
            foreach ($keys as $k) {
                $fullKey = static::resolveFullKey($k->group, $k->key);
                $dictionary[$fullKey] = $k->default_value ?: $k->key;
            }

            // 2. Override with Fallback Locale values if requested locale is different
            if ($locale !== $fallbackLocale) {
                $fallbackTranslations = static::where('locale', $fallbackLocale)
                    ->whereNotNull('value')
                    ->where('value', '!=', '')
                    ->with('key')
                    ->get();

                foreach ($fallbackTranslations as $ft) {
                    if ($ft->key) {
                        $fullKey = static::resolveFullKey($ft->key->group, $ft->key->key);
                        $dictionary[$fullKey] = $ft->value;
                    }
                }
            }

            // 3. Override with Requested Locale values
            $requestedTranslations = static::where('locale', $locale)
                ->whereNotNull('value')
                ->where('value', '!=', '')
                ->with('key')
                ->get();

            foreach ($requestedTranslations as $rt) {
                if ($rt->key) {
                    $fullKey = static::resolveFullKey($rt->key->group, $rt->key->key);
                    $dictionary[$fullKey] = $rt->value;
                }
            }

            return $dictionary;
        });
    }

    /**
     * Resolve the full dictionary key, avoiding double-prefixing when
     * the key already starts with the group name (e.g. group='footer', key='footer.address').
     */
    private static function resolveFullKey(?string $group, string $key): string
    {
        // No group or generic 'ui' group — use key as-is
        if (! $group || $group === 'ui') {
            return $key;
        }

        // If the key already starts with the group prefix, don't add it again
        if (str_starts_with($key, "{$group}.")) {
            return $key;
        }

        return "{$group}.{$key}";
    }
}
