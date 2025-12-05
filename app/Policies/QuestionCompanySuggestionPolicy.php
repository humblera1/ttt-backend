<?php

namespace App\Policies;

use App\Models\User;

class QuestionCompanySuggestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view-any-question-company-suggestion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo('change-status-question-company-suggestion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('force-delete-any-question-company-suggestion');
    }

    /**
     * Determine whether the user can bulk delete the model.
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('force-delete-bulk-question-company-suggestion');
    }

    /**
     * Determine whether the user can change status of the model.
     */
    public function changeStatus(User $user): bool
    {
        return $user->hasPermissionTo('change-status-question-company-suggestion');
    }
}
