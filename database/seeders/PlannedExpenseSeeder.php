<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PlannedExpense;
use Illuminate\Database\Seeder;

class PlannedExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PlannedExpense::factory()->create();
    }
}
