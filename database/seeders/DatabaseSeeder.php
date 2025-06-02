<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            AccountSeeder::class,
            TransactionSeeder::class,
            TagSeeder::class,
            PlannedExpenseSeeder::class,
            SavingsGoalSeeder::class,
            SavingsGoalTransactionSeeder::class,
            BillSeeder::class
        ]);
    }
}
