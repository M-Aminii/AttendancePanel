<?php

// app/Observers/AttendanceRecordObserver.php
namespace App\Observers;

use App\Events\AttendanceFinalized;

use App\Models\Attendance;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class AttendanceRecordObserver
{
    /*public function creating(AttendanceRecord $record)
    {
        // پیدا کردن حضور مربوط به روز قبلی
        $previousAttendance = Attendance::where('user_id', $record->user_id)
            ->where('attendance_date', '<', Carbon::today())
            ->where('is_finalized', false)
            ->first();

        if ($previousAttendance) {
            // به‌روزرسانی وضعیت نهایی حضور
            $previousAttendance->update(['is_finalized' => true]);

            // محاسبه مجموع دقایق حضور برای هر مکان
            $locationMinutes = [];
            $totalMinutes = 0;

            foreach ($previousAttendance->records as $rec) {
                if (!isset($locationMinutes[$rec->location_id])) {
                    $locationMinutes[$rec->location_id] = 0;
                }

                $entryTime = strtotime($rec->entry_time);
                $exitTime = strtotime($rec->exit_time);
                $minutes = ($exitTime - $entryTime) / 60;

                $locationMinutes[$rec->location_id] += $minutes;
                $totalMinutes += $minutes;
            }

            // ذخیره‌سازی در جدول location_attendances
            foreach ($locationMinutes as $locationId => $minutes) {
                \App\Models\LocationAttendance::create([
                    'user_id' => $previousAttendance->user_id,
                    'attendance_id' => $previousAttendance->id,
                    'location_id' => $locationId,
                    'minutes' => $minutes,
                ]);
            }

            // به‌روزرسانی فیلد total_minutes در جدول attendance
            $previousAttendance->update(['total_minutes' => $totalMinutes]);
        }
    }*/

}
