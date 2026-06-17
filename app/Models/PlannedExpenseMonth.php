<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlannedExpenseMonth extends Model
{
    /** @use HasFactory<\Database\Factories\PlannedExpenseMonthFactory> */
    use HasFactory;

    protected $fillable = [
        'planned_expense_id',
        'month',
        'amount'
    ];

    public function plannedExpense(): BelongsTo
    {
        return $this->belongsTo(PlannedExpense::class);
    }
}
