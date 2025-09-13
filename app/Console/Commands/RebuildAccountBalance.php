<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;

class RebuildAccountBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rebuild-account-balance {account_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculates and rebuilds the balance for a single account';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $account_id = $this->argument('account_id');

        $account = Account::find($account_id);

        if (! $account) {
            $this->error("Account with ID {$account_id} not found.");

            return;
        }

        $account->recalculateBalance();

        $this->info("Account #{$account_id} balance rebuilt successfully.");
    }
}
