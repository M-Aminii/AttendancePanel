<?php

namespace App\Http\Controllers;

use App\Enums\َAttendanceRequestsStatus;
use App\Events\AttendanceApproved;
use App\Http\Requests\AttendanceRequest\CreateAttendanceRequestRequest;
use App\Http\Requests\AttendanceRequest\UpdateAttendanceRequestRequest;
use App\Models\AttendanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\CalendarUtils;

class AttendanceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasAnyAdminRole()) {
            // مدیر تمامی درخواست‌ها را می‌بیند
            $requests = AttendanceRequest::with('user')->get();
        } else {
            // کاربران عادی فقط درخواست‌های خود را می‌بینند
            $requests = AttendanceRequest::where('user_id', Auth::id())->get();
        }

        // تبدیل attendance_details از JSON به آرایه
        $requests->each(function ($request) {
            $request->attendance_details = json_decode($request->attendance_details, true);
        });

        return response()->json($requests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAttendanceRequestRequest $request)
    {
        $data = $request->validated();

        // تبدیل تاریخ شمسی به میلادی
        $jalaliDate = $data['attendance_date'];
        $gregorianDate = CalendarUtils::createCarbonFromFormat('Y/m/d', $jalaliDate)->toDateString();

        // بررسی وجود درخواست دیگر برای همین تاریخ
        $existingRequest = AttendanceRequest::where('attendance_date', $gregorianDate)
            ->where('user_id', Auth::id())
            ->exists();

        if ($existingRequest) {
            return response()->json(['message' => 'در همین تاریخ درخواست دیگری ثبت شده است.'], 400);
        }

        $attendanceRequest = AttendanceRequest::create([
            'user_id' => Auth::id(),
            'attendance_date' => $gregorianDate,
            'attendance_details' => json_encode($data['records']),
        ]);

        return response()->json(['message' => 'درخواست حضور و غیاب با موفقیت ثبت شد'], 201);
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
    public function update(UpdateAttendanceRequestRequest $request, $id)
    {
        $data = $request->validated();

        $attendanceRequest = AttendanceRequest::findOrFail($id);

        // بررسی اینکه آیا رکورد برای همان کاربر است یا خیر و وضعیت در انتظار است
        if ($attendanceRequest->user_id !== Auth::id() || $attendanceRequest->status !== َAttendanceRequestsStatus::PENDING ) {
            return response()->json(['message' => 'Unauthorized or request is not pending'], 403);
        }

        // تبدیل تاریخ شمسی به میلادی
        $jalaliDate = $data['attendance_date'];
        $gregorianDate = CalendarUtils::createCarbonFromFormat('Y/m/d', $jalaliDate)->toDateString();

        $attendanceRequest->update([
            'attendance_date' => $gregorianDate,
            'attendance_details' => json_encode($data['records']),
        ]);

        return response()->json(['message' => 'درخواست حضور و غیاب با موفقیت به‌روزرسانی شد'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changeStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->hasAnyAdminRole()){
            return response()->json(['message' => 'شما مجاز به تغییر وضعیت کاربر نیستید.'], 403);
        }

        $attendanceRequest = AttendanceRequest::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        // بررسی وضعیت قبلی
        if ($attendanceRequest->status !== $data['status']) {
            $attendanceRequest->update(['status' => $data['status']]);

            if ($data['status'] === َAttendanceRequestsStatus::APPROVED) {
                event(new AttendanceApproved($attendanceRequest));
            }
        }

        return response()->json(['message' => 'وضعیت درخواست با موفقیت تغییر کرد'], 200);
    }
}
