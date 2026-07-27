<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingsRegistry;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingsAdminController extends Controller
{
    /**
     * List all registered setting groups and their field schemas
     */
    public function index()
    {
        $registry = app(SettingsRegistry::class);
        $groups = $registry->groups();

        $result = [];
        foreach ($groups as $slug => $groupConfig) {
            $values = array_replace(
                $registry->defaults($slug),
                Setting::forGroup($slug)
            );

            $result[] = [
                'slug' => $slug,
                'label' => $groupConfig['label'],
                'icon' => $groupConfig['icon'],
                'description' => $groupConfig['description'] ?? null,
                'fields' => $groupConfig['fields'],
                'values' => $values,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Get settings for a specific group
     */
    public function show(string $group)
    {
        $registry = app(SettingsRegistry::class);
        if (! $registry->hasGroup($group)) {
            return response()->json([
                'success' => false,
                'message' => "Settings group [{$group}] is not registered.",
            ], 404);
        }

        $values = array_replace(
            $registry->defaults($group),
            Setting::forGroup($group)
        );

        return response()->json([
            'success' => true,
            'group' => $registry->group($group),
            'data' => $values,
        ]);
    }

    /**
     * Update settings for a specific group
     */
    public function update(Request $request, string $group)
    {
        $registry = app(SettingsRegistry::class);
        if (! $registry->hasGroup($group)) {
            return response()->json([
                'success' => false,
                'message' => "Settings group [{$group}] is not registered.",
            ], 404);
        }

        try {
            $validated = $registry->validate($group, $request->all());
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $types = [];
        foreach ($registry->fields($group) as $field) {
            $types[$field['key']] = $this->fieldStorageType($field['type'] ?? 'string');
        }

        Setting::setMany($validated, $group, $types);

        return response()->json([
            'success' => true,
            'message' => "Settings for group [{$group}] updated successfully.",
            'data' => Setting::forGroup($group),
        ]);
    }

    protected function fieldStorageType(string $uiType): string
    {
        return match ($uiType) {
            'boolean' => 'boolean',
            'number' => 'integer',
            'multiselect', 'tags', 'array' => 'array',
            default => 'string',
        };
    }
}
