<?php

declare(strict_types=1);

use App\Models\Bill;
use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Support\Carbon;
use function Pest\Livewire\livewire;
use App\Livewire\MonthlySpendingOverview;

beforeEach(function () {
    $user = User::factory()->create();

    $account = Account::factory()->for($user)->create();

    if (Category::count() === 0) {
        $categories = collect([
            'Personal Income',
            'Pets',
            'Shopping',
            'Travel',
            'Utilities',
        ]);

        $categories->each(function (string $name) use ($user, $account): void {
            $category = Category::factory()->for($user)->create([
                'name' => $name,
            ]);

            Transaction::factory(2)
                ->for($account)
                ->for($category)
                ->create([
                    'type' => TransactionType::DEBIT,
                    'date' => now('America/Chicago')->subDays(3)->toDateString()
                ]);
        });
    }

    Bill::factory(10)
        ->for($user)
        ->create(['paid' => false]);

    $this->actingAs($user);
});

test('component can render with long month name', function () {
    Carbon::setTestNow(Carbon::parse('2025-11-15 12:00:00', 'America/Chicago'));

    livewire(MonthlySpendingOverview::class)
        ->assertHasNoErrors();
});

test('component can render', function () {
    livewire(MonthlySpendingOverview::class)
        ->assertHasNoErrors();
});
