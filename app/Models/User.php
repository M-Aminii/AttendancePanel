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
    use HasApiTokens, HasFactory, Notifiable , HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => UserStatus::class,
        'gender'=> UserGender::class,
    ];


    public function hasAnyAdminRole(): bool
    {
        return $this->hasAnyRole(['super-admin']);
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Accessor
    public function getGenderAttribute($value)
    {
        return new UserGender($value);
    }

    // Mutator
    public function setGenderAttribute($value)
    {
        if ($value instanceof UserGender) {
            $this->attributes['gender'] = $value->value;
        } else {
            $this->attributes['gender'] = (new UserGender($value))->value;
        }
    }
    // Accessor
    public function getStatusAttribute($value)
    {
        return new UserStatus($value);
    }

    // Mutator
    public function setStatusAttribute($value)
    {
        if ($value instanceof UserStatus) {
            $this->attributes['status'] = $value->value;
        } else {
            $this->attributes['status'] = (new UserStatus($value))->value;
        }
    }
}
