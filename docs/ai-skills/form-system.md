# CTI CMS — Form System (AI Skill)

> This skill explains how the Form Builder system works in CTI CMS.

## Overview

The Form Builder (Form Studio) allows admin to create dynamic forms with
drag-and-drop fields, validation rules, and email notifications. Forms are
assigned to theme "placeholders" defined in `theme.json`.

## Form Model Structure

```json
{
  "id": 1,
  "name": "Contact Form",
  "slug": "contact-form",
  "description": "Main contact form",
  "success_message": "Thank you for contacting us!",
  "email_notification": true,
  "notification_email": "info@company.com",
  "fields": [
    {
      "id": 1,
      "label": "Full Name",
      "name": "full_name",
      "type": "text",
      "is_required": true,
      "placeholder": "Enter your name",
      "sort_order": 1
    },
    {
      "id": 2,
      "label": "Email",
      "name": "email",
      "type": "email",
      "is_required": true,
      "validation_rules": "email",
      "sort_order": 2
    },
    {
      "id": 3,
      "label": "Message",
      "name": "message",
      "type": "textarea",
      "is_required": true,
      "sort_order": 3
    }
  ]
}
```

## Field Types

| Type | Description |
|------|-------------|
| `text` | Single-line text input |
| `email` | Email input with validation |
| `textarea` | Multi-line text |
| `select` | Dropdown select |
| `checkbox` | Checkbox (single or group) |
| `radio` | Radio button group |
| `number` | Numeric input |
| `phone` | Phone number input |
| `url` | URL input |
| `file` | File upload |
| `date` | Date picker |
| `hidden` | Hidden field |

## Theme Integration

### Form Placeholders in `theme.json`
```json
{
  "form_placeholders": [
    {
      "key": "contact_form",
      "label": "Contact Form",
      "description": "Main contact form on Contact Us page"
    },
    {
      "key": "consultation_form",
      "label": "Consultation Form",
      "description": "Request consultation form"
    }
  ]
}
```

### Rendering in Blade
```blade
@php
    $theme = active_theme();
    $assignments = setting("theme_{$theme->slug}_form_assignments", []);
    $formId = $assignments['contact_form'] ?? null;
    $form = $formId ? \App\Models\Form::with('fields')->find($formId) : null;
@endphp

@if($form)
    @include('{theme}::partials.tailwind-form', ['form' => $form, 'variant' => 'dark'])
@endif
```

### Admin Assignment
Forms are assigned to placeholders at `/ctrlpanel/forms/assignments`.
Stored in settings as `theme_{theme_slug}_form_assignments`.

## Form Submissions

### API Endpoint
`POST /api/v1/forms/{slug}/submit` (rate limited: 30/min)

### Submission Storage
Entries stored in `form_entries` table with JSON `data` column:
```json
{
  "full_name": "John Doe",
  "email": "john@example.com",
  "message": "I want to learn more..."
}
```

### Admin View
Form entries viewable at `/ctrlpanel/forms/{id}/entries`.
