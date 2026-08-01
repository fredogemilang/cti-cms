<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\Setting;
use App\Services\FormNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FormController extends Controller
{
    /**
     * Display a listing of forms.
     */
    public function index()
    {
        return view('admin.forms.index');
    }

    /**
     * Show the form for creating a new form.
     */
    public function create()
    {
        return view('admin.forms.create');
    }

    /**
     * Store a newly created form.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forms,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string',
            'fields.*.field_id' => 'required|string',
            'fields.*.type' => 'required|string',
            'fields.*.is_required' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $form = Form::create([
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'settings' => $request->settings,
                'submit_button_text' => $request->submit_button_text ?? 'Submit',
                'notifications' => $request->notifications,
                'confirmations' => $request->confirmations,
                'spam_protection' => $request->spam_protection,
            ]);

            // Create form fields
            foreach ($request->fields as $index => $fieldData) {
                $form->fields()->create([
                    'label' => $fieldData['label'],
                    'field_id' => $fieldData['field_id'],
                    'type' => $fieldData['type'],
                    'options' => $fieldData['options'] ?? null,
                    'validation' => $fieldData['validation'] ?? null,
                    'order' => $index,
                    'is_required' => $fieldData['is_required'] ?? false,
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'help_text' => $fieldData['help_text'] ?? null,
                    'default_value' => $fieldData['default_value'] ?? null,
                    'column_width' => $fieldData['column_width'] ?? 'full',
                    'advanced_settings' => $this->prepareAdvancedSettings($fieldData),
                    'conditional_logic' => $fieldData['conditional_logic'] ?? null,
                    'translations' => $this->prepareTranslations($fieldData),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Form created successfully',
                'data' => $form->load('fields'),
            ]);
        } catch (\Exception $e) {
            \Log::error('Form creation failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Form creation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified form.
     */
    public function show(Form $form)
    {
        $form->load(['fields', 'entries']);

        return view('admin.forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified form.
     */
    public function edit(Form $form)
    {
        return redirect()->route('admin.forms.studio', [$form->id, 'fields']);
    }

    /**
     * Update the specified form.
     */
    public function update(Request $request, Form $form)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forms,slug,'.$form->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
            'fields' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $form->update([
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'description' => $request->description,
                'is_active' => $request->is_active ?? true,
                'settings' => $request->settings,
                'submit_button_text' => $request->submit_button_text ?? 'Submit',
                'notifications' => $request->notifications,
                'confirmations' => $request->confirmations,
                'spam_protection' => $request->spam_protection,
            ]);

            // Delete existing fields and recreate
            $form->fields()->delete();

            foreach ($request->fields as $index => $fieldData) {
                $form->fields()->create([
                    'label' => $fieldData['label'],
                    'field_id' => $fieldData['field_id'],
                    'type' => $fieldData['type'],
                    'options' => $fieldData['options'] ?? null,
                    'validation' => $fieldData['validation'] ?? null,
                    'order' => $index,
                    'is_required' => $fieldData['is_required'] ?? false,
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'help_text' => $fieldData['help_text'] ?? null,
                    'default_value' => $fieldData['default_value'] ?? null,
                    'column_width' => $fieldData['column_width'] ?? 'full',
                    'advanced_settings' => $this->prepareAdvancedSettings($fieldData),
                    'conditional_logic' => $fieldData['conditional_logic'] ?? null,
                    'translations' => $this->prepareTranslations($fieldData),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Form updated successfully',
                'data' => $form->load('fields'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form update failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified form.
     */
    public function destroy(Form $form)
    {
        try {
            $form->delete();

            return response()->json([
                'success' => true,
                'message' => 'Form deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Form deletion failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display form entries.
     */
    public function entries(Form $form, Request $request)
    {
        $query = $form->entries()->with('user')->latest();

        // Search across all fields
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('data', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Get stats before pagination
        $stats = [
            'total' => $form->entries()->count(),
            'today' => $form->entries()->whereDate('created_at', today())->count(),
            'this_week' => $form->entries()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => $form->entries()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        $entries = $query->paginate(25)->withQueryString();

        return view('admin.forms.entries', compact('form', 'entries', 'stats'));
    }

    /**
     * Export form entries to multiple formats.
     */
    public function exportEntries(Form $form, Request $request)
    {
        $entries = $form->entries()->with('user')->get();
        $format = $request->get('format', 'xlsx');

        if ($entries->isEmpty()) {
            return back()->with('error', 'No entries to export');
        }

        $baseFilename = Str::slug($form->name).'-entries-'.now()->format('Y-m-d');

        switch ($format) {
            case 'pdf':
                // Generate simple HTML table for PDF
                $html = $this->generatePdfHtml($form, $entries);

                return response($html, 200, [
                    'Content-Type' => 'text/html',
                    'Content-Disposition' => "attachment; filename=\"{$baseFilename}.html\"",
                ]);

            default: // xlsx, excel, csv
                $filename = $baseFilename.'.xlsx';

                $spreadsheet = new Spreadsheet;
                $sheet = $spreadsheet->getActiveSheet();

                // Header row
                $headers = ['ID', 'Submitted At', 'IP Address'];
                foreach ($form->fields as $field) {
                    if (! in_array($field->type, ['section', 'divider', 'html'])) {
                        $headers[] = $field->label;
                    }
                }
                $sheet->fromArray($headers, null, 'A1');

                // Data rows
                $rowNumber = 2;
                foreach ($entries as $entry) {
                    $row = [$entry->id, $entry->created_at->format('Y-m-d H:i:s'), $entry->ip_address];
                    foreach ($form->fields as $field) {
                        if (! in_array($field->type, ['section', 'divider', 'html'])) {
                            $value = $entry->getFieldValue($field->field_id);
                            $row[] = is_array($value) ? implode(', ', $value) : $value;
                        }
                    }
                    $sheet->fromArray($row, null, 'A'.$rowNumber);
                    $rowNumber++;
                }

                return response()->streamDownload(function () use ($spreadsheet) {
                    $writer = new Xlsx($spreadsheet);
                    $writer->save('php://output');
                }, $filename, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                ]);
        }
    }

    /**
     * Generate PDF-ready HTML for entries.
     */
    protected function generatePdfHtml(Form $form, $entries)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>'.e($form->name).' - Entries</title>';
        $html .= '<style>body{font-family:Arial,sans-serif;font-size:12px}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#f5f5f5}h1{color:#333}</style></head><body>';
        $html .= '<h1>'.e($form->name).' - Form Entries</h1>';
        $html .= '<p>Exported: '.now()->format('F j, Y g:i A').' | Total: '.$entries->count().' entries</p>';
        $html .= '<table><thead><tr><th>ID</th><th>Submitted</th>';

        foreach ($form->fields as $field) {
            if (! in_array($field->type, ['section', 'divider', 'html'])) {
                $html .= '<th>'.e($field->label).'</th>';
            }
        }
        $html .= '</tr></thead><tbody>';

        foreach ($entries as $entry) {
            $html .= '<tr><td>#'.$entry->id.'</td><td>'.$entry->created_at->format('M d, Y H:i').'</td>';
            foreach ($form->fields as $field) {
                if (! in_array($field->type, ['section', 'divider', 'html'])) {
                    $value = $entry->getFieldValue($field->field_id);
                    $html .= '<td>'.e(is_array($value) ? implode(', ', $value) : $value).'</td>';
                }
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return $html;
    }

    /**
     * Toggle form active status.
     */
    public function toggleStatus(Form $form)
    {
        try {
            $form->update(['is_active' => ! $form->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Form status updated successfully',
                'is_active' => $form->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Status update failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a form entry.
     */
    public function deleteEntry($entryId)
    {
        try {
            $entry = FormEntry::findOrFail($entryId);
            $entry->delete();

            return response()->json([
                'success' => true,
                'message' => 'Entry deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete entry: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display theme form assignments.
     */
    public function assignments()
    {
        $theme = active_theme();
        if (! $theme) {
            return redirect()->route('admin.forms.index')
                ->with('error', 'No active theme found.');
        }

        $config = $theme->loadConfig();
        $placeholders = $config['form_placeholders'] ?? [];

        $forms = Form::where('is_active', true)->get();
        $currentAssignments = Setting::get("theme_{$theme->slug}_form_assignments", []);

        return view('admin.forms.assignments', compact('theme', 'placeholders', 'forms', 'currentAssignments'));
    }

    /**
     * Save theme form assignments.
     */
    public function saveAssignments(Request $request)
    {
        $theme = active_theme();
        if (! $theme) {
            return redirect()->route('admin.forms.index')
                ->with('error', 'No active theme found.');
        }

        $assignments = $request->input('assignments', []);

        // Clean up empty assignments
        $assignments = array_filter($assignments, fn ($v) => ! empty($v));

        Setting::set("theme_{$theme->slug}_form_assignments", $assignments, 'theme', 'array');

        return redirect()->route('admin.forms.assignments')
            ->with('success', 'Form assignments saved successfully.');
    }

    /**
     * Display Form Studio Workspace (Unified UI/UX).
     */
    public function studio($id, $tab = 'fields')
    {
        $form = Form::with(['fields', 'entries' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        $theme = active_theme();
        $placeholders = [];
        $currentAssignments = [];

        if ($theme) {
            $config = $theme->loadConfig();
            $placeholders = $config['form_placeholders'] ?? [];
            $currentAssignments = Setting::get("theme_{$theme->slug}_form_assignments", []);
        }

        $activeTab = in_array($tab, ['fields', 'settings', 'emails', 'entries']) ? $tab : 'fields';

        return view('admin.forms.studio', compact('form', 'activeTab', 'placeholders', 'currentAssignments', 'theme'));
    }

    /**
     * Save Unified Form Studio changes.
     */
    public function saveStudio(Request $request, $id)
    {
        $form = Form::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forms,slug,'.$form->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'submit_button_text' => 'nullable|string',
            'fields' => 'nullable|array',
            'notifications' => 'nullable|array',
            'confirmations' => 'nullable|array',
            'spam_protection' => 'nullable|array',
            'theme_slot' => 'nullable|string',
        ]);

        $notifications = $validated['notifications'] ?? $form->notifications ?? [];
        $notifications['notify_admin'] = (bool) ($notifications['notify_admin'] ?? false);
        $notifications['send_to_user'] = (bool) ($notifications['send_to_user'] ?? false);
        $notifications['enabled'] = $notifications['notify_admin'] || $notifications['send_to_user'];

        $form->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'submit_button_text' => $validated['submit_button_text'] ?? 'Submit',
            'notifications' => $notifications,
            'confirmations' => $validated['confirmations'] ?? $form->confirmations ?? [],
            'spam_protection' => $validated['spam_protection'] ?? $form->spam_protection ?? [],
        ]);

        // Save fields if provided
        if (isset($validated['fields'])) {
            $form->fields()->delete();
            foreach ($validated['fields'] as $index => $fieldData) {
                $form->fields()->create([
                    'label' => $fieldData['label'],
                    'field_id' => $fieldData['field_id'],
                    'type' => $fieldData['type'],
                    'options' => $fieldData['options'] ?? null,
                    'validation' => $fieldData['validation'] ?? null,
                    'order' => $index,
                    'is_required' => ! empty($fieldData['is_required']),
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'help_text' => $fieldData['help_text'] ?? null,
                    'default_value' => $fieldData['default_value'] ?? null,
                    'column_width' => $fieldData['column_width'] ?? 'full',
                    'advanced_settings' => $this->prepareAdvancedSettings($fieldData),
                    'conditional_logic' => $fieldData['conditional_logic'] ?? null,
                    'translations' => $this->prepareTranslations($fieldData),
                ]);
            }
        }

        // Save theme slot assignment if specified
        $theme = active_theme();
        if ($theme && ! empty($request->theme_slot)) {
            $currentAssignments = Setting::get("theme_{$theme->slug}_form_assignments", []);
            $currentAssignments[$request->theme_slot] = $form->id;
            Setting::set("theme_{$theme->slug}_form_assignments", $currentAssignments, 'theme', 'array');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Form saved successfully!',
                'redirect_url' => route('admin.forms.studio', [$form->id, $request->input('tab', 'fields')]),
            ]);
        }

        return redirect()->route('admin.forms.studio', [$form->id, $request->input('tab', 'fields')])
            ->with('success', 'Form saved successfully!');
    }

    /**
     * Send test email notification to currently logged in admin.
     */
    public function sendTestEmail(Request $request, $id)
    {
        $form = Form::with('fields')->findOrFail($id);
        $targetEmail = $request->input('test_email', auth()->user()->email ?? config('mail.from.address'));
        $emailType = $request->input('email_type', 'admin'); // 'admin' or 'user'

        $mockEntry = new FormEntry([
            'id' => 999,
            'form_id' => $form->id,
            'data' => [
                'name' => auth()->user()->name ?? 'Test Admin',
                'corporate_email' => $targetEmail,
                'email' => $targetEmail,
                'company_name' => 'Central Data Technology Test',
                'company' => 'Central Data Technology Test',
                'phone_number' => '+62 812-3456-7890',
            ],
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $service = app(FormNotificationService::class);

        try {
            $notifications = $form->notifications ?? [];
            if ($emailType === 'user') {
                $service->sendNotifications($form, $mockEntry);
            } else {
                $service->sendNotifications($form, $mockEntry);
            }

            return response()->json([
                'success' => true,
                'message' => "Test email ({$emailType}) dispatched successfully to {$targetEmail}!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display form notification settings page.
     */
    public function notifications($id)
    {
        return redirect()->route('admin.forms.studio', [$id, 'emails']);
    }

    /**
     * Update form notification settings.
     */
    public function updateNotifications(Request $request, $id)
    {
        return $this->saveStudio($request, $id);
    }

    /**
     * Prepare advanced settings array from field data.
     */
    private function prepareAdvancedSettings(array $fieldData): ?array
    {
        $advanced = $fieldData['advanced_settings'] ?? [];
        if (is_string($advanced)) {
            $advanced = json_decode($advanced, true) ?? [];
        }

        if (! empty($fieldData['consent_text'])) {
            $advanced['consent_text'] = $fieldData['consent_text'];
        }
        if (! empty($fieldData['privacy_content'])) {
            $advanced['consent_text'] = $fieldData['privacy_content'];
        }
        if (! empty($fieldData['terms_text'])) {
            $advanced['terms_text'] = $fieldData['terms_text'];
        }
        if (! empty($fieldData['html_content'])) {
            $advanced['html_content'] = $fieldData['html_content'];
        }

        return ! empty($advanced) ? $advanced : null;
    }

    /**
     * Build translations JSON from flat form data.
     * Expects keys like translations_id_label, translations_id_placeholder.
     */
    private function prepareTranslations(array $fieldData): ?array
    {
        $translations = [];

        $idLabel = $fieldData['translations_id_label'] ?? '';
        $idPlaceholder = $fieldData['translations_id_placeholder'] ?? '';
        $idConsentText = $fieldData['translations_id_consent_text'] ?? '';

        if (! empty($idLabel) || ! empty($idPlaceholder) || ! empty($idConsentText)) {
            $id = [];
            if (! empty($idLabel)) {
                $id['label'] = $idLabel;
            }
            if (! empty($idPlaceholder)) {
                $id['placeholder'] = $idPlaceholder;
            }
            if (! empty($idConsentText)) {
                $id['consent_text'] = $idConsentText;
            }
            $translations['id'] = $id;
        }

        return ! empty($translations) ? $translations : null;
    }
}
