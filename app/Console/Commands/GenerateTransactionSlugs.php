<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class GenerateTransactionSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-transaction-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Transaction::select(['id', 'payee'])
            ->chunk(100, function (Collection $transactions): void {
                foreach ($transactions as $transaction) {
                    $transaction->update([
                        'slug' => Str::slug($transaction->payee),
                    ]);
                }
            });
    }
}
