# PRD 04: Guest List & Approval

## Overview
Modul ini memungkinkan admin mengelola daftar tamu yang mendaftar ke event, termasuk workflow approve/reject, pelacakan siapa yang menyetujui, kapasitas enforcement, inline editing, dan bulk actions. Ini adalah modul kritis yang menjembatani antara registrasi publik dan check-in.

---

## Current State

### web-cms (Target System)
- Tabel `event_registrations` memiliki field `status`: `pending → confirmed → cancelled / attended`
- `RegistrationsTable` Livewire component hanya menyediakan bulk approve / cancel / delete
- Tidak ada tracked record siapa yang menyetujui/tolak, kapan, dan alasan apa
- Tidak ada tab terpisah berdasarkan status dengan count badge
- Tidak ada inline editing dari guest list
- Tidak ada capacity check saat approval

### ERS (Reference System)
- Guest list menggunakan DataTables dengan tab pending/approved/rejected + real-time count badges
- Setiap approve/reject tercatat: `verified_by`, `verified_date`, `verified_type`, `from_subs`
- Capacity check saat approve — reject jika quota reached
- Inline editing dan modal-based editing
- Bulk approve all / reject all
- Approval types dengan alasan berbeda + email notification

---

## Gap Analysis

| Feature | web-cms | ERS | Priority |
|---------|---------|-----|----------|
| Guest list page per event | Basic table | DataTables + tabs + count badges | High |
| Approve action with capacity check | ❌ | ✅ | High |
| Reject action with reason | ❌ | ✅ | High |
| Verified tracking (who/when) | ❌ | ✅ | High |
| Approval types / reasons | ❌ | ✅ | Medium |
| Inline editing | ❌ | ✅ | Medium |
| Bulk approve / reject all | Partial (bulk cancel) | ✅ | Medium |
| Search & filter | Basic | Advanced (name, email, company, date) | Medium |
| Email on approve/reject | ❌ | ✅ | High |
| Registration type (reg_type) | ❌ | ✅ | Low |

---

## Feature Specification

### 4.1 Guest List Management Page

Admin membuka `/admin/events/{id}/guests` dan melihat DataTables atau Livewire table dengan:
- **Tab navigation**: All | Pending | Approved | Rejected
- **Count badges** di setiap tab: e.g., "Pending (12)", "Approved (85)"
- **Columns**: Name, Email, Company, Job Title, Status, Registered At, Actions
- **Row actions**: View, Approve, Reject, Edit, Check-in, Ban (doorprize)
- **Permission required**: `events.guests.view` atau `events.guests.manage`

```
GET /admin/events/{event_id}/guests
Route name: events.admin.guests.index
Controller: EventGuestController@index
```

### 4.2 Approve / Reject Actions

**Approve flow:**
1. Admin klik "Approve" pada satu row
2. Sistem cek: `if (event.is_approval && event.quota > 0)` — hitung approved count
3. Jika quota reached → tampilkan warning dialog
4. Jika belum penuh → update `status = 'confirmed'`, set `verified_by`, `verified_date`, `verified_type`
5. Trigger email "approved" template
6. Update `registered_count` di events table

**Reject flow:**
1. Admin klik "Reject" pada satu row
2. Modal popup: pilih rejection reason (dari `approval_types` table)
3. Update `status = 'cancelled'`, set verified tracking fields
4. Trigger email "rejected" template

```
POST /admin/events/{event_id}/guests/{id}/approve
POST /admin/events/{event_id}/guests/{id}/reject
```

### 4.3 Approval Types / Reasons

Admin dapat mendefinisikan template alasan untuk approve/reject, disimpan di tabel `approval_types`:
- `id`, `event_id`, `cat` (category: 'approved' | 'rejected'), `type_name`, `email_subject`, `email_banner`, `email_body`
- Pre-seeded: "Approved - General", "Rejected - Capacity Full", "Rejected - Invalid Info"
- Admin bisa tambah/edit dari event settings
- Dipakai sebagai `verified_type` saat approve/reject

### 4.4 Verified Tracking

Tambahkan kolom ke `event_registrations`:

| Column | Type | Description |
|--------|------|-------------|
| `verified_by` | bigint unsigned null | User ID yang approve/reject |
| `verified_at` | timestamp null | Waktu approve/reject |
| `verified_type` | string(100) null | Nama approval type yang dipilih |
| `verified_note` | text null | Catatan opsional dari admin |

### 4.5 Capacity Enforcement on Approval

Jika event memiliki `registration_requires_approval = true` dan `max_participants > 0`:
- Sebelum approve, hitung: `SELECT COUNT(*) WHERE event_id = ? AND status = 'confirmed'`
- Jika `confirmed_count >= max_participants` → block dengan error message "Event capacity reached"
- UI: tampilkan remaining slots di guest list header

### 4.6 Inline Editing

Dari guest list table, admin bisa edit langsung field:
- `name`, `email`, `phone`, `organization`, `notes`
- Klik icon edit → field jadi editable → blur/enter saves via AJAX
- Endpoint: `PATCH /admin/events/{event_id}/guests/{id}`

### 4.7 Bulk Actions

- **Approve All Pending**: Bulk update semua status `pending` → `confirmed`
- **Reject All Pending**: Bulk update + trigger rejection emails
- **Export CSV**: Download semua registrations dengan kolom lengkap
- **Filter by status** dropdown
- **Date range filter** untuk registered_at

### 4.8 Guest Search & Filter

- Full-text search: name, email, company
- Filter dropdowns: status, registration date
- Sort by: name, email, registered_at, status

---

## Database Schema Changes

### New Table: `approval_types`

```sql
CREATE TABLE approval_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    cat ENUM('approved', 'rejected') NOT NULL,
    type_name VARCHAR(100) NOT NULL,
    email_subject VARCHAR(255) NOT NULL,
    email_banner VARCHAR(500) NULL,
    email_body TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_event_cat (event_id, cat)
);
```

### Alter Table: `event_registrations`

```sql
ALTER TABLE event_registrations ADD COLUMN verified_by BIGINT UNSIGNED NULL;
ALTER TABLE event_registrations ADD COLUMN verified_at TIMESTAMP NULL;
ALTER TABLE event_registrations ADD COLUMN verified_type VARCHAR(100) NULL;
ALTER TABLE event_registrations ADD COLUMN verified_note TEXT NULL;
ALTER TABLE event_registrations ADD COLUMN uuid CHAR(36) NULL UNIQUE AFTER id;

ALTER TABLE event_registrations
    ADD CONSTRAINT fk_verified_by FOREIGN KEY (verified_by) REFERENCES users(id);
```

### Alter Table: `events` (already has registration_requires_approval, keep as is)

```sql
-- No new columns needed for approval — already supports it via:
-- registration_requires_approval, max_participants
```

---

## Implementation Notes

### Livewire Component
Buat `plugins\events\src\Livewire\EventGuestsTable.php`:
- Extends existing `RegistrationsTable` pattern
- Tab state: `activeTab` ('all' | 'pending' | 'confirmed' | 'cancelled')
- Computed property: `guestCounts` → `{pending: int, approved: int, rejected: int}`
- Actions: `approve($id)`, `reject($id)`, `bulkApprove()`, `bulkReject()`, `inlineUpdate($id, $field, $value)`

### DataTables Alternative
Jika preferensi ke DataTables, gunakan `yajra/laravel-datatables`:
- `EventRegistrationDataTable` class
- AJAX endpoints untuk approve/reject inline

### Queue for Email
Email dispatch on approve/reject harus di-queue (`ShouldQueue`) untuk performance.

### Permission
Tambahkan ke `plugins/events/plugin.json`:
```json
"permissions": {
    "events.guests.view": "View guest list",
    "events.guests.manage": "Manage guests (approve/reject)",
    "events.guests.export": "Export guest list"
}
```

---

## Acceptance Criteria

- [ ] Admin dapat membuka guest list page per event
- [ ] Tab pending/approved/rejected menampilkan count badge yang akurat
- [ ] Approve satu guest berhasil dan mencatat verified_by + verified_at
- [ ] Reject satu guest menampilkan modal pilih alasan
- [ ] Capacity check блокирует approve jika quota reached
- [ ] Bulk approve/reject all pending guest berfungsi
- [ ] Inline editing menyimpan perubahan via AJAX
- [ ] Search dan filter berfungsi
- [ ] Export CSV mengunduh semua data registrasi
- [ ] Email terkirim saat approve/reject (jika email module aktif)
