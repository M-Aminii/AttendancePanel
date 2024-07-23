<?php

namespace App\Listeners;

use App\Events\AttendanceFinalized;
use App\Models\AttendanceRecord;
use App\Models\HoursAttendanceRecord;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MoveAttendanceToHours
{
    public function handle(AttendanceFinalized $event)
    {
        $attendanceRecord = $event->attendanceRecord;
        $currentDate = Carbon::now('Asia/Tehran')->toDateString();

        $userRecords = AttendanceRecord::where('user_id', $attendanceRecord->user_id)
            ->whereDate('entry_time', $currentDate)
            ->get();

        $attendanceDetails = $userRecords->map(function($record) {
            return [
                'entry_time' => $record->entry_time,
                'exit_time' => $record->exit_time,
                'location_id' => $record->location_id,
                'work_type_id' => $record->work_type_id,
                'report' => $record->report,
            ];
        })->toArray();

        HoursAttendanceRecord::updateOrCreate(
            ['user_id' => $attendanceRecord->user_id, 'date' => $currentDate],
            ['attendance_details' => $attendanceDetails]
        );
    }
}
