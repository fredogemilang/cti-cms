<div>
    {{-- ── Capacity / duplicate / registration-period errors ──────────────────── --}}
    @if($errors->has('capacity') || $errors->has('registration'))
        <div class="alert alert-danger rounded-3 mb-4">
            @error('capacity') <p class="mb-0">{{ $message }}</p> @enderror
            @error('registration') <p class="mb-0">{{ $message }}</p> @enderror
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="register" novalidate>
        <div class="row g-4">

            {{-- ── Row 1: Salutation + Full Name ─────────────────────────────── --}}
            <div class="col-md-2">
                <label class="form-label small fw-bold">
                    Salutation
                </label>
                <select wire:model="salutation"
                    class="form-select border-0 border-bottom rounded-0 px-0 @error('salutation') is-invalid @enderror">
                    <option value="">—</option>
                    <option value="Mr">Mr</option>
                    <option value="Ms">Ms</option>
                    <option value="Mrs">Mrs</option>
                </select>
                @error('salutation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-10">
                <label class="form-label small fw-bold">
                    Full Name <span class="text-danger">*</span>
                </label>
                <input wire:model.blur="full_name" type="text"
                    class="form-control border-0 border-bottom rounded-0 px-0 @error('full_name') is-invalid @enderror"
                    placeholder="Your full name">
                @error('full_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── Row 2: Company + Job Title ─────────────────────────────────── --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">
                    Company / Institution <span class="text-danger">*</span>
                </label>
                <input wire:model.blur="company_name" type="text"
                    class="form-control border-0 border-bottom rounded-0 px-0 @error('company_name') is-invalid @enderror"
                    placeholder="PT Example Indonesia">
                @error('company_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold">
                    Job Title <span class="text-danger">*</span>
                </label>
                <input wire:model.blur="job_title" type="text"
                    class="form-control border-0 border-bottom rounded-0 px-0 @error('job_title') is-invalid @enderror"
                    placeholder="Cloud Engineer">
                @error('job_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── Row 3: Contact Level + Division ─────────────────────────────── --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold">
                    Job Level <span class="text-danger">*</span>
                </label>
                <select wire:model="contact_level_id"
                    class="form-select border-0 border-bottom rounded-0 px-0 @error('contact_level_id') is-invalid @enderror">
                    <option value="0" disabled>Select level...</option>
                    @foreach($contactLevels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
                @error('contact_level_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold">
                    Division / Department <span class="text-danger">*</span>
                </label>
                <select wire:model.live="contact_divisi_id"
                    class="form-select border-0 border-bottom rounded-0 px-0 @error('contact_divisi_id') is-invalid @enderror">
                    <option value="0" disabled>Select division...</option>
                    @foreach($contactDivisions as $div)
                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                    @endforeach
                </select>
                @error('contact_divisi_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Division "Other" text input ---------------------------------------- --}}
            @if($contact_divisi_id == 5)
            <div class="col-12">
                <label class="form-label small fw-bold">
                    Specify Your Division <span class="text-danger">*</span>
                </label>
                <input wire:model.blur="contact_divisi_name" type="text"
                    class="form-control border-0 border-bottom rounded-0 px-0 @error('contact_divisi_name') is-invalid @enderror"
                    placeholder="e.g., Research & Development">
                @error('contact_divisi_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif

            {{-- ── Row 4: Phone (country code + number) ────────────────────────── --}}
            <div class="col-md-3">
                <label class="form-label small fw-bold">Country Code</label>
                <select wire:model="country_code"
                    class="form-select border-0 border-bottom rounded-0 px-0">
                    @foreach($countries as $code => $label)
                        <option value="{{ $code }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-9">
                <label class="form-label small fw-bold">
                    Phone Number <span class="text-danger">*</span>
                </label>
                <input wire:model.blur="mobile_phone" type="tel"
                    class="form-control border-0 border-bottom rounded-0 px-0 @error('mobile_phone') is-invalid @enderror"
                    placeholder="81234567890  (without leading 0)">
                @error('mobile_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── Row 5: Email ──────────────────────────────────────────────────── --}}
            <div class="col-md-12">
                <label class="form-label small fw-bold">
                    Email Address <span class="text-danger">*</span>
                    @if($event->requires_corporate_email)
                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Corporate email required</span>
                    @endif
                </label>
                <input wire:model.blur="email" type="email"
                    class="form-control border-0 border-bottom rounded-0 px-0 @error('email') is-invalid @enderror"
                    placeholder="you@company.com">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── Row 6: Referral Code (optional) ──────────────────────────────── --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">
                    Referral / Promo Code
                    <span class="fw-normal">(optional)</span>
                </label>
                <input wire:model="referral_code" type="text"
                    class="form-control border-0 border-bottom rounded-0 px-0"
                    placeholder="e.g., ICCOM2025">
            </div>

            {{-- ── Row 7: Notes ─────────────────────────────────────────────────── --}}
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">
                    Notes / Message
                    <span class="fw-normal">(optional)</span>
                </label>
                <input wire:model="notes" type="text"
                    class="form-control border-0 border-bottom rounded-0 px-0"
                    placeholder="Any special requirements?">
            </div>

            {{-- ── Consent Checkbox ──────────────────────────────────────────────── --}}
            <div class="col-12 mt-2">
                <div class="form-check @error('consentCheckbox') is-invalid @enderror">
                    <input wire:model="consentCheckbox" type="checkbox" id="consentCheckbox"
                        class="form-check-input @error('consentCheckbox') is-invalid @enderror"
                        style="width:1.1em; height:1.1em;">
                    <label class="form-check-label small" for="consentCheckbox">
                        I agree to the processing of my personal data for event registration purposes.
                        <span class="text-danger">*</span>
                    </label>
                </div>
                @error('consentCheckbox')
                    <div class="text-danger" style="font-size: 0.85em; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── Submit ─────────────────────────────────────────────────────────── --}}
            <div class="col-12 text-center mt-4">
                <button type="submit"
                    class="btn btn-cta btn-warning text-white rounded-pill px-5 py-2 fw-bold shadow"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="register">Submit Registration</span>
                    <span wire:loading wire:target="register">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Submitting...
                    </span>
                </button>
            </div>

        </div>{{-- /row --}}
    </form>
</div>
