<?php

namespace App\Policies;

use App\Models\User;

class QuestionPositionSuggestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-question-position-suggestion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo('change-status-question-position-suggestion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('force-delete-any-question-position-suggestion');
    }

    /**
     * Determine whether the user can bulk delete the model.
     */
    public function bulkForceDelete(User $user): bool
    {
        return $user->hasPermissionTo('force-delete-bulk-question-position-suggestion');
    }

    /**
     * Determine whether the user can change status of the model.
     */
    public function changeStatus(User $user): bool
    {
        return $user->hasPermissionTo('change-status-question-position-suggestion');
    }
}
