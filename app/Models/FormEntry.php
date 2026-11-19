<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'form_id',
        'data',
        'ip_address',
        'user_agent',
        'user_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the form that owns the entry.
     *
     * @return BelongsTo<Form, $this>
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get the user that submitted the entry.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a specific field value from the entry data.
     */
    public function getFieldValue($fieldId)
    {
        return $this->data[$fieldId] ?? null;
    }

    /**
     * Get all field values with labels.
     */
    public function getFieldsWithLabels()
    {
        $fields = $this->form->fields;
        $result = [];

        foreach ($fields as $field) {
            $result[] = [
                'label' => $field->label,
                'value' => $this->getFieldValue($field->field_id),
                'type' => $field->type,
            ];
        }

        return $result;
    }

    /**
     * Get attribution metadata array.
     */
    public function getAttributionData(): array
    {
        return $this->data['_attribution'] ?? [];
    }

    /**
     * Export entry to array format.
     */
    public function toExportArray()
    {
        $export = [
            'ID' => $this->id,
            'Submitted At' => $this->created_at->format('Y-m-d H:i:s'),
        ];

        foreach ($this->form->fields as $field) {
            $value = $this->getFieldValue($field->field_id);

            // Format array values (for checkboxes)
            if (is_array($value)) {
                $value = implode(', ', $value);
            }

            $export[$field->label] = $value;
        }

        if ($this->user) {
            $export['Submitted By'] = $this->user->name;
        }

        $attr = $this->getAttributionData();
        $export['UTM Source'] = $attr['utm_source'] ?? '-';
        $export['UTM Medium'] = $attr['utm_medium'] ?? '-';
        $export['UTM Campaign'] = $attr['utm_campaign'] ?? '-';
        $export['Google Click ID (gclid)'] = $attr['gclid'] ?? '-';
        $export['Facebook Click ID (fbclid)'] = $attr['fbclid'] ?? '-';
        $export['Device Type'] = $attr['device_type'] ?? '-';
        $export['Browser'] = $attr['browser'] ?? '-';
        $export['Operating System'] = $attr['os'] ?? '-';
        $export['Screen Resolution'] = $attr['screen_resolution'] ?? '-';
        $export['Browser Language'] = $attr['browser_language'] ?? '-';
        $export['Page Views Count'] = $attr['page_views_count'] ?? '-';
        $export['Time to Convert'] = $attr['time_to_convert'] ?? '-';
        $export['Initial Landing Page'] = $attr['initial_landing_page'] ?? '-';
        $export['Submission Page'] = $attr['submission_page'] ?? '-';
        $export['HTTP Referrer'] = $attr['http_referrer'] ?? ($attr['initial_referrer'] ?? '-');
        $export['IP Address'] = $this->ip_address;

        return $export;
    }
}
