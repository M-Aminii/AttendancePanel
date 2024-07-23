<?php

// app/Observers/AttendanceRecordObserver.php
namespace App\Observers;

use App\Models\AttendanceRecord;
use App\Models\DailyAttendanceRecord;
use Carbon\Carbon;

class AttendanceRecordObserver
{
    public function updated(AttendanceRecord $attendanceRecord)
    {
        if ($attendanceRecord->is_finalized) {
            $currentDate = Carbon::now('Asia/Tehran')->toDateString();

            DailyAttendanceRecord::updateOrCreate(
                [
                    'user_id' => $attendanceRecord->user_id,
                    'date' => $currentDate,
                ],
                [
                    'is_present' => true,
                ]
            );
        }
    }
}
