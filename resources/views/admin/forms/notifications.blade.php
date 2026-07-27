@extends('layouts.admin')

@section('title', 'Email Notifications - ' . $form->name)

@section('page-title')
    <div class="flex flex-col">
        <span class="text-xs font-bold text-[#6F767E] uppercase tracking-widest mb-1">Email Notifications</span>
        <span class="text-[#111827] dark:text-white">{{ $form->name }}</span>
    </div>
@endsection

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
@endphp

<div class="space-y-6" x-data="{
    notifyAdmin: {{ json_encode((bool)$notifyAdmin) }},
    adminBody: {{ json_encode($adminEmailBody) }},
    sendToUser: {{ json_encode((bool)$sendToUser) }},
    userBody: {{ json_encode($userEmailBody) }},
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
    }
}">

    {{-- Top Action Header --}}
    <div class="flex flex-wrap justify-between items-center gap-4">
        <a href="{{ route('admin.forms.index') }}" 
            class="flex items-center gap-2 text-sm font-bold text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-colors">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Back to Forms
        </a>

        <div class="flex items-center gap-3">
            {{-- Sub-navigation Tabs --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.forms.edit', $form) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-all">Form Builder</a>
                <a href="{{ route('admin.forms.notifications', $form) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-primary text-white shadow-sm">Email Notifications</a>
                <a href="{{ route('admin.forms.entries', $form) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] text-[#6F767E] hover:text-[#111827] dark:hover:text-white transition-all">Entries ({{ $form->entries()->count() }})</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-medium text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.forms.notifications.update', $form) }}" method="POST" class="space-y-8">
        @csrf

        {{-- Section 1: Admin Notification --}}
        <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-6 md:p-8 space-y-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-[#272B30]">
                <div>
                    <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">1. Admin Email Notification</h3>
                    <p class="text-xs text-[#6F767E]">Receive email alerts whenever a visitor submits this form.</p>
                </div>
                
                {{-- Toggle Notify Admin --}}
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="notifications[notify_admin]" value="1" x-model="notifyAdmin" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    <span class="ml-3 text-xs font-bold text-[#111827] dark:text-[#FCFCFC]" x-text="notifyAdmin ? 'ON (Active)' : 'OFF (Disabled)'"></span>
                </label>
            </div>

            <div x-show="notifyAdmin" x-collapse class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Admin Recipient Email</label>
                        <input type="email" name="notifications[admin_email]" value="{{ $adminEmail }}"
                            class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl focus:ring-2 focus:ring-primary px-4 text-[#111827] dark:text-[#FCFCFC]"
                            placeholder="admin@centraldatatech.com">
                        <p class="text-xs text-[#6F767E]">Defaults to site admin email if left empty.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Email Subject</label>
                        <input type="text" name="notifications[subject]" value="{{ $adminSubject }}"
                            class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl focus:ring-2 focus:ring-primary px-4 text-[#111827] dark:text-[#FCFCFC]"
                            placeholder="New Form Submission: {{ $form->name }}">
                    </div>
                </div>

                {{-- Admin WYSIWYG Editor --}}
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">Admin Email Content (WYSIWYG HTML)</label>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-[#6F767E]">Insert Placeholders:</span>
                            <button type="button" @click="insertAdminPlaceholder('{submission_table}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30] hover:border-primary transition-all">{submission_table}</button>
                            <button type="button" @click="insertAdminPlaceholder('{admin_url}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30] hover:border-primary transition-all">{admin_url}</button>
                            <button type="button" @click="insertAdminPlaceholder('{name}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30] hover:border-primary transition-all">{name}</button>
                            <button type="button" @click="insertAdminPlaceholder('{email}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30] hover:border-primary transition-all">{email}</button>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <input type="hidden" name="notifications[admin_email_body]" x-model="adminBody" id="admin_email_body_input">
                        <div id="quill-admin-editor" class="bg-white dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] rounded-2xl border border-gray-200 dark:border-[#272B30] min-h-[200px]"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: User Auto-Responder --}}
        <div class="bg-white dark:bg-[#1A1A1A] border border-gray-200 dark:border-[#272B30] rounded-3xl p-6 md:p-8 space-y-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-[#272B30]">
                <div>
                    <h3 class="text-base font-bold text-[#111827] dark:text-[#FCFCFC]">2. User Auto-Responder Email</h3>
                    <p class="text-xs text-[#6F767E]">Send an automated response email back to the visitor who submitted the form.</p>
                </div>

                {{-- Toggle Send to User --}}
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="notifications[send_to_user]" value="1" x-model="sendToUser" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    <span class="ml-3 text-xs font-bold text-[#111827] dark:text-[#FCFCFC]" x-text="sendToUser ? 'ON (Active)' : 'OFF (Disabled)'"></span>
                </label>
            </div>

            <div x-show="sendToUser" x-collapse class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">User Email Subject</label>
                    <input type="text" name="notifications[user_subject]" value="{{ $userSubject }}"
                        class="w-full h-11 bg-[#F4F5F6] dark:bg-[#0B0B0B] border-none text-sm font-medium rounded-xl focus:ring-2 focus:ring-primary px-4 text-[#111827] dark:text-[#FCFCFC]"
                        placeholder="Thank you for your submission">
                </div>

                {{-- WYSIWYG Editor Toolbar Helpers --}}
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <label class="block text-xs font-bold text-[#6F767E] uppercase tracking-wider">User Email Content (WYSIWYG HTML)</label>
                        
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs font-semibold text-[#6F767E]">Insert Placeholders:</span>
                            <button type="button" @click="insertUserPlaceholder('{name}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30] hover:border-primary transition-all">{name}</button>
                            <button type="button" @click="insertUserPlaceholder('{corporate_email}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30] hover:border-primary transition-all">{email}</button>
                            <button type="button" @click="insertUserPlaceholder('{company_name}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30] hover:border-primary transition-all">{company}</button>
                            <button type="button" @click="insertUserPlaceholder('{form_name}')" class="px-2.5 py-1 rounded-lg text-xs font-mono bg-[#F4F5F6] dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] border border-gray-200 dark:border-[#272B30] hover:border-primary transition-all">{form_name}</button>
                        </div>
                    </div>

                    {{-- Insert CTA Download Button Quick Action --}}
                    <div class="p-4 rounded-2xl bg-primary/5 border border-primary/20 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-2xl">download_for_offline</span>
                            <div>
                                <h4 class="text-xs font-bold text-[#111827] dark:text-[#FCFCFC]">CTA Download Button Tool</h4>
                                <p class="text-xs text-[#6F767E]">Quickly insert a styled download button for PDF/Guide into the email body.</p>
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

                    {{-- Quill WYSIWYG Editor Container --}}
                    <div class="space-y-2">
                        <input type="hidden" name="notifications[user_email_body]" x-model="userBody" id="user_email_body_input">
                        <div id="quill-user-editor" class="bg-white dark:bg-[#0B0B0B] text-[#111827] dark:text-[#FCFCFC] rounded-2xl border border-gray-200 dark:border-[#272B30] min-h-[250px]"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end pt-4">
            <button type="submit" class="px-8 py-3.5 rounded-2xl font-bold bg-primary text-white hover:bg-red-700 transition-all shadow-md flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">save</span>
                <span>Save Notification Settings</span>
            </button>
        </div>
    </form>
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
