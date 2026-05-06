# PRD 03: Custom Questions

## Overview

This module enables event administrators to create and manage custom questions per event, collecting specific information from registrants during the event registration process. Custom questions support multiple field types (text, textarea, single/multi-select, email, phone, date), can be marked as required, support drag-drop ordering, and allow question images for visual reference. Registrant answers are linked to their registration record for export and reporting.

**Current Status**: The ERS system has a fully functional custom questions feature. The web-cms events plugin has placeholder controller code but no working UI or database tables.

## Current State

### web-cms (Target System)

**Existing Form Builder System** (`app/Models/Form.php`, `FormField.php`):
- 26 field types: text, email, tel, textarea, number, date, select, radio, checkbox, file, name, address, url, password, hidden, time, datetime, color, range, rating, signature, image, mask, section, html, divider, gdpr, terms, nps, repeater
- Features: multi-step forms, conditional_logic JSON, spam protection, column widths, import/export
- Forms are standalone — no integration with Events plugin

**Events Plugin** (`plugins/events/`):
- `EventQuestionController.php` exists but references CodeIgniter models (CustomQuestionModel, QuestionOptionModel) that don't exist in web-cms
- `EventRegistration` model has `custom_fields` JSON column but no UI for populating it
- No database tables for custom questions or answers
- No admin UI for building custom questions per event

**Gap**: Events use a hardcoded `custom_fields` JSON column on registrations with no way for admins to define what fields exist.

### ERS (Reference System)

**Fully Implemented** (`app/Controllers\Backend\EventQuestionsController.php`):
- Per-event custom questions with full CRUD operations
- Drag-drop sortable ordering via SortableJS
- Question types: text, textarea, single_select, multi_select, email, phone, date
- Required field toggle per question
- Question image upload support
- Clone question functionality (duplicate within same event)
- Options management for select-type questions
- RBAC + ABAC permission checks (events.questions.view/create/edit/delete)
- Answer storage in `custom_answers` table linked to registrants

**Database Tables**:
```sql
-- custom_questions
id, event_id, type, question, question_description, short_label, required, order, image, created_at, updated_at

-- custom_question_options
id, question_id, option_text, created_at, updated_at

-- custom_answers
id, registrant_id, question_id, answer, created_at, updated_at
```

**View**: `app/Views/events/custom_questions.php` (32KB) — admin interface with question list, add/edit modal, drag-drop reordering

## Gap Analysis

| Feature | web-cms | ERS | Priority |
|---------|---------|-----|----------|
| Per-event question builder | ❌ | ✅ | P0 |
| Question types (7+) | Partial (26 in Form Builder) | ✅ (7 types) | P0 |
| Drag-drop ordering | ❌ | ✅ | P0 |
| Required field toggle | ❌ | ✅ | P0 |
| Question image upload | ❌ | ✅ | P1 |
| Clone question to another event | ❌ | ✅ (same event only) | P1 |
| Conditional logic | ✅ (Form Builder) | ❌ | P2 |
| Answer storage & export | ❌ | ✅ | P0 |
| Permission integration | Partial | ✅ | P1 |

## Feature Specification

### 3.1 Custom Question Builder (Admin)

**Location**: Events plugin admin UI → Event Edit → "Custom Questions" tab

**User Flow**:
1. Admin navigates to Events → Edit Event
2. Clicks "Custom Questions" tab (new tab)
3. Sees list of existing questions for this event (if any)
4. Clicks "Add Question" button to open modal
5. Fills in question details (see 3.2)
6. Saves question — appears in list
7. Can drag questions to reorder
8. Can edit or delete existing questions

**UI Components**:
- **Question List Panel**: Left column showing all questions with drag handles
  - Display: question text, type badge, short_label, required indicator
  - Actions: Edit button, Delete button, Clone button
  - Drag handle for reordering
- **Add/Edit Modal**: 
  - Question text (required, max 255 chars)
  - Question description/help text (optional, textarea)
  - Short label/code (required, max 50 chars, alphanumeric+underscore)
  - Question type (dropdown: text, textarea, single_select, multi_select, email, phone, date)
  - Required toggle (checkbox)
  - Options panel (shows for single_select/multi_select types only)
    - Dynamic option rows with add/remove buttons
    - Each option: text input, remove button
    - "Add Option" button
  - Image upload (optional) — file input with preview
- **Clone Modal** (optional P1):
  - Select target event from dropdown
  - Confirmation message

**Permissions**:
- `events.questions.view` — see questions tab
- `events.questions.create` — add new questions
- `events.questions.edit` — edit existing questions
- `events.questions.delete` — delete questions

### 3.2 Question Types

| Type | Description | Options Supported | Validation |
|------|-------------|-------------------|------------|
| `text` | Single-line text input | No | Max length configurable |
| `textarea` | Multi-line text input | No | Max length configurable |
| `single_select` | Dropdown select (radio UI alternative) | Yes (from options table) | Must select one if required |
| `multi_select` | Checkbox multi-select | Yes (from options table) | Must select at least one if required |
| `email` | Email input with validation | No | Valid email format |
| `phone` | Phone number input | No | Numeric + symbols |
| `date` | Date picker | No | Valid date format |

**Question Metadata**:
- `question`: The question text shown to registrant (max 255 chars)
- `question_description`: Help/description text (optional)
- `short_label`: Internal code for export/reporting (max 50 chars, alphanumeric+underscore)
- `required`: Boolean — whether answer is mandatory
- `order`: Integer for display ordering

### 3.3 Question Ordering

**Implementation**: SortableJS library
- Drag handle on left side of each question item
- AJAX POST to `/admin/events/{id}/questions/reorder` on drop
- Payload: Array of question IDs in new order
- Updates `order` column in database
- Re-renders list to reflect new order

**Database Schema**:
```sql
ALTER TABLE event_custom_questions ADD INDEX idx_event_order (event_id, order);
```

### 3.4 Required Field Toggle

**UI**: Checkbox in question form labeled "Required field"
**Behavior**:
- If checked: question shows asterisk (*) in public form
- Validation error on submit if empty
- Export marks required questions distinctly

**Frontend Validation**:
```javascript
// During registration form submission
requiredQuestions.forEach(q => {
  if (!answers[q.short_label]) {
    errors.push(`${q.question} is required`);
  }
});
```

### 3.5 Conditional Logic (Optional Enhancement - P2)

**Future Enhancement**: Show/hide questions based on previous answers
- Reuse Form Builder's `conditional_logic` JSON structure
- UI: "Add Condition" button in question edit
- Condition builder: "Show this question if [question] [operator] [value]"
- Operators: equals, not_equals, contains, does_not_contain

**Example**:
```json
{
  "conditional_logic": {
    "enabled": true,
    "conditions": [
      {"field": "dietary_restrictions", "operator": "equals", "value": "vegetarian"}
    ],
    "action": "show"
  }
}
```

### 3.6 Question Image Upload

**Purpose**: Attach reference images to questions (e.g., "Select your shirt size" with size chart image)

**Implementation**:
- File upload input in question form (accept: image/*)
- Stored in `storage/app/public/event-questions/images/`
- Filename: `{question_id}_{timestamp}.{ext}`
- Shown above question in public registration form
- Admin can delete/replace image

**Database**:
```sql
ALTER TABLE event_custom_questions ADD COLUMN image VARCHAR(255) NULL;
```

### 3.7 Clone Questions to Another Event

**Purpose**: Copy question set from one event to another (e.g., recurring events)

**User Flow**:
1. Admin clicks "Clone" button on question
2. Modal opens with event dropdown
3. Admin selects target event
4. Confirmation: "Copy 'X' to 'Event Y'?"
5. System creates new question with:
  - Same type, question text, description, options
  - New order (max + 1 in target event)
  - Suffix "(Copy)" added to question text
  - Short label: `{original}_copy`
  - Image: copied to new file

**API**: `POST /admin/events/questions/{id}/clone`
**Payload**: `{ target_event_id: int }`

### 3.8 Registration Form Integration

**Public Registration Flow**:
1. User clicks "Register" on event page
2. Standard registration form: name, email, phone, organization
3. After standard fields: "Additional Questions" section
4. Questions rendered in order
5. Required questions marked with asterisk
6. Help text shown below question (if provided)
7. Question images shown above question (if present)
8. Submit button validates all required fields

**Frontend Rendering** (Livewire component or Blade):
```blade
@section('custom_questions')
  @if($event->customQuestions->count() > 0)
    <div class="custom-questions-section">
      <h4>Additional Information</h4>
      @foreach($event->customQuestions->orderBy('order')->get() as $question)
        <div class="form-group mb-3" data-question-id="{{ $question->id }}">
          <label>
            {{ $question->question }}
            @if($question->required) <span class="text-danger">*</span> @endif
          </label>
          
          @if($question->image)
            <img src="{{ Storage::url($question->image) }}" class="question-image mb-2">
          @endif
          
          @if($question->question_description)
            <small class="text-muted d-block mb-2">{{ $question->question_description }}</small>
          @endif
          
          {{-- Render input based on type --}}
          @include('events.questions.inputs.' . $question->type, ['question' => $question])
        </div>
      @endforeach
    </div>
  @endif
@endsection
```

### 3.9 Answer Storage

**Database Table**: `event_custom_answers`
```sql
CREATE TABLE event_custom_answers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_registration_id BIGINT UNSIGNED NOT NULL,
  question_id BIGINT UNSIGNED NOT NULL,
  answer TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (event_registration_id) REFERENCES event_registrations(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES event_custom_questions(id) ON DELETE CASCADE,
  INDEX idx_registration_question (event_registration_id, question_id),
  INDEX idx_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Answer Formats**:
- Single value (text, email, phone, date, single_select): stored as string
- Multi-value (multi_select): stored as JSON array `["option1", "option2"]`
- File upload: stored as file path string

**Relationships**:
```php
// EventCustomQuestion model
public function answers()
{
    return $this->hasMany(EventCustomAnswer::class, 'question_id');
}

// EventRegistration model (extend existing)
public function customAnswers()
{
    return $this->hasMany(EventCustomAnswer::class, 'event_registration_id');
}

public function getAnswerForQuestion($questionShortLabel)
{
    return $this->customAnswers()
        ->whereHas('question', fn($q) => $q->where('short_label', $questionShortLabel))
        ->value('answer');
}
```

## Database Schema Changes

### New Tables

**1. event_custom_questions**
```sql
CREATE TABLE event_custom_questions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id BIGINT UNSIGNED NOT NULL,
  type ENUM('text', 'textarea', 'single_select', 'multi_select', 'email', 'phone', 'date') NOT NULL,
  question VARCHAR(255) NOT NULL,
  question_description TEXT NULL,
  short_label VARCHAR(50) NOT NULL,
  required BOOLEAN DEFAULT 0,
  `order` INT UNSIGNED NOT NULL,
  image VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  INDEX idx_event_order (event_id, `order`),
  INDEX idx_event_type (event_id, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**2. event_custom_question_options**
```sql
CREATE TABLE event_custom_question_options (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  question_id BIGINT UNSIGNED NOT NULL,
  option_text VARCHAR(255) NOT NULL,
  `order` INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (question_id) REFERENCES event_custom_questions(id) ON DELETE CASCADE,
  INDEX idx_question_order (question_id, `order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**3. event_custom_answers**
```sql
CREATE TABLE event_custom_answers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_registration_id BIGINT UNSIGNED NOT NULL,
  question_id BIGINT UNSIGNED NOT NULL,
  answer TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (event_registration_id) REFERENCES event_registrations(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES event_custom_questions(id) ON DELETE CASCADE,
  INDEX idx_registration_question (event_registration_id, question_id),
  INDEX idx_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Models to Create

**`plugins/events/src/Models/EventCustomQuestion.php`**
```php
<?php

namespace Plugins\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCustomQuestion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'type',
        'question',
        'question_description',
        'short_label',
        'required',
        'order',
        'image',
    ];

    protected $casts = [
        'required' => 'boolean',
        'order' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function options()
    {
        return $this->hasMany(EventCustomQuestionOption::class, 'question_id')->orderBy('order');
    }

    public function answers()
    {
        return $this->hasMany(EventCustomAnswer::class, 'question_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
```

**`plugins/events/src/Models/EventCustomQuestionOption.php`**
```php
<?php

namespace Plugins\Events\Models;

use Illuminate\Database\Eloquent\Model;

class EventCustomQuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'option_text',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(EventCustomQuestion::class, 'question_id');
    }
}
```

**`plugins/events/src/Models/EventCustomAnswer.php`**
```php
<?php

namespace Plugins\Events\Models;

use Illuminate\Database\Eloquent\Model;

class EventCustomAnswer extends Model
{
    protected $fillable = [
        'event_registration_id',
        'question_id',
        'answer',
    ];

    protected $casts = [
        'answer' => 'array', // For multi-select answers
    ];

    public function registration()
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    public function question()
    {
        return $this->belongsTo(EventCustomQuestion::class, 'question_id');
    }
}
```

### Extend Existing Model

**`plugins/events/src/Models/EventRegistration.php`** — add relationship:
```php
public function customAnswers()
{
    return $this->hasMany(EventCustomAnswer::class, 'event_registration_id');
}

public function getCustomAnswer($questionShortLabel)
{
    return $this->customAnswers()
        ->whereHas('question', fn($q) => $q->where('short_label', $questionShortLabel))
        ->first()?->answer;
}

public function toExportArray()
{
    $export = [
        // ... existing fields
    ];

    // Add custom question answers
    foreach ($this->registration->event->customQuestions as $question) {
        $answer = $this->getCustomAnswer($question->short_label);
        $export[$question->short_label] = is_array($answer) ? implode(', ', $answer) : $answer;
    }

    return $export;
}
```

### Extend Existing Model

**`plugins/events/src/Models/Event.php`** — add relationship:
```php
public function customQuestions()
{
    return $this->hasMany(EventCustomQuestion::class, 'event_id')->ordered();
}
```

## Implementation Notes

### Technical Approach: **New Event-Specific System** (Recommended)

**Decision**: Create new event-specific tables rather than extend Form Builder

**Rationale**:
1. **Simpler Integration**: Event registrations have specific needs (per-event questions, registrant linkage) that differ from generic forms
2. **Clearer Data Model**: Direct relationship between events → questions → answers mirrors ERS proven pattern
3. **Easier Migration**: Can migrate ERS data structure directly without complex transformations
4. **Performance**: Simpler queries without form abstraction layer
5. **Permissions**: Event-specific permissions already exist; form permissions would need extension

**Alternative Rejected**: Extending Form Builder would require:
- Creating "event_form" relationship
- Mapping FormField types to event question types
- Handling conditional logic complexity not needed for events
- More complex queries (forms → fields → entries → event linkage)

### Implementation Order

**Phase 1: Core CRUD (P0)**
1. Create database tables via migration
2. Create models with relationships
3. Build admin UI (question list, add/edit modal)
4. Implement drag-drop ordering
5. Test question CRUD operations

**Phase 2: Registration Integration (P0)**
1. Modify registration form to include custom questions section
2. Implement frontend validation
3. Save answers on registration submission
4. Update registration export to include answers

**Phase 3: Enhanced Features (P1)**
1. Question image upload
2. Clone question to another event
3. Bulk delete questions
4. Question reordering improvements

**Phase 4: Advanced Features (P2)**
1. Conditional logic
2. Question templates
3. Import/export question sets

### Admin UI Components

**Livewire Component**: `QuestionsManager.php`
```php
<?php

namespace Plugins\Events\Livewire;

use Livewire\WithFileUploads;
use Plugins\Events\Models\Event;
use Plugins\Events\Models\EventCustomQuestion;
use Plugins\Events\Models\EventCustomQuestionOption;

class QuestionsManager extends Component
{
    use WithFileUploads;

    public $event;
    public $questions;
    public $editingQuestion = null;
    public $showModal = false;

    // Form fields
    public $question_text;
    public $question_description;
    public $short_label;
    public $type;
    public $required = false;
    public $options = [];
    public $image;

    protected $rules = [
        'question_text' => 'required|max:255',
        'short_label' => 'required|max:50|alpha_dash',
        'type' => 'required|in:text,textarea,single_select,multi_select,email,phone,date',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->loadQuestions();
    }

    public function loadQuestions()
    {
        $this->questions = $this->event->customQuestions()->with('options')->ordered()->get();
    }

    public function saveQuestion()
    {
        $this->validate();

        $question = EventCustomQuestion::updateOrCreate(
            ['id' => $this->editingQuestion?->id],
            [
                'event_id' => $this->event->id,
                'type' => $this->type,
                'question' => $this->question_text,
                'question_description' => $this->question_description,
                'short_label' => $this->short_label,
                'required' => $this->required,
                'order' => $this->editingQuestion ? $this->editingQuestion->order : $this->getNextOrder(),
            ]
        );

        // Handle image upload
        if ($this->image) {
            $path = $this->image->store('event-questions/images', 'public');
            $question->image = $path;
            $question->save();
        }

        // Handle options for select types
        if (in_array($this->type, ['single_select', 'multi_select'])) {
            $question->options()->delete();
            foreach ($this->options as $index => $optionText) {
                if (!empty($optionText)) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'order' => $index,
                    ]);
                }
            }
        }

        $this->resetForm();
        $this->loadQuestions();
    }

    public function editQuestion($questionId)
    {
        $question = EventCustomQuestion::with('options')->find($questionId);
        $this->editingQuestion = $question;
        $this->question_text = $question->question;
        $this->question_description = $question->question_description;
        $this->short_label = $question->short_label;
        $this->type = $question->type;
        $this->required = $question->required;
        $this->options = $question->options->pluck('option_text')->toArray();
        $this->showModal = true;
    }

    public function deleteQuestion($questionId)
    {
        EventCustomQuestion::find($questionId)?->delete();
        $this->loadQuestions();
    }

    public function updateOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            EventCustomQuestion::where('id', $id)->update(['order' => $index]);
        }
        $this->loadQuestions();
    }

    private function getNextOrder()
    {
        return $this->event->customQuestions()->max('order') + 1;
    }

    private function resetForm()
    {
        $this->editingQuestion = null;
        $this->question_text = '';
        $this->question_description = '';
        $this->short_label = '';
        $this->type = 'text';
        $this->required = false;
        $this->options = [];
        $this->image = null;
        $this->showModal = false;
    }

    public function render()
    {
        return view('events::livewire.questions-manager');
    }
}
```

### Frontend Registration Integration

**Controller Method**: In registration controller, add:
```php
protected function validateCustomQuestions(Event $event, array $data)
{
    $errors = [];

    foreach ($event->customQuestions as $question) {
        $answer = $data['custom_questions'][$question->short_label] ?? null;

        if ($question->required && empty($answer)) {
            $errors["custom_questions.{$question->short_label}"] = 
                "{$question->question} is required.";
        }

        // Type-specific validation
        if (!empty($answer)) {
            switch ($question->type) {
                case 'email':
                    if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                        $errors["custom_questions.{$question->short_label}"] = 
                            "Please enter a valid email address.";
                    }
                    break;
                case 'phone':
                    if (!preg_match('/^[0-9\-\+\(\)\s]+$/', $answer)) {
                        $errors["custom_questions.{$question->short_label}"] = 
                            "Please enter a valid phone number.";
                    }
                    break;
            }
        }
    }

    return $errors;
}

protected function saveCustomAnswers(EventRegistration $registration, array $data)
{
    if (!isset($data['custom_questions'])) {
        return;
    }

    foreach ($data['custom_questions'] as $shortLabel => $answer) {
        $question = $registration->event->customQuestions()
            ->where('short_label', $shortLabel)
            ->first();

        if ($question) {
            EventCustomAnswer::updateOrCreate(
                [
                    'event_registration_id' => $registration->id,
                    'question_id' => $question->id,
                ],
                ['answer' => $answer]
            );
        }
    }
}
```

### Security Considerations

1. **XSS Prevention**: Escape all question text and options in output
2. **SQL Injection**: Use Eloquent ORM (parameterized queries)
3. **File Upload**: Validate image types, sanitize filenames, store outside webroot
4. **Authorization**: Check permissions on all CRUD operations
5. **CSRF**: Include CSRF tokens on all forms
6. **Rate Limiting**: Apply to question creation endpoints

### Performance Optimization

1. **Database Indexes**: As specified in schema
2. **Eager Loading**: Load questions with options in one query
3. **Caching**: Cache questions for event display (invalidate on update)
4. **Lazy Loading**: Load answers only when needed (export, detail view)

## Acceptance Criteria

- [ ] Admin can view Custom Questions tab on event edit page
- [ ] Admin can add questions with all 7 types (text, textarea, single_select, multi_select, email, phone, date)
- [ ] Admin can mark questions as required
- [ ] Admin can add/edit/remove options for select-type questions
- [ ] Admin can reorder questions via drag-drop
- [ ] Admin can edit existing questions
- [ ] Admin can delete questions (cascade deletes answers)
- [ ] Admin can upload question images (optional)
- [ ] Public registration form shows custom questions in order
- [ ] Required questions are validated on submission
- [ ] Answers are saved to database and linked to registration
- [ ] Registration export includes custom question answers
- [ ] Questions are filtered by event (no cross-event contamination)
- [ ] Permissions are properly enforced (view/create/edit/delete)
- [ ] UI is responsive and accessible
- [ ] Database migrations are reversible
- [ ] No data loss on question updates (preserve existing answers)
- [ ] Clone question works (copies to target event)
- [ ] Multi-select answers stored as JSON array

## Migration Notes (from ERS to web-cms)

**Data Migration Script** (if needed):
```sql
-- Map ERS custom_questions to web-cms event_custom_questions
INSERT INTO web_cms.event_custom_questions 
(id, event_id, type, question, question_description, short_label, required, `order`, image, created_at, updated_at)
SELECT 
    id,
    event_id,
    type,
    question,
    question_description,
    short_label,
    required,
    `order`,
    image,
    created_at,
    updated_at
FROM ers.custom_questions;

-- Map options
INSERT INTO web_cms.event_custom_question_options 
(id, question_id, option_text, `order`, created_at, updated_at)
SELECT 
    id,
    question_id,
    option_text,
    0 as `order`,
    created_at,
    updated_at
FROM ers.custom_question_options;

-- Map answers (requires registrant mapping table)
INSERT INTO web_cms.event_custom_answers 
(event_registration_id, question_id, answer, created_at, updated_at)
SELECT 
    rm.new_registration_id,
    question_id,
    answer,
    ca.created_at,
    ca.updated_at
FROM ers.custom_answers ca
JOIN registrant_mapping rm ON rm.old_registrant_id = ca.registrant_id;
```

**Note**: Registrant mapping table must be created first to link old ERS registrant IDs to new web-cms event_registration IDs.
