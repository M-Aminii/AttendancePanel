<?php

namespace App\Http\Controllers;

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
        if ($attendanceRequest->user_id !== Auth::id() || $attendanceRequest->status !== 'pending') {
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
            return response()->json(['message' => 'دسترسی تغییر وضعیت به شما داده نشده است.'], 403);
        }

        $attendanceRequest = AttendanceRequest::findOrFail($id);

        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $attendanceRequest->update(['status' => $data['status']]);

        if ($data['status'] === 'approved') {

            event(new AttendanceApproved($attendanceRequest));
        }

        return response()->json(['message' => 'وضعیت درخواست با موفقیت تغییر کرد'], 200);
    }
}
