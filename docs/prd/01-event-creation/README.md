# PRD 01: Event Creation

## Overview

Modul ini mendefinisikan fitur pembuatan dan pengelolaan event di Events plugin web-cms. Diadaptasi dari ERS ke dalam arsitektur Laravel. Tujuannya adalah memberikan admin kemampuan penuh membuat, mengedit, menerbitkan, dan mengelola event dengan wizard multi-step yang terstruktur.

**Current Status**: `EventController` di web-cms sudah memiliki CRUD dasar, tetapi belum ada wizard multi-step, auto-creation email templates, konfigurasi success page, banner upload, dan sender email per event.

---

## Current State

### web-cms (Target System)

**Existing:**
- Controller: `plugins/events/src/Http/Controllers/EventController.php` — CRUD + export CSV
- Model: `plugins/events/src/Models/Event.php` — SoftDeletes, relationships, scopes
- Table: `events` — title, slug, description, content, dates, type, location, registration fields, media, status, settings (JSON)

**Missing:**
- Wizard multi-step (ERS: 4 steps via AJAX)
- Auto-create email templates saat event dibuat
- Banner/logo upload terpisah
- Sender email config per event (`sender_email`, `sender_name`, `cc_to_email`)
- `sending_email` toggle flag
- Success page configuration (`success_title`, `success_desc`, `success_button`, `success_link`)
- `show_registered_count`, `enable_track_session`, `requires_corporate_email`
- `registration_start_date` dan `registration_end_date` (hanya ada `registration_deadline`)
- ABAC permission checks

### ERS (Reference System)

- Controller: `Backend\Events.php` — wizard multi-step (4 steps) dengan AJAX submission
- Auto-create 4 email templates (default/pending/approved/rejected) di `createDefaultEmailTemplates()`
- Steps: eventDetailForm → eventDateForm → eventPropertiesForm → eventSuccessForm
- Random 8-char alphanumeric slug (bukan title-based)
- Banner upload ke `uploads/events/{slug}/`

---

## Gap Analysis

| Feature | web-cms | ERS | Priority |
|---------|---------|-----|----------|
| CRUD Event dasar | ✅ | ✅ | ✅ Complete |
| Wizard multi-step | ❌ | ✅ (4 steps) | High |
| Auto-create email templates | ❌ | ✅ | High |
| Success page config | ❌ | ✅ | Medium |
| Banner/Logo upload | ❌ | ✅ | Medium |
| Sender email per event | ❌ | ✅ | High |
| sending_email toggle | ❌ | ✅ | High |
| requires_corporate_email | ❌ | ✅ | High |
| registration start/end date | ⚠️ (hanya deadline) | ✅ | Medium |
| show_registered_count | ❌ | ✅ | Low |
| enable_track_session | ❌ | ✅ | Low |
| ABAC permission | ❌ | ✅ | Medium |

---

## Feature Specification

### 1.1 Event Wizard Multi-Step (Admin)

**Lokasi**: Admin panel → Events → Create / Edit Event

**Flow (Create)**:
1. Admin klik "Create Event"
2. **Step 1 — Event Details**: Nama event, kategori, deskripsi, tipe (online/offline/hybrid), URL meeting/lokasi
3. **Step 2 — Date & Schedule**: Tanggal event (start/end), periode registrasi (start/end/deadline), timezone
4. **Step 3 — Properties**: Banner upload, konfigurasi email (sender, CC), quota, approval flag, corpo email flag
5. **Step 4 — Success Page**: Konfigurasi halaman sukses setelah registrasi

Data disimpan sebagai **draft** setelah setiap step, admin dapat kembali ke step sebelumnya.

**Implementasi**: Livewire multi-step component `EventWizard` atau tab-based navigation di halaman edit.

### 1.2 Event Fields Specification

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| title | text | Yes | Max 255 |
| slug | text | Auto | Dari title (unik, suffix jika duplikat) |
| description | textarea | No | SEO description |
| content | richtext | No | Konten HTML penuh |
| category_id | select | No | Dari `event_categories` |
| event_type | select | Yes | online / offline / hybrid |
| start_date | datetime | Yes | |
| end_date | datetime | No | |
| is_all_day | boolean | No | Default: false |
| timezone | select | No | Default: Asia/Jakarta |
| location | text | Conditional | Wajib jika offline/hybrid |
| location_address | textarea | No | |
| location_url | url | No | Google Maps link |
| online_meeting_url | url | Conditional | Wajib jika online/hybrid |
| registration_start_date | datetime | No | Kapan registrasi dibuka |
| registration_end_date | datetime | No | Deadline registrasi |
| requires_registration | boolean | No | Default: true |
| registration_requires_approval | boolean | No | Default: false |
| requires_corporate_email | boolean | No | Default: false |
| max_participants | integer | No | Null = unlimited |
| sending_email | boolean | No | Default: true |
| sender_email | email | No | Override global BREVO config |
| sender_name | text | No | Override global BREVO config |
| cc_to_email | email | No | CC notifikasi |
| banner_image | file | No | Upload banner event |
| status | select | Yes | draft/published/cancelled/completed |
| success_title | text | No | Judul halaman sukses |
| success_desc | textarea | No | |
| success_button | text | No | Label tombol CTA |
| success_link_type | select | No | `event` / `custom` |
| success_link | url | No | URL custom redirect |
| show_registered_count | boolean | No | Default: false |
| enable_track_session | boolean | No | Default: false |

### 1.3 Auto-Create Email Templates

Saat **Step 3 (Properties) pertama kali disimpan**, sistem otomatis membuat 4 baris di `approval_types`:

```php
// ApprovalTypeService::seedDefaultTemplates($event)
$categories = ['default', 'pending', 'approved', 'rejected'];

foreach ($categories as $cat) {
    ApprovalType::firstOrCreate(
        ['event_id' => $event->id, 'cat' => $cat],
        [
            'type_name'     => $defaults[$cat]['type_name'],
            'email_subject' => $defaults[$cat]['subject'],
            'email_body'    => $defaults[$cat]['body'],
        ]
    );
}
```

Konten default tiap template: lihat **PRD 07 — Email Customization** section 7.3.

### 1.4 Banner Upload

- Simpan ke `storage/app/public/events/{event_slug}/`
- Max size: 2MB; Accepted: jpg, jpeg, png, webp
- Kolom DB: `banner_image VARCHAR(255)`

### 1.5 Success Page Configuration

Setelah registrasi berhasil, user diarahkan ke halaman sukses yang dikonfigurasi per event:

- `success_link_type = 'event'` → redirect ke `/event/{slug}`
- `success_link_type = 'custom'` → redirect ke `success_link`
- Jika tidak dikonfigurasi → tampilkan halaman sukses generik

### 1.6 Publishing Workflow

```
draft → published (admin klik Publish)
published → draft  (admin klik Unpublish)
published → completed (otomatis/manual setelah event selesai)
published → cancelled (admin cancel)
```

---

## Database Schema Changes

```sql
ALTER TABLE events
    ADD COLUMN banner_image VARCHAR(255) NULL AFTER featured_image_id,
    ADD COLUMN registration_start_date TIMESTAMP NULL AFTER registration_deadline,
    ADD COLUMN registration_end_date TIMESTAMP NULL AFTER registration_start_date,
    ADD COLUMN requires_corporate_email BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN sending_email BOOLEAN NOT NULL DEFAULT TRUE,
    ADD COLUMN sender_email VARCHAR(255) NULL,
    ADD COLUMN sender_name VARCHAR(255) NULL,
    ADD COLUMN cc_to_email VARCHAR(255) NULL,
    ADD COLUMN success_title VARCHAR(255) NULL,
    ADD COLUMN success_desc TEXT NULL,
    ADD COLUMN success_button VARCHAR(100) NULL,
    ADD COLUMN success_link_type ENUM('event','custom') DEFAULT 'event',
    ADD COLUMN success_link VARCHAR(500) NULL,
    ADD COLUMN show_registered_count BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN enable_track_session BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN wizard_step TINYINT UNSIGNED DEFAULT 0;
```

---

## Implementation Notes

### Livewire EventWizard Component

```php
namespace Plugins\Events\Livewire;

class EventWizard extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;
    public int $totalSteps  = 4;
    public ?Event $event    = null; // null = create, Event = edit

    // Step 1
    public string $title       = '';
    public ?int $category_id   = null;
    public string $event_type  = 'offline';
    public ?string $description = null;

    // Step 2
    public string $start_date  = '';
    public ?string $end_date   = null;
    public bool $is_all_day    = false;
    public string $timezone    = 'Asia/Jakarta';
    public ?string $registration_start_date = null;
    public ?string $registration_end_date   = null;

    // Step 3
    public bool $requires_registration          = true;
    public bool $registration_requires_approval = false;
    public bool $requires_corporate_email       = false;
    public bool $sending_email                  = true;
    public ?string $sender_email = null;
    public ?string $sender_name  = null;
    public ?string $cc_to_email  = null;
    public ?int $max_participants = null;
    public $banner_image; // file upload

    // Step 4
    public ?string $success_title       = null;
    public ?string $success_desc        = null;
    public string $success_button       = 'Back to Event';
    public string $success_link_type    = 'event';
    public ?string $success_link        = null;
    public bool $show_registered_count  = false;

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->savePartialEvent();
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function submit(): void
    {
        $this->validateCurrentStep();
        $this->savePartialEvent();
        session()->flash('message', 'Event saved successfully.');
        $this->redirect(route('events.admin.index'));
    }
}
```

### Model Extensions (`Event.php`)

Tambahkan ke `$fillable`:
```php
'banner_image', 'registration_start_date', 'registration_end_date',
'requires_corporate_email', 'sending_email', 'sender_email', 'sender_name',
'cc_to_email', 'success_title', 'success_desc', 'success_button',
'success_link_type', 'success_link', 'show_registered_count',
'enable_track_session', 'wizard_step',
```

Tambahkan ke `$casts`:
```php
'registration_start_date'         => 'datetime',
'registration_end_date'           => 'datetime',
'requires_corporate_email'        => 'boolean',
'sending_email'                   => 'boolean',
'show_registered_count'           => 'boolean',
'enable_track_session'            => 'boolean',
```

Override `getIsRegistrationOpenAttribute()` untuk mendukung `registration_start_date`:
```php
public function getIsRegistrationOpenAttribute(): bool
{
    if (!$this->requires_registration) return false;
    if ($this->registration_start_date?->isFuture()) return false;  // belum dibuka
    if ($this->registration_end_date?->isPast()) return false;       // sudah tutup
    if ($this->max_participants && $this->registered_count >= $this->max_participants) return false;
    return true;
}
```

### Event Observer

```php
// plugins/events/src/Observers/EventObserver.php
class EventObserver
{
    public function created(Event $event): void
    {
        app(ApprovalTypeService::class)->seedDefaultTemplates($event);
    }
}
```

Daftarkan di `EventServiceProvider`:
```php
Event::observe(EventObserver::class);
```

### Permissions

Tambahkan ke `plugins/events/plugin.json`:
```json
"permissions": {
    "events.view":      "View events list",
    "events.create":    "Create new events",
    "events.edit.own":  "Edit own events",
    "events.edit.all":  "Edit all events",
    "events.delete":    "Delete events",
    "events.publish":   "Publish/unpublish events"
}
```

---

## Acceptance Criteria

### Phase 1: Core CRUD
- [ ] Admin dapat membuat event baru; semua field tersimpan ke database
- [ ] Slug auto-generated dari title, unik (suffix numerik jika duplikat)
- [ ] Event tampil di admin list setelah dibuat
- [ ] Admin dapat mengedit, menghapus (soft delete), dan mengubah status event

### Phase 2: Wizard Multi-Step
- [ ] Wizard menampilkan 4 step dengan progress indicator
- [ ] Setiap step memvalidasi field sebelum pindah
- [ ] Data disimpan sebagai draft setelah setiap step
- [ ] Admin dapat kembali ke step sebelumnya tanpa kehilangan data

### Phase 3: Email Template Auto-Creation
- [ ] 4 approval_type rows ter-create otomatis setelah event pertama kali disimpan
- [ ] Idempotent — tidak membuat duplikat jika sudah ada
- [ ] Deleting event men-cascade delete template rows

### Phase 4: Image Upload
- [ ] Banner image dapat diupload dari wizard step 3
- [ ] Validasi size (max 2MB) dan type
- [ ] Preview banner tampil di halaman edit

### Phase 5: Success Page
- [ ] Konfigurasi success page tersimpan per event
- [ ] Redirect ke URL yang benar sesuai `success_link_type`

### Phase 6: Email Config
- [ ] `sending_email` toggle memblokir semua email dispatch saat off
- [ ] Warning banner tampil di admin saat `sending_email = false`
- [ ] `sender_email`, `sender_name`, `cc_to_email` override global config

### Testing
- [ ] Unit test: slug uniqueness, `isRegistrationOpen` attribute
- [ ] Feature test: wizard step submission dan validasi
- [ ] Integration test: email template auto-creation on event created
