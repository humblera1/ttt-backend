<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Auth\Access\Response;

class UserNotificationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-notification');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserNotification $userNotification): bool
    {
        return $user->hasPermissionTo('view-own-notification') && $user->id === $userNotification->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-any-notification');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit-any-notification');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserNotification $userNotification): bool
    {
        if ($user->hasPermissionTo('delete-own-notification') && $user->id === $userNotification->user_id) {
            return true;
        }

        return $user->hasPermissionTo('delete-any-notification');
    }

    /**
     * Determine whether the user can bulk delete the models.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->hasPermissionTo('delete-bulk-notification');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserNotification $userNotification): bool
    {
        if ($user->hasPermissionTo('restore-own-notification') && $user->id === $userNotification->user_id) {
            return true;
        }

        return $user->hasPermissionTo('restore-any-notification');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserNotification $userNotification): bool
    {
        if (
            $user->hasPermissionTo('force-delete-own-notification')
            && $user->id === $userNotification->user_id
        ) {
            return true;
        }

        return $user->hasPermissionTo('force-delete-any-notification');
    }

    /**
     * Determine whether the user can bulk permanently delete the models.
     */
    public function bulkForceDelete(User $user): bool
    {
        return $user->hasPermissionTo('force-delete-bulk-notification');
    }

    /**
     * Determine whether the user can send notifications.
     */
    public function send(User $user): bool
    {
        return $user->hasPermissionTo('send-notification');
    }
}
