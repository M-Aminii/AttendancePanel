<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'location_id',
        'minutes'
    ];
}
