<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class GenerateAccountSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-account-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (Account::select(['id', 'name'])->get() as $account) {
            $account->update(['slug' => Str::slug($account->name)]);
        }
    }
}
