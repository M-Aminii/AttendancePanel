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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAttendanceRequest $request)
    {
        $validatedData = $request->validated();
        try {
            DB::beginTransaction();

        $currentDate = Carbon::now('Asia/Tehran')->toDateString();

        // ترکیب تاریخ جاری با زمان‌های ورودی
        $entryDateTime = $validatedData['entry_time'] ? Carbon::createFromFormat('Y-m-d H:i', $currentDate . ' ' . $validatedData['entry_time'], 'Asia/Tehran') : null;
        $exitDateTime =  $validatedData['exit_time'] ? Carbon::createFromFormat('Y-m-d H:i', $currentDate . ' ' . $validatedData['exit_time'], 'Asia/Tehran') : null;

        // چک کردن نهایی بودن رکوردهای قبلی کاربر در روز جاری
        $userRecords = AttendanceRecord::where('user_id', auth()->id())
            ->whereDate('created_at', $currentDate)
            ->get();

        foreach ($userRecords as $record) {
            if ($record->is_finalized) {
                return response()->json(['error' => 'You cannot add a new entry/exit because one of your records has been finalized.'], 403);
            }
        }
        // ثبت ورود و خروج جدید
         AttendanceRecord::create([
            'user_id' => auth()->id(),
            'entry_time' => $entryDateTime,
            'exit_time' => $exitDateTime,
            'location_id' =>  $validatedData['location_id'],
            'work_type_id' =>  $validatedData['work_type_id'],
            'report' =>  $validatedData['report'],
        ]);

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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, $id)
    {
        $validatedData = $request->validated();
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $record = AttendanceRecord::findOrFail($id);


            // چک کردن نهایی بودن رکورد
            if ($record->is_finalized && !$user->hasAnyAdminRole()) {
                return response()->json(['error' => 'You cannot update a finalized record.'], 403);
            }


            $currentDate = Carbon::now('Asia/Tehran')->toDateString();

            // ترکیب تاریخ جاری با زمان‌های ورودی
            $entryDateTime = $validatedData['entry_time'] ? Carbon::createFromFormat('Y-m-d H:i', $currentDate . ' ' . $validatedData['entry_time'], 'Asia/Tehran') : $record->entry_time;
            $exitDateTime = $validatedData['exit_time'] ? Carbon::createFromFormat('Y-m-d H:i', $currentDate . ' ' . $validatedData['exit_time'], 'Asia/Tehran') : $record->exit_time;

            // به روزرسانی رکورد
            $record->update([
                'entry_time' => $entryDateTime ?? $record->entry_time,
                'exit_time' => $exitDateTime ?? $record->exit_time,
                'location_id' =>  $validatedData['location_id'] ?? $record->location_id,
                'work_type_id' =>  $validatedData['work_type_id'] ?? $record->work_type_id,
                'report' =>  $validatedData['report'] ?? $record->report,
                'is_finalized' => $validatedData['is_finalized'] ?? $record->is_finalized
            ]);
            DB::commit();
            return response()->json(['message' => 'ساعت ورود و خروج با موفقیت بروزرسانی شد'], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response()->json(['message' => 'خطایی به وجود آمده است: ' . $exception->getMessage()], 500);
        }
    }



    public function finalize()
    {
        $currentDate = Carbon::now('Asia/Tehran')->toDateString();
        $userId = auth()->id();
        $userRecords = AttendanceRecord::where('user_id', $userId)
            ->whereDate('created_at', $currentDate)
            ->get();
        // چک کردن اینکه آیا رکوردی برای نهایی شدن وجود دارد یا خیر
        if ($userRecords->isEmpty()) {
            return response()->json(['error' => 'هیچ رکوردی برای نهایی شدن یافت نشد.'], 404);
        }

        // چک کردن و به روز رسانی فقط رکوردهای روز جاری
        foreach ($userRecords as $record) {
            $record->update(['is_finalized' => true]);
            event(new AttendanceFinalized($record));
        }

        return response()->json(['message' => 'رکوردهای روز جاری با موفقیت نهایی شد.'], 200);
    }


    public function getCurrentDayRecords()
    {
        $currentDate = Carbon::now('Asia/Tehran')->toDateString();
        $userRecords = AttendanceRecord::where('user_id', auth()->id())
            ->whereDate('entry_time', $currentDate)
            ->get();

        return response()->json($userRecords);
    }

    public function getPreviousRecords()
    {
        $currentDate = Carbon::now('Asia/Tehran')->toDateString();
        $userRecords = AttendanceRecord::where('user_id', auth()->id())
            ->whereDate('entry_time', '<', $currentDate)
            ->get();

        return response()->json($userRecords);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
