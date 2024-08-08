<?php

namespace App\Listeners;

use App\Events\AttendanceApproved;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HandleAttendanceApproved
{
    public function handle(AttendanceApproved $event)
    {
        $attendanceRequest = $event->attendanceRequest;
        DB::beginTransaction();
        try {
            $attendance = Attendance::create([
                'user_id' => $attendanceRequest->user_id,
                'attendance_date' => $attendanceRequest->attendance_date,
                'attendance_details' => $attendanceRequest->attendance_details,
                'is_finalized' => false,
                'total_minutes' => 0, // مقدار اولیه صفر برای total_minutes
            ]);

            $records = json_decode($attendanceRequest->attendance_details, true);
            $totalMinutes = 0; // متغیر برای ذخیره مجموع دقایق

            foreach ($records as $key => $record) {
                // محاسبه مدت زمان بین entry_time و exit_time به دقیقه
                $entry = Carbon::parse($record['entry_time']);
                $exit = Carbon::parse($record['exit_time']);
                $minutes = $exit->diffInMinutes($entry);
                $totalMinutes += $minutes;

                AttendanceRecord::create([
                    'key' => $key + 1, // Assuming 'key' is a unique identifier within the attendance details
                    'attendance_id' => $attendance->id,
                    'user_id' => $attendanceRequest->user_id,
                    'entry_time' => $record['entry_time'],
                    'exit_time' => $record['exit_time'],
                    'location_id' => $record['location_id'],
                    'work_type_id' => $record['work_type_id'],
                    'report' => $record['report'],
                    'minutes' => $minutes, // ذخیره مدت زمان به دقیقه
                ]);
            }

            // به‌روزرسانی مقدار total_minutes در رکورد attendance
            $attendance->update([
                'total_minutes' => $totalMinutes,
            ]);

            DB::commit();
            Log::info('HandleAttendanceApproved: Transaction committed');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error handling attendance approved: ' . $e->getMessage());
        }
    }
}



