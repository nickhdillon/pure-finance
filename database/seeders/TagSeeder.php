<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::factory()
            ->for(User::first())
            ->create([
                'name' => 'Groceries',
            ]);

        Tag::factory()
            ->for(User::first())
            ->create([
                'name' => 'Bills',
            ]);

        Tag::factory()
            ->for(User::first())
            ->create([
                'name' => 'Entertainment',
            ]);
    }
}
