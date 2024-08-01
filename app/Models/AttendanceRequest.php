<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'attendance_details',
        'status',
    ];

    protected $casts = [
        'attendance_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
