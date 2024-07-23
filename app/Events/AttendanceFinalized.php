<?php
// app/Events/AttendanceFinalized.php
namespace App\Events;

use App\Models\AttendanceRecord;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceFinalized
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $attendanceRecord;

    public function __construct(AttendanceRecord $attendanceRecord)
    {
        $this->attendanceRecord = $attendanceRecord;
    }
}

