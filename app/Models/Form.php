<?php

namespace App\Models;

use App\Jobs\SendFormNotificationJob;
use App\Services\CaptchaService;
use App\Services\FormConditionalLogic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use App\Traits\HasTranslations;

class Form extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected array $translatable = [
        'name',
        'description',
        'submit_button_text',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'settings',
        'form_type',
        'steps',
        'notifications',
        'confirmations',
        'spam_protection',
        'has_conditional_logic',
        'total_entries',
        'submit_button_text',
        'styling',
        'translations',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_conditional_logic' => 'boolean',
        'settings' => 'array',
        'steps' => 'array',
        'notifications' => 'array',
        'confirmations' => 'array',
        'spam_protection' => 'array',
        'styling' => 'array',
        'translations' => 'array',
    ];

    /**
     * Get localized confirmation message based on active locale.
     */
    public function getConfirmationMessage(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $translations = $this->translations ?? [];
        $transMessage = $translations[$locale]['confirmations']['message'] ?? null;

        if (! empty($transMessage)) {
            return $transMessage;
        }

        return $this->confirmations['message'] ?? 'Thank you for your submission. We will get back to you soon.';
    }

    /**
     * Get localized redirect URL based on active locale.
     */
    public function getConfirmationRedirectUrl(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $translations = $this->translations ?? [];
        $transUrl = $translations[$locale]['confirmations']['redirect_url'] ?? null;

        if (! empty($transUrl)) {
            return $transUrl;
        }

        return $this->confirmations['redirect_url'] ?? url('/');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($form) {
            if (empty($form->slug)) {
                $form->slug = Str::slug($form->name);
            }

            // Ensure unique slug (including soft-deleted records)
            $originalSlug = $form->slug;
            $counter = 1;
            while (static::withTrashed()->where('slug', $form->slug)->exists()) {
                $form->slug = $originalSlug.'-'.$counter;
                $counter++;
            }
        });
    }

    /**
     * Get the fields for the form.
     *
     * @return HasMany<FormField, $this>
     */
    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    /**
     * Get the entries for the form.
     *
     * @return HasMany<FormEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(FormEntry::class);
    }

    /**
     * Get the user who last updated the form.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Render the form HTML.
     */
    public function renderForm($attributes = [])
    {
        $defaultAttributes = [
            'method' => 'POST',
            'action' => route('forms.submit', $this->slug),
            'class' => 'form-dynamic',
        ];

        $attributes = array_merge($defaultAttributes, $attributes);
        $attributeString = collect($attributes)
            ->map(fn ($value, $key) => "{$key}=\"{$value}\"")
            ->implode(' ');

        $html = "<form {$attributeString}>";
        $html .= csrf_field();

        // Spam protection - honeypot field
        if ($this->spam_protection['honeypot'] ?? false) {
            $html .= '<div style="display:none;"><input type="text" name="website_url" tabindex="-1" autocomplete="off"></div>';
        }

        // Render fields with row wrapper for multi-column support
        $html .= '<div class="row">';
        foreach ($this->fields as $field) {
            // Wrap each field with data attribute for conditional logic
            $fieldHtml = $field->renderField();
            $fieldHtml = str_replace(
                '<div class="form-group',
                '<div data-field-id="'.$field->field_id.'" class="form-group',
                $fieldHtml
            );
            $html .= $fieldHtml;
        }
        $html .= '</div>';

        // Render CAPTCHA widget if configured
        $captchaProvider = $this->spam_protection['captcha_provider'] ?? 'none';
        if ($captchaProvider !== 'none') {
            $captchaService = new CaptchaService;
            $html .= $captchaService->renderWidget($captchaProvider);
        }

        // Submit button with custom text
        $buttonText = $this->submit_button_text ?? 'Submit';
        $html .= '<button type="submit" class="btn btn-primary">'.e($buttonText).'</button>';
        $html .= '</form>';

        // Add conditional logic JavaScript if any field has conditions
        $hasConditions = $this->fields->some(fn ($f) => ! empty($f->conditional_logic['conditions']));
        if ($hasConditions) {
            $conditionalLogic = new FormConditionalLogic;
            $html .= $conditionalLogic->renderJavaScript($this);
        }

        return $html;
    }

    /**
     * Process form submission.
     */
    public function processSubmission(array $data, $request = null)
    {
        $validatedData = [];
        $errors = [];

        // Check honeypot spam protection
        if ($this->spam_protection['honeypot'] ?? false) {
            $honeypotValue = $data['website_url'] ?? null;
            if (! empty($honeypotValue)) {
                // Bot detected - silently reject
                return ['success' => false, 'errors' => ['spam' => 'Submission rejected.']];
            }
            // Remove honeypot from data
            unset($data['website_url']);
        }

        // Verify CAPTCHA if configured
        $captchaProvider = $this->spam_protection['captcha_provider'] ?? 'none';
        if ($captchaProvider !== 'none') {
            $captchaService = new CaptchaService;
            $responseField = $captchaService->getResponseFieldName($captchaProvider);
            $captchaResponse = $data[$responseField] ?? '';

            $ip = $request ? $request->ip() : null;
            if (! $captchaService->verify($captchaProvider, $captchaResponse, $ip)) {
                return ['success' => false, 'errors' => ['captcha' => 'CAPTCHA verification failed. Please try again.']];
            }

            // Remove captcha response from data
            unset($data[$responseField]);
        }

        // Initialize conditional logic evaluator
        $conditionalLogic = new FormConditionalLogic;

        foreach ($this->fields as $field) {
            $value = $data[$field->field_id] ?? null;

            // Check if field is visible based on conditional logic
            $isVisible = $conditionalLogic->evaluateVisibility($field, $data);

            // Skip validation for hidden fields
            if (! $isVisible) {
                continue;
            }

            // Validate field
            $validation = $field->validateValue($value);
            if ($validation !== true) {
                $errors[$field->field_id] = $validation;
            } else {
                $validatedData[$field->field_id] = $value;
            }
        }

        if (! empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Auto-capture server-side & client-side attribution metadata (Approach B)
        $attributionData = [];
        if (! empty($data['_attribution'])) {
            $attributionData = is_array($data['_attribution'])
                ? $data['_attribution']
                : (json_decode($data['_attribution'], true) ?? []);
        } elseif ($request && $request->hasCookie('cdt_attribution')) {
            $attributionData = json_decode($request->cookie('cdt_attribution'), true) ?? [];
        }

        $attributionData['submission_page'] = $data['_submission_page'] ?? ($request ? $request->fullUrl() : null);
        $attributionData['http_referrer'] = $request ? $request->header('referer') : null;

        // Calculate time to convert (supports both ISO date strings like "2026-08-03T05:04:20.373Z" and numeric timestamps)
        if (! empty($attributionData['first_visit_at'])) {
            $rawVisit = $attributionData['first_visit_at'];
            $firstVisitSec = 0;

            if (is_numeric($rawVisit)) {
                $firstVisitSec = (float) $rawVisit;
                if ($firstVisitSec > 100000000000) {
                    $firstVisitSec = floor($firstVisitSec / 1000);
                }
            } else {
                $parsedTime = strtotime((string) $rawVisit);
                if ($parsedTime !== false && $parsedTime > 0) {
                    $firstVisitSec = $parsedTime;
                }
            }

            if ($firstVisitSec > 0) {
                $nowSec = time();
                $diffSec = max(0, $nowSec - (int) $firstVisitSec);

                if ($diffSec < 60) {
                    $attributionData['time_to_convert'] = $diffSec.'s';
                } elseif ($diffSec < 3600) {
                    $attributionData['time_to_convert'] = floor($diffSec / 60).'m '.($diffSec % 60).'s';
                } else {
                    $attributionData['time_to_convert'] = floor($diffSec / 3600).'h '.floor(($diffSec % 3600) / 60).'m';
                }
            }
        }

        // Parse User Agent for Browser & Operating System
        $ua = $request ? $request->userAgent() : null;
        if ($ua) {
            // Parse OS
            if (preg_match('/windows nt 10/i', $ua)) {
                $attributionData['os'] = 'Windows 10/11';
            } elseif (preg_match('/windows/i', $ua)) {
                $attributionData['os'] = 'Windows';
            } elseif (preg_match('/macintosh|mac os x/i', $ua)) {
                $attributionData['os'] = 'macOS';
            } elseif (preg_match('/android/i', $ua)) {
                $attributionData['os'] = 'Android';
            } elseif (preg_match('/iphone|ipad|ipod/i', $ua)) {
                $attributionData['os'] = 'iOS';
            } elseif (preg_match('/linux/i', $ua)) {
                $attributionData['os'] = 'Linux';
            } else {
                $attributionData['os'] = 'Unknown OS';
            }

            // Parse Browser
            if (preg_match('/edg/i', $ua)) {
                $attributionData['browser'] = 'Edge';
            } elseif (preg_match('/chrome/i', $ua)) {
                $attributionData['browser'] = 'Chrome';
            } elseif (preg_match('/safari/i', $ua)) {
                $attributionData['browser'] = 'Safari';
            } elseif (preg_match('/firefox/i', $ua)) {
                $attributionData['browser'] = 'Firefox';
            } elseif (preg_match('/opera|opr/i', $ua)) {
                $attributionData['browser'] = 'Opera';
            } else {
                $attributionData['browser'] = 'Unknown Browser';
            }
        }

        $attributionData = array_filter($attributionData);

        if (! empty($attributionData)) {
            $validatedData['_attribution'] = $attributionData;
        }

        // Create entry
        $entry = $this->entries()->create([
            'data' => $validatedData,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'user_id' => auth()->id(),
        ]);

        // Dispatch notifications onto the queue so the public POST returns quickly.
        // (With QUEUE_CONNECTION=sync this still runs inline.)
        SendFormNotificationJob::dispatch($this->id, $entry->id);

        return ['success' => true, 'entry' => $entry];
    }

    /**
     * Get form statistics.
     */
    public function getStats()
    {
        return [
            'total_entries' => $this->entries()->count(),
            'entries_today' => $this->entries()->whereDate('created_at', today())->count(),
            'entries_this_week' => $this->entries()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'entries_this_month' => $this->entries()->whereMonth('created_at', now()->month)->count(),
        ];
    }
}
