<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;


class AttendanceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Decode the JSON attendance_detail to array
        $records = json_decode($this->attendance_details, true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('JSON decode error: ' . json_last_error_msg());
        }

        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')), // Assuming you have a UserResource for user details
            'attendance_date' => Jalalian::fromDateTime($this->attendance_date)->format('Y/m/d'),
            'records' => $records,
            'status' => $this->status,
        ];
    }
}
