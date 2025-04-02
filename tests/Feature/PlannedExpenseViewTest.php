<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Category;
use App\Models\PlannedExpense;
use App\Livewire\PlannedExpenseView;
use function Pest\Livewire\livewire;

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
                'name' => $name
            ]);
        });
    }

    $this->actingAs($user);
});

test('component can render', function () {
    livewire(PlannedExpenseView::class, ['expense' => PlannedExpense::factory()->create()])
        ->assertHasNoErrors();
});
