# PRD 02: Registration Form

## Overview

This PRD documents the adaptation of ERS (Event Registration System) registration form features into the web-cms Events plugin. The registration form enables public users to register for events through a public-facing form with validation, capacity checks, and confirmation workflows.

## Current State

### web-cms (Target System)

**Existing Implementation:**
- **Controller**: `EventRegistrationController::register()` in `plugins/events/src/Http/Controllers/EventRegistrationController.php`
- **Model**: `EventRegistration` in `plugins/events/src/Models/EventRegistration.php`
- **Table**: `event_registrations` with fields:
  - id, event_id, user_id (nullable), name, email, phone, organization, notes
  - status (pending/confirmed/cancelled/attended)
  - confirmed_at, cancelled_at, custom_fields (JSON), ip_address, user_agent
  - timestamps, soft_deletes

**Current Features:**
- Basic registration fields: name, email, phone, organization, notes
- Custom fields stored as JSON: job_level, domicile, job_title, linkedin, institution, highest_education, industry
- Duplicate detection (email + event_id combination)
- Approval workflow: status = 'pending' if `registration_requires_approval` is true, else 'confirmed'
- Basic validation: required fields, email format, max lengths

**Missing Features (compared to ERS):**
- No UUID/QR code generation
- No corporate email validation
- No capacity enforcement before submission
- No referral/tracking code support
- No consent checkbox requirement
- No registration period validation
- No walk-in registration support

### ERS (Reference System)

**Implementation:**
- **Controller**: `Event.php::create()` in `app/Controllers/Event.php`
- **Service**: `RegistrationService` in `app/Services/RegistrationService.php`
- **Validation**: `ValidationService` in `app/Services/ValidationService.php`
- **Table**: `registrant` with comprehensive fields including UUID, QR code, referral tracking, and verification fields

**Key Features:**
- Corporate email validation (blocks free email domains: gmail.com, yahoo.com, hotmail.com, etc.)
- UUID-based QR code generation for each registrant
- Capacity enforcement (checks quota before confirming)
- Referral tracking (reff_no, reff_by fields)
- Registration period validation with timezone support
- Walk-in registration support
- Consent checkbox requirement
- UTM parameter tracking (utm_source, utm_campaign, utm_medium)
- Track session registration
- Event status validation

## Gap Analysis

| Feature | web-cms | ERS | Priority |
|---------|---------|-----|----------|
| Corporate Email Validation | ❌ None | ✅ Blocks free domains (DB + fallback) | High |
| Duplicate Registration Detection | ✅ Email + event_id | ✅ Email + event_id | ✅ Complete |
| Capacity Enforcement | ⚠️ After submission | ✅ Before submission | High |
| UUID/QR Code Generation | ❌ None | ✅ UUID stored, QR image generated | Medium |
| Referral Tracking | ❌ None | ✅ reff_no, reff_by, tracking codes | Medium |
| Consent Checkbox | ❌ None | ✅ Required consent field | High |
| Registration Period Validation | ⚠️ Basic deadline | ✅ Start/end date with timezone | Medium |
| Walk-in Registration | ❌ None | ✅ walk_in flag with auto check-in | Low |
| UTM Parameter Tracking | ❌ None | ✅ Captured in source field | Low |
| Phone Number Validation | ⚠️ Basic string | ✅ Format validation (Indonesian standard) | Medium |
| Salutation Field | ❌ None | ✅ Mr/Ms/Ms dropdown | Low |
| Company Type Detection | ❌ None | ✅ Auto-detects PT/CV/etc. from name | Low |

## Feature Specification

### 2.1 Standard Registration Fields

| Field Name | Label | Type | Validation Rules | Required? | Notes |
|------------|-------|------|------------------|-----------|-------|
| salutation | Salutation | Select (dropdown) | in:Mr,Ms,Mrs | No | Options: Mr, Ms, Mrs |
| full_name | Full Name | Text | required, max:255 | Yes | Person's full name |
| company_name | Company | Text | required, max:255 | Yes | Company/organization name |
| company_type | Company Type | Text | max:50 | No | Auto-detected from company_name |
| job_title | Job Title | Text | required, max:255 | Yes | Current job position |
| contact_level | Level | Select | required, integer | Yes | Dropdown from database |
| contact_divisi_id | Division | Select | required, integer | Yes | Dropdown from database |
| contact_divisi_name | Division (Other) | Text | max:255 | Conditional | Required if contact_divisi_id = 5 (Others) |
| country_code | Country Code | Text | max:10 | No | Phone country code (e.g., +62) |
| mobile_phone | Phone Number | Text | required, phone_format | Yes | Formatted Indonesian phone |
| email | Email Address | Email | required, email, corporate_email* | Yes | May require corporate domain |
| consentCheckbox | Consent | Checkbox | required, accepted | Yes | Required data processing consent |

*corporate_email validation applies only when event requires corporate email

### 2.2 Corporate Email Validation

**Description**: Custom validation rule to enforce corporate (non-disposable) email addresses when event requires it.

**Implementation**:
1. Check event's `requires_corporate_email` flag (new field on events table)
2. Extract domain from email address
3. Check against `free_email_domains` database table
4. Fallback to hardcoded list if database unavailable:
   - gmail.com, yahoo.com, hotmail.com, outlook.com, aol.com
   - icloud.com, live.com, msn.com, ymail.com, rocketmail.com
   - mail.com, gmx.com, protonmail.com, tutanota.com, zoho.com
   - inbox.com, rediffmail.com, mailinator.com, tempmail.org
   - 10minutemail.com, guerrillamail.com

**Validation Message**:
> "Corporate email required. Free email providers (gmail.com, yahoo.com, etc.) are not allowed for this event."

**Database Migration Required**:
```sql
-- Add to events table
ALTER TABLE events ADD COLUMN requires_corporate_email BOOLEAN DEFAULT FALSE AFTER registration_requires_approval;

-- Create free_email_domains table
CREATE TABLE free_email_domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(100) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2.3 Duplicate Registration Detection

**Description**: Block duplicate email registrations for the same event.

**Implementation**:
- Check `event_registrations` table for existing registration with:
  - Matching `event_id`
  - Matching `email`
  - Status in ['pending', 'confirmed']

**Validation Message**:
> "This email is already registered for this event."

**Note**: Already implemented in web-cms. No changes needed.

### 2.4 Capacity Enforcement

**Description**: Check registered count vs. max_participants before confirming registration.

**Implementation Logic**:
1. Check if event has `max_participants` set (non-zero)
2. Count existing registrations:
   - If `registration_requires_approval` = false: count all confirmed registrations
   - If `registration_requires_approval` = true: count all registrations (pending + confirmed)
3. If count >= max_participants: reject with error

**Validation Message**:
> "This event has reached its maximum capacity and is now full."

**Current web-cms Status**: 
- Capacity check exists in `Event::getIsRegistrationOpenAttribute()` but only prevents form display
- Need to add check before submission in controller

### 2.5 QR Code / Unique Registrant ID

**Description**: Generate UUID for each registrant and optionally create QR code image.

**Database Changes**:
```sql
ALTER TABLE event_registrations ADD COLUMN uuid CHAR(36) UNIQUE AFTER id;
ALTER TABLE event_registrations ADD COLUMN qr_image VARCHAR(255) AFTER uuid;
ALTER TABLE event_registrations ADD INDEX idx_uuid (uuid);
```

**Implementation**:
1. Generate UUID using Laravel's `Str::uuid()` on registration creation
2. Store in `uuid` column
3. Optionally generate QR code image (stored in `qr_image` or generated on-demand)
4. QR code URL format: `/qr/{event_slug}/{uuid}`

**Use Cases**:
- Check-in at event venue
- Registration confirmation lookup
- Badge printing

### 2.6 Referral Tracking

**Description**: Optional referral code/source tracking for marketing attribution.

**Database Changes**:
```sql
ALTER TABLE event_registrations ADD COLUMN referral_code VARCHAR(50) AFTER notes;
ALTER TABLE event_registrations ADD COLUMN referral_source VARCHAR(255) AFTER referral_code;
```

**Implementation**:
1. Capture `referral_code` from form input (optional field)
2. Capture `referral_source` from:
   - Tracking code lookup (if tracking_codes table exists)
   - UTM parameters (utm_source, utm_campaign, utm_medium)
   - Default: "Direct" if none provided
3. Store in registration record

**UTM Parameter Handling**:
```
Format: {utm_source} - {utm_campaign} ({utm_medium})
Example: "newsletter - june_event (email)"
```

### 2.7 Consent Checkbox

**Description**: Required consent checkbox for data processing / terms acceptance.

**Field Specification**:
- Name: `consentCheckbox`
- Type: Checkbox
- Label: "I agree to the processing of my personal data for event registration purposes."
- Validation: `required|accepted`

**Implementation**:
- Add checkbox to registration form
- Validation fails if not checked
- Store consent timestamp in `custom_fields` JSON or new column

**Optional Database Addition**:
```sql
ALTER TABLE event_registrations ADD COLUMN consent_accepted_at TIMESTAMP NULL AFTER custom_fields;
```

### 2.8 Registration Confirmation Page

**Description**: Custom success page with registration confirmation details.

**Route**:
```
GET /event/success
```

**Page Content**:
- Success message
- Event details (name, date, location)
- Registrant details (name, email)
- Registration status (pending/confirmed)
- Next steps information
- QR code display (if UUID exists)

**Current Status**: Basic success page exists at `/event/success` route. Enhancement needed to display registration details.

## Database Schema Changes

### event_registrations Table - New Columns

```sql
-- Add to existing event_registrations table
ALTER TABLE event_registrations
    ADD COLUMN salutation VARCHAR(10) NULL AFTER user_id,
    ADD COLUMN full_name VARCHAR(255) NULL AFTER salutation,
    ADD COLUMN company_name VARCHAR(255) NULL AFTER full_name,
    ADD COLUMN company_type VARCHAR(50) NULL AFTER company_name,
    ADD COLUMN job_title VARCHAR(255) NULL AFTER company_type,
    ADD COLUMN contact_level INT NULL AFTER job_title,
    ADD COLUMN contact_divisi_id INT NULL AFTER contact_level,
    ADD COLUMN contact_divisi_name VARCHAR(255) NULL AFTER contact_divisi_id,
    ADD COLUMN country_code VARCHAR(10) NULL AFTER contact_divisi_name,
    ADD COLUMN mobile_phone VARCHAR(20) NULL AFTER country_code,
    ADD COLUMN uuid CHAR(36) UNIQUE NULL AFTER mobile_phone,
    ADD COLUMN qr_image VARCHAR(255) NULL AFTER uuid,
    ADD COLUMN referral_code VARCHAR(50) NULL AFTER qr_image,
    ADD COLUMN referral_source VARCHAR(255) NULL AFTER referral_code,
    ADD COLUMN consent_accepted_at TIMESTAMP NULL AFTER referral_source,
    ADD COLUMN walk_in BOOLEAN DEFAULT FALSE AFTER consent_accepted_at,
    ADD COLUMN check_in BOOLEAN DEFAULT FALSE AFTER walk_in,
    ADD COLUMN check_in_date TIMESTAMP NULL AFTER check_in,
    ADD COLUMN registration_type VARCHAR(20) DEFAULT 'normal' AFTER check_in_date,
    ADD INDEX idx_uuid (uuid),
    ADD INDEX idx_event_email (event_id, email);
```

### events Table - New Columns

```sql
-- Add to existing events table
ALTER TABLE events
    ADD COLUMN requires_corporate_email BOOLEAN DEFAULT FALSE AFTER registration_requires_approval,
    ADD COLUMN registration_start_date TIMESTAMP NULL AFTER requires_corporate_email,
    ADD COLUMN registration_end_date TIMESTAMP NULL AFTER registration_start_date,
    ADD COLUMN enable_track_session BOOLEAN DEFAULT FALSE AFTER registration_end_date;
```

### New Tables (Optional)

```sql
-- Free email domains for corporate email validation
CREATE TABLE free_email_domains (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(100) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tracking codes for referral tracking
CREATE TABLE tracking_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_id BIGINT UNSIGNED NOT NULL,
    tracking_code VARCHAR(50) UNIQUE NOT NULL,
    source VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_code (event_id, tracking_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact levels dropdown
CREATE TABLE contact_levels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    level INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact divisions dropdown
CREATE TABLE contact_divisions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Implementation Notes

### Livewire Component Structure

**Component**: `EventRegistrationForm`

**Location**: `plugins/events/src/Livewire/EventRegistrationForm.php`

**Responsibilities**:
- Display registration form with all fields
- Handle form submission
- Validate input (server-side)
- Show validation errors
- Redirect to success page on success

**Key Methods**:
```php
class EventRegistrationForm extends Component
{
    public $event;
    public $salutation;
    public $full_name;
    public $company_name;
    public $job_title;
    public $contact_level;
    public $contact_divisi_id;
    public $contact_divisi_name;
    public $mobile_phone;
    public $email;
    public $consentCheckbox = false;
    public $referral_code;

    protected $rules = [
        'full_name' => 'required|max:255',
        'company_name' => 'required|max:255',
        'job_title' => 'required|max:255',
        'mobile_phone' => 'required|phone_format',
        'email' => 'required|email|corporate_email_if_required',
        'consentCheckbox' => 'required|accepted',
    ];

    public function mount($slug)
    {
        $this->event = Event::where('slug', $slug)->firstOrFail();
    }

    public function register()
    {
        $this->validate();

        // Additional validations
        $this->validateCapacity();
        $this->validateDuplicate();

        // Create registration
        $registration = $this->createRegistration();

        return redirect()->route('events.register.success');
    }

    protected function validateCapacity()
    {
        if ($this->event->max_participants) {
            $count = $this->event->registrations()
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            if ($count >= $this->event->max_participants) {
                throw ValidationException::withMessages([
                    'capacity' => 'This event has reached maximum capacity.'
                ]);
            }
        }
    }

    protected function validateDuplicate()
    {
        $existing = EventRegistration::where('event_id', $this->event->id)
            ->where('email', $this->email)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'email' => 'This email is already registered for this event.'
            ]);
        }
    }

    protected function createRegistration()
    {
        $registration = EventRegistration::create([
            'event_id' => $this->event->id,
            'uuid' => Str::uuid(),
            'salutation' => $this->salutation,
            'full_name' => $this->full_name,
            'company_name' => $this->company_name,
            'company_type' => $this->detectCompanyType($this->company_name),
            'job_title' => $this->job_title,
            'contact_level' => $this->contact_level,
            'contact_divisi_id' => $this->contact_divisi_id,
            'contact_divisi_name' => $this->contact_divisi_name,
            'mobile_phone' => $this->formatPhoneNumber($this->mobile_phone),
            'email' => $this->email,
            'referral_code' => $this->referral_code,
            'referral_source' => $this->getReferralSource(),
            'status' => $this->event->registration_requires_approval ? 'pending' : 'confirmed',
            'consent_accepted_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $registration;
    }

    protected function detectCompanyType($companyName)
    {
        // Auto-detect PT, CV, etc. from company name
        $types = ['PT', 'CV', 'Firma', 'UD', 'Yayasan', 'Koperasi',
                  'Ltd', 'LLC', 'Inc', 'Corp', 'Pte Ltd', 'GmbH'];

        foreach ($types as $type) {
            if (stripos($companyName, $type) === 0) {
                return $type;
            }
        }

        return null;
    }

    protected function formatPhoneNumber($phone)
    {
        // Format Indonesian phone number
        $cleaned = preg_replace('/[^\d]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            return '62' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    protected function getReferralSource()
    {
        // Check tracking code first
        if ($this->referral_code) {
            $tracking = TrackingCode::where('tracking_code', $this->referral_code)
                ->where('event_id', $this->event->id)
                ->first();

            if ($tracking) {
                return $tracking->source;
            }
        }

        // Fallback to UTM parameters
        $source = request()->query('utm_source', 'Direct');
        $campaign = request()->query('utm_campaign');
        $medium = request()->query('utm_medium');

        if ($campaign) {
            $source .= ' - ' . $campaign;
        }

        if ($medium) {
            $source .= ' (' . $medium . ')';
        }

        return $source;
    }
}
```

### Validation Approach

**Laravel Validation Rules**:
- Extend Laravel's validation with custom rules
- Create `App\Rules\CorporateEmail` rule class
- Create `App\Rules\PhoneNumberFormat` rule class

**Custom Rule: CorporateEmail**
```php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CorporateEmail implements Rule
{
    protected $eventId;

    public function __construct($eventId = null)
    {
        $this->eventId = $eventId;
    }

    public function passes($attribute, $value)
    {
        // Check if event requires corporate email
        if ($this->eventId) {
            $event = Event::find($this->eventId);
            if (!$event || !$event->requires_corporate_email) {
                return true; // Corporate email not required
            }
        }

        // Validate against free email domains
        return $this->isCorporateEmail($value);
    }

    protected function isCorporateEmail($email)
    {
        $domain = strtolower(explode('@', $email)[1] ?? '');

        // Check database first
        $blocked = \DB::table('free_email_domains')
            ->where('domain', $domain)
            ->where('is_active', 1)
            ->exists();

        if ($blocked) {
            return false;
        }

        // Fallback hardcoded list
        $freeDomains = [
            'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
            'aol.com', 'icloud.com', 'live.com', 'msn.com',
        ];

        return !in_array($domain, $freeDomains);
    }

    public function message()
    {
        return 'Corporate email required. Free email providers are not allowed.';
    }
}
```

**Custom Rule: PhoneNumberFormat**
```php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumberFormat implements Rule
{
    public function passes($attribute, $value)
    {
        $cleaned = preg_replace('/[^\d]/', '', $value);

        // Must be 9-17 digits
        if (strlen($cleaned) < 9 || strlen($cleaned) > 17) {
            return false;
        }

        // If starts with 0, invalid (should use country code)
        if (str_starts_with($cleaned, '0')) {
            return false;
        }

        // Must start with valid country code
        $validCodes = ['62', '65', '60', '66', '63', '84'];
        $startsWithValidCode = false;

        foreach ($validCodes as $code) {
            if (str_starts_with($cleaned, $code)) {
                $startsWithValidCode = true;
                break;
            }
        }

        return $startsWithValidCode;
    }

    public function message()
    {
        return 'Phone number format is invalid. Please use international format (e.g., +62...)';
    }
}
```

### Form Field Migration Notes

**Existing web-cms fields**:
- `name` → Keep for backward compatibility, map to `full_name`
- `organization` → Keep, map to `company_name`
- `custom_fields` JSON → Continue using for extensibility

**New field mappings**:
- ERS `full_name` → web-cms `full_name` (new)
- ERS `mobile_phone` → web-cms `mobile_phone` (new)
- ERS `salutation` → web-cms `salutation` (new)

## Acceptance Criteria

### Phase 1: Core Registration Fields
- [ ] Registration form displays all standard fields (salutation, full_name, company_name, job_title, email, phone)
- [ ] All required fields are validated on submission
- [ ] Validation error messages display correctly
- [ ] Successful registration creates record in database
- [ ] Success page displays with confirmation details

### Phase 2: Corporate Email Validation
- [ ] Corporate email validation rule created and registered
- [ ] Validation respects event's `requires_corporate_email` setting
- [ ] Free email domains are blocked when required
- [ ] Database lookup for free_email_domains works
- [ ] Fallback to hardcoded list works when database unavailable
- [ ] Clear error message shown when corporate email required but not provided

### Phase 3: Capacity & Duplicate Detection
- [ ] Capacity check runs before registration creation
- [ ] Registration rejected when event at max capacity
- [ ] Duplicate email detection works per event
- [ ] Appropriate error messages shown
- [ ] Capacity check respects approval setting (counts all vs confirmed only)

### Phase 4: UUID & QR Code
- [ ] UUID generated for each registration
- [ ] UUID stored in database and indexed
- [ ] QR code image generated (optional)
- [ ] QR code accessible via `/qr/{event_slug}/{uuid}` route
- [ ] QR code displays on success page (if enabled)

### Phase 5: Referral Tracking
- [ ] Referral code field displayed on form (optional)
- [ ] Referral source captured and stored
- [ ] UTM parameters parsed and stored in referral_source
- [ ] Tracking code lookup works if tracking_codes table exists
- [ ] Default "Direct" source used when no referral

### Phase 6: Consent & Additional Features
- [ ] Consent checkbox displayed on form
- [ ] Consent required for submission
- [ ] Consent timestamp stored in database
- [ ] Contact level dropdown populated from database
- [ ] Contact division dropdown populated from database
- [ ] "Other" division option shows text input when selected
- [ ] Company type auto-detected from company name

### Phase 7: Registration Period
- [ ] Registration start/end date validation
- [ ] Registration rejected before start date
- [ ] Registration rejected after end date
- [ ] Timezone support for period validation
- [ ] Clear error messages for period violations

### Testing Requirements
- [ ] Unit tests for corporate email validation
- [ ] Unit tests for phone number formatting
- [ ] Unit tests for capacity checking
- [ ] Unit tests for duplicate detection
- [ ] Integration test for complete registration flow
- [ ] Feature test for Livewire component
