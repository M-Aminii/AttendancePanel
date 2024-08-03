<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\LocationAttendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\AttendanceRecord;
use App\Events\AttendanceFinalized;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinalizeAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        DB::beginTransaction();
        try {
            $attendances = Attendance::where('is_finalized', false)->get();

            foreach ($attendances as $attendance) {
                $attendance->update(['is_finalized' => true]);

                // محاسبه مجموع زمان حضور برای هر مکان
                $locationMinutes = [];
                $totalMinutes = 0;

                foreach ($attendance->records as $record) {
                    if (!isset($locationMinutes[$record->location_id])) {
                        $locationMinutes[$record->location_id] = 0;
                    }

                    // محاسبه دقایق حضور
                    $entryTime = strtotime($record->entry_time);
                    $exitTime = strtotime($record->exit_time);
                    $minutes = ($exitTime - $entryTime) / 60;


                    $locationMinutes[$record->location_id] += $minutes;
                    $totalMinutes += $minutes;
                }

                // ذخیره‌سازی در جدول location_attendances
                foreach ($locationMinutes as $locationId => $minutes) {
                    LocationAttendance::create([
                        'attendance_id' => $attendance->id,
                        'location_id' => $locationId,
                        'minutes' => $minutes,
                    ]);
                }

                // به‌روزرسانی فیلد total_minutes در جدول attendance
                $attendance->update(['total_minutes' => $totalMinutes]);
            }

            DB::commit();
            Log::info('Attendance records finalized and location attendances calculated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error finalizing attendance records: ' . $e->getMessage());
        }
    }
}

