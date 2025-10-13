<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\User;

class QuestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-question');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Question $question): bool
    {
        // просмотр собственных вопросов независимо от премиальности
        if ($user->hasPermissionTo('view-own-question') && $user->id === $question->user_id) {
            return true;
        }

        // premium question!
        if ($question->is_premium) {
            if ($user->hasPermissionTo('view-premium-question')) {
                return true;
            }

            return false;
        }

        return $user->hasPermissionTo('view-any-question');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-question');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Question $question): bool
    {
        if ($user->hasPermissionTo('edit-own-question') && $user->id === $question->user_id) {
            return true;
        }

        return $user->hasPermissionTo('edit-any-question');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Question $question): bool
    {
        if ($user->hasPermissionTo('delete-own-question') && $user->id === $question->user_id) {
            return true;
        }

        return $user->hasPermissionTo('delete-any-question');
    }

    public function bulkDelete(User $user): bool
    {
        return $user->hasPermissionTo('delete-bulk-question');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Question $question): bool
    {
        if ($user->hasPermissionTo('restore-own-question') && $user->id === $question->user_id) {
            return true;
        }

        return $user->hasPermissionTo('restore-any-question');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Question $question): bool
    {
        if ($user->hasPermissionTo('force-delete-own-question') && $user->id === $question->user_id) {
            return true;
        }

        return $user->hasPermissionTo('force-delete-any-question');
    }

    public function bulkForceDelete(User $user): bool
    {
        return $user->hasPermissionTo('force-delete-bulk-question');
    }

    public function changeStatus(User $user): bool
    {
        return $user->hasPermissionTo('change-status-question');
    }
}
