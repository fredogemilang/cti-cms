<?php

namespace Tests\Unit;

use App\Models\CustomPostType;
use App\Models\MetaField;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CptMetaFieldTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_sub_fields_is_auto_converted_to_repeater_fields()
    {
        $cpt = CustomPostType::create([
            'name' => 'test_cpt',
            'singular_label' => 'Test CPT',
            'plural_label' => 'Test CPTs',
            'slug' => 'test-cpt',
            'icon' => 'article',
        ]);

        $field = MetaField::create([
            'fieldable_type' => CustomPostType::class,
            'fieldable_id' => $cpt->id,
            'name' => 'test_repeater',
            'label' => 'Test Repeater',
            'type' => 'repeater',
            'options' => [
                'sub_fields' => [
                    ['name' => 'sub1', 'label' => 'Sub 1', 'type' => 'text'],
                ],
            ],
        ]);

        $this->assertArrayHasKey('repeater_fields', $field->fresh()->options);
        $this->assertArrayNotHasKey('sub_fields', $field->fresh()->options);
    }

    public function test_invalid_repeater_keys_are_rejected()
    {
        $this->expectException(ValidationException::class);

        $cpt = CustomPostType::create([
            'name' => 'test_cpt_invalid',
            'singular_label' => 'Test CPT',
            'plural_label' => 'Test CPTs',
            'slug' => 'test-cpt-invalid',
            'icon' => 'article',
        ]);

        MetaField::create([
            'fieldable_type' => CustomPostType::class,
            'fieldable_id' => $cpt->id,
            'name' => 'bad_repeater',
            'label' => 'Bad Repeater',
            'type' => 'repeater',
            'options' => [
                'anak_fields' => [
                    ['name' => 'sub1', 'label' => 'Sub 1', 'type' => 'text'],
                ],
            ],
        ]);
    }
}
