<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\TransactionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;
    use Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'slug',
        'balance',
        'initial_balance',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => AccountType::class,
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function savings_goals(): HasMany
    {
        return $this->hasMany(SavingsGoal::class);
    }

    public function recalculateBalance(): void
    {
        DB::transaction(function (): void {
            $account = $this->lockForUpdate()->first();

            $transactions = $account->transactions()
                ->whereDate('date', '<=', now()->timezone('America/Chicago'));

            $total_credits = (clone $transactions)
                ->whereIn('type', [TransactionType::CREDIT, TransactionType::DEPOSIT])
                ->sum('amount');

            $total_debits = (clone $transactions)
                ->whereIn('type', [
                    TransactionType::DEBIT,
                    TransactionType::TRANSFER,
                    TransactionType::WITHDRAWAL,
                ])
                ->sum('amount');

            $balance = in_array($account->type, [AccountType::LOAN, AccountType::CREDIT_CARD])
                ? $account->initial_balance - $total_debits + $total_credits
                : $account->initial_balance + $total_credits - $total_debits;

            $account->update(['balance' => $balance]);
        });
    }
}
