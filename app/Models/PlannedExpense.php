<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlannedExpenseType;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlannedExpense extends Model
{
    /** @use HasFactory<\Database\Factories\PlannedExpenseFactory> */
    use HasFactory;
    use Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'monthly_amount',
        'type',
        'starts_on',
        'ends_on'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => PlannedExpenseType::class,
            'starts_on' => 'date',
            'ends_on' => 'date'
        ];
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function months(): HasMany
    {
        return $this->hasMany(PlannedExpenseMonth::class);
    }

    public function currentMonth(): HasOne
    {
        return $this->hasOne(PlannedExpenseMonth::class)
            ->whereDate(
                'month',
                now('America/Chicago')->startOfMonth()
            );
    }
}
