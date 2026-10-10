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
@endif
