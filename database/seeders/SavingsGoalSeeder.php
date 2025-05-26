<?php

namespace Database\Seeders;

use App\Models\SavingsGoal;
use Illuminate\Database\Seeder;

class SavingsGoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SavingsGoal::factory(2)
            ->sequence(
                ['name' => 'House', 'slug' => 'house'],
                ['name' => 'Car', 'slug' => 'car']
            )
            ->create();
    }
}
