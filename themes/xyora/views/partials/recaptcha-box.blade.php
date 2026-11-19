<div class="recaptcha-box" @if(!empty($style)) style="{{ $style }}" @endif>
  <div class="recaptcha-left">
    <input type="checkbox" id="{{ $inputId ?? 'recaptcha-mock' }}" class="recaptcha-check" required />
    <label for="{{ $inputId ?? 'recaptcha-mock' }}">I'm not a robot</label>
  </div>
  <div class="recaptcha-logo">
    <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" role="img">
      <path fill="#1c3aa9" d="M12 2a9.96 9.96 0 0 1 6.29 2.22l1.42-1.42V8h-5.2l1.82-1.82A7.46 7.46 0 0 0 4.5 12H2A10 10 0 0 1 12 2z"/>
      <path fill="#4285f4" d="M22 12a10 10 0 0 1-16.29 7.78L4.29 21.2V16h5.2l-1.82 1.82A7.46 7.46 0 0 0 19.5 12H22z"/>
      <path fill="#ababab" d="M12 4.5a7.5 7.5 0 0 1 7.5 7.5H22A10 10 0 0 0 12 2v2.5z" opacity=".35"/>
    </svg>
    <span>reCAPTCHA<br />Privacy - Terms</span>
  </div>
</div>
