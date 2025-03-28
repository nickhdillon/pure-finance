<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create();

        $parent_categories = collect([
            'Auto & Transport',
            'Food',
            'Home',
            'Health',
            'Personal Care',
            'Personal Income',
            'Pets',
            'Shopping',
            'Travel',
            'Utilities',
        ]);

        $child_categories = collect([
            'Car Insurance',
            'Car Payment',
            'Fast Food',
            'Restaurants',
            'Mortgage',
            'Rent',
            'Doctor',
            'Pharmacy',
            'Haircut',
            'Laundry',
            'Paycheck',
            'Bonus',
            'Pet Food',
            'Veterinary',
            'Clothing',
            'Gifts',
            'Hotel',
            'Airfare',
            'Gas',
            'Electric',
        ]);

        User::query()
            ->with('categories')
            ->get()
            ->each(function (User $user) use ($parent_categories, $child_categories) {
                $parent_categories = $parent_categories->map(function (string $parent) use ($user): Model {
                    return $user->categories()->create(['name' => $parent]);
                });

                $parent_index = 0;

                $child_categories->each(
                    function (string $child, int $index) use ($parent_categories, &$parent_index, $user): void {
                        $parent = $parent_categories->get($parent_index);

                        $user->categories()->create([
                            'name' => $child,
                            'parent_id' => $parent->id,
                        ]);

                        if (($index + 1) % 2 === 0) {
                            $parent_index++;
                        }
                    }
                );
            });
    }
}
