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

            {{-- ── Custom Questions Section ───────────────────────────────────────────── --}}
            @if($event->customQuestions->count() > 0)
            <div class="col-12 mt-4 pt-4 border-top">
                <h5 class="fw-bold mb-4 text-primary">
                    <span class="material-symbols-outlined align-middle me-2" style="font-size:1.2em;">quiz</span>
                    Additional Information
                </h5>

                @foreach($event->customQuestions->ordered()->get() as $question)
                    <div class="mb-4" data-question-id="{{ $question->id }}">

                        {{-- Question Image --}}
                        @if($question->image)
                            <img src="{{ asset('storage/' . $question->image) }}"
                                class="mb-2 rounded" style="max-height:120px; max-width:100%; object-fit:contain;">
                        @endif

                        <label class="form-label small fw-bold">
                            {{ $question->question }}
                            @if($question->required)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        {{-- Help text --}}
                        @if($question->question_description)
                            <small class="text-muted d-block mb-1">{{ $question->question_description }}</small>
                        @endif

                        {{-- TEXT --}}
                        @if($question->type === 'text')
                            <input wire:model.live="custom_questions.{{ $question->short_label }}" type="text"
                                class="form-control border-0 border-bottom rounded-0 px-0 @error('custom_questions.' . $question->short_label) is-invalid @enderror"
                                placeholder="Your answer">
                            @error('custom_questions.' . $question->short_label)
                                <div class="text-danger" style="font-size:0.85em; margin-top:0.25rem;">{{ $message }}</div>
                            @enderror

                        {{-- TEXTAREA --}}
                        @elseif($question->type === 'textarea')
                            <textarea wire:model.live="custom_questions.{{ $question->short_label }}" rows="3"
                                class="form-control border-0 border-bottom rounded-0 px-0 @error('custom_questions.' . $question->short_label) is-invalid @enderror"
                                placeholder="Your answer"></textarea>
                            @error('custom_questions.' . $question->short_label)
                                <div class="text-danger" style="font-size:0.85em; margin-top:0.25rem;">{{ $message }}</div>
                            @enderror

                        {{-- EMAIL --}}
                        @elseif($question->type === 'email')
                            <input wire:model.live="custom_questions.{{ $question->short_label }}" type="email"
                                class="form-control border-0 border-bottom rounded-0 px-0 @error('custom_questions.' . $question->short_label) is-invalid @enderror"
                                placeholder="email@example.com">
                            @error('custom_questions.' . $question->short_label)
                                <div class="text-danger" style="font-size:0.85em; margin-top:0.25rem;">{{ $message }}</div>
                            @enderror

                        {{-- PHONE --}}
                        @elseif($question->type === 'phone')
                            <input wire:model.live="custom_questions.{{ $question->short_label }}" type="tel"
                                class="form-control border-0 border-bottom rounded-0 px-0 @error('custom_questions.' . $question->short_label) is-invalid @enderror"
                                placeholder="+62 812 3456 7890">
                            @error('custom_questions.' . $question->short_label)
                                <div class="text-danger" style="font-size:0.85em; margin-top:0.25rem;">{{ $message }}</div>
                            @enderror

                        {{-- DATE --}}
                        @elseif($question->type === 'date')
                            <input wire:model.live="custom_questions.{{ $question->short_label }}" type="date"
                                class="form-control border-0 border-bottom rounded-0 px-0 @error('custom_questions.' . $question->short_label) is-invalid @enderror">
                            @error('custom_questions.' . $question->short_label)
                                <div class="text-danger" style="font-size:0.85em; margin-top:0.25rem;">{{ $message }}</div>
                            @enderror

                        {{-- SINGLE SELECT (radio-style buttons) --}}
                        @elseif($question->type === 'single_select')
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($question->options as $option)
                                    <label class="cursor-pointer">
                                        <input wire:model.live="custom_questions.{{ $question->short_label }}"
                                            type="radio" name="custom_{{ $question->short_label }}"
                                            value="{{ $option->option_text }}"
                                            class="btn-check">
                                        <span class="btn btn-outline-primary rounded-pill px-3 py-1">
                                            {{ $option->option_text }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('custom_questions.' . $question->short_label)
                                <div class="text-danger" style="font-size:0.85em; margin-top:0.25rem;">{{ $message }}</div>
                            @enderror

                        {{-- MULTI SELECT (checkboxes) --}}
                        @elseif($question->type === 'multi_select')
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($question->options as $option)
                                    <label class="cursor-pointer">
                                        <input wire:model.live="custom_questions.{{ $question->short_label }}"
                                            type="checkbox" value="{{ $option->option_text }}"
                                            class="btn-check">
                                        <span class="btn btn-outline-primary rounded-pill px-3 py-1">
                                            {{ $option->option_text }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <small class="text-muted">Select all that apply</small>
                            @error('custom_questions.' . $question->short_label)
                                <div class="text-danger" style="font-size:0.85em; margin-top:0.25rem;">{{ $message }}</div>
                            @enderror
                        @endif

                    </div>
                @endforeach
            </div>
            @endif

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
