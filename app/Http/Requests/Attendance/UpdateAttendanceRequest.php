<?php

namespace App\Http\Requests\Attendance;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use App\Models\AttendanceRecord;
use App\Rules\MobileRule;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateAttendanceRequest extends FormRequest
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
            'records' => 'required|array',
            'records.*.entry_time' => 'required|date_format:H:i',
            'records.*.exit_time' => 'nullable|date_format:H:i',
            'records.*.location_id' => 'required|integer',
            'records.*.work_type_id' => 'required|integer',
            'records.*.report' => 'nullable|string',
        ];
    }
}
