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
            'location' => new LocationResource($this->whenLoaded('location')),
            'work_type' => new WorkTypeResource($this->whenLoaded('workType')),
            'report' => $this->report,
            // سایر فیلدهای AttendanceRecord
        ];
    }
}
