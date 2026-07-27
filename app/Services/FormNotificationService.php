<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormEntry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FormNotificationService
{
    /**
     * Send notifications after form submission.
     */
    public function sendNotifications(Form $form, FormEntry $entry): void
    {
        $notifications = $form->notifications ?? [];

        $notifyAdmin = ! empty($notifications['notify_admin']) || (! isset($notifications['notify_admin']) && ! empty($notifications['enabled']));
        $sendToUser = ! empty($notifications['send_to_user']);

        if (! $notifyAdmin && ! $sendToUser) {
            return;
        }

        try {
            // Send admin notification if enabled
            if ($notifyAdmin) {
                $this->sendAdminNotification($form, $entry, $notifications);
            }

            // Send user confirmation if enabled
            if ($sendToUser) {
                $this->sendUserConfirmation($form, $entry, $notifications);
            }
        } catch (\Exception $e) {
            Log::error('Form notification failed: '.$e->getMessage(), [
                'form_id' => $form->id,
                'entry_id' => $entry->id,
            ]);
        }
    }

    /**
     * Send notification email to admin.
     */
    protected function sendAdminNotification(Form $form, FormEntry $entry, array $notifications): void
    {
        $adminEmail = $notifications['admin_email'] ?? config('mail.from.address');

        if (empty($adminEmail)) {
            return;
        }

        $subject = $notifications['subject'] ?? "New Form Submission: {$form->name}";
        $data = $entry->data ?? [];

        $html = $this->buildAdminEmailHtml($form, $entry, $data);

        // Pre-resolve the visitor's email so we can set Reply-To inside the Mail closure.
        $replyTo = $this->findUserEmailFromData($form, $data);

        Mail::html($html, function ($message) use ($adminEmail, $subject, $replyTo) {
            $message->to($adminEmail)->subject($subject);
            if ($replyTo) {
                $message->replyTo($replyTo);
            }
        });
    }

    /**
     * Send confirmation email to user.
     */
    protected function sendUserConfirmation(Form $form, FormEntry $entry, array $notifications): void
    {
        $data = $entry->data ?? [];
        $userEmail = $this->findUserEmailFromData($form, $data);

        if (empty($userEmail)) {
            return;
        }

        $subject = $notifications['user_subject'] ?? "Thank you for your submission - {$form->name}";
        $customBody = $notifications['user_email_body'] ?? null;

        $html = $this->buildUserConfirmationHtml($form, $customBody, $data);

        Mail::html($html, function ($mail) use ($userEmail, $subject) {
            $mail->to($userEmail)
                ->subject($subject);
        });
    }

    /**
     * Find user email from form data.
     */
    protected function findUserEmailFromData(Form $form, array $data): ?string
    {
        // Look for email field in form
        foreach ($form->fields as $field) {
            if ($field->type === 'email') {
                $email = $data[$field->field_id] ?? null;
                if (! empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return $email;
                }
            }
        }

        // Check common field names
        $emailFields = ['email', 'user_email', 'contact_email', 'your_email'];
        foreach ($emailFields as $fieldName) {
            if (! empty($data[$fieldName]) && filter_var($data[$fieldName], FILTER_VALIDATE_EMAIL)) {
                return $data[$fieldName];
            }
        }

        return null;
    }

    /**
     * Build HTML for admin notification email.
     */
    protected function buildAdminEmailHtml(Form $form, FormEntry $entry, array $data, ?string $customBody = null): string
    {
        $formUrl = route('admin.forms.entries', $form->id);
        $timestamp = $entry->created_at ? $entry->created_at->format('F j, Y \a\t g:i A') : date('F j, Y \a\t g:i A');
        $userName = $this->findUserName($data) ?? 'Visitor';
        $userEmail = $this->findUserEmailFromData($form, $data) ?? '';

        // Build submission table HTML for placeholder or default template
        $tableHtml = '<div class="fields-wrapper" style="margin-top: 15px; margin-bottom: 15px;">';
        foreach ($form->fields as $field) {
            if (in_array($field->type, ['section', 'divider', 'html'])) {
                continue;
            }
            $val = $data[$field->field_id] ?? '';
            if (is_array($val)) {
                $val = implode(', ', array_filter($val));
            }
            $valStr = empty($val) ? '<em style="color: #9ca3af;">Not provided</em>' : e($val);

            $tableHtml .= '
            <div style="margin-bottom: 12px; padding: 12px; background: #ffffff; border-radius: 6px; border-left: 4px solid #b82d25; border: 1px solid #e5e7eb;">
                <div style="font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase; margin-bottom: 4px;">'.e($field->label).'</div>
                <div style="color: #111827; font-size: 14px;">'.$valStr.'</div>
            </div>';
        }
        $tableHtml .= '</div>';

        if (! empty($customBody)) {
            $parsedContent = strtr($customBody, [
                '{name}' => e($userName),
                '{email}' => e($userEmail),
                '{corporate_email}' => e($userEmail),
                '{form_name}' => e($form->name),
                '{submission_table}' => $tableHtml,
                '{admin_url}' => $formUrl,
                '{submission_date}' => $timestamp,
            ]);

            return '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 20px auto; padding: 0; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #ffffff; }
                    .header { background: #111827; color: white; padding: 24px 30px; }
                    .header h1 { margin: 0; font-size: 20px; font-weight: bold; }
                    .content { padding: 30px; background: #f9fafb; color: #111827; font-size: 15px; }
                    .footer { background: #f3f4f6; padding: 16px; text-align: center; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>📬 New Submission: '.e($form->name).'</h1>
                    </div>
                    <div class="content">
                        '.$parsedContent.'
                    </div>
                    <div class="footer">
                        <p>Automated Admin Notification — Central Data Technology CMS</p>
                    </div>
                </div>
            </body>
            </html>';
        }

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
                .field { margin-bottom: 20px; padding: 15px; background: white; border-radius: 6px; border-left: 4px solid #667eea; }
                .field-label { font-weight: 600; color: #374151; margin-bottom: 5px; font-size: 12px; text-transform: uppercase; }
                .field-value { color: #111827; font-size: 15px; }
                .footer { background: #f3f4f6; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; font-size: 13px; color: #6b7280; }
                .btn { display: inline-block; background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 500; }
                .meta { color: #6b7280; font-size: 13px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>📬 New Submission</h1>
                    <p style="margin: 5px 0 0; opacity: 0.9;">'.e($form->name).'</p>
                </div>
                <div class="content">'.$tableHtml.'
                    <div class="meta">
                        <p>📅 Submitted: '.$timestamp.'</p>
                        <p>🌐 IP Address: '.($entry->ip_address ?? 'Unknown').'</p>
                    </div>
                    <div style="text-align: center; margin-top: 30px;">
                        <a href="'.$formUrl.'" class="btn">View All Entries</a>
                    </div>
                </div>
                <div class="footer">
                    This is an automated notification from your form system.
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Build HTML for user confirmation email.
     */
    protected function buildUserConfirmationHtml(Form $form, ?string $customBody, array $data): string
    {
        $userName = $this->findUserName($data) ?? 'Valued Customer';
        $userEmail = $this->findUserEmailFromData($form, $data) ?? '';
        $companyName = is_string($data['company_name'] ?? null) ? $data['company_name'] : (is_string($data['company'] ?? null) ? $data['company'] : '');

        if (! empty($customBody)) {
            $parsedContent = strtr($customBody, [
                '{name}' => e($userName),
                '{email}' => e($userEmail),
                '{corporate_email}' => e($userEmail),
                '{company_name}' => e($companyName),
                '{company}' => e($companyName),
                '{form_name}' => e($form->name),
            ]);

            return '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 20px auto; padding: 0; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #ffffff; }
                    .header { background: #b82d25; color: white; padding: 24px 30px; text-align: center; }
                    .header h1 { margin: 0; font-size: 20px; font-weight: bold; }
                    .content { padding: 30px; background: #ffffff; color: #111827; font-size: 15px; }
                    .footer { background: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>'.e($form->name).'</h1>
                    </div>
                    <div class="content">
                        '.$parsedContent.'
                    </div>
                    <div class="footer">
                        <p>This email was sent because you submitted a request on Central Data Technology.</p>
                    </div>
                </div>
            </body>
            </html>';
        }

        $greeting = "Dear {$userName},";
        $message = 'Thank you for your submission. We will get back to you soon.';

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { background: white; padding: 30px; border: 1px solid #e5e7eb; }
                .message { font-size: 16px; color: #374151; }
                .summary { background: #f9fafb; padding: 20px; border-radius: 8px; margin-top: 20px; }
                .summary h3 { margin: 0 0 15px; font-size: 14px; color: #6b7280; text-transform: uppercase; }
                .summary-item { display: flex; border-bottom: 1px solid #e5e7eb; padding: 10px 0; }
                .summary-item:last-child { border-bottom: none; }
                .summary-label { font-weight: 500; color: #6b7280; width: 40%; }
                .summary-value { color: #111827; }
                .footer { background: #f3f4f6; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; font-size: 13px; color: #6b7280; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>✅ Submission Received</h1>
                </div>
                <div class="content">
                    <p class="message"><strong>'.$greeting.'</strong></p>
                    <p class="message">'.nl2br(e($message)).'</p>
                    
                    <div class="summary">
                        <h3>Your Submission Summary</h3>';

        // Add submission summary (limited fields)
        $count = 0;
        foreach ($data as $key => $value) {
            if ($count >= 5) {
                break;
            } // Limit to 5 fields

            if (is_array($value)) {
                $value = implode(', ', array_filter($value));
            }
            if (empty($value)) {
                continue;
            }

            // Format field name
            $label = ucwords(str_replace(['_', '-'], ' ', $key));

            $html .= '
                        <div class="summary-item">
                            <span class="summary-label">'.e($label).'</span>
                            <span class="summary-value">'.e($value).'</span>
                        </div>';
            $count++;
        }

        $html .= '
                    </div>
                </div>
                <div class="footer">
                    <p>This email was sent because you submitted a form on our website.</p>
                    <p style="margin-top: 10px;">Thank you for contacting us!</p>
                </div>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Find user's name from form data.
     */
    protected function findUserName(array $data): ?string
    {
        // Check for name field (array format from name field type)
        if (isset($data['name']) && is_array($data['name'])) {
            $firstName = $data['name']['first_name'] ?? '';
            $lastName = $data['name']['last_name'] ?? '';

            return trim("{$firstName} {$lastName}") ?: null;
        }

        // Check common name fields
        $nameFields = ['name', 'full_name', 'your_name', 'first_name'];
        foreach ($nameFields as $field) {
            if (! empty($data[$field]) && is_string($data[$field])) {
                return $data[$field];
            }
        }

        return null;
    }
}
