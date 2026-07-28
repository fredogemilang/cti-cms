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
    $userEmailBody = $notifications['user_email_body'] ?? "<p>Hi {name},</p><p>Thank you for submitting <strong>{form_name}</strong>. We have received your details and will get back to you shortly.</p><p><a href=\"https://cdt.devs/themes/cdt/assets/banner_hero-DHYDqbF8.jpg\" style=\"display: inline-block; background-color: #b82d25; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 9999px; font-weight: bold; text-transform: uppercase; font-size: 13px; letter-spacing: 1px;\">Download Digital Solution Guide</a></p><p>Best regards,<br>Central Data Technology Team</p>";

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
@endphp

<div class="h-full flex flex-col w-full bg-[#F4F5F6] dark:bg-[#0B0B0B]"
    x-data="{
        activeTab: '{{ $activeTab ?? 'fields' }}',
        name: {{ json_encode($form->name) }},
        slug: {{ json_encode($form->slug) }},
        description: {{ json_encode($form->description ?? '') }},
        isActive: {{ json_encode($form->is_active ? '1' : '0') }},
        submitButtonText: {{ json_encode($form->submit_button_text ?? 'Submit') }},
        themeSlot: {{ json_encode($assignedSlot) }},
        
        // Confirmations & Spam
        confirmationType: {{ json_encode($confirmationType) }},
        confirmationMessage: {{ json_encode($confirmationMessage) }},
        redirectUrl: {{ json_encode($redirectUrl) }},
        honeypot: {{ json_encode((bool)$honeypot) }},
        captchaProvider: {{ json_encode($captchaProvider) }},

        // Notifications
        notifyAdmin: {{ json_encode((bool)$notifyAdmin) }},
        adminEmail: {{ json_encode($adminEmail) }},
        adminSubject: {{ json_encode($adminSubject) }},
        adminBody: {{ json_encode($adminEmailBody) }},
        
        sendToUser: {{ json_encode((bool)$sendToUser) }},
        userSubject: {{ json_encode($userSubject) }},
        userBody: {{ json_encode($userEmailBody) }},

        // Builder State
        fields: {{ json_encode(array_map(function($f) {
            $adv = $f['advanced_settings'] ?? [];
            if (is_string($adv)) {
                $adv = json_decode($adv, true) ?? [];
            }
            $f['consent_text'] = $f['consent_text'] ?? ($adv['consent_text'] ?? ($adv['privacy_content'] ?? ''));
            $f['terms_text'] = $f['terms_text'] ?? ($adv['terms_text'] ?? '');
            $f['html_content'] = $f['html_content'] ?? ($adv['html_content'] ?? '');
            return $f;
        }, $form->fields ? $form->fields->toArray() : [])) }},
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

        // Builder actions
        addField(type) {
            const fieldId = 'field_' + Math.random().toString(36).substr(2, 6);
            const labelMap = { gdpr: 'Privacy Consent', terms: 'Terms & Conditions', vendor_solutions: 'Solution Needed' };
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
                consent_text: type === 'gdpr' ? defaultConsent : '',
                terms_text: type === 'terms' ? defaultTerms : '',
            });
            this.selectedFieldIndex = this.fields.length - 1;
            this.showFieldModal = false;
        },
        removeField(index) {
            this.fields.splice(index, 1);
            if (this.selectedFieldIndex === index) this.selectedFieldIndex = null;
            else if (this.selectedFieldIndex > index) this.selectedFieldIndex--;
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
        }
    }">

    {{-- Persistent Workspace Top Bar --}}
    <div class="h-16 px-6 bg-white dark:bg-[#1A1A1A] border-b border-gray-200 dark:border-[#272B30] flex items-center justify-between shrink-0 shadow-sm z-30">
        <div class="flex items-center gap-4 min-w-0">
            <a href="{{ route('admin.forms.index') }}" 
                class="p-2 rounded-xl text-[#6F767E] hover:text-[#111827] dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#272B30] transition-all">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>

            <div class="flex items-center gap-3">
                <input x-model="name" type="text"
                    class="text-base font-bold text-[#111827] dark:text-[#FCFCFC] bg-transparent border-none focus:ring-0 p-0 w-auto min-w-[200px]"
                    placeholder="Form Name">

                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0"
                    :class="isActive == '1' ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400'">
                    <span class="w-1.5 h-1.5 rounded-full" :class="isActive == '1' ? 'bg-emerald-500' : 'bg-gray-500'"></span>
                    <span x-text="isActive == '1' ? 'Active' : 'Inactive'"></span>
                </span>
            </div>
        </div>

        {{-- 4 Primary Studio Tabs --}}
        <div class="flex items-center gap-1 p-1 bg-[#F4F5F6] dark:bg-[#0B0B0B] rounded-2xl border border-gray-200 dark:border-[#272B30]">
            <button @click="activeTab = 'fields'" 
                :class="activeTab === 'fields' ? 'bg-white dark:bg-[#1A1A1A] text-primary font-bold shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white font-medium'"
                class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">build</span>
                <span>Fields</span>
            </button>

            <button @click="activeTab = 'settings'" 
                :class="activeTab === 'settings' ? 'bg-white dark:bg-[#1A1A1A] text-primary font-bold shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white font-medium'"
                class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">settings</span>
                <span>Settings</span>
            </button>

            <button @click="activeTab = 'emails'" 
                :class="activeTab === 'emails' ? 'bg-white dark:bg-[#1A1A1A] text-primary font-bold shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white font-medium'"
                class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">mail</span>
                <span>Email Notifications</span>
            </button>

            <button @click="activeTab = 'entries'" 
                :class="activeTab === 'entries' ? 'bg-white dark:bg-[#1A1A1A] text-primary font-bold shadow-sm' : 'text-[#6F767E] hover:text-[#111827] dark:hover:text-white font-medium'"
                class="px-4 py-2 rounded-xl text-xs transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">inbox</span>
                <span>Submissions ({{ $form->entries()->count() }})</span>
            </button>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3">
            <a href="https://cdt.devs/" target="_blank" class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-all flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm">open_in_new</span>
                <span>Frontend Preview</span>
            </a>

            <button type="button" @click="$refs.studioForm.submit()"
                class="px-5 py-2 rounded-xl text-xs font-bold bg-primary text-white hover:bg-red-700 transition-all shadow-md flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span>
                <span>Save Studio Changes</span>
            </button>
        </div>
    </div>

    {{-- Main Studio Form --}}
    <form x-ref="studioForm" action="{{ route('admin.forms.studio.save', $form) }}" method="POST" class="flex-1 flex overflow-hidden">
        @csrf
        <input type="hidden" name="tab" x-model="activeTab">
        <input type="hidden" name="name" x-model="name">
        <input type="hidden" name="slug" x-model="slug">

        {{-- TAB 1: 🛠️ FIELDS BUILDER --}}
        <div x-show="activeTab === 'fields'" class="flex-1 flex w-full overflow-hidden">
            {{-- Left Canvas --}}
            <div class="flex-1 overflow-y-auto p-6 md:p-10 no-scrollbar">
                <div class="max-w-3xl mx-auto space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold text-[#6F767E] uppercase tracking-widest">Canvas Form Fields</h3>
                        <span class="text-xs font-bold text-[#6F767E]" x-text="fields.length + ' fields'"></span>
                    </div>

                    <div class="builder-dropzone min-h-[450px] rounded-3xl p-6 md:p-8 flex flex-col gap-4 border border-gray-200 dark:border-[#272B30]/40 relative bg-white/50 dark:bg-[#1A1A1A]/30">
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
                                        <div class="h-9 w-9 rounded-xl bg-gray-100 dark:bg-[#0B0B0B] text-primary flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-lg" x-text="getFieldIcon(field.type)"></span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-[#111827] dark:text-[#FCFCFC]" x-text="field.label || 'Untitled Field'"></div>
                                            <div class="text-xs text-[#6F767E] font-mono" x-text="field.field_id"></div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span x-show="field.is_required" class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400">Required</span>
                                        <button type="button" @click.stop="removeField(index)" class="p-1 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10">
                                            <span class="material-symbols-outlined text-lg">delete</span>
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
                                <input type="hidden" :name="`fields[${index}][options]`" x-model="field.options_text">
                                <input type="hidden" :name="`fields[${index}][consent_text]`" x-model="field.consent_text">
                                <input type="hidden" :name="`fields[${index}][terms_text]`" x-model="field.terms_text">
                                <input type="hidden" :name="`fields[${index}][html_content]`" x-model="field.html_content">
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

            {{-- Right Field Inspector Drawer --}}
            <aside class="w-[360px] border-l border-gray-200 dark:border-[#272B30] bg-white dark:bg-[#1A1A1A] p-6 shrink-0 overflow-y-auto">
                <template x-if="selectedFieldIndex !== null && fields[selectedFieldIndex]">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-[#272B30]">
                            <h4 class="text-xs font-bold text-[#6F767E] uppercase tracking-widest">Field Inspector</h4>
                            <button type="button" @click="selectedFieldIndex = null" class="text-gray-400 hover:text-gray-600">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-[#6F767E] mb-1">Field Label</label>
                                <input type="text" x-model="fields[selectedFieldIndex].label"
                                    class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium px-3 text-[#111827] dark:text-[#FCFCFC]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#6F767E] mb-1">Field ID (Unique Identifier)</label>
                                <input type="text" x-model="fields[selectedFieldIndex].field_id"
                                    class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs font-mono px-3 text-[#111827] dark:text-[#FCFCFC]">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#6F767E] mb-1">Placeholder Text</label>
                                <input type="text" x-model="fields[selectedFieldIndex].placeholder"
                                    class="w-full h-10 rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium px-3 text-[#111827] dark:text-[#FCFCFC]">
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

                            {{-- GDPR consent text --}}
                            <template x-if="fields[selectedFieldIndex].type === 'gdpr'">
                                <div class="space-y-1 pt-2">
                                    <label class="block text-xs font-bold text-[#6F767E]">Consent Text <span class="font-normal text-[#6F767E]/50">(HTML links supported)</span></label>
                                    <textarea x-model="fields[selectedFieldIndex].consent_text" rows="3"
                                        class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs p-3 text-[#111827] dark:text-[#FCFCFC] resize-none"
                                        placeholder="I consent to having my personal data processed..."></textarea>
                                </div>
                            </template>

                            {{-- Terms text --}}
                            <template x-if="fields[selectedFieldIndex].type === 'terms'">
                                <div class="space-y-1 pt-2">
                                    <label class="block text-xs font-bold text-[#6F767E]">Terms Text <span class="font-normal text-[#6F767E]/50">(HTML links supported)</span></label>
                                    <textarea x-model="fields[selectedFieldIndex].terms_text" rows="3"
                                        class="w-full rounded-xl bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-xs p-3 text-[#111827] dark:text-[#FCFCFC] resize-none"
                                        placeholder="I agree to the Terms and Conditions."></textarea>
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

                <template x-if="selectedFieldIndex === null">
                    <div class="text-center py-20 text-[#6F767E]">
                        <span class="material-symbols-outlined text-4xl mb-2 block opacity-30">touch_app</span>
                        <p class="text-xs font-medium">Click any field in the canvas to inspect and edit its properties.</p>
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
                            <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Submit Button Label</label>
                            <input type="text" name="submit_button_text" x-model="submitButtonText" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Form Description</label>
                        <textarea name="description" x-model="description" rows="3" class="w-full bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl p-4 text-[#111827] dark:text-[#FCFCFC] resize-none"></textarea>
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
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Success Message</label>
                        <textarea name="confirmations[message]" x-model="confirmationMessage" rows="4" class="w-full bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl p-4 text-[#111827] dark:text-[#FCFCFC] resize-none"></textarea>
                    </div>

                    <div x-show="confirmationType === 'redirect'" class="space-y-2">
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Redirect URL</label>
                        <input type="url" name="confirmations[redirect_url]" x-model="redirectUrl" class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl px-4 text-[#111827] dark:text-[#FCFCFC]" placeholder="https://cdt.devs/thank-you">
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
                                    const url = prompt('Enter File Download URL:', 'https://cdt.devs/themes/cdt/assets/banner_hero-DHYDqbF8.jpg');
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
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">Submissions DataGrid ({{ $form->entries->count() }})</h3>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.forms.export', $form) }}?format=xlsx" class="px-4 py-2 rounded-xl text-xs font-bold bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#111827] dark:text-white hover:bg-gray-50 transition-all flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm">download</span>
                            <span>Export Excel</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl overflow-hidden shadow-sm">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-[#272B30] bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#6F767E] uppercase font-bold tracking-wider">
                                <th class="py-4 px-6">Submitted Date</th>
                                <th class="py-4 px-6">Visitor Info</th>
                                <th class="py-4 px-6">Submitted Data</th>
                                <th class="py-4 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#272B30]">
                            @forelse($form->entries as $entry)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-[#272B30]/30 transition-colors">
                                    <td class="py-4 px-6 font-medium text-[#6F767E]">
                                        {{ $entry->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-[#111827] dark:text-[#FCFCFC]">
                                        {{ $entry->data['name'] ?? ($entry->data['corporate_email'] ?? 'Anonymous') }}
                                    </td>
                                    <td class="py-4 px-6 text-[#6F767E]">
                                        <div class="truncate max-w-md">
                                            @foreach($entry->data as $k => $v)
                                                <span class="font-bold">{{ $k }}:</span> {{ is_array($v) ? implode(', ', $v) : $v }} |
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <button type="button" @click="alert(JSON.stringify({{ json_encode($entry->data) }}, null, 2))" class="text-primary font-bold hover:underline">View Entry</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-[#6F767E]">No submissions recorded yet for this form.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
