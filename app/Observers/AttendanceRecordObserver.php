<?php

// app/Observers/AttendanceRecordObserver.php
namespace App\Observers;

use App\Events\AttendanceFinalized;

use Carbon\Carbon;

class AttendanceRecordObserver
{
    public  function handle(AttendanceFinalized $event)
{
    $userId = $event->userId;
    $date = $event->date;
    $records = $event->records;

    $timesData = [];
    $locationMinutes = [];
    $totalMinutes = 0;

    foreach ($records as $record) {
        $entryTime = Carbon::parse($record->entry_time);
        $exitTime = Carbon::parse($record->exit_time);
        $duration = $entryTime->diffInMinutes($exitTime);

        $timesData[] = [
            'entry_time' => $entryTime->toTimeString(),
            'exit_time' => $exitTime->toTimeString(),
            'location_id' => $record->location_id,
            'work_type_id' => $record->work_type_id
        ];

        $locationId = $record->location_id;
        if (!isset($locationMinutes[$locationId])) {
            $locationMinutes[$locationId] = 0;
        }
        $locationMinutes[$locationId] += $duration;
        $totalMinutes += $duration;
    }

    // ذخیره اطلاعات در جدول خلاصه‌ی کلی ساعات
    $attendanceSummary = AttendanceSummary::create([
        'user_id' => $userId,
        'date' => $date,
        'times_json' => json_encode($timesData),
        'total_minutes' => $totalMinutes,
    ]);

    // ذخیره اطلاعات مکان‌ها در جدول جداگانه
    foreach ($locationMinutes as $locationId => $minutes) {
        LocationSummary::create([
            'attendance_summary_id' => $attendanceSummary->id,
            'location_id' => $locationId,
            'minutes' => $minutes,
        ]);
    }
}
}
