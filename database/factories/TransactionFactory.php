<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Enums\TransactionType;
use App\Enums\RecurringFrequency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $transaction_date = $this->faker->dateTimeBetween('-2 years', 'now');

        $frequency = Arr::random(array_values(
            array_filter(
                RecurringFrequency::cases(),
                fn(RecurringFrequency $case): bool =>
                $case !== RecurringFrequency::ONE_TIME
            )
        ));

        $modifier = match ($frequency) {
            RecurringFrequency::MONTHLY => '+1 month',
            RecurringFrequency::QUARTERLY => '+3 months',
            RecurringFrequency::SEMI_ANNUALLY => '+6 months',
            RecurringFrequency::YEARLY => '+1 year',
        };

        $recurring_end = (clone $transaction_date)->modify($modifier);

        $payee = $this->faker->company();

        return [
            'account_id' => Account::count() > 0
                ? Account::inRandomOrder()->first()->id
                : Account::factory(),
            'category_id' => Category::count() > 0
                ? Category::inRandomOrder()->first()->id
                : Category::factory(),
            'type' => Arr::random(TransactionType::cases()),
            'transfer_to' => null,
            'amount' => $this->faker->randomFloat(2, 0, 100),
            'payee' => $payee,
            'slug' => Str::slug($payee),
            'date' => $transaction_date->format('Y-m-d'),
            'notes' => $this->faker->paragraph(4),
            'status' => Arr::random([true, false]),
            'is_recurring' => Arr::random([true, false]),
            'frequency' => $frequency,
            'recurring_end' => $recurring_end->format('Y-m-d'),
        ];
    }
}
