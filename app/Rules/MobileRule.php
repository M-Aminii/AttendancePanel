<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MobileRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^(0098|\+?98|0)9[0-9]{9}$/', $value)) {
            $fail('شماره موبایل وارد شده اشتباه میباشد. لطفا یک شماره موبایل معتبر وارد کنید.');
        }
    }


    public function message()
    {
        return 'شماره موبایل را با فرمت صحیح وارد کنید';
    }
}

