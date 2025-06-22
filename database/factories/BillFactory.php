<?php

namespace Database\Factories;

use App\Models\Account;
use App\Enums\BillAlert;
use App\Models\Category;
use Illuminate\Support\Arr;
use App\Enums\RecurringFrequency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::count() > 0
                ? Account::inRandomOrder()->first()->id
                : Account::factory(),
            'name' => $this->faker->company(),
            'category_id' => Category::count() > 0
                ? Category::inRandomOrder()->first()->id
                : Category::factory(),
            'amount' => $this->faker->randomFloat(2, 0, 100),
            'date' => $this->faker->dateTimeBetween('first day of this month', 'last day of this month')
                ->format('Y-m-d'),
            'frequency' => Arr::random(RecurringFrequency::cases()),
            'notes' => $this->faker->paragraph(4),
            'paid' => Arr::random([true, false]),
            'first_alert' => Arr::random(BillAlert::cases()),
            'first_alert_time' => $this->faker->dateTimeBetween('00:00', '23:00')->format('g A'),
            'second_alert' => Arr::random(BillAlert::cases()),
            'second_alert_time' => $this->faker->dateTimeBetween('00:00', '23:00')->format('g A'),
        ];
    }
}
