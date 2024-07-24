<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'attendance_details',
        'total_minutes'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
