<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_summary_id',
        'location_id',
        'minutes'
    ];
}
