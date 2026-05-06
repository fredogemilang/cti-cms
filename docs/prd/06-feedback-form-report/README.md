# PRD 06: Feedback Form & Report

## Overview

Modul ini memungkinkan admin membangun form feedback per event, menampilkan form tersebut kepada tamu setelah event, mengumpulkan respons, dan menyajikan laporan agregat berupa chart dan export data. Diadaptasi dari ERS `EventFeedbackController` ke dalam arsitektur Laravel web-cms.

**Current Status**: web-cms memiliki `EventFeedbackController.php` (versi lama di root `plugins/events/`) yang merupakan port langsung dari CodeIgniter ERS — masih menggunakan model CodeIgniter (`EventsModel`, `FeedbackQuestionModel`) dan tidak terintegrasi dengan sistem Laravel. Perlu di-rewrite sepenuhnya menggunakan Eloquent + Livewire.

---

## Current State

### web-cms (Target System)

**Existing (legacy — perlu rewrite):**
- `plugins/events/EventFeedbackController.php` — CodeIgniter-style, menggunakan `EventsModel`, `FeedbackQuestionModel`, `FeedbackOptionModel` (model tidak ada di web-cms)
- Methods: `edit_feedback`, `updateFeedbackOrder`, `saveFeedbackQuestion`, `deleteFeedback`, `getQuestionOptions`, `updateFeedbackSettings`
- Tidak ada Livewire component
- Tidak ada database tables (`feedback_questions`, `feedback_options`, `feedback_registrant`)
- Tamu tidak memiliki cara untuk mengisi feedback di frontend

**Missing:**
- Database tables untuk feedback question builder
- Livewire component untuk admin builder
- Public feedback form untuk tamu (Guest App)
- Report page dengan chart agregat
- Conditional logic antar pertanyaan
- Export data feedback ke CSV
- Feedback redirect URL per event
- Feedback background/foreground image settings
- `feedback_require_checkin` toggle

### ERS (Reference System)

**Fully Implemented:**
- `Backend\EventFeedbackController.php` — 1043 baris, full-featured
- Question types: `text`, `textarea`, `single_select`, `multi_select`, `rating`, `digits`, `date`
- Multi-step form (steps yang bisa ditambah/hapus, max 10 step)
- Conditional logic: `is_conditional`, `parent_question_id`, `condition_operator`, `condition_value`
- Leads flag: opsi single_select bisa ditandai sebagai "leads"
- Feedback settings: background/foreground image, primary_color, redirect URL, require check-in
- Report: chart agregat per pertanyaan (bar chart untuk select, rating average, text preview)
- Database: `feedback_questions`, `feedback_options`, `feedback_registrant`

---

## Gap Analysis

| Feature | web-cms | ERS | Priority |
|---------|---------|-----|----------|
| Feedback question builder (admin) | ❌ (legacy code) | ✅ | P0 |
| Question types (7+) | ❌ | ✅ | P0 |
| Multi-step form (configurable steps) | ❌ | ✅ (max 10 step) | P1 |
| Drag-drop ordering | ❌ | ✅ | P0 |
| Required field toggle | ❌ | ✅ | P0 |
| Conditional logic | ❌ | ✅ | P1 |
| Leads flag (single select options) | ❌ | ✅ | P1 |
| Public feedback form (Guest App) | ❌ | ✅ | P0 |
| Feedback response storage | ❌ | ✅ | P0 |
| Report + chart agregat | ❌ | ✅ | P0 |
| Export CSV feedback | ❌ | ✅ | P1 |
| Feedback settings (BG image, color, URL) | ❌ | ✅ | P1 |
| feedback_require_checkin toggle | ❌ | ✅ | P1 |
| Clone question | ❌ | ✅ | P2 |

---

## Feature Specification

### 6.1 Feedback Question Builder (Admin)

**Lokasi**: Admin → Events → [Event] → Feedback tab

**User Flow**:
1. Admin navigasi ke event → tab "Feedback"
2. Melihat list pertanyaan yang sudah ada (per step)
3. Klik "Add Question" → modal terbuka
4. Isi detail pertanyaan → Save → muncul di list
5. Drag untuk reorder pertanyaan dalam step
6. Klik "+" untuk tambah step baru (max 10), atau "-" untuk hapus step kosong terakhir

**UI Components**:
- **Step Tabs**: Tab per step dengan badge jumlah pertanyaan
- **Question List** per step: drag handle, question text, type badge, required indicator, actions (edit/delete/clone)
- **Add/Edit Question Modal**: form isian (lihat 6.2)
- **Settings Panel**: background image, foreground image, primary color, redirect URL, require check-in toggle

### 6.2 Question Types & Fields

| Type | UI Element | Opsi | Notes |
|------|-----------|------|-------|
| `text` | Input text satu baris | Tidak | |
| `textarea` | Textarea multi-baris | Tidak | |
| `single_select` | Radio buttons / dropdown | Ya | Bisa tandai opsi sebagai "leads" |
| `multi_select` | Checkboxes | Ya | |
| `rating` | Star / angka rating | Tidak | Konfigurasi min-max (default 1-5) |
| `digits` | Input number | Tidak | |
| `date` | Date picker | Tidak | |

**Form Fields per Question**:
| Field | Type | Required | Notes |
|-------|------|----------|-------|
| question | text | Yes | Teks pertanyaan (max 255) |
| short_label | text | Yes | Internal code (alphanumeric, underscore) |
| type | select | Yes | Dari 7 tipe di atas |
| step | number | Yes | Step ke berapa (1-based) |
| is_required | boolean | No | Default: false |
| rating_min_value | number | Conditional | Hanya jika type = rating |
| rating_max_value | number | Conditional | Hanya jika type = rating |
| options | array | Conditional | Hanya jika type = single/multi_select |
| is_conditional | boolean | No | Aktifkan conditional logic |
| parent_question_id | select | Conditional | Pertanyaan induk |
| condition_operator | select | Conditional | equals / not_equals / contains |
| condition_value | text/select | Conditional | Nilai kondisi |

### 6.3 Conditional Logic

**Tujuan**: Sembunyikan/tampilkan pertanyaan berdasarkan jawaban pertanyaan lain.

**Konfigurasi**:
- `is_conditional = true` → aktifkan conditional logic
- `parent_question_id` → ID pertanyaan yang dijadikan kondisi
- `condition_operator` → `equals`, `not_equals`, `contains`, `not_contains`
- `condition_value` → nilai yang dibandingkan (untuk multi_select: JSON array)

**Frontend behavior**:
```javascript
// Pada perubahan jawaban, evaluasi semua conditional questions
function evaluateConditions(answers, questions) {
    questions.forEach(q => {
        if (!q.is_conditional) return;
        const parentAnswer = answers[q.parent_question_id];
        const show = evaluateCondition(parentAnswer, q.condition_operator, q.condition_value);
        q.visible = show;
    });
}
```

**Batasan**:
- Rating dan digit types tidak bisa dijadikan parent question
- Hanya 1 level kondisi (tidak nested)
- Deleting parent question membutuhkan konfirmasi (ada dependent questions)

### 6.4 Multi-Step Feedback Form

**Konfigurasi**: Admin bisa tambah step (hingga 10) atau hapus step terakhir yang kosong.

**Opsi**: Step count disimpan di tabel `events` kolom `feedback_step_count` (integer, default 2).

**Frontend Guest App**:
- Tampilkan pertanyaan per step
- Progress bar / step indicator
- Tombol "Next" dan "Back" antar step
- Validasi required fields sebelum pindah step
- Submit di step terakhir

### 6.5 Public Feedback Form (Guest / Frontend)

**URL**: `/feedback/{event_slug}` atau `?email={email}` sebagai pre-fill

**Alur Tamu**:
1. Tamu membuka link feedback (dari email / QR)
2. Jika `feedback_require_checkin = true`: sistem cek apakah tamu sudah check-in → jika belum, tampilkan pesan
3. Form tampil dengan design custom (background image, foreground image, primary color)
4. Tamu isi form step by step
5. Submit → data disimpan ke `event_feedback_responses`
6. Redirect ke `feedback_redirect_url` (atau halaman terima kasih default)

**Pre-fill email**: Jika URL mengandung `?email=...`, field email di-pre-fill dan tamu langsung dianggap dikenali.

### 6.6 Feedback Settings per Event

Konfigurasi disimpan di tabel `events`:

| Field | Type | Default | Description |
|-------|------|---------|-------------|
| `feedback_background` | varchar(255) | null | Path gambar background |
| `feedback_foreground` | varchar(255) | null | Path gambar foreground/logo |
| `feedback_primary_color` | varchar(7) | '#007bff' | Hex color utama form |
| `feedback_redirect_url` | varchar(500) | null | URL setelah submit |
| `feedback_require_checkin` | boolean | false | Wajib check-in sebelum feedback |
| `feedback_step_count` | tinyint | 2 | Jumlah step form |

**Upload constraints**:
- Background: max 500KB, max width 740px
- Foreground: max 500KB, max width 500px

### 6.7 Feedback Report (Admin)

**URL**: `/admin/events/{event_id}/feedback/report`

**Konten**:
- **Overview Card**: Total respons, rata-rata waktu pengisian (jika tersedia)
- **Per-question Cards**:
  - `single_select` / `multi_select`: Bar chart (horizontal) + persentase per opsi
  - `rating`: Rata-rata bintang + distribusi nilai per rating
  - `text` / `digits` / `date`: Tampilkan 5 respons terbaru
- **Data Table**: Tabel semua respons dengan kolom per pertanyaan
- **Export Button**: Download CSV semua respons

**Chart Library**: Chart.js (sudah ada di web-cms)

---

## Database Schema Changes

### New Tables

```sql
-- Feedback questions per event
CREATE TABLE event_feedback_questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    step TINYINT UNSIGNED NOT NULL DEFAULT 1,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    question VARCHAR(255) NOT NULL,
    short_label VARCHAR(50) NOT NULL,
    type ENUM('text','textarea','single_select','multi_select','rating','digits','date') NOT NULL,
    is_required BOOLEAN NOT NULL DEFAULT FALSE,
    rating_min_value TINYINT UNSIGNED NULL DEFAULT 1,
    rating_max_value TINYINT UNSIGNED NULL DEFAULT 5,
    is_conditional BOOLEAN NOT NULL DEFAULT FALSE,
    parent_question_id BIGINT UNSIGNED NULL,
    condition_operator ENUM('equals','not_equals','contains','not_contains') NULL,
    condition_value TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_question_id) REFERENCES event_feedback_questions(id) ON DELETE SET NULL,
    INDEX idx_event_step_order (event_id, step, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Options untuk select-type questions
CREATE TABLE event_feedback_options (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id BIGINT UNSIGNED NOT NULL,
    option_label VARCHAR(255) NOT NULL,
    is_leads_flag BOOLEAN NOT NULL DEFAULT FALSE,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES event_feedback_questions(id) ON DELETE CASCADE,
    INDEX idx_question_order (question_id, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Respons feedback dari tamu
CREATE TABLE event_feedback_responses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    event_registration_id BIGINT UNSIGNED NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    answer TEXT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (event_registration_id) REFERENCES event_registrations(id) ON DELETE SET NULL,
    FOREIGN KEY (question_id) REFERENCES event_feedback_questions(id) ON DELETE CASCADE,
    INDEX idx_event_registration (event_id, event_registration_id),
    INDEX idx_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Alter Table: `events`

```sql
ALTER TABLE events
    ADD COLUMN feedback_background VARCHAR(255) NULL,
    ADD COLUMN feedback_foreground VARCHAR(255) NULL,
    ADD COLUMN feedback_primary_color VARCHAR(7) DEFAULT '#007bff',
    ADD COLUMN feedback_redirect_url VARCHAR(500) NULL,
    ADD COLUMN feedback_require_checkin BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN feedback_step_count TINYINT UNSIGNED NOT NULL DEFAULT 2;
```

### Alter Table: `event_registrations`

```sql
-- Track apakah registrant sudah mengisi feedback
ALTER TABLE event_registrations
    ADD COLUMN feedback_submitted BOOLEAN NOT NULL DEFAULT FALSE AFTER check_in,
    ADD COLUMN feedback_submitted_at TIMESTAMP NULL AFTER feedback_submitted;
```

---

## Implementation Notes

### Models to Create

**`EventFeedbackQuestion.php`**:
```php
namespace Plugins\Events\Models;

class EventFeedbackQuestion extends Model
{
    protected $fillable = [
        'event_id', 'step', 'sort_order', 'question', 'short_label', 'type',
        'is_required', 'rating_min_value', 'rating_max_value',
        'is_conditional', 'parent_question_id', 'condition_operator', 'condition_value',
    ];

    protected $casts = [
        'is_required'    => 'boolean',
        'is_conditional' => 'boolean',
        'condition_value' => 'array', // JSON decode untuk multi-value conditions
    ];

    public function options()
    {
        return $this->hasMany(EventFeedbackOption::class, 'question_id')->orderBy('sort_order');
    }

    public function responses()
    {
        return $this->hasMany(EventFeedbackResponse::class, 'question_id');
    }

    public function parentQuestion()
    {
        return $this->belongsTo(self::class, 'parent_question_id');
    }
}
```

**`EventFeedbackResponse.php`**:
```php
namespace Plugins\Events\Models;

class EventFeedbackResponse extends Model
{
    protected $fillable = [
        'event_id', 'event_registration_id', 'question_id', 'answer',
    ];

    public function question()
    {
        return $this->belongsTo(EventFeedbackQuestion::class, 'question_id');
    }

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }
}
```

### Livewire Components

**`FeedbackBuilder`** (admin):
```php
namespace Plugins\Events\Livewire;

class FeedbackBuilder extends Component
{
    public Event $event;
    public int $activeStep = 1;
    public bool $showModal = false;

    // Form fields
    public ?int $editingQuestionId = null;
    public string $questionText    = '';
    public string $shortLabel      = '';
    public string $type            = 'text';
    public int $step               = 1;
    public bool $isRequired        = false;
    public array $options          = [];
    public array $leadsFlags       = [];
    // ... rating, conditional logic fields

    public function getQuestionsForStepProperty()
    {
        return $this->event->feedbackQuestions()
            ->where('step', $this->activeStep)
            ->orderBy('sort_order')
            ->with('options')
            ->get();
    }

    public function saveQuestion(): void { /* ... */ }
    public function deleteQuestion(int $id): void { /* ... */ }
    public function updateOrder(array $orderedIds): void { /* ... */ }
    public function addStep(): void { /* ... */ }
    public function removeStep(int $stepNumber): void { /* ... */ }
}
```

**`FeedbackForm`** (public / guest):
```php
namespace Plugins\Events\Livewire;

class FeedbackForm extends Component
{
    public Event $event;
    public EventRegistration $registration;
    public int $currentStep = 1;
    public array $answers   = [];

    public function getQuestionsForCurrentStepProperty()
    {
        return $this->event->feedbackQuestions()
            ->where('step', $this->currentStep)
            ->orderBy('sort_order')
            ->with('options')
            ->get()
            ->filter(fn($q) => $this->isQuestionVisible($q));
    }

    protected function isQuestionVisible(EventFeedbackQuestion $q): bool
    {
        if (!$q->is_conditional) return true;
        $parentAnswer = $this->answers[$q->parent_question_id] ?? null;
        return match($q->condition_operator) {
            'equals'      => $parentAnswer == $q->condition_value,
            'not_equals'  => $parentAnswer != $q->condition_value,
            'contains'    => str_contains((string)$parentAnswer, $q->condition_value),
            'not_contains'=> !str_contains((string)$parentAnswer, $q->condition_value),
            default       => true,
        };
    }

    public function nextStep(): void
    {
        $this->validateStep();
        if ($this->currentStep < $this->event->feedback_step_count) {
            $this->currentStep++;
        }
    }

    public function submit(): void
    {
        $this->validateStep();
        $this->saveResponses();
        $this->registration->update([
            'feedback_submitted'    => true,
            'feedback_submitted_at' => now(),
        ]);
        $this->redirect($this->event->feedback_redirect_url ?? route('events.feedback.thankyou'));
    }

    protected function saveResponses(): void
    {
        foreach ($this->answers as $questionId => $answer) {
            EventFeedbackResponse::updateOrCreate(
                [
                    'event_registration_id' => $this->registration->id,
                    'question_id'           => $questionId,
                ],
                [
                    'event_id' => $this->event->id,
                    'answer'   => is_array($answer) ? implode(',', $answer) : $answer,
                ]
            );
        }
    }
}
```

### Routes

```php
// Public
Route::get('feedback/{event_slug}', [FeedbackController::class, 'show'])->name('events.feedback.show');
Route::get('feedback/{event_slug}/thankyou', [FeedbackController::class, 'thankYou'])->name('events.feedback.thankyou');

// Admin
Route::prefix('admin/events/{event}/feedback')->middleware(['auth'])->group(function () {
    Route::get('/',          [EventFeedbackAdminController::class, 'index'])->name('events.admin.feedback.index');
    Route::get('/report',    [EventFeedbackAdminController::class, 'report'])->name('events.admin.feedback.report');
    Route::get('/export',    [EventFeedbackAdminController::class, 'export'])->name('events.admin.feedback.export');
    Route::post('/settings', [EventFeedbackAdminController::class, 'updateSettings'])->name('events.admin.feedback.settings');
});
```

### Permissions

```json
"events.feedback.view":   "View feedback questions and report",
"events.feedback.edit":   "Build feedback form (add/edit/delete questions)",
"events.feedback.export": "Export feedback responses"
```

---

## Acceptance Criteria

### Phase 1: Question Builder
- [ ] Admin dapat membuka tab "Feedback" di edit event
- [ ] Admin dapat menambah pertanyaan dengan semua 7 tipe
- [ ] Pertanyaan ditampilkan per step dengan drag-drop ordering
- [ ] Admin dapat mengedit dan menghapus pertanyaan
- [ ] Menghapus pertanyaan parent yang memiliki dependent questions meminta konfirmasi
- [ ] Options dapat ditambah/edit/hapus untuk tipe select
- [ ] `is_required` toggle berfungsi

### Phase 2: Multi-Step
- [ ] Admin dapat menambah step baru (hingga 10)
- [ ] Admin dapat menghapus step terakhir jika kosong
- [ ] Pertanyaan dapat dipindahkan antar step (via edit, ubah field step)

### Phase 3: Conditional Logic
- [ ] Admin dapat mengaktifkan conditional logic pada pertanyaan
- [ ] Pilih parent question, operator, dan condition value
- [ ] Public form menyembunyikan/menampilkan pertanyaan berdasarkan kondisi secara real-time

### Phase 4: Public Feedback Form
- [ ] URL `/feedback/{slug}` menampilkan form dengan desain custom (warna, gambar)
- [ ] Jika `feedback_require_checkin = true` dan belum check-in: tampilkan pesan larangan
- [ ] Multi-step navigation dengan validasi required fields per step
- [ ] Submit berhasil menyimpan semua jawaban ke database
- [ ] `feedback_submitted` di `event_registrations` ter-update

### Phase 5: Report
- [ ] Report page menampilkan chart per pertanyaan
- [ ] Bar chart untuk single/multi select
- [ ] Rating average + distribusi untuk rating type
- [ ] Preview 5 respons terbaru untuk text/digits/date
- [ ] Export CSV berisi semua respons

### Phase 6: Settings
- [ ] Admin dapat upload background dan foreground image
- [ ] Admin dapat set primary color dan redirect URL
- [ ] `feedback_require_checkin` toggle berfungsi

### Testing
- [ ] Unit test: conditional logic evaluation
- [ ] Feature test: submit feedback form (dengan dan tanpa kondisi)
- [ ] Feature test: require check-in enforcement
- [ ] Feature test: report chart data calculation
