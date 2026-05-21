<?php

namespace App\Settings\Contracts;

/**
 * Implemented by classes wired into a Settings group's `actions` array.
 * SettingsPage resolves the handler via the container and calls handle().
 *
 * The returned array is dispatched as a `notify` toast:
 *   ['type' => 'success|error|info', 'message' => '...']
 */
interface SettingsAction
{
    /**
     * @param  array<string, mixed>  $values  Current (unsaved) form values
     * @return array{type:string,message:string}
     */
    public function handle(array $values): array;
}
