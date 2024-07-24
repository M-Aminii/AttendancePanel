<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CreateAttendanceRequest extends FormRequest
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
            '*.entry_time' => 'required|date_format:H:i',
            '*.exit_time' => 'required|date_format:H:i|after:*.entry_time',
            '*.location_id' => 'required|exists:locations,id',
            '*.work_type_id' => 'nullable|exists:work_types,id',
            '*.report' => 'nullable|string',
        ];
    }
}
