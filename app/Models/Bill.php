<?php

namespace App\Models;

use App\Enums\BillAlert;
use App\Enums\RecurringFrequency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    /** @use HasFactory<\Database\Factories\BillFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'category_id',
        'amount',
        'date',
        'frequency',
        'notes',
        'paid',
        'attachments',
        'first_alert',
        'first_alert_time',
        'second_alert',
        'second_alert_time'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'frequency' => RecurringFrequency::class,
            'paid' => 'bool',
            'attachments' => 'array',
            'first_alert' => BillAlert::class,
            'second_alert' => BillAlert::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }
}
