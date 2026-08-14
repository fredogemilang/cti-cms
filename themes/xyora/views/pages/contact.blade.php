@extends('xyora::layouts.app')

@section('title', $page->title ?? 'XYORA - Hubungi Kami')

@section('content')
  @php
    $rmaStatusResult = null;
    $rmaCodeSearched = request()->get('rma_code');
    if ($rmaCodeSearched) {
      $cleanedId = preg_replace('/[^0-9]/', '', $rmaCodeSearched);
      if ($cleanedId) {
        $entry = \App\Models\FormEntry::where('id', $cleanedId)
          ->whereHas('form', function ($q) {
            $q->where('slug', 'rma-form');
          })
          ->first();
        if ($entry) {
          $formData = is_array($entry->data) ? $entry->data : json_decode($entry->data ?? '[]', true);
          $rmaStatusResult = [
            'id' => $entry->id,
            'serial_number' => $formData['serial_number_produk'] ?? '-',
            'product_name' => $formData['nama_produk'] ?? '-',
            'status' => $entry->status ?? 'completed',
            'created_at' => $entry->created_at ? $entry->created_at->translatedFormat('d F Y') : '-',
            'nama_lengkap' => $formData['nama_lengkap'] ?? '-',
          ];
        } else {
          $rmaStatusResult = false;
        }
      } else {
        $rmaStatusResult = false;
      }
    }

    // Dynamic Blocks
    $contactTitle = $page->block('contact_title', 'Konsultasikan Kebutuhan Anda <span>Bersama Kami</span>');
    $contactText = $page->block('contact_text', 'Butuh informasi harga, promo terbaru, konsultasi solusi, atau ingin melakukan pembelian? Isi formulir berikut agar tim sales kami dapat menghubungi Anda.');
    $contactImage = $page->block('contact_image', 'images/contact1.png');

    $rmaTitle = $page->block('rma_title', 'Ajukan Proses <br /><span>RMA Anda</span>');
    $rmaText = $page->block('rma_text', 'Mengalami kendala pada produk Anda? Isi formulir berikut untuk mengajukan proses RMA, dan tim kami akan segera membantu Anda.');
    $rmaImage = $page->block('rma_image', 'images/contact2.png');

    $statusTitle = $page->block('status_title', 'Cek Status <br /><span>Pengajuan RMA Anda</span>');
    $statusText = $page->block('status_text', 'Sudah mengajukan RMA? Masukkan nomor RMA Anda untuk melihat status pengajuan terbaru.');
    $statusImage = $page->block('status_image', 'images/contact3.png');
  @endphp
  <!-- Screen reader only H1 for SEO compliance -->
  <h1 class="sr-only">
    {{ t('contact.h1_seo', 'XYORA - Hubungi Kami untuk Solusi Jaringan Terbaik') }}
  </h1>

  <!-- Main Content Area -->
  <main>
    <!-- Contact Page Section -->
    <section class="kontak-section-custom">
      <div class="kontak-container-custom">
        <!-- Left Column -->
        <div class="kontak-info">
          <h2>{!! $contactTitle !!}</h2>
          <div class="green-line"></div>
          <p>
            {!! $contactText !!}
          </p>
          <div class="kontak-image-wrapper">
            <x-image :src="$contactImage" alt="Hubungi Xyora" class="w-full h-full object-cover" sizes="100vw" />
          </div>
        </div>

        <!-- Right Column (Form Card) -->
        <div class="kontak-card">


          @php
            $contactForm = \App\Models\Form::where('slug', 'contact-form')->with('fields')->first();
          @endphp

          @if($contactForm)
            <form action="{{ route('forms.submit', $contactForm->slug) }}" method="POST" class="kontak-form-custom">
              @csrf
              
              @php
                $fields = $contactForm->fields->sortBy('order');
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
                  <div class="kontak-form-row">
                    @foreach ($row as $field)
                      <input type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}" 
                             name="{{ $field->field_id }}" 
                             value="{{ old($field->field_id) }}" 
                             placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                             class="kontak-input" 
                             {{ $field->is_required ? 'required' : '' }} />
                    @endforeach
                  </div>
                @else
                  @php $field = $row[0]; @endphp
                  @if ($field->type === 'textarea')
                    <textarea name="{{ $field->field_id }}" 
                              placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                              class="kontak-input kontak-textarea" 
                              {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_id) }}</textarea>
                  @else
                    <div class="kontak-form-row" style="display: block;">
                      <input type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}" 
                             name="{{ $field->field_id }}" 
                             value="{{ old($field->field_id) }}" 
                             placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                             class="kontak-input" 
                             style="width: 100%;" 
                             {{ $field->is_required ? 'required' : '' }} />
                    </div>
                  @endif
                @endif
              @endforeach

              <div class="kontak-checkbox-row">
                <input type="checkbox" id="consent-check" required />
                <label for="consent-check">
                  {{ t('contact.consent_text', 'Dengan mengisi data di atas, Anda mengizinkan Xyora dan pihak terkait untuk mengumpulkan dan memproses sesuai kebutuhan.') }}
                </label>
              </div>

              <div class="kontak-form-action">
                <div class="kontak-recaptcha">
                  @php
                    $captchaProvider = $contactForm->spam_protection['captcha_provider'] ?? 'none';
                    $captchaService = new \App\Services\CaptchaService;
                    $captchaHtml = $captchaService->renderWidget($captchaProvider);
                  @endphp
                  @if(!empty($captchaHtml))
                    {!! $captchaHtml !!}
                  @else
                    <!-- Mock reCAPTCHA Box -->
                    <div class="recaptcha-box" style="margin-top: 0">
                      <div class="recaptcha-left">
                        <input type="checkbox" id="recaptcha-mock" class="recaptcha-check" required />
                        <label for="recaptcha-mock">I'm not a robot</label>
                      </div>
                      <div class="recaptcha-logo">
                        <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA" width="24" />
                        <span>reCAPTCHA<br />Privacy - Terms</span>
                      </div>
                    </div>
                  @endif
                </div>
                <button type="submit" class="kontak-btn-submit">{{ t('contact.send', 'Kirim') }}</button>
              </div>
            </form>
          @endif
        </div>
      </div>
    </section>

    <!-- RMA Section -->
    <section class="rma-section-custom">
      <div class="rma-container-custom">
        <!-- Left Column (Form Card) -->
        <div class="rma-card">


          @php
            $rmaForm = \App\Models\Form::where('slug', 'rma-form')->with('fields')->first();
          @endphp

          @if($rmaForm)
            <form action="{{ route('forms.submit', $rmaForm->slug) }}?rma=1" method="POST" enctype="multipart/form-data" class="kontak-form-custom">
              @csrf
              
              @php
                $fields = $rmaForm->fields->sortBy('order');
                $groupedRows = [];
                $tempRow = [];
                foreach ($fields as $field) {
                    // Check if it's file or textarea or date
                    if (in_array($field->type, ['textarea', 'file', 'image'])) {
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
                  <div class="kontak-form-row">
                    @foreach ($row as $field)
                      @if ($field->field_id === 'tanggal_pembelian')
                        <input type="text" name="{{ $field->field_id }}" 
                               value="{{ old($field->field_id) }}" 
                               placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                               class="kontak-input" 
                               onfocus="this.type = 'date'" 
                               onblur="this.value ? this.type = 'date' : this.type = 'text'" 
                               {{ $field->is_required ? 'required' : '' }} />
                      @elseif ($field->field_id === 'jumlah_unit')
                        <input type="number" name="{{ $field->field_id }}" 
                               value="{{ old($field->field_id) }}" 
                               placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                               class="kontak-input" 
                               min="1" 
                               {{ $field->is_required ? 'required' : '' }} />
                      @else
                        <input type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}" 
                               name="{{ $field->field_id }}" 
                               value="{{ old($field->field_id) }}" 
                               placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                               class="kontak-input" 
                               {{ $field->is_required ? 'required' : '' }} />
                      @endif
                    @endforeach
                  </div>
                @else
                  @php $field = $row[0]; @endphp
                  @if ($field->type === 'textarea')
                    <textarea name="{{ $field->field_id }}" 
                              placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                              class="kontak-input kontak-textarea" 
                              {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_id) }}</textarea>
                  @elseif (in_array($field->type, ['file', 'image']))
                    <div class="rma-file-wrapper">
                      <label class="rma-file-label">
                        <span class="file-text">{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }} (upload file)</span>
                        <input type="file" name="{{ $field->field_id }}" 
                               {{ $field->is_required ? 'required' : '' }} 
                               onchange="this.previousElementSibling.textContent = this.files[0] ? this.files[0].name : '{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }} (upload file)'" />
                      </label>
                    </div>
                  @else
                    <div class="kontak-form-row" style="display: block;">
                      @if ($field->field_id === 'tanggal_pembelian')
                        <input type="text" name="{{ $field->field_id }}" 
                               value="{{ old($field->field_id) }}" 
                               placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                               class="kontak-input" 
                               style="width: 100%;" 
                               onfocus="this.type = 'date'" 
                               onblur="this.value ? this.type = 'date' : this.type = 'text'" 
                               {{ $field->is_required ? 'required' : '' }} />
                      @elseif ($field->field_id === 'jumlah_unit')
                        <input type="number" name="{{ $field->field_id }}" 
                               value="{{ old($field->field_id) }}" 
                               placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                               class="kontak-input" 
                               style="width: 100%;" 
                               min="1" 
                               {{ $field->is_required ? 'required' : '' }} />
                      @else
                        <input type="{{ $field->type === 'email' ? 'email' : ($field->type === 'tel' ? 'tel' : 'text') }}" 
                               name="{{ $field->field_id }}" 
                               value="{{ old($field->field_id) }}" 
                               placeholder="{{ $field->localizedPlaceholder() ?: $field->localizedLabel() }}" 
                               class="kontak-input" 
                               style="width: 100%;" 
                               {{ $field->is_required ? 'required' : '' }} />
                      @endif
                    </div>
                  @endif
                @endif
              @endforeach

              <div class="kontak-checkbox-row">
                <input type="checkbox" id="rma-consent-check" required />
                <label for="rma-consent-check">
                  {{ t('contact.consent_text', 'Dengan mengisi data di atas, Anda mengizinkan Xyora dan pihak terkait untuk mengumpulkan dan memproses sesuai kebutuhan.') }}
                </label>
              </div>

              <div class="rma-note">
                {{ t('contact.rma_note', '*Nomor RMA akan dikirimkan melalui email setelah proses verifikasi oleh tim kami.') }}
              </div>

              <div class="kontak-form-action">
                <div class="kontak-recaptcha">
                  @php
                    $captchaProvider = $rmaForm->spam_protection['captcha_provider'] ?? 'none';
                    $captchaService = new \App\Services\CaptchaService;
                    $captchaHtml = $captchaService->renderWidget($captchaProvider);
                  @endphp
                  @if(!empty($captchaHtml))
                    {!! $captchaHtml !!}
                  @else
                    <!-- Mock reCAPTCHA Box -->
                    <div class="recaptcha-box" style="margin-top: 0">
                      <div class="recaptcha-left">
                        <input type="checkbox" id="rma-recaptcha-mock" class="recaptcha-check" required />
                        <label for="rma-recaptcha-mock">I'm not a robot</label>
                      </div>
                      <div class="recaptcha-logo">
                        <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA" width="24" />
                        <span>reCAPTCHA<br />Privacy - Terms</span>
                      </div>
                    </div>
                  @endif
                </div>
                <button type="submit" class="kontak-btn-submit">{{ t('contact.send', 'Kirim') }}</button>
              </div>
            </form>
          @endif
        </div>

        <!-- Right Column -->
        <div class="rma-info">
          <h2>{!! $rmaTitle !!}</h2>
          <div class="green-line"></div>
          <p>
            {!! $rmaText !!}
          </p>
          <div class="rma-image-wrapper">
            <x-image :src="$rmaImage" alt="Ajukan RMA Xyora" class="w-full h-full object-cover" sizes="100vw" />
          </div>
        </div>
      </div>
    </section>

    <!-- Status RMA Section -->
    <section class="status-section-custom" id="status-rma">
      <div class="status-container-custom">
        <!-- Left Column -->
        <div class="status-info">
          <h2>{!! $statusTitle !!}</h2>
          <div class="green-line"></div>
          <p>
            {!! $statusText !!}
          </p>
          <form action="{{ request()->url() }}#status-rma" method="GET" class="status-form-custom">
            <div class="status-search-wrapper">
              <input type="text" name="rma_code" value="{{ request('rma_code') }}" placeholder="Masukkan nomor RMA Anda"
                class="status-input" required />
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
            </div>
            <button type="submit" class="status-btn-submit">{{ t('contact.send', 'Kirim') }}</button>
          </form>

          @if(request()->has('rma_code'))
            <div class="status-result-box"
              style="margin-top: 25px; padding: 20px; border-radius: 8px; border: 1px solid #E0EEFB; background: #F8FAFC; text-align: left; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
              @if($rmaStatusResult)
                <h4
                  style="margin: 0 0 15px; color: #1e293b; font-weight: 600; font-size: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">
                  Hasil Penelusuran RMA: <span
                    style="color: #89C55C;">#{{ sprintf('RMA-%04d', $rmaStatusResult['id']) }}</span>
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px; line-height: 1.8;">
                  <tr>
                    <td style="width: 130px; font-weight: 600; color: #64748b; padding: 6px 0;">Nama Pengaju:</td>
                    <td style="color: #334155; padding: 6px 0;">{{ $rmaStatusResult['nama_lengkap'] }}</td>
                  </tr>
                  <tr>
                    <td style="font-weight: 600; color: #64748b; padding: 6px 0;">Nama Produk:</td>
                    <td style="color: #334155; padding: 6px 0;">{{ $rmaStatusResult['product_name'] }}</td>
                  </tr>
                  <tr>
                    <td style="font-weight: 600; color: #64748b; padding: 6px 0;">Serial Number:</td>
                    <td style="color: #334155; padding: 6px 0; font-family: monospace; font-size: 13px;">
                      {{ $rmaStatusResult['serial_number'] }}</td>
                  </tr>
                  <tr>
                    <td style="font-weight: 600; color: #64748b; padding: 6px 0;">Tanggal Masuk:</td>
                    <td style="color: #334155; padding: 6px 0;">{{ $rmaStatusResult['created_at'] }}</td>
                  </tr>
                  <tr>
                    <td style="font-weight: 600; color: #64748b; padding: 6px 0;">Status:</td>
                    <td style="padding: 6px 0;">
                      @if($rmaStatusResult['status'] === 'completed')
                        <span
                          style="background: rgba(137, 197, 92, 0.15); color: #538d24; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; border: 1px solid rgba(137, 197, 92, 0.4);">Selesai
                          / Disetujui</span>
                      @elseif($rmaStatusResult['status'] === 'pending')
                        <span
                          style="background: rgba(245, 158, 11, 0.15); color: #b45309; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; border: 1px solid rgba(245, 158, 11, 0.4);">Menunggu
                          Verifikasi</span>
                      @elseif($rmaStatusResult['status'] === 'processing')
                        <span
                          style="background: rgba(59, 130, 246, 0.15); color: #1d4ed8; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; border: 1px solid rgba(59, 130, 246, 0.4);">Sedang
                          Diproses</span>
                      @elseif($rmaStatusResult['status'] === 'rejected')
                        <span
                          style="background: rgba(239, 68, 68, 0.15); color: #b91c1c; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; border: 1px solid rgba(239, 68, 68, 0.4);">Ditolak</span>
                      @else
                        <span
                          style="background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px; border: 1px solid #cbd5e1;">{{ ucfirst($rmaStatusResult['status']) }}</span>
                      @endif
                    </td>
                  </tr>
                </table>
              @else
                <div style="color: #ef4444; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                  </svg>
                  Nomor RMA tidak ditemukan. Pastikan format nomor benar (contoh: RMA-0001).
                </div>
              @endif
            </div>
          @endif
        </div>

        <!-- Right Column -->
        <div class="status-image-wrapper">
          <x-image :src="$statusImage" alt="Cek Status RMA Xyora" class="w-full h-full object-cover" sizes="100vw" />
        </div>
      </div>
    </section>
  </main>
@endsection