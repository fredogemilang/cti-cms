@php
  // Check if current page is CPT products detail page
  $isProductDetail = false;
  if (isset($entry) && $entry instanceof \App\Models\CptEntry && $entry->postType->slug === 'products') {
      $isProductDetail = true;
  }
@endphp

<!-- Footer -->
@if($isProductDetail)
  <footer class="site-footer product-detail-footer" style="background-image: url('{{ theme_asset('images/bg-footer.png') }}')">
    <div class="footer-container product-detail-footer-container">
      
      <!-- Left Column: Logo & Social/Contact Details -->
      <div class="footer-left-section">
        <!-- Logo column -->
        <div class="footer-logo-column">
          <img src="{{ theme_asset('images/logo-footer.png') }}" alt="Xyora Logo" class="footer-logo" />
        </div>
        
        <!-- Divider -->
        <div class="footer-vertical-divider"></div>
        
        <!-- Contact Details & Social Media Column -->
        <div class="footer-info-column">
          <div class="footer-social-section" style="margin-bottom: 1.5rem;">
            <h3 class="footer-heading-small" style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 800; color: var(--primary-navy); margin-bottom: 0.75rem;">Ikuti Social Media Kami</h3>
            <div class="social-icons" style="justify-content: flex-start; display: flex; gap: 1rem;">
              <a href="{{ setting('social_instagram', '#') }}" aria-label="Instagram">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff" width="22" height="22">
                  <path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z" />
                </svg>
              </a>
              <a href="{{ setting('social_linkedin', '#') }}" aria-label="LinkedIn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff" width="22" height="22">
                  <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z" />
                </svg>
              </a>
              <a href="{{ setting('social_youtube', '#') }}" aria-label="YouTube">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff" width="24" height="24">
                  <path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.47.13-1.33.22-2.65.28-1.3.07-2.49.1-3.59.1L12 5c4.19 0 6.8.16 7.83.44.9.25 1.48.83 1.73 1.73z" />
                </svg>
              </a>
            </div>
          </div>
          
          <h3 class="footer-heading-small" style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 800; color: var(--primary-navy); margin-bottom: 0.75rem;">Xyora Indonesia</h3>
          <ul class="footer-contact-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1.2rem;">
            <li style="display: flex; align-items: flex-start; gap: 1rem;">
              <div class="contact-icon" style="flex-shrink: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#89C55C" width="24" height="24">
                  <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                </svg>
              </div>
              <div class="contact-text" style="font-size: 0.85rem; line-height: 1.4; color: var(--primary-navy); font-weight: 500;">
                {{ setting('contact_address', 'Ketapang Business Center Blok D2-D3, Gedung Graha SA Lt. 8, R801, Jl. KH. Zainul Arifin No. 20, Jakarta 11140') }}
              </div>
            </li>
            <li style="display: flex; align-items: flex-start; gap: 1rem;">
              <div class="contact-icon" style="flex-shrink: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#89C55C" width="24" height="24">
                  <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                </svg>
              </div>
              <div class="contact-text" style="font-size: 0.85rem; line-height: 1.4; color: var(--primary-navy); font-weight: 500;">{{ setting('contact_phone', '(021) 634 8020') }}</div>
            </li>
            <li style="display: flex; align-items: flex-start; gap: 1rem;">
              <div class="contact-icon" style="flex-shrink: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#89C55C" width="24" height="24">
                  <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                </svg>
              </div>
              <div class="contact-text" style="font-size: 0.85rem; line-height: 1.4; color: var(--primary-navy); font-weight: 500;">{{ setting('contact_email', 'info@xyora-indonesia.com') }}</div>
            </li>
          </ul>
        </div>
      </div>
      
      <!-- Right Column: Form Section -->
      <div class="footer-right-form-section">
        <!-- SweetAlert handles notifications -->

        @php
          $contactForm = \App\Models\Form::where('slug', 'contact-form')->with('fields')->first();
        @endphp

        @if($contactForm)
          <form action="{{ route('forms.submit', $contactForm->slug) }}" method="POST" class="footer-form-element">
            @csrf
            
            @php
              $fields = $contactForm->fields->sortBy('order');
              
              // Group text-like fields into rows of 2, keep textarea full width
              $groupedRows = [];
              $tempRow = [];
              foreach ($fields as $field) {
                  if ($field->type === 'textarea') {
                      if (!empty($tempRow)) {
                          $groupedRows[] = $tempRow;
                          $tempRow = [];
                      }
                      $groupedRows[] = [$field];
                  } else {
                      $tempRow[] = $field;
                      if (count($tempRow) === 2) {
                          $groupedRows[] = $tempRow;
                          $tempRow = [];
                      }
                  }
              }
              if (!empty($tempRow)) {
                  $groupedRows[] = $tempRow;
              }
            @endphp

            @foreach ($groupedRows as $row)
              @if (count($row) === 2)
                <div class="form-grid-row">
                  @foreach ($row as $field)
                    @php
                      $val = old($field->field_id);
                      if ($field->field_id === 'produk_yang_diminati' && empty($val)) {
                          $val = $entry->getTranslation('title');
                      }
                    @endphp
                    <input type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}" 
                           name="{{ $field->field_id }}" 
                           value="{{ $val }}" 
                           placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                           class="footer-form-input" 
                           {{ $field->is_required ? 'required' : '' }} />
                  @endforeach
                </div>
              @else
                @php $field = $row[0]; @endphp
                @if ($field->type === 'textarea')
                  <textarea name="{{ $field->field_id }}" 
                            placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                            class="footer-form-input footer-textarea" 
                            style="height: 60px; resize: vertical;" 
                            {{ $field->is_required ? 'required' : '' }}>{{ $field->field_id === 'deskripsi_kebutuhan' ? (old($field->field_id) ?: 'Konsultasi diajukan dari footer detail produk ' . $entry->getTranslation('title') . '.') : old($field->field_id) }}</textarea>
                @else
                  <div class="form-grid-row" style="grid-template-columns: 1fr;">
                    @php
                      $val = old($field->field_id);
                      if ($field->field_id === 'produk_yang_diminati' && empty($val)) {
                          $val = $entry->getTranslation('title');
                      }
                    @endphp
                    <input type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}" 
                           name="{{ $field->field_id }}" 
                           value="{{ $val }}" 
                           placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                           class="footer-form-input" 
                           {{ $field->is_required ? 'required' : '' }} />
                  </div>
                @endif
              @endif
            @endforeach

            <div class="form-consent-row">
              <input type="checkbox" id="consent-check" class="consent-checkbox-input" required />
              <label for="consent-check" class="consent-label">
                Dengan mengisi data di atas, Anda mengizinkan Xyora dan pihak terkait untuk mengumpulkan dan memproses sesuai kebutuhan.
              </label>
            </div>
            
            <div class="form-action-row">
              <div class="recaptcha-box-wrapper">
                @php
                  $captchaProvider = $contactForm->spam_protection['captcha_provider'] ?? 'none';
                  $captchaService = new \App\Services\CaptchaService;
                  $captchaHtml = $captchaService->renderWidget($captchaProvider);
                @endphp
                @if(!empty($captchaHtml))
                  {!! $captchaHtml !!}
                @else
                  <div class="recaptcha-box" style="margin-top: 0">
                    <div class="recaptcha-left">
                      <input type="checkbox" id="footer-recaptcha-mock" class="recaptcha-check" required />
                      <label for="footer-recaptcha-mock">I'm not a robot</label>
                    </div>
                    <div class="recaptcha-logo">
                      <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA" width="24" />
                      <span>reCAPTCHA<br />Privacy - Terms</span>
                    </div>
                  </div>
                @endif
              </div>
              <button type="submit" class="footer-submit-btn">Kirim</button>
            </div>
          </form>
        @endif
      </div>
      
    </div>
  </footer>

  <style>
    /* Product Detail Footer Specific Styles */
    .product-detail-footer-container {
      display: grid !important;
      grid-template-columns: 1.3fr 1fr;
      gap: 4rem;
      align-items: flex-start;
    }

    .footer-left-section {
      display: flex;
      align-items: stretch;
      gap: 3.5rem;
    }

    .footer-logo-column {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      text-align: center;
    }

    .footer-vertical-divider {
      width: 1px;
      background-color: var(--primary-navy);
      align-self: stretch;
      opacity: 0.15;
      margin: 0 1rem;
    }

    .footer-info-column {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .footer-right-form-section {
      width: 100%;
    }

    .footer-form-element {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .form-grid-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0.75rem;
    }

    .footer-form-input {
      width: 100%;
      padding: 0.6rem 0.8rem;
      border: 1px solid var(--gray-border);
      border-radius: 8px;
      font-family: var(--font-body);
      font-size: 0.88rem;
      color: var(--text-dark);
      background-color: var(--white);
      transition: border-color var(--transition-fast);
    }

    .footer-form-input:focus {
      outline: none;
      border-color: var(--accent-green);
    }

    .footer-form-input::placeholder {
      color: #cbd5e1;
    }

    .form-consent-row {
      display: flex;
      align-items: flex-start;
      gap: 0.5rem;
      margin-top: 0.1rem;
    }

    .consent-checkbox-input {
      margin-top: 0.2rem;
      cursor: pointer;
      accent-color: var(--accent-green);
    }

    .consent-label {
      font-size: 0.8rem;
      line-height: 1.4;
      color: var(--text-light);
      font-weight: 500;
      cursor: pointer;
    }

    .form-action-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 0.25rem;
    }

    .recaptcha-box-wrapper {
      flex-shrink: 0;
      transform: scale(0.85);
      transform-origin: left center;
    }

    .footer-submit-btn {
      background-color: var(--accent-green);
      color: var(--primary-navy);
      font-family: var(--font-heading);
      font-weight: 700;
      font-size: 0.95rem;
      padding: 0.7rem 2.2rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all var(--transition-normal);
    }

    .footer-submit-btn:hover {
      background-color: var(--accent-green-dark);
      transform: translateY(-2px);
    }

    @media (max-width: 1200px) {
      .product-detail-footer-container {
        grid-template-columns: 1fr;
        gap: 3rem;
      }
    }

    @media (max-width: 768px) {
      .footer-left-section {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }
      .footer-vertical-divider {
        display: none;
      }
      .footer-info-column {
        align-items: center;
      }
      .footer-social-section .social-icons {
        justify-content: center !important;
      }
      .form-grid-row {
        grid-template-columns: 1fr;
      }
      .form-action-row {
        flex-direction: column;
        gap: 1.5rem;
        align-items: center;
      }
    }
  </style>
@else
  <footer class="site-footer" style="background-image: url('{{ theme_asset('images/bg-footer.png') }}')">
    <div class="footer-container">
      <!-- Column 1: Logo -->
      <div class="footer-col footer-col-logo">
        <img src="{{ theme_asset('images/logo-footer.png') }}" alt="Xyora Logo" class="footer-logo" />
      </div>

      <!-- Column 2: Contact Info -->
      <div class="footer-col footer-col-contact">
        <h3 class="footer-heading">{{ setting('site_name', 'Xyora Indonesia') }}</h3>
        <ul class="footer-contact-list">
          <li>
            <div class="contact-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#89C55C" width="28" height="28">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
              </svg>
            </div>
            <div class="contact-text">
              {{ setting('contact_address', 'Ketapang Business Center Blok D2-D3, Gedung Graha SA Lt. 8, R801, Jl. KH. Zainul Arifin No. 20, Jakarta 11140') }}
            </div>
          </li>
          <li>
            <div class="contact-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#89C55C" width="28" height="28">
                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
              </svg>
            </div>
            <div class="contact-text">{{ setting('contact_phone', '(021) 634 8020') }}</div>
          </li>
          <li>
            <div class="contact-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#89C55C" width="28" height="28">
                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
              </svg>
            </div>
            <div class="contact-text">{{ setting('contact_email', 'info@xyora-indonesia.com') }}</div>
          </li>
        </ul>
      </div>

      <!-- Column 3: Social Media -->
      <div class="footer-col footer-col-social">
        <h3 class="footer-heading">{{ t('footer.social_title', 'Ikuti Social Media Kami') }}</h3>
        <div class="social-icons">
          <a href="{{ setting('social_instagram', '#') }}" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff" width="22" height="22">
              <path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z" />
            </svg>
          </a>
          <a href="{{ setting('social_linkedin', '#') }}" aria-label="LinkedIn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff" width="22" height="22">
              <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z" />
            </svg>
          </a>
          <a href="{{ setting('social_youtube', '#') }}" aria-label="YouTube">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ffffff" width="24" height="24">
              <path d="M10 15l5.19-3L10 9v6m11.56-7.83c.13.47.22 1.1.28 1.9.07.8.1 1.49.1 2.09L22 12c0 2.19-.16 3.8-.44 4.83-.25.9-.83 1.48-1.73 1.73-.47.13-1.33.22-2.65.28-1.3.07-2.49.1-3.59.1L12 5c4.19 0 6.8.16 7.83.44.9.25 1.48.83 1.73 1.73z" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  </footer>
@endif
