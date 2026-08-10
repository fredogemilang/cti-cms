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

## Multi-Language

`FormField.translations` JSON:
```json
{
  "id": {
    "label": "Nama",
    "placeholder": "Masukkan nama",
    "consent_text": "Saya setuju dengan <a href='/id/privacy'>kebijakan privasi</a>"
  }
}
```

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

## CAPTCHA Configuration

Configure in Admin → Settings → Forms:
- **reCAPTCHA**: `RECAPTCHA_SITE_KEY` + `RECAPTCHA_SECRET_KEY` in `.env`
- **Turnstile**: `TURNSTILE_SITE_KEY` + `TURNSTILE_SECRET_KEY` in `.env`

## Key Files

| File | Purpose |
|------|---------|
| `app/Models/Form.php` | Form model |
| `app/Models/FormField.php` | Field definitions |
| `app/Models/FormEntry.php` | Submissions |
| `app/Http/Controllers/FormSubmissionController.php` | Public submit handler |
| `app/Livewire/Admin/Forms/` | Admin form builder Livewire components |
| `themes/cdt/views/partials/tailwind-form.blade.php` | Reusable Tailwind form renderer |
