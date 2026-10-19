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
                    $isFullWidth = in_array($field->type, ['textarea', 'gdpr', 'terms', 'checkbox', 'radio', 'html', 'section']) || ((int) $field->column_width === 12);
                    $fieldId = $field->field_id;
                    $label = $field->localizedLabel() ?: $field->label;
                    $placeholder = $field->localizedPlaceholder() ?: ($field->placeholder ?? '');
                @endphp

                <div style="display: flex; flex-direction: column; gap: 8px; align-self: start; {{ $isFullWidth ? 'grid-column: 1 / -1;' : '' }}">
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
                        <style>
                            [x-cloak] { display: none !important; }
                            .kv-otp-wrap { display: flex; flex-direction: column; gap: 12px; width: 100%; position: relative; }
                            .kv-otp-input-row { display: flex; gap: 10px; align-items: center; width: 100%; }
                            .kv-otp-input-box { position: relative; flex: 1; min-width: 0; }
                            .kv-otp-input-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #25D366; display: inline-flex; align-items: center; justify-content: center; pointer-events: none; z-index: 2; }
                            .kv-otp-phone-field {
                                box-sizing: border-box !important;
                                width: 100% !important;
                                height: 52px !important;
                                padding: 0 40px 0 48px !important;
                                border-radius: 12px !important;
                                border: 1.5px solid #DCE3E0 !important;
                                background: #F7F9F8 !important;
                                font-size: 15px !important;
                                color: #0E1A24 !important;
                                font-weight: 600 !important;
                                letter-spacing: 0.01em !important;
                                transition: all 0.2s ease !important;
                                outline: none !important;
                                font-family: inherit !important;
                            }
                            .kv-otp-phone-field:focus:not(:read-only) {
                                border-color: #2BD46A !important;
                                background: #FFFFFF !important;
                                box-shadow: 0 0 0 3px rgba(43, 212, 106, 0.18) !important;
                            }
                            .kv-otp-phone-field.is-verified {
                                background: #F0FDF4 !important;
                                border-color: #86EFAC !important;
                                color: #15803D !important;
                            }
                            .kv-otp-verify-badge {
                                position: absolute;
                                right: 14px;
                                top: 50%;
                                transform: translateY(-50%);
                                display: inline-flex;
                                align-items: center;
                                justify-content: center;
                                width: 22px;
                                height: 22px;
                                border-radius: 50%;
                                background: #22C55E;
                                color: #FFFFFF;
                                z-index: 2;
                            }
                            .kv-otp-action-btn {
                                box-sizing: border-box !important;
                                height: 52px !important;
                                padding: 0 22px !important;
                                border-radius: 12px !important;
                                border: 0 !important;
                                font-size: 14.5px !important;
                                font-weight: 700 !important;
                                display: inline-flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                gap: 8px !important;
                                flex-shrink: 0 !important;
                                transition: all 0.2s ease !important;
                                white-space: nowrap !important;
                                cursor: pointer !important;
                                background: #071C33 !important;
                                color: #FFFFFF !important;
                                box-shadow: 0 4px 14px rgba(7, 28, 51, 0.12) !important;
                            }
                            .kv-otp-action-btn:hover:not(:disabled) {
                                background: #0E2A4D !important;
                            }
                            .kv-otp-action-btn.is-cooldown {
                                background: #F1F5F9 !important;
                                color: #475569 !important;
                                border: 1.5px solid #CBD5E1 !important;
                                cursor: not-allowed !important;
                                box-shadow: none !important;
                            }
                            .kv-otp-action-btn.is-loading {
                                opacity: 0.7 !important;
                                cursor: wait !important;
                            }
                            .kv-otp-action-btn.is-verified {
                                background: #DCFCE7 !important;
                                color: #15803D !important;
                                border: 1px solid #86EFAC !important;
                                cursor: default !important;
                                box-shadow: none !important;
                            }
                            .kv-otp-card {
                                display: flex;
                                flex-direction: column;
                                gap: 16px;
                                background: #FFFFFF;
                                border: 1.5px solid #E2E8F0;
                                border-radius: 16px;
                                padding: 20px;
                                box-shadow: 0 10px 25px -5px rgba(7, 28, 51, 0.08), 0 8px 10px -6px rgba(7, 28, 51, 0.04);
                                position: relative !important;
                                width: 100% !important;
                                box-sizing: border-box !important;
                                z-index: 10 !important;
                            }
                            .kv-otp-card-header {
                                display: flex !important;
                                align-items: center !important;
                                justify-content: space-between !important;
                                gap: 12px !important;
                                border-bottom: 1px solid #F1F5F9 !important;
                                padding-bottom: 14px !important;
                            }
                            .kv-otp-card-brand {
                                display: flex !important;
                                align-items: center !important;
                                gap: 10px !important;
                            }
                            .kv-otp-card-icon {
                                width: 36px !important;
                                height: 36px !important;
                                border-radius: 10px !important;
                                background: #DCFCE7 !important;
                                color: #16A34A !important;
                                display: inline-flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                flex-shrink: 0 !important;
                            }
                            .kv-otp-card-title {
                                font-size: 14px !important;
                                font-weight: 700 !important;
                                color: #0F172A !important;
                                line-height: 1.3 !important;
                            }
                            .kv-otp-card-subtitle {
                                font-size: 12px !important;
                                color: #64748B !important;
                                line-height: 1.3 !important;
                            }
                            .kv-otp-badge-active {
                                display: inline-flex !important;
                                align-items: center !important;
                                gap: 5px !important;
                                font-size: 11.5px !important;
                                font-weight: 600 !important;
                                color: #16A34A !important;
                                background: #F0FDF4 !important;
                                border: 1px solid #BBF7D0 !important;
                                padding: 3px 9px !important;
                                border-radius: 9999px !important;
                                white-space: nowrap !important;
                            }
                            .kv-otp-badge-dot {
                                width: 6px !important;
                                height: 6px !important;
                                border-radius: 50% !important;
                                background: #22C55E !important;
                                display: inline-block !important;
                            }
                            .kv-otp-digits-row {
                                display: flex !important;
                                justify-content: center !important;
                                gap: 10px !important;
                            }
                            .kv-otp-digit-box {
                                box-sizing: border-box !important;
                                width: 52px !important;
                                height: 54px !important;
                                border-radius: 12px !important;
                                border: 1.5px solid #CBD5E1 !important;
                                background: #F8FAFC !important;
                                font-size: 24px !important;
                                font-weight: 800 !important;
                                text-align: center !important;
                                color: #071C33 !important;
                                transition: all 0.15s ease !important;
                                outline: none !important;
                                font-variant-numeric: tabular-nums !important;
                                font-family: inherit !important;
                                padding: 0 !important;
                            }
                            .kv-otp-digit-box:focus {
                                border-color: #2BD46A !important;
                                background: #FFFFFF !important;
                                box-shadow: 0 0 0 3px rgba(43, 212, 106, 0.22) !important;
                            }
                            .kv-otp-confirm-btn {
                                box-sizing: border-box !important;
                                width: 100% !important;
                                height: 48px !important;
                                border-radius: 12px !important;
                                border: 0 !important;
                                background: #071C33 !important;
                                color: #FFFFFF !important;
                                font-size: 15px !important;
                                font-weight: 700 !important;
                                cursor: pointer !important;
                                display: flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                gap: 8px !important;
                                transition: all 0.2s ease !important;
                                box-shadow: 0 4px 12px rgba(7, 28, 51, 0.12) !important;
                            }
                            .kv-otp-confirm-btn:hover:not(:disabled) {
                                background: #0E2A4D !important;
                            }
                            .kv-otp-confirm-btn:disabled {
                                opacity: 0.45 !important;
                                cursor: not-allowed !important;
                                box-shadow: none !important;
                            }
                            .kv-otp-card-footer {
                                display: flex !important;
                                flex-direction: column !important;
                                gap: 8px !important;
                                font-size: 13px !important;
                                border-top: 1px solid #F1F5F9 !important;
                                padding-top: 12px !important;
                            }
                            .kv-otp-error-banner {
                                color: #DC2626 !important;
                                background: #FEF2F2 !important;
                                border: 1px solid #FECACA !important;
                                border-radius: 8px !important;
                                padding: 8px 12px !important;
                                font-weight: 600 !important;
                                display: flex;
                                align-items: center !important;
                                gap: 8px !important;
                            }
                            .kv-otp-info-banner {
                                color: #16A34A !important;
                                background: #F0FDF4 !important;
                                border: 1px solid #BBF7D0 !important;
                                border-radius: 8px !important;
                                padding: 8px 12px !important;
                                font-weight: 600 !important;
                                display: flex;
                                align-items: center !important;
                                gap: 8px !important;
                            }
                            .kv-otp-resend-row {
                                display: flex !important;
                                align-items: center !important;
                                justify-content: space-between !important;
                                color: #64748B !important;
                                font-size: 12.5px !important;
                                padding-top: 2px !important;
                            }
                            .kv-otp-resend-link {
                                background: none !important;
                                border: 0 !important;
                                padding: 0 !important;
                                font-size: 12.5px !important;
                                font-weight: 700 !important;
                                color: #16A34A !important;
                                text-decoration: underline !important;
                                cursor: pointer !important;
                            }
                            .kv-otp-verified-pill {
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                background: #F0FDF4;
                                border: 1.5px solid #86EFAC;
                                border-radius: 12px;
                                padding: 12px 16px;
                                font-size: 13.5px;
                                gap: 10px;
                            }
                            .kv-otp-verified-text {
                                display: flex !important;
                                align-items: center !important;
                                gap: 10px !important;
                                color: #15803D !important;
                                font-weight: 600 !important;
                            }
                            .kv-otp-verified-icon {
                                width: 24px !important;
                                height: 24px !important;
                                border-radius: 50% !important;
                                background: #22C55E !important;
                                color: #FFFFFF !important;
                                display: inline-flex !important;
                                align-items: center !important;
                                justify-content: center !important;
                                flex-shrink: 0 !important;
                            }
                            .kv-otp-change-btn {
                                background: none !important;
                                border: 0 !important;
                                padding: 0 !important;
                                font-size: 12.5px !important;
                                font-weight: 600 !important;
                                color: #64748B !important;
                                text-decoration: underline !important;
                                cursor: pointer !important;
                                white-space: nowrap !important;
                            }
                            .kv-otp-change-btn:hover {
                                color: #0F172A !important;
                            }
                        </style>

                        <div x-data="waPhoneVerifier({
                            initialPhone: '{{ old($fieldId) }}',
                            sendUrl: '{{ route('plugins.whatsapp-verifier.send-otp') }}',
                            verifyUrl: '{{ route('plugins.whatsapp-verifier.verify-otp') }}',
                            csrfToken: '{{ csrf_token() }}',
                            isRequired: {{ $field->is_required ? 'true' : 'false' }},
                            nameFieldId: 'f-full_name'
                        })" class="kv-otp-wrap">

                            <!-- Input & Action Button Group -->
                            <div class="kv-otp-input-row">
                                <div class="kv-otp-input-box">
                                    <!-- WhatsApp Icon Prefix inside Input -->
                                    <span class="kv-otp-input-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.23 8.23 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.06-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.24-8.23m4.52 11.66c-.19.53-1.06 1.04-1.46 1.09-.4.06-.9.08-1.48-.1-.35-.11-.8-.26-1.39-.51-2.45-1.07-4.04-3.56-4.16-3.73-.12-.16-1-1.33-1-2.54 0-1.2.63-1.79.85-2.04.22-.24.49-.3.66-.3.17 0 .34 0 .48.01.16.01.37-.06.57.43.21.5.73 1.78.79 1.91.06.14.1.29.02.46-.08.17-.12.28-.24.42-.12.15-.26.33-.37.45-.12.12-.24.25-.1.5.14.24.62 1.02 1.33 1.65.91.82 1.68 1.08 1.92 1.2.24.12.38.1.53-.06.15-.17.63-.73.8-1 .17-.26.34-.22.58-.13.24.09 1.51.71 1.77.84.26.13.43.19.49.3.06.11.06.64-.13 1.17z"/>
                                        </svg>
                                    </span>

                                    <input id="f-{{ $fieldId }}" name="{{ $fieldId }}" type="tel"
                                           x-model="phone"
                                           :readonly="isVerified"
                                           placeholder="{{ $placeholder ?: '0812 3456 7890' }}"
                                           {{ $field->is_required ? 'required' : '' }}
                                           class="kv-otp-phone-field"
                                           :class="{ 'is-verified': isVerified }"
                                           @input="onPhoneInput($event)">

                                    <!-- Verified Check Icon inside Input when locked -->
                                    <template x-if="isVerified">
                                        <span class="kv-otp-verify-badge">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"></path></svg>
                                        </span>
                                    </template>
                                </div>

                                <button type="button"
                                        @click="sendOtp()"
                                        :disabled="loading || cooldown > 0 || isVerified"
                                        class="kv-otp-action-btn"
                                        :class="{
                                            'is-verified': isVerified,
                                            'is-cooldown': cooldown > 0,
                                            'is-loading': loading
                                        }">
                                    <template x-if="isVerified">
                                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"></path></svg>
                                            <span>Verified</span>
                                        </span>
                                    </template>
                                    <template x-if="!isVerified && loading">
                                        <span style="display: inline-flex; align-items: center; gap: 8px;">
                                            <svg style="animation: spin 1s linear infinite;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path></svg>
                                            <span>Sending...</span>
                                        </span>
                                    </template>
                                    <template x-if="!isVerified && !loading && cooldown > 0">
                                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                            <span x-text="'Resend (' + cooldown + 's)'"></span>
                                        </span>
                                    </template>
                                    <template x-if="!isVerified && !loading && cooldown === 0">
                                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                                            <span x-text="showOtpInput ? 'Resend' : 'Verify'"></span>
                                        </span>
                                    </template>
                                </button>
                            </div>

                            <!-- Hidden Verification Token Input -->
                            <input type="hidden" name="_wa_verification_token" :value="token">
                            <input type="hidden" name="_wa_verified_phone" :value="verifiedPhone">

                            <!-- Professional Inline OTP Verification Card -->
                            <div x-show="showOtpInput && !isVerified" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 class="kv-otp-card">

                                <!-- Card Header -->
                                <div class="kv-otp-card-header">
                                    <div class="kv-otp-card-brand">
                                        <div class="kv-otp-card-icon">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.23 8.23 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.42 0-2.82-.37-4.06-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.23 8.24-8.23m4.52 11.66c-.19.53-1.06 1.04-1.46 1.09-.4.06-.9.08-1.48-.1-.35-.11-.8-.26-1.39-.51-2.45-1.07-4.04-3.56-4.16-3.73-.12-.16-1-1.33-1-2.54 0-1.2.63-1.79.85-2.04.22-.24.49-.3.66-.3.17 0 .34 0 .48.01.16.01.37-.06.57.43.21.5.73 1.78.79 1.91.06.14.1.29.02.46-.08.17-.12.28-.24.42-.12.15-.26.33-.37.45-.12.12-.24.25-.1.5.14.24.62 1.02 1.33 1.65.91.82 1.68 1.08 1.92 1.2.24.12.38.1.53-.06.15-.17.63-.73.8-1 .17-.26.34-.22.58-.13.24.09 1.51.71 1.77.84.26.13.43.19.49.3.06.11.06.64-.13 1.17z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="kv-otp-card-title">{{ t('kouventa.Verification Code', 'Verification Code') }}</div>
                                            <div class="kv-otp-card-subtitle">
                                                {{ t('kouventa.Enter 4-digit code sent to', 'Enter 4-digit code sent to') }} <span style="font-weight: 700; color: #071C33;" x-text="phone"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="kv-otp-badge-active">
                                        <span class="kv-otp-badge-dot"></span>
                                        <span>Sent</span>
                                    </span>
                                </div>

                                <!-- 4 Segmented Digit Input Grid & Action -->
                                <div style="display: flex; flex-direction: column; gap: 14px;">
                                    <div class="kv-otp-digits-row" @paste="handleDigitPaste($event)">
                                        <template x-for="(digit, idx) in digits" :key="idx">
                                            <input type="text"
                                                   inputmode="numeric"
                                                   pattern="[0-9]*"
                                                   maxlength="1"
                                                   :id="'wa-otp-' + idx"
                                                   :value="digits[idx]"
                                                   @input="handleDigitInput(idx, $event)"
                                                   @keydown="handleDigitKeydown(idx, $event)"
                                                   @focus="$event.target.select()"
                                                   autocomplete="one-time-code"
                                                   class="kv-otp-digit-box">
                                        </template>
                                    </div>

                                    <!-- Confirm OTP Button -->
                                    <button type="button"
                                            @click="verifyOtp()"
                                            :disabled="verifying || otpCode.length < 4"
                                            class="kv-otp-confirm-btn">
                                        <template x-if="verifying">
                                            <span style="display: inline-flex; align-items: center; gap: 8px;">
                                                <svg style="animation: spin 1s linear infinite;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10" stroke-linecap="round"></path></svg>
                                                <span>{{ t('kouventa.Verifying...', 'Verifying...') }}</span>
                                            </span>
                                        </template>
                                        <template x-if="!verifying">
                                            <span style="display: inline-flex; align-items: center; gap: 6px;">
                                                <span>{{ t('kouventa.Confirm OTP', 'Confirm OTP') }}</span>
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5l7 7-7 7"></path></svg>
                                            </span>
                                        </template>
                                    </button>
                                </div>

                                <!-- Feedback Row & Resend Link -->
                                <div class="kv-otp-card-footer">
                                    <div x-show="errorMessage" x-cloak class="kv-otp-error-banner">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        <span x-text="errorMessage"></span>
                                    </div>

                                    <div x-show="!errorMessage && infoMessage" class="kv-otp-info-banner">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                        <span x-text="infoMessage"></span>
                                    </div>

                                    <!-- Resend Link -->
                                    <div class="kv-otp-resend-row">
                                        <span>{{ t('kouventa.Didn\'t receive the code?', 'Didn\'t receive the code?') }}</span>
                                        <div>
                                            <template x-if="cooldown > 0">
                                                <span style="font-weight: 600; color: #475569;">
                                                    {{ t('kouventa.Resend in', 'Resend in') }} <span style="color: #071C33; font-weight: 700;" x-text="cooldown + 's'"></span>
                                                </span>
                                            </template>
                                            <template x-if="cooldown === 0">
                                                <button type="button" @click="sendOtp()" class="kv-otp-resend-link">
                                                    {{ t('kouventa.Resend via WhatsApp', 'Resend via WhatsApp') }}
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Verified Badge Card + Reset Action -->
                            <div x-show="isVerified" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-1"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 class="kv-otp-verified-pill">
                                <div class="kv-otp-verified-text">
                                    <span class="kv-otp-verified-icon">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"></path></svg>
                                    </span>
                                    <span>
                                        {{ t('kouventa.WhatsApp number verified successfully', 'WhatsApp number verified successfully') }}
                                    </span>
                                </div>
                                <button type="button" @click="resetVerification()" class="kv-otp-change-btn">
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
                    digits: ['', '', '', ''],
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

                    get otpCode() {
                        return this.digits.join('');
                    },

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
                            this.digits = ['', '', '', ''];
                        }
                    },

                    handleDigitInput(idx, event) {
                        const val = event.target.value.replace(/[^0-9]/g, '');
                        if (!val) {
                            const newDigits = [...this.digits];
                            newDigits[idx] = '';
                            this.digits = newDigits;
                            event.target.value = '';
                            return;
                        }

                        const char = val.slice(-1);
                        const newDigits = [...this.digits];
                        newDigits[idx] = char;
                        this.digits = newDigits;
                        event.target.value = char;

                        if (idx < 3) {
                            const nextInput = document.getElementById('wa-otp-' + (idx + 1));
                            if (nextInput) {
                                nextInput.focus();
                                nextInput.select();
                            }
                        }

                        if (this.otpCode.length === 4) {
                            this.verifyOtp();
                        }
                    },

                    handleDigitKeydown(idx, event) {
                        if (event.key === 'Backspace') {
                            if (!this.digits[idx] && idx > 0) {
                                const prevInput = document.getElementById('wa-otp-' + (idx - 1));
                                if (prevInput) {
                                    const newDigits = [...this.digits];
                                    newDigits[idx - 1] = '';
                                    this.digits = newDigits;
                                    prevInput.value = '';
                                    prevInput.focus();
                                    event.preventDefault();
                                }
                            } else {
                                const newDigits = [...this.digits];
                                newDigits[idx] = '';
                                this.digits = newDigits;
                            }
                        } else if (event.key === 'ArrowLeft' && idx > 0) {
                            const prevInput = document.getElementById('wa-otp-' + (idx - 1));
                            if (prevInput) {
                                prevInput.focus();
                                event.preventDefault();
                            }
                        } else if (event.key === 'ArrowRight' && idx < 3) {
                            const nextInput = document.getElementById('wa-otp-' + (idx + 1));
                            if (nextInput) {
                                nextInput.focus();
                                event.preventDefault();
                            }
                        }
                    },

                    handleDigitPaste(event) {
                        event.preventDefault();
                        const pasteData = (event.clipboardData || window.clipboardData).getData('text') || '';
                        const cleanDigits = pasteData.replace(/[^0-9]/g, '').slice(0, 4);
                        if (!cleanDigits) return;

                        const newDigits = ['', '', '', ''];
                        for (let i = 0; i < 4; i++) {
                            newDigits[i] = cleanDigits[i] || '';
                            const el = document.getElementById('wa-otp-' + i);
                            if (el) el.value = newDigits[i];
                        }
                        this.digits = newDigits;

                        const focusIdx = Math.min(cleanDigits.length, 3);
                        const targetInput = document.getElementById('wa-otp-' + focusIdx);
                        if (targetInput) targetInput.focus();

                        if (cleanDigits.length === 4) {
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
                                this.digits = ['', '', '', ''];
                                this.infoMessage = data.message || 'OTP sent to your WhatsApp.';
                                this.startCooldown(data.cooldown || 60);

                                setTimeout(() => {
                                    for (let i = 0; i < 4; i++) {
                                        const el = document.getElementById('wa-otp-' + i);
                                        if (el) el.value = '';
                                    }
                                    const firstInput = document.getElementById('wa-otp-0');
                                    if (firstInput) {
                                        firstInput.focus();
                                    }
                                }, 150);
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
                        if (this.otpCode.length < 4 || this.verifying) return;

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
                                    otp: this.otpCode
                                })
                            });

                            let data = await res.json();
                            if (data.success) {
                                this.token = data.token;
                                this.verifiedPhone = data.phone;
                                this.isVerified = true;
                                this.showOtpInput = false;
                                this.errorMessage = '';
                                this.infoMessage = data.message || 'Phone number verified!';
                                if (this.timer) clearInterval(this.timer);
                            } else {
                                this.errorMessage = data.message || 'Invalid verification code.';
                                this.digits = ['', '', '', ''];
                                setTimeout(() => {
                                    for (let i = 0; i < 4; i++) {
                                        const el = document.getElementById('wa-otp-' + i);
                                        if (el) el.value = '';
                                    }
                                    const firstInput = document.getElementById('wa-otp-0');
                                    if (firstInput) {
                                        firstInput.focus();
                                    }
                                }, 150);
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
                        this.digits = ['', '', '', ''];
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
