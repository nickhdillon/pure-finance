<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\Category;

beforeEach(function () {
    $user = User::factory()->create([
        'phone_numbers' => [['value' => '123-456-7890']]
    ]);

    if (Category::count() === 0) {
        $categories = collect([
            'Personal Income',
            'Pets',
            'Shopping',
            'Travel',
            'Utilities',
        ]);

        $categories->each(function (string $name) use ($user): void {
            Category::factory()->for($user)->create([
                'name' => $name,
            ]);
        });
    }

    Account::factory()
        ->for($user)
        ->create();

    $this->actingAs($user);
});

it('can rebuild account balance successfully', function () {
    $this->artisan('rebuild-account-balance 1')->assertExitCode(0);
});

it('throws error when account is not found', function () {
    $this->artisan('rebuild-account-balance 2')->assertExitCode(0);
});
