<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserGender;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'last_name',
        'mobile',
        'email',
        'username',
        'status',
        'gender',
        'password',
        'avatar',
        'about_me',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function hasAnyAdminRole(): bool
    {
        return $this->hasAnyRole(['super-admin']);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Accessor for gender
    public function getGenderAttribute($value)
    {
        return UserGender::from($value);
    }

    // Mutator for gender
    public function setGenderAttribute($value)
    {
        $this->attributes['gender'] = $value instanceof UserGender ? $value->value : UserGender::from($value)->value;
    }

    // Accessor for status
    public function getStatusAttribute($value)
    {
        return UserStatus::from($value);
    }

    // Mutator for status
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value instanceof UserStatus ? $value->value : UserStatus::from($value)->value;
    }
}
