<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionType;
use App\Enums\RecurringFrequency;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;
    use Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'category_id',
        'type',
        'transfer_to',
        'amount',
        'payee',
        'slug',
        'date',
        'notes',
        'attachments',
        'status',
        'is_recurring',
        'frequency',
        'recurring_end',
        'parent_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'date' => 'date',
            'attachments' => 'array',
            'status' => 'bool',
            'is_recurring' => 'bool',
            'frequency' => RecurringFrequency::class,
            'recurring_end' => 'date',
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
                'source' => 'payee'
            ]
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Transaction $transaction): void {
            $transaction->recalculateAccountBalance();
        });

        static::updated(function (Transaction $transaction): void {
            $transaction->recalculateAccountBalance();
        });

        static::deleting(function (Transaction $transaction): void {
            $transaction->recalculateAccountBalance();
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function recalculateAccountBalance(): void
    {
        defer(function (): void {
            $total_credits = $this->account->transactions()
                ->whereDate('date', '<=', now()->timezone('America/Chicago'))
                ->whereIn('type', [TransactionType::CREDIT, TransactionType::DEPOSIT])
                ->sum('amount');

            $total_debits = $this->account->transactions()
                ->whereDate('date', '<=', now()->timezone('America/Chicago'))
                ->whereIn('type', [
                    TransactionType::DEBIT,
                    TransactionType::TRANSFER,
                    TransactionType::WITHDRAWAL,
                ])
                ->sum('amount');

            $initial_balance = $this->account->initial_balance;

            $new_balance = $initial_balance + $total_credits - $total_debits;

            $this->account->update(['balance' => $new_balance]);
        });
    }
}
