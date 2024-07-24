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

    public $userId;
    public $date;
    public $records;

    public function __construct($userId, $date, $records)
    {
        $this->userId = $userId;
        $this->date = $date;
        $this->records = $records;
    }
}

