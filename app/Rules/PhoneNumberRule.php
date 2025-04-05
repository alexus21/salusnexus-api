<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PhoneNumberRule implements ValidationRule {
    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void {
        if (!preg_match('/^(\+503\s)?([267])\d{3}(-)?\d{4}$/', $value)) {
            $fail("El campo $attribute no tiene un formato de número de teléfono válido");
        }
    }
}
