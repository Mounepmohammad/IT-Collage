<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ArabicOnly implements Rule
{
    public function passes($attribute, $value)
    {
        // تحقق من أن النص يحتوي فقط على حروف عربية ومسافات
        return preg_match('/^[\p{Arabic}\s]+$/u', $value);
    }

    public function message()
    {
        return 'The :attribute must contain only Arabic letters.';
    }
}

