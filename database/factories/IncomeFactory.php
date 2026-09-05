<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\IncomeType;
use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Income>
 */
class IncomeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company(),
            'amount' => $this->faker->randomFloat(2, 1, 10000),
            'type' => IncomeType::EXPECTED,
            'date' => $this->faker->date(),
            'received' => false,
        ];
    }
}
