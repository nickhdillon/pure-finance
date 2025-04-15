<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\PlannedExpense;

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
    }

    $this->actingAs($user);
});

it('generates slugs for existing planned expenses', function () {
    $planned_expense = PlannedExpense::factory()->create(['slug' => null]);

    $this->artisan('generate-planned-expense-slugs')->assertExitCode(0);

    $planned_expense->refresh();

    expect($planned_expense->slug)->toBe(Str::slug($planned_expense->name));
});
