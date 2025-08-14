<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingsGoalTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\SavingsGoalTransactionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'savings_goal_id',
        'contribution_amount',
        'withdrawal_amount',
    ];

    public function savings_goal(): BelongsTo
    {
        return $this->belongsTo(SavingsGoal::class);
    }
}
