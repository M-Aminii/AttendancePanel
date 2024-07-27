<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class RecordResource extends JsonResource
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
            'entry_time' => Jalalian::forge($this->entry_time)->format('H:i'),
            'exit_time' => Jalalian::forge($this->exit_time)->format('H:i'),
            'location_id' => $this->location_id,
            'work_type_id' => $this->work_type_id,
            'report' => $this->report,
            // سایر فیلدهای AttendanceRecord
        ];
    }
}
