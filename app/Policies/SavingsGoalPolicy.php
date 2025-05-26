<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\SavingsGoal;

class SavingsGoalPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SavingsGoal $savings_goal): bool
    {
        return $user->id === $savings_goal->account->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SavingsGoal $savings_goal): bool
    {
        return $user->id === $savings_goal->account->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SavingsGoal $savings_goal): bool
    {
        return $user->id === $savings_goal->account->user_id;
    }
}
