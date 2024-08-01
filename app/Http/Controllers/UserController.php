<?php

namespace App\Http\Controllers;

use App\DTO\UserDTO;
use App\Enums\UserStatus;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = User::query();
        if (!$query){
            throw new NotFoundHttpException('کاربری وجود نداد');
        }
        // انتخاب فیلدهای مورد نیاز
        $fields = [
            'id',
            'name',
            'last_name',
            'mobile',
            'email',
            'status',
            'gender',
            'created_at',
            'updated_at',
        ];
        // انجام کوئری و بازگشت نتیجه
        $users = $query->with('roles:id,name')->select($fields)->get();
        // بازگشت نتیجه به عنوان پاسخ
        return response()->json($users);

    }

    /**
     * Store a newly created resource in storage.
     */
    //TODO:اضافه کردن ایونت برای زمانی که یک تصویر برای کاربر اضافه میشه

    public function store(CreateUserRequest $request)
    {

        try {
            DB::beginTransaction();
            $userDTO = new UserDTO($request->validated());
            $user = User::create((array) $userDTO);
            $role = Role::findById($request->role);
            $user->assignRole($role);

            DB::commit();

            return response(['message' => 'مشخصات فردی ثبت شد'], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // جستجوی کاربر با استفاده از شناسه
            $user = User::with('roles:id,name')->findOrFail($id);
            // بازگشت نتیجه به عنوان پاسخ
            return response()->json($user);
        } catch (ModelNotFoundException $exception) {
            // در صورتی که کاربر پیدا نشود، یک استثنا پرتاب شود
            return response(['message' => 'کاربر مورد نظر یافت نشد'], 404);
        } catch (Exception $exception) {
            // در صورتی که خطای دیگری رخ دهد، خطای دیفالت بازگردانده می‌شود
            return response()->json(['message' => 'خطایی به وجود آمده است'], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            // یافتن کاربر
            $user = User::findOrFail($id);
            // پر کردن فیلدهای داده‌ای با استفاده از DTO
            $user->fill($request->validated());
            // بررسی تغییرات قبل از ذخیره
            if ($user->isDirty()) {
                // ذخیره کاربر
                $user->save();
            }
            // یافتن نقش
            if ($request->role){
                $role = Role::findById($request->role);
                $user->syncRoles($role);
            }
            // همگام سازی نقش کاربر
           //dd($user->getChanges());
            DB::commit();
            return response()->json(['message' => 'اطلاعات کاربر با موفقیت بروزرسانی شد'], 200);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            return response(['message' => 'کاربر مورد نظر یافت نشد'], 404);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception);
            return response(['message' => 'خطایی به وجود آمده است'], 500);
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
