<?php

namespace App\Exceptions;

use Exception;

class PluginDependencyException extends Exception
{
    protected array $dependents;

    public function __construct(string $pluginName, array $dependentNames)
    {
        $this->dependents = $dependentNames;
        $list = implode("', '", $dependentNames);
        parent::__construct(
            "Cannot deactivate '{$pluginName}': the following active plugins depend on it: '{$list}'."
        );
    }

    public function getDependents(): array
    {
        return $this->dependents;
    }
}
