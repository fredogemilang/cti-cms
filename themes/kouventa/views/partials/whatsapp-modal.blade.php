@php
    $theme = active_theme();
    $assignments = setting("theme_{$theme->slug}_form_assignments", []);
    $waFormId = $assignments['whatsapp_chat_form'] ?? null;
    $waForm = $waFormId 
        ? \App\Models\Form::with('fields')->find($waFormId) 
        : \App\Models\Form::where('slug', 'whatsapp-chat')->where('is_active', true)->with('fields')->first();
@endphp

<div x-data="{
    waOpen: false,
    loading: false,
    success: false,
    errorMessage: '',
    errors: {},
    formData: {
        name: '',
        working_email: '',
        company: '',
        job_role: '',
        position: '',
        consent: false
    },
    toggle() {
        this.waOpen = !this.waOpen;
        if (this.waOpen) {
            this.errorMessage = '';
            this.errors = {};
            this.$nextTick(() => {
                const firstInput = this.$refs.waCard?.querySelector('input:not([type=hidden])');
                if (firstInput) firstInput.focus();
            });
        }
    },
    close() {
        this.waOpen = false;
    },
    async submitForm() {
        this.errorMessage = '';
        this.errors = {};

        if (!this.formData.name || !this.formData.name.trim()) {
            this.errors.name = '{{ t('kouventa.Please enter your name', 'Please enter your name') }}';
        }
        if (!this.formData.working_email || !this.formData.working_email.trim()) {
            this.errors.working_email = '{{ t('kouventa.Please enter your email', 'Please enter your email') }}';
        }
        if (!this.formData.company || !this.formData.company.trim()) {
            this.errors.company = '{{ t('kouventa.Please enter your company', 'Please enter your company') }}';
        }
        if (!this.formData.job_role) {
            this.errors.job_role = '{{ t('kouventa.Please select your job role', 'Please select your job role') }}';
        }
        if (!this.formData.position) {
            this.errors.position = '{{ t('kouventa.Please select your position', 'Please select your position') }}';
        }
        if (!this.formData.consent) {
            this.errors.consent = '{{ t('kouventa.Please accept the privacy policy', 'Please accept the privacy policy') }}';
        }

        if (Object.keys(this.errors).length > 0) {
            return;
        }

        this.loading = true;

        try {
            const payload = new FormData();
            payload.append('_token', '{{ csrf_token() }}');
            payload.append('name', this.formData.name);
            payload.append('working_email', this.formData.working_email);
            payload.append('company', this.formData.company);
            payload.append('job_role', this.formData.job_role);
            payload.append('position', this.formData.position);
            payload.append('consent', this.formData.consent ? '1' : '');

            const submitUrl = '{{ route('forms.submit.ajax', $waForm ? $waForm->slug : 'whatsapp-chat') }}';
            const response = await fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: payload
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                if (data.errors) {
                    for (const [key, msgs] of Object.entries(data.errors)) {
                        this.errors[key] = Array.isArray(msgs) ? msgs[0] : msgs;
                    }
                } else {
                    this.errorMessage = data.message || '{{ t('kouventa.An error occurred. Please try again.', 'An error occurred. Please try again.') }}';
                }
                this.loading = false;
                return;
            }

            // Success state
            this.success = true;
            this.loading = false;

            // Generate WhatsApp message
            const waNumber = '{{ function_exists('setting') ? setting('whatsapp_number', '62811849689') : '62811849689' }}';
            const msg = `Halo Kouventa,\nSaya ingin memulai percakapan.\n\n*Nama:* ${this.formData.name}\n*Email:* ${this.formData.working_email}\n*Perusahaan:* ${this.formData.company}\n*Jabatan:* ${this.formData.job_role}\n*Posisi:* ${this.formData.position}`;
            const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(msg)}`;

            // Open WhatsApp window
            setTimeout(() => {
                window.open(waUrl, '_blank', 'noopener,noreferrer');
            }, 600);

            // Auto-close after 3 seconds
            setTimeout(() => {
                this.waOpen = false;
                this.success = false;
                this.formData = { name: '', working_email: '', company: '', job_role: '', position: '', consent: false };
            }, 3000);

        } catch (err) {
            console.error('WhatsApp form submit error:', err);
            this.errorMessage = '{{ t('kouventa.Failed to submit. Please try again.', 'Failed to submit. Please try again.') }}';
            this.loading = false;
        }
    }
}" 
class="kv-wa-widget-container"
@keydown.escape.window="close()">

    <!-- Popup Card Form -->
    <div x-show="waOpen"
         x-ref="waCard"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95"
         @click.outside="close()"
         class="kv-wa-card"
         role="dialog"
         aria-modal="true"
         aria-labelledby="kv-wa-title">

        <!-- Header -->
        <div class="kv-wa-header">
            <div class="kv-wa-header-title-wrap">
                <div class="kv-wa-header-icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="#FFFFFF">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.59 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19.01L7.55 18.83L4.44 19.65L5.27 16.62L5.07 16.3C4.24 14.98 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67ZM8.53 7.33C8.37 7.33 8.1 7.39 7.87 7.64C7.65 7.89 7.02 8.48 7.02 9.69C7.02 10.9 7.9 12.06 8.02 12.22C8.15 12.39 9.75 14.86 12.21 15.92C12.8 16.17 13.25 16.33 13.61 16.44C14.2 16.63 14.74 16.6 15.17 16.54C15.64 16.47 16.63 15.94 16.84 15.36C17.04 14.78 17.04 14.28 16.98 14.18C16.92 14.07 16.76 14.01 16.51 13.89C16.27 13.76 15.06 13.17 14.84 13.08C14.61 13 14.45 12.96 14.29 13.21C14.12 13.46 13.65 14.01 13.5 14.18C13.36 14.34 13.22 14.36 12.97 14.24C12.73 14.12 11.95 13.86 11.02 13.03C10.29 12.38 9.8 11.58 9.66 11.34C9.52 11.09 9.64 10.96 9.77 10.83C9.88 10.72 10.02 10.54 10.14 10.39C10.26 10.25 10.3 10.15 10.38 9.98C10.46 9.82 10.42 9.68 10.36 9.55C10.3 9.43 9.82 8.24 9.61 7.75C9.42 7.28 9.21 7.34 9.06 7.33C8.91 7.33 8.73 7.33 8.53 7.33Z"/>
                    </svg>
                </div>
                <h3 id="kv-wa-title" class="kv-wa-header-title">{{ t('kouventa.Start a Conversation', 'Start a Conversation') }}</h3>
            </div>
            <button type="button" @click="close()" class="kv-wa-close-btn" aria-label="Close dialog">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body Form -->
        <div class="kv-wa-body">
            <!-- Success State -->
            <div x-show="success" class="kv-wa-success-state" style="display: none;">
                <div class="kv-wa-success-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h4 style="margin: 0; font-size: 18px; font-weight: 800; color: #071C33">{{ t('kouventa.Connecting to WhatsApp...', 'Connecting to WhatsApp...') }}</h4>
                <p style="margin: 0; font-size: 13px; color: #4A5866; max-width: 260px">
                    {{ t('kouventa.Thank you! We are opening a WhatsApp conversation with our solution specialist.', 'Thank you! We are opening a WhatsApp conversation with our solution specialist.') }}
                </p>
            </div>

            <!-- Active Form -->
            <form x-show="!success" @submit.prevent="submitForm()" novalidate>
                <div x-show="errorMessage" class="kv-wa-alert-error" x-text="errorMessage" style="margin-bottom: 12px; display: none;"></div>

                <div class="kv-wa-form-grid">
                    @if($waForm && $waForm->fields->isNotEmpty())
                        @foreach($waForm->fields as $field)
                            @php
                                $fieldId = $field->field_id;
                                $isFull = ($field->column_width === 'full' || $field->column_width === 12 || in_array($field->type, ['textarea', 'gdpr', 'terms', 'checkbox']));
                                $label = $field->localizedLabel() ?: $field->label;
                                $placeholder = $field->localizedPlaceholder() ?: ($field->placeholder ?? '');
                            @endphp

                            <div class="kv-wa-field-group {{ $isFull ? 'is-full' : '' }}">
                                @if(!in_array($field->type, ['gdpr', 'terms', 'hidden']))
                                    <label for="wa-{{ $fieldId }}" class="kv-wa-label">
                                        <span>{{ $label }}</span>
                                        @if($field->is_required)
                                            <span class="kv-wa-req" aria-hidden="true">*</span>
                                        @endif
                                    </label>
                                @endif

                                @if($field->type === 'select')
                                    <select id="wa-{{ $fieldId }}" 
                                            x-model="formData.{{ $fieldId }}"
                                            class="kv-wa-select"
                                            :class="{ 'has-error': errors.{{ $fieldId }} }">
                                        <option value="">{{ $placeholder ?: t('kouventa.Select an option', 'Select an option') }}</option>
                                        @foreach($field->options ?? [] as $opt)
                                            @php
                                                $val = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                                                $lbl = is_array($opt) ? ($opt['label'] ?? $val) : $opt;
                                            @endphp
                                            <option value="{{ $val }}">{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                @elseif(in_array($field->type, ['gdpr', 'terms']))
                                    @php
                                        $consentText = $field->advanced_settings['consent_text'] ?? $label;
                                    @endphp
                                    <div class="kv-wa-consent">
                                        <input id="wa-{{ $fieldId }}" 
                                               type="checkbox" 
                                               x-model="formData.{{ $fieldId }}"
                                               class="kv-wa-checkbox"
                                               :class="{ 'has-error': errors.{{ $fieldId }} }">
                                        <label for="wa-{{ $fieldId }}" class="kv-wa-consent-text">
                                            {!! $consentText !!}
                                        </label>
                                    </div>
                                @else
                                    <input id="wa-{{ $fieldId }}" 
                                           type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}"
                                           x-model="formData.{{ $fieldId }}"
                                           placeholder="{{ $placeholder }}"
                                           class="kv-wa-input"
                                           :class="{ 'has-error': errors.{{ $fieldId }} }">
                                @endif

                                <template x-if="errors.{{ $fieldId }}">
                                    <span class="kv-wa-field-error" x-text="errors.{{ $fieldId }}"></span>
                                </template>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback fields if DB record is missing -->
                        <div class="kv-wa-field-group is-full">
                            <label for="wa-name" class="kv-wa-label">Name <span class="kv-wa-req">*</span></label>
                            <input id="wa-name" type="text" x-model="formData.name" placeholder="John Doe" class="kv-wa-input" :class="{ 'has-error': errors.name }">
                            <template x-if="errors.name"><span class="kv-wa-field-error" x-text="errors.name"></span></template>
                        </div>
                        <div class="kv-wa-field-group">
                            <label for="wa-email" class="kv-wa-label">Working Email <span class="kv-wa-req">*</span></label>
                            <input id="wa-email" type="email" x-model="formData.working_email" placeholder="john.doe@corporate.com" class="kv-wa-input" :class="{ 'has-error': errors.working_email }">
                            <template x-if="errors.working_email"><span class="kv-wa-field-error" x-text="errors.working_email"></span></template>
                        </div>
                        <div class="kv-wa-field-group">
                            <label for="wa-company" class="kv-wa-label">Company <span class="kv-wa-req">*</span></label>
                            <input id="wa-company" type="text" x-model="formData.company" placeholder="PT. Corporate" class="kv-wa-input" :class="{ 'has-error': errors.company }">
                            <template x-if="errors.company"><span class="kv-wa-field-error" x-text="errors.company"></span></template>
                        </div>
                        <div class="kv-wa-field-group">
                            <label for="wa-job-role" class="kv-wa-label">Job Role <span class="kv-wa-req">*</span></label>
                            <select id="wa-job-role" x-model="formData.job_role" class="kv-wa-select" :class="{ 'has-error': errors.job_role }">
                                <option value="">Choose Job Role</option>
                                <option value="Director / C-Level">Director / C-Level</option>
                                <option value="VP / Head of Department">VP / Head of Department</option>
                                <option value="Manager / Team Lead">Manager / Team Lead</option>
                                <option value="Specialist / Senior Staff">Specialist / Senior Staff</option>
                                <option value="Staff / Officer">Staff / Officer</option>
                                <option value="Other">Other</option>
                            </select>
                            <template x-if="errors.job_role"><span class="kv-wa-field-error" x-text="errors.job_role"></span></template>
                        </div>
                        <div class="kv-wa-field-group">
                            <label for="wa-position" class="kv-wa-label">Position <span class="kv-wa-req">*</span></label>
                            <select id="wa-position" x-model="formData.position" class="kv-wa-select" :class="{ 'has-error': errors.position }">
                                <option value="">Choose Your Position</option>
                                <option value="IT / Engineering">IT / Engineering</option>
                                <option value="Customer Service / Support">Customer Service / Support</option>
                                <option value="Sales & Marketing">Sales & Marketing</option>
                                <option value="Operations">Operations</option>
                                <option value="Finance & Accounting">Finance & Accounting</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Other">Other</option>
                            </select>
                            <template x-if="errors.position"><span class="kv-wa-field-error" x-text="errors.position"></span></template>
                        </div>
                        <div class="kv-wa-field-group is-full">
                            <div class="kv-wa-consent">
                                <input id="wa-consent" type="checkbox" x-model="formData.consent" class="kv-wa-checkbox" :class="{ 'has-error': errors.consent }">
                                <label for="wa-consent" class="kv-wa-consent-text">
                                    By submitting your personal data in the required fields above, PT Helios Informatika Nusantara and its affiliates collect and proceed with such information. To learn more about our privacy practices, please refer to PT Helios Informatika Nusantara's <a href="/privacy-policy" target="_blank">Privacy Policy</a>.
                                </label>
                            </div>
                            <template x-if="errors.consent"><span class="kv-wa-field-error" x-text="errors.consent"></span></template>
                        </div>
                    @endif

                    <div class="kv-wa-field-group is-full" style="padding-top: 6px">
                        <button type="submit" class="kv-wa-btn-send" :disabled="loading">
                            <span x-show="!loading">{{ $waForm ? ($waForm->submit_button_text ?: t('kouventa.Send', 'Send')) : t('kouventa.Send', 'Send') }}</span>
                            <span x-show="loading" style="display: none; align-items: center; gap: 8px">
                                <svg class="animate-spin" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                                    <path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"/>
                                </svg>
                                <span>{{ t('kouventa.Sending...', 'Sending...') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sticky Floating WhatsApp Trigger Button -->
    <button type="button"
            @click="toggle()"
            class="kv-wa-sticky"
            aria-label="Chat on WhatsApp"
            :aria-expanded="waOpen.toString()">
        <span class="kv-wa-icon-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="#FFFFFF" aria-hidden="true">
                <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.59 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19.01L7.55 18.83L4.44 19.65L5.27 16.62L5.07 16.3C4.24 14.98 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67ZM8.53 7.33C8.37 7.33 8.1 7.39 7.87 7.64C7.65 7.89 7.02 8.48 7.02 9.69C7.02 10.9 7.9 12.06 8.02 12.22C8.15 12.39 9.75 14.86 12.21 15.92C12.8 16.17 13.25 16.33 13.61 16.44C14.2 16.63 14.74 16.6 15.17 16.54C15.64 16.47 16.63 15.94 16.84 15.36C17.04 14.78 17.04 14.28 16.98 14.18C16.92 14.07 16.76 14.01 16.51 13.89C16.27 13.76 15.06 13.17 14.84 13.08C14.61 13 14.45 12.96 14.29 13.21C14.12 13.46 13.65 14.01 13.5 14.18C13.36 14.34 13.22 14.36 12.97 14.24C12.73 14.12 11.95 13.86 11.02 13.03C10.29 12.38 9.8 11.58 9.66 11.34C9.52 11.09 9.64 10.96 9.77 10.83C9.88 10.72 10.02 10.54 10.14 10.39C10.26 10.25 10.3 10.15 10.38 9.98C10.46 9.82 10.42 9.68 10.36 9.55C10.3 9.43 9.82 8.24 9.61 7.75C9.42 7.28 9.21 7.34 9.06 7.33C8.91 7.33 8.73 7.33 8.53 7.33Z"/>
            </svg>
            <span class="kv-wa-ping"></span>
        </span>
        <span class="kv-wa-text">{{ t('kouventa.Chat on WhatsApp', 'Chat on WhatsApp') }}</span>
    </button>
</div>
