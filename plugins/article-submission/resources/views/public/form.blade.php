@extends('iccom::layouts.app')

@section('title', 'Publish Your Article - ' . setting('site_name', 'iCCom'))

@section('content')
    <!-- Page Hero -->
    <header class="hero-section d-flex align-items-center position-relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h1 class="display-4 fw-bold mb-3">Fill This Form<br>and be Contributor<br>of iCCom Articles!</h1>
                    <p class="lead mb-4 fw-normal">As an iCCom member, you can contribute to publish articles related to cloud technology.</p>
                    <p class="lead mb-4 fw-normal">Please fill in this form to submit your article.</p>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ asset('themes/iccom/assets/publish-front-hero.png') }}" alt="Contributor Hero" class="img-fluid hero-illustration">
                </div>
            </div>
        </div>
    </header>

    <style>
        .form-control-flushed.is-invalid,
        .form-select.is-invalid {
            border-bottom-color: #EF4444 !important;
        }
        .btn.is-invalid {
            border-color: #EF4444 !important;
        }
    </style>

    <!-- Form Section -->
    <section class="publish-form-section py-5">
        <div class="container">
            <div class="publish-form-card bg-white rounded-4 p-5 shadow-lg mx-auto">
                @if(session('error'))
                <div class="alert alert-danger mb-4">{{ session('error') }}</div>
                @endif
                
                @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                
                <form
                    action="{{ route('article-submission.submit') }}"
                    method="POST"
                    enctype="multipart/form-data"
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

                            // Job Title validation
                            let jobTitleVal = this.$el.querySelector('[name=job_title]')?.value || '';
                            if (!jobTitleVal.trim()) {
                                this.errors.job_title = 'Job Title is required.';
                                isValid = false;
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

                            // Article File validation
                            let articleFileVal = this.$el.querySelector('[name=article_file]')?.value || '';
                            if (!articleFileVal) {
                                this.errors.article_file = 'Article file is required.';
                                isValid = false;
                            }

                            // LinkedIn validation (optional, but if filled must be valid link)
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
                                <option value="">Job Level</option>
                                <option value="Entry Level" {{ old('job_level') == 'Entry Level' ? 'selected' : '' }}>Entry Level</option>
                                <option value="Mid Level" {{ old('job_level') == 'Mid Level' ? 'selected' : '' }}>Mid Level</option>
                                <option value="Senior Level" {{ old('job_level') == 'Senior Level' ? 'selected' : '' }}>Senior Level</option>
                                <option value="Manager" {{ old('job_level') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                <option value="Director" {{ old('job_level') == 'Director' ? 'selected' : '' }}>Director</option>
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
                                :class="errors.job_title ? 'is-invalid' : ''" placeholder="Job Title" value="{{ old('job_title') }}" required>
                            <template x-if="errors.job_title">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.job_title"></div>
                            </template>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Linkedin Account:</label>
                            <input type="text" name="linkedin" class="form-control form-control-flushed @error('linkedin') is-invalid @enderror"
                                :class="errors.linkedin ? 'is-invalid' : ''"
                                placeholder="Linkedin Account" value="{{ old('linkedin') }}">
                            <template x-if="errors.linkedin">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.linkedin"></div>
                            </template>
                        </div>

                        <!-- Row 3 -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Phone Number: <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control form-control-flushed @error('phone') is-invalid @enderror"
                                :class="errors.phone ? 'is-invalid' : ''"
                                placeholder="Phone Number" value="{{ old('phone') }}" required>
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
                            <label class="form-label fw-bold small">Upload Your Article: <span class="text-danger">*</span></label>
                            <div class="upload-btn-wrapper">
                                <button type="button"
                                    class="btn btn-outline-warning w-100 text-start d-flex justify-content-between align-items-center @error('article_file') is-invalid @enderror"
                                    :class="errors.article_file ? 'is-invalid' : ''"
                                    onclick="document.getElementById('article_file').click()">
                                    <span class="small text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="me-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span id="file-name">Upload PDF Format</span>
                                    </span>
                                </button>
                                <input type="file" name="article_file" id="article_file" accept=".pdf" class="d-none" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'Upload PDF Format'; delete errors.article_file;" required />
                            </div>
                            <template x-if="errors.article_file">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.article_file"></div>
                            </template>
                        </div>

                        <!-- Row 4 -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Highest Education Level:</label>
                            <select name="education_level" class="form-select form-control-flushed">
                                <option value="">Highest Education Level</option>
                                <option value="High School" {{ old('education_level') == 'High School' ? 'selected' : '' }}>High School</option>
                                <option value="Diploma" {{ old('education_level') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                                <option value="Bachelor" {{ old('education_level') == 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
                                <option value="Master" {{ old('education_level') == 'Master' ? 'selected' : '' }}>Master</option>
                                <option value="Doctorate" {{ old('education_level') == 'Doctorate' ? 'selected' : '' }}>Doctorate</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Industry: <span class="text-danger">*</span></label>
                            <select name="industry" class="form-select form-control-flushed @error('industry') is-invalid @enderror"
                                :class="errors.industry ? 'is-invalid' : ''" required>
                                <option value="">Industry</option>
                                <option value="Technology" {{ old('industry') == 'Technology' ? 'selected' : '' }}>Technology</option>
                                <option value="Finance" {{ old('industry') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Healthcare" {{ old('industry') == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                <option value="Education" {{ old('industry') == 'Education' ? 'selected' : '' }}>Education</option>
                                <option value="Manufacturing" {{ old('industry') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="Other" {{ old('industry') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <template x-if="errors.industry">
                                <div class="text-danger small mt-1" style="font-size:0.75rem;" x-text="errors.industry"></div>
                            </template>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-12 text-center mt-5">
                            <button type="submit" class="btn btn-cta btn-warning text-white fw-bold rounded-pill px-5 py-2 shadow">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Guidelines Section -->
    <section class="guidelines-section py-5">
        <div class="container">
            <h5 class="fw-bold mb-4">Blog Article Guidelines</h5>
            <ol class="guideline-list small text-muted">
                <li class="mb-2">The article should be original and have never been published on the internet before.</li>
                <li class="mb-2">There is no minimum length for any article. However, we recommend 750-1,500 words for a greater depth of the topic.</li>
                <li class="mb-2">The article must provide benefits or new knowledge to community members.</li>
                <li class="mb-2">The article must be well-written and grammatically correct (in English or Bahasa Indonesia).</li>
                <li class="mb-2">To be more approachable to the readers, we recommend writing an article in a friendly, smart-casual tonality.</li>
                <li class="mb-2">Hard selling or marketing of a specific product/brand is disallowed, unless the product is related to the topic being addressed in the article.</li>
                <li class="mb-2">No discrimination or offense against a specific product/brand.</li>
                <li class="mb-2">The community committee has the authority to choose which articles are published; and take note that not all submitted articles will be chosen.</li>
                <li class="mb-2">The community committee reserves the right to change some of the article's content (if required).</li>
                <li class="mb-2">The community committee will inform the members whether or not their article will be published.</li>
                <li class="mb-2">Please include a brief biography if you would want this to feature with your article (a close-up headshot of yourself is also acceptable).</li>
                <li class="mb-2">All articles contributed to the community will become our copyright. However, the article will be made available under the author's name.</li>
            </ol>
        </div>
    </section>
@endsection

@push('livewire-styles')
    @livewireStyles
@endpush

@push('livewire-scripts')
    @livewireScripts
@endpush
