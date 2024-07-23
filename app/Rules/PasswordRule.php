<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordRule implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        if (strlen($value) < 6 || strlen($value) > 20) {
            return false;
        }
        // چک کردن حداقل یک حرف بزرگ در ابتدا
        if (!preg_match('/^[A-Z]/', $value)) {
            return false;
        }

        if (!preg_match('/[0-9]+/', $value)) {
            return false;
        }

        // چک کردن فقط حروف کوچک و اعداد برای بقیه قسمت‌ها
       /* if (!preg_match('/[a-z]+/', substr($value, 1))) {
            return false;
        }*/

        return true;
    }



    public function message()
    {
        return 'پسورد باید حداقل ۶ کاراکتر، حداکثر ۲۰ کاراکتر و شامل حروف بزرگ در اول کاراکتر، حروف کوچک و اعداد باشد.';
    }
}
