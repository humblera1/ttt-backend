<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-comment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comment $comment): bool
    {
        if ($user->hasPermissionTo('view-own-comment') && $user->id === $comment->user_id) {
            return true;
        }

        return $user->hasPermissionTo('view-any-comment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-comment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        if ($user->hasPermissionTo('edit-own-comment') && $user->id === $comment->user_id) {
            return true;
        }

        return $user->hasPermissionTo('edit-any-comment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->hasPermissionTo('delete-own-comment') && $user->id === $comment->user_id) {
            return true;
        }

        return $user->hasPermissionTo('delete-any-comment');
    }

    /**
     * Determine whether the user can bulk delete the models.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->hasPermissionTo('delete-bulk-comment');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        if ($user->hasPermissionTo('restore-own-comment') && $user->id === $comment->user_id) {
            return true;
        }

        return $user->hasPermissionTo('restore-any-comment');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->hasPermissionTo('force-delete-any-comment');
    }

    /**
     * Determine whether the user can bulk delete the model.
     */
    public function bulkForceDelete(User $user): bool
    {
        return $user->hasPermissionTo('force-delete-bulk-comment');
    }
}
