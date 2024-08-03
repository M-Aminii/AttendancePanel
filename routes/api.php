<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class ,'login']);
    Route::post('logout', [AuthController::class ,'logout']);
    Route::post('refresh', [AuthController::class ,'refresh']);
    Route::post('me', [AuthController::class ,'me']);
});

Route::group(['middleware' => ['auth:api'],'prefix' => 'Personnel'], function () {


    Route::group(['prefix' => '/user'], function () {
        Route::post('/', [UserController::class, 'store']);
        Route::get('/list', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::patch('/{id}', [UserController::class, 'update']);
    });


    Route::group(['prefix' => '/attendance'], function () {
        Route::post('/', [AttendanceController::class, 'store']);
        Route::get('/list', [AttendanceController::class, 'index']);
        Route::patch('/{id}', [AttendanceController::class, 'update']);
        Route::get('/{id}', [AttendanceController::class, 'show']);
        Route::patch('/finalize/{id}', [AttendanceController::class, 'finalize']);
        Route::delete('/{id}', [AttendanceController::class, 'destroy']);
    });


    Route::group(['prefix' => '/attendance_request'], function () {
        Route::post('/', [AttendanceRequestController::class, 'store']);
        Route::get('/list', [AttendanceRequestController::class, 'index']);
        Route::patch('/{id}', [AttendanceRequestController::class, 'update']);
        Route::get('/{id}', [AttendanceRequestController::class, 'show']);
        Route::patch('/status/{id}', [AttendanceRequestController::class, 'changeStatus']);

    });

});
