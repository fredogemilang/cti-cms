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

<?php
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
?>

{{-- Success message --}}
@if(session('success') || session('form_success_message'))
  <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-start gap-3">
    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>{{ session('success') ?? session('form_success_message') }}</span>
  </div>
@endif

<div x-data="tailwindFormValidator({{ json_encode([
        'fields'       => $fieldConfig,
        'serverErrors' => $fieldErrors,
        'captcha'      => $captchaProvider,
        'ajaxUrl'      => route('forms.submit.ajax', $form->slug),
    ]) }})">

    <form method="POST" action="{{ route('forms.submit', $form->slug) }}" class="elementor-form space-y-8 {{ $formTextAlign }}"
          novalidate
          @submit.prevent="validateAndSubmit">

    @csrf

    {{-- Form Success Alert --}}
    <div x-show="isSubmitted" x-transition
         id="elementor-message-success"
         class="elementor-message elementor-message-success elementor-help-inline bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center mb-8"
         role="alert">
        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div class="text-lg font-bold text-emerald-900 mb-2">Thank you!</div>
        <p class="text-emerald-700 text-sm font-light leading-relaxed max-w-md mx-auto" x-text="successMessage"></p>
    </div>

    {{-- Auto-Injected Attribution Metadata Fields --}}
    <input type="hidden" name="_submission_page" value="{{ url()->current() }}">
    <input type="hidden" name="_attribution" id="_attribution_{{ $form->id }}" value="">
    <script>
    (function() {
        var el = document.getElementById('_attribution_{{ $form->id }}');
        if (el) {
            var m = document.cookie.match(new RegExp('(^| )cdt_attribution=([^;]+)'));
            if (m) { try { el.value = decodeURIComponent(m[2]); } catch(e){} }
        }
    })();
    </script>

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
            <?php
                $colClass = match($field->column_width) {
                    'full' => 'md:col-span-2',
                    'half' => '',
                    'third' => '',
                    default => '',
                };
                $hasError = isset($fieldErrors[$field->field_id]);
                $baseClass = 'w-full bg-transparent border-t-0 border-x-0 py-2.5 text-sm focus:outline-none transition-all text-zinc-800 placeholder-zinc-400/70';
                $defaultBorder = $hasError ? ($isDark ? 'ring-2 ring-yellow-300 border-yellow-300' : 'border-b-2 border-red-500') : ($isDark ? '' : 'border-b border-zinc-300 focus:border-primary');
            ?>

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

                @if($field->type === 'select' || $field->type === 'vendor_solutions' || $field->type === 'solution_needed')
                    <?php
                        $defaultOptions = [
                            ['label' => 'Cloud Services', 'value' => 'Cloud Services'],
                            ['label' => 'Consulting', 'value' => 'Consulting'],
                            ['label' => 'Security', 'value' => 'Security'],
                            ['label' => 'Other', 'value' => 'Other'],
                        ];

                        $selectedVal = null;
                        $selectOptions = [];
                        $isSolutionField = ($field->type === 'vendor_solutions' || $field->type === 'solution_needed' || str_contains(strtolower($field->getRawOriginal('label') ?? $field->label), 'solution') || str_contains(strtolower($field->field_id), 'solution'));

                        if ($isSolutionField && isset($entry)) {
                            $postTypeSlug = $entry->postType?->slug;

                            if ($postTypeSlug === 'technology-alliance') {
                                // 1. Single Technology Alliance Page (e.g. /akamai)
                                // Extract Featured Solutions titles & related products
                                $solutionsFeatured = $entry->getMeta('solutions_featured', []);
                                $relatedProducts = $entry->relatedEntries('product_id')->published()->get();
                                if ($relatedProducts->isEmpty() && $entry->slug) {
                                    $relatedProducts = \App\Models\CptEntry::published()
                                        ->whereHas('postType', fn($q) => $q->where('slug', 'tech-products'))
                                        ->where('meta->parent_vendor', $entry->slug)
                                        ->get();
                                }

                                $optionsList = [];
                                if (!empty($solutionsFeatured) && is_array($solutionsFeatured)) {
                                    foreach ($solutionsFeatured as $sf) {
                                        if (!empty($sf['title'])) {
                                            $optionsList[] = ['label' => $sf['title'], 'value' => $sf['title']];
                                        }
                                    }
                                }

                                if (empty($optionsList) && $relatedProducts->isNotEmpty()) {
                                    foreach ($relatedProducts as $rp) {
                                        $optionsList[] = ['label' => $rp->title, 'value' => $rp->title];
                                    }
                                }

                                if (!empty($optionsList)) {
                                    $selectOptions = array_values(array_column($optionsList, null, 'value'));
                                }
                            } elseif ($postTypeSlug === 'tech-products') {
                                // 2. Tech Products Page (e.g. /akamai-connected-cloud)
                                // Fetch all sibling tech products under the same parent alliance
                                $parentVendorSlug = $entry->getMeta('parent_vendor');
                                $parentVendor = $entry->parentRelatedEntries()->first() 
                                    ?? ($parentVendorSlug ? \App\Models\CptEntry::where('slug', $parentVendorSlug)->first() : null);

                                if ($parentVendor) {
                                    $subProducts = $parentVendor->relatedEntries('product_id')->published()->get();
                                    if ($subProducts->isEmpty() && $parentVendor->slug) {
                                        $subProducts = \App\Models\CptEntry::published()
                                            ->whereHas('postType', fn($q) => $q->where('slug', 'tech-products'))
                                            ->where('meta->parent_vendor', $parentVendor->slug)
                                            ->get();
                                    }
                                    $selectedVal = $entry->title;
                                    if ($subProducts->isNotEmpty()) {
                                        $selectOptions = $subProducts->map(fn($s) => ['label' => $s->title, 'value' => $s->title])->toArray();
                                    }
                                } else {
                                    $selectOptions = array_merge(
                                        [['label' => $entry->title, 'value' => $entry->title]],
                                        $defaultOptions
                                    );
                                }
                            }
                        }

                        if (empty($selectOptions)) {
                            $selectOptions = !empty($field->options) ? $field->options : $defaultOptions;
                        }

                        if (empty($selectOptions)) {
                            $selectOptions = $defaultOptions;
                        }
                    ?>
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
                              <?php
                                $isSelected = !empty($selectedVal) && strtolower(trim((string)$selectedVal)) === strtolower(trim((string)$opt['value']));
                              ?>
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
                    <?php
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
                    ?>
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

    {{-- CAPTCHA — respects form's spam_protection settings --}}
    @if($captchaProvider === 'none')
        {{-- Simple honeypot-only, no visible captcha. Skip captcha validation. --}}
    @elseif(!$captchaConfigured)
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-700 text-xs flex items-start gap-2">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <span><strong>CAPTCHA not configured.</strong> The {{ $captchaProvider }} site key is missing. Configure it in <code>config/services.php</code> or set captcha to "none" in form settings.</span>
        </div>
    @else
        @if(in_array($captchaProvider, ['recaptcha_v2', 'recaptcha_v3']) && !empty(config('services.recaptcha.site_key')))
            @if($captchaProvider === 'recaptcha_v2')
                <div class="mb-3">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                </div>
                <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            @else
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
            @endif
        @elseif($captchaProvider === 'turnstile' && !empty(config('services.turnstile.site_key')))
            <div class="mb-3">
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="auto"></div>
            </div>
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endif

        {{-- Show captcha not completed error --}}
        <p class="text-xs text-red-500" x-show="errors['captcha']" x-text="errors['captcha']"></p>
    @endif

    {{-- Submit --}}
    <div class="{{ $isDark ? 'flex flex-col items-center' : 'pt-4 flex justify-end' }}">
        <?php
            $btnText = !empty($form->submit_button_text) ? $form->submit_button_text : t('form.submit_' . $form->slug, 'Send Message');
        ?>
        <button type="submit" :disabled="submitting" class="{{ $btnClass }} inline-flex items-center justify-center gap-2 text-center transition-all duration-300">
            <template x-if="submitting">
                <svg class="h-4 w-4 text-current shrink-0 animate-spin" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3.5"></circle>
                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </template>
            <template x-if="justSubmitted && !submitting">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </template>
            <span x-text="submitting ? 'Submitting...' : (justSubmitted ? 'Submitted!' : '{{ addslashes($btnText) }}')">{{ $btnText }}</span>
        </button>
    </div>
</form>
</div>

{{-- Alpine.js validator --}}
@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('tailwindFormValidator', (config) => ({
        errors: {},
        submitting: false,
        justSubmitted: false,
        isSubmitted: false,
        successMessage: '',

        init() {
            if (config.serverErrors && Object.keys(config.serverErrors).length) {
                this.errors = {...config.serverErrors};
            }
        },

        resetForm() {
            this.isSubmitted = false;
            this.justSubmitted = false;
            this.errors = {};
            this.successMessage = '';
            this.submitting = false;
        },

        validateAndSubmit(e) {
            this.errors = {};
            let valid = true;
            const formEl = e.target;

            // Validate form fields
            config.fields.forEach(f => {
                if (!f.is_required) return;
                const el = formEl.querySelector(`[name="${f.field_id}"]`) || document.getElementById(f.field_id);
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

            if (!valid) {
                const firstError = document.querySelector('[x-show^="errors["]');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            // Perform AJAX Submission without page refresh if ajaxUrl is present
            if (config.ajaxUrl) {
                this.submitting = true;
                const formData = new FormData(formEl);

                fetch(config.ajaxUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async (res) => {
                    const data = await res.json();
                    this.submitting = false;

                    if (res.ok && data.success) {
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                            return;
                        }
                        this.isSubmitted = true;
                        this.justSubmitted = true;
                        this.successMessage = data.message || 'Thank you for your submission!';
                        try { formEl.reset(); } catch(e){}

                        setTimeout(() => {
                            this.justSubmitted = false;
                        }, 3000);

                        const banner = formEl.querySelector('#elementor-message-success');
                        if (banner) banner.scrollIntoView({ behavior: 'smooth', block: 'center' });

                        try {
                            const eventData = { form_id: formEl.getAttribute('action'), message: data.message };
                            window.dispatchEvent(new CustomEvent('submit_success', { detail: eventData }));
                            document.dispatchEvent(new CustomEvent('submit_success', { detail: eventData }));
                        } catch(e) {}
                    } else if (data.errors) {
                        this.errors = data.errors;
                        const firstError = document.querySelector('[x-show^="errors["]');
                        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        this.errors['captcha'] = 'Submission failed. Please try again.';
                    }
                })
                .catch((err) => {
                    this.submitting = false;
                    console.error('AJAX form submission error:', err);
                    formEl.submit();
                });
            } else {
                formEl.submit();
            }
        }
    }));
});
</script>
@endpush
@endonce
