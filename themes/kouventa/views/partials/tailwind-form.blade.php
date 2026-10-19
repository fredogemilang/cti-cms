@props(['form'])

@if($form)
    <form action="{{ route('forms.submit', $form->slug) }}" method="POST" aria-label="{{ $form->name }}"
          style="background: #FFFFFF; border-radius: 24px; padding: 40px; display: flex; flex-direction: column; gap: 22px; box-shadow: 0 24px 60px rgba(7,28,51,0.14)">
        @csrf

        @if($form->spam_protection['honeypot'] ?? false)
            <div style="display:none;"><input type="text" name="website_url" tabindex="-1" autocomplete="off"></div>
        @endif

        @if(session('success_' . $form->slug) || session('success'))
            <div style="padding: 16px 20px; border-radius: 12px; background: #E8F9EE; border: 1px solid #A4E8BC; color: #0B5C28; font-size: 15px; font-weight: 600">
                {{ session('success_' . $form->slug) ?: session('success') }}
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div style="padding: 16px 20px; border-radius: 12px; background: #FDE8E8; border: 1px solid #F8B4B4; color: #9B1C1C; font-size: 14px">
                <p style="font-weight: 700; margin: 0 0 8px">{{ t('kouventa.Please fix the errors below:', 'Please fix the errors below:') }}</p>
                <ul style="margin: 0; padding-left: 20px; display: flex; flex-direction: column; gap: 4px">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px">
            @foreach($form->fields as $field)
                @php
                    $isFullWidth = in_array($field->type, ['textarea', 'gdpr', 'terms', 'checkbox', 'radio', 'html', 'section']) || ($field->column_width === 12);
                    $fieldId = $field->field_id;
                    $label = $field->localizedLabel() ?: $field->label;
                    $placeholder = $field->localizedPlaceholder() ?: ($field->placeholder ?? '');
                @endphp

                <div style="display: flex; flex-direction: column; gap: 8px; {{ $isFullWidth ? 'grid-column: 1 / -1;' : '' }}">
                    @if(!in_array($field->type, ['gdpr', 'terms', 'hidden']))
                        <label for="f-{{ $fieldId }}" style="font-size: 14px; font-weight: 700; color: #0E1A24">
                            {{ $label }}
                            @if($field->is_required)
                                <span aria-hidden="true" style="color: #C62828"> *</span>
                            @endif
                        </label>
                    @endif

                    @if($field->type === 'textarea')
                        <textarea id="f-{{ $fieldId }}" name="{{ $fieldId }}" rows="4" placeholder="{{ $placeholder }}"
                                  {{ $field->is_required ? 'required' : '' }}
                                  class="kv-in"
                                  style="box-sizing: border-box; width: 100%; min-height: 120px; padding: 14px 16px; border-radius: 12px; border: 1px solid #DCE3E0; background: #F7F9F8; font-size: 15px; color: #0E1A24; resize: vertical">{{ old($fieldId) }}</textarea>
                    @elseif($field->type === 'select')
                        <select id="f-{{ $fieldId }}" name="{{ $fieldId }}"
                                {{ $field->is_required ? 'required' : '' }}
                                class="kv-in"
                                style="box-sizing: border-box; width: 100%; height: 52px; padding: 0 16px; border-radius: 12px; border: 1px solid #DCE3E0; background: #F7F9F8; font-size: 15px; color: #0E1A24">
                            <option value="">{{ $placeholder ?: t('kouventa.Select an option', 'Select an option') }}</option>
                            @foreach($field->options ?? [] as $opt)
                                @php
                                    $val = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                                    $lbl = is_array($opt) ? ($opt['label'] ?? $val) : $opt;
                                @endphp
                                <option value="{{ $val }}" {{ old($fieldId) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    @elseif(in_array($field->type, ['gdpr', 'terms']))
                        @php
                            $consentText = $field->type === 'gdpr'
                                ? ($field->advanced_settings['consent_text'] ?? $field->advanced_settings['privacy_content'] ?? $label)
                                : ($field->advanced_settings['terms_text'] ?? $label);
                        @endphp
                        <div style="display: flex; gap: 12px; align-items: flex-start; padding-top: 4px">
                            <input id="f-{{ $fieldId }}" name="{{ $fieldId }}" type="checkbox" value="1"
                                   {{ $field->is_required ? 'required' : '' }}
                                   {{ old($fieldId) ? 'checked' : '' }}
                                   style="width: 20px; height: 20px; margin: 2px 0 0; accent-color: #13803F; flex-shrink: 0">
                            <label for="f-{{ $fieldId }}" style="font-size: 14px; line-height: 1.6; color: #4A5866">
                                {!! $consentText !!}
                            </label>
                        </div>
                    @elseif(is_plugin_active('whatsapp-verifier') && (in_array($field->type, ['tel', 'wa_verified_phone'], true) || $field->field_id === 'phone' || str_contains(strtolower($field->field_id), 'phone')))
                        <div x-data="waPhoneVerifier({
                            initialPhone: '{{ old($fieldId) }}',
                            sendUrl: '{{ route('plugins.whatsapp-verifier.send-otp') }}',
                            verifyUrl: '{{ route('plugins.whatsapp-verifier.verify-otp') }}',
                            csrfToken: '{{ csrf_token() }}',
                            isRequired: {{ $field->is_required ? 'true' : 'false' }},
                            nameFieldId: 'f-full_name'
                        })" class="kv-wa-verifier-wrap" style="display: flex; flex-direction: column; gap: 10px; width: 100%">
                            
                            <!-- Input & Verify Button Group -->
                            <div style="display: flex; gap: 10px; align-items: center; width: 100%">
                                <input id="f-{{ $fieldId }}" name="{{ $fieldId }}" type="tel"
                                       x-model="phone"
                                       :readonly="isVerified"
                                       placeholder="{{ $placeholder ?: 'WhatsApp number' }}"
                                       {{ $field->is_required ? 'required' : '' }}
                                       class="kv-in"
                                       @input="onPhoneInput($event)"
                                       style="box-sizing: border-box; flex: 1; min-width: 0; height: 52px; padding: 0 16px; border-radius: 12px; border: 1px solid #DCE3E0; background: #F7F9F8; font-size: 15px; color: #0E1A24">

                                <button type="button"
                                        @click="sendOtp()"
                                        :disabled="loading || cooldown > 0 || isVerified"
                                        :style="isVerified
                                            ? 'box-sizing: border-box; height: 52px; padding: 0 20px; border-radius: 12px; border: 0; background: #2BD46A; color: #06251A; font-size: 15px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; cursor: default; flex-shrink: 0'
                                            : 'box-sizing: border-box; height: 52px; padding: 0 24px; border-radius: 12px; border: 0; background: #071C33; color: #FFFFFF; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;' + (cooldown > 0 || loading ? ' opacity: 0.65; cursor: not-allowed;' : '')">
                                    <template x-if="isVerified">
                                        <span style="display: inline-flex; align-items: center; gap: 4px">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"></path></svg>
                                            <span>Verified</span>
                                        </span>
                                    </template>
                                    <template x-if="!isVerified && loading">
                                        <span>Sending...</span>
                                    </template>
                                    <template x-if="!isVerified && !loading && cooldown > 0">
                                        <span x-text="'Resend (' + cooldown + 's)'"></span>
                                    </template>
                                    <template x-if="!isVerified && !loading && cooldown === 0">
                                        <span>Verify</span>
                                    </template>
                                </button>
                            </div>

                            <!-- Hidden Verification Token Input -->
                            <input type="hidden" name="_wa_verification_token" :value="token">
                            <input type="hidden" name="_wa_verified_phone" :value="verifiedPhone">

                            <!-- Inline OTP Container -->
                            <div x-show="showOtpInput && !isVerified" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 style="background: #F4F7F5; border: 1px solid #D2E0D8; border-radius: 14px; padding: 16px 18px; display: flex; flex-direction: column; gap: 10px">
                                <div style="display: flex; justify-content: space-between; align-items: center">
                                    <label style="font-size: 13px; font-weight: 700; color: #0E1A24">{{ t('kouventa.Verification Code', 'Verification Code') }}</label>
                                    <span style="font-size: 12px; color: #5B6B78">{{ t('kouventa.Enter 4-digit code sent to WhatsApp', 'Enter 4-digit code sent to WhatsApp') }}</span>
                                </div>

                                <div style="display: flex; gap: 10px; align-items: center">
                                    <input type="text"
                                           x-model="otp"
                                           maxlength="4"
                                           placeholder="••••"
                                           inputmode="numeric"
                                           autocomplete="one-time-code"
                                           @input="onOtpInput($event)"
                                           @keydown.enter.prevent="verifyOtp()"
                                           style="box-sizing: border-box; width: 140px; height: 46px; text-align: center; font-size: 20px; font-weight: 800; letter-spacing: 6px; border-radius: 10px; border: 1px solid #C4D2CB; background: #FFFFFF; color: #0E1A24">

                                    <button type="button"
                                            @click="verifyOtp()"
                                            :disabled="verifying || otp.length < 4"
                                            style="height: 46px; padding: 0 20px; border-radius: 10px; border: 0; background: #2BD46A; color: #06251A; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s ease"
                                            :style="otp.length < 4 ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                                        <span x-text="verifying ? '{{ t('kouventa.Verifying...', 'Verifying...') }}' : '{{ t('kouventa.Confirm OTP', 'Confirm OTP') }}'"></span>
                                    </button>
                                </div>

                                <!-- Feedback messages -->
                                <div x-show="errorMessage" style="font-size: 13px; color: #C62828; font-weight: 600" x-text="errorMessage"></div>
                                <div x-show="infoMessage" style="font-size: 13px; color: #0B5C28; font-weight: 600" x-text="infoMessage"></div>
                            </div>

                            <!-- Verified Status Notice + Reset Option -->
                            <div x-show="isVerified" x-cloak style="display: flex; align-items: center; justify-content: space-between; font-size: 13px">
                                <span style="color: #0B5C28; font-weight: 600; display: inline-flex; align-items: center; gap: 4px">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12l5 5L20 7"></path></svg>
                                    <span>{{ t('kouventa.WhatsApp number verified', 'WhatsApp number verified') }}</span>
                                </span>
                                <button type="button" @click="resetVerification()" style="background: none; border: 0; padding: 0; font-size: 12px; color: #4A5866; text-decoration: underline; cursor: pointer">
                                    {{ t('kouventa.Change number', 'Change number') }}
                                </button>
                            </div>
                        </div>
                    @else
                        <input id="f-{{ $fieldId }}" name="{{ $fieldId }}"
                               type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}"
                               value="{{ old($fieldId) }}" placeholder="{{ $placeholder }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="kv-in"
                               style="box-sizing: border-box; width: 100%; height: 52px; padding: 0 16px; border-radius: 12px; border: 1px solid #DCE3E0; background: #F7F9F8; font-size: 15px; color: #0E1A24">
                    @endif

                    @if(isset($errors) && $errors->has($fieldId))
                        <span style="font-size: 12px; color: #C62828; font-weight: 600">{{ $errors->first($fieldId) }}</span>
                    @endif
                </div>
            @endforeach
        </div>

        <button type="submit"
                style="height: 56px; border: 0; border-radius: 12px; background: #2BD46A; color: #06251A; font-size: 17px; font-weight: 700; cursor: pointer; transition: all 0.2s ease"
                onmouseover="this.style.background='#22be5c'" onmouseout="this.style.background='#2BD46A'">
            {{ $form->submit_button_text ?: t('kouventa.Submit', 'Submit') }}
        </button>
    </form>

    @if(is_plugin_active('whatsapp-verifier'))
        <script>
        (function() {
            function initWaPhoneVerifier() {
                if (!window.Alpine) return;
                if (typeof window.waPhoneVerifierRegistered !== 'undefined') return;
                window.waPhoneVerifierRegistered = true;

                Alpine.data('waPhoneVerifier', (config) => ({
                    phone: config.initialPhone || '',
                    otp: '',
                    token: '',
                    verifiedPhone: '',
                    loading: false,
                    verifying: false,
                    isVerified: false,
                    showOtpInput: false,
                    cooldown: 0,
                    timer: null,
                    errorMessage: '',
                    infoMessage: '',

                    onPhoneInput(e) {
                        let val = e.target.value.replace(/[^0-9+]/g, '');
                        if (val.startsWith('0')) {
                            val = '62' + val.substring(1);
                        } else if (val.startsWith('8')) {
                            val = '628' + val.substring(1);
                        }
                        this.phone = val;
                        if (this.isVerified && this.phone !== this.verifiedPhone) {
                            this.isVerified = false;
                            this.token = '';
                            this.verifiedPhone = '';
                        }
                    },

                    onOtpInput(e) {
                        this.otp = e.target.value.replace(/[^0-9]/g, '').slice(0, 4);
                        if (this.otp.length === 4) {
                            this.verifyOtp();
                        }
                    },

                    async sendOtp() {
                        this.errorMessage = '';
                        this.infoMessage = '';

                        let raw = this.phone.trim();
                        if (!raw || raw.length < 10) {
                            this.errorMessage = 'Please enter a valid phone number (min. 10 digits).';
                            return;
                        }

                        let nameInput = document.getElementById(config.nameFieldId) || document.querySelector('input[name="full_name"]') || document.querySelector('input[name="name"]');
                        let nameVal = nameInput ? nameInput.value : 'Customer';

                        this.loading = true;
                        try {
                            let res = await fetch(config.sendUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': config.csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    phone: raw,
                                    name: nameVal
                                })
                            });

                            let data = await res.json();
                            if (data.success) {
                                this.showOtpInput = true;
                                this.infoMessage = data.message || 'OTP sent to your WhatsApp.';
                                this.startCooldown(data.cooldown || 60);
                            } else {
                                this.errorMessage = data.message || 'Failed to send OTP.';
                            }
                        } catch (err) {
                            this.errorMessage = 'Network error while sending OTP. Please try again.';
                        } finally {
                            this.loading = false;
                        }
                    },

                    async verifyOtp() {
                        if (this.otp.length < 4 || this.verifying) return;

                        this.errorMessage = '';
                        this.infoMessage = '';
                        this.verifying = true;

                        try {
                            let res = await fetch(config.verifyUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': config.csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    phone: this.phone,
                                    otp: this.otp
                                })
                            });

                            let data = await res.json();
                            if (data.success) {
                                this.token = data.token;
                                this.verifiedPhone = data.phone;
                                this.isVerified = true;
                                this.showOtpInput = false;
                                this.infoMessage = data.message || 'Phone number verified!';
                                if (this.timer) clearInterval(this.timer);
                            } else {
                                this.errorMessage = data.message || 'Invalid verification code.';
                            }
                        } catch (err) {
                            this.errorMessage = 'Network error while verifying OTP.';
                        } finally {
                            this.verifying = false;
                        }
                    },

                    startCooldown(seconds) {
                        this.cooldown = seconds;
                        if (this.timer) clearInterval(this.timer);
                        this.timer = setInterval(() => {
                            this.cooldown--;
                            if (this.cooldown <= 0) {
                                clearInterval(this.timer);
                                this.cooldown = 0;
                            }
                        }, 1000);
                    },

                    resetVerification() {
                        this.isVerified = false;
                        this.token = '';
                        this.verifiedPhone = '';
                        this.otp = '';
                        this.showOtpInput = false;
                        this.errorMessage = '';
                        this.infoMessage = '';
                    }
                }));
            }

            if (window.Alpine) {
                initWaPhoneVerifier();
            } else {
                document.addEventListener('alpine:init', initWaPhoneVerifier);
            }
        })();
        </script>
    @endif
@endif
