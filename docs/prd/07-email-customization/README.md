# PRD 07: Email Customization

## Overview

This document defines the feature set, architecture, and acceptance criteria for adapting ERS's email customization capabilities into web-cms. The goal is to give event organizers granular control over transactional emails sent to registrants — including per-event templates, a visual template editor, merge-field placeholders, sender configuration, and Brevo API integration as the primary email service provider.

---

## Current State

### web-cms (Target System)

- **Mail driver**: Laravel's native `config/mail.php` configuration only — no external ESP integration.
- **Event registration**: Registration form collects data and stores it in the database. No email is dispatched upon registration.
- **Email templates**: None — there is no `approval_type` table, no template wrapper, and no concept of template types.
- **Per-event email toggle**: Not implemented.
- **Sender configuration**: No per-event `sender_email`, `sender_name`, or `cc_to_email` fields exist.
- **QR code links**: No QR generation or embedding in emails.

### ERS (Reference System)

- **ESP**: Brevo API, abstracted via `app\Libraries\Brevo.php`.
- **Table `approval_type`**: Stores one row per event × category combination, with columns: `id`, `event_id`, `cat`, `type_name`, `email_subject`, `email_banner`, `email_body`.
- **Categories**: Four per event — `default`, `pending`, `approved`, `rejected`. Auto-created on event creation.
- **Template wrapper**: `app\Views\emails\allmail.php` wraps all outgoing email bodies.
- **Merge fields**: `{content}`, `{url_qr}`, event name, registrant name are replaced at send time.
- **Sender config**: `sender_email`, `sender_name`, `cc_to_email` per event.
- **QR link**: `base_url('qr/'.$event['slug'].'/'.$data['uuid'])` embedded in emails.
- **Per-event toggle**: `sending_email` flag on the event record controls whether emails are dispatched.

---

## Gap Analysis

| Feature | web-cms | ERS | Priority |
|---|---|---|---|
| ESP integration (Brevo API) | ❌ None | ✅ Brevo | P0 — Critical |
| Email sending on registration | ❌ None | ✅ Sends `pending` email | P0 — Critical |
| Per-event email templates | ❌ None | ✅ `approval_type` table | P0 — Critical |
| Four template types (`default`, `pending`, `approved`, `rejected`) | ❌ None | ✅ Per event | P1 — High |
| Template editor (UI) | ❌ None | ❌ Manual DB edit | P1 — High |
| Merge field substitution | ❌ None | ✅ `{content}`, `{url_qr}`, names | P1 — High |
| Sender configuration (from name, email, CC) | ❌ None | ✅ Per event | P1 — High |
| QR code link in email | ❌ None | ✅ UUID-based URL | P1 — High |
| Email preview (in-browser) | ❌ None | ❌ None | P2 — Medium |
| Per-event email toggle (`sending_email`) | ❌ None | ✅ Flag on event | P0 — Critical |
| Auto-create 4 templates on event creation | ❌ N/A | ✅ Trigger | P1 — High |

---

## Feature Specification

### 7.1 Email Service Provider Integration

**Decision**: Integrate Brevo as the default (and only) ESP for web-cms. Abstraction must follow the same pattern as ERS — a dedicated `app\Libraries\Brevo.php` library class wrapping the Brevo API v3.

**Requirements**:
- `Brevo.php` exposes at minimum: `sendEmail(to, subject, htmlContent, senderEmail, senderName, ccEmail)`.
- API key is stored in `.env` as `BREVO_API_KEY` and read via `config/services.php`.
- The `MailConfig` model (or a new `EmailConfig` model) holds per-event Brevo sender overrides, falling back to global config when null.
- If the global `MAIL_MAILER` is set to `log` or `array`, emails are written to the log instead of dispatched — enabling a developer/test mode without a live Brevo key.
- All outgoing emails MUST go through this library, not Laravel's native `Mail` facade directly.

**Fallback**: If Brevo is unreachable or returns a non-retryable error, log the failure with full payload context and surface a non-blocking admin notification.

---

### 7.2 Email Templates per Event

Each event owns a set of email templates. Templates are not global — they are scoped to `event_id`.

**Behavior**:
- On event creation, the system auto-creates exactly 4 template rows in the `approval_types` table (one per `cat` value: `default`, `pending`, `approved`, `rejected`) with sensible defaults.
- On event deletion, all associated template rows are cascade-deleted.
- Templates can be individually edited, previewed, and toggled via the admin UI.
- If `sending_email = false` on the event, no email is dispatched regardless of template content.

---

### 7.3 Template Types

| Category | Trigger | Purpose |
|---|---|---|
| `default` | Manual/administrative send | Fallback or informational email |
| `pending` | Registration submitted | Confirmation that registration was received |
| `approved` | Registration approved | Notifies registrant of acceptance |
| `rejected` | Registration rejected | Notifies registrant of rejection |

Each row stores: `event_id`, `cat`, `type_name`, `email_subject`, `email_banner`, `email_body`.

**Default content** (auto-populated on creation):

- **`pending`**:
  - Subject: `Registration Received — {{event_name}}`
  - Body: `Thank you for registering, {{registrant_name}}. Your registration is under review.`
- **`approved`**:
  - Subject: `You're In! — {{event_name}}`
  - Body: `Congratulations, {{registrant_name}}! Your registration has been approved.`
- **`rejected`**:
  - Subject: `Registration Update — {{event_name}}`
  - Body: `We're sorry, {{registrant_name}}. Your registration was not approved.`
- **`default`**:
  - Subject: `Message from {{event_name}}`
  - Body: `{{content}}`

---

### 7.4 Template Editor

A Livewire component (`ManageEventEmailTemplates`) renders a tabbed editor for the 4 templates under **Events → [Event] → Email Templates**.

**UI layout**:
- Tabs or accordion for each category (`pending`, `approved`, `rejected`, `default`).
- Fields per tab:
  - `type_name` — short label (e.g., "Registration Confirmation")
  - `email_subject` — text input
  - `email_banner` — file upload (image, max 1MB, accepted: jpg, png, gif, webp) stored in `storage/app/public/email-banners/`; stores banner URL in `email_banner`
  - `email_body` — rich-text editor (Tiptap or Quill); supports HTML; renders merge field toolbar
- Save button per template, with optimistic UI feedback.
- A "Reset to default" button per template to restore system-generated defaults.

**Validation**:
- `email_subject`: required, max 255 chars.
- `email_body`: required, no max (long HTML emails accepted).
- `email_banner`: nullable, image only, max 1MB.

---

### 7.5 Merge Fields / Placeholders

The template engine replaces the following placeholders at render time (before sending, not at save time):

| Placeholder | Source |
|---|---|
| `{{event_name}}` | `events.name` |
| `{{registrant_name}}` | `registrants.name` (or `first_name last_name`) |
| `{{content}}` | Arbitrary text passed at send time (used by `default` template) |
| `{{url_qr}}` | Generated QR code URL — see 7.7 |
| `{{registered_at}}` | `registrants.created_at` formatted |
| `{{status}}` | `pending`, `approved`, or `rejected` — used in body copy |

**Implementation**:
- Placeholder substitution is handled by a `EmailTemplateRenderer` service class.
- Method: `render(string $templateBody, array $data): string` — performs `str_replace` on all known placeholders.
- Unknown placeholders are left as-is (no error thrown).
- The renderer also handles `{{content}}` injection by accepting a `$content` parameter.

---

### 7.6 Sender Configuration

Each event has three configurable sender fields:

| Field | Type | Default |
|---|---|---|
| `sender_email` | string (email) | Global BREVO_DEFAULT_FROM_EMAIL from `.env` |
| `sender_name` | string | Global BREVO_DEFAULT_FROM_NAME from `.env` |
| `cc_to_email` | string (email, nullable) | `null` — no CC if empty |

- These are stored in the `events` table or a dedicated `event_email_config` pivot table. Storing in `events` is preferred for simplicity.
- The `sending_email` boolean flag on the event controls global dispatch.
- All three fields are editable in the Event Settings section of the admin UI.

---

### 7.7 QR Code Link in Email

The QR code link is generated from: `base_url('qr/' . $event['slug'] . '/' . $registrant['uuid'])`.

**Requirements**:
- The `qr` route must be registered in `routes/web.php`: `Route::get('qr/{slug}/{uuid}', [QrController::class, 'show'])->name('qr.show');`.
- `QrController::show` resolves the registrant by `uuid` and event by `slug`, then renders a QR code image using a library (e.g., `simplesoftwareio/simple-qrcode`).
- The QR code image is generated server-side and either:
  - **Option A**: Embedded as a base64 image in the email HTML directly (increases email size, works offline).
  - **Option B**: The `{url_qr}` placeholder resolves to a direct URL to the QR controller, which the email client renders as an image (lighter email, requires internet).
- **Decision**: Implement Option A (base64 embedded) for reliability across all email clients.
- The QR contains the URL pointing to the event's public registration confirmation page or a "check-in" page for walk-in scans.

---

### 7.8 Email Preview

A "Preview" button on each template tab renders the email as it would be sent, substituting merge fields with sample data.

**Sample data**:
- `event_name`: event's actual name
- `registrant_name`: "Jane Doe"
- `registered_at`: today's date formatted
- `status`: the category of the current tab
- `content`: "This is a sample message for the default template."
- `url_qr`: a valid QR code image (base64) generated for a dummy UUID

**Implementation**:
- Livewire component exposes a `previewHtml` computed property.
- Renders in a modal or split-pane using an iframe to isolate styles.
- Does not send an actual email.

---

### 7.9 Email Per-Event Toggle

A single boolean flag `sending_email` on the event record controls all email dispatch.

**Behavior**:
- When `sending_email = true`: emails are dispatched normally per registration workflow.
- When `sending_email = false`: `EmailDispatcher::shouldSend()` returns `false` — no email is sent, no error is thrown.
- The flag is displayed as a toggle switch in the Event Settings admin section, with a label: "Send registration emails to registrants".
- When toggled off, a yellow warning banner appears: "Email sending is disabled. Registrants will not receive confirmation or status emails."

**Dispatch triggers** (callable from registration workflow or admin actions):
- `EmailDispatcher::sendPending(Registrant $registrant)` — dispatches `pending` template.
- `EmailDispatcher::sendApproved(Registrant $registrant)` — dispatches `approved` template.
- `EmailDispatcher::sendRejected(Registrant $registrant)` — dispatches `rejected` template.
- `EmailDispatcher::sendDefault(Registrant $registrant, string $content)` — dispatches `default` template with custom content.

---

## Database Schema Changes

```sql
-- New table: approval_types (matches ERS structure)
CREATE TABLE approval_types (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id      BIGINT UNSIGNED NOT NULL,
    cat           ENUM('default', 'pending', 'approved', 'rejected') NOT NULL,
    type_name     VARCHAR(100) NOT NULL DEFAULT '',
    email_subject VARCHAR(255) NOT NULL,
    email_banner  VARCHAR(500) NULL,   -- URL or file path to banner image
    email_body    TEXT NOT NULL,
    created_at    TIMESTAMP NULL,
    updated_at    TIMESTAMP NULL,

    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY event_cat_unique (event_id, cat)
);

-- New columns on events table
ALTER TABLE events
    ADD COLUMN sending_email  BOOLEAN NOT NULL DEFAULT TRUE,
    ADD COLUMN sender_email    VARCHAR(255) NULL,
    ADD COLUMN sender_name     VARCHAR(255) NULL,
    ADD COLUMN cc_to_email     VARCHAR(255) NULL;

-- Index for QR code lookups (on registrants table, assumed existing)
-- Ensure uuid column exists and is indexed:
ALTER TABLE registrants ADD COLUMN uuid CHAR(36) NOT NULL UNIQUE;
```

**Migration files**:
- `database/migrations/xxxx_xx_xx_create_approval_types_table.php`
- `database/migrations/xxxx_xx_xx_add_email_config_to_events_table.php`
- `database/migrations/xxxx_xx_xx_add_uuid_to_registrants_table.php`

---

## Implementation Notes

1. **Brevo Library**: `app\Libraries\Brevo.php` should mirror the existing ERS implementation. All API calls are HTTP POST to `https://api.brevo.com/v3/smtp/email`. Use Guzzle or Laravel's HTTP client (`Http::post()`).

2. **Template Wrapper**: All emails are wrapped in `resources/views/emails/allmail.php` before sending. This wrapper receives `$subject`, `$bannerUrl`, and `$slot` (the rendered body) as Blade variables.

3. **Queue**: Email sending should be queued (`MailJob` implementing `ShouldQueue`) to prevent blocking HTTP responses during registration.

4. **Event Creation Hook**: On `Event::created`, fire a `CreatingApprovalTypes` listener or use model observers to auto-seed 4 template rows.

5. **Registration Flow**: `RegistrationController` (or Livewire component) calls `EmailDispatcher::sendPending()` after a successful `Registrant::create()`. Admin status-change actions call `sendApproved()` or `sendRejected()`.

6. **Seeders**: Create a seeder class `ApprovalTypeDefaults` with the default content per category (section 7.3) for re-use in both auto-creation and manual `php artisan db:seed`.

7. **Config files**: Add to `config/services.php`:
   ```php
   'brevo' => [
       'api_key' => env('BREVO_API_KEY'),
       'default_from_email' => env('BREVO_DEFAULT_FROM_EMAIL'),
       'default_from_name'  => env('BREVO_DEFAULT_FROM_NAME'),
   ],
   ```

8. **Admin navigation**: Add a sidebar item under Events: "Email Templates" linking to the Livewire component for the selected event.

9. **Banner uploads**: Use Laravel's `Storage` facade to persist to `public/email-banners/{event_id}/`. Generate a unique filename and store the path (relative to storage) in `email_banner`.

10. **Testability**: `EmailDispatcher` and `EmailTemplateRenderer` must be fully unit-testable with no real API calls. Use dependency injection so Brevo can be swapped with a `FakeBrevo` implementation in tests.

---

## Acceptance Criteria

- [ ] `app\Libraries\Brevo.php` sends a valid Brevo transactional email payload and returns success/failure.
- [ ] Creating a new event automatically creates 4 rows in `approval_types`, one per category.
- [ ] Deleting an event removes all its template rows (cascade).
- [ ] Admin can view and edit all 4 templates for any event via the Livewire UI.
- [ ] Admin can upload a banner image per template; it is stored and displayed in the wrapped email.
- [ ] All merge fields (`{{event_name}}`, `{{registrant_name}}`, `{{url_qr}}`, `{{content}}`, `{{registered_at}}`, `{{status}}`) are substituted correctly in sent emails.
- [ ] The `{url_qr}` placeholder resolves to a base64-encoded QR code image embedded in the HTML.
- [ ] The Preview button renders the email with sample data in a modal without sending.
- [ ] The `sending_email` toggle prevents all email dispatch when off; a warning is visible in admin.
- [ ] Sender config (`sender_email`, `sender_name`, `cc_to_email`) overrides global config when set.
- [ ] Emails are sent via a queued job (does not block HTTP response).
- [ ] When `BREVO_API_KEY` is absent or `MAIL_MAILER=log`, emails are logged rather than sent.
- [ ] All new fields are included in relevant Livewire form and validation rules.
- [ ] Unit tests cover `EmailTemplateRenderer` substitution and `EmailDispatcher` toggle logic.
