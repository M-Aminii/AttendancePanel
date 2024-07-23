<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManagerApprovalController extends Controller
{
    public function removeFinalization($userId)
    {
        $currentDate = Carbon::now('Asia/Tehran')->toDateString();

        $userRecords = AttendanceRecord::where('user_id', $userId)
            ->whereDate('created_at', $currentDate)
            ->get();

        foreach ($userRecords as $record) {
            $record->update(['is_finalized' => false]);
        }

        return response()->json(['status' => 'وضعیت نهایی رکوردهای کاربر برداشته شد.']);
    }
}

