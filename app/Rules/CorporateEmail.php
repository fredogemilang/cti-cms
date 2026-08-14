<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates that an email address is not a free/disposable email provider.
 *
 * Generic core rule — usable from any validation context:
 * form builder fields (via `validation.rule` in Form Studio JSON),
 * CPT meta, plugins, or custom controllers.
 *
 * Usage:
 *   // Built-in blocked-domain list
 *   'email' => [new CorporateEmail()]
 *
 *   // Custom blocked domains + custom (e.g. translated) message
 *   'email' => [new CorporateEmail(['gmail.com', 'yahoo.com'], __('validation.corporate_email'))]
 *
 * The blocked-domain list can also be managed from the database via the
 * `validation.free_email_domains` setting (JSON array). Resolution order:
 * constructor param → setting → built-in default list.
 */
class CorporateEmail implements ValidationRule
{
    /**
     * Built-in free/disposable email domains.
     */
    public const DEFAULT_FREE_DOMAINS = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
        'aol.com', 'icloud.com', 'live.com', 'msn.com',
        'ymail.com', 'rocketmail.com', 'mail.com', 'gmx.com',
        'protonmail.com', 'tutanota.com', 'zoho.com',
        'inbox.com', 'rediffmail.com', 'mailinator.com',
        'tempmail.org', '10minutemail.com', 'guerrillamail.com',
    ];

    public function __construct(
        protected ?array $blockedDomains = null,
        protected ?string $message = null,
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value) {
            return;
        }

        if (! $this->isCorporateEmail((string) $value)) {
            $fail($this->message ?? 'A corporate email address is required. Free email providers (Gmail, Yahoo, etc.) are not allowed.');
        }
    }

    /**
     * Check if the given email is a corporate (non-free) email.
     */
    protected function isCorporateEmail(string $email): bool
    {
        $domain = strtolower(explode('@', $email)[1] ?? '');

        if (empty($domain)) {
            return false;
        }

        return ! in_array($domain, $this->freeDomains(), true);
    }

    /**
     * Resolve the blocked-domain list: constructor param → database setting
     * (`validation.free_email_domains`) → built-in defaults.
     */
    protected function freeDomains(): array
    {
        if ($this->blockedDomains !== null) {
            return $this->blockedDomains;
        }

        $configured = setting('validation.free_email_domains', null);

        return is_array($configured) ? $configured : self::DEFAULT_FREE_DOMAINS;
    }
}
