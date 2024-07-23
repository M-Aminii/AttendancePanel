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
        $AttendanceRecord = AttendanceRecord::find($this->id);

        if (!$AttendanceRecord) {
            throw new NotFoundHttpException('رکوردی برای اپدیت وجود ندارد');
        }

        return Gate::allows('UpdateAttendanceRecord', $AttendanceRecord);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'entry_time' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i|after:entry_time',
            'location_id' => 'nullable|exists:locations,id',
            'work_type_id' => 'nullable|exists:work_types,id',
            'report' => 'nullable|string',
        ];
    }
}
