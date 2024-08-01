<?php

namespace App\Events;

use App\Models\AttendanceRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttendanceApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $attendanceRequest;

    public function __construct(AttendanceRequest $attendanceRequest)
    {

        $this->attendanceRequest = $attendanceRequest;
    }
}

