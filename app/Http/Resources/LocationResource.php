<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;


class LocationResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')), // Assuming you have a UserResource for user details
            'name' => $this->name,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_time' => Jalalian::fromDateTime($this->created_at)->format('Y/m/d H:i'),
        ];
    }
}
