<?php

namespace App\Rules\Username;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotBlacklisted implements ValidationRule
{
    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public $implicit = true;

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array(strtolower($value), config('mailflusher.blacklist'))) {
            $fail('The :attribute has already been taken.');
        }
    }
}
