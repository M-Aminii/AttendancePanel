<?php

namespace App\Http\Requests\User;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:100', 'unique:users,username'],
            'mobile' => ['required', new MobileRule ,'unique:users,mobile'],
            'email' => ['nullable' ,'unique:users,email' ],
            'gender' =>  'nullable|in:' . implode(',', UserGender::toValues()),
            'status' => 'nullable|in:' . implode(',', UserStatus::toValues()),
            'role' => ['required', 'exists:roles,id'],
            'password' => ['required', new PasswordRule],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'about_me' => ['nullable', 'string','max:255']
        ];
    }
}
