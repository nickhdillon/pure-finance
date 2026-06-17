<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Enums\PlannedExpenseType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlannedExpense>
 */
class PlannedExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = Category::query()->inRandomOrder()->first();

        $type = Arr::random(PlannedExpenseType::cases());

        $starts_on = $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-01');

        return [
            'name' => $category->name,
            'slug' => Str::slug($category->name),
            'category_id' => $category->id,
            'monthly_amount' => $this->faker->randomFloat(2, 0, 100),
            'type' => $type,
            'starts_on' => $starts_on,
            'ends_on' => $type === PlannedExpenseType::ONE_TIME
                ? $starts_on
                : null,
        ];
    }
}
