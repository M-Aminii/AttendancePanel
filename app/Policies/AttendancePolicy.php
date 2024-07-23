<?php

namespace App\Policies;

use App\Enums\UserStatus;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyAdminRole() || $user->invoices()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {

        return $user->id === $invoice->user_id || $user->hasAnyAdminRole();

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,  AttendanceRecord $AttendanceRecord): bool
    {
        return true;
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AttendanceRecord $AttendanceRecord): bool
    {

        return $user->id === $AttendanceRecord->user_id || $user->hasAnyAdminRole();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        //
    }
}
