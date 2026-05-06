# PRD 08: Doorprize

## Overview

Modul Doorprize memungkinkan admin event untuk mengorganisir sesi undian hadiah selama atau setelah event berlangsung. Admin dapat membuat round (sesi), mendefinisikan hadiah per round, mengatur kriteria eligibilitas peserta (check-in, feedback, dll.), melakukan undian secara live, dan mencatat pemenang. Diadaptasi dari ERS `EventDoorprizeController` ke dalam arsitektur Laravel web-cms.

**Current Status**: web-cms memiliki `EventDoorprizeController.php` (versi lama di root `plugins/events/`) yang merupakan port langsung CodeIgniter — masih menggunakan CodeIgniter model dan tidak terintegrasi dengan sistem Laravel. Perlu di-rewrite menggunakan Eloquent + Livewire. Database tables untuk doorprize belum ada di web-cms.

---

## Current State

### web-cms (Target System)

**Existing (legacy — perlu rewrite):**
- `plugins/events/EventDoorprizeController.php` — CodeIgniter-style (428 baris)
- Methods: `doorprize`, `addRound`, `editRound`, `deleteRound`, `addPrize`, `editPrize`, `deletePrize`, `setWinner`, `getDoorprizeGuests`, `getPrizes`, `setGuestPrize`, `removePrize`, `updateDoorprizeSettings`
- Menggunakan: `DoorprizeRoundModel`, `DoorprizePrizeModel`, `DoorprizeWinnerModel`, `DoorprizeSettingsModel` (model tidak ada di web-cms)
- Tidak ada Livewire component
- Tidak ada database tables

**Missing:**
- Semua database tables doorprize
- Laravel Eloquent models
- Livewire component untuk live drawing UI
- Predetermined winners feature (ERS versi terbaru)
- Ban/unban peserta dari doorprize
- Reset doorprize capability
- Eligibility filters (check-in, feedback, mission, track session)
- RBAC permission checks

### ERS (Reference System)

**Fully Implemented:**
- `Backend\EventDoorprizeController.php` — 1328 baris, full-featured
- Rounds (sesi undian) per event
- Prizes per round (max 6 per round)
- Winners per prize
- Predetermined winners: admin bisa pre-set pemenang dengan backup
- Ban guest dari doorprize (field `doorprize_ban` di registrant)
- Reset: hapus semua winners, reset status registrant
- Settings: criteria eligibilitas (check_in, feedback, mission, track_session), banner image
- Eligibility filters: `doorprize_ban = 0`, approval check, filter per criteria

---

## Gap Analysis

| Feature | web-cms | ERS | Priority |
|---------|---------|-----|----------|
| Round management (CRUD) | ❌ (legacy) | ✅ | P0 |
| Prize management per round | ❌ (legacy) | ✅ (max 6/round) | P0 |
| Winner recording | ❌ (legacy) | ✅ | P0 |
| Live drawing UI | ❌ | ✅ | P0 |
| Eligible guest list | ❌ (legacy) | ✅ | P0 |
| Eligibility filters (checkin/feedback/mission) | ❌ | ✅ | P1 |
| Ban/unban guest from doorprize | ❌ | ✅ | P1 |
| Predetermined winners | ❌ | ✅ | P2 |
| Reset doorprize | ❌ | ✅ | P1 |
| Prize image upload | ❌ (legacy) | ✅ | P1 |
| Doorprize settings (banner, criteria) | ❌ | ✅ | P1 |
| RBAC/ABAC permission | ❌ | ✅ | P0 |

---

## Feature Specification

### 8.1 Round Management

**Definisi**: Round adalah sesi undian dalam satu event. Satu event bisa memiliki multiple rounds.

**CRUD Rounds**:
- **Create**: Admin klik "Add Round" → input nama round → simpan
- **Edit**: Klik edit icon → ubah nama → simpan
- **Delete**: Klik delete → konfirmasi → hapus round beserta semua prize-nya

**Tampilan**: Accordion atau tab per round, menampilkan list prizes di bawahnya.

**API**:
```
POST   /admin/events/{event}/doorprize/rounds         — addRound
PUT    /admin/events/{event}/doorprize/rounds/{round} — editRound
DELETE /admin/events/{event}/doorprize/rounds/{round} — deleteRound
```

### 8.2 Prize Management

**Definisi**: Setiap round memiliki beberapa hadiah (prizes). Max 6 prizes per round.

**Prize Fields**:
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| prize_name | text | Yes | Nama hadiah |
| total_winner | integer | Yes | Jumlah pemenang untuk prize ini |
| prize_image | file | No | Gambar hadiah (max 500KB, jpg/png) |

**CRUD Prizes**:
- **Create**: Di dalam round, klik "Add Prize" → isi nama, slot, upload gambar opsional
- **Edit**: Klik edit → ubah detail → simpan (gambar lama diganti jika upload baru)
- **Delete**: Hapus prize + file gambar dari storage

**Validation**:
- Max 6 prizes per round
- `total_winner >= 1`
- Prize image: max 500KB, jpg/png only

### 8.3 Eligible Guest List

**Definisi**: Daftar tamu yang berhak mengikuti doorprize, berdasarkan kriteria yang dikonfigurasi admin.

**Base Criteria**:
- Status `confirmed` atau `attended` (jika event requires approval)
- `doorprize_ban = false` (tidak diblokir)

**Optional Criteria** (dikonfigurasi di doorprize settings):
| Setting | Filter |
|---------|--------|
| `require_checkin` | `check_in = true` |
| `require_feedback` | `feedback_submitted = true` |
| `require_mission` | `mission_completed = true` (jika ada) |
| `require_track_session` | `has_track_session = true` (jika ada) |

**API**:
```
GET /admin/events/{event}/doorprize/guests — getDoorprizeGuests (JSON untuk DataTables)
```

### 8.4 Live Drawing UI

**Halaman**: `/admin/events/{event_id}/doorprize`

**UI Layout**:
```
┌─────────────────────────────────────────────┐
│  [Event Name] — Doorprize Management         │
├──────────────────┬──────────────────────────┤
│  ROUNDS & PRIZES │   LIVE DRAWING           │
│  ─────────────── │   ─────────────────────  │
│  Round 1         │   Select Round:  [▼]     │
│  ├ Grand Prize   │   Select Prize:  [▼]     │
│  └ Consolation   │                          │
│  ─────────────── │   [🎲 ROLL / DRAW]       │
│  Round 2         │                          │
│  └ ...           │   Winner: John Doe       │
│                  │   PT Example Corp        │
│  [+ Add Round]   │                          │
│                  │   Previous Winners:      │
│                  │   1. Jane Smith          │
│                  │   2. Bob Johnson         │
└──────────────────┴──────────────────────────┘
```

**Drawing Flow**:
1. Admin pilih round → pilih prize
2. Klik "Roll" / "Draw"
3. Animasi rolling (tampilkan nama-nama berganti cepat)
4. Setelah beberapa detik, berhenti di satu nama (pemenang)
5. Pemenang ditampilkan besar dengan konfetti animasi
6. Admin konfirmasi pemenang atau minta roll ulang (jika predetermined)
7. Pemenang disimpan ke `doorprize_winners`
8. Status registrant: `doorprize_won = true`, `prize_id = prize.id`

**Random Selection Logic**:
```php
$eligibleGuests = EventRegistration::where('event_id', $event->id)
    ->where('doorprize_ban', false)
    ->whereNotIn('id', $alreadyWonIds)  // tidak menang sebelumnya
    ->when($settings->require_checkin, fn($q) => $q->where('check_in', true))
    ->when($settings->require_feedback, fn($q) => $q->where('feedback_submitted', true))
    ->inRandomOrder()
    ->first();
```

### 8.5 Ban/Unban Guest

**Tujuan**: Admin bisa memblokir tamu tertentu dari doorprize (misalnya: panitia, sponsor yang tidak boleh menang).

**Flow**:
- Dari guest list di halaman doorprize, admin klik "Ban" icon
- Konfirmasi → registrant di-update `doorprize_ban = true`
- Tamu dikeluarkan dari eligible list
- Admin bisa "Unban" untuk mengembalikan eligibilitas

**Endpoint**:
```
POST /admin/events/{event}/doorprize/ban   — { registration_id }
POST /admin/events/{event}/doorprize/unban — { registration_id }
```

### 8.6 Reset Doorprize

**Tujuan**: Hapus semua pemenang dan reset status doorprize tamu (untuk mengulang seluruh sesi doorprize).

**Efek Reset**:
1. Hapus semua record di `doorprize_winners` untuk event ini
2. Reset `doorprize_won = false`, `prize_id = null` untuk semua registrant event ini
3. Reset `status = 'active'` untuk predetermined winners (jika ada)

**Flow**: Admin klik "Reset" → konfirmasi modal → eksekusi dalam DB transaction

**Endpoint**: `POST /admin/events/{event}/doorprize/reset`

### 8.7 Predetermined Winners (P2)

**Tujuan**: Admin dapat menetapkan pemenang tertentu di awal untuk prize tertentu, dengan backup list.

**Struktur**:
- `doorprize_predetermined_winners` table: `prize_id`, `registration_id`, `priority_order`, `is_backup`, `status` (active/skipped/used), `notes`
- Saat drawing prize tersebut: sistem ambil predetermined winner pertama yang statusnya `active`
- Jika admin klik "Skip": tandai winner sebagai `skipped`, ambil backup berikutnya

**API**:
```
GET    /admin/events/{event}/doorprize/prizes/{prize}/predetermined         — getPredeterminedWinners
POST   /admin/events/{event}/doorprize/prizes/{prize}/predetermined         — savePredeterminedWinners
DELETE /admin/events/{event}/doorprize/prizes/{prize}/predetermined         — removePredeterminedWinners
POST   /admin/events/{event}/doorprize/predetermined/{winner}/skip          — skipPredeterminedWinner
```

### 8.8 Doorprize Settings

**Konfigurasi** disimpan di tabel `doorprize_settings` (1 row per event):

| Field | Type | Default | Description |
|-------|------|---------|-------------|
| `event_id` | bigint | - | FK ke events |
| `require_checkin` | boolean | false | Wajib check-in |
| `require_feedback` | boolean | false | Wajib isi feedback |
| `require_mission` | boolean | false | Wajib selesaikan mission |
| `require_track_session` | boolean | false | Wajib track session |
| `doorprize_background` | varchar(255) | null | Background image untuk layar undian |

---

## Database Schema Changes

### New Tables

```sql
-- Doorprize rounds per event
CREATE TABLE doorprize_rounds (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    round_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Prizes per round (max 6)
CREATE TABLE doorprize_prizes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    round_id BIGINT UNSIGNED NOT NULL,
    prize_name VARCHAR(255) NOT NULL,
    slot INT UNSIGNED NOT NULL DEFAULT 1,
    prize_image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (round_id) REFERENCES doorprize_rounds(id) ON DELETE CASCADE,
    INDEX idx_round (round_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Winners per prize
CREATE TABLE doorprize_winners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prize_id BIGINT UNSIGNED NOT NULL,
    event_registration_id BIGINT UNSIGNED NOT NULL,
    won_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prize_id) REFERENCES doorprize_prizes(id) ON DELETE CASCADE,
    FOREIGN KEY (event_registration_id) REFERENCES event_registrations(id) ON DELETE CASCADE,
    UNIQUE KEY unique_prize_registrant (prize_id, event_registration_id),
    INDEX idx_prize (prize_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Predetermined winners (P2)
CREATE TABLE doorprize_predetermined_winners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    prize_id BIGINT UNSIGNED NOT NULL,
    event_registration_id BIGINT UNSIGNED NOT NULL,
    priority_order INT UNSIGNED NOT NULL DEFAULT 1,
    is_backup BOOLEAN NOT NULL DEFAULT FALSE,
    status ENUM('active','skipped','used') NOT NULL DEFAULT 'active',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prize_id) REFERENCES doorprize_prizes(id) ON DELETE CASCADE,
    FOREIGN KEY (event_registration_id) REFERENCES event_registrations(id) ON DELETE CASCADE,
    INDEX idx_prize_order (prize_id, priority_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings per event
CREATE TABLE doorprize_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL UNIQUE,
    require_checkin BOOLEAN NOT NULL DEFAULT FALSE,
    require_feedback BOOLEAN NOT NULL DEFAULT FALSE,
    require_mission BOOLEAN NOT NULL DEFAULT FALSE,
    require_track_session BOOLEAN NOT NULL DEFAULT FALSE,
    doorprize_background VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Alter Table: `event_registrations`

```sql
-- Doorprize tracking fields
ALTER TABLE event_registrations
    ADD COLUMN doorprize_ban BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN doorprize_won BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN prize_id BIGINT UNSIGNED NULL,
    ADD INDEX idx_doorprize (event_id, doorprize_ban, check_in);
```

---

## Implementation Notes

### Models

```php
// DoorprizeRound.php
class DoorprizeRound extends Model
{
    protected $fillable = ['event_id', 'round_name'];

    public function prizes()
    {
        return $this->hasMany(DoorprizePrize::class, 'round_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}

// DoorprizePrize.php
class DoorprizePrize extends Model
{
    protected $fillable = ['round_id', 'prize_name', 'slot', 'prize_image'];

    public function round()
    {
        return $this->belongsTo(DoorprizeRound::class, 'round_id');
    }

    public function winners()
    {
        return $this->hasMany(DoorprizeWinner::class, 'prize_id');
    }

    public function getRemainingSlots(): int
    {
        return $this->slot - $this->winners()->count();
    }
}

// DoorprizeSettings.php
class DoorprizeSettings extends Model
{
    protected $fillable = [
        'event_id', 'require_checkin', 'require_feedback',
        'require_mission', 'require_track_session', 'doorprize_background',
    ];

    protected $casts = [
        'require_checkin'       => 'boolean',
        'require_feedback'      => 'boolean',
        'require_mission'       => 'boolean',
        'require_track_session' => 'boolean',
    ];
}
```

### Controller: `EventDoorprizeController` (Rewrite)

```php
namespace Plugins\Events\Http\Controllers;

class EventDoorprizeController extends Controller
{
    public function index(Event $event)
    {
        $this->authorize('viewDoorprize', $event);

        $rounds   = $event->doorprizeRounds()->with('prizes.winners')->get();
        $settings = DoorprizeSettings::firstOrNew(['event_id' => $event->id]);

        return view('events::admin.doorprize.index', compact('event', 'rounds', 'settings'));
    }

    public function getEligibleGuests(Event $event): JsonResponse
    {
        $settings = DoorprizeSettings::where('event_id', $event->id)->first();

        $query = EventRegistration::where('event_id', $event->id)
            ->where('doorprize_ban', false)
            ->whereIn('status', ['confirmed', 'attended']);

        if ($settings?->require_checkin)  $query->where('check_in', true);
        if ($settings?->require_feedback) $query->where('feedback_submitted', true);

        return response()->json(['data' => $query->get()]);
    }

    public function draw(Request $request, Event $event, DoorprizePrize $prize): JsonResponse
    {
        $this->authorize('manageDoorprize', $event);

        $alreadyWonIds = DoorprizeWinner::whereIn('prize_id',
            $event->doorprizeRounds()->with('prizes')->get()->pluck('prizes.*.id')->flatten()
        )->pluck('event_registration_id')->toArray();

        $settings = DoorprizeSettings::where('event_id', $event->id)->first();

        $winner = EventRegistration::where('event_id', $event->id)
            ->where('doorprize_ban', false)
            ->whereNotIn('id', $alreadyWonIds)
            ->whereIn('status', ['confirmed', 'attended'])
            ->when($settings?->require_checkin, fn($q) => $q->where('check_in', true))
            ->when($settings?->require_feedback, fn($q) => $q->where('feedback_submitted', true))
            ->inRandomOrder()
            ->first();

        if (!$winner) {
            return response()->json(['status' => 'error', 'message' => 'No eligible guests available.'], 400);
        }

        DoorprizeWinner::create([
            'prize_id'              => $prize->id,
            'event_registration_id' => $winner->id,
        ]);

        $winner->update(['doorprize_won' => true, 'prize_id' => $prize->id]);

        return response()->json(['status' => 'success', 'winner' => $winner]);
    }

    public function reset(Event $event): JsonResponse
    {
        $this->authorize('manageDoorprize', $event);

        DB::transaction(function () use ($event) {
            // Hapus semua winners
            DoorprizeWinner::whereIn('prize_id',
                $event->doorprizeRounds()->with('prizes')->get()->pluck('prizes.*.id')->flatten()
            )->delete();

            // Reset registrant doorprize status
            EventRegistration::where('event_id', $event->id)
                ->update(['doorprize_won' => false, 'prize_id' => null]);
        });

        return response()->json(['status' => 'success', 'message' => 'Doorprize reset successfully.']);
    }

    public function ban(Request $request, Event $event): JsonResponse
    {
        $registration = EventRegistration::where('id', $request->registration_id)
            ->where('event_id', $event->id)
            ->firstOrFail();

        $registration->update(['doorprize_ban' => true]);

        return response()->json(['status' => 'success']);
    }

    public function unban(Request $request, Event $event): JsonResponse
    {
        $registration = EventRegistration::where('id', $request->registration_id)
            ->where('event_id', $event->id)
            ->firstOrFail();

        $registration->update(['doorprize_ban' => false]);

        return response()->json(['status' => 'success']);
    }
}
```

### Livewire Component: `DoorprizeManager`

```php
namespace Plugins\Events\Livewire;

class DoorprizeManager extends Component
{
    public Event $event;
    public ?DoorprizeRound $selectedRound = null;
    public ?DoorprizePrize $selectedPrize = null;

    // Modal states
    public bool $showRoundModal  = false;
    public bool $showPrizeModal  = false;
    public bool $showWinnerModal = false;

    // Form fields
    public string $roundName  = '';
    public string $prizeName  = '';
    public int $totalWinners  = 1;
    public $prizeImage;

    // Drawing state
    public $currentWinner      = null;
    public bool $isDrawing     = false;

    public function getRoundsProperty()
    {
        return $this->event->doorprizeRounds()->with('prizes.winners')->get();
    }

    public function draw(): void
    {
        if (!$this->selectedPrize) return;
        $this->isDrawing = true;
        $this->dispatch('start-animation');
        // Actual draw logic via AJAX to controller
    }

    public function confirmWinner(): void
    {
        // Konfirmasi dan simpan pemenang
    }
}
```

### Routes

```php
Route::prefix('admin/events/{event}/doorprize')
    ->middleware(['auth', 'permission:events.doorprize.view'])
    ->name('events.admin.doorprize.')
    ->group(function () {
        Route::get('/', [EventDoorprizeController::class, 'index'])->name('index');
        Route::get('/guests', [EventDoorprizeController::class, 'getEligibleGuests'])->name('guests');
        Route::post('/reset', [EventDoorprizeController::class, 'reset'])->name('reset');
        Route::post('/ban', [EventDoorprizeController::class, 'ban'])->name('ban');
        Route::post('/unban', [EventDoorprizeController::class, 'unban'])->name('unban');
        Route::post('/settings', [EventDoorprizeController::class, 'updateSettings'])->name('settings');

        // Rounds
        Route::post('/rounds', [EventDoorprizeController::class, 'addRound'])->name('rounds.store');
        Route::put('/rounds/{round}', [EventDoorprizeController::class, 'editRound'])->name('rounds.update');
        Route::delete('/rounds/{round}', [EventDoorprizeController::class, 'deleteRound'])->name('rounds.destroy');

        // Prizes
        Route::post('/prizes', [EventDoorprizeController::class, 'addPrize'])->name('prizes.store');
        Route::put('/prizes/{prize}', [EventDoorprizeController::class, 'editPrize'])->name('prizes.update');
        Route::delete('/prizes/{prize}', [EventDoorprizeController::class, 'deletePrize'])->name('prizes.destroy');

        // Drawing
        Route::post('/prizes/{prize}/draw', [EventDoorprizeController::class, 'draw'])->name('prizes.draw');

        // P2: Predetermined
        Route::get('/prizes/{prize}/predetermined', [EventDoorprizeController::class, 'getPredetermined'])->name('predetermined.index');
        Route::post('/prizes/{prize}/predetermined', [EventDoorprizeController::class, 'savePredetermined'])->name('predetermined.store');
        Route::delete('/prizes/{prize}/predetermined', [EventDoorprizeController::class, 'removePredetermined'])->name('predetermined.destroy');
    });
```

### Permissions

```json
"events.doorprize.view":   "View doorprize management",
"events.doorprize.manage": "Manage rounds, prizes, and conduct drawing"
```

---

## Acceptance Criteria

### Phase 1: Round & Prize Management
- [ ] Admin dapat membuat, mengedit, dan menghapus round
- [ ] Admin dapat menambah prize ke round (max 6 per round)
- [ ] Admin dapat upload gambar prize (max 500KB)
- [ ] Menghapus round menghapus semua prize terkait
- [ ] Validasi max 6 prize per round diterapkan

### Phase 2: Drawing
- [ ] Halaman doorprize menampilkan rounds dan prizes
- [ ] Admin dapat memilih round dan prize untuk drawing
- [ ] Animasi rolling nama peserta saat drawing
- [ ] Pemenang dipilih secara random dari eligible guests
- [ ] Pemenang tidak dapat dipilih lagi untuk prize lain di event yang sama
- [ ] Record pemenang tersimpan di `doorprize_winners`

### Phase 3: Eligibility
- [ ] Eligible guests difilter berdasarkan `doorprize_ban = false`
- [ ] Filter `require_checkin`, `require_feedback` berfungsi sesuai settings
- [ ] Guest yang sudah menang dikecualikan dari drawing berikutnya

### Phase 4: Ban/Unban
- [ ] Admin dapat ban/unban guest dari doorprize di halaman guest list
- [ ] Banned guests tidak muncul di eligible list

### Phase 5: Reset
- [ ] Reset menghapus semua winners dan mereset status registrant
- [ ] Reset menggunakan DB transaction (atomic)
- [ ] Konfirmasi modal muncul sebelum reset

### Phase 6: Settings
- [ ] Admin dapat toggle require_checkin, require_feedback criteria
- [ ] Admin dapat upload doorprize background image
- [ ] Settings tersimpan per event (idempotent)

### Phase 7: Predetermined Winners (P2)
- [ ] Admin dapat set predetermined winners dengan backup list per prize
- [ ] Saat drawing prize tersebut, predetermined winner dipilih otomatis
- [ ] Admin dapat skip predetermined winner (ambil backup)
- [ ] Remove all predetermined winners untuk prize tertentu

### Testing
- [ ] Unit test: eligible guest filter logic
- [ ] Unit test: random draw (tidak menang dua kali)
- [ ] Feature test: CRUD rounds dan prizes
- [ ] Feature test: reset doorprize (DB transaction)
- [ ] Feature test: ban/unban guest
