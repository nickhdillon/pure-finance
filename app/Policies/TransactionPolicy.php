<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Transaction;

class TransactionPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }
}
