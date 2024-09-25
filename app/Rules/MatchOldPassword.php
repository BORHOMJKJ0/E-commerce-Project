<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class MatchOldPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = auth()->user();
        $password = $user->password;
        if (! Hash::check($value, $password)) {
            $fail("$attribute is not a valid password");
        }
    }
}
