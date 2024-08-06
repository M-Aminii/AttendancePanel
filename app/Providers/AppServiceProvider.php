<?php

namespace App\Providers;

use App\Models\AttendanceRecord;
use App\Observers\AttendanceRecordObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        AttendanceRecord::observe(AttendanceRecordObserver::class);

        JsonResource::withoutWrapping();
    }
}
