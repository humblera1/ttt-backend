<?php

namespace App\Policies\v1;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-user');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // permission to edit own profile
        if ($user->hasPermissionTo('edit-own-user') && $user->id === $model->id) {
            return true;
        }

        // permission to edit any user except admin; to edit an admin user should be another admin
        if ($user->hasPermissionTo('edit-any-user') && ($user->hasRole('admin') || !$model->hasRole('admin'))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // permission to delete own profile
        if ($user->hasPermissionTo('delete-own-user') && $user->id === $model->id) {
            return true;
        }

        // permission to delete any user except admin
        if ($user->hasPermissionTo('delete-any-user') && !$model->hasRole('admin')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    public function ban(User $user, User $model): bool
    {
        return true;
    }
}
