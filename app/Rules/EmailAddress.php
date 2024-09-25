<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailAddress implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($attribute.' is not a valid email address, it must be end with a @gmail.com');
        }
    }

    protected function passes(mixed $attribute, mixed $value): bool
    {
        return str_ends_with($value, '@gmail.com');
    }
}
