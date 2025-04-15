<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Enums\AccountType;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = Arr::random(AccountType::cases());

        return [
            'user_id' => User::factory(),
            'type' => $type,
            'name' => $type->label(),
            'slug' => Str::slug($type->label()),
            'balance' => $this->faker->randomFloat(2, 500, 50000),
            'initial_balance' => 100000,
        ];
    }
}
