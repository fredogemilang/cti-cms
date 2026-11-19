# Form System

## Flow

```
Admin creates Form → assigns to theme placeholder → Blade renders via tailwind-form partial
User submits → FormSubmissionController → validation → FormEntry created → SendFormNotificationJob
```

## Models

| Model | Table | Purpose |
|-------|-------|---------|
| `Form` | `forms` | name, slug, is_active, submit_button_text, spam_protection, SoftDeletes |
| `FormField` | `form_fields` | form_id, label, field_id, type, is_required, placeholder, column_width, options, translations |
| `FormEntry` | `form_entries` | form_id, data (JSON), ip, user_agent |

## Assignment

Form-to-placeholder mapping stored in settings table:
```
setting("theme_{$theme->slug}_form_assignments")
→ {"contact_form": "2", "alliance_form": "4", "newsletter_form": "5"}
```

## Rendering (Tailwind)

```blade
@include('cdt::partials.tailwind-form', ['form' => $form, 'entry' => $entry])
```

Features: Alpine.js validation, captcha-aware, locale-aware labels, `*` for required fields, inline errors.

## Multi-Language & Anti-Redundancy Architecture

Both `Form` and `FormField` models natively use the `HasTranslations` trait (storing locale translations in JSON columns):

### 1. `Form` Model Translations (`forms.translations`)
Stores translatable attributes for `name`, `description`, `submit_button_text`, and `confirmations`:
```json
{
  "id": {
    "name": "Form Pengajuan AWS Cloud Credits",
    "description": "Spesialis AWS kami akan menghubungi Anda dalam 1 hari kerja.",
    "submit_button_text": "Kirim",
    "confirmations": {
      "message": "Terima kasih atas pengajuan Anda. Spesialis AWS kami akan menghubungi Anda dalam 1 hari kerja."
    }
  }
}
```

### 2. `FormField` Model Translations (`form_fields.translations`)
Stores translatable attributes for field labels, placeholders, consent text, and terms:
```json
{
  "id": {
    "label": "Nama Lengkap",
    "placeholder": "Masukkan nama lengkap Anda",
    "consent_text": "Saya setuju dengan <a href='/id/privacy'>kebijakan privasi</a>"
  }
}
```

### ⚠️ MANDATORY RULE: No Duplicate String Translations for Forms
When creating or editing form translations:
- AI agents **MUST** manage form translations directly inside the `Form` model (`forms.translations`) and `FormField` model (`form_fields.translations`) via Form Studio UI (`/ctrlpanel/forms/{id}/studio`).
- AI agents **MUST NEVER** create duplicate `string_translations` (`t()`) keys for form names, descriptions, submit buttons, or field labels.

---

## 🛑 MANDATORY RULE: Form Title & Description Disambiguation
When generating or implementing a form on any page, the AI agent **MUST NOT ASSUME** where the Form Title and Description originate.

Before writing code or seeding database records, the AI **MUST ask for user confirmation** with the following options:
1. **Pull directly from Form Model** (`$form->name` & `$form->description`) — Managed globally via Form Studio.
2. **Use Page Blocks / CPT Meta Fields** — Custom per page/entry.
3. **Use String Translations (`t()`)** — Static locale translation key.

---

## 🔒 MANDATORY RULE: Strict Form Assignment Compliance
ALL forms on the frontend **MUST strictly comply with Form Assignments** (`/ctrlpanel/forms/assignments`).

1. Form-to-slot mapping is stored in `settings`: `Setting::get("theme_{$theme->slug}_form_assignments")`.
2. **If a form is NOT assigned to a theme slot in Form Assignments, it MUST NOT render on the frontend.**
3. Theme Blade views must fetch the assigned form via:
   ```php
   $assignedForm = get_assigned_form('slot_name'); // Returns Form model or null
   ```
   If `$assignedForm` is null or inactive, the frontend template MUST safely hide/omit the form.

---

## Submission Flow

```
POST /forms/{slug}/submit
  ↓
honeypot check
  ↓
captcha verification (reCAPTCHA or Turnstile)
  ↓
per-field validation
  ↓
FormEntry::create()
  ↓
SendFormNotificationJob::dispatch()
  ↓
redirect back with success message
```

Failure at any step → redirect back with `$errors` and old input.

## Custom Validation Rule Classes

Beyond the built-in type checks, `FormField::validateValue()` supports named rule classes from `App\Rules\` — configured per field via the `validation` JSON in Form Studio:

```json
{
  "rule": "corporate_email",
  "rule_message": "Gunakan email perusahaan."
}
```

- `validation.rule` — registered rule name (currently: `corporate_email`)
- `validation.rule_message` — optional custom/translated message (falls back to the rule's default English message)

**`corporate_email`** (`App\Rules\CorporateEmail`): rejects free/disposable email providers. Blocked-domain list resolution order: rule constructor param → `validation.free_email_domains` setting (JSON array) → built-in defaults. The same rule class is reusable from any plugin or custom code:

```php
'email' => [new \App\Rules\CorporateEmail(['gmail.com'], __('validation.corporate_email'))]
```

## CAPTCHA Configuration

Configure in Admin → Settings → Forms:
- **reCAPTCHA**: `RECAPTCHA_SITE_KEY` + `RECAPTCHA_SECRET_KEY` in `.env`
- **Turnstile**: `TURNSTILE_SITE_KEY` + `TURNSTILE_SECRET_KEY` in `.env`

## Key Files

| File | Purpose |
|------|---------|
| `app/Models/Form.php` | Form model (`HasTranslations`) |
| `app/Models/FormField.php` | Field definitions (`HasTranslations`) |
| `app/Models/FormEntry.php` | Submissions |
| `app/Http/Controllers/FormSubmissionController.php` | Public submit handler |
| `resources/views/admin/forms/studio.blade.php` | Form Studio UI with unified `[ 🇬🇧 EN \| 🇮🇩 ID ]` switcher |
| `themes/cdt/views/partials/tailwind-form.blade.php` | Reusable Tailwind form renderer |
