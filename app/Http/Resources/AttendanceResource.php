<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;


class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user_id,
            //'attendance_details' => json_decode($this->attendance_details),
            'attendance_date'=> Jalalian::fromDateTime($this->attendance_date)->format('Y/m/d'),
            'total_minutes'=>$this->total_minutes,
            'is_finalized'=>$this->is_finalized,
            //'updated_at' => Jalalian::fromCarbon($this->updated_at)->format('Y/m/d'),
            'records' => RecordResource::collection($this->whenLoaded('records')),
        ];
    }
}
