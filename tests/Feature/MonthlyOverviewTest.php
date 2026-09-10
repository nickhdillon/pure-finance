<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->account = Account::factory()->for($this->user)->create();
    $this->actingAs($this->user);
});

it('renders the monthly overview page', function (): void {
    $this->get(route('monthly-overview'))
        ->assertOk()
        ->assertSee('Income')
        ->assertSee('Bills');
});
