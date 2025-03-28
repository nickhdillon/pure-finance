<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = Account::where('user_id', auth()->id())->get();

        Transaction::factory()
            ->count(500)
            ->recycle($accounts)
            ->recycle(Category::get())
            ->create();
    }
}
