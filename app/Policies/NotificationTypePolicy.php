<?php

namespace App\Policies;

use App\Models\User;

class NotificationTypePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-notification-type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view-any-notification-type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit-any-notification-type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete-any-notification-type');
    }

    public function bulkDelete(User $user): bool
    {
        return $user->hasPermissionTo('delete-bulk-notification-type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return $user->hasPermissionTo('restore-any-notification-type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo('force-delete-any-notification-type');
    }

    /**
     * Determine whether the user can bulk delete the model.
     */
    public function bulkForceDelete(User $user): bool
    {
        return $user->hasPermissionTo('force-delete-bulk-notification-type');
    }
}
