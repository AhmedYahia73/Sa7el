<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Base64Image implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {        
        if (!$value || !is_string($value)) {
            $fail('The :attribute must be a valid base64 encoded image.');
            return;
        }

        if (!preg_match('/^data:image\/(\w+);base64,/', $value)) {
            $fail('The :attribute must be a valid base64 encoded image.');
            return;
        }

        $data = substr($value, strpos($value, ',') + 1);
        if (!base64_decode($data, true)) {
            $fail('The :attribute must be a valid base64 encoded image.');
        }

    }
}
