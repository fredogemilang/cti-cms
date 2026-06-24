<div class="form-registration-container py-3">
    <style>
        [wire\:loading] {
            display: none;
        }
        .form-registration-container .form-label {
            font-family: 'Montserrat', 'Inter', sans-serif;
            color: #333333;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-registration-container .form-control,
        .form-registration-container .form-select {
            border-top: 0 !important;
            border-left: 0 !important;
            border-right: 0 !important;
            border-bottom: 1px solid #E2E8F0 !important;
            border-radius: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            font-size: 0.95rem;
            color: #1F2937;
            background-color: transparent !important;
            box-shadow: none !important;
            transition: border-color 0.2s ease-in-out;
            height: auto;
            padding-top: 0.375rem;
            padding-bottom: 0.375rem;
        }
        .form-registration-container .form-control:focus,
        .form-registration-container .form-select:focus {
            border-bottom-color: #F28F35 !important;
        }
        .form-registration-container .form-control.is-invalid,
        .form-registration-container .form-select.is-invalid {
            border-bottom-color: #EF4444 !important;
        }
        .form-registration-container .form-control::placeholder {
            color: #9CA3AF;
        }
        .form-registration-container select.custom-orange-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23F28F35'%3e%3cpath d='M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0px center;
            background-size: 20px;
            padding-right: 24px !important;
        }
        .btn-submit-orange {
            background-color: #F28F35 !important;
            border-color: #F28F35 !important;
            color: #ffffff !important;
            font-family: 'Montserrat', 'Inter', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 0.65rem 4rem;
            border-radius: 50rem;
            transition: all 0.2s ease-in-out;
            border: 1px solid transparent;
        }
        .btn-submit-orange:hover {
            background-color: #E07D24 !important;
            border-color: #E07D24 !important;
            box-shadow: 0 4px 14px rgba(242, 143, 53, 0.4);
        }
        .domicile-option:hover, .hover-bg-light:hover {
            background-color: #f8f9fa !important;
            color: #F28F35 !important;
        }
        .form-registration-container .form-check-input:checked {
            background-color: #F28F35 !important;
            border-color: #F28F35 !important;
        }
        .form-registration-container .form-check-input:focus {
            border-color: #F28F35 !important;
            box-shadow: 0 0 0 0.25rem rgba(242, 143, 53, 0.25) !important;
        }
    </style>

    {{-- Capacity / duplicate / registration-period errors --}}
    @if($errors->has('capacity') || $errors->has('registration'))
        <div class="alert alert-danger rounded-3 mb-4">
            @error('capacity') <p class="mb-0">{{ $message }}</p> @enderror
            @error('registration') <p class="mb-0">{{ $message }}</p> @enderror
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
    @endif

    <form
        x-data="{
            errors: {},
            validate() {
                this.errors = {};
                let isValid = true;
                
                // Name validation
                let nameVal = this.$el.querySelector('[name=full_name]')?.value || '';
                if (!nameVal.trim()) {
                    this.errors.full_name = 'Name is required.';
                    isValid = false;
                }
                
                // Email validation
                let emailVal = this.$el.querySelector('[name=email]')?.value || '';
                if (!emailVal.trim()) {
                    this.errors.email = 'E-mail is required.';
                    isValid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                    this.errors.email = 'Please enter a valid e-mail address.';
                    isValid = false;
                }
                
                // Phone validation
                let phoneVal = this.$el.querySelector('[name=mobile_phone]')?.value || '';
                if (!phoneVal.trim()) {
                    this.errors.mobile_phone = 'Phone Number is required.';
                    isValid = false;
                } else {
                    let cleanPhone = phoneVal.replace(/\D/g, '');
                    if (cleanPhone.length < 9 || cleanPhone.length > 13) {
                        this.errors.mobile_phone = 'Phone Number must be between 9 and 13 digits.';
                        isValid = false;
                    }
                }
                
                // Job Level validation
                let levelVal = this.$el.querySelector('[name=contact_level_id]')?.value || '0';
                if (levelVal === '0' || !levelVal) {
                    this.errors.contact_level_id = 'Job Level is required.';
                    isValid = false;
                }
                
                // Job Title validation
                let titleVal = this.$el.querySelector('[name=job_title]')?.value || '';
                if (!titleVal) {
                    this.errors.job_title = 'Job Title is required.';
                    isValid = false;
                }
                
                // Institution validation
                let companyVal = this.$el.querySelector('[name=company_name]')?.value || '';
                if (!companyVal.trim()) {
                    this.errors.company_name = 'Institution/company is required.';
                    isValid = false;
                }
                
                // Industry validation
                let industryVal = this.$el.querySelector('[name=industry]')?.value || '';
                if (!industryVal) {
                    this.errors.industry = 'Industry is required.';
                    isValid = false;
                }
                
                // Domicile validation
                let domicileVal = this.$wire.get('domicile') || '';
                if (!domicileVal) {
                    this.errors.domicile = 'Domicile is required.';
                    isValid = false;
                } else if (domicileVal === 'Other') {
                    let domicileOtherVal = this.$wire.get('domicile_other') || '';
                    if (!domicileOtherVal.trim()) {
                        this.errors.domicile = 'Please specify your domicile.';
                        isValid = false;
                    }
                }

                // LinkedIn validation
                let linkedinVal = this.$el.querySelector('[name=linkedin]')?.value || '';
                if (linkedinVal.trim() && !/^(https?:\/\/)?(www\.)?linkedin\.com\/.*$/i.test(linkedinVal)) {
                    this.errors.linkedin = 'LinkedIn account must be a valid LinkedIn URL.';
                    isValid = false;
                }
                
                return isValid;
            },
            submitForm() {
                if (this.validate()) {
                    this.$wire.register();
                }
            },
            clearError(name) {
                if (this.errors[name]) {
                    delete this.errors[name];
                }
            }
        }"
        @submit.prevent="submitForm()"
        @input="clearError($event.target.name)"
        @change="clearError($event.target.name)"
        novalidate
    >
        <div class="row g-4 lg:g-5">
            {{-- Column 1 --}}
            <div class="col-lg-4 col-md-6 col-12">
                <!-- Name -->
                <div class="mb-4">
                    <label class="form-label">Name: <span class="text-danger">*</span></label>
                    <input wire:model.blur="full_name" type="text" name="full_name"
                        class="form-control @error('full_name') is-invalid @enderror"
                        :class="errors.full_name ? 'is-invalid' : ''"
                        placeholder="Name">
                    <template x-if="errors.full_name">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.full_name"></div>
                    </template>
                    @error('full_name')
                        <div x-show="!errors.full_name" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- E-mail -->
                <div class="mb-4">
                    <label class="form-label">E-mail: <span class="text-danger">*</span></label>
                    <input wire:model.blur="email" type="email" name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        :class="errors.email ? 'is-invalid' : ''"
                        placeholder="Email">
                    <template x-if="errors.email">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.email"></div>
                    </template>
                    @error('email')
                        <div x-show="!errors.email" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div class="mb-4">
                    <label class="form-label">Phone Number: <span class="text-danger">*</span></label>
                    <input wire:model.blur="mobile_phone" type="tel" name="mobile_phone"
                        class="form-control @error('mobile_phone') is-invalid @enderror"
                        :class="errors.mobile_phone ? 'is-invalid' : ''"
                        placeholder="Phone Number">
                    <template x-if="errors.mobile_phone">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.mobile_phone"></div>
                    </template>
                    @error('mobile_phone')
                        <div x-show="!errors.mobile_phone" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Highest Education Level -->
                <div class="mb-4">
                    <label class="form-label">Highest Education Level:</label>
                    <select wire:model="highest_education_level" name="highest_education_level"
                        class="form-select custom-orange-select @error('highest_education_level') is-invalid @enderror"
                        :class="errors.highest_education_level ? 'is-invalid' : ''">
                        <option value="">Highest Education Level</option>
                        @foreach($educationLevels as $level)
                            <option value="{{ $level }}">{{ $level }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors.highest_education_level">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.highest_education_level"></div>
                    </template>
                    @error('highest_education_level')
                        <div x-show="!errors.highest_education_level" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Column 2 --}}
            <div class="col-lg-4 col-md-6 col-12">
                <!-- Job Level -->
                <div class="mb-4">
                    <label class="form-label">Job Level: <span class="text-danger">*</span></label>
                    <select wire:model="contact_level_id" name="contact_level_id"
                        class="form-select custom-orange-select @error('contact_level_id') is-invalid @enderror"
                        :class="errors.contact_level_id ? 'is-invalid' : ''">
                        <option value="0">Job Level</option>
                        @foreach($contactLevels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors.contact_level_id">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.contact_level_id"></div>
                    </template>
                    @error('contact_level_id')
                        <div x-show="!errors.contact_level_id" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Job Title -->
                <div class="mb-4">
                    <label class="form-label">Job Title: <span class="text-danger">*</span></label>
                    <select wire:model="job_title" name="job_title"
                        class="form-select custom-orange-select @error('job_title') is-invalid @enderror"
                        :class="errors.job_title ? 'is-invalid' : ''">
                        <option value="">Job Title</option>
                        @foreach($jobTitles as $title)
                            <option value="{{ $title }}">{{ $title }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors.job_title">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.job_title"></div>
                    </template>
                    @error('job_title')
                        <div x-show="!errors.job_title" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Institution/company -->
                <div class="mb-4">
                    <label class="form-label">Institution/company: <span class="text-danger">*</span></label>
                    <input wire:model.blur="company_name" type="text" name="company_name"
                        class="form-control @error('company_name') is-invalid @enderror"
                        :class="errors.company_name ? 'is-invalid' : ''"
                        placeholder="Institution/company">
                    <template x-if="errors.company_name">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.company_name"></div>
                    </template>
                    @error('company_name')
                        <div x-show="!errors.company_name" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Industry -->
                <div class="mb-4">
                    <label class="form-label">Industry: <span class="text-danger">*</span></label>
                    <select wire:model="industry" name="industry"
                        class="form-select custom-orange-select @error('industry') is-invalid @enderror"
                        :class="errors.industry ? 'is-invalid' : ''">
                        <option value="">Industry</option>
                        @foreach($industries as $ind)
                            <option value="{{ $ind }}">{{ $ind }}</option>
                        @endforeach
                    </select>
                    <template x-if="errors.industry">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.industry"></div>
                    </template>
                    @error('industry')
                        <div x-show="!errors.industry" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Column 3 --}}
            <div class="col-lg-4 col-md-6 col-12">
                <!-- Domicile -->
                <div class="mb-4">
                    <label class="form-label">Domicile: <span class="text-danger">*</span></label>
                    
                    <div class="position-relative" x-data="{ open: false }" @click.away="open = false">
                        <input 
                            type="text" 
                            wire:model.live.debounce.150ms="domicileSearch"
                            placeholder="Type to search Domicile..."
                            class="form-control @error('domicile') is-invalid @enderror"
                            :class="errors.domicile ? 'is-invalid' : ''"
                            @focus="open = true"
                            @keydown.escape="open = false"
                            autocomplete="off"
                            name="domicileSearch"
                        />
                        
                        <input type="hidden" name="domicile" wire:model="domicile" />

                        <div x-show="open" 
                             class="position-absolute bg-white border border-light-subtle rounded-3 shadow-lg w-100 z-3 overflow-y-auto"
                             style="max-height: 250px; top: 100%; left: 0; display: none;"
                             x-transition>
                            @if(strlen($domicileSearch) < 2)
                                <div class="px-3 py-2 text-muted" style="font-size: 0.85rem;">
                                    Type 2 or more characters to search...
                                </div>
                            @elseif(empty($this->getDomicileOptions()))
                                <div class="px-3 py-2 text-muted" style="font-size: 0.85rem;">
                                    No results found.
                                </div>
                            @else
                                @foreach($this->getDomicileOptions() as $option)
                                    <button type="button" 
                                            wire:click="selectDomicile('{{ addslashes($option['value']) }}', '{{ addslashes($option['label']) }}')"
                                            @click="open = false"
                                            class="d-block w-100 text-start border-0 bg-transparent px-3 py-2 text-dark domicile-option"
                                            style="font-size: 0.9rem; transition: background-color 0.1s;">
                                        {{ $option['label'] }}
                                    </button>
                                @endforeach
                            @endif
                            <button type="button" 
                                    wire:click="selectDomicile('Other', 'Other')"
                                    @click="open = false"
                                    class="d-block w-100 text-start border-0 bg-transparent px-3 py-2 text-[#F28F35] fw-bold hover-bg-light border-top border-light-subtle"
                                    style="font-size: 0.9rem;">
                                Other (Lainnya)
                            </button>
                        </div>
                    </div>

                    <template x-if="errors.domicile">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.domicile"></div>
                    </template>
                    @error('domicile')
                        <div x-show="!errors.domicile" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror

                    @if($domicile === 'Other')
                        <div class="mt-2">
                            <input 
                                type="text"
                                name="domicile_other"
                                wire:model.blur="domicile_other"
                                class="form-control"
                                placeholder="Specify Domicile"
                                required
                            />
                            @error('domicile_other')
                                <div class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>

                <!-- LinkedIn Account -->
                <div class="mb-4">
                    <label class="form-label">LinkedIn Account:</label>
                    <input wire:model.blur="linkedin" type="text" name="linkedin"
                        class="form-control @error('linkedin') is-invalid @enderror"
                        :class="errors.linkedin ? 'is-invalid' : ''"
                        placeholder="LinkedIn Account">
                    <template x-if="errors.linkedin">
                        <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.linkedin"></div>
                    </template>
                    @error('linkedin')
                        <div x-show="!errors.linkedin" class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- ── Custom Questions Section (Optional if exists) ───────────────────────────────────────────── --}}
            @if($event->customQuestions->count() > 0)
            <div class="col-12 mt-4 pt-4 border-top">
                <h5 class="fw-bold mb-4" style="color: #333;">Additional Information</h5>
                <div class="row">
                    @foreach($event->customQuestions as $question)
                        <div class="col-md-6 mb-4" data-question-id="{{ $question->id }}">
                            <label class="form-label">
                                {{ $question->question }}
                                @if($question->required)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            
                            @if($question->question_description)
                                <p class="text-muted small mb-2" style="font-size: 0.85rem; font-weight: normal; line-height: 1.4;">{{ $question->question_description }}</p>
                            @endif

                            @if($question->image)
                                <div class="mb-2">
                                    <img src="/storage/{{ $question->image }}" alt="{{ $question->question }}" class="img-fluid rounded-3" style="max-height: 180px; object-fit: contain;">
                                </div>
                            @endif

                            @if($question->type === 'text')
                                <input wire:model.live="custom_questions.{{ $question->short_label }}" type="text"
                                    class="form-control" placeholder="Your answer">
                            @elseif($question->type === 'textarea')
                                <textarea wire:model.live="custom_questions.{{ $question->short_label }}" rows="2"
                                    class="form-control" placeholder="Your answer"></textarea>
                            @elseif($question->type === 'single_select')
                                <select wire:model.live="custom_questions.{{ $question->short_label }}"
                                    class="form-select custom-orange-select">
                                    <option value="">Select option</option>
                                    @foreach($question->options as $option)
                                        <option value="{{ $option->option_text }}">{{ $option->option_text }}</option>
                                    @endforeach
                                </select>
                            @elseif($question->type === 'multi_select')
                                <div class="mt-2 d-flex flex-wrap gap-3">
                                    @foreach($question->options as $option)
                                        <div class="form-check">
                                            <input wire:model.live="custom_questions.{{ $question->short_label }}" 
                                                type="checkbox" 
                                                value="{{ $option->option_text }}" 
                                                id="q_{{ $question->id }}_{{ $loop->index }}"
                                                class="form-check-input">
                                            <label class="form-check-label small" for="q_{{ $question->id }}_{{ $loop->index }}">
                                                {{ $option->option_text }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question->type === 'email')
                                <input wire:model.live="custom_questions.{{ $question->short_label }}" type="email"
                                    class="form-control" placeholder="Your email address">
                            @elseif($question->type === 'phone')
                                <input wire:model.live="custom_questions.{{ $question->short_label }}" type="tel"
                                    class="form-control" placeholder="Your phone number">
                            @elseif($question->type === 'date')
                                <input wire:model.live="custom_questions.{{ $question->short_label }}" type="date"
                                    class="form-control">
                            @endif
                            @error('custom_questions.' . $question->short_label)
                                <div class="text-danger small mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Submit Button --}}
            <div class="col-12 text-center mt-5">
                <button type="submit" class="btn btn-submit-orange shadow-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="register">Submit</span>
                    <span wire:loading wire:target="register">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Submitting...
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
