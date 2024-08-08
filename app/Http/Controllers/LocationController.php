<?php

namespace App\Http\Controllers;

use App\Enums\LocationStatus;
use App\Http\Requests\Location\CreateLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
            $requests = Location::get();

        // استفاده از AttendanceResource برای فرمت‌دهی داده‌ها
        return LocationResource::collection($requests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLocationRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();

             Location::create([
                'user_id' => auth()->id(),
                'name' => $data['name'],
                'status' => LocationStatus::ACTIVE,
            ]);

            DB::commit();
            return response()->json(['message' => 'پروژه مورد نظر با موفقیت ایجاد شد'], 201);
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
    public function show(string $id)
    {
        $requests = Location::where('id', $id)->get();
        return LocationResource::collection($requests);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, string $id)
    {
        $data = $request->validated();

        $Location = Location::findOrFail($id);
        try {
            DB::beginTransaction();

            $Location->update([
                'name' => $data['name'],
                'status' => $data['status'] ?? $Location->status ,
            ]);

            DB::commit();
            return response()->json(['message' => 'پروژه مورد نظر با موفقیت بروزرسانی شد'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            // Log the exception for further investigation
            Log::error('Attendance store error: '.$exception->getMessage());
            return response()->json(['message' => 'خطایی به وجود آمده است، لطفا دوباره تلاش کنید.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
