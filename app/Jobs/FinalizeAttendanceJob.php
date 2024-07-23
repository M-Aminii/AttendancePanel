<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\AttendanceRecord;
use App\Events\AttendanceFinalized;
use Carbon\Carbon;

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
        $currentDate = Carbon::now('Asia/Tehran')->toDateString();

        // پیدا کردن تمامی رکوردهایی که نهایی نشده‌اند
        $records = AttendanceRecord::where('is_finalized', false)
            ->whereDate('created_at', '<=', $currentDate)
            ->get();

        foreach ($records as $record) {
            $record->update(['is_finalized' => true]);
            event(new AttendanceFinalized($record));
        }
    }
}

