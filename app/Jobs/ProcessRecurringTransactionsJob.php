<?php

declare(strict_types=1);

namespace App\Jobs;

use Throwable;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

class ProcessRecurringTransactionsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 2;

    public function handle(): void
    {
        $transactions = Transaction::query()
            ->with('account')
            ->whereNotNull('parent_id')
            ->whereNull('posted_at')
            ->whereDate('date', '<=', now()->timezone('America/Chicago'));

        $transactions_count = $transactions->count();

        $transactions->chunkById(100, function (Collection $transactions): void {
            DB::transaction(function () use ($transactions): void {
                $transactions->each(function (Transaction $transaction): void {
                    $transaction->update(['posted_at' => now()]);
                });
            });
        });

        if ($transactions_count <= 0) {
            Log::info('No recurring transactions found');
        } else {
            Log::info('Recurring transactions were processed succesfully');
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error('There was a problem processing recurring transactions', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode()
        ]);
    }
}
