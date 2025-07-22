<?php

declare(strict_types=1);

use App\Models\Bill;
use App\Models\User;
use App\Models\Account;
use App\Enums\BillAlert;
use App\Models\Category;
use App\Notifications\BillAlertNotification;
use Illuminate\Support\Facades\Notification;

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

    $now = now('America/Chicago');

    $bill_time = $now->copy()->format('g A');

    collect(BillAlert::cases())->each(function (BillAlert $alert) use ($user, $now, $bill_time): void {
        Bill::factory()
            ->for($user)
            ->create([
                'date' => $now->copy()->toDateString(),
                'first_alert' => $alert,
                'first_alert_time' => $bill_time,
                'second_alert' => $alert,
                'second_alert_time' => $bill_time,
            ]);
    });

    $this->actingAs($user);

    Notification::fake();
});

it('can send bill alerts successfully', function () {
    $this->artisan('send-bill-alerts')->assertExitCode(0);

    Notification::assertSentOnDemand(
        BillAlertNotification::class,
        function (BillAlertNotification $notification) {
            expect($notification->toVonage()->content)->toContain('Pure Finance');

            return true;
        }
    );
});
