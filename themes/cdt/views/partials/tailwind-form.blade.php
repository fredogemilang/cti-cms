{{--
    Tailwind Form Partial — Reusable form renderer for CDT theme.
    Renders any Form model with Tailwind styling, Alpine.js custom validation,
    and captcha awareness (respects form's spam_protection settings).

    Usage:
      @include('cdt::partials.tailwind-form', ['form' => $allianceForm, 'entry' => $entry])
      @include('cdt::partials.tailwind-form', ['form' => $gatedForm])

    The $entry (CptEntry) is optional. When provided, vendor_solutions fields
    dynamically populate from vendor sub-products.
--}}

@php
    $theme = active_theme();
    $variant = $variant ?? 'light'; // 'light' or 'dark'
    $captchaProvider = $form->spam_protection['captcha_provider'] ?? 'none';
    $honeypot = $form->spam_protection['honeypot'] ?? false;

    // Build field config for Alpine validator
    $fieldConfig = $form->fields->map(fn($f) => [
        'field_id' => $f->field_id,
        'label'     => $f->localizedLabel(),
        'type'      => $f->type,
        'is_required' => (bool) $f->is_required,
    ])->toArray();

    // Map server-side validation errors keyed by field_id
    $fieldErrors = [];
    $errorsBag = $errors ?? new \Illuminate\Support\ViewErrorBag;
    if ($errorsBag->any()) {
        foreach ($errorsBag->getMessages() as $key => $msgs) {
            $fieldErrors[$key] = $msgs[0];
        }
    }

    // Check captcha keys are configured
    $captchaConfigured = match ($captchaProvider) {
        'recaptcha_v2', 'recaptcha_v3' => !empty(config('services.recaptcha.site_key')),
        'turnstile' => !empty(config('services.turnstile.site_key')),
        default => true, // 'none' doesn't need keys
    };

    // Dark variant styles
    $isDark = $variant === 'dark';
    $labelClass = $isDark ? 'text-white font-medium' : 'text-primary';
    $inputClass = $isDark
        ? 'w-full bg-white text-zinc-900 border-none px-4 py-3 rounded-md text-sm focus:ring-2 focus:ring-white/50 focus:outline-none placeholder-zinc-400'
        : 'w-full bg-transparent border-t-0 border-x-0 py-2.5 text-sm focus:outline-none transition-all text-zinc-800 placeholder-zinc-400/70';
    $selectClass = $isDark
        ? 'w-full bg-white text-zinc-900 border-none px-4 py-3 rounded-md text-sm focus:ring-2 focus:ring-white/50 focus:outline-none appearance-none'
        : 'w-full bg-transparent border-t-0 border-x-0 py-2.5 text-sm focus:outline-none transition-all text-zinc-800 placeholder-zinc-400/70 appearance-none cursor-pointer';
    $textareaClass = $isDark
        ? 'w-full bg-white text-zinc-900 border-none px-4 py-3 rounded-md text-sm focus:ring-2 focus:ring-white/50 focus:outline-none resize-none'
        : 'w-full bg-transparent border-t-0 border-x-0 py-2.5 text-sm focus:outline-none transition-all text-zinc-800 placeholder-zinc-400/70 resize-none';
    $btnClass = $isDark
        ? 'bg-white text-[#b82d25] px-12 py-3.5 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-zinc-100 transition shadow-md disabled:opacity-50'
        : 'w-full md:w-auto px-10 py-4 bg-primary text-white hover:bg-red-700 shadow-md hover:shadow-lg transition-all duration-300 rounded-xl text-xs font-bold uppercase tracking-wider';

    $errorBorderClass = $isDark ? 'ring-2 ring-yellow-300 border-yellow-300 shadow-lg' : 'border-b-2 border-red-500';
    $errorLabelClass = $isDark ? 'text-yellow-300 font-bold' : 'text-red-500';
    $errorTextClass = $isDark
        ? 'mt-2 text-xs font-semibold text-amber-200 bg-zinc-950/90 border border-amber-400/50 px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 shadow-xl w-fit'
        : 'mt-1.5 text-xs text-red-500 font-medium';
    $gridGap = $isDark ? 'gap-4' : 'gap-x-8 gap-y-6';
    $formTextAlign = $isDark ? 'text-left' : '';
    $consentLabelClass = $isDark ? 'text-xs font-normal text-white/90 leading-relaxed' : 'text-xs text-zinc-500';
    $consentCheckClass = $isDark ? 'mt-1 w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary' : 'mt-1 h-4 w-4 rounded border-zinc-300 text-primary focus:ring-primary';
@endphp

{{-- Success message --}}
@if(session('success') || session('form_success_message'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-start gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>{{ session('success') ?? session('form_success_message') }}</span>
  </div>
@endif

<form method="POST" action="{{ route('forms.submit', $form->slug) }}" class="space-y-6 {{ $formTextAlign }}"
      novalidate
      x-data="tailwindFormValidator({{ json_encode([
          'fields'       => $fieldConfig,
          'serverErrors' => $fieldErrors,
          'captcha'      => $captchaProvider,
      ]) }})"
      @submit.prevent="validateAndSubmit">

    @csrf

    {{-- Honeypot --}}
    @if($honeypot)
      <div style="display:none;"><input type="text" name="website_url" tabindex="-1" autocomplete="off"></div>
    @endif

    {{-- Top Error Alert Summary --}}
    <div x-show="Object.keys(errors).length > 0" x-transition
         class="{{ $isDark ? 'p-4 bg-zinc-950/90 border border-amber-400/50 rounded-xl text-amber-200 text-xs flex items-center gap-3 shadow-xl mb-6' : 'p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs flex items-center gap-3 shadow-sm mb-6' }}">
      <svg class="{{ $isDark ? 'w-5 h-5 text-amber-400' : 'w-5 h-5 text-red-500' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
      </svg>
      <span class="font-medium">Please fill in all required fields marked below.</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 {{ $gridGap }}">
        @foreach($form->fields as $field)
            @php
                $colClass = match($field->column_width) {
                    'full' => 'md:col-span-2',
                    'half' => '',
                    'third' => '',
                    default => '',
                };
                $hasError = isset($fieldErrors[$field->field_id]);
                $baseClass = 'w-full bg-transparent border-t-0 border-x-0 py-2.5 text-sm focus:outline-none transition-all text-zinc-800 placeholder-zinc-400/70';
                $defaultBorder = $hasError ? ($isDark ? 'ring-2 ring-yellow-300 border-yellow-300' : 'border-b-2 border-red-500') : ($isDark ? '' : 'border-b border-zinc-300 focus:border-primary');
            @endphp

            <div class="{{ $colClass }}">
                @if($field->type === 'gdpr' || $field->type === 'terms')
                    {{-- GDPR/Terms: label is the consent text itself, rendered below --}}
                @else
                    <label for="{{ $field->field_id }}"
                           class="{{ $isDark ? 'block text-xs font-bold uppercase tracking-wider mb-1.5' : 'block text-xs font-bold uppercase tracking-wider mb-1 transition-colors' }}"
                           :class="errors['{{ $field->field_id }}'] ? '{{ $errorLabelClass }}' : '{{ $labelClass }}'">
                        {{ $field->localizedLabel() }}
                        @if($field->is_required)
                          <span class="{{ $isDark ? 'text-yellow-300' : 'text-red-500' }} ml-0.5">*</span>
                        @endif
                    </label>
                @endif

                @if($field->type === 'select' || $field->type === 'vendor_solutions')
                    @php
                        $defaultOptions = [
                            ['label' => 'Cloud Services', 'value' => 'Cloud Services'],
                            ['label' => 'Consulting', 'value' => 'Consulting'],
                            ['label' => 'Security', 'value' => 'Security'],
                            ['label' => 'Other', 'value' => 'Other'],
                        ];

                        $selectedVal = null;
                        $isSolutionField = ($field->type === 'vendor_solutions' || str_contains(strtolower($field->getRawOriginal('label') ?? $field->label), 'solution') || str_contains(strtolower($field->field_id), 'solution'));

                        if ($isSolutionField && isset($entry)) {
                            // Check if $entry is a sub-product with parent vendor or parent entry
                            $parentVendorSlug = $entry->getMeta('parent_vendor');
                            $parentVendor = $entry->parentRelatedEntries()->first() 
                                ?? ($parentVendorSlug ? \App\Models\CptEntry::where('slug', $parentVendorSlug)->first() : null);

                            if ($parentVendor) {
                                // Sub-product page — fetch all sibling sub-products under the parent vendor
                                $subProducts = $parentVendor->relatedEntries('product_id')->published()->get();
                                if ($subProducts->isEmpty() && $parentVendor->slug) {
                                    $subProducts = \App\Models\CptEntry::published()
                                        ->whereHas('postType', fn($q) => $q->where('slug', 'tech-products'))
                                        ->where('meta->parent_vendor', $parentVendor->slug)
                                        ->get();
                                }
                                $selectedVal = $entry->title;
                            } else {
                                // Parent vendor page — fetch all sub-products under this vendor
                                $subProducts = $entry->relatedEntries('product_id')->published()->get();
                                if ($subProducts->isEmpty() && $entry->slug) {
                                    $subProducts = \App\Models\CptEntry::published()
                                        ->whereHas('postType', fn($q) => $q->where('slug', 'tech-products'))
                                        ->where('meta->parent_vendor', $entry->slug)
                                        ->get();
                                }
                            }

                            if (isset($subProducts) && $subProducts->isNotEmpty()) {
                                $selectOptions = $subProducts->map(fn($s) => ['label' => $s->title, 'value' => $s->title])->toArray();
                            } else {
                                $targetEntry = $parentVendor ?? $entry;
                                $selectOptions = array_merge(
                                    [['label' => $targetEntry->title, 'value' => $targetEntry->title]],
                                    $defaultOptions
                                );
                            }
                        } else {
                            $selectOptions = !empty($field->options) ? $field->options : $defaultOptions;
                        }

                        if (empty($selectOptions)) {
                            $selectOptions = $defaultOptions;
                        }
                    @endphp
                    <div class="relative">
                        <select name="{{ $field->field_id }}" id="{{ $field->field_id }}"
                                data-required="{{ $field->is_required ? 'true' : 'false' }}"
                                class="{{ $isDark ? $selectClass : $baseClass.' appearance-none cursor-pointer' }}"
                                :class="errors['{{ $field->field_id }}'] ? '{{ $errorBorderClass }}' : '{{ $defaultBorder }}'"
                                @change="delete errors['{{ $field->field_id }}']">
                            @if($field->placeholder)
                              <option value="" disabled {{ empty($selectedVal) ? 'selected' : '' }}>{{ $field->localizedPlaceholder() }}</option>
                            @endif
                            @foreach($selectOptions as $opt)
                              @php
                                $isSelected = !empty($selectedVal) && strtolower(trim((string)$selectedVal)) === strtolower(trim((string)$opt['value']));
                              @endphp
                              <option value="{{ $opt['value'] }}" {{ $isSelected ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-zinc-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                @elseif($field->type === 'textarea')
                    <textarea name="{{ $field->field_id }}" id="{{ $field->field_id }}" rows="4"
                              data-required="{{ $field->is_required ? 'true' : 'false' }}"
                              placeholder="{{ $field->localizedPlaceholder() }}"
                              class="{{ $isDark ? $textareaClass : $baseClass.' resize-none' }}"
                              :class="errors['{{ $field->field_id }}'] ? '{{ $errorBorderClass }}' : '{{ $defaultBorder }}'"
                              @input="delete errors['{{ $field->field_id }}']"></textarea>

                @elseif($field->type === 'gdpr' || $field->type === 'terms')
                    @php
                        $locale = app()->getLocale();
                        $defaultLocale = setting('default_locale', config('app.locale', 'en'));
                        $enConsentText = $field->type === 'gdpr'
                            ? ($field->advanced_settings['consent_text'] ?? $field->advanced_settings['privacy_content'] ?? 'I consent to having my personal data processed in accordance with the Privacy Policy.')
                            : ($field->advanced_settings['terms_text'] ?? 'I agree to the Terms & Conditions.');

                        // Check for translated consent text
                        if ($locale !== $defaultLocale) {
                            $trans = $field->getRawOriginal('translations');
                            $trans = is_string($trans) ? json_decode($trans, true) : ($trans ?? []);
                            $consentText = $trans[$locale]['consent_text'] ?? $enConsentText;
                        } else {
                            $consentText = $enConsentText;
                        }
                    @endphp
                    <div class="flex items-start gap-3 pt-2">
                      <input type="checkbox" name="{{ $field->field_id }}" id="{{ $field->field_id }}" value="1"
                             data-required="{{ $field->is_required ? 'true' : 'false' }}"
                             class="{{ $consentCheckClass }}"
                             :class="errors['{{ $field->field_id }}'] ? 'border-red-500 ring-2 ring-yellow-400' : 'border-zinc-300'"
                             @change="delete errors['{{ $field->field_id }}']">
                      <label for="{{ $field->field_id }}"
                             class="{{ $consentLabelClass }} cursor-pointer select-none leading-relaxed {{ $isDark ? '[&_a]:text-yellow-300 [&_a]:underline [&_a]:font-semibold hover:[&_a]:text-white' : '' }}"
                             :class="errors['{{ $field->field_id }}'] ? '{{ $errorLabelClass }}' : '{{ $isDark ? 'text-white/90' : 'text-zinc-500' }}'">
                        {!! $consentText !!}
                        @if($field->is_required)
                          <span class="{{ $isDark ? 'text-yellow-300' : 'text-red-500' }}">*</span>
                        @endif
                      </label>
                    </div>

                @else
                    <input type="{{ $field->type }}" name="{{ $field->field_id }}" id="{{ $field->field_id }}"
                           data-required="{{ $field->is_required ? 'true' : 'false' }}"
                           placeholder="{{ $field->localizedPlaceholder() }}"
                           class="{{ $isDark ? $inputClass : $baseClass }}"
                           :class="errors['{{ $field->field_id }}'] ? '{{ $errorBorderClass }}' : '{{ $defaultBorder }}'"
                           value="{{ old($field->field_id) }}"
                           @input="delete errors['{{ $field->field_id }}']">
                @endif

                {{-- Inline error --}}
                @if($isDark)
                  <div class="{{ $errorTextClass }}" x-show="errors['{{ $field->field_id }}']">
                    <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="errors['{{ $field->field_id }}']"></span>
                  </div>
                @else
                  <p class="{{ $errorTextClass }}"
                     x-show="errors['{{ $field->field_id }}']"
                     x-text="errors['{{ $field->field_id }}']"></p>
                @endif

                @if($hasError)
                  @if($isDark)
                    <div class="{{ $errorTextClass }}">
                      <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      <span>{{ $fieldErrors[$field->field_id] }}</span>
                    </div>
                  @else
                    <p class="{{ $errorTextClass }}">{{ $fieldErrors[$field->field_id] }}</p>
                  @endif
                @endif
            </div>
        @endforeach
    </div>

    {{-- Consent / GDPR / Terms fields are now CMS-managed via gdpr/terms field types.
         Add these fields in the form studio and they'll render with proper Tailwind styling. --}}

    {{-- CAPTCHA — respects form's spam_protection settings --}}
    @if($captchaProvider === 'none')
        {{-- Simple honeypot-only, no visible captcha. Skip captcha validation. --}}
    @elseif(!$captchaConfigured)
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-700 text-xs flex items-start gap-2">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <span><strong>CAPTCHA not configured.</strong> The {{ $captchaProvider }} site key is missing. Configure it in <code>config/services.php</code> or set captcha to "none" in form settings.</span>
        </div>
    @else
        @if(in_array($captchaProvider, ['recaptcha_v2', 'recaptcha_v3']))
            @if(!empty(config('services.recaptcha.site_key')))
                @if($captchaProvider === 'recaptcha_v2')
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    </div>
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                @else
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.querySelector('#g-recaptcha-response')?.closest('form');
                            if (form) {
                                form.addEventListener('submit', function(e) {
                                    const originalHandler = e.preventDefault; // let Alpine handle it
                                });
                            }
                        });
                    </script>
                @endif
            @endif
        @elseif($captchaProvider === 'turnstile')
            @if(!empty(config('services.turnstile.site_key')))
                <div class="mb-3">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="auto"></div>
                </div>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            @endif
        @endif

        {{-- Show captcha not completed error --}}
        <p class="text-xs text-red-500" x-show="errors['captcha']" x-text="errors['captcha']"></p>
    @endif

    {{-- Submit --}}
    <div class="{{ $isDark ? 'flex flex-col items-center' : 'pt-6' }}">
        <button type="submit" class="{{ $btnClass }}">
            {{ t('form.submit_' . $form->slug, $form->submit_button_text ?? 'Send Message') }}
        </button>
    </div>
</form>

{{-- Alpine.js validator (loaded once per page) --}}
@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('tailwindFormValidator', (config) => ({
        errors: {},

        init() {
            if (config.serverErrors && Object.keys(config.serverErrors).length) {
                this.errors = {...config.serverErrors};
            }
        },

        validateAndSubmit(e) {
            this.errors = {};
            let valid = true;

            // Validate form fields
            config.fields.forEach(f => {
                if (!f.is_required) return;
                const el = document.getElementById(f.field_id);
                if (!el) return;

                // Checkbox fields (gdpr, terms, checkbox) — check .checked
                if (el.type === 'checkbox') {
                    if (!el.checked) {
                        this.errors[f.field_id] = `${f.label} is required.`;
                        valid = false;
                    }
                    return;
                }

                const value = el.value?.trim() || '';
                if (!value) {
                    this.errors[f.field_id] = `${f.label} is required.`;
                    valid = false;
                } else if (f.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    this.errors[f.field_id] = 'Please enter a valid email address.';
                    valid = false;
                } else if (f.type === 'tel' && !/^[0-9\-\+\(\)\s]+$/.test(value)) {
                    this.errors[f.field_id] = 'Please enter a valid phone number.';
                    valid = false;
                }
            });

            // Captcha — only validate if provider is configured
            const captchaProvider = config.captcha || 'none';
            if (captchaProvider === 'recaptcha_v2') {
                const recaptchaResp = document.querySelector('[name="g-recaptcha-response"]');
                if (!recaptchaResp?.value) {
                    this.errors['captcha'] = 'Please complete the CAPTCHA verification.';
                    valid = false;
                }
            } else if (captchaProvider === 'turnstile') {
                const turnstileResp = document.querySelector('[name="cf-turnstile-response"]');
                if (!turnstileResp?.value) {
                    this.errors['captcha'] = 'Please complete the CAPTCHA verification.';
                    valid = false;
                }
            }
            // recaptcha_v3 is invisible — no user-facing validation needed
            // 'none' skips captcha entirely

            if (!valid) {
                const firstError = document.querySelector('[x-show^="errors["]');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            e.target.submit();
        }
    }));
});
</script>
@endpush
@endonce
