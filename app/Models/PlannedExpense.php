<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlannedExpense extends Model
{
    /** @use HasFactory<\Database\Factories\PlannedExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'monthly_amount',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
