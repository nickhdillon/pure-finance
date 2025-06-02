<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bill::factory()
            ->for(User::first())
            ->count(20)
            ->create();
    }
}
