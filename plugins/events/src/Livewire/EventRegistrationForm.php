<?php

namespace Plugins\Events\Livewire;

use Livewire\Component;
use Plugins\Events\Models\Event;
use Plugins\Events\Models\EventRegistration;
use Plugins\Events\Models\ContactLevel;
use Plugins\Events\Models\ContactDivision;
use App\Rules\CorporateEmail;
use App\Rules\PhoneNumberFormat;
use Illuminate\Support\Str;

class EventRegistrationForm extends Component
{
    public Event $event;

    // ─── Form Fields ─────────────────────────────────────────────────────────
    public string $salutation       = '';
    public string $full_name       = '';
    public string $company_name     = '';
    public string $job_title       = '';
    public int    $contact_level_id = 0;
    public int    $contact_divisi_id = 0;
    public string $contact_divisi_name = '';
    public string $country_code    = '+62';
    public string $mobile_phone    = '';
    public string $email           = '';
    public string $notes           = '';
    public string $referral_code   = '';
    public bool   $consentCheckbox = false;

    protected function getRules(): array
    {
        $rules = [
            'salutation'          => 'nullable|in:Mr,Ms,Mrs',
            'full_name'          => 'required|string|max:255',
            'company_name'       => 'required|string|max:255',
            'job_title'          => 'required|string|max:255',
            'contact_level_id'   => 'required|integer|exists:contact_levels,id',
            'contact_divisi_id'  => 'required|integer|exists:contact_divisions,id',
            'contact_divisi_name'=> "nullable|string|max:255",
            'country_code'       => 'nullable|string|max:10',
            'mobile_phone'       => ['required', new PhoneNumberFormat()],
            'email'              => [
                'required',
                'email',
                'max:255',
                new CorporateEmail($this->event->id),
            ],
            'notes'              => 'nullable|string',
            'referral_code'      => 'nullable|string|max:50',
            'consentCheckbox'    => 'accepted',
        ];

        return $rules;
    }

    public function mount(string $slug): void
    {
        $this->event = Event::where('slug', $slug)->published()->firstOrFail();
    }

    public function updatedContactDivisiId(int $value): void
    {
        // Clear other-divisi text when switching away from "Other"
        if ($value != 5) {
            $this->contact_divisi_name = '';
        }
    }

    public function register()
    {
        // Extra: validate conditional divisi_name
        if ($this->contact_divisi_id == 5 && empty(trim($this->contact_divisi_name))) {
            $this->addError('contact_divisi_name', 'Please specify your division.');
            return;
        }

        $this->validate();

        // ── Capacity check ──────────────────────────────────────────────
        if ($this->event->max_participants) {
            $query = $this->event->registrations()->whereIn('status', ['pending', 'confirmed']);

            // If approval not required, only count confirmed
            if (!($this->event->registration_requires_approval ?? false)) {
                $query->where('status', 'confirmed');
            }

            if ($query->count() >= $this->event->max_participants) {
                $this->addError('capacity', 'This event has reached its maximum capacity and is now full.');
                return;
            }
        }

        // ── Duplicate check ─────────────────────────────────────────────
        $duplicate = EventRegistration::where('event_id', $this->event->id)
            ->where('email', $this->email)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($duplicate) {
            $this->addError('email', 'This email is already registered for this event.');
            return;
        }

        // ── Registration period check ───────────────────────────────────
        if ($this->event->registration_start_date && $this->event->registration_start_date->isFuture()) {
            $this->addError('registration', 'Registration for this event has not yet opened.');
            return;
        }
        $effectiveEnd = $this->event->registration_end_date ?? $this->event->registration_deadline;
        if ($effectiveEnd && $effectiveEnd->isPast()) {
            $this->addError('registration', 'Registration for this event has closed.');
            return;
        }

        // ── Create registration ──────────────────────────────────────────
        $requiresApproval = (bool) ($this->event->registration_requires_approval ?? false);

        EventRegistration::create([
            'event_id'              => $this->event->id,
            'uuid'                  => Str::uuid(),
            'salutation'            => $this->salutation ?: null,
            'full_name'             => $this->full_name,
            'company_name'          => $this->company_name,
            'company_type'          => EventRegistration::detectCompanyType($this->company_name),
            'job_title'             => $this->job_title,
            'contact_level_id'      => $this->contact_level_id,
            'contact_divisi_id'     => $this->contact_divisi_id,
            'contact_divisi_name'   => $this->contact_divisi_id == 5 ? $this->contact_divisi_name : null,
            'country_code'          => $this->country_code,
            'mobile_phone'          => EventRegistration::formatPhoneNumber($this->mobile_phone),
            'email'                 => $this->email,
            'notes'                 => $this->notes ?: null,
            'referral_code'         => $this->referral_code ?: null,
            'referral_source'       => EventRegistration::buildReferralSource($this->referral_code, request()),
            'status'                => $requiresApproval ? 'pending' : 'confirmed',
            'confirmed_at'          => $requiresApproval ? null : now(),
            'consent_accepted_at'   => $this->consentCheckbox ? now() : null,
            'walk_in'               => false,
            'ip_address'           => request()->ip(),
            'user_agent'            => request()->userAgent(),
        ]);

        // Increment count only if auto-confirmed
        if (!$requiresApproval) {
            $this->event->incrementRegisteredCount();
        }

        $message = $requiresApproval
            ? 'Registration submitted! Your registration is pending admin approval.'
            : 'Registration successful! You will receive a confirmation email shortly.';

        session()->flash('success', $message);

        return redirect()->route('events.register.success', [
            'slug'  => $this->event->slug,
            'email' => $this->email,
        ]);
    }

    public function render()
    {
        $contactLevels  = ContactLevel::where('is_active', true)->orderBy('level')->get();
        $contactDivisions = ContactDivision::where('is_active', true)->orderBy('name')->get();
        $countries = [
            '+62' => 'Indonesia (+62)',
            '+65' => 'Singapore (+65)',
            '+60' => 'Malaysia (+60)',
            '+66' => 'Thailand (+66)',
            '+63' => 'Philippines (+63)',
            '+84' => 'Vietnam (+84)',
        ];

        return view('events::livewire.event-registration-form', [
            'contactLevels'    => $contactLevels,
            'contactDivisions'=> $contactDivisions,
            'countries'        => $countries,
        ]);
    }
}
