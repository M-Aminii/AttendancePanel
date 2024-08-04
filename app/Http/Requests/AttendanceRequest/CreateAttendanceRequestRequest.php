<?php

namespace App\Http\Requests\AttendanceRequest;

use Illuminate\Foundation\Http\FormRequest;

class CreateAttendanceRequestRequest extends FormRequest
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
            'attendance_date' => 'required|string',
            'records' => 'required|array',
            'records.*.entry_time' => 'required|date_format:H:i',
            'records.*.exit_time' => 'required|date_format:H:i',
            'records.*.location_id' => 'required|integer',
            'records.*.work_type_id' => 'required|integer',
            'records.*.report' => 'nullable|string',
        ];
    }



}
