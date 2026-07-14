@extends('iccom::layouts.app')

@section('title', 'Become a Member - ' . setting('site_name', 'iCCom'))

@section('content')
    <div style="height: 100px;"></div> <!-- Spacer logic from template .spacer css might need to be checked, using inline style for safety -->

    <style>
        .form-control-flushed.is-invalid,
        .form-select.is-invalid {
            border-bottom-color: #EF4444 !important;
        }
    </style>

    <!-- Form Section (Overlapping) -->
    <section class="membership-form-section pb-5 position-relative">
        <div class="container">
            <div class="text-white text-center" data-aos="fade-down">
                <h1 class="display-4 fw-bold mb-3">Join iCCom Membership</h1>
                <p class="lead mb-2 text-white-50">Sign up for free and take part in social and educational activities
                    designed with you in mind.</p>
                <p class="lead mb-2 text-white-50">Discover new insights at our events, learn from member-written
                    articles,
                    and connect with people who share the same passion for cloud.</p>
                <h2 class="display-5 fw-bold mt-4">Let's #UnitedatCloud</h2>
            </div>

            <div class="publish-form-card bg-white rounded-4 p-5 shadow-lg mx-auto mt-5" data-aos="fade-up" data-aos-delay="100">
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    action="{{ route('membership.store') }}"
                    method="POST"
                    x-data="{
                        errors: {},
                        validate() {
                            this.errors = {};
                            let isValid = true;
                            
                            // Name validation
                            let nameVal = this.$el.querySelector('[name=name]')?.value || '';
                            if (!nameVal.trim()) {
                                this.errors.name = 'Name is required.';
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
                            let phoneVal = this.$el.querySelector('[name=phone]')?.value || '';
                            if (!phoneVal.trim()) {
                                this.errors.phone = 'Phone Number is required.';
                                isValid = false;
                            } else {
                                let cleanPhone = phoneVal.replace(/\D/g, '');
                                if (cleanPhone.length < 9 || cleanPhone.length > 13) {
                                    this.errors.phone = 'Phone Number must be between 9 and 13 digits.';
                                    isValid = false;
                                }
                            }

                            // Job Level validation
                            let jobLevelVal = this.$el.querySelector('[name=job_level]')?.value || '';
                            if (!jobLevelVal) {
                                this.errors.job_level = 'Job Level is required.';
                                isValid = false;
                            }

                            // Job Title validation
                            let jobTitleVal = this.$el.querySelector('[name=job_title]')?.value || '';
                            if (!jobTitleVal.trim()) {
                                this.errors.job_title = 'Job Title is required.';
                                isValid = false;
                            }

                            // Domicile validation
                            let domicileVal = this.$el.querySelector('[name=domicile]')?.value || '';
                            if (!domicileVal) {
                                this.errors.domicile = 'Domicile is required.';
                                isValid = false;
                            } else if (domicileVal === 'Other') {
                                let domicileOtherVal = this.$el.querySelector('[name=domicile_other]')?.value || '';
                                if (!domicileOtherVal.trim()) {
                                    this.errors.domicile = 'Please specify your domicile.';
                                    isValid = false;
                                }
                            }

                            // Institution validation
                            let institutionVal = this.$el.querySelector('[name=institution]')?.value || '';
                            if (!institutionVal.trim()) {
                                this.errors.institution = 'Institution/Company is required.';
                                isValid = false;
                            }

                            // Industry validation
                            let industryVal = this.$el.querySelector('[name=industry]')?.value || '';
                            if (!industryVal) {
                                this.errors.industry = 'Industry is required.';
                                isValid = false;
                            }

                            // LinkedIn validation
                            let linkedinVal = this.$el.querySelector('[name=linkedin]')?.value || '';
                            if (linkedinVal.trim() && !/^(https?:\/\/)?(www\.)?linkedin\.com\/.*$/i.test(linkedinVal)) {
                                this.errors.linkedin = 'LinkedIn account must be a valid LinkedIn URL.';
                                isValid = false;
                            }
                            
                            return isValid;
                        },
                        clearError(name) {
                            if (this.errors[name]) {
                                delete this.errors[name];
                            }
                        }
                    }"
                    @submit="if (!validate()) { $event.preventDefault(); $event.stopPropagation(); return false; }"
                    @input="clearError($event.target.name)"
                    @change="clearError($event.target.name)"
                    novalidate
                >
                    @csrf
                    <div class="row g-4">
                        <!-- Row 1 -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Name: <span class="text-danger">*</span></label>
                            <input type="text" name="name"
                                class="form-control form-control-flushed @error('name') is-invalid @enderror"
                                :class="errors.name ? 'is-invalid' : ''"
                                placeholder="Name" value="{{ old('name') }}" required>
                            <template x-if="errors.name">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.name"></div>
                            </template>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Job Level: <span class="text-danger">*</span></label>
                            <select name="job_level" class="form-select form-control-flushed @error('job_level') is-invalid @enderror"
                                :class="errors.job_level ? 'is-invalid' : ''" required>
                                <option value="">Select Job Level</option>
                                <option value="Entry Level" {{ old('job_level') == 'Entry Level' ? 'selected' : '' }}>Entry Level</option>
                                <option value="Mid Level" {{ old('job_level') == 'Mid Level' ? 'selected' : '' }}>Mid Level</option>
                                <option value="Senior Level" {{ old('job_level') == 'Senior Level' ? 'selected' : '' }}>Senior Level</option>
                                <option value="Manager" {{ old('job_level') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                <option value="Director" {{ old('job_level') == 'Director' ? 'selected' : '' }}>Director</option>
                                <option value="C-Level" {{ old('job_level') == 'C-Level' ? 'selected' : '' }}>C-Level</option>
                            </select>
                            <template x-if="errors.job_level">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.job_level"></div>
                            </template>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Domicile: <span class="text-danger">*</span></label>
                            @livewire('domicile-select', [
                                'fieldName' => 'domicile',
                                'oldValue' => old('domicile'),
                                'oldOtherValue' => old('domicile_other'),
                                'hasError' => $errors->has('domicile'),
                                'errorMessage' => $errors->first('domicile')
                            ])
                        </div>

                        <!-- Row 2 -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">E-mail: <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                class="form-control form-control-flushed @error('email') is-invalid @enderror"
                                :class="errors.email ? 'is-invalid' : ''"
                                placeholder="Email" value="{{ old('email') }}" required>
                            <template x-if="errors.email">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.email"></div>
                            </template>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Job Title: <span class="text-danger">*</span></label>
                            <input type="text" name="job_title" class="form-control form-control-flushed @error('job_title') is-invalid @enderror"
                                :class="errors.job_title ? 'is-invalid' : ''" placeholder="e.g. Software Engineer" value="{{ old('job_title') }}" required>
                            <template x-if="errors.job_title">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.job_title"></div>
                            </template>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Linkedin Account:</label>
                            <input type="text" name="linkedin" class="form-control form-control-flushed"
                                :class="errors.linkedin ? 'is-invalid' : ''"
                                placeholder="linkedin.com/in/username" value="{{ old('linkedin') }}">
                            <template x-if="errors.linkedin">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.linkedin"></div>
                            </template>
                        </div>

                        <!-- Row 3 -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Phone Number: <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control form-control-flushed @error('phone') is-invalid @enderror"
                                :class="errors.phone ? 'is-invalid' : ''" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" required>
                            <template x-if="errors.phone">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.phone"></div>
                            </template>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Institution/Company: <span class="text-danger">*</span></label>
                            <input type="text" name="institution" class="form-control form-control-flushed @error('institution') is-invalid @enderror"
                                :class="errors.institution ? 'is-invalid' : ''" placeholder="Institution/Company" value="{{ old('institution') }}" required>
                            <template x-if="errors.institution">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.institution"></div>
                            </template>
                        </div>
                        <div class="col-md-4">
                            <!-- Empty Column -->
                        </div>

                        <!-- Row 4 -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Highest Education Level:</label>
                            <select name="education_level" class="form-select form-control-flushed">
                                <option value="">Select Education Level</option>
                                <option value="SMA/SMK" {{ old('education_level') == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                <option value="D3" {{ old('education_level') == 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="S1" {{ old('education_level') == 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2" {{ old('education_level') == 'S2' ? 'selected' : '' }}>S2</option>
                                <option value="S3" {{ old('education_level') == 'S3' ? 'selected' : '' }}>S3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Industry: <span class="text-danger">*</span></label>
                            <select name="industry" class="form-select form-control-flushed @error('industry') is-invalid @enderror"
                                :class="errors.industry ? 'is-invalid' : ''" required>
                                <option value="">Select Industry</option>
                                <option value="Technology" {{ old('industry') == 'Technology' ? 'selected' : '' }}>Technology</option>
                                <option value="Finance" {{ old('industry') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Healthcare" {{ old('industry') == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                <option value="Education" {{ old('industry') == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Government" {{ old('industry') == 'Government' ? 'selected' : '' }}>Government</option>
                                <option value="Retail" {{ old('industry') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                <option value="Manufacturing" {{ old('industry') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="Consulting" {{ old('industry') == 'Consulting' ? 'selected' : '' }}>Consulting</option>
                                <option value="Other" {{ old('industry') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <template x-if="errors.industry">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.industry"></div>
                            </template>
                        </div>
                        <div class="col-md-4">
                            <!-- Empty Column -->
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 text-center mt-5">
                            <button type="submit"
                                class="btn btn-cta btn-warning text-white fw-bold rounded-pill px-5 py-2 shadow">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('livewire-styles')
    @livewireStyles
@endpush

@push('livewire-scripts')
    @livewireScripts
@endpush
