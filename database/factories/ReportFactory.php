<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Support\Arr;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_date = $this->faker->dateTimeBetween('-2 years', 'now');

        return [
            'name' => $this->faker->text(6),
            'user_id' => User::first(),
            'account_id' => Account::count() > 0
                ? Account::inRandomOrder()->first()->id
                : Account::factory(),
            'type' => Arr::random(TransactionType::cases()),
            'category_id' => Category::count() > 0
                ? Category::inRandomOrder()->first()->id
                : Category::factory(),
            'tag_id' => Tag::count() > 0
                ? Tag::inRandomOrder()->first()->id
                : Tag::factory(),
            'payees' => [$this->faker->company()],
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => clone ($start_date)->modify('-3 months')
        ];
    }
}
