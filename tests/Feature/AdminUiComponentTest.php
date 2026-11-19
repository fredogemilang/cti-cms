<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class AdminUiComponentTest extends TestCase
{
    /**
     * Test that card component renders correctly.
     */
    public function test_card_component_renders()
    {
        $rendered = Blade::render('<x-admin.ui.card>Hello Card</x-admin.ui.card>');
        $this->assertStringContainsString('Hello Card', $rendered);
        $this->assertStringContainsString('bg-white dark:bg-[#1A1A1A]', $rendered);

        $renderedLoading = Blade::render('<x-admin.ui.card :loading="true">Hello Loading</x-admin.ui.card>');
        $this->assertStringContainsString('animate-indeterminate', $renderedLoading);
    }

    /**
     * Test that table components render correctly.
     */
    public function test_table_components_render()
    {
        $template = <<<'BLADE'
            <x-admin.ui.table :loading="false">
                <x-slot:thead>
                    <x-admin.ui.table-header sort-by="name" field="name" direction="asc">User</x-admin.ui.table-header>
                </x-slot:thead>
                <x-admin.ui.table-row>
                    <x-admin.ui.table-cell>John Doe</x-admin.ui.table-cell>
                </x-admin.ui.table-row>
            </x-admin.ui.table>
        BLADE;

        $rendered = Blade::render($template);
        $this->assertStringContainsString('John Doe', $rendered);
        $this->assertStringContainsString('arrow_upward', $rendered);
        $this->assertStringContainsString('text-[11px] font-bold text-[#6F767E]', $rendered);
    }

    /**
     * Test that form components render correctly.
     */
    public function test_form_components_render()
    {
        // Share empty errors view bag
        view()->share('errors', new ViewErrorBag);

        $template = <<<'BLADE'
            <x-admin.ui.input name="title" label="Title Label" required />
            <x-admin.ui.select name="status" label="Status Label">
                <option value="active">Active</option>
            </x-admin.ui.select>
            <x-admin.ui.checkbox name="is_active" label="Active Check" description="Check description" />
        BLADE;

        $rendered = Blade::render($template);
        $this->assertStringContainsString('Title Label', $rendered);
        $this->assertStringContainsString('name="title"', $rendered);
        $this->assertStringContainsString('Status Label', $rendered);
        $this->assertStringContainsString('Active Check', $rendered);
        $this->assertStringContainsString('Check description', $rendered);
    }

    /**
     * Test that alert and button components render correctly.
     */
    public function test_alert_and_button_components_render()
    {
        $template = <<<'BLADE'
            <x-admin.ui.alert type="success">Operation completed</x-admin.ui.alert>
            <x-admin.ui.button variant="secondary">Cancel</x-admin.ui.button>
        BLADE;

        $rendered = Blade::render($template);
        $this->assertStringContainsString('Operation completed', $rendered);
        $this->assertStringContainsString('Cancel', $rendered);
        $this->assertStringContainsString('bg-emerald-50 dark:bg-emerald-950/20', $rendered);
        $this->assertStringContainsString('bg-gray-100', $rendered);
    }
}
