<?php

namespace App\DTO;

use App\Enums\UserGender;
use App\Enums\UserStatus;
use Illuminate\Support\Str;

class UserDTO
{

    public $name;
    public $last_name;
    public $mobile;
    public $email;
    public $username;
    public $status;
    public $gender;
    public $password;
    public $avatar;
    public $about_me;







    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->last_name = $data['last_name'];
        $this->mobile = $data['mobile'];
        $this->email = $data['email'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->status =$data['status'] ?? UserStatus::ACTIVE;
        $this->gender =$data['gender'] ?? UserGender::GENDER_MAN;
        $this->password = $data['password'];
        $this->avatar = $this->saveAvatar($data['avatar']) ?? null;
        $this->about_me = $data['about_me'] ?? null;
    }

    private function saveAvatar($avatar)
    {
        if ($avatar) {
            $fileName = time() . Str::random(10) . '-avatar';
            $path = public_path('users/'.auth()->id());
            $avatar->move($path, $fileName);
            return $fileName;
        }
        return null;
    }

}
