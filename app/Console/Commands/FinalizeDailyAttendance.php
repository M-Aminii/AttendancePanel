<?php

namespace App\Console\Commands;

use App\Jobs\FinalizeAttendanceJob;
use Illuminate\Console\Command;
use App\Models\Attendance;

class FinalizeDailyAttendance extends Command
{
    protected $signature = 'attendance:finalize';
    protected $description = 'Finalize attendance records at the end of each day';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        FinalizeAttendanceJob::dispatch();
        $this->info('FinalizeAttendanceJob has been dispatched.');
    }
}

