<?php

namespace App\Http\Controllers;

use App\Events\AttendanceFinalized;
use App\Http\Requests\Attendance\CreateAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\AttendanceRecord;
use App\Models\LocationAttendance;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
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
        $userId = auth()->id();
        // بارگذاری تمامی رکوردهای حضور و غیاب به همراه رکوردهای مربوطه
        $attendances = Attendance::with('records')->where('user_id',$userId)->get();

        // استفاده از AttendanceResource برای فرمت‌دهی داده‌ها
        return AttendanceResource::collection($attendances);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAttendanceRequest $request)
    {
        $data = $request->validated();


        try {
            DB::beginTransaction();

            $currentDate = Carbon::now()->toDateString();

            // بررسی موجود بودن رکورد حضور برای کاربر در همان روز
            $existingAttendance = Attendance::where('user_id', auth()->id())
                ->whereDate('created_at', $currentDate)
                ->first();

            if ($existingAttendance) {
                return response()->json(['message' => 'ساعت ورود و خروج امروز از قبل ثبت شده است.'], 409);
            }

            $jsonData = json_encode($request->records);

            // ایجاد رکورد جدید در جدول attendance
            $attendance = Attendance::create([
                'user_id' => auth()->id(),
                'attendance_details' => $jsonData,
                'is_finalized' => false,
            ]);


            foreach ($data['records'] as $validatedData) {

                $entryTime = $validatedData['entry_time'];
                $exitTime = $validatedData['exit_time'];

                // محاسبه مقدار key برای رکورد جدید
                $currentMaxKey = AttendanceRecord::where('attendance_id', $attendance->id)->max('key');
                $newKey = $currentMaxKey ? $currentMaxKey + 1 : 1;

                AttendanceRecord::create([
                    'attendance_id' => $attendance->id,
                    'user_id' => auth()->id(),
                    'key' => $newKey,
                    'entry_time' => $entryTime,
                    'exit_time' => $exitTime,
                    'location_id' => $validatedData['location_id'],
                    'work_type_id' => $validatedData['work_type_id'],
                    'report' => $validatedData['report'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'ساعت ورود و خروج با موفقیت ثبت شد'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            // Log the exception for further investigation
            Log::error('Attendance store error: '.$exception->getMessage());
            return response()->json(['message' => 'خطایی به وجود آمده است، لطفا دوباره تلاش کنید.'], 500);
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
   /* public function update(UpdateAttendanceRequest $request, $id)
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
    }*/

    public function update(Request $request, string $id)
    {
        // یافتن رکورد حضور
        $attendance = Attendance::findOrFail($id);

        // بررسی اینکه آیا رکورد برای همان کاربر است یا خیر
        if ($attendance->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // بررسی وضعیت فعلی
        if ($attendance->is_finalized) {
            return response()->json(['message' => 'این حضور از قبل نهایی شده است و امکان اپدیت وجود ندارد.'], 400);
        }

        // به روز رسانی رکوردهای attendance_details
        $attendance->attendance_details = json_encode($request->records); // نیازی به json_encode نیست
        $attendance->save();

        // به روز رسانی رکوردهای مرتبط با حضور
        $existingRecordIds = $attendance->records->pluck('id')->toArray();


        $newRecordIds = array_column($request->records, 'id');

        // حذف رکوردهایی که در درخواست نیامده‌اند
        $toDelete = array_diff($existingRecordIds, $newRecordIds);
        AttendanceRecord::destroy($toDelete);

        // به روز رسانی یا ایجاد رکوردهای جدید
        foreach ($request->records as $recordData) {
            if (isset($recordData['id'])) {
                // به روز رسانی رکورد موجود
                $record = AttendanceRecord::find($recordData['id']);
                if ($record) {
                    $record->update([
                        'entry_time' => $recordData['entry_time'],
                        'exit_time' => $recordData['exit_time'],
                        'location_id' => $recordData['location_id'],
                        'work_type_id' => $recordData['work_type_id'],
                        'report' => $recordData['report'],
                    ]);
                }
            } else {
                // ایجاد رکورد جدید
                $attendance->records()->create([
                    'user_id' => auth()->id(),
                    'key' => AttendanceRecord::where('attendance_id', $attendance->id)->max('key') + 1,
                    'entry_time' => $recordData['entry_time'],
                    'exit_time' => $recordData['exit_time'],
                    'location_id' => $recordData['location_id'],
                    'work_type_id' => $recordData['work_type_id'],
                    'report' => $recordData['report'],
                ]);
            }
        }

        return response()->json(['message' => 'رکوردها با موفقیت به‌روزرسانی شدند'], 200);
    }




    public function finalize(Request $request,$id)
    {
        try {
            DB::beginTransaction();

            // پیدا کردن رکورد مورد نظر
            $attendance = Attendance::findOrFail($id);

            // بررسی وضعیت فعلی
            if ($attendance->is_finalized) {
                return response()->json(['message' => 'این حضور از قبل نهایی شده است.'], 400);
            }

            // تغییر وضعیت is_finalized به true
            $attendance->is_finalized = true;

            // محاسبه مجموع دقایق حضور
            $totalMinutes = 0;
            $locationMinutes = [];

            foreach ($attendance->records as $record) {
                $entryTime = Carbon::parse($record->entry_time);
                $exitTime = Carbon::parse($record->exit_time);
                $minutes = $exitTime->diffInMinutes($entryTime);

                $totalMinutes += $minutes;

                if (!isset($locationMinutes[$record->location_id])) {
                    $locationMinutes[$record->location_id] = 0;
                }
                $locationMinutes[$record->location_id] += $minutes;
            }

            $attendance->total_minutes = $totalMinutes;
            $attendance->save();

            // ذخیره اطلاعات حضور در هر لوکیشن یا به‌روزرسانی رکوردهای موجود
            foreach ($locationMinutes as $locationId => $minutes) {
                $locationAttendance = LocationAttendance::firstOrNew([
                    'attendance_id' => $attendance->id,
                    'location_id' => $locationId,
                ]);
                $locationAttendance->minutes = $minutes;
                $locationAttendance->save();
            }
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
