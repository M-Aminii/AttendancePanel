<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\AttendanceRecord;
use App\Policies\AttendancePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        AttendanceRecord::class => AttendancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        //Gate::define('create', [AttendancePolicy::class, 'create']);
        //Gate::define('viewAny', [AttendancePolicy::class, 'viewAny']);
        //Gate::define('view', [AttendancePolicy::class, 'view']);
        Gate::define('UpdateAttendanceRecord', [AttendancePolicy::class, 'update']);
    }
}
