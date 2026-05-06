# PRD 05: Check-in

## Overview

Modul check-in memungkinkan panitia event untuk memverifikasi kehadiran tamu secara real-time pada hari event berlangsung. Tamu dapat melakukan check-in via QR code yang diterima di email konfirmasi, atau melalui pencarian manual oleh panitia. Modul ini berada di antara Guest List Approval (PRD 04) dan Feedback (PRD 06).

**Current Status**: web-cms sudah memiliki `status = 'attended'` pada `event_registrations`, namun tidak ada UI check-in, tidak ada QR code scanner, dan tidak ada dedicated endpoint untuk check-in. ERS memiliki sistem check-in penuh berbasis QR code scan + walk-in.

---

## Current State

### web-cms (Target System)

**Existing:**
- `EventRegistration::markAsAttended()` — method untuk mengubah status menjadi `attended`
- `status` column: `pending | confirmed | cancelled | attended`
- `Qrcode.php` controller belum ada di web-cms (hanya di ERS)
- Tidak ada route `/qr/{slug}/{uuid}` di web-cms
- `uuid` column belum ada di `event_registrations` (didefinisikan di PRD 02)

**Missing:**
- QR code generation saat registrasi terkonfirmasi
- Route dan controller untuk QR code scan
- Halaman check-in khusus untuk panitia (Officer App)
- Walk-in registration flow langsung dari halaman check-in
- Real-time check-in count update
- Check-in timestamp recording

### ERS (Reference System)

- **Qrcode.php**: Controller yang menerima `{event_slug}/{uuid}`, resolve registrant, redirect ke feedback page
- `check_in` boolean column + `check_in_date` timestamp di tabel registrant
- Walk-in: panitia bisa mendaftarkan tamu langsung dari halaman check-in (auto `check_in = 1`)
- Officer App: web app terpisah yang digunakan panitia untuk scan QR
- `feedback_require_checkin`: jika aktif, tamu harus check-in sebelum bisa isi feedback

---

## Gap Analysis

| Feature | web-cms | ERS | Priority |
|---------|---------|-----|----------|
| UUID per registrant | ❌ (PRD 02) | ✅ | P0 — Prerequisite |
| QR code generation | ❌ | ✅ | P0 |
| QR code scan endpoint | ❌ | ✅ `/qr/{slug}/{uuid}` | P0 |
| Check-in via QR | ❌ | ✅ | P0 |
| Check-in via manual search | ❌ | ✅ | P0 |
| Check-in UI (panitia) | ❌ | ✅ Officer App | P0 |
| Walk-in registration | ❌ | ✅ | P1 |
| Check-in timestamp | ❌ | ✅ `check_in_date` | P0 |
| Real-time check-in count | ❌ | ✅ | P1 |
| Feedback require check-in toggle | ❌ | ✅ | P1 |
| Check-in report / export | ❌ | ✅ | P1 |

---

## Feature Specification

### 5.1 Check-in Flow (QR Code)

**Alur utama**:
1. Tamu registrasi → menerima email konfirmasi (PRD 07)
2. Email berisi QR code yang meng-encode URL: `https://domain.com/qr/{event_slug}/{uuid}`
3. Panitia membuka scanner di Officer App / halaman check-in admin
4. Panitia scan QR tamu
5. Sistem:
   a. Resolve event via `slug`
   b. Resolve registrant via `uuid` + `event_id`
   c. Cek: apakah status `confirmed`? → lanjut
   d. Update `check_in = true`, `check_in_date = now()`
   e. Redirect ke halaman konfirmasi check-in dengan data tamu
6. Panitia melihat nama, perusahaan, foto profil (jika ada), status berhasil

**QR Route**:
```
GET /qr/{event_slug}/{uuid}
Route name: events.checkin.qr
Controller: EventCheckinController@scanQr
```

**Response**:
- Jika UUID valid dan status `confirmed`: check-in berhasil, tampilkan data tamu
- Jika UUID valid tapi sudah check-in: tampilkan pesan "Already checked in" + waktu check-in
- Jika UUID tidak valid: 404 / error message
- Jika status bukan `confirmed`: tampilkan pesan sesuai status (pending/cancelled)

### 5.2 Check-in Page (Admin / Officer)

**URL**: `/admin/events/{event_id}/checkin`

**UI Components**:
- **Summary Panel**: Total registrasi, total check-in, persentase kehadiran
- **Manual Search**: Input nama/email/perusahaan → autocomplete → klik untuk check-in
- **QR Scanner Panel**: Kamera web/mobile untuk scan QR code (menggunakan `jsQR` atau `html5-qrcode`)
- **Recent Check-ins**: List 10 check-in terbaru secara real-time
- **Walk-in Button**: Tombol "Add Walk-in Guest" untuk mendaftarkan tamu baru langsung di tempat

**Real-time Update**: Gunakan Livewire polling atau Laravel Reverb WebSocket untuk update check-in count.

### 5.3 Manual Check-in (Admin)

Admin/panitia dapat melakukan check-in manual dari:
1. **Check-in Page**: Cari nama/email → klik Check-in
2. **Guest List Page** (PRD 04): Row action "Check-in" di tabel guest list

**Endpoint**:
```
POST /admin/events/{event_id}/guests/{registration_id}/checkin
Route: events.admin.checkin
Controller: EventCheckinController@checkinByAdmin
```

**Validation**:
- Registrant harus ada dan milik event ini
- Status harus `confirmed` (tidak bisa check-in tamu `pending`/`cancelled`)
- Tidak ada double check-in (idempotent: jika sudah check-in, return sukses tanpa error)

### 5.4 Walk-in Registration

**Definisi**: Pendaftaran yang dilakukan langsung di tempat event oleh panitia, langsung dengan status check-in.

**Flow**:
1. Panitia klik "Add Walk-in Guest" di halaman check-in
2. Form singkat: nama, email, perusahaan, nomor telepon, tipe tamu
3. Submit → buat `event_registration` baru dengan:
   - `walk_in = true`
   - `check_in = true`
   - `check_in_date = now()`
   - `status = 'confirmed'`
   - UUID di-generate otomatis
4. Panitia bisa print/tunjukkan QR code untuk badge

**Endpoint**:
```
POST /admin/events/{event_id}/checkin/walkin
Controller: EventCheckinController@walkin
```

### 5.5 QR Code Generation

**Trigger**: QR code di-generate saat:
- Registrant pertama kali mendapat status `confirmed`
- Email approved dikirim (embed base64 QR di email body)

**Implementasi**: Gunakan package `simplesoftwareio/simple-qrcode`

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$qrUrl = route('events.checkin.qr', [
    'event_slug' => $event->slug,
    'uuid'       => $registration->uuid,
]);

// Generate inline untuk email
$qrBase64 = base64_encode(QrCode::format('png')->size(200)->generate($qrUrl));
$qrDataUri = 'data:image/png;base64,' . $qrBase64;

// Atau simpan ke file
$qrPath = "event-qr/{$registration->uuid}.png";
QrCode::format('png')->size(200)->generate($qrUrl, storage_path("app/public/{$qrPath}"));
$registration->update(['qr_image' => $qrPath]);
```

**QR Content Format**: URL lengkap ke endpoint check-in
```
https://domain.com/qr/{event_slug}/{uuid}
```

### 5.6 Check-in Report

**Endpoint**: `GET /admin/events/{event_id}/checkin/report`

**Data**:
- Total registered
- Total checked-in
- Total walk-in
- Attendance rate (%)
- Check-in timeline (per jam)
- Export CSV: nama, email, perusahaan, check-in time, walk-in status

---

## Database Schema Changes

```sql
-- Tambahkan ke event_registrations (jika belum ada dari PRD 02)
ALTER TABLE event_registrations
    ADD COLUMN uuid CHAR(36) UNIQUE NULL AFTER id,
    ADD COLUMN walk_in BOOLEAN NOT NULL DEFAULT FALSE AFTER uuid,
    ADD COLUMN check_in BOOLEAN NOT NULL DEFAULT FALSE AFTER walk_in,
    ADD COLUMN check_in_date TIMESTAMP NULL AFTER check_in,
    ADD COLUMN qr_image VARCHAR(255) NULL AFTER check_in_date,
    ADD INDEX idx_uuid (uuid),
    ADD INDEX idx_event_checkin (event_id, check_in);
```

---

## Implementation Notes

### Controller: `EventCheckinController`

```php
namespace Plugins\Events\Http\Controllers;

use Plugins\Events\Models\Event;
use Plugins\Events\Models\EventRegistration;

class EventCheckinController extends Controller
{
    /**
     * Handle QR code scan.
     * GET /qr/{event_slug}/{uuid}
     */
    public function scanQr(string $eventSlug, string $uuid)
    {
        $event = Event::where('slug', $eventSlug)->firstOrFail();

        $registration = EventRegistration::where('uuid', $uuid)
            ->where('event_id', $event->id)
            ->firstOrFail();

        if ($registration->status !== 'confirmed') {
            return view('events::checkin.invalid', compact('registration', 'event'));
        }

        if ($registration->check_in) {
            return view('events::checkin.already', compact('registration', 'event'));
        }

        $registration->update([
            'check_in'      => true,
            'check_in_date' => now(),
            'status'        => 'attended',
        ]);

        return view('events::checkin.success', compact('registration', 'event'));
    }

    /**
     * Manual check-in by admin.
     * POST /admin/events/{event}/guests/{registration}/checkin
     */
    public function checkinByAdmin(Event $event, EventRegistration $registration)
    {
        abort_unless($registration->event_id === $event->id, 404);
        abort_unless($registration->status === 'confirmed', 422, 'Only confirmed registrations can check in.');

        $registration->update([
            'check_in'      => true,
            'check_in_date' => now(),
            'status'        => 'attended',
        ]);

        return response()->json(['success' => true, 'message' => 'Check-in successful.']);
    }

    /**
     * Walk-in registration.
     * POST /admin/events/{event}/checkin/walkin
     */
    public function walkin(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name'         => 'required|max:255',
            'email'        => 'required|email|max:255',
            'organization' => 'nullable|max:255',
            'phone'        => 'nullable|max:20',
        ]);

        $registration = EventRegistration::create([
            'event_id'       => $event->id,
            'uuid'           => \Str::uuid(),
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'organization'   => $validated['organization'],
            'phone'          => $validated['phone'],
            'status'         => 'confirmed',
            'walk_in'        => true,
            'check_in'       => true,
            'check_in_date'  => now(),
            'ip_address'     => $request->ip(),
        ]);

        return response()->json([
            'success'      => true,
            'registration' => $registration,
        ]);
    }

    /**
     * Check-in page for admin/officer.
     * GET /admin/events/{event}/checkin
     */
    public function index(Event $event)
    {
        $stats = [
            'total'     => $event->registrations()->whereIn('status', ['confirmed', 'attended'])->count(),
            'checked_in' => $event->registrations()->where('check_in', true)->count(),
            'walk_in'   => $event->registrations()->where('walk_in', true)->count(),
        ];

        $recentCheckins = $event->registrations()
            ->where('check_in', true)
            ->orderByDesc('check_in_date')
            ->limit(10)
            ->get();

        return view('events::admin.checkin.index', compact('event', 'stats', 'recentCheckins'));
    }
}
```

### Routes

```php
// Public — QR code scan
Route::get('qr/{event_slug}/{uuid}', [EventCheckinController::class, 'scanQr'])
    ->name('events.checkin.qr');

// Admin — Check-in management
Route::prefix('admin/events/{event}')->middleware(['auth', 'permission:events.checkin'])->group(function () {
    Route::get('checkin', [EventCheckinController::class, 'index'])->name('events.admin.checkin.index');
    Route::post('checkin/walkin', [EventCheckinController::class, 'walkin'])->name('events.admin.checkin.walkin');
    Route::get('checkin/report', [EventCheckinController::class, 'report'])->name('events.admin.checkin.report');
});

// Admin — Manual check-in per guest
Route::post('admin/events/{event}/guests/{registration}/checkin', [EventCheckinController::class, 'checkinByAdmin'])
    ->middleware(['auth', 'permission:events.checkin'])
    ->name('events.admin.guests.checkin');
```

### Livewire: `CheckinDashboard`

```php
namespace Plugins\Events\Livewire;

class CheckinDashboard extends Component
{
    public Event $event;
    public string $searchQuery = '';

    public function getStatsProperty(): array
    {
        return [
            'total'      => $this->event->registrations()->whereIn('status', ['confirmed', 'attended'])->count(),
            'checked_in' => $this->event->registrations()->where('check_in', true)->count(),
        ];
    }

    public function getSearchResultsProperty()
    {
        if (strlen($this->searchQuery) < 2) return collect();

        return $this->event->registrations()
            ->where(function($q) {
                $q->where('name', 'like', "%{$this->searchQuery}%")
                  ->orWhere('email', 'like', "%{$this->searchQuery}%")
                  ->orWhere('organization', 'like', "%{$this->searchQuery}%");
            })
            ->whereIn('status', ['confirmed', 'attended'])
            ->limit(10)
            ->get();
    }

    public function checkin(int $registrationId): void
    {
        $registration = EventRegistration::findOrFail($registrationId);
        $registration->update([
            'check_in'      => true,
            'check_in_date' => now(),
            'status'        => 'attended',
        ]);
        $this->dispatch('checkin-success', name: $registration->name);
    }
}
```

### UUID Auto-Generation

Tambahkan ke `EventRegistration` model boot:
```php
protected static function boot(): void
{
    parent::boot();
    static::creating(function (EventRegistration $registration) {
        if (empty($registration->uuid)) {
            $registration->uuid = (string) \Str::uuid();
        }
    });
}
```

### Permissions

```json
"events.checkin":        "Access check-in management",
"events.checkin.walkin": "Add walk-in guests"
```

---

## Acceptance Criteria

### Phase 1: QR Code Foundation
- [ ] UUID di-generate otomatis untuk setiap registrant baru
- [ ] Route `GET /qr/{event_slug}/{uuid}` terdaftar dan berfungsi
- [ ] QR code ter-generate saat registrant dikonfirmasi
- [ ] QR code embed di email approved (base64)

### Phase 2: QR Scan Check-in
- [ ] Scan QR → resolve registrant dan event dengan benar
- [ ] Jika valid: update `check_in = true`, `check_in_date`, `status = attended`
- [ ] Jika sudah check-in: tampilkan halaman "Already Checked In" + waktu
- [ ] Jika UUID tidak valid: tampilkan error 404

### Phase 3: Admin Check-in Dashboard
- [ ] Halaman `/admin/events/{id}/checkin` menampilkan stats (total, checked-in, walk-in)
- [ ] Manual search berhasil mencari registrant by name/email/company
- [ ] Klik check-in dari hasil pencarian berhasil
- [ ] Recent check-ins list terupdate secara real-time (polling/WebSocket)

### Phase 4: Walk-in
- [ ] Form walk-in menghasilkan registration baru dengan `walk_in = true` dan `check_in = true`
- [ ] Walk-in registration muncul di guest list
- [ ] Walk-in count tampil di stats

### Phase 5: Report
- [ ] Report check-in menampilkan total, attendance rate, timeline
- [ ] Export CSV berisi semua data check-in

### Testing
- [ ] Unit test: UUID generation, check-in idempotency
- [ ] Feature test: QR scan flow (valid, invalid, already checked in)
- [ ] Feature test: Manual check-in by admin
- [ ] Feature test: Walk-in registration
