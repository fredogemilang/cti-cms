<?php

use App\Models\FormField;
use Tests\TestCase;

uses(TestCase::class);

test('form field validates corporate email rule via validation config', function () {
    $field = new FormField([
        'label' => 'Work Email',
        'field_id' => 'work_email',
        'type' => 'email',
        'validation' => ['rule' => 'corporate_email'],
    ]);

    // Corporate domain passes
    expect($field->validateValue('user@company.com'))->toBeTrue();

    // Free provider fails with the rule's default message
    $error = $field->validateValue('user@gmail.com');
    expect($error)->toBeString()
        ->and($error)->toContain('corporate email');
});

test('form field corporate email rule supports custom message', function () {
    $field = new FormField([
        'label' => 'Work Email',
        'field_id' => 'work_email',
        'type' => 'email',
        'validation' => [
            'rule' => 'corporate_email',
            'rule_message' => 'Gunakan email perusahaan.',
        ],
    ]);

    expect($field->validateValue('user@gmail.com'))->toBe('Gunakan email perusahaan.');
});

test('form field corporate email rule skipped for empty value', function () {
    $field = new FormField([
        'label' => 'Work Email',
        'field_id' => 'work_email',
        'type' => 'email',
        'validation' => ['rule' => 'corporate_email'],
    ]);

    expect($field->validateValue(null))->toBeTrue();
});
