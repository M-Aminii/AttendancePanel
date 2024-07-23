<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoursAttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'attendance_details', //'is_finalized'
    ];

    protected $casts = [
        'attendance_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
