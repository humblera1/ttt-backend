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
        // todo: просмотр собственных вопросов независимо от премиальности
//        if ($user->hasPermissionTo('view-own-question') && $user->id === $question->user_id) {
//            return true;
//        }

        // premium question!
        if ($question->is_premium) {
            if ($user->hasPermissionTo('view-premium-question')) {
                return true;
            }

            return false;
        }

        return $user->hasPermissionTo('view-any-question');
    }
}
