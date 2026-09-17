@extends('layouts.admin')

@section('title', 'Form Studio - ' . $form->name)
@section('hide-title', true)

@section('content')
@php
    $notifications = $form->notifications ?? [];
    $notifyAdmin = $notifications['notify_admin'] ?? ($notifications['enabled'] ?? true);
    $adminEmail = $notifications['admin_email'] ?? config('mail.from.address');
    $adminSubject = $notifications['subject'] ?? "New Form Submission: {$form->name}";
    $adminEmailBody = $notifications['admin_email_body'] ?? "<p>A new submission has been received for <strong>{form_name}</strong>.</p>{submission_table}<p style=\"margin-top: 20px;\"><a href=\"{admin_url}\" style=\"display: inline-block; background-color: #111827; color: #ffffff; padding: 10px 22px; text-decoration: none; border-radius: 8px; font-weight: bold; text-transform: uppercase; font-size: 12px;\">View Entries in Admin</a></p>";

    $sendToUser = $notifications['send_to_user'] ?? false;
    $userSubject = $notifications['user_subject'] ?? "Thank you for your submission - {$form->name}";
    $userEmailBody = $notifications['user_email_body'] ?? ("<p>Hi {name},</p><p>Thank you for submitting <strong>{form_name}</strong>. We have received your details and will get back to you shortly.</p><p><a href=\"".url('themes/cdt/assets/banner_hero-DHYDqbF8.jpg')."\" style=\"display: inline-block; background-color: #b82d25; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 9999px; font-weight: bold; text-transform: uppercase; font-size: 13px; letter-spacing: 1px;\">Download Digital Solution Guide</a></p><p>Best regards,<br>Central Data Technology Team</p>");

    $confirmations = $form->confirmations ?? [];
    $confirmationType = $confirmations['type'] ?? 'message';
    $confirmationMessage = $confirmations['message'] ?? 'Thank you for your submission. We will get back to you soon.';
    $redirectUrl = $confirmations['redirect_url'] ?? '';

    $spamProtection = $form->spam_protection ?? [];
    $honeypot = $spamProtection['honeypot'] ?? true;
    $captchaProvider = $spamProtection['captcha_provider'] ?? 'none';

    // Current assigned theme slot
    $assignedSlot = '';
    if (!empty($currentAssignments)) {
        foreach ($currentAssignments as $slotKeyName => $formId) {
            if ($formId == $form->id) {
                $assignedSlot = $slotKeyName;
                break;
            }
        }
    }
    unset($slot);

    // Prepare fields map for human-readable labels & types
    $fieldMap = [];
    if ($form->fields) {
        foreach ($form->fields as $field) {
            $fieldMap[$field->field_id] = [
                'label' => $field->label,
                'type' => $field->type,
                'options' => $field->options,
            ];
        }
    }

    // Helper closure to format file URLs safely
    $resolveFileUrl = function ($path) {
        if (empty($path) || !is_string($path)) return '';
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) return asset(ltrim($path, '/'));
        if (str_starts_with($path, '/uploads/') || str_starts_with($path, 'uploads/')) return asset('storage/' . ltrim($path, '/'));
        return asset($path);
    };

    // Calculate submission statistics
    $allEntries = $form->entries ?? collect();
    $totalSubmissions = $allEntries->count();
    $todaySubmissions = $allEntries->filter(fn($e) => $e->created_at >= now()->startOfDay())->count();
    $weekSubmissions = $allEntries->filter(fn($e) => $e->created_at >= now()->startOfWeek())->count();
    $latestSubmission = $allEntries->first()?->created_at?->format('M d, Y H:i') ?? 'No submissions yet';

    // Transform entries into rich structured array
    $entriesDataList = $allEntries->map(function ($entry) use ($fieldMap, $resolveFileUrl) {
        $data = is_array($entry->data) ? $entry->data : [];
        $attr = is_array($entry->data['_attribution'] ?? null) ? $entry->data['_attribution'] : $entry->getAttributionData();

        // 1. Detect Applicant Name
        $name = null;
        $nameCandidates = ['full_name', 'name', 'your_name', 'first_name', 'applicant_name', 'contact_name'];
        foreach ($nameCandidates as $k) {
            if (!empty($data[$k]) && is_string($data[$k])) {
                $name = trim($data[$k]);
                break;
            }
        }
        if (!$name) {
            foreach ($data as $k => $v) {
                if (is_string($v) && str_contains(strtolower((string)$k), 'name') && !empty(trim($v))) {
                    $name = trim($v);
                    break;
                }
            }
        }

        // 2. Detect Email
        $email = null;
        $emailCandidates = ['email', 'corporate_email', 'work_email', 'contact_email', 'user_email'];
        foreach ($emailCandidates as $k) {
            if (!empty($data[$k]) && is_string($data[$k]) && filter_var(trim($data[$k]), FILTER_VALIDATE_EMAIL)) {
                $email = trim($data[$k]);
                break;
            }
        }
        if (!$email) {
            foreach ($data as $k => $v) {
                if (is_string($v) && filter_var(trim($v), FILTER_VALIDATE_EMAIL)) {
                    $email = trim($v);
                    break;
                }
            }
        }

        // 3. Detect Phone
        $phone = null;
        $phoneCandidates = ['phone', 'phone_number', 'telephone', 'mobile', 'whatsapp', 'wa'];
        foreach ($phoneCandidates as $k) {
            if (!empty($data[$k]) && is_string($data[$k])) {
                $phone = trim($data[$k]);
                break;
            }
        }

        // 4. Detect Company / Position
        $company = null;
        $companyCandidates = ['company', 'company_name', 'organization', 'institution'];
        foreach ($companyCandidates as $k) {
            if (!empty($data[$k]) && is_string($data[$k])) {
                $company = trim($data[$k]);
                break;
            }
        }

        $position = null;
        $posCandidates = ['position', 'job_title', 'applied_position', 'role', 'subject'];
        foreach ($posCandidates as $k) {
            if (!empty($data[$k]) && is_string($data[$k])) {
                $position = trim($data[$k]);
                break;
            }
        }

        // 5. Build Initials
        $initials = 'AN';
        if ($name) {
            $words = preg_split('/\s+/', $name);
            if (count($words) >= 2) {
                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } else {
                $initials = strtoupper(substr($name, 0, 2));
            }
        } elseif ($email) {
            $initials = strtoupper(substr($email, 0, 2));
        }

        // 6. Parse structured fields
        $parsedFields = [];
        $filesList = [];
        $searchableParts = [
            (string)$entry->id,
            (string)$name,
            (string)$email,
            (string)$phone,
            (string)$company,
            (string)$position,
            (string)$entry->ip_address,
        ];

        foreach ($data as $k => $v) {
            if (in_array($k, ['_attribution', '_token', '_locale', '_honeypot', '_timestamp'])) {
                continue;
            }

            $label = $fieldMap[$k]['label'] ?? ucwords(str_replace(['_', '-'], ' ', (string)$k));
            $fieldType = $fieldMap[$k]['type'] ?? null;
            $valStr = is_array($v) ? implode(', ', array_filter(array_map('strval', $v))) : (string)$v;
            $searchableParts[] = $label . ' ' . $valStr;

            $isFile = false;
            $fileUrl = null;
            $fileName = null;

            if ($fieldType === 'file' || $fieldType === 'image' || 
                (is_string($v) && (str_contains($v, 'uploads/') || preg_match('/\.(pdf|docx?|xlsx?|pptx?|txt|zip|jpe?g|png|webp)$/i', $v)))) {
                $isFile = true;
                $fieldType = $fieldType ?: 'file';
                $fileUrl = $resolveFileUrl($v);
                $fileName = basename($v);
                $filesList[] = [
                    'label' => $label,
                    'key' => $k,
                    'name' => $fileName,
                    'url' => $fileUrl,
                    'ext' => strtolower(pathinfo($fileName, PATHINFO_EXTENSION)),
                ];
            } elseif (!$fieldType) {
                if (filter_var($v, FILTER_VALIDATE_EMAIL)) {
                    $fieldType = 'email';
                } elseif (filter_var($v, FILTER_VALIDATE_URL)) {
                    $fieldType = 'url';
                } elseif (is_string($v) && strlen($v) > 80) {
                    $fieldType = 'textarea';
                } elseif (is_array($v)) {
                    $fieldType = 'checkbox';
                } else {
                    $fieldType = 'text';
                }
            }

            $parsedFields[] = [
                'key' => $k,
                'label' => $label,
                'value' => $v,
                'display_value' => $valStr,
                'type' => $fieldType,
                'is_file' => $isFile,
                'file_url' => $fileUrl,
                'file_name' => $fileName,
            ];
        }

        return [
            'id' => $entry->id,
            'name' => $name ?: ($email ?: 'Anonymous Applicant'),
            'initials' => $initials,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'position' => $position,
            'fields' => $parsedFields,
            'files' => $filesList,
            'has_file' => count($filesList) > 0,
            'file_count' => count($filesList),
            'attribution' => [
                'os' => $attr['os'] ?? ($attr['device_type'] ?? null),
                'browser' => $attr['browser'] ?? null,
                'device_type' => $attr['device_type'] ?? null,
                'screen_resolution' => $attr['screen_resolution'] ?? null,
                'browser_language' => $attr['browser_language'] ?? null,
                'http_referrer' => $attr['http_referrer'] ?? ($attr['initial_referrer'] ?? null),
                'submission_page' => $attr['submission_page'] ?? null,
                'initial_landing_page' => $attr['initial_landing_page'] ?? null,
                'utm_source' => $attr['utm_source'] ?? null,
                'utm_medium' => $attr['utm_medium'] ?? null,
                'utm_campaign' => $attr['utm_campaign'] ?? null,
                'utm_content' => $attr['utm_content'] ?? null,
                'time_to_convert' => $attr['time_to_convert'] ?? null,
                'page_views_count' => $attr['page_views_count'] ?? null,
                'gclid' => $attr['gclid'] ?? null,
                'fbclid' => $attr['fbclid'] ?? null,
            ],
            'ip_address' => $entry->ip_address,
            'user_agent' => $entry->user_agent,
            'submitted_at' => $entry->created_at->format('M d, Y H:i'),
            'submitted_date' => $entry->created_at->format('M d, Y'),
            'submitted_time' => $entry->created_at->format('H:i'),
            'time_ago' => $entry->created_at->diffForHumans(),
            'raw_data' => $data,
            'searchable_text' => implode(' ', $searchableParts),
        ];
    })->values()->all();

    $formTranslations = $form->translations ?? [
        'id' => [
            'name' => '',
            'description' => '',
            'submit_button_text' => 'Kirim',
            'confirmations' => [
                'message' => 'Terima kasih atas pengajuan Anda. Tim kami akan segera menghubungi Anda.'
            ]
        ]
    ];

    $formFieldsData = array_map(function($f) {
        $adv = $f['advanced_settings'] ?? [];
        if (is_string($adv)) {
            $adv = json_decode($adv, true) ?? [];
        }
        $f['consent_text'] = $f['consent_text'] ?? ($adv['consent_text'] ?? ($adv['privacy_content'] ?? ''));
        $f['terms_text'] = $f['terms_text'] ?? ($adv['terms_text'] ?? '');
        $f['html_content'] = $f['html_content'] ?? ($adv['html_content'] ?? '');
        // Flatten translations for Alpine UI
        $trans = $f['translations'] ?? [];
        $f['translations_id_label'] = $trans['id']['label'] ?? '';
        $f['translations_id_placeholder'] = $trans['id']['placeholder'] ?? '';
        $f['translations_id_consent_text'] = $trans['id']['consent_text'] ?? '';
        // Normalize validation + flatten named rule state for the Alpine UI
        $validation = $f['validation'] ?? [];
        if (is_string($validation)) {
            $validation = json_decode($validation, true) ?? [];
        }
        $f['validation'] = $validation;
        $f['validation_corporate_email'] = ($validation['rule'] ?? null) === 'corporate_email';
        $f['validation_rule_message'] = $validation['rule_message'] ?? '';
        return $f;
    }, $form->fields ? $form->fields->toArray() : []);
@endphp

<script>
function formStudioController() {
    return {
        activeTab: '{{ $activeTab ?? 'fields' }}',
        editingLocale: 'en',
        name: @json($form->name),
        slug: @json($form->slug),
        description: @json($form->description ?? ''),
        isActive: @json($form->is_active ? '1' : '0'),
        submitButtonText: @json($form->submit_button_text ?? 'Submit'),
        themeSlot: @json($assignedSlot),
        translations: @json($formTranslations),
        
        // Confirmations & Spam
        confirmationType: @json($confirmationType),
        confirmationMessage: @json($confirmationMessage),
        redirectUrl: @json($redirectUrl),
        honeypot: @json((bool)$honeypot),
        captchaProvider: @json($captchaProvider),

        // Notifications
        notifyAdmin: @json((bool)$notifyAdmin),
        adminEmail: @json($adminEmail),
        adminSubject: @json($adminSubject),
        adminBody: @json($adminEmailBody),
        
        sendToUser: @json((bool)$sendToUser),
        userSubject: @json($userSubject),
        userBody: @json($userEmailBody),

        // Builder State
        fields: @json($formFieldsData),
        selectedFieldIndex: null,
        showFieldModal: false,
        settingsSubTab: 'general',

        // Notification helpers
        insertAdminPlaceholder(ph) {
            if (window.adminEmailEditor) {
                window.adminEmailEditor.focus();
                const range = window.adminEmailEditor.getSelection();
                const index = range ? range.index : 0;
                window.adminEmailEditor.insertText(index, ph);
            } else {
                this.adminBody += ph;
            }
        },
        insertUserPlaceholder(ph) {
            if (window.userEmailEditor) {
                window.userEmailEditor.focus();
                const range = window.userEmailEditor.getSelection();
                const index = range ? range.index : 0;
                window.userEmailEditor.insertText(index, ph);
            } else {
                this.userBody += ph;
            }
        },
        insertDownloadBtn(url, text) {
            const btnHtml = `<p style='margin-top: 15px; margin-bottom: 15px;'><a href='${url}' style='display: inline-block; background-color: #b82d25; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 9999px; font-weight: bold; text-transform: uppercase; font-size: 13px; letter-spacing: 1px;'>${text}</a></p>`;
            if (window.userEmailEditor) {
                const range = window.userEmailEditor.getSelection();
                const index = range ? range.index : 0;
                window.userEmailEditor.clipboard.dangerouslyPasteHTML(index, btnHtml);
            } else {
                this.userBody += btnHtml;
            }
        },

        // Serialize per-field validation JSON (preserves min/max/pattern,
        // adds or removes the corporate_email named rule from the toggle)
        serializeValidation(field) {
            const v = Object.assign({}, field.validation || {});
            if (field.validation_corporate_email) {
                v.rule = 'corporate_email';
                if (field.validation_rule_message) {
                    v.rule_message = field.validation_rule_message;
                } else {
                    delete v.rule_message;
                }
            } else {
                delete v.rule;
                delete v.rule_message;
            }
            return JSON.stringify(v);
        },

        // Builder actions
        addField(type) {
            const fieldId = 'field_' + Math.random().toString(36).substr(2, 6);
            const labelMap = { gdpr: 'Privacy Consent', terms: 'Terms & Conditions', vendor_solutions: 'Solution Needed', solution_needed: 'Solution Needed' };
            const label = labelMap[type] || (type.charAt(0).toUpperCase() + type.slice(1).replace('_', ' '));

            const defaultConsent = 'I consent to having my personal data processed and agree to the Privacy Policy.';
            const defaultTerms = 'I agree to the Terms and Conditions.';

            this.fields.push({
                field_id: fieldId,
                type: type,
                label: label,
                is_required: (type === 'gdpr' || type === 'terms') ? true : false,
                column_width: 'full',
                placeholder: '',
                help_text: '',
                options_text: ['select', 'radio', 'checkbox'].includes(type) ? 'Option 1|value_1\nOption 2|value_2' : '',
                conditional_logic: { enabled: false, conditions: [] },
                advanced_settings: {},
                validation: {},
                validation_corporate_email: false,
                validation_rule_message: '',
                consent_text: type === 'gdpr' ? defaultConsent : '',
                terms_text: type === 'terms' ? defaultTerms : '',
            });
            this.selectedFieldIndex = this.fields.length - 1;
            this.showFieldModal = false;
            this.initSortable();
        },
        removeField(index) {
            this.fields.splice(index, 1);
            if (this.selectedFieldIndex === index) this.selectedFieldIndex = null;
            else if (this.selectedFieldIndex > index) this.selectedFieldIndex--;
            this.initSortable();
        },
        moveFieldUp(index) {
            if (index > 0) {
                const item = this.fields.splice(index, 1)[0];
                this.fields.splice(index - 1, 0, item);
                this.selectedFieldIndex = index - 1;
                this.initSortable();
            }
        },
        moveFieldDown(index) {
            if (index < this.fields.length - 1) {
                const item = this.fields.splice(index, 1)[0];
                this.fields.splice(index + 1, 0, item);
                this.selectedFieldIndex = index + 1;
                this.initSortable();
            }
        },
        initSortable() {
            this.$nextTick(() => {
                const container = document.getElementById('sortable-fields-list');
                if (container && typeof Sortable !== 'undefined') {
                    if (container._sortable) {
                        container._sortable.destroy();
                    }
                    container._sortable = Sortable.create(container, {
                        handle: '.drag-handle',
                        animation: 200,
                        ghostClass: 'opacity-30',
                        chosenClass: 'bg-primary/5',
                        dragClass: 'shadow-2xl',
                        onEnd: (evt) => {
                            const oldIndex = evt.oldIndex;
                            const newIndex = evt.newIndex;
                            if (oldIndex !== undefined && newIndex !== undefined && oldIndex !== newIndex) {
                                const moved = this.fields.splice(oldIndex, 1)[0];
                                this.fields.splice(newIndex, 0, moved);
                                this.selectedFieldIndex = newIndex;
                            }
                        }
                    });
                }
            });
        },
        getFieldIcon(type) {
            const icons = {
                text: 'short_text',
                textarea: 'notes',
                email: 'mail',
                number: 'pin',
                phone: 'call',
                select: 'arrow_drop_down_circle',
                checkbox: 'check_box',
                radio: 'radio_button_checked',
                date: 'calendar_today',
                file: 'upload_file',
                section: 'view_headline',
                divider: 'horizontal_rule'
            };
            return icons[type] || 'widgets';
        },

        // Test email dispatcher
        sendingTest: false,
        async sendTest(type) {
            this.sendingTest = true;
            try {
                const res = await fetch('{{ route('admin.forms.test-email', $form) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email_type: type })
                });
                const data = await res.json();
                alert(data.message);
            } catch(e) {
                alert('Test email failed: ' + e.message);
            } finally {
                this.sendingTest = false;
            }
        },

        // AJAX Save
        saving: false,
        saveMessage: '',
        async saveForm() {
            this.saving = true;
            this.saveMessage = '';
            try {
                const formData = new FormData(this.$refs.studioForm);
                const res = await fetch(this.$refs.studioForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });
                const data = await res.json();
                if (data.success) {
                    this.saveMessage = '✓ Saved';
                    setTimeout(() => this.saveMessage = '', 2500);
                } else {
                    this.saveMessage = '✗ Error';
                    setTimeout(() => this.saveMessage = '', 4000);
                }
            } catch(e) {
                this.saveMessage = '✗ ' + e.message;
                setTimeout(() => this.saveMessage = '', 4000);
            } finally {
                this.saving = false;
            }
        },

        // Submissions & Entries DataGrid State
        entriesList: @json($entriesDataList),
        submissionSearch: '',
        activeEntry: null,
        showEntryDrawer: false,
        copiedSummary: false,
        copiedJson: false,

        get filteredEntries() {
            if (!this.submissionSearch || !this.submissionSearch.trim()) {
                return this.entriesList;
            }
            const q = this.submissionSearch.toLowerCase().trim();
            return this.entriesList.filter(entry => {
                return entry.searchable_text && entry.searchable_text.toLowerCase().includes(q);
            });
        },

        openEntry(entry) {
            this.activeEntry = entry;
            this.showEntryDrawer = true;
            this.copiedSummary = false;
            this.copiedJson = false;
        },

        closeEntry() {
            this.showEntryDrawer = false;
        },

        async copyEntrySummary() {
            if (!this.activeEntry) return;
            let text = `Submission #${this.activeEntry.id} - ${this.activeEntry.name}\n`;
            text += `Submitted: ${this.activeEntry.submitted_at}\n`;
            if (this.activeEntry.email) text += `Email: ${this.activeEntry.email}\n`;
            if (this.activeEntry.phone) text += `Phone: ${this.activeEntry.phone}\n`;
            if (this.activeEntry.company) text += `Company: ${this.activeEntry.company}\n`;
            text += `\n--- Submitted Fields ---\n`;
            this.activeEntry.fields.forEach(f => {
                if (f.is_file) {
                    text += `${f.label}: ${f.file_url || f.display_value}\n`;
                } else {
                    text += `${f.label}: ${f.display_value}\n`;
                }
            });
            try {
                await navigator.clipboard.writeText(text);
                this.copiedSummary = true;
                setTimeout(() => this.copiedSummary = false, 2200);
            } catch (err) {
                console.error('Clipboard copy failed', err);
            }
        },

        async copyRawJson() {
            if (!this.activeEntry) return;
            try {
                await navigator.clipboard.writeText(JSON.stringify(this.activeEntry.raw_data, null, 2));
                this.copiedJson = true;
                setTimeout(() => this.copiedJson = false, 2200);
            } catch (err) {
                console.error('Clipboard copy failed', err);
            }
        },

        async deleteEntry(entryId) {
            if (!confirm('Are you sure you want to permanently delete submission #' + entryId + '? This action cannot be undone.')) {
                return;
            }
            try {
                const url = '{{ url('ctrlpanel/forms/entries') }}/' + entryId;
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    this.entriesList = this.entriesList.filter(e => e.id !== entryId);
                    if (this.activeEntry && this.activeEntry.id === entryId) {
                        this.closeEntry();
                    }
                } else {
                    alert('Failed to delete entry: ' + (data.message || 'Unknown error'));
                }
            } catch(err) {
                alert('Delete failed: ' + err.message);
            }
        }
    };
}
document.addEventListener('alpine:init', () => {
    Alpine.data('formStudioController', formStudioController);
});
</script>

<div class="h-full flex flex-col w-full bg-[#F4F5F6] dark:bg-[#0B0B0B]"
    x-data="formStudioController()"
    x-init="initSortable()">

    {{-- Persistent Workspace Top Bar --}}
    <div class="h-16 px-4 md:px-6 bg-white dark:bg-[#1A1A1A] border-b border-gray-200 dark:border-[#272B30] flex items-center justify-between shrink-0 shadow-sm z-30 gap-4 overflow-x-auto no-scrollbar">
        {{-- Left Section: Navigation & Form Meta --}}
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('admin.forms.index') }}" 
                class="p-2 rounded-xl text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all" title="Back to Forms list">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>

            <div class="flex items-center gap-2.5">
                <div class="text-sm md:text-base font-bold text-[#111827] dark:text-[#FCFCFC] truncate max-w-[220px]"
                     x-text="editingLocale === 'id' && translations.id.name ? translations.id.name : name"></div>

                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0"
                    :class="isActive == '1' ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400'">
                    <span class="w-1.5 h-1.5 rounded-full" :class="isActive == '1' ? 'bg-emerald-500' : 'bg-gray-500'"></span>
                    <span x-text="isActive == '1' ? 'Active' : 'Inactive'"></span>
                </span>

                {{-- Unified Language Switcher --}}
                <div class="flex items-center p-0.5 bg-gray-100 dark:bg-[#0B0B0B] rounded-xl border border-gray-200 dark:border-[#272B30] text-xs font-bold shrink-0">
                    <button type="button" @click="editingLocale = 'en'"
                        :class="editingLocale === 'en' ? 'bg-white dark:bg-[#1A1A1A] text-primary shadow-sm font-black' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white'"
                        class="px-2.5 py-1 rounded-lg transition-all flex items-center gap-1" title="Switch editor context to English">
                        <span>🇬🇧</span> <span class="text-[11px]">EN</span>
                    </button>
                    <button type="button" @click="editingLocale = 'id'"
                        :class="editingLocale === 'id' ? 'bg-white dark:bg-[#1A1A1A] text-primary shadow-sm font-black' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white'"
                        class="px-2.5 py-1 rounded-lg transition-all flex items-center gap-1" title="Switch editor context to Bahasa Indonesia">
                        <span>🇮🇩</span> <span class="text-[11px]">ID</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Right Section: Navigation Tabs & Studio Action Buttons --}}
        <div class="flex items-center gap-3 shrink-0">
            {{-- 4 Primary Studio Tabs --}}
            <div class="flex items-center gap-1 p-1 bg-[#F4F5F6] dark:bg-[#0B0B0B] rounded-2xl border border-gray-200 dark:border-[#272B30]">
                <button type="button" @click="activeTab = 'fields'" 
                    :class="activeTab === 'fields' ? 'bg-white dark:bg-[#1A1A1A] text-primary font-bold shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white font-medium'"
                    class="px-3.5 py-1.5 rounded-xl text-xs transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[17px]">build</span>
                    <span>Fields</span>
                </button>

                <button type="button" @click="activeTab = 'settings'" 
                    :class="activeTab === 'settings' ? 'bg-white dark:bg-[#1A1A1A] text-primary font-bold shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white font-medium'"
                    class="px-3.5 py-1.5 rounded-xl text-xs transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[17px]">settings</span>
                    <span>Settings</span>
                </button>

                <button type="button" @click="activeTab = 'emails'" 
                    :class="activeTab === 'emails' ? 'bg-white dark:bg-[#1A1A1A] text-primary font-bold shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white font-medium'"
                    class="px-3.5 py-1.5 rounded-xl text-xs transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[17px]">mail</span>
                    <span class="hidden lg:inline">Email Notifications</span>
                    <span class="inline lg:hidden">Emails</span>
                </button>

                <button type="button" @click="activeTab = 'entries'" 
                    :class="activeTab === 'entries' ? 'bg-white dark:bg-[#1A1A1A] text-primary font-bold shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white font-medium'"
                    class="px-3.5 py-1.5 rounded-xl text-xs transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[17px]">inbox</span>
                    <span>Submissions (<span x-text="entriesList.length"></span>)</span>
                </button>
            </div>

            {{-- Action Buttons --}}
            <a href="{{ url('/') }}" target="_blank" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-all flex items-center gap-1.5" title="Frontend Preview">
                <span class="material-symbols-outlined text-sm">open_in_new</span>
                <span class="hidden lg:inline">Preview</span>
            </a>

            <button type="button" @click="saveForm()" :disabled="saving"
                class="px-4 py-1.5 rounded-xl text-xs font-bold bg-primary text-white hover:bg-red-700 transition-all shadow-md flex items-center gap-1.5 disabled:opacity-60">
                <span class="material-symbols-outlined text-sm" x-show="!saving">save</span>
                <svg x-show="saving" class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                <span x-text="saving ? 'Saving...' : 'Save'"></span>
            </button>

            {{-- Save status toast --}}
            <span x-show="saveMessage" x-transition.opacity.duration.300ms
                class="text-[11px] font-bold px-2.5 py-1 rounded-lg shrink-0"
                :class="saveMessage.startsWith('✓') ? 'text-emerald-700 bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-500/10' : 'text-red-700 bg-red-100 dark:text-red-400 dark:bg-red-500/10'"
                x-text="saveMessage"></span>
        </div>
    </div>

    {{-- Main Studio Form --}}
    <form x-ref="studioForm" action="{{ route('admin.forms.studio.save', $form) }}" method="POST" class="flex-1 flex overflow-hidden">
        @csrf
        <input type="hidden" name="tab" x-model="activeTab">
        <input type="hidden" name="name" x-model="name">
        <input type="hidden" name="slug" x-model="slug">
        <input type="hidden" name="translations[id][name]" x-model="translations.id.name">

        {{-- TAB 1: 🛠️ FIELDS BUILDER --}}
        <div x-show="activeTab === 'fields'" class="flex-1 flex w-full overflow-hidden">
            {{-- Left Canvas --}}
            <div class="flex-1 overflow-y-auto p-6 md:p-10 no-scrollbar">
                <div class="max-w-3xl mx-auto space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-[#6F767E] uppercase tracking-widest">Canvas Form Fields</h3>
                        <span class="text-xs font-bold text-[#6F767E]" x-text="fields.length + ' fields'"></span>
                    </div>

                    <div id="sortable-fields-list" class="builder-dropzone min-h-[450px] rounded-3xl p-6 md:p-8 flex flex-col gap-4 border border-gray-200 dark:border-[#272B30]/40 relative bg-white/50 dark:bg-[#1A1A1A]/30">
                        <template x-if="fields.length === 0">
                            <div class="text-center py-16 text-[#6F767E]">
                                <span class="material-symbols-outlined text-6xl mb-4 block opacity-30">add_task</span>
                                <p class="font-bold text-base text-[#111827] dark:text-[#FCFCFC]">No Form Fields Yet</p>
                                <p class="text-xs">Click "Add New Field" below to build your form canvas.</p>
                            </div>
                        </template>

                        <template x-for="(field, index) in fields" :key="index">
                            <div @click="selectedFieldIndex = index"
                                class="p-5 rounded-2xl border transition-all cursor-pointer relative group bg-white dark:bg-[#1A1A1A]"
                                :class="selectedFieldIndex === index ? 'border-primary shadow-md ring-2 ring-primary/20' : 'border-gray-200 dark:border-[#272B30] hover:border-gray-300'">
                                
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div class="drag-handle cursor-grab active:cursor-grabbing p-1 text-gray-400 hover:text-primary rounded-lg hover:bg-gray-100 dark:hover:bg-[#0B0B0B] transition-colors" title="Drag to reorder">
                                            <span class="material-symbols-outlined text-xl block">drag_indicator</span>
                                        </div>
                                        <div class="h-9 w-9 rounded-xl bg-gray-100 dark:bg-[#0B0B0B] text-primary flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-lg" x-text="getFieldIcon(field.type)"></span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]" x-text="(editingLocale === 'id' && field.translations_id_label) ? field.translations_id_label : (field.label || 'Untitled Field')"></div>
                                            <div class="text-xs text-[#6F767E] font-mono" x-text="field.field_id"></div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click.stop="moveFieldUp(index)" :disabled="index === 0" :class="index === 0 ? 'opacity-20 cursor-not-allowed' : 'hover:text-primary hover:bg-gray-100 dark:hover:bg-[#0B0B0B]'" class="p-1 text-gray-400 rounded-lg transition-colors" title="Move field up">
                                            <span class="material-symbols-outlined text-lg block">arrow_upward</span>
                                        </button>
                                        <button type="button" @click.stop="moveFieldDown(index)" :disabled="index === fields.length - 1" :class="index === fields.length - 1 ? 'opacity-20 cursor-not-allowed' : 'hover:text-primary hover:bg-gray-100 dark:hover:bg-[#0B0B0B]'" class="p-1 text-gray-400 rounded-lg transition-colors" title="Move field down">
                                            <span class="material-symbols-outlined text-lg block">arrow_downward</span>
                                        </button>
                                        <span x-show="field.is_required" class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400">Required</span>
                                        <button type="button" @click.stop="removeField(index)" class="p-1 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10" title="Delete field">
                                            <span class="material-symbols-outlined text-lg block">delete</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Hidden Form Field Inputs for POST --}}
                                <input type="hidden" :name="`fields[${index}][label]`" x-model="field.label">
                                <input type="hidden" :name="`fields[${index}][field_id]`" x-model="field.field_id">
                                <input type="hidden" :name="`fields[${index}][type]`" x-model="field.type">
                                <input type="hidden" :name="`fields[${index}][is_required]`" :value="field.is_required ? '1' : '0'">
                                <input type="hidden" :name="`fields[${index}][column_width]`" x-model="field.column_width">
                                <input type="hidden" :name="`fields[${index}][placeholder]`" x-model="field.placeholder">
                                <input type="hidden" :name="`fields[${index}][help_text]`" x-model="field.help_text">
                                <input type="hidden" :name="`fields[${index}][validation]`" :value="serializeValidation(field)">
                                <input type="hidden" :name="`fields[${index}][options]`" x-model="field.options_text">
                                <input type="hidden" :name="`fields[${index}][consent_text]`" x-model="field.consent_text">
                                <input type="hidden" :name="`fields[${index}][terms_text]`" x-model="field.terms_text">
                                <input type="hidden" :name="`fields[${index}][html_content]`" x-model="field.html_content">
                                <input type="hidden" :name="`fields[${index}][translations_id_label]`" x-model="field.translations_id_label">
                                <input type="hidden" :name="`fields[${index}][translations_id_placeholder]`" x-model="field.translations_id_placeholder">
                                <input type="hidden" :name="`fields[${index}][translations_id_consent_text]`" x-model="field.translations_id_consent_text">
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-center">
                        <button type="button" @click="showFieldModal = true"
                            class="px-6 py-3 bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-full text-sm font-bold text-[#111827] dark:text-[#FCFCFC] hover:border-primary transition-all flex items-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-primary text-xl">add_circle</span>
                            <span>Add New Field</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right Field Inspector Off-Canvas Drawer --}}
            <!-- Drawer Backdrop -->
            <div x-show="selectedFieldIndex !== null" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="selectedFieldIndex = null"
                 class="fixed inset-0 bg-black/40 backdrop-blur-xs z-40"
                 style="display: none;"></div>

            <!-- Off-Canvas Drawer Panel -->
            <aside x-show="selectedFieldIndex !== null" 
                   x-transition:enter="transition transform ease-out duration-300"
                   x-transition:enter-start="translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition transform ease-in duration-200"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="translate-x-full"
                   class="fixed inset-y-0 right-0 z-50 w-full max-w-[400px] bg-white dark:bg-[#1A1A1A] border-l border-gray-200 dark:border-[#272B30] shadow-2xl p-6 overflow-y-auto"
                   style="display: none;">
                <template x-if="selectedFieldIndex !== null && fields[selectedFieldIndex]">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-[#272B30]">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-xl">tune</span>
                                <h4 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC] uppercase tracking-widest">Field Inspector</h4>
                            </div>
                            <button type="button" @click="selectedFieldIndex = null" class="p-1.5 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-[#272B30] transition-colors" title="Close inspector">
                                <span class="material-symbols-outlined text-xl block">close</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-bold text-[#6F767E]">Field Label</label>
                                    <span class="text-[10px] font-black text-primary px-2 py-0.5 rounded-md bg-primary/10 uppercase" x-text="editingLocale === 'en' ? '🇬🇧 English' : '🇮🇩 Indonesia'"></span>
                                </div>
                                <input x-show="editingLocale === 'en'" type="text" x-model="fields[selectedFieldIndex].label"
                                    class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium px-3 text-[#111827] dark:text-[#FCFCFC]" placeholder="Field Label (EN)">
                                <input x-show="editingLocale === 'id'" type="text" x-model="fields[selectedFieldIndex].translations_id_label"
                                    class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium px-3 text-[#111827] dark:text-[#FCFCFC]" :placeholder="fields[selectedFieldIndex].label || 'Field Label (ID)'">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#6F767E] mb-1">Field ID (Unique Identifier)</label>
                                <input type="text" x-model="fields[selectedFieldIndex].field_id"
                                    class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-mono px-3 text-[#111827] dark:text-[#FCFCFC]">
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-bold text-[#6F767E]">Placeholder Text</label>
                                    <span class="text-[10px] font-black text-primary px-2 py-0.5 rounded-md bg-primary/10 uppercase" x-text="editingLocale === 'en' ? '🇬🇧 English' : '🇮🇩 Indonesia'"></span>
                                </div>
                                <input x-show="editingLocale === 'en'" type="text" x-model="fields[selectedFieldIndex].placeholder"
                                    class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium px-3 text-[#111827] dark:text-[#FCFCFC]" placeholder="Placeholder (EN)">
                                <input x-show="editingLocale === 'id'" type="text" x-model="fields[selectedFieldIndex].translations_id_placeholder"
                                    class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium px-3 text-[#111827] dark:text-[#FCFCFC]" :placeholder="fields[selectedFieldIndex].placeholder || 'Placeholder (ID)'">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#6F767E] mb-1">Column Width</label>
                                <select x-model="fields[selectedFieldIndex].column_width"
                                    class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-medium px-3 text-[#111827] dark:text-[#FCFCFC]">
                                    <option value="full">Full Width (100%)</option>
                                    <option value="half">Half Width (50%)</option>
                                    <option value="third">One Third (33%)</option>
                                </select>
                            </div>

                            <label class="flex items-center gap-3 cursor-pointer pt-2">
                                <input type="checkbox" x-model="fields[selectedFieldIndex].is_required" class="rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Required Field</span>
                            </label>

                            {{-- Corporate email validation (email fields only) --}}
                            <template x-if="fields[selectedFieldIndex].type === 'email'">
                                <div class="space-y-3 pt-3 border-t border-gray-100 dark:border-[#272B30]">
                                    <label class="block text-xs font-bold text-[#6F767E]">Corporate Email Validation</label>
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" x-model="fields[selectedFieldIndex].validation_corporate_email" class="rounded border-gray-300 text-primary focus:ring-primary">
                                        <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">Reject free email providers (Gmail, Yahoo, etc.)</span>
                                    </label>
                                    <input x-show="fields[selectedFieldIndex].validation_corporate_email" type="text" x-model="fields[selectedFieldIndex].validation_rule_message"
                                        class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs px-3 text-[#111827] dark:text-[#FCFCFC]"
                                        placeholder="Custom error message (optional)">
                                </div>
                            </template>

                            {{-- GDPR consent text --}}
                            <template x-if="fields[selectedFieldIndex].type === 'gdpr'">
                                <div class="space-y-1 pt-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-[#6F767E]">Consent Text <span class="font-normal text-[#6F767E]/50">(HTML supported)</span></label>
                                        <span class="text-[10px] font-black text-primary px-2 py-0.5 rounded-md bg-primary/10 uppercase" x-text="editingLocale === 'en' ? '🇬🇧 English' : '🇮🇩 Indonesia'"></span>
                                    </div>
                                    <textarea x-show="editingLocale === 'en'" x-model="fields[selectedFieldIndex].consent_text" rows="3"
                                        class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs p-3 text-[#111827] dark:text-[#FCFCFC] resize-none" placeholder="I consent to having my personal data processed..."></textarea>
                                    <textarea x-show="editingLocale === 'id'" x-model="fields[selectedFieldIndex].translations_id_consent_text" rows="3"
                                        class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs p-3 text-[#111827] dark:text-[#FCFCFC] resize-none" placeholder="Masukkan teks persetujuan dalam Bahasa Indonesia..."></textarea>
                                </div>
                            </template>

                            {{-- Terms text --}}
                            <template x-if="fields[selectedFieldIndex].type === 'terms'">
                                <div class="space-y-1 pt-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-[#6F767E]">Terms Text <span class="font-normal text-[#6F767E]/50">(HTML supported)</span></label>
                                        <span class="text-[10px] font-black text-primary px-2 py-0.5 rounded-md bg-primary/10 uppercase" x-text="editingLocale === 'en' ? '🇬🇧 English' : '🇮🇩 Indonesia'"></span>
                                    </div>
                                    <textarea x-show="editingLocale === 'en'" x-model="fields[selectedFieldIndex].terms_text" rows="3"
                                        class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs p-3 text-[#111827] dark:text-[#FCFCFC] resize-none" placeholder="I agree to the Terms and Conditions."></textarea>
                                    <textarea x-show="editingLocale === 'id'" x-model="fields[selectedFieldIndex].translations_id_terms_text" rows="3"
                                        class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs p-3 text-[#111827] dark:text-[#FCFCFC] resize-none" placeholder="Teks Syarat & Ketentuan dalam Bahasa Indonesia"></textarea>
                                </div>
                            </template>

                            <template x-if="['select', 'radio', 'checkbox'].includes(fields[selectedFieldIndex].type)">
                                <div class="space-y-1 pt-2">
                                    <label class="block text-xs font-bold text-[#6F767E]">Options List (Label|Value)</label>
                                    <textarea x-model="fields[selectedFieldIndex].options_text" rows="4"
                                        class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-mono p-3 text-[#111827] dark:text-[#FCFCFC] resize-none"></textarea>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </aside>
        </div>

        {{-- TAB 2: ⚙️ SETTINGS --}}
        <div x-show="activeTab === 'settings'" class="flex-1 overflow-y-auto p-6 md:p-10 no-scrollbar w-full">
            <div class="max-w-4xl mx-auto space-y-8">
                {{-- Sub Tab Navigation --}}
                <div class="flex items-center gap-2 border-b border-gray-200 dark:border-[#272B30] pb-4">
                    <button type="button" @click="settingsSubTab = 'general'"
                        :class="settingsSubTab === 'general' ? 'border-primary text-primary font-bold' : 'border-transparent text-[#6F767E] hover:text-[#111827] dark:hover:text-white'"
                        class="px-4 py-2 border-b-2 text-xs transition-all">General & Theme Assignment</button>

                    <button type="button" @click="settingsSubTab = 'confirmations'"
                        :class="settingsSubTab === 'confirmations' ? 'border-primary text-primary font-bold' : 'border-transparent text-[#6F767E] hover:text-[#111827] dark:hover:text-white'"
                        class="px-4 py-2 border-b-2 text-xs transition-all">Confirmation Settings</button>

                    <button type="button" @click="settingsSubTab = 'spam'"
                        :class="settingsSubTab === 'spam' ? 'border-primary text-primary font-bold' : 'border-transparent text-[#6F767E] hover:text-[#111827] dark:hover:text-white'"
                        class="px-4 py-2 border-b-2 text-xs transition-all">Spam & Protection</button>
                </div>

                {{-- Sub-section 1: General & Theme Assignment --}}
                <div x-show="settingsSubTab === 'general'" class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-8 space-y-6 shadow-sm">
                    <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">General Configuration & Theme Slot</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Form Status</label>
                            <select name="is_active" x-model="isActive" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Submit Button Label</label>
                                <span class="text-[10px] font-black text-primary px-2 py-0.5 rounded-md bg-primary/10 uppercase" x-text="editingLocale === 'en' ? '🇬🇧 English' : '🇮🇩 Indonesia'"></span>
                            </div>
                            <input x-show="editingLocale === 'en'" type="text" name="submit_button_text" x-model="submitButtonText" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]">
                            <input x-show="editingLocale === 'id'" type="text" name="translations[id][submit_button_text]" x-model="translations.id.submit_button_text" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]" placeholder="Kirim">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Form Title / Name</label>
                            <span class="text-[10px] font-black text-primary px-2 py-0.5 rounded-md bg-primary/10 uppercase" x-text="editingLocale === 'en' ? '🇬🇧 English' : '🇮🇩 Indonesia'"></span>
                        </div>
                        <input x-show="editingLocale === 'en'" type="text" name="name" x-model="name" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]" placeholder="Form Title in English">
                        <input x-show="editingLocale === 'id'" type="text" name="translations[id][name]" x-model="translations.id.name" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]" placeholder="Judul Form dalam Bahasa Indonesia">
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Form Description</label>
                            <span class="text-[10px] font-black text-primary px-2 py-0.5 rounded-md bg-primary/10 uppercase" x-text="editingLocale === 'en' ? '🇬🇧 English' : '🇮🇩 Indonesia'"></span>
                        </div>
                        <textarea x-show="editingLocale === 'en'" name="description" x-model="description" rows="3" class="w-full bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl p-4 text-[#111827] dark:text-[#FCFCFC] resize-none"></textarea>
                        <textarea x-show="editingLocale === 'id'" name="translations[id][description]" x-model="translations.id.description" rows="3" class="w-full bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl p-4 text-[#111827] dark:text-[#FCFCFC] resize-none" placeholder="Deskripsi form dalam Bahasa Indonesia"></textarea>
                    </div>

                    {{-- Theme Location Assignment --}}
                    <div class="p-6 rounded-2xl bg-primary/5 border border-primary/20 space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-2xl">grid_view</span>
                            <div>
                                <h4 class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Theme Position / Slot Assignment</h4>
                                <p class="text-xs text-[#6F767E]">Assign which theme location slot this form renders in on the frontend.</p>
                            </div>
                        </div>

                        <select name="theme_slot" x-model="themeSlot" class="w-full h-11 bg-white dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-sm font-bold rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]">
                            <option value="">-- Unassigned (Manual Shortcode Only) --</option>
                            @foreach($placeholders as $key => $placeholder)
                                @php
                                    $slotKey = is_array($placeholder) ? ($placeholder['key'] ?? $key) : $key;
                                    $slotLabel = is_array($placeholder) ? ($placeholder['label'] ?? $slotKey) : $placeholder;
                                @endphp
                                <option value="{{ $slotKey }}" {{ (string)$assignedSlot === (string)$slotKey ? 'selected' : '' }}>
                                    {{ $slotLabel }} (slot: {{ $slotKey }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Sub-section 2: Confirmation Settings --}}
                <div x-show="settingsSubTab === 'confirmations'" class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-8 space-y-6 shadow-sm">
                    <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Post-Submission Behavior</h3>

                    <div class="space-y-4">
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">After Submission Action</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="confirmations[type]" value="message" x-model="confirmationType" class="text-primary focus:ring-primary">
                                <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Show Success Message</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="confirmations[type]" value="redirect" x-model="confirmationType" class="text-primary focus:ring-primary">
                                <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Redirect to Custom URL</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="confirmationType === 'message'" class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Success Message</label>
                            <span class="text-[10px] font-black text-primary px-2 py-0.5 rounded-md bg-primary/10 uppercase" x-text="editingLocale === 'en' ? '🇬🇧 English' : '🇮🇩 Indonesia'"></span>
                        </div>
                        <textarea x-show="editingLocale === 'en'" name="confirmations[message]" x-model="confirmationMessage" rows="4" class="w-full bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl p-4 text-[#111827] dark:text-[#FCFCFC] resize-none"></textarea>
                        <textarea x-show="editingLocale === 'id'" name="translations[id][confirmations][message]" x-model="translations.id.confirmations.message" rows="4" class="w-full bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl p-4 text-[#111827] dark:text-[#FCFCFC] resize-none" placeholder="Terima kasih atas pengajuan Anda. Tim spesialis kami akan segera menghubungi Anda."></textarea>
                    </div>

                    <div x-show="confirmationType === 'redirect'" class="space-y-2">
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Redirect URL</label>
                        <input type="url" name="confirmations[redirect_url]" x-model="redirectUrl" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]" placeholder="{{ url('/thank-you-submission/') }}">
                    </div>
                </div>

                {{-- Sub-section 3: Spam Protection --}}
                <div x-show="settingsSubTab === 'spam'" class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-8 space-y-6 shadow-sm">
                    <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Spam & Bot Protection</h3>

                    <label class="flex items-center gap-3 cursor-pointer p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B]">
                        <input type="checkbox" name="spam_protection[honeypot]" value="1" x-model="honeypot" class="rounded border-gray-300 text-primary focus:ring-primary">
                        <div>
                            <span class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]">Honeypot Trap Protection</span>
                            <p class="text-xs text-[#6F767E]">Invisible trap field to block automated form bots automatically.</p>
                        </div>
                    </label>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">CAPTCHA Service Provider</label>
                        <select name="spam_protection[captcha_provider]" x-model="captchaProvider" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]">
                            <option value="none">No CAPTCHA</option>
                            <option value="recaptcha_v2">Google reCAPTCHA v2</option>
                            <option value="recaptcha_v3">Google reCAPTCHA v3 (Invisible)</option>
                            <option value="turnstile">Cloudflare Turnstile</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: 📧 EMAIL NOTIFICATIONS --}}
        <div x-show="activeTab === 'emails'" class="flex-1 overflow-y-auto p-6 md:p-10 no-scrollbar w-full space-y-8">
            <div class="max-w-4xl mx-auto space-y-8">
                {{-- Section 1: Admin Alert --}}
                <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-8 space-y-6 shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-[#272B30]">
                        <div>
                            <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">1. Admin Email Notification</h3>
                            <p class="text-xs text-[#6F767E]">Send immediate email alerts to admin when a form is submitted.</p>
                        </div>
                        
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notifications[notify_admin]" value="1" x-model="notifyAdmin" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ml-3 text-xs font-bold text-[#111827] dark:text-[#FCFCFC]" x-text="notifyAdmin ? 'ON' : 'OFF'"></span>
                        </label>
                    </div>

                    <div x-show="notifyAdmin" x-collapse class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Admin Recipient Email</label>
                                <input type="email" name="notifications[admin_email]" x-model="adminEmail" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Email Subject</label>
                                <input type="text" name="notifications[subject]" x-model="adminSubject" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]">
                            </div>
                        </div>

                        {{-- WYSIWYG Admin --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Admin Email Content (WYSIWYG)</label>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="insertAdminPlaceholder('{submission_table}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-[#111827] dark:text-[#FCFCFC]">{submission_table}</button>
                                    <button type="button" @click="insertAdminPlaceholder('{admin_url}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-[#111827] dark:text-[#FCFCFC]">{admin_url}</button>
                                </div>
                            </div>
                            <input type="hidden" name="notifications[admin_email_body]" x-model="adminBody" id="admin_email_body_input">
                            <div id="quill-admin-editor" class="bg-white dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] rounded-2xl border border-gray-200 dark:border-[#272B30] min-h-[180px]"></div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" @click="sendTest('admin')" :disabled="sendingTest" class="px-4 py-2 rounded-xl text-xs font-bold bg-gray-100 dark:bg-[#272B30] text-[#111827] dark:text-white hover:bg-gray-200 transition-all flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm">send</span>
                                <span>Send Test Admin Email</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Section 2: User Auto-Responder --}}
                <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-8 space-y-6 shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-[#272B30]">
                        <div>
                            <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">2. Visitor Auto-Responder Email</h3>
                            <p class="text-xs text-[#6F767E]">Send an automated response email back to the visitor who submitted the form.</p>
                        </div>
                        
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="notifications[send_to_user]" value="1" x-model="sendToUser" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ml-3 text-xs font-bold text-[#111827] dark:text-[#FCFCFC]" x-text="sendToUser ? 'ON' : 'OFF'"></span>
                        </label>
                    </div>

                    <div x-show="sendToUser" x-collapse class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">User Email Subject</label>
                            <input type="text" name="notifications[user_subject]" x-model="userSubject" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]">
                        </div>

                        {{-- CTA Download Tool --}}
                        <div class="p-4 rounded-2xl bg-primary/5 border border-primary/20 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary text-2xl">download_for_offline</span>
                                <div>
                                    <h4 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">CTA Download Button Tool</h4>
                                    <p class="text-xs text-[#6F767E]">Insert downloadable Guide PDF button into email body.</p>
                                </div>
                            </div>

                            <button type="button"
                                @click="
                                    const url = prompt('Enter File Download URL:', '{{ url('themes/cdt/assets/banner_hero-DHYDqbF8.jpg') }}');
                                    const text = prompt('Enter Button Label:', 'Download Digital Solution Guide');
                                    if (url && text) insertDownloadBtn(url, text);
                                "
                                class="px-4 py-2 rounded-xl text-xs font-bold bg-primary text-white hover:bg-red-700 transition-all flex items-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-sm">add_link</span>
                                <span>Insert Download Button</span>
                            </button>
                        </div>

                        {{-- WYSIWYG User --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">User Email Content (WYSIWYG)</label>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="insertUserPlaceholder('{name}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-[#111827] dark:text-[#FCFCFC]">{name}</button>
                                    <button type="button" @click="insertUserPlaceholder('{corporate_email}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-[#111827] dark:text-[#FCFCFC]">{email}</button>
                                </div>
                            </div>
                            <input type="hidden" name="notifications[user_email_body]" x-model="userBody" id="user_email_body_input">
                            <div id="quill-user-editor" class="bg-white dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] rounded-2xl border border-gray-200 dark:border-[#272B30] min-h-[220px]"></div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" @click="sendTest('user')" :disabled="sendingTest" class="px-4 py-2 rounded-xl text-xs font-bold bg-gray-100 dark:bg-[#272B30] text-[#111827] dark:text-white hover:bg-gray-200 transition-all flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm">send</span>
                                <span>Send Test User Email</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 4: 📊 SUBMISSIONS & ENTRIES --}}
        <div x-show="activeTab === 'entries'" class="flex-1 overflow-y-auto p-6 md:p-10 no-scrollbar w-full space-y-6">
            <div class="max-w-6xl mx-auto space-y-6">

                {{-- Metric Summary Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-2xl p-4 shadow-sm relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-[#6F767E]">Total Submissions</p>
                                <h4 class="text-2xl font-black text-[#111827] dark:text-white mt-1" x-text="entriesList.length"></h4>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">inbox</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-2xl p-4 shadow-sm relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-[#6F767E]">Received Today</p>
                                <h4 class="text-2xl font-black text-[#111827] dark:text-white mt-1">{{ $todaySubmissions }}</h4>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">today</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-2xl p-4 shadow-sm relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-[#6F767E]">This Week</p>
                                <h4 class="text-2xl font-black text-[#111827] dark:text-white mt-1">{{ $weekSubmissions }}</h4>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">date_range</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-2xl p-4 shadow-sm relative overflow-hidden group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-[#6F767E]">Latest Activity</p>
                                <h4 class="text-xs font-bold text-[#111827] dark:text-white mt-2 truncate max-w-[140px]">{{ $latestSubmission }}</h4>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                <span class="material-symbols-outlined text-2xl">schedule</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Toolbar: Search & Export --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#1A1A1A] p-4 rounded-2xl border border-gray-200 dark:border-[#272B30] shadow-sm">
                    <div class="flex items-center gap-3">
                        <h3 class="text-sm font-black text-[#111827] dark:text-[#FCFCFC] tracking-tight">Submissions DataGrid</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                            <span x-text="filteredEntries.length"></span> of <span x-text="entriesList.length"></span>
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Instant Alpine Search Input --}}
                        <div class="relative min-w-[240px] sm:min-w-[320px]">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">search</span>
                            <input 
                                type="text" 
                                x-model="submissionSearch" 
                                placeholder="Search applicant, email, phone, details..." 
                                class="w-full pl-9 pr-8 py-2 text-xs rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] text-[#111827] dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                            />
                            <button 
                                type="button" 
                                x-show="submissionSearch" 
                                @click="submissionSearch = ''" 
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                            >
                                <span class="material-symbols-outlined text-base">close</span>
                            </button>
                        </div>

                        {{-- Export Excel --}}
                        <a href="{{ route('admin.forms.export', $form) }}?format=xlsx" 
                            class="px-3.5 py-2 rounded-xl text-xs font-bold bg-[#F4F5F6] dark:bg-[#0B0B0B] hover:bg-primary hover:text-white border border-gray-200 dark:border-[#272B30] text-[#111827] dark:text-white transition-all flex items-center gap-1.5 shadow-sm shrink-0">
                            <span class="material-symbols-outlined text-base">download</span>
                            <span class="hidden md:inline">Export Excel</span>
                        </a>
                    </div>
                </div>

                {{-- Submissions Table Card --}}
                <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-[#272B30] bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#6F767E] uppercase font-bold tracking-wider">
                                    <th class="py-4 px-6">Applicant / Contact</th>
                                    <th class="py-4 px-6">Submitted Details</th>
                                    <th class="py-4 px-6">Source & Device</th>
                                    <th class="py-4 px-6">Submitted Date</th>
                                    <th class="py-4 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-[#272B30]">
                                <template x-for="entry in filteredEntries" :key="entry.id">
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-[#272B30]/30 transition-colors group">
                                        {{-- Column 1: Applicant & Contact --}}
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-black text-xs flex items-center justify-center shadow-sm shrink-0"
                                                     x-text="entry.initials"></div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-sm text-[#111827] dark:text-[#FCFCFC] truncate hover:text-primary cursor-pointer"
                                                         @click="openEntry(entry)"
                                                         x-text="entry.name"></div>
                                                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                                        <template x-if="entry.email">
                                                            <a :href="'mailto:' + entry.email" 
                                                               class="text-[#6F767E] hover:text-primary transition-colors flex items-center gap-1 text-[11px] truncate">
                                                                <span class="material-symbols-outlined text-[13px]">mail</span>
                                                                <span x-text="entry.email"></span>
                                                            </a>
                                                        </template>
                                                        <template x-if="entry.phone">
                                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-[#272B30] px-1.5 py-0.5 rounded">
                                                                <span class="material-symbols-outlined text-[12px]">call</span>
                                                                <span x-text="entry.phone"></span>
                                                            </span>
                                                        </template>
                                                        <template x-if="entry.company">
                                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-500/10 px-1.5 py-0.5 rounded font-medium">
                                                                <span class="material-symbols-outlined text-[12px]">apartment</span>
                                                                <span x-text="entry.company"></span>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Column 2: Submitted Details & Files --}}
                                        <td class="py-4 px-6 text-[#6F767E]">
                                            <div class="space-y-1.5 max-w-sm">
                                                {{-- File Attachments Badge --}}
                                                <template x-if="entry.has_file">
                                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-bold text-[11px] border border-emerald-200 dark:border-emerald-500/20">
                                                        <span class="material-symbols-outlined text-sm">attach_file</span>
                                                        <span x-text="entry.file_count === 1 ? '1 File Attached (' + (entry.files[0].ext ? '.' + entry.files[0].ext : 'Document') + ')' : entry.file_count + ' Files Attached'"></span>
                                                    </div>
                                                </template>

                                                {{-- Fields Snippet --}}
                                                <div class="line-clamp-2 text-xs text-[#111827] dark:text-[#E2E8F0]">
                                                    <template x-for="(f, fIdx) in entry.fields.slice(0, 3)" :key="f.key">
                                                        <span class="inline">
                                                            <span class="font-semibold text-gray-500 dark:text-gray-400" x-text="f.label + ':'"></span>
                                                            <span class="font-medium mr-2" x-text="f.display_value"></span>
                                                        </span>
                                                    </template>
                                                    <template x-if="entry.fields.length > 3">
                                                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500" x-text="'+' + (entry.fields.length - 3) + ' more'"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Column 3: Source & Attribution --}}
                                        <td class="py-4 px-6">
                                            <div class="space-y-1 text-[11px]">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100 dark:bg-[#272B30] text-gray-600 dark:text-gray-300 font-medium">
                                                        <span class="material-symbols-outlined text-[13px]">devices</span>
                                                        <span x-text="entry.attribution.os ? (entry.attribution.os + (entry.attribution.browser ? ' • ' + entry.attribution.browser : '')) : 'Direct / Web'"></span>
                                                    </span>
                                                </div>
                                                <template x-if="entry.attribution.submission_page">
                                                    <div class="text-[10px] text-gray-400 truncate max-w-[200px]" :title="entry.attribution.submission_page">
                                                        <span class="font-semibold">Page:</span> <span x-text="entry.attribution.submission_page.replace(/^https?:\/\/[^\/]+/, '')"></span>
                                                    </div>
                                                </template>
                                                <template x-if="entry.attribution.utm_source">
                                                    <div class="inline-flex items-center gap-1 text-[10px] text-blue-600 dark:text-blue-400 font-bold">
                                                        <span class="material-symbols-outlined text-[12px]">tag</span>
                                                        <span x-text="entry.attribution.utm_source + (entry.attribution.utm_medium ? ' / ' + entry.attribution.utm_medium : '')"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>

                                        {{-- Column 4: Submitted Date --}}
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <div class="font-bold text-xs text-[#111827] dark:text-[#FCFCFC]" x-text="entry.submitted_date"></div>
                                            <div class="text-[11px] text-[#6F767E] flex items-center gap-1 mt-0.5">
                                                <span x-text="entry.submitted_time"></span>
                                                <span>•</span>
                                                <span class="italic text-[10px]" x-text="entry.time_ago"></span>
                                            </div>
                                        </td>

                                        {{-- Column 5: Actions --}}
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button 
                                                    type="button" 
                                                    @click="openEntry(entry)" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-primary/10 hover:bg-primary text-primary hover:text-white transition-all shadow-sm"
                                                    title="View Full Submission Details"
                                                >
                                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                                    <span>View</span>
                                                </button>
                                                <button 
                                                    type="button" 
                                                    @click="deleteEntry(entry.id)" 
                                                    class="p-1.5 rounded-xl text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                                                    title="Delete Submission"
                                                >
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                {{-- Empty State --}}
                                <tr x-show="filteredEntries.length === 0">
                                    <td colspan="5" class="py-16 text-center">
                                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-[#272B30] flex items-center justify-center mx-auto mb-3 text-gray-400">
                                            <span class="material-symbols-outlined text-3xl">inbox</span>
                                        </div>
                                        <h4 class="text-sm font-bold text-[#111827] dark:text-white" x-text="submissionSearch ? 'No matching submissions found' : 'No submissions recorded yet'"></h4>
                                        <p class="text-xs text-[#6F767E] mt-1 max-w-sm mx-auto" x-text="submissionSearch ? 'Try adjusting your search keywords to find the submission.' : 'Submissions from live forms will automatically appear in this datagrid.'"></p>
                                        <button 
                                            type="button" 
                                            x-show="submissionSearch" 
                                            @click="submissionSearch = ''" 
                                            class="mt-4 px-4 py-2 rounded-xl text-xs font-bold bg-gray-100 dark:bg-[#272B30] text-[#111827] dark:text-white hover:bg-gray-200 transition-all"
                                        >
                                            Clear Filter
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Field Modal Picker --}}
    <div x-show="showFieldModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.outside="showFieldModal = false" class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-6 md:p-8 max-w-xl w-full space-y-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-[#272B30]">
                <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Add Field to Canvas</h3>
                <button type="button" @click="showFieldModal = false" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            @php
                $quickTypes = ['text','email','textarea','select','tel','checkbox','gdpr'];
                $allFieldTypes = get_form_field_types();
                $customTypes = array_filter($allFieldTypes, fn($ft) => !empty($ft['theme_custom']));
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($quickTypes as $qt)
                    @if(isset($allFieldTypes[$qt]))
                    <button type="button" @click="addField('{{ $qt }}')" class="p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] hover:bg-primary/10 hover:border-primary border border-transparent transition-all text-left flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-2xl">{{ $allFieldTypes[$qt]['icon'] }}</span>
                        <span class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">{{ $allFieldTypes[$qt]['label'] }}</span>
                    </button>
                    @endif
                @endforeach

                @foreach($customTypes as $typeKey => $ct)
                    <button type="button" @click="addField('{{ $typeKey }}')" class="p-4 rounded-2xl bg-primary/5 dark:bg-primary/10 hover:bg-primary/15 border-2 border-dashed border-primary/30 hover:border-primary transition-all text-left flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-2xl">{{ $ct['icon'] }}</span>
                        <span class="text-xs font-bold text-primary">{{ $ct['label'] }}</span>
                        <span class="text-[9px] text-primary/60 leading-tight text-center">{{ $ct['category'] ?? 'theme' }}</span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 📑 SLIDE-OVER SUBMISSION DETAIL DRAWER (MODAL) --}}
    <div x-show="showEntryDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        {{-- Backdrop blur --}}
        <div 
            x-show="showEntryDrawer"
            x-transition:enter="ease-in-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
            @click="closeEntry()"
        ></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div 
                x-show="showEntryDrawer"
                x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-2xl bg-white dark:bg-[#1A1A1A] border-l border-gray-200 dark:border-[#272B30] shadow-2xl flex flex-col"
            >
                {{-- Drawer Header --}}
                <div class="p-6 border-b border-gray-200 dark:border-[#272B30] flex items-center justify-between gap-4 bg-gray-50/50 dark:bg-[#0B0B0B]/50 shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-primary/10 text-primary border border-primary/20" x-text="'#' + activeEntry?.id"></span>
                        <div>
                            <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC] leading-tight" x-text="activeEntry?.name"></h3>
                            <p class="text-xs text-[#6F767E] mt-0.5 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                <span x-text="activeEntry?.submitted_at"></span>
                                <span>•</span>
                                <span class="italic" x-text="activeEntry?.time_ago"></span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="copyEntrySummary()" 
                            class="px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#111827] dark:text-white hover:bg-gray-50 transition-all flex items-center gap-1 shadow-sm"
                            title="Copy submission summary to clipboard"
                        >
                            <span class="material-symbols-outlined text-sm" x-text="copiedSummary ? 'check' : 'content_copy'"></span>
                            <span x-text="copiedSummary ? 'Copied!' : 'Copy Summary'"></span>
                        </button>

                        <button 
                            type="button" 
                            @click="deleteEntry(activeEntry?.id)" 
                            class="p-2 rounded-xl text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors"
                            title="Delete Submission"
                        >
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>

                        <button 
                            type="button" 
                            @click="closeEntry()" 
                            class="p-2 rounded-xl text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#272B30] transition-colors"
                            title="Close (Esc)"
                        >
                            <span class="material-symbols-outlined text-lg">close</span>
                        </button>
                    </div>
                </div>

                {{-- Drawer Body --}}
                <div class="flex-1 overflow-y-auto p-6 md:p-8 space-y-6 no-scrollbar">
                    {{-- Profile Header Card --}}
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-[#F8F9FA] to-white dark:from-[#0B0B0B] dark:to-[#141414] border border-gray-200 dark:border-[#272B30] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-black text-lg flex items-center justify-center shadow-md shrink-0"
                                 x-text="activeEntry?.initials"></div>
                            <div>
                                <h4 class="text-lg font-bold text-[#111827] dark:text-white" x-text="activeEntry?.name"></h4>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <template x-if="activeEntry?.email">
                                        <a :href="'mailto:' + activeEntry?.email" class="inline-flex items-center gap-1 text-xs text-[#2563EB] dark:text-blue-400 hover:underline">
                                            <span class="material-symbols-outlined text-[14px]">mail</span>
                                            <span x-text="activeEntry?.email"></span>
                                        </a>
                                    </template>
                                    <template x-if="activeEntry?.phone">
                                        <a :href="'tel:' + activeEntry?.phone" class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-mono hover:underline">
                                            <span class="material-symbols-outlined text-[14px]">call</span>
                                            <span x-text="activeEntry?.phone"></span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <template x-if="activeEntry?.company || activeEntry?.position">
                            <div class="text-right sm:border-l sm:border-gray-200 sm:dark:border-[#272B30] sm:pl-4">
                                <template x-if="activeEntry?.position">
                                    <div class="text-xs font-bold text-gray-900 dark:text-white" x-text="activeEntry?.position"></div>
                                </template>
                                <template x-if="activeEntry?.company">
                                    <div class="text-[11px] text-[#6F767E]" x-text="activeEntry?.company"></div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Uploaded Files / Attachments Section (e.g. CV / Resume) --}}
                    <template x-if="activeEntry?.has_file">
                        <div class="space-y-3">
                            <h5 class="text-xs font-bold uppercase tracking-wider text-[#6F767E] flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-emerald-600">attach_file</span>
                                Attached Documents & Files (<span x-text="activeEntry?.file_count"></span>)
                            </h5>

                            <div class="grid grid-cols-1 gap-3">
                                <template x-for="file in activeEntry?.files" :key="file.name">
                                    <div class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                                <span class="material-symbols-outlined text-xl">description</span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-[#111827] dark:text-white truncate" x-text="file.name"></div>
                                                <div class="text-[10px] text-gray-500 font-mono mt-0.5" x-text="file.label + (file.ext ? ' • ' + file.ext.toUpperCase() : '')"></div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <a 
                                                :href="file.url" 
                                                target="_blank" 
                                                rel="noopener noreferrer"
                                                class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition-all inline-flex items-center gap-1 shadow-sm"
                                            >
                                                <span class="material-symbols-outlined text-sm">open_in_new</span>
                                                <span>Download / View</span>
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Form Fields List --}}
                    <div class="space-y-3">
                        <h5 class="text-xs font-bold uppercase tracking-wider text-[#6F767E] flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-primary">dynamic_form</span>
                            Submitted Form Fields
                        </h5>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <template x-for="field in activeEntry?.fields" :key="field.key">
                                <div class="p-4 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border border-gray-100 dark:border-[#272B30]/60 space-y-1"
                                     :class="field.type === 'textarea' || (field.display_value && field.display_value.length > 60) ? 'md:col-span-2' : ''">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-[#6F767E]" x-text="field.label"></div>
                                    
                                    {{-- File link --}}
                                    <template x-if="field.is_file">
                                        <div class="pt-1">
                                            <a :href="field.file_url" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline">
                                                <span class="material-symbols-outlined text-sm">download</span>
                                                <span x-text="field.file_name || 'Download Attachment'"></span>
                                            </a>
                                        </div>
                                    </template>

                                    {{-- Email link --}}
                                    <template x-if="!field.is_file && field.type === 'email'">
                                        <div class="pt-1">
                                            <a :href="'mailto:' + field.value" class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                                <span class="material-symbols-outlined text-[13px]">mail</span>
                                                <span x-text="field.value"></span>
                                            </a>
                                        </div>
                                    </template>

                                    {{-- Phone link --}}
                                    <template x-if="!field.is_file && field.type === 'tel'">
                                        <div class="pt-1">
                                            <a :href="'tel:' + field.value" class="inline-flex items-center gap-1 text-xs font-mono font-medium text-emerald-600 dark:text-emerald-400 hover:underline">
                                                <span class="material-symbols-outlined text-[13px]">call</span>
                                                <span x-text="field.value"></span>
                                            </a>
                                        </div>
                                    </template>

                                    {{-- URL link --}}
                                    <template x-if="!field.is_file && field.type === 'url'">
                                        <div class="pt-1">
                                            <a :href="field.value" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs font-medium text-primary hover:underline break-all">
                                                <span class="material-symbols-outlined text-[13px]">link</span>
                                                <span x-text="field.value"></span>
                                            </a>
                                        </div>
                                    </template>

                                    {{-- Textarea / Multi-line --}}
                                    <template x-if="!field.is_file && field.type === 'textarea'">
                                        <div class="text-xs text-[#111827] dark:text-[#FCFCFC] whitespace-pre-wrap leading-relaxed pt-1 bg-white dark:bg-[#1A1A1A] p-3 rounded-xl border border-gray-200 dark:border-[#272B30]" x-text="field.display_value || '-'"></div>
                                    </template>

                                    {{-- Standard text / others --}}
                                    <template x-if="!field.is_file && field.type !== 'email' && field.type !== 'tel' && field.type !== 'url' && field.type !== 'textarea'">
                                        <div class="text-xs font-semibold text-[#111827] dark:text-[#FCFCFC] pt-1" x-text="field.display_value || '-'"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Marketing Attribution & System Info --}}
                    <div class="space-y-3 pt-2">
                        <h5 class="text-xs font-bold uppercase tracking-wider text-[#6F767E] flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-base text-amber-500">insights</span>
                            Source & Technical Attribution
                        </h5>

                        <div class="p-5 rounded-2xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border border-gray-200 dark:border-[#272B30] space-y-3 text-xs">
                            <div class="grid grid-cols-2 gap-3 pb-3 border-b border-gray-200 dark:border-[#272B30]">
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Operating System</span>
                                    <span class="font-bold text-[#111827] dark:text-white" x-text="activeEntry?.attribution?.os || 'Unknown'"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Browser</span>
                                    <span class="font-bold text-[#111827] dark:text-white" x-text="activeEntry?.attribution?.browser || 'Unknown'"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">IP Address</span>
                                    <span class="font-mono text-[#111827] dark:text-white" x-text="activeEntry?.ip_address || 'N/A'"></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Screen & Lang</span>
                                    <span class="text-[#111827] dark:text-white" x-text="(activeEntry?.attribution?.screen_resolution || 'Default') + ' (' + (activeEntry?.attribution?.browser_language || 'en') + ')'"></span>
                                </div>
                            </div>

                            <template x-if="activeEntry?.attribution?.submission_page">
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">Submission Page</span>
                                    <a :href="activeEntry?.attribution?.submission_page" target="_blank" class="font-mono text-[11px] text-blue-600 dark:text-blue-400 hover:underline break-all" x-text="activeEntry?.attribution?.submission_page"></a>
                                </div>
                            </template>

                            <template x-if="activeEntry?.attribution?.http_referrer">
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-0.5">HTTP Referrer</span>
                                    <span class="font-mono text-[11px] text-gray-600 dark:text-gray-400 break-all" x-text="activeEntry?.attribution?.http_referrer"></span>
                                </div>
                            </template>

                            <template x-if="activeEntry?.attribution?.utm_source || activeEntry?.attribution?.utm_campaign">
                                <div class="pt-2 border-t border-gray-200 dark:border-[#272B30] flex flex-wrap gap-2">
                                    <template x-if="activeEntry?.attribution?.utm_source">
                                        <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-mono text-[10px]">
                                            source: <strong x-text="activeEntry?.attribution?.utm_source"></strong>
                                        </span>
                                    </template>
                                    <template x-if="activeEntry?.attribution?.utm_medium">
                                        <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-mono text-[10px]">
                                            medium: <strong x-text="activeEntry?.attribution?.utm_medium"></strong>
                                        </span>
                                    </template>
                                    <template x-if="activeEntry?.attribution?.utm_campaign">
                                        <span class="px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 font-mono text-[10px]">
                                            campaign: <strong x-text="activeEntry?.attribution?.utm_campaign"></strong>
                                        </span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Collapsible Raw JSON Accordion --}}
                    <div class="pt-2">
                        <details class="group bg-[#F4F5F6] dark:bg-[#0B0B0B] rounded-2xl p-4 border border-gray-200 dark:border-[#272B30]">
                            <summary class="text-xs font-bold cursor-pointer flex items-center justify-between text-[#6F767E] hover:text-[#111827] dark:hover:text-white select-none">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">code</span>
                                    <span>Developer Raw JSON Payload</span>
                                </span>
                                <span class="material-symbols-outlined text-base group-open:rotate-180 transition-transform">expand_more</span>
                            </summary>
                            <div class="mt-3 space-y-2">
                                <div class="flex justify-end">
                                    <button 
                                        type="button" 
                                        @click="copyRawJson()" 
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#111827] dark:text-white hover:bg-gray-50 transition-all flex items-center gap-1 shadow-sm"
                                    >
                                        <span class="material-symbols-outlined text-xs" x-text="copiedJson ? 'check' : 'content_copy'"></span>
                                        <span x-text="copiedJson ? 'Copied!' : 'Copy JSON'"></span>
                                    </button>
                                </div>
                                <pre class="p-3 bg-black/90 text-emerald-400 font-mono text-[11px] rounded-xl overflow-x-auto select-all leading-relaxed" x-text="JSON.stringify(activeEntry?.raw_data, null, 2)"></pre>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            border-color: #E5E7EB;
            background-color: #F4F5F6;
        }
        .dark .ql-toolbar.ql-snow {
            border-color: #272B30;
            background-color: #0B0B0B;
        }
        .ql-container.ql-snow {
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
            border-color: #E5E7EB;
            font-size: 14px;
        }
        .dark .ql-container.ql-snow {
            border-color: #272B30;
            color: #FCFCFC;
        }
        .dark .ql-stroke { stroke: #FCFCFC !important; }
        .dark .ql-fill { fill: #FCFCFC !important; }
        .dark .ql-picker { color: #FCFCFC !important; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quillOptions = {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'clean']
                    ]
                }
            };

            // Admin Editor
            const adminEl = document.getElementById('quill-admin-editor');
            const adminInput = document.getElementById('admin_email_body_input');
            if (adminEl && adminInput) {
                window.adminEmailEditor = new Quill('#quill-admin-editor', quillOptions);
                window.adminEmailEditor.clipboard.dangerouslyPasteHTML(adminInput.value);
                window.adminEmailEditor.on('text-change', function() {
                    adminInput.value = window.adminEmailEditor.root.innerHTML;
                    adminInput.dispatchEvent(new Event('input'));
                });
            }

            // User Editor
            const userEl = document.getElementById('quill-user-editor');
            const userInput = document.getElementById('user_email_body_input');
            if (userEl && userInput) {
                window.userEmailEditor = new Quill('#quill-user-editor', quillOptions);
                window.userEmailEditor.clipboard.dangerouslyPasteHTML(userInput.value);
                window.userEmailEditor.on('text-change', function() {
                    userInput.value = window.userEmailEditor.root.innerHTML;
                    userInput.dispatchEvent(new Event('input'));
                });
            }
        });
    </script>
@endpush
@endsection
