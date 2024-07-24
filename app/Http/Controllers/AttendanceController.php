<?php

namespace App\Http\Controllers;

use App\Events\AttendanceFinalized;
use App\Http\Requests\Attendance\CreateAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       ///
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAttendanceRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            foreach ($data as $validatedData) {
                $currentDate = Carbon::now('Asia/Tehran')->toDateString();

                $entryDateTime = $validatedData['entry_time'] ? Carbon::createFromFormat('Y-m-d H:i', $currentDate . ' ' . $validatedData['entry_time'], 'Asia/Tehran') : null;
                $exitDateTime = $validatedData['exit_time'] ? Carbon::createFromFormat('Y-m-d H:i', $currentDate . ' ' . $validatedData['exit_time'], 'Asia/Tehran') : null;

                $userRecords = AttendanceRecord::where('user_id', auth()->id())
                    ->whereDate('created_at', $currentDate)
                    ->get();

                foreach ($userRecords as $record) {
                    if ($record->is_finalized) {
                        return response()->json(['error' => 'شما نمیتوانید تاریخ و ورود خروجی ثبت کنید به علت ثبت نهایی کردن'], 403);
                    }
                }

                AttendanceRecord::create([
                    'user_id' => auth()->id(),
                    'entry_time' => $entryDateTime,
                    'exit_time' => $exitDateTime,
                    'location_id' => $validatedData['location_id'],
                    'work_type_id' => $validatedData['work_type_id'],
                    'report' => $validatedData['report'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'ساعت ورود و خروج با موفقیت ثبت شد'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        try {
            DB::beginTransaction();
        $currentDate = Carbon::now('Asia/Tehran')->toDateString();
        $date = $request->input('date') ?? $currentDate ;

        $userId = auth()->id();

        $attendanceRecords = AttendanceRecord::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->get();

            DB::commit();
        return response()->json($attendanceRecords, 200);
            } catch (\Exception $exception) {
        Log::error($exception);
        return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, $id)
    {
        $validatedData = $request->validated();

        try {
            $attendanceRecord = AttendanceRecord::findOrFail($id);

            if ($attendanceRecord->is_finalized) {
                return response()->json(['error' => 'شما نمیتوانید رکورد نهایی شده را ویرایش کنید'], 403);
            }

            if ($attendanceRecord->user_id !== auth()->id()) {
                return response()->json(['error' => 'شما مجاز به ویرایش این رکورد نیستید'], 403);
            }

            $currentDate = Carbon::now('Asia/Tehran')->toDateString();
            $entryDateTime = $validatedData['entry_time'] ? Carbon::createFromFormat('Y-m-d H:i', $currentDate . ' ' . $validatedData['entry_time'], 'Asia/Tehran') : null;
            $exitDateTime = $validatedData['exit_time'] ? Carbon::createFromFormat('Y-m-d H:i', $currentDate . ' ' . $validatedData['exit_time'], 'Asia/Tehran') : null;

            $attendanceRecord->update([
                'entry_time' => $entryDateTime ?? $attendanceRecord['entry_time'],
                'exit_time' => $exitDateTime ?? $attendanceRecord['exit_time'],
                'location_id' => $validatedData['location_id'] ?? $attendanceRecord['location_id'],
                'work_type_id' => $validatedData['work_type_id'] ?? $attendanceRecord['work_type_id'],
                'report' => $validatedData['report'] ?? $attendanceRecord['report'],
            ]);

            return response()->json(['message' => 'رکورد با موفقیت به‌روزرسانی شد'], 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }




    public function finalize(Request $request)
    {
        try {
            DB::beginTransaction();

            $currentDate = Carbon::now('Asia/Tehran')->toDateString();
            $date = $request->input('date') ?? $currentDate ;
            $userId = auth()->id();

            // دریافت رکوردهای کاربر در تاریخ مشخص شده
            $records = AttendanceRecord::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->get();

            if ($records->isEmpty()) {
                return response()->json(['error' => 'هیچ رکوردی برای نهایی کردن یافت نشد.'], 404);
            }

            foreach ($records as $record) {
                if ($record->is_finalized) {
                    continue; // اگر رکورد قبلاً نهایی شده باشد، به آن دست نزنید
                }
                $record->is_finalized = true;
                $record->save();
            }
            event(new AttendanceFinalized($userId, $currentDate, $records));

            DB::commit();
            return response()->json(['message' => 'رکوردها با موفقیت نهایی شدند'], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }

/*    public function finalizeAllUnfinalized(Request $request)
    {
        $userId = auth()->id();

        // دریافت تمام رکوردهای غیر نهایی کاربر
        $records = AttendanceRecord::where('user_id', $userId)
            ->where('is_finalized', false)
            ->get();

        if ($records->isEmpty()) {
            return response()->json(['error' => 'هیچ رکورد غیر نهایی برای نهایی کردن یافت نشد.'], 404);
        }

        foreach ($records as $record) {
            $record->is_finalized = true;
            $record->save();
        }

        return response()->json(['message' => 'تمام رکوردهای غیر نهایی با موفقیت نهایی شدند'], 200);
    }*/


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $attendanceRecord = AttendanceRecord::findOrFail($id);

            if ($attendanceRecord->is_finalized) {
                return response()->json(['error' => 'شما نمیتوانید رکورد نهایی شده را حذف کنید'], 403);
            }

            if ($attendanceRecord->user_id !== auth()->id()) {
                return response()->json(['error' => 'شما مجاز به حذف این رکورد نیستید'], 403);
            }

            $attendanceRecord->delete();

            return response()->json(['message' => 'رکورد با موفقیت حذف شد'], 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }

}
