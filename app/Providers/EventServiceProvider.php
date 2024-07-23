<?php

namespace App\Providers;

use App\Events\AttendanceFinalized;
use App\Listeners\MoveAttendanceToHours;
use App\Models\AttendanceRecord;
use App\Observers\AttendanceRecordObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AttendanceFinalized::class => [
            MoveAttendanceToHours::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
        AttendanceRecord::observe(AttendanceRecordObserver::class);

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
