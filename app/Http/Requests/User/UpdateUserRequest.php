<?php

namespace App\Http\Requests\User;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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

            'name' => [ 'string', 'max:100'],
            'last_name' => [ 'string', 'max:100'],
            // در فیلد rules
            'username' => ['nullable', 'string', 'max:100', Rule::unique('users')->ignore($this->route('id'))],
            'mobile' => ['nullable', new MobileRule, Rule::unique('users')->ignore($this->route('id'))],
            'email' => ['nullable', 'email', Rule::unique('users')->ignore($this->route('id'))],
            'gender' =>  'nullable|in:' . implode(',', UserGender::toValues()),
            'status' => 'nullable|in:' . implode(',', UserStatus::toValues()),
            'role' => [ 'exists:roles,id'],
            'password' => [ new PasswordRule],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'about_me' => ['nullable', 'string','max:255']
        ];
    }
}
