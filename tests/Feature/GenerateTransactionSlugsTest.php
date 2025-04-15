<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Str;

beforeEach(function () {
    $user = User::factory()->create();

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

        Account::factory()
            ->for($user)
            ->create();
    }

    $this->actingAs($user);
});

it('generates slugs for existing transactions', function () {
    $transaction = Transaction::factory()->create(['slug' => null]);

    $this->artisan('generate-transaction-slugs')->assertExitCode(0);

    $transaction->refresh();

    expect($transaction->slug)->toBe(Str::slug($transaction->payee));
});
