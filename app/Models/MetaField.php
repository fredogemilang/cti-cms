<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Validation\ValidationException;

class MetaField extends Model
{
    protected $fillable = [
        'name',
        'label',
        'type',
        'description',
        'options',
        'validation',
        'default_value',
        'is_required',
        'order',
        'fieldable_type',
        'fieldable_id',
        'field_group',
        'conditional_logic',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'validation' => 'array',
        'is_required' => 'boolean',
        'order' => 'integer',
        'conditional_logic' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot method to normalize repeater fields key in options JSON column.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($metaField) {
            $options = $metaField->options;
            if (is_array($options)) {
                // Auto-convert legacy ACF 'sub_fields' key to official 'repeater_fields'
                if (isset($options['sub_fields'])) {
                    $options['repeater_fields'] = $options['repeater_fields'] ?? $options['sub_fields'];
                    unset($options['sub_fields']);
                }

                // If field type is repeater, reject unsupported/unknown repeater keys (e.g. anak_fields, items, etc)
                if ($metaField->type === 'repeater') {
                    $validKeys = ['repeater_fields', 'conditional_logic', 'options_list', 'min', 'max', 'button_label'];
                    foreach (array_keys($options) as $key) {
                        if (! in_array($key, $validKeys)) {
                            throw ValidationException::withMessages([
                                'options' => ["Invalid repeater options key '{$key}'. Repeater sub-fields must be defined under 'repeater_fields'."],
                            ]);
                        }
                    }
                }

                $metaField->options = $options;
            }
        });
    }

    /**
     * Available field types
     */
    public static array $fieldTypes = [
        'text' => [
            'label' => 'Text',
            'icon' => 'text_fields',
            'description' => 'Single line text input',
        ],
        'textarea' => [
            'label' => 'Textarea',
            'icon' => 'notes',
            'description' => 'Multi-line text input',
        ],
        'wysiwyg' => [
            'label' => 'WYSIWYG Editor',
            'icon' => 'edit_note',
            'description' => 'Rich text editor with formatting',
        ],
        'number' => [
            'label' => 'Number',
            'icon' => 'pin',
            'description' => 'Numeric input with optional min/max',
        ],
        'email' => [
            'label' => 'Email',
            'icon' => 'email',
            'description' => 'Email address input',
        ],
        'url' => [
            'label' => 'URL',
            'icon' => 'link',
            'description' => 'URL/Link input',
        ],
        'date' => [
            'label' => 'Date',
            'icon' => 'calendar_today',
            'description' => 'Date picker',
        ],
        'datetime' => [
            'label' => 'Date & Time',
            'icon' => 'schedule',
            'description' => 'Date and time picker',
        ],
        'time' => [
            'label' => 'Time',
            'icon' => 'access_time',
            'description' => 'Time picker',
        ],
        'select' => [
            'label' => 'Select',
            'icon' => 'arrow_drop_down_circle',
            'description' => 'Dropdown selection',
        ],
        'radio' => [
            'label' => 'Radio',
            'icon' => 'radio_button_checked',
            'description' => 'Radio button selection',
        ],
        'checkbox' => [
            'label' => 'Checkbox',
            'icon' => 'check_box',
            'description' => 'Multiple checkbox selection',
        ],
        'switcher' => [
            'label' => 'Switcher',
            'icon' => 'toggle_on',
            'description' => 'On/Off toggle switch',
        ],
        'media' => [
            'label' => 'Media',
            'icon' => 'image',
            'description' => 'Single image/file picker',
        ],
        'gallery' => [
            'label' => 'Gallery',
            'icon' => 'photo_library',
            'description' => 'Multiple images picker',
        ],
        'repeater' => [
            'label' => 'Repeater',
            'icon' => 'repeat',
            'description' => 'Repeatable group of fields',
        ],
        'color' => [
            'label' => 'Color',
            'icon' => 'palette',
            'description' => 'Color picker',
        ],
        'relationship' => [
            'label' => 'Relationship',
            'icon' => 'alt_route',
            'description' => 'Relate entries from another or same Custom Post Type',
        ],
        'icon' => [
            'label' => 'Icon Picker',
            'icon' => 'category',
            'description' => 'Select an icon from active libraries (Lucide Icons, Custom)',
        ],
    ];

    /**
     * Get the parent model (CPT or Taxonomy)
     */
    public function fieldable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for active fields
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for fields in a specific group
     */
    public function scopeInGroup($query, string $group)
    {
        return $query->where('field_group', $group);
    }

    /**
     * Get field type info
     */
    public function getTypeInfoAttribute(): array
    {
        return self::$fieldTypes[$this->type] ?? [
            'label' => ucfirst($this->type),
            'icon' => 'help',
            'description' => '',
        ];
    }

    /**
     * Generate validation rules for this field
     */
    public function getValidationRulesAttribute(): array
    {
        $rules = [];

        if ($this->is_required) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Type-specific rules
        switch ($this->type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'url':
                $rules[] = 'url';
                break;
            case 'number':
                $rules[] = 'numeric';
                if (isset($this->options['min'])) {
                    $rules[] = 'min:'.$this->options['min'];
                }
                if (isset($this->options['max'])) {
                    $rules[] = 'max:'.$this->options['max'];
                }
                break;
            case 'date':
            case 'datetime':
                $rules[] = 'date';
                break;
        }

        // Merge with custom validation rules
        if (! empty($this->validation)) {
            $rules = array_merge($rules, $this->validation);
        }

        return $rules;
    }
}
