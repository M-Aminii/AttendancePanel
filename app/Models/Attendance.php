<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table ='attendance';
    protected $fillable = ['user_id','attendance_details','total_minutes','is_finalized'];


    protected $casts = [
        'attendance_details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
