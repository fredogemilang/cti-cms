@php
    $honeypot = $form->spam_protection['honeypot'] ?? false;

    $fieldConfig = $form->fields->map(fn ($f) => [
        'field_id' => $f->field_id,
        'label' => $f->localizedLabel(),
        'type' => $f->type,
        'is_required' => (bool) $f->is_required,
        'validation' => $f->validation ?? [],
    ])->values()->toArray();

    $fieldErrors = [];
    $errorsBag = $errors ?? new \Illuminate\Support\ViewErrorBag;
    if ($errorsBag->any()) {
        foreach ($errorsBag->getMessages() as $key => $msgs) {
            $fieldErrors[$key] = $msgs[0];
        }
    }

    $submitText = $form->getTranslation('submit_button_text') ?: $form->getRawOriginal('submit_button_text') ?: 'Submit Inquiry';
@endphp

@if(session('success') || session('form_success_message'))
  <div class="mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-start gap-3">
    <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>{{ session('success') ?? session('form_success_message') }}</span>
  </div>
@endif

<div x-data="csiTailwindForm({{ json_encode([
        'fields' => $fieldConfig,
        'serverErrors' => $fieldErrors,
        'ajaxUrl' => route('forms.submit.ajax', $form->slug),
    ]) }})">

  <form method="POST" action="{{ route('forms.submit', $form->slug) }}" class="mt-8 space-y-4" novalidate @submit.prevent="validateAndSubmit">
    @csrf

    {{-- AJAX Success Banner --}}
    <div x-show="isSubmitted" x-cloak x-transition
         id="csi-form-success"
         class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 text-center mb-4"
         role="alert">
      <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>
      <p class="text-emerald-800 text-sm font-medium leading-relaxed" x-text="successMessage"></p>
    </div>

    {{-- Attribution --}}
    <input type="hidden" name="_submission_page" value="{{ url()->current() }}">
    <input type="hidden" name="_attribution" id="_attribution_{{ $form->id }}" value="">
    <script>
      (function () {
        var el = document.getElementById('_attribution_{{ $form->id }}');
        if (el) {
          var m = document.cookie.match(/(^| )cms_attribution=([^;]+)/);
          if (m) { try { el.value = decodeURIComponent(m[2]); } catch (e) {} }
        }
      })();
    </script>

    {{-- Honeypot --}}
    @if($honeypot)
      <div style="display:none;"><input type="text" name="website_url" tabindex="-1" autocomplete="off"></div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      @foreach($form->fields as $field)
        @php
          if ($field->is_hidden) {
              continue;
          }
          $colClass = match ($field->column_width) {
              'full' => 'sm:col-span-2',
              default => '',
          };
        @endphp

        <div class="{{ $colClass }}">
          @if($field->type === 'gdpr' || $field->type === 'terms')
            @php
              $locale = app()->getLocale();
              $defaultLocale = setting('default_locale', config('app.locale', 'en'));
              $defaultConsentText = $field->advanced_settings['consent_text']
                  ?? $field->advanced_settings['privacy_content']
                  ?? "By submitting this form, you acknowledge that you have read and agree to Converion's Privacy Policy regarding the collection and processing of your personal data.";
              if ($locale !== $defaultLocale) {
                  $rawTrans = $field->getRawOriginal('translations');
                  $transArr = is_string($rawTrans) ? json_decode($rawTrans, true) : ($rawTrans ?? []);
                  $consentText = $transArr[$locale]['consent_text'] ?? $defaultConsentText;
              } else {
                  $consentText = $defaultConsentText;
              }
            @endphp
            <div class="flex items-start gap-2.5 pt-1">
              <input type="checkbox"
                     name="{{ $field->field_id }}"
                     id="contact-{{ $field->field_id }}"
                     value="1"
                     @if($field->is_required) required @endif
                     @change="delete errors['{{ $field->field_id }}']"
                     class="mt-1 rounded text-[#04B1C7] focus:ring-[#04B1C7] cursor-pointer" />
              <label for="contact-{{ $field->field_id }}" class="text-xs text-slate-600 leading-normal cursor-pointer">
                {!! $consentText !!}
              </label>
            </div>
          @else
            <label for="contact-{{ $field->field_id }}" class="block text-xs font-semibold text-slate-700 mb-1">
              {{ $field->localizedLabel() }}
              @if($field->is_required)<span class="text-[#ED7F22]">*</span>@endif
            </label>

            @if($field->type === 'select')
              <select id="contact-{{ $field->field_id }}"
                      name="{{ $field->field_id }}"
                      @if($field->is_required) required @endif
                      @change="delete errors['{{ $field->field_id }}']"
                      :class="errors['{{ $field->field_id }}'] ? 'border-red-500' : 'border-slate-200'"
                      class="w-full px-4 py-2.5 rounded-lg border bg-white text-sm text-slate-700 focus:outline-none focus:border-[#04B1C7] transition cursor-pointer">
                @if($field->localizedPlaceholder())
                  <option value="" disabled selected>{{ $field->localizedPlaceholder() }}</option>
                @endif
                @foreach(($field->options ?? []) as $opt)
                  @php
                    $optValue = is_array($opt) ? ($opt['value'] ?? $opt['label'] ?? '') : $opt;
                    $optLabel = is_array($opt) ? ($opt['label'] ?? $opt['value'] ?? '') : $opt;
                  @endphp
                  <option value="{{ $optValue }}">{{ $optLabel }}</option>
                @endforeach
              </select>
            @elseif($field->type === 'textarea')
              <textarea id="contact-{{ $field->field_id }}"
                        name="{{ $field->field_id }}"
                        rows="4"
                        @if($field->is_required) required @endif
                        placeholder="{{ $field->localizedPlaceholder() }}"
                        @input="delete errors['{{ $field->field_id }}']"
                        :class="errors['{{ $field->field_id }}'] ? 'border-red-500' : 'border-slate-200'"
                        class="w-full px-4 py-2.5 rounded-lg border bg-white text-sm focus:outline-none focus:border-[#04B1C7] transition"></textarea>
            @else
              <input type="{{ $field->type === 'tel' ? 'tel' : ($field->type === 'email' ? 'email' : 'text') }}"
                     id="contact-{{ $field->field_id }}"
                     name="{{ $field->field_id }}"
                     @if($field->is_required) required @endif
                     placeholder="{{ $field->localizedPlaceholder() }}"
                     @input="delete errors['{{ $field->field_id }}']"
                     :class="errors['{{ $field->field_id }}'] ? 'border-red-500' : 'border-slate-200'"
                     class="w-full px-4 py-2.5 rounded-lg border bg-white text-sm focus:outline-none focus:border-[#04B1C7] transition" />
            @endif
          @endif

          <p x-show="errors['{{ $field->field_id }}']" x-cloak x-text="errors['{{ $field->field_id }}']" class="text-xs text-red-500 mt-1 font-medium"></p>
        </div>
      @endforeach
    </div>

    <button type="submit"
            id="contact-submit-btn"
            :disabled="submitting"
            class="w-full bg-[#ED7F22] hover:bg-[#d86f1b] text-white font-bold text-base py-3.5 rounded-md shadow-md hover:shadow-orange-200 transition-all duration-200 mt-2 uppercase tracking-wide cursor-pointer disabled:opacity-60">
      <span x-show="!submitting">{{ $submitText }}</span>
      <span x-show="submitting" x-cloak>{{ t('form.sending', 'Sending...') }}</span>
    </button>
  </form>
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  const freeEmailProviders = new Set([
    'gmail.com', 'googlemail.com',
    'yahoo.com', 'yahoo.co.id', 'yahoo.co.uk', 'yahoo.fr', 'yahoo.es', 'yahoo.de', 'ymail.com', 'rocketmail.com',
    'hotmail.com', 'hotmail.co.id', 'hotmail.co.uk', 'outlook.com', 'outlook.co.id', 'live.com', 'msn.com',
    'icloud.com', 'me.com', 'mac.com',
    'aol.com', 'aim.com',
    'mail.com', 'email.com',
    'proton.me', 'protonmail.com',
    'zoho.com', 'zohomail.com',
    'yandex.com', 'yandex.ru',
    'gmx.com', 'gmx.net',
    'tutanota.com', 'tutamail.com',
    'tempmail.com', '10minutemail.com', 'guerrillamail.com', 'throwawaymail.com',
    'mailinator.com', 'yopmail.com'
  ]);

  Alpine.data('csiTailwindForm', (config) => ({
    errors: {},
    submitting: false,
    isSubmitted: false,
    successMessage: '',

    init() {
      if (config.serverErrors && Object.keys(config.serverErrors).length) {
        this.errors = { ...config.serverErrors };
      }
    },

    validateAndSubmit(e) {
      this.errors = {};
      let valid = true;
      const formEl = e.target;

      let xsrfToken = '';
      try {
        const cookieMatch = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
        if (cookieMatch) xsrfToken = decodeURIComponent(cookieMatch[1]);
      } catch (err) {}

      const tokenInput = formEl.querySelector('input[name="_token"]');
      if (xsrfToken && tokenInput) {
        tokenInput.value = xsrfToken;
      }

      config.fields.forEach((f) => {
        const el = formEl.querySelector(`[name="${f.field_id}"]`);
        if (!el) return;

        if (el.type === 'checkbox') {
          if (f.is_required && !el.checked) {
            this.errors[f.field_id] = `${f.label} is required.`;
            valid = false;
          }
          return;
        }

        const value = (el.value || '').trim();
        if (f.is_required && !value) {
          this.errors[f.field_id] = `${f.label} is required.`;
          valid = false;
        } else if (value && f.type === 'email') {
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            this.errors[f.field_id] = 'Please enter a valid email address.';
            valid = false;
          } else if (f.validation && f.validation.rule === 'corporate_email') {
            const domain = (value.split('@')[1] || '').toLowerCase().trim();
            if (freeEmailProviders.has(domain)) {
              this.errors[f.field_id] = f.validation.rule_message || `Please enter a corporate/business email address (free email providers like ${domain} are not accepted).`;
              valid = false;
            }
          }
        } else if (value && f.type === 'tel' && !/^[0-9\-\+\(\)\s]+$/.test(value)) {
          this.errors[f.field_id] = 'Please enter a valid phone number.';
          valid = false;
        }
      });

      if (!valid) {
        const firstErr = formEl.querySelector('.border-red-500, [type="checkbox"]');
        if (firstErr) firstErr.focus();
        return;
      }

      if (config.ajaxUrl) {
        this.submitting = true;
        const formData = new FormData(formEl);

        fetch(config.ajaxUrl, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
          },
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
              this.successMessage = data.message || 'Thank you for contacting Converion! Our team will get back to you shortly.';
              try { formEl.reset(); } catch (err) {}
            } else if (data.errors) {
              const normalized = {};
              Object.entries(data.errors).forEach(([k, v]) => {
                normalized[k] = Array.isArray(v) ? v[0] : v;
              });
              this.errors = normalized;
            } else {
              this.errors.general = 'Submission failed. Please try again.';
            }
          })
          .catch(() => {
            this.submitting = false;
            formEl.submit();
          });
      } else {
        formEl.submit();
      }
    },
  }));
});
</script>
@endpush
@endonce
