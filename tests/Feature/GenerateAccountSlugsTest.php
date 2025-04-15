<?php

declare(strict_types=1);

use App\Models\Account;
use Illuminate\Support\Str;

it('generates slugs for existing accounts', function () {
    $account = Account::factory()->create(['slug' => null]);

    $this->artisan('generate-account-slugs')->assertExitCode(0);

    $account->refresh();

    expect($account->slug)->toBe(Str::slug($account->name));
});
