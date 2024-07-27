<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' =>$this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'username' => $this->username,
            'status' => $this->status,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'about_me' => $this->about_me,
        ];
    }
}
